<?php


namespace Aigletter\Core\Components\Database;


use Aigletter\Core\Application;

abstract class ActiveRecord
{
    public $id;

    protected abstract static function getTable();

    public function save()
    {
        $sql = "UPDATE " . static::getTable() . " SET ";
        foreach ($this as $key => $value) {
            $columns[] = $key . " = '" . $value . "'";
        }
        $sql .= implode(", ", $columns);
        $sql .= " WHERE id = " . $this->id;
        return Application::getInstance()->get('db')
            ->query($sql);
    }

    public static function fromArray($data)
    {
        $instance = new static();
        foreach ($data as $key => $value) {
            $instance->{$key} = $value;
        }
        return $instance;
    }

    public static function getById($id)
    {
        $rows = Application::getInstance()->get('db')
            ->query("SELECT * from " . static::getTable() . " WHERE id = " . $id);
        return self::fromArray($rows[0]);
    }
}