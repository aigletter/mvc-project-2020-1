<?php


namespace Aigletter\Core\Components\Storage;


use Aigletter\Core\Components\Database\Db;
use Aigletter\Core\Components\Logger\Logger;
use Aigletter\Core\Components\Router\Router;
use Aigletter\Core\Contracts\BootstrapInterface;
use Aigletter\Core\Contracts\ComponentInterface;

class Storage implements ComponentInterface, BootstrapInterface
{
    protected $fileName;

    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        //$this->fileName = $fileName;
    }

    public function test($test)
    {
        echo $this->fileName;
    }


    public function bootstrap()
    {
        // TODO: Implement bootstrap() method.
    }
}