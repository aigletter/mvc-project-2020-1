<?php


namespace Aigletter\Core\Contracts;


interface CreateInstanceInterface
{
    public function createInstance($params = []): ComponentInterface;
}