<?php

namespace App\Api;

use Exception;

abstract class BaseApi
{
    protected $configFile;
    protected $baseUrl;
    protected function getRoute(string $name): string
    {
        if (!file_exists($this->configFile)) {
            throw new Exception("The config file not found {$this->configFile}");
        }
        $conf = include($this->configFile);
        $route = &$conf;
        $parts = explode('.', $name);
        foreach ($parts as $key) {
            if (!array_key_exists($key, $route)) {
                throw new Exception("key {$name} not found in {$this->configFile}");
            }
            $route = &$route[$key];
        }
        if (!is_string($route)) {
            throw new Exception("The navigation in config file return a non string value");
        }
        return $this->baseUrl . $route;
    }
}
