<?php
namespace Craft;

class PluginUploaderService extends BaseApplicationComponent
{
    public function upload($file, $overwrite = false)
    {
      $target_file = CRAFT_STORAGE_PATH.'uploads/pluginuploader/'.basename($file["name"]);
      $fileType = pathinfo($target_file, PATHINFO_EXTENSION);
      $error = false;

      // Check if file already exists
      if (file_exists($target_file)) {
          $error = 'Sorry, file already exists.';
      }
      // Check file size
      if (!$error && $file["size"] > 500000) {
          $error = 'Sorry, your file is too large.';
      }
      // Allow certain file formats
      if (!$error && $fileType != "zip") {
          $error = 'Sorry, only ZIP files are allowed.';
      }
      // Check if $uploadOk is set to 0 by an error
      if (!$error) {
        if ($this->move_uploaded_file($file["tmp_name"], $target_file)) {
          $error = $this->extract($target_file, $overwrite);
        } else {
          $error = 'Sorry, there was an error uploading your file.';
        }
      }

      if (file_exists($target_file)) {
        unlink($target_file);
      }

      return $error;
    }

    /**
     * @param string $file
     */
    public function extract($file, $overwrite = false)
    {
      $date = new DateTime();
      $now = $date->getTimestamp();
      $zip = new \ZipArchive();
      $res = $zip->open($file);
      if ($res === TRUE) {
        $zip->extractTo(CRAFT_STORAGE_PATH.'uploads/pluginuploader');
        $zip->close();

        return $this->move(pathinfo($file, PATHINFO_FILENAME), $overwrite);
      }

      return 'Unknown Error';
    }

    public function move($folder, $overwrite = false)
    {
      $zipFolder = CRAFT_STORAGE_PATH.'uploads/pluginuploader/'.$folder;
      $pluginExtractFile = '';
      $pluginExtractFolder = '';
      // Find the folder that the Plugin.php file is in. That is the root of the plugin.
      foreach (glob($zipFolder."/*Plugin.php") as $filename) {
        $pluginExtractFile = $filename;
        $pluginExtractFolder = dirname($filename);
      }
      if ($pluginExtractFile === '') {
        foreach (glob($zipFolder."/**/*Plugin.php") as $filename) {
          $pluginExtractFile = $filename;
          $pluginExtractFolder = dirname($filename);
        }
      }

      // Open the file
      $fp = @fopen($pluginExtractFile, 'r');
      if ($fp) {
        $array = explode("\n", fread($fp, filesize($pluginExtractFile)));

        $pluginName = '';
        // Get name of plugin
        foreach ($array as $line) {
          if (strpos($line, 'class') !== false && strpos($line, 'extends') !== false) {
            $split = explode(" ", $line);
            $pluginName = substr($split[1], 0, -6);
            break;
          }
        }

        // Copy to craft/plugins folder.
        $pluginInstallFolder = CRAFT_PLUGINS_PATH.strtolower($pluginName);
        if ($overwrite || !file_exists($pluginInstallFolder)) {
          // Copy folder to craft/plugins
          $this->recurse_copy($pluginExtractFolder, $pluginInstallFolder);
          // Remove zipped folder
          $this->rrmdir($zipFolder);
        }
        else {
          return "A plugin with the same name (".$pluginName.") is already uploaded.";
        }

        return false;
      } else {
        return "The uploaded file is not a valid plugin.";
      }
    }

    /**
     * @param string $dir
     */
    private function rrmdir($dir) {
      foreach (glob($dir.'/{,.}[!.,!..]*', GLOB_MARK|GLOB_BRACE) as $file) {
        if (is_dir($file)) {
          $this->rrmdir($file);
        } else {
          unlink($file);
        }
      }
      rmdir($dir);
    }

    /**
     * @param string $src
     * @param string $dst
     */
    private function recurse_copy($src,$dst)
    {
      $dir = opendir($src);
      @mkdir($dst);
      while(false !== ( $file = readdir($dir)) ) {
          if (( $file != '.' ) && ( $file != '..' )) {
              if ( is_dir($src . '/' . $file) ) {
                  $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
              } else {
                  if (!file_exists($dst)) {
                      mkdir($dst, 0755, true);
                  }
                  copy($src . '/' . $file,$dst . '/' . $file);
              }
          }
      }
      closedir($dir);
    }

    /**
     * @param string $to
     */
    protected function move_uploaded_file($from, $to) {
        return move_uploaded_file($from, $to);
    }
}
