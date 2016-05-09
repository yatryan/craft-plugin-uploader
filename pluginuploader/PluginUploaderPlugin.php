<?php
namespace Craft;

class PluginUploaderPlugin extends BasePlugin
{
    public function getName()
    {
          return Craft::t('Plugin Uploader');
    }

    public function getDescription()
    {
          return 'Easily upload new and upgraded plugins.';
    }

    public function getVersion()
    {
        return '1.1.4';
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
        return 'https://github.com/yatryan/craft-plugin-uploader/';
    }

    public function getDocumentationUrl()
    {
        return 'https://github.com/yatryan/craft-plugin-uploader/blob/master/README.md';
    }

    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/yatryan/craft-plugin-uploader/master/changelog.json';
    }

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
