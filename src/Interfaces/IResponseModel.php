<?php
/**
 * Interface describing a methods for response model.
 *
 * Copyright (c) 2020 Serhii Kondrashov.
 * @author Serhii Kondrashov <garphild@garphild.pro>
 * @version    SVN: $Id$
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Garphild\JsonApiResponse\Interfaces;

/**
 * Interface IResponseModel
 * Interface/model of responce handler.c
 * @package Garphild\JsonApiResponse\Interfaces
 */
interface IResponseModel {
  /**
   * Set current response status
   *
   * @param $status
   * @return IResponseModel
   */
  function setResponseStatus($status): IResponseModel;

  /**
   * Return current response status
   *
   * @return int
   */
  function getResponseStatus(): int;

  /**
   * Set a response field value
   *
   * @param string $name
   * @param mixed $value
   * @return IResponseModel
   */
  function setField($name, $value): IResponseModel;

  /**
   * Get a value for field
   *
   * @param $name
   * @return mixed|null
   */
  function getField($name);

  /**
   * Remove root field from responce
   *
   * @param string $name
   * @return IResponseModel
   */
  function removeField($name): IResponseModel;

  /**
   * return full response
   *
   * @return array
   */
  function getResponse(): array;

  /**
   * check field exists
   *
   * @param string $name
   * @return bool
   */
  function haveField($name): bool;

  /**
   * Totally clear data. Don't work with status or errors
   *
   * @return IResponseModel
   */
  function clearData(): IResponseModel;
}
