<?php


namespace Aigletter\Core\Contracts;


use Psr\Log\LoggerInterface;

abstract class ContainerAbstract implements BootstrapInterface, ContainerInterface
{
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
                // Здесь мы не создаем обьекты, а лишь добавляем привязку имени сервиса и фабрики
                $this->components[$key] = $item;

                // Если в конфиге сервиса указаны алиасы, добавляем их в массив аллиасов
                if (isset($item['aliases'])) {
                    foreach ($item['aliases'] as $alias) {
                        $this->aliases[$alias] = $key;
                    }
                }

                // Если указан класс или ключ является названием класса, автоматически создаем алиас для этомого имени
                // Это нужно для того, чтобы наш контейнер умел их потом доставать, когда эти классы будут указываться в параметрах
                $className = $item['class'] ?? $key;
                if (class_exists($className) && !$this->has($className)) {
                    $this->aliases[$className] = $key;
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

        // Если запросили не по оригинальному имени сервиса, а по его алиасу, ищем в массиве алиасов соответствие
        if (isset($this->aliases[$name])) {
            $name = $this->aliases[$name];
        }

        // Если это первое обращение к сервису, проверяем есть ли для указанного имени привязка с фабрикой
        // Если такая привязка есть в массиве components, создаем фабрику и запускаем ее метод создания экземпляра
        if (array_key_exists($name, $this->components)) {
            // Здесь испоьзуется паттерн Фабричный метод
            // Все классы фабрик наследуют абстрактный класс, у которого есть метод createInstance
            if ($factory = $this->getFactory($name)) {
                $params = $this->components[$name]['params'] ?? [];
                $instance = $factory->createInstance($params);
            }
            // Здесь используем инжектор (DI контейнер)
            else {
                $instance = $this->makeInstance($name);
            }

            // Пока не используем
            if ($instance instanceof BootstrapInterface) {
                $instance->bootstrap();
            }

            // Вновь созданный экземпляр сервиса добавляем в массив instances
            // Из этого массива будет доставаться уже готовый экземпляр при следующих обращениях к севрису
            $this->instances[$name] = $instance;

            return $instance;
        }

        throw new \Exception('Can not get service with name ' . $name);
    }

    /**
     * Метод с помощью ReflectionAPI определяет какие сервисы требуется конструктору создаваемого класса
     * Определенные параметры достаются или создаются тем же контейнером с помощью того же метода get и далее по цепочке вызовов
     *
     * @param $name
     * @return object
     * @throws \ReflectionException
     */
    public function makeInstance($name)
    {
        // Получаем из конфига создаваемого сервиса его класс и дополнительные аргументы, которые нужно передать конструктору
        $config = $this->components[$name];
        $class = $config['class'] ?? $name;
        $arguments = $config['params'] ?? [];
        if (class_exists($class)) {
            // В массив будем складывать созданные/полученные сервисы (зависимости) данного класса
            $instances = [];
            // Через рефлексию получаем класс, конструктор и его параметры
            $reflectionClass = new \ReflectionClass($class);
            $constructor = $reflectionClass->getConstructor();
            $params = $constructor->getParameters();
            // Пробегаемся по всем параметрам, определяем ожидаемые типы и пытаемся их найти
            foreach ($params as $param) {
                $name = $param->getName();
                // Если это не встроенные типы, считаем что это наши пользовательские классы сервисов
                // Определяем эти классы и достаем/создаем их из контейнера
                if (!$param->getType()->isBuiltin()) {
                    $type = $param->getClass()->getName();
                    $instances[$name] = $this->get($type);
                }
                // Если это примитивы, ищем их в массиве из конфига севриса
                elseif (isset($arguments[$name])) {
                    $instances[$name] = $arguments[$name];
                }
            }
        }

        // Здесь мы уже разрулили все ожидаемые конструктором параметры
        // Создаем экземпляр класса сервиса и возвращаем его
        return $reflectionClass->newInstanceArgs($instances);
    }

    protected function getFactory($name)
    {
        if ($factoryName = $this->components[$name]['factory'] ?? null) {
            return new $factoryName();
        }

        return  null;
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

        // Проверяем есть ли зарегистрированные алиасы сервисов по имени, если есть возвращаем true
        if (isset($this->aliases[$name])) {
            return true;
        }

        // В случае если дошли до этого момента, значит ни экземпляра нет ни привязки нет
        return false;
    }
}