<?php


namespace Aigletter\Core\Components\Database;


use Aigletter\Core\Application;
use Aigletter\Core\Contracts\ComponentAbstract;
use Aigletter\Core\Contracts\ComponentFactoryAbstract;
use Aigletter\Core\Contracts\ComponentInterface;

class DbFactory extends ComponentFactoryAbstract
{

    protected function createConcreteInstance($params = []): ComponentInterface
    {
        if (empty($params['dsn']) || empty($params['user']) || empty($params['password'])) {
            throw new \Exception('Params dsn, user and password are required');
        }

        $logger = Application::getInstance()->get('logger');

        return new Db($params['dsn'], $params['user'], $params['password'], $logger);
    }
}