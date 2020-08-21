<?php

namespace Garphild\JsonApiResponse\Interfaces;

interface IResponseModel {
  function setResponseStatus($status);
  function getResponseStatus();
  function setField($name, $value);
  function getField($name);
  function removeField($name);
  function getResponse(): array;
  function haveField($name): bool;
  function clearData();
}
