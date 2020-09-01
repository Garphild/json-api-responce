<?php

require("../vendor/autoload.php");

use Garphild\JsonApiResponse\ApiResponseManager;
use PHPUnit\Framework\TestCase;

class MainTest extends TestCase {
  public $defaultResponse;

  function setUp(): void
  {
    parent::setUp();
    $this->defaultResponse = ApiResponseManager::instance();
  }

  public function testGetEmptyValue()
  {
    $this->expectOutputString('{"status":200,"data":[],"errors":[]}');
    $this->defaultResponse->send(false);
    $this->assertEquals(200, http_response_code());
  }

  public function testSingleValue()
  {
    $this->expectOutputString('{"status":200,"data":{"test":1},"errors":[]}');
    $this->defaultResponse->setField("test", 1);
    $this->defaultResponse->send(false);
    $this->assertEquals(200, http_response_code());
  }

  public function testReplaceValue()
  {
    $this->expectOutputString('{"status":200,"data":{"test":2},"errors":[]}');
    $this->defaultResponse->setField("test", 1);
    $this->defaultResponse->setField("test", 2);
    $this->defaultResponse->send(false);
    $this->assertEquals(200, http_response_code());
  }

  public function testTerminate200()
  {
    $this->defaultResponse->terminateWithHttpCode(200, true);
    $this->expectOutputString('{"status":200,"data":[],"errors":[]}');
    $this->defaultResponse->send(false);
    $this->assertEquals(200, http_response_code());
  }

  public function testForbidden()
  {
    $this->defaultResponse->forbidden();
    $this->expectOutputString('{"status":403,"data":[],"errors":[]}');
    $this->assertEquals(403, http_response_code());
  }

  public function testNestedFields()
  {
    $data = $this->defaultResponse->getField("test.nested");
    $this->assertEquals(null, $data);
    $this->defaultResponse->setField("test.nested", 1);
    $data = $this->defaultResponse->getField("test.nested");
    $this->assertEquals(1, $data);
    $this->expectOutputString('{"status":200,"data":{"test":{"nested":1}},"errors":[]}');
    $this->defaultResponse->send();
    $this->assertEquals(200, http_response_code());
  }

  public function testRemoveNestedFields()
  {
    $path = "test.section.subsection";
    $expectedArray = [
      'status' => 200,
      'data' => [
        'test' => [
          'section' => []
        ]
      ],
      'errors' => [],
    ];
    $data = $this->defaultResponse->getField($path);
    $this->assertEquals(null, $data);
    $this->defaultResponse->setField($path, 1);
    $data = $this->defaultResponse->getField($path);
    $this->assertEquals(1, $data);
    $this->assertTrue($this->defaultResponse->haveField($path));
    $data = $this->defaultResponse->removeField($path)->getData();
    $this->assertEquals($expectedArray, $data);
    $data = $this->defaultResponse->removeField("test")->getData();
    $expectedArray = [
      'status' => 200,
      'data' => [],
      'errors' => [],
    ];
    $this->assertEquals($expectedArray, $data);
    $data = $this->defaultResponse->getField($path);
    $this->assertEquals(null, $data);
    $this->assertFalse($this->defaultResponse->haveField($path));
  }
}
