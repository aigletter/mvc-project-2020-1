<?php

/**
 * Класс компонента для работы с базой
 *
 * @author Yurii Orlyk
 * @version 1.0
 */


namespace Aigletter\Core\Components\Database;


use Aigletter\Core\Application;
use Aigletter\Core\Contracts\BootstrapInterface;
use Aigletter\Core\Contracts\ComponentInterface;
use Psr\Log\LoggerInterface;

/**
 * Класс для работы с базой данных
 */
class Db implements ComponentInterface, BootstrapInterface
{
    /**
     * @var string Хост
     */
    protected $host;

    /**
     * @var string Пользователь
     */
    protected $user;

    /**
     * @var string Пароль
     */
    protected $password;

    /**
     * @var string База данных
     */
    protected $db;

    /**
     * @var LoggerInterface логер
     */
    protected $logger;

    /**
     * @var \PDO
     */
    protected $connection;

    /**
     * Db constructor.
     * @param string|int $host
     * @param string $user
     * @param string $password
     * @param string $db
     * @param LoggerInterface $logger
     */
    public function __construct($host, $user, $password, $db, LoggerInterface $logger)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->db = $db;

        $this->logger = $logger;

        $this->connect();
    }

    /**
     * Метод для начальной инициализации
     */
    public function bootstrap()
    {
        // TODO: Implement bootstrap() method.
    }

    /**
     * Устанавливает соединение с базой данных
     * @throws \PDOException
     */
    public function connect()
    {
        $dsn = 'mysql:dbname=' . $this->db . ';host=' . $this->host;
        $this->connection = new \PDO($dsn, $this->user, $this->password);

        $this->logger->debug('Connected');

        Application::getInstance()->get('logger')->debug('Connected');
    }

    /**
     * Метод для отправки sql запроса
     *
     * @param string $sql
     * @param array $values
     * @return array|null|false
     */
    public function query(string $sql, array $values = [])
    {
        $result = $this->connection->query($sql);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Создает новый экземпляр квери билдера
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return new QueryBuilder($this);
    }
}