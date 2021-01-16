<?php

/**
 * Конфигурационный файл приложения
 */

use Aigletter\Core\AbstractFactory;
use Aigletter\Core\Components\Database\DbFactory;
use Aigletter\Core\Components\Hello\HelloFactory;
use Aigletter\Core\Components\Logger\LoggerFactory;
use Aigletter\Core\Components\Router\RouterFactory;
use Aigletter\Core\Components\Storage\Storage;
use App\Component\Test\TestFactory;

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
                'logFile' => $_SERVER['DOCUMENT_ROOT'] . '/../storage/logs/log.txt',
            ],
        ],
        'logger2' => [
            'factory' => LoggerFactory::class,
            'params' => [
                'logFile' => $_SERVER['DOCUMENT_ROOT'] . '/../storage/logs/log2.txt',
            ],
        ],
        'db' => [
            'factory' => DbFactory::class,
            'params' => [
                'dsn' => 'test',
                'user' => 'root',
                'password' => 'hello'
            ]
        ],
        'storage' => [
            'factory' => AbstractFactory::class,
            'params' => [
                'class' => Storage::class,
                'fileName' => $_SERVER['DOCUMENT_ROOT'] . '/../storage/logs/log2.txt',
            ]
        ]
    ],
    // ...
    // Здесь могут содержаться другие настройки приложения, кроме сервисов
];