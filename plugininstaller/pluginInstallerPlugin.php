<?php
namespace Craft;

class PluginInstallerPlugin extends BasePlugin
{
    public function getName()
    {
         return Craft::t('Plugin Installer');
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
      if (!file_exists("../craft/storage/uploads/plugininstaller")) {
        mkdir("../craft/storage/uploads/plugininstaller", 0777, true);
      }
    }

    public function getSettingsHtml()
    {
        return craft()->templates->render('plugininstaller/_settings', array(
          'settings' => $this->getSettings()
        ));
    }

    protected function defineSettings()
    {
        return array(
            'cocktailCategories' => array(AttributeType::Mixed, 'default' => array('Sours', 'Fizzes', 'Juleps')),
        );
    }
}
