<?php


namespace App\Entities;


class User
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