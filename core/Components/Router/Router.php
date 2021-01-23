<?php


namespace Aigletter\Core\Components\Router;


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
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/../routes/routes.php')) {
            include $_SERVER['DOCUMENT_ROOT'] . '/../routes/routes.php';
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

    /**
     * Пока данный метод парсит строку и по пути запроса определяем контроллер и метод, который нужно вызвать
     * В дальнейшем планируется усовершенствовать этот механизм, чтобы можно было более гибко настраивать роуты
     *
     * @deprecated
     *
     * @return \Closure
     * @throws \Exception
     */
    public function routeOld()
    {
        // Получаем путь с адресной строки
        $path = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        // Определяем контроллер и действие по умолчанию.
        // Если контроллера и действия не будет в строке запроса, будут отрабатывать дефолтные
        $controllerName = 'App\\Controllers\\IndexController';
        $actionName = 'indexAction';

        // Если конроллер есть в урле, определяем имя класса
        if (!empty($path[1])) {
            $controllerName = 'App\\Controllers\\' . ucfirst($path[1]) . 'Controller';
        }

        // Если класс конроллера не найден, выкидываем исключение.
        // В дальнейшем будем это исключение обрабатывать и выводить 404
        if (!class_exists($controllerName)) {
            throw new \Exception("Class not found");
        }

        // Создаем экземпляр класса контроллера
        $controller = new $controllerName();

        // Если действие есть в урле, определяем его имя
        if (isset($path[2])) {
            $actionName = $path[2] . 'Action';
        }

        // Если метод не найден в классе контроллера, выкидываем исключение
        // В дальнейшем будем это исключение обрабатывать и выводить 404
        if (!method_exists($controller, $actionName)) {
            throw new \Exception('Action not found');
        }

        // Возвращаем функцию, которую могут вызвать
        return function() use ($controller, $actionName){
            $controller->$actionName($this->getParams());
        };
    }

    // Это временный и не очень умный метод.
    public function getParams()
    {
        return $_GET;
    }
}