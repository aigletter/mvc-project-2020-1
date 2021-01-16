<?php


namespace Aigletter\Core\Components\Database;


use Aigletter\Core\Application;
use Aigletter\Core\Contracts\BootstrapInterface;
use Aigletter\Core\Contracts\ComponentInterface;
use Psr\Log\LoggerInterface;

class Db implements ComponentInterface, BootstrapInterface
{
    protected $dsn;

    protected $user;

    protected $password;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \PDO
     */
    protected $connection;

    public function __construct($dsn, $user, $password, LoggerInterface $logger)
    {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;

        $this->logger = $logger;
    }

    public function bootstrap()
    {
        // TODO: Implement bootstrap() method.
    }

    public function connect()
    {
        $this->connection = new \PDO($this->dsn, $this->user, $this->password);

        $this->logger->debug('Connected');

        Application::getInstance()->get('logger')->debug('Connected');
    }

    public function query(string $sql, array $values = [])
    {
        return $this->connection->query($sql);
    }

    public function getQueryBuilder()
    {
        return new QueryBuilder($this);
    }
}