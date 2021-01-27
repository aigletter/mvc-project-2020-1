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
        'router' => [
            'factory' => RouterFactory::class,
        ],
        'logger' => [
            'aliases' => [
                \Psr\Log\LoggerInterface::class
            ],
            'factory' => LoggerFactory::class,
            'params' => [
                'logFile' => realpath(__DIR__ . '/../storage/logs')  . '/log.txt',
            ],
        ],
        'db' => [
            'factory' => DbFactory::class,
            'params' => [
                'host' => 'localhost',
                'user' => 'root',
                'password' => '1q2w3e',
                'db' => 'examples'
            ]
        ],
        /*'storage' => [
            'factory' => AbstractFactory::class,
            'params' => [
                'class' => Storage::class,
                'fileName' => $_SERVER['DOCUMENT_ROOT'] . '/../storage/logs/log2.txt',
            ]
        ]*/
    ],
    // ...
    // Здесь могут содержаться другие настройки приложения, кроме сервисов
    'app_path' => realpath(__DIR__ . '/../')

];