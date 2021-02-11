<?php


namespace Aigletter\Core;


use Aigletter\Core\Contracts\ContainerAbstract;
use Aigletter\Core\Contracts\RunnableInterface;

/**
 * Class Application
 * Класс прилоежния - контейнер, который содержит различные сервисы.
 * Приложение можно конфигурировать - добавлять и удалять различные севрисы
 *
 * @package Aigletter\Core
 */
class Application extends ContainerAbstract implements RunnableInterface
{
    /**
     * @var Application Экземпляр приложения
     * Паттерн Singleton
     */
    protected static $instance;

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