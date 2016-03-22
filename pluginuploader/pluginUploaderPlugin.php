<?php
namespace Craft;

class PluginUploaderPlugin extends BasePlugin
{
    public function getName()
    {
         return Craft::t('Plugin Uploader');
    }

    public function getVersion()
    {
        return '0.1';
    }

    public function getDeveloper()
    {
        return 'Taylor Ryan';
    }

    public function getDeveloperUrl()
    {
        return 'http://yatryan.com/';
    }

    public function getPluginUrl()
    {
        return 'http://yatryan.com/';
    }

    public function getDocumentationUrl()
    {
        return 'http://yatryan.com/blob/master/README.md';
    }

    // public function getReleaseFeedUrl()
    // {
    //     return 'http://yatryan.dev/';
    // }

    public function getSourceLanguage()
    {
        return 'en';
    }

    public function hasCpSection()
    {
        return true;
    }

    public function onBeforeInstall()
    {
      if (!file_exists("../craft/storage/uploads/pluginuploader")) {
        mkdir("../craft/storage/uploads/pluginuploader", 0777, true);
      }
    }
}
