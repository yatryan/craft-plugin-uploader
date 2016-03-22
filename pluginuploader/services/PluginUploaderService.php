<?php
namespace Craft;

const CRAFT_PLUGIN_FOLDER = '../craft/plugins';
const UPLOAD_FOLDER = "../craft/storage/uploads/pluginuploader/";

class PluginUploaderService extends BaseApplicationComponent
{
    public function upload($file)
    {
      $target_file = UPLOAD_FOLDER . basename($file["name"]);
      $uploadOk = 1;
      $fileType = pathinfo($target_file,PATHINFO_EXTENSION);
      // Check if file already exists
      if (file_exists($target_file)) {
          craft()->userSession->setNotice(Craft::t('Sorry, file already exists.'));
          $uploadOk = 0;
      }
      // Check file size
      if ($file["size"] > 500000) {
          craft()->userSession->setNotice(Craft::t('Sorry, your file is too large.'));
          $uploadOk = 0;
      }
      // Allow certain file formats
      if($fileType != "zip") {
          craft()->userSession->setNotice(Craft::t('Sorry, only ZIP files are allowed.'));
          $uploadOk = 0;
      }
      // Check if $uploadOk is set to 0 by an error
      if ($uploadOk == 1) {
          if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $this->extract($target_file);
          } else {
            craft()->userSession->setNotice(Craft::t('Sorry, there was an error uploading your file.'));
          }

          unlink($target_file);
      }
    }

    public function extract($file)
    {
      $date = new DateTime();
      $now = $date->getTimestamp();
      $zip = new \ZipArchive();
      $res = $zip->open($file);
      if ($res === TRUE) {
        $zip->extractTo(UPLOAD_FOLDER);
        $zip->close();

        $this->move(pathinfo($file,PATHINFO_FILENAME));
      }
    }

    public function move($folder)
    {
      $zipFolder = UPLOAD_FOLDER.$folder;
      $pluginExtractFile = '';
      $pluginExtractFolder = '';
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
      // Add each line to an array
      if (!$fp) {
        craft()->userSession->setNotice(Craft::t("The uploaded file is not a valid plugin."));
         die();
      }

      $array = explode("\n", fread($fp, filesize($pluginExtractFile)));

      $pluginName = '';
      foreach ($array as $line) {
        if (strpos($line, 'class') !== false && strpos($line, 'extends') !== false) {
          $split = explode(" ", $line);
          $pluginName = substr($split[1], 0, -6);
          break;
        }
      }

      if (!file_exists(CRAFT_PLUGIN_FOLDER . '/' . strtolower($pluginName))) {
        // Copy folder to craft/plugins
        $this->recurse_copy($pluginExtractFolder, CRAFT_PLUGIN_FOLDER . '/' . strtolower($pluginName));
        // Remove zipped folder
        $this->rrmdir($zipFolder);

        craft()->userSession->setNotice(Craft::t("The plugin ". $pluginName . " has been uploaded."));
      }
    }

    function rrmdir($dir) {
      // glob($path . '{,.}[!.,!..]*',GLOB_MARK|GLOB_BRACE)
      foreach(glob($dir . '/{,.}[!.,!..]*',GLOB_MARK|GLOB_BRACE) as $file) {
        if(is_dir($file)) {
          $this->rrmdir($file);
        } else {
          unlink($file);
        }
      }
      rmdir($dir);
    }

    function recurse_copy($src,$dst)
    {
      $dir = opendir($src);
      @mkdir($dst);
      while(false !== ( $file = readdir($dir)) ) {
          if (( $file != '.' ) && ( $file != '..' )) {
              if ( is_dir($src . '/' . $file) ) {
                  $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
              }
              else {
                  copy($src . '/' . $file,$dst . '/' . $file);
              }
          }
      }
      closedir($dir);
  }
}
