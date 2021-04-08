<?php

namespace App\Config;

class Config
{
    protected static $config = null;

    public function __construct()
    {
        $globalConfig = require __DIR__ . '/../../config/config.php';
        $overloadConfigFile = __DIR__ . '/../../config.local.php';

        $overloadConfig = [];
        if (file_exists($overloadConfigFile)) {
            $overloadConfig = require $overloadConfigFile;
        }

        self::$config = array_merge($globalConfig, $overloadConfig);
    }

    public static function config(string $configName = null)
    {
        if (self::$config === null) {
            new self();
        }

        if ($configName) {
            return self::$config[$configName] ?: null;
        }

        return self::$config;
    }
}
