<?php

namespace TwmDev;

use Zend\EventManager\EventManagerInterface;
use Zend\Loader;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Glob;
use Zend\Config\Factory as ConfigFactory;
use Zend\Console\Console as Console;
use Zend\Console\Request as ConsoleRequest;

class Module
{

    const DEV_ARGUMENT = '-D dev';
    const DEFAULT_CONFIG_GLOB_PATHS = 'config/autoload/{,*.}{dev}.php';

    public function init(ModuleManagerInterface $mm)
    {
        if (!defined('DEV')) {
            if (Console::isConsole()) {
                $params =& $_SERVER['argv'];
                if ($dev = array_search('-dev', $params)) {
                    unset($params[$dev]);
                    define('DEV', true);
                } else {
                    define('DEV', false);
                }
            } else {
                define('DEV', stristr(getenv('APACHE_ARGUMENTS'), self::DEV_ARGUMENT) !== false);
            }
        }
        if (DEV) {
            $eventManager = $mm->getEventManager();
            $eventManager->attach(ModuleEvent::EVENT_LOAD_MODULES_POST, function (ModuleEvent $e) {
                $config = $e->getConfigListener()->getMergedConfig(false);

                if (!isset($config['twmdev']) || !isset($config['twmdev']['config_glob_paths'])) {
                    $configGlobPaths = Module::DEFAULT_CONFIG_GLOB_PATHS;
                } else {
                    $configGlobPaths = $config['twmdev']['config_glob_paths'];
                }

                foreach (Glob::glob($configGlobPaths, Glob::GLOB_BRACE) as $filename) {
                    $config = ArrayUtils::merge($config, ConfigFactory::fromFile($filename));
                }

                $e->getConfigListener()->setMergedConfig($config);
            }, 1337);
        }
    }
}
