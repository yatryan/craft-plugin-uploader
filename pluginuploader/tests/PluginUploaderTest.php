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

    chdir('craft');

    if (!file_exists("../craft/storage/uploads/pluginuploader")) {
      mkdir("../craft/storage/uploads/pluginuploader", 0777, true);
    }
  }

  public static function tearDownAfterClass()
  {
    // Tear down parent
    parent::tearDownAfterClass();
    chdir('../');
  }

  /**
   * Test Upload Success.
   *
   * @covers ::upload
   */
  public function testUploadSuccess()
  {
    $file = array("name"=>"zipSubFolder.zip", "size"=>500, "tmp_name"=>"./zipSubFolder.zip");

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
    $file = array("name"=>"zipSubFolder.zip", "size"=>500, "tmp_name"=>"./zipSubFolder.zip");

    $service = $this->setMockPluginUploaderServiceExtract();

    // Create test file
    @touch('../craft/storage/uploads/pluginuploader/zipSubFolder.zip');
    $result = $service->upload($file);
    @unlink('../craft/storage/uploads/pluginuploader/zipSubFolder.zip');

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
    $file = array("name"=>"zipSubFolder.zip", "size"=>5000000, "tmp_name"=>"./zipSubFolder.zip");

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
    $file = array("name"=>"zipSubFolder.png", "size"=>500, "tmp_name"=>"./zipSubFolder.png");

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
    $file = array("name"=>"zipSubFolder.zip", "size"=>500, "tmp_name"=>"./zipSubFolder.zip");

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
    $file = '../craft/plugins/pluginuploader/tests/zipSubFolder.zip';

    $service = $this->setMockPluginUploaderServiceMove();

    //check if it updates the correct record
    $service->expects($this->once())
      ->method('move')
      ->with($this->anything());

    $result = $service->extract($file);
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
}
