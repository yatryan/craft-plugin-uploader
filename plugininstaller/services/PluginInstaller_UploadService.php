<?php
namespace Craft;

class PluginInstaller_UploadService extends BaseApplicationComponent
{
    public function upload($file)
    {
      $target_dir = "../craft/storage/uploads/plugininstaller/";
      $target_file = $target_dir . basename($file["name"]);
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
            craft()->userSession->setNotice(Craft::t("The file ". basename( $file["name"]). " has been uploaded."));
            $this->extract($target_file);
          } else {
            craft()->userSession->setNotice(Craft::t('Sorry, there was an error uploading your file.'));
          }
      }
    }

    public function extract($file)
    {
      $zip = new \ZipArchive();
      $res = $zip->open($file);
      if ($res === TRUE) {
        $zip->extractTo('../craft/storage/uploads/plugininstaller/');
        $zip->close();
      }
    }
}
