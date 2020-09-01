<?php
/**
 * Default model for response. Can store and manipulate response fields.
 *
 * Copyright (c) 2020 Serhii Kondrashov.
 * @author Serhii Kondrashov <garphild@garphild.pro>
 * @version    SVN: $Id$
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Garphild\JsonApiResponse\Models;

use Garphild\JsonApiResponse\ApiResponseManager;
use Garphild\JsonApiResponse\Interfaces\IResponseModel;

/**
 * This class realise a logic for working with response.
 * Store and manipulate data required for client.
 *
 * @todo Add values to array by path
 *
 * @package Garphild\JsonApiResponse\Models
 */
class DefaultResponseModel implements IResponseModel {
  /**
   * Status of response. Equals to http response codes.
   *
   * @var int
   */
  public $status = 200;

  /**
   * Response data
   *
   * @var array
   */
  public $payload = [];

  /**
   * Response errors
   *
   * @var array
   */
  public $errors = [];

  /**
   * Set current response status
   *
   * @param int|string $status
   * @return IResponseModel
   */
  function setResponseStatus($status): IResponseModel
  {
    $this->status = (int)$status;
    return $this;
  }

  /**
   * Return current response status
   *
   * @return int
   */
  function getResponseStatus(): int
  {
    return $this->status;
  }

  /**
   * Set a value for specified field. Field may contains a path to field in response data tree.
   * Path must be specified as a string an may be delimited by dot for specify a tree path.
   * - $manager->setField("test", 1) : set a root field named 'test'
   * - $manager->setField("test.subtree1.subtree2", 1) : produce tree structure {test: {subtree1: {subtree2: 1}}}
   * @example examples/DefaultResponseModelExample1.php
   * @param string $name
   * @param mixed $value
   * @return IResponseModel
   */
  function setField($name, $value): IResponseModel
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

  /**
   * Return a value from current response which described by name or dot delimited path.
   *
   * Attention: Return null if path not exists.
   *
   * See: ApiResponseManager::setField()
   *
   * @see DefaultResponseModel::setField()
   * @param $name
   * @return mixed|null
   */
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
      return isset($target[$last]) ? $target[$last] : null;
    } else {
      return $this->payload[$name];
    }
  }

  /**
   * Remove root field from responce
   * @param string $name
   * @return IResponseModel
   */
  function removeField($name): IResponseModel
  {
    if (strpos($name, ".") !== false) {
      $path = explode(".", $name);
      $last = array_pop($path);
      $target = &$this->payload;
      foreach($path as $part) {
        if (!isset($target[$part])) return $this;
        $target = &$target[$part];
      }
      unset($target[$last]);
    } else {
      unset($this->payload[$name]);
    }
    return $this;
  }

  /**
   * return full response
   *
   * @return array
   */
  function getResponse(): array
  {
    return [
      'status' => $this->status,
      'data' => $this->payload,
      'errors' => $this->errors
    ];
  }

  /**
   * check field exists
   *
   * @param string $name path to field
   * @return bool
   */
  function haveField($name): bool
  {
    if (strpos($name, ".") !== false) {
      $path = explode(".", $name);
      $last = array_pop($path);
      $target = &$this->payload;
      foreach($path as $part) {
        if (!isset($target[$part])) return false;
        $target = &$target[$part];
      }
      return isset($target[$last]);
    } else {
      return isset($this->payload[$name]);
    }
  }

  /**
   * Totally clear data. Don't work with status or errors
   *
   * @return IResponseModel
   */
  function clearData(): IResponseModel
  {
    $this->payload = [];
    return $this;
  }

  function addError($error) {
    $this->errors[] = $error;
    return $this;
  }
}
