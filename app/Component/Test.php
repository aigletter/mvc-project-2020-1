<?php


namespace App\Component;


use Aigletter\Core\Components\Database\Db;
use Psr\Log\LoggerInterface;

class Test
{
    protected $db;

    protected $name;

    protected $logger;

    public function __construct(Db $db, string $name, LoggerInterface $logger)
    {
        $this->db = $db;
        $this->name = $name;
        $this->logger = $logger;
    }
}