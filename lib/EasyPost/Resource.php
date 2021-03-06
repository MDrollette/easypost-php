<?php

abstract class EasyPost_Resource extends EasyPost_Object {
  public static function className($class) {
    // Strip namespace if present
    if ($postfix = strrchr($class, '\\')) {
      $class = substr($postfix, 1);
    }
    if (substr($class, 0, strlen('EasyPost')) == 'EasyPost') {
      $class = substr($class, strlen('EasyPost'));
    }
    $class = str_replace('_', '', $class);
    $class = substr($class, 0, 1) . preg_replace("/([A-Z])/", "_$1", substr($class, 1)); // Camel -> snake
    $name = urlencode($class);
    $name = strtolower($name);
    return $name;
  }

  public static function classUrl($class) {
    $className = self::className($class);
    if(substr($className, -1) !== "s") {
      return "/{$className}s";
    } else {
      return "/{$className}es";
    }
  }

  public function instanceUrl() {
    $id = $this['id'];
    $class = get_class($this);
    if (!$id) {
      throw new EasyPost_Error("Could not determine which URL to request: {$class} instance has invalid ID: {$id}");
    }
    $id = EasyPost_Requestor::utf8($id);
    $classUrl = self::classUrl($class);
    return "{$classUrl}/".urlencode($id);
  }

  public function refresh() {
    $requestor = new EasyPost_Requestor($this->_apiKey);
    $url = $this->instanceUrl();

    list($response, $apiKey) = $requestor->request('get', $url, $this->_retrieveOptions);
    $this->refreshFrom($response, $apiKey);
    return $this;
   }

  private static function _validate($method, $params=null, $apiKey=null) {
    if ($params && !is_array($params)) {
      throw new EasyPost_Error("You must pass an array as the first argument to EasyPost API method calls.");
    }
    if ($apiKey && !is_string($apiKey)) {
      throw new EasyPost_Error('The second argument to EasyPost API method calls is an optional per-request apiKey, which must be a string.');
    }
  }

  protected static function _retrieve($class, $id, $apiKey=null) {
    if($id instanceof EasyPost_Resource) {
      $id = $id->id;
    }
    $instance = new $class($id, $apiKey);
    $instance->refresh();
    return $instance;
  }

  protected static function _all($class, $params=null, $apiKey=null) {
    self::_validate('all', $params, $apiKey);
    $requestor = new EasyPost_Requestor($apiKey);
    $url = self::classUrl($class);
    list($response, $apiKey) = $requestor->request('get', $url, $params);
    return EasyPost_Util::convertToEasyPostObject($response, $apiKey);
  }

  protected static function _create($class, $params=null, $apiKey=null) {
    self::_validate('create', $params, $apiKey);
    $requestor = new EasyPost_Requestor($apiKey);
    $url = self::classUrl($class);
    list($response, $apiKey) = $requestor->request('post', $url, $params);
    return EasyPost_Util::convertToEasyPostObject($response, $apiKey);
  }

  protected function _save($class) {
    self::_validate('save');
    if (count($this->_unsavedValues)) {
      $requestor = new EasyPost_Requestor($this->_apiKey);
      $params = array();
      foreach ($this->_unsavedValues as $k) {
        $params[$k] = $this->$k;
      }
      $url = $this->instanceUrl();
      list($response, $apiKey) = $requestor->request('post', $url, $params);
      $this->refreshFrom($response, $apiKey);
    }
    return $this;
  }

  protected function _delete($class, $params=null) {
    self::_validate('delete');
    $requestor = new EasyPost_Requestor($this->_apiKey);
    $url = $this->instanceUrl();
    list($response, $apiKey) = $requestor->request('delete', $url, $params);
    $this->refreshFrom($response, $apiKey);
    return $this;
  }
}
