<?php


namespace Aigletter\Core\Components\Database;


class QueryBuilder
{
    protected $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function build()
    {
        $sql = '';

        // ...

        return $sql;
    }

    public function one()
    {
        $sql = $this->build();
        return $this->db->query($sql);
    }
}