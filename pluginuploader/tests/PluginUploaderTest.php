<?php

namespace Craft;

/**
 * Plugin Uploader Tests.
 *
 * Unit test for plugin uploader.
 *
 * @author    Taylor Ryan <spiketmr@gmail.com>
 * @copyright Copyright (c) 2016, Taylor Ryan
 *
 * @link      http://github.com/yatryan
 *
 * @coversDefaultClass Craft\PluginUploaderService
 * @covers ::<!public>
 */
class PluginUploaderTest extends BaseTest
{
  /**
   * {@inheritdoc}
   */
  public static function setUpBeforeClass()
  {
    // Set up parent
    parent::setUpBeforeClass();

    // Require dependencies
    require_once __DIR__.'/../services/PluginUploaderService.php';
  }

  public static function tearDownAfterClass()
  {
    // Tear down parent
    parent::tearDownAfterClass();
  }

  public function setUp()
  {
    if (!file_exists(__DIR__."/../../../storage/uploads/pluginuploader")) {
      mkdir(__DIR__."/../../../storage/uploads/pluginuploader", 0777, true);
    }
  }

  public function tearDown()
  {
    if (file_exists(__DIR__."/../../../storage/uploads")) {
      self::rrmdir(__DIR__."/../../../storage/uploads");
    }
    if (file_exists(__DIR__."/../../../plugins/testplugin")) {
      self::rrmdir(__DIR__."/../../../plugins/testplugin");
    }
  }

  /**
   * Test Upload Success.
   *
   * @covers ::upload
   */
  public function testUploadSuccess()
  {
    $file = array("name"=>"zipSubfolder.zip", "size"=>500, "tmp_name"=>__DIR__.'/zipSubfolder.zip');

    $service = $this->setMockPluginUploaderServiceExtract();

    //check if it updates the correct record
    $service->expects($this->once())
      ->method('extract')
      ->with($this->anything());

    $result = $service->upload($file);
  }

  /**
   * Test Upload failure - File Exists.
   *
   * @covers ::upload
   */
  public function testUploadFail_FileExists()
  {
    $file = array("name"=>"zipSubfolder.zip", "size"=>500, "tmp_name"=>__DIR__.'/zipSubfolder.zip');

    $service = $this->setMockPluginUploaderServiceExtract();

    // Create test file
    @touch(__DIR__.'/../../../storage/uploads/pluginuploader/zipSubfolder.zip');
    $result = $service->upload($file);
    @unlink(__DIR__.'/../../../craft/storage/uploads/pluginuploader/zipSubfolder.zip');

    // check we got the correct result
    $this->assertEquals('Sorry, file already exists.', $result);
  }

  /**
   * Test Upload failure - File Size to large.
   *
   * @covers ::upload
   */
  public function testUploadFail_FileSize()
  {
    $file = array("name"=>"zipSubfolder.zip", "size"=>50000000000, "tmp_name"=>__DIR__.'/zipSubfolder.zip');

    $service = $this->setMockPluginUploaderServiceExtract();
    $result = $service->upload($file);

    // check we got the correct result
    $this->assertEquals('Sorry, your file is too large.', $result);
  }

  /**
   * Test Upload failure - File Format incorrect.
   *
   * @covers ::upload
   */
  public function testUploadFail_FileFormat()
  {
    $file = array("name"=>"zipSubfolder.png", "size"=>500, "tmp_name"=>__DIR__.'/zipSubfolder.zip');

    $service = $this->setMockPluginUploaderServiceExtract();
    $result = $service->upload($file);

    // check we got the correct result
    $this->assertEquals('Sorry, only ZIP files are allowed.', $result);
  }

  /**
   * Test Upload failure - Upload Failed, Unknown.
   *
   * @covers ::upload
   */
  public function testUploadFail_Unknown()
  {
    $file = array("name"=>"zipSubfolder.zip", "size"=>500, "tmp_name"=>__DIR__.'/zipSubfolder.zip');

    $service = $this->setMockPluginUploaderServiceExtract(false);
    $result = $service->upload($file);

    // check we got the correct result
    $this->assertEquals('Sorry, there was an error uploading your file.', $result);
  }

