<?php
namespace Craft;

class PluginUploader_UploadController extends BaseController
{
  public function actionUploadPlugin()
  {
    // $this->requirePostRequest();
    $error = craft()->pluginUploader->upload($_FILES["fileToUpload"], (bool)$_POST["overwrite"]);

    if ($error) {
      craft()->userSession->setNotice(Craft::t($error));
    } else {
      $this->redirectToPostedUrl();
      craft()->userSession->setNotice(Craft::t("The plugin has been uploaded."));
    }
  }
}
