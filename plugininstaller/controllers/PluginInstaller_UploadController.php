<?php
namespace Craft;

class PluginInstaller_UploadController extends BaseController
{
  public function actionUploadPlugin()
  {
    // $this->requirePostRequest();
    craft()->pluginInstaller_upload->upload($_FILES["fileToUpload"]);
    $this->redirectToPostedUrl();
  }
}