  /**
   * Test Extract Success.
   *
   * @covers ::extract
   */
  public function testExtractSuccess()
  {
    if (!extension_loaded('zip')) {
      $this->markTestSkipped('The Zip extension is not available.');
    }

    $file = __DIR__.'/zipSubfolder.zip';

    $service = $this->setMockPluginUploaderServiceMove();

    //check if it updates the correct record
    $service->expects($this->once())
      ->method('move')
      ->with($this->anything());

    $result = $service->extract($file);
  }

  /**
   * Test Move Success - zip with subfolder
   *
   * @covers ::move
   */
  public function testMoveSuccess_zipSubfolder()
  {
    $file = array("name"=>"zipSubfolder.zip", "size"=>500, "tmp_name"=>__DIR__.'/zipSubfolder.zip');

    $service = $this->setMockPluginUploaderServiceMoveUploadedFile();

    $result = $service->upload($file);

    // check we got the correct result
    $this->assertEquals(false, $result);
  }

  /**
   * Test Move Success - zip without subfolder
   *
   * @covers ::move
   */
  public function testMoveSuccess_ZipNoSubfolder()
  {
    $file = array("name"=>"zipNoSubfolder.zip", "size"=>500, "tmp_name"=>__DIR__.'/zipNoSubfolder.zip');

    $service = $this->setMockPluginUploaderServiceMoveUploadedFile();

    $result = $service->upload($file);

    // check we got the correct result
    $this->assertEquals(false, $result);
  }

  /**
   * Test Move Fail - plugin exists
   *
   * @covers ::move
   */
  public function testMoveFail_PluginExists()
  {
    $file = array("name"=>"zipNoSubfolder.zip", "size"=>500, "tmp_name"=>__DIR__.'/zipNoSubfolder.zip');

    $service = $this->setMockPluginUploaderServiceMoveUploadedFile();

    $result = $service->upload($file);
    // check we got the correct result first time around
    $this->assertEquals(false, $result);

    $result = $service->upload($file);
    // check we got the correct result second time around
    $this->assertEquals('A plugin with the same name (TestPlugin) is already uploaded.', $result);
  }

  /**
   * Mock PluginUploaderService->Extract
   */
  private function setMockPluginUploaderServiceExtract($output = 'success')
  {
    $mock = $this->getMockBuilder('Craft\PluginUploaderService')
      ->setMethods(array('extract', 'move_uploaded_file'))
      ->getMock();
    $mock->method('extract')
      ->willReturn($output);
    $mock->method('move_uploaded_file')
      ->willReturn($output);
    return $mock;
  }

  /**
   * Mock PluginUploaderService->Move
   */
  private function setMockPluginUploaderServiceMove($output = 'success')
  {
    $mock = $this->getMockBuilder('Craft\PluginUploaderService')
      ->setMethods(array('move'))
      ->getMock();
    $mock->method('move')
      ->willReturn($output);
    return $mock;
  }

  /**
   * Mock PluginUploaderService->MoveUploadedFile
   */
  private function setMockPluginUploaderServiceMoveUploadedFile()
  {
    $mock = $this->getMockBuilder('Craft\PluginUploaderService')
      ->setMethods(array('move_uploaded_file'))
      ->getMock();
    $mock->method('move_uploaded_file')
      ->will($this->returnCallback('copy'));
    return $mock;
  }

  /**
   * Remove Directory
   */
  private function rrmdir($dir) {
    foreach (glob($dir.'/{,.}[!.,!..]*', GLOB_MARK|GLOB_BRACE) as $file) {
      if (is_dir($file)) {
        self::rrmdir($file);
      } else {
        unlink($file);
      }
    }
    rmdir($dir);
  }
}
