<?php


namespace App\Controllers;


use Aigletter\Core\Application;
use Aigletter\Core\Components\Database\Db;
use App\Component\Test;
use App\Entities\UserMapper;
use App\Model\User;

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
        // ActiveRecord
        $user = User::getById(1);
        $user->description = "Hello world!";
        $user->save();


        // DataMapper
        $mapper = new UserMapper();
        $user = $mapper->getById(1);
        $user->description = 'Test description';
        $mapper->save($user);




        /*$genders = ['male', 'female'];
        for ($i = 0; $i < 10000000; $i++) {
            $gender = $genders[rand(0,1)];
            $values[] = "(" . $i . ",'" . uniqid() . "'," . rand(1,99) .",'" .  $gender . "','"
                . date('Y-m-d H:i:s') . "')";
            if (count($values) >= 50000) {
                $sql = "INSERT INTO tests VALUES " . implode(',', $values);
                Application::getInstance()->get(Db::class)->query($sql);
                $values = [];
            }
        }*/

        //$storage = Application::getInstance()->get(Test::class);
        echo 'Here';

        /** @var Db $db */
        /*$db = Application::getInstance()->get('db');

        $builder = $db->getQueryBuilder();
        $builder->table('clients');
        $client = $builder->one();
        print_r($client);

        $builder = $db->getQueryBuilder();
        $builder->table('clients');
        $builder->where('age > 30');
        $clients = $builder->all();
        print_r($clients);*/
    }

    /**
     * Действие обновление страницы
     */
    public function updateAction()
    {
        echo 'Update action';
    }
}