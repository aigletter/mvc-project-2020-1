<?php


namespace Aigletter\Core;


use Aigletter\Core\Contracts\ComponentInterface;
use Aigletter\Core\Contracts\CreateInstanceInterface;

class AbstractFactory implements CreateInstanceInterface
{
    public function createInstance($params = []): ComponentInterface
    {
        $className = $params['class'];
        $reflectionClass = new \ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();
        $parameters = $constructor->getParameters();

        $dependencies = [];
        foreach ($parameters as $param) {
            $name = $param->getName();
            if (isset($params[$name])) {
                $dependencies[$name] = $params[$name];
            }
        }

        $instance = $reflectionClass->newInstanceArgs($dependencies);

        return $instance;
    }
}