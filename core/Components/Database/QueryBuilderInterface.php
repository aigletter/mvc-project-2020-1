<?php


namespace Aigletter\Core\Components\Database;


interface QueryBuilderInterface
{
    public function select($columns): QueryBuilderInterface;

    public function where($conditions): QueryBuilderInterface;

    public function table($table): QueryBuilderInterface;

    public function limit($limit): QueryBuilderInterface;

    public function offset($offset): QueryBuilderInterface;

    public function order($order): QueryBuilderInterface;

    public function build(): string;

    public function one(): ?array;

    public function all(): ?array;
}