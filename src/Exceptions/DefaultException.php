<?php
/**
 * Default exception template
 *
 * Copyright (c) 2020 Serhii Kondrashov.
 * @author Serhii Kondrashov <garphild@garphild.pro>
 * @version   1.0.0
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Garphild\JsonApiResponse\Exceptions;

/**
 * Default exception template
 *
 * @package Garphild\JsonApiResponse\Errors
 */
class DefaultException extends \Exception {
  /**
   * @var string Exception text
   */
  protected $currentMessage = "";

  /**
   * Constructor
   *
   * @param string $message message with param %PARAM% which replace manager name
   * @param string $param value for replace of params in message
   * @param int $code error code (default: 1000)
   * @param Exception $previous
   */
  public function __construct($message = null, $param = null, $code = 1000, $previous = null) {
    if ($param !== null) $this->propertyName = $param;
    if ($message !== null) $this->currentMessage = $message;
    parent::__construct($this->getMessageAsString(), $code, $previous);
  }

  /**
   * Replace params in message
   *
   * @return string
   */
  function getMessageAsString() {
    return str_replace("%PARAM%", $this->propertyName, $this->currentMessage);
  }

  /**
   * Convert exception to string
   * @return string
   */
  public function __toString() {
    return __CLASS__ . ": [{$this->code}]: ".$this->getMessage()."\n";
  }
}
