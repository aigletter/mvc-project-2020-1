<?php

/**
 * Конфигурационный файл приложения
 */

use Aigletter\Core\Components\Database\DbFactory;
use Aigletter\Core\Components\Logger\LoggerFactory;
use Aigletter\Core\Components\Router\RouterFactory;

return [
    // Массив конфигураций сервисов
    'components' => [

        // Фабрики
        'router' => [
            'factory' => RouterFactory::class,
            'aliases' => [
                \Aigletter\Core\Components\Router\Router::class
            ],
        ],
        'logger' => [
            'factory' => LoggerFactory::class,
            'params' => [
                'logFile' => realpath(__DIR__ . '/../storage/logs')  . '/log.txt',
            ],
            'aliases' => [
                \Aigletter\Core\Components\Logger\Logger::class,
                \Psr\Log\LoggerInterface::class,
            ],
        ],
        'db' => [
            'factory' => DbFactory::class,
            'params' => [
                'host' => 'localhost',
                'user' => 'root',
                'password' => '1q2w3e',
                'db' => 'examples'
            ],
            'aliases' => [
                \Aigletter\Core\Components\Database\Db::class
            ],
        ],

        // Инжектор
        'storage' => [
            'class' => \Aigletter\Core\Components\Storage\Storage::class,
        ],
        \App\Component\Test::class => [
            'params' => [
                'name' => 'Ivan'
            ],
            'dependencies' => [
                'logger' => \Psr\Log\LoggerInterface::class,
            ]
        ]
    ],
    // ...
    // Здесь могут содержаться другие настройки приложения, кроме сервисов
    'app_path' => realpath(__DIR__ . '/../')

];