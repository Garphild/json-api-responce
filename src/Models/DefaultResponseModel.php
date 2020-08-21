<?php

namespace Garphild\JsonApiResponse\Models;

use Garphild\JsonApiResponse\Interfaces\IResponseModel;

class DefaultResponseModel implements IResponseModel {
  public $status = 200;
  public $payload = [];
  public $errors = [];

  function setResponseStatus($status)
  {
    // TODO: Check types
    $this->status = $status;
  }

  function getResponseStatus()
  {
    return $this->status;
  }

  function setField($name, $value)
  {
    if (strpos($name, ".") !== false) {
      $path = explode(".", $name);
      $target = &$this->payload;
      foreach($path as $part) {
        if (!isset($target[$part])) $target[$part] = [];
        $target = &$target[$part];
      }
      $target = $value;
    } else {
      $this->payload[$name] = $value;
    }
    return $this;
  }

  function getField($name)
  {
    if (strpos($name, ".") !== false) {
      $path = explode(".", $name);
      $last = array_pop($path);
      $target = &$this->payload;
      foreach($path as $part) {
        if (!isset($target[$part])) return null;
        $target = &$target[$part];
      }
      return $target[$last];
    } else {
      return $this->payload[$name];
    }
  }

  function removeField($name)
  {
    unset($this->payload[$name]);
  }

  function getResponse(): array
  {
    return [
      'status' => $this->status,
      'data' => $this->payload,
      'errors' => $this->errors
    ];
  }

  function haveField($name): bool
  {
    return isset($this->payload[$name]);
  }

  function clearData()
  {
    $this->payload = [];
  }
}
