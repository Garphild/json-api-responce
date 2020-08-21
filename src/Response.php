<?php

namespace Garphild\JsonApiResponse;

use Garphild\JsonApiResponse\Interfaces\IResponseModel;
use Garphild\JsonApiResponse\Models\DefaultResponseModel;
use Garphild\SettingsManager\Errors\DefaultException;

class Response {
  /**
   * @var IResponseModel
   */
  protected $body;
  static protected $instance;
  protected $headersSent = false;
  protected $httpCodeSent = false;

  function __construct($responseModel = null) {
    if ($responseModel !== null && !($responseModel instanceof IResponseModel)) {
      throw new DefaultException("Wrong responce model type");
    }
    $this->body = $responseModel;
    if (!$this->headersSent) {
      header("Content-Type: application/json");
      $this->headersSent = true;
    }
  }

  static function instance() {
    if (!self::$instance) {
      self::$instance = new Response(new DefaultResponseModel());
    }
    return self::$instance;
  }

  function changeResponseModel(IResponseModel $responseModel) {
    $this->body = $responseModel;
  }

  function forbidden($final = false) {
    $this->terminateWithHttpCode(403)->send($final);
    return $this;
  }

  function notFound() {}
  function badRequest() {}
  function terminateWithHttpCode($code, $clear = true) {
    $this->body->setResponseStatus($code);
    if ($clear) $this->body->clearData();
    return $this;
  }

  function send($final = false) {
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

  function finalize($message) {
    echo $message;
    exit;
  }

  function getData() {
    return $this->body->getResponse();
  }

  protected function prepareBody($message) {
    return json_encode($message, JSON_UNESCAPED_UNICODE || JSON_PRETTY_PRINT, 9999999);
  }

  public function setField(string $name, $value)
  {
    $this->body->setField($name, $value);
  }

  public function getField($name) {
    return $this->body->getField($name);
  }

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
