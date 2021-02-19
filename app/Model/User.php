<?php


namespace App\Model;


use Aigletter\Core\Components\Database\ActiveRecord;

class User extends ActiveRecord
{
    protected static function getTable()
    {
        return 'users';
    }

    public $id;

    public $login;

    public $name;

    public $password;

    public $description;

    public function checkPassword()
    {
        // TODO
    }

    public function changeStatus()
    {

    }
}