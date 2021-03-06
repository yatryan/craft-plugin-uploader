

# Plugin Uploader for Craft CMS

[![Build Status](https://img.shields.io/travis/yatryan/craft-plugin-uploader/master.svg)](https://travis-ci.org/yatryan/craft-plugin-uploader)  [![codecov.io](https://img.shields.io/codecov/c/github/yatryan/craft-plugin-uploader/master.svg)](https://codecov.io/github/yatryan/craft-plugin-uploader?branch=master)


Plugin Uploader for [Craft](http://craftcms.com) makes it much easier to upload plugins for install or upgrade. No longer do you need to manually extract and upload plugins to your craft/plugins folder.

## Installation

1. Copy the 'pluginuploader/' folder into 'craft/plugins/'
2. Go to Settings → Plugins and click the “Install” button next to "Plugin Uploader"

## Usage

1. Within the Admin control panel, click "Plugin Uploader"
2. Select the zip file of a plugin you would like to upload and click the "Upload" button.
3. Enable overwriting if you are updating a plugin or previously had issues installing a plugin.
4. Install new plugin in Settings → Plugins. You should be redirected here automatically.

## TODO
- [ ] Research ability to automatically install uploaded plugins
- [ ] Customize max upload size

## Changelog

### 1.1.4
- [Fixed] Issue #3: Missing CSRF

### 1.1.3
- [Improved] Increased Plugin max upload size to 5MB

### 1.1.2
- [Fixed] Issue #2: Plugin not enabling

### 1.1
- [Added] Unit Tests
- [Improved] Handling of Error messages

### 1.0.0
- Added Force override function to allow for upgrading existing plugins
- [Fixed] Styling to match that of Craft Admin

### 0.2.0
- Improved Error Messages

### 0.1.0
- Initial Release
