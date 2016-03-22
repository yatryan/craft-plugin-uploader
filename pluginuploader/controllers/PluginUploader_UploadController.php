<?php
namespace Craft;

class PluginUploader_UploadController extends BaseController
{
  public function actionUploadPlugin()
  {
    // $this->requirePostRequest();
    craft()->pluginUploader->upload($_FILES["fileToUpload"]);
    $this->redirectToPostedUrl();
  }
}
