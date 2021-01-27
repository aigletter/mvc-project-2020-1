<?php


namespace Aigletter\Core;


use Aigletter\Core\Components\Router\Router;
use Aigletter\Core\Contracts\BootstrapInterface;
use Aigletter\Core\Contracts\ContainerInterface;
use Aigletter\Core\Contracts\RunnableInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Application
 * Класс прилоежния - контейнер, который содержит различные сервисы.
 * Приложение можно конфигурировать - добавлять и удалять различные севрисы
 *
 * @package Aigletter\Core
 */
class Application implements BootstrapInterface, ContainerInterface, RunnableInterface
{
    /**
     * @var Application Экземпляр приложения
     * Паттерн Singleton
     */
    protected static $instance;

    /**
     * @var array Массив конфигураций
     */
    protected $config;

    /**
     * @var array Массив привязок названий сервисов и фабрик, которые умеют создавать экземпляры этих сервисов
     */
    protected $components = [];

    /**
     * @var array Массив экземпляров сервисов.
     * При создании нового сервиса, экземпляр попадает в этот массив с ключом, который указан в конфиге
     */
    protected $instances = [];

    protected $aliases = [
        LoggerInterface::class => 'logger',
    ];

    /**
     * Метод для получения экземпляра приложения.
     * Паттерн Singleton
     *
     * @param array $config
     * @return Application
     */
    public static function getInstance($config = [])
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * Application constructor.
     *
     * @param array $config Массив конфигураций
     */
    protected function __construct($config = [])
    {
        $this->config = $config;

        $this->bootstrap();
    }

    /**
     * Метод начальной загрузки приложения.
     * В настоящий момент здесь происходит привязка имени сервиса с фабрикой, которая умеет создавать экземпляр севриса
     */
    public function bootstrap()
    {
        if (!empty($this->config['components'])) {
            // Перебираем массив сервисов из конфига и разбиаем каждый сервис - проверяем есть ли у него фабрика
            foreach ($this->config['components'] as $key => $item) {
                if (isset($item['factory']) && class_exists($item['factory'])) {
                    // Здесь мы не создаем обьекты, а лишь добавляем привязку имени сервиса и фабрики
                    $this->components[$key] = $item;
                }
            }
        }
    }

    /**
     * Метод контейнера, который умеет по имени сервиса создавать и/или возвращать уже готовый экземпляр сервиса
     *
     * @param $name
     * @return mixed|null
     */
    public function get($name)
    {
        // Если экземпляр севриса уже был ранее создан, просто достаем его из контейнера и возвращаем
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        // Если это первое обращение к сервису, проверяем есть ли для указанного имени привязка с фабрикой
        // Если такая привязка есть в массиве components, создаем фабрику и запускаем ее метод создания экземпляра
        if (array_key_exists($name, $this->components)) {
            // Здесь испоьзуется паттерн Фабричный метод
            // Все классы фабрик наследуют абстрактный класс, у которого есть метод createInstance
            $factory = new $this->components[$name]['factory'];
            $params = $this->components[$name]['params'] ?? [];
            $instance = $factory->createInstance($params);

            if ($instance instanceof BootstrapInterface) {
                $instance->bootstrap();
            }

            // Вновь созданный экземпляр сервиса добавляем в массив instances
            // Из этого массива будет доставаться уже готовый экземпляр при следующих обращениях к севрису
            $this->instances[$name] = $instance;

            return $instance;
        }

        return null;
    }

    /**
     * Метод проверяет есть ли зарегистрированный или уже созданный сервис в контейнере
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        // Сначала проверяем есть ли уже готовый экземпляр севриса по его имени, если есть возвращаем true
        if (isset($this->instances[$name])) {
            return true;
        }

        // Проверяем есть ли зарегистрированная привязка по имени, если есть возвращаем true
        if (isset($this->components[$name])) {
            return true;
        }

        // В случае если дошли до этого момента, значит ни экземпляра нет ни привязки нет
        return false;
    }

    /**
     * Метод ля запуска приложения
     */
    public function run()
    {
        // Получаем с контейнера севрис router, запускаем роутинг и вызываем функцию, которую вернет роутер
        $router = $this->get('router');
        if ($action = $router->route()) {
            // TODO Сделать передачу параметров в фунцию действия
            // $action($id, $name, $params);
            $action();
        }
    }

    public function getAppPath()
    {
        return $this->config['app_path'];
    }
}