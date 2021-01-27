<?php


namespace Aigletter\Core\Components\Router;


use Aigletter\Core\Application;
use Aigletter\Core\Contracts\ComponentAbstract;

/**
 * Class Router
 * Полезный сервис, который занимается роутингом (маршрутизацией).
 * Суть его в том, чтобы определить какое действие (какой метод какого класса) нужно выполнить по каждому конкретному запросу (урлу)
 *
 * @package Aigletter\Core\Components\Router
 */
class Router extends ComponentAbstract
{
    public const METHOD_GET = 'get';

    public const METHOD_POST = 'post';

    protected $routes = [];

    public function bootstrap()
    {
        if (file_exists(Application::getInstance()->getAppPath() . '/routes/routes.php')) {
            include Application::getInstance()->getAppPath() . '/routes/routes.php';
        }
    }

    public function get(string $url, callable $action)
    {
        $this->addRoute(self::METHOD_GET, $url, $action);
    }

    public function post(string $url, callable $action)
    {
        $this->addRoute(self::METHOD_POST, $url, $action);
    }

    /**
     * Добавляет роут
     *
     * @param string $method Http метод
     * @param string $url Урл
     * @param callable $action Действие, которое нужно выполнить при запросе данным методом по данному пути
     */
    public function addRoute(string $method, string $url, callable $action)
    {
        $method = strtolower($method);
        $this->routes[$method][$url] = $action;
    }

    /**
     * Данный метод определяет путь запроса, определяем HTTP метод, проверяет есть ли в настройках такой роут
     * Есть роут сконфигурирован возвращает его, если нет выбрасывает исключение
     *
     * @return \Closure
     * @throws \Exception
     */
    public function route()
    {
        // Получаем путь запроса
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        // Получаем метод запроса
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        // Проверяем есть ли сконфигурирован роут по текущему пути с текущим методом
        if (isset($this->routes[$method][$path])) { // [get]['/page/view']
            return $this->routes[$method][$path];
        }

        throw new \Exception('Not found');
    }

    // Это временный и не очень умный метод.
    public function getParams()
    {
        return $_GET;
    }
}