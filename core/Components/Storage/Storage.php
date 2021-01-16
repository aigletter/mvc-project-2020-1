<?php


namespace Aigletter\Core\Components\Storage;


use Aigletter\Core\Contracts\BootstrapInterface;
use Aigletter\Core\Contracts\ComponentInterface;
use Psr\Log\LoggerInterface;

class Storage implements ComponentInterface, BootstrapInterface
{
    protected $fileName;

    public function __construct(string $fileName, LoggerInterface $logger)
    {
        $this->fileName = $fileName;
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

$storage = new Storage();
$storage->test('test',);