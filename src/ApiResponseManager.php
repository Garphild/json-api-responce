<?php
/**
 * Package that help manage of JSON Api responses
 *
 * Copyright (c) 2020 Serhii Kondrashov.
 * @author Serhii Kondrashov <garphild@garphild.pro>
 * @version   1.0.0
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Garphild\JsonApiResponse;

use Garphild\JsonApiResponse\Interfaces\IResponseModel;
use Garphild\JsonApiResponse\Models\DefaultResponseModel;
use Garphild\SettingsManager\Errors\DefaultException;

/**
 * Main class. This is front for package.
 * @package Garphild\JsonApiResponse
 */
class ApiResponseManager {
  /**
   * The body of response
   * @var IResponseModel
   */
  protected $body;

  /**
   * Instance of Api Response Manager
   * @var ApiResponseManager
   */
  static protected $instance;

  /**
   * Was headers already sent?
   * @var bool
   */
  protected $headersSent = false;

  /**
   * Was http header with response code sent?
   * @var bool
   */
  protected $httpCodeSent = false;

  /**
   * Response constructor.
   * @param IResponseModel $responseModel
   * @throws DefaultException
   */
  function __construct($responseModel = null) {
    if ($responseModel !== null && !($responseModel instanceof IResponseModel)) {
      throw new DefaultException("Wrong responce model type");
    }
    if ($responseModel === null) {
      $this->body = new DefaultResponseModel();
    } else {
      $this->body = $responseModel;
    }
    if (!$this->headersSent) {
      header("Content-Type: application/json");
      $this->headersSent = true;
    }
  }

  /**
   * Return stored or newly created instance of Api Responce Manager
   * @return ApiResponseManager
   * @throws DefaultException
   */
  static function instance() {
    if (!self::$instance) {
      self::$instance = new ApiResponseManager(new DefaultResponseModel());
    }
    return self::$instance;
  }

  /**
   * Replace old response model to new. You must save data before change.
   * @param IResponseModel $responseModel
   */
  function changeResponseModel(IResponseModel $responseModel) {
    $this->body = $responseModel;
  }

  /**
   * Send forbidden response.
   * @param bool $final If set true headers will be send with immediate process exit.
   * @return ApiResponseManager
   */
  function forbidden(bool $final = false) {
    $this->terminateWithHttpCode(403)->send($final);
    return $this;
  }

  /**
   * Send not found (404) response.
   * @param bool $final If set true headers will be send with immediate process exit.
   * @return ApiResponseManager
   */
  function notFound(bool $final = false) {
    $this->terminateWithHttpCode(404)->send($final);
    return $this;
  }

  /**
   * Send bad request (400) response.
   * @param bool $final If set true headers will be send with immediate process exit.
   * @return ApiResponseManager
   */
  function badRequest(bool $final = false) {
    $this->terminateWithHttpCode(404)->send($final);
    return $this;
  }

  /**
   * Set response code to provided value.
   * @param int|string $code Provided code (http codes equal)
   * @param bool $clear If set true all data will be removed from response model
   * @return ApiResponseManager
   */
  function terminateWithHttpCode($code, $clear = true) {
    $this->body->setResponseStatus($code);
    if ($clear) $this->body->clearData();
    return $this;
  }

  /**
   * Send response to client.
   * @param bool $final If set true the process will be terminated immediately after send response.
   * @return ApiResponseManager
   */
  function send(bool $final = false) {
    http_response_code($this->body->getResponseStatus());
    $this->httpCodeSent = true;
    $message = $this->body->getResponse();
    if (count($message) > 0) {
      $message = $this->prepareBody($message);
    }
    if ($final) {
      $this->finalize($message);
    } else {
      echo $message;
    }
    return $this;
  }

  /**
   * Send message string to client and terminate process normally.
   * @param string $message
   */
  function finalize($message) {
    echo $message;
    exit;
  }

  /**
   * Return full data of response.
   * @return array
   */
  function getData() {
    return $this->body->getResponse();
  }

  /**
   * Encode array to JSON string.
   * @param $message
   * @return bool|string
   */
  protected function prepareBody($message) {
    return json_encode($message, JSON_UNESCAPED_UNICODE || JSON_PRETTY_PRINT, 9999999);
  }

  /**
   * Set value for specified field
   *
   * @param string $name
   * @param $value
   * @return ApiResponseManager
   */
  public function setField(string $name, $value)
  {
    $this->body->setField($name, $value);
    return $this;
  }

  /**
   * Return value of specified field.
   * @param $name
   * @return mixed
   */
  public function getField($name) {
    return $this->body->getField($name);
  }

  /**
   * Check if field exists
   *
   * @param string $name path to field
   * @return bool
   */
  public function haveField($name) {
    return $this->body->haveField($name);
  }

  /**
   * Remove existing item in path.
   *
   * If path not exists it willbe ignored.
   *
   * @param string $name path to removeable item
   * @return ApiResponseManager
   */
  function removeField($name) {
    $this->body->removeField($name);
    return $this;
  }

  /**
   * Get a text for http code.
   * @param $code
   * @return string
   */
  protected function getHTTPResponceTextByCode($code) {
    $text = '';
    switch ($code) {
      case 100: $text = 'Continue'; break;
      case 101: $text = 'Switching Protocols'; break;
      case 200: $text = 'OK'; break;
      case 201: $text = 'Created'; break;
      case 202: $text = 'Accepted'; break;
      case 203: $text = 'Non-Authoritative Information'; break;
      case 204: $text = 'No Content'; break;
      case 205: $text = 'Reset Content'; break;
      case 206: $text = 'Partial Content'; break;
      case 300: $text = 'Multiple Choices'; break;
      case 301: $text = 'Moved Permanently'; break;
      case 302: $text = 'Moved Temporarily'; break;
      case 303: $text = 'See Other'; break;
      case 304: $text = 'Not Modified'; break;
      case 305: $text = 'Use Proxy'; break;
      case 400: $text = 'Bad Request'; break;
      case 401: $text = 'Unauthorized'; break;
      case 402: $text = 'Payment Required'; break;
      case 403: $text = 'Forbidden'; break;
      case 404: $text = 'Not Found'; break;
      case 405: $text = 'Method Not Allowed'; break;
      case 406: $text = 'Not Acceptable'; break;
      case 407: $text = 'Proxy Authentication Required'; break;
      case 408: $text = 'Request Time-out'; break;
      case 409: $text = 'Conflict'; break;
      case 410: $text = 'Gone'; break;
      case 411: $text = 'Length Required'; break;
      case 412: $text = 'Precondition Failed'; break;
      case 413: $text = 'Request Entity Too Large'; break;
      case 414: $text = 'Request-URI Too Large'; break;
      case 415: $text = 'Unsupported Media Type'; break;
      case 500: $text = 'Internal Server Error'; break;
      case 501: $text = 'Not Implemented'; break;
      case 502: $text = 'Bad Gateway'; break;
      case 503: $text = 'Service Unavailable'; break;
      case 504: $text = 'Gateway Time-out'; break;
      case 505: $text = 'HTTP Version not supported'; break;
      default: break;
    }
    return $text;
  }
}
