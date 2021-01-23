<?php


namespace App\Controllers;


use Aigletter\Core\Application;
use Aigletter\Core\Components\Database\Db;

/**
 * Class PageController
 * Контроллер для обработки запросов каких-то страниц нашего прилоежения
 *
 * @package App\Controllers
 */
class PageController
{
    /**
     * Действие по умолчанию
     */
    public function indexAction()
    {
        echo 'IndexAction PageController';
    }

    /**
     * Действие посмотр страницы
     */
    public function viewAction()
    {
        /** @var Db $db */
        $db = Application::getInstance()->get('db');

        $builder = $db->getQueryBuilder();
        $builder->table('clients');
        $client = $builder->one();
        print_r($client);

        $builder = $db->getQueryBuilder();
        $builder->table('clients');
        $builder->where('age > 30');
        $clients = $builder->all();
        print_r($clients);
    }

    /**
     * Действие обновление страницы
     */
    public function updateAction()
    {
        echo 'Update action';
    }
}