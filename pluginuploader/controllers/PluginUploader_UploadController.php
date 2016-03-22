<?php
namespace Craft;

class PluginUploader_UploadController extends BaseController
{
  public function actionUploadPlugin()
  {
    // $this->requirePostRequest();
    $success = craft()->pluginUploader->upload($_FILES["fileToUpload"], (bool)$_POST["overwrite"]);

    if ($success) {
      $this->redirectToPostedUrl();
    }
  }
}
