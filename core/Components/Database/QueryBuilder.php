<?php


namespace Aigletter\Core\Components\Database;


/**
 * Class QueryBuilder
 * @package Aigletter\Core\Components\Database
 */
class QueryBuilder implements QueryBuilderInterface
{
    public const ORDER_ASC = 'ASC';

    public const ORDER_DESC = 'DESC';

    /**
     * @var Db
     */
    protected $db;

    /**
     * @var array|string
     */
    protected $where = [];

    /**
     * @var string
     */
    protected $columns = ['*'];

    /**
     * @var string
     */
    protected $table;

    /**
     * @var int|null
     */
    protected $limit;

    /**
     * @var int|null
     */
    protected $offset;

    /**
     * @var array
     */
    protected $order = [];

    /**
     * QueryBuilder constructor.
     * @param Db $db
     */
    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    /**
     * @param $columns
     * @return QueryBuilderInterface
     */
    public function select($columns): QueryBuilderInterface
    {
        if (is_string($columns)) {
            $columns = explode(',', $columns);
        }

        $this->columns = $columns;

        return $this;
    }

    /**
     * Добавляет условия выборки
     *
     * Можно передавать условие в виде строки, в виде ассоциативного массива или в виде индексированного массива.
     * 1. В случае передачи строки, она будет использваться в условии WHERE в неизменном виде.
     * 2. В случае передачи ассоциативного массива, из него будет формираться условие по следующему принципу:
     * из ключей и значений будут формироваться условия сравнения с оператором '=',
     * например, из массива вида
     * [
     *     'age' => 30,
     *     'status' => 1
     * ]
     * будет сформирована строка 'age = 30 AND status 1'
     * 3. В случае индексированного массива, его элементами должны быть тоже массивы, состоящие из 3 элеметов, где:
     * первый элемент - название колонки, второй элемент - оператор, третий элемент - значение колонки
     * Например, массив вида
     * [
     *     ['age', '>', 30'],
     *     ['status', '!=', 1]
     * ]
     * будет преобразован в строку 'age > 30 AND status != 1
     *
     * @param $conditions
     * @return QueryBuilderInterface
     * @todo Сделать возможность добавлять условия с оператором OR
     */
    public function where($conditions): QueryBuilderInterface
    {
        if (is_array($conditions)) {
            foreach ($conditions as $key => $condition) {
                if (is_int($key)) {
                    $conditions[$key] = [$condition[0], $condition[1], $condition[2]];
                } else {
                    $conditions[$key] = [$key, '=', $condition];
                }
            }
        } else {
            $conditions = (array) $conditions;
        }

        $this->where = $conditions;

        return $this;
    }

    /**
     * @param $table
     * @return QueryBuilderInterface
     */
    public function table($table): QueryBuilderInterface
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @param $limit
     * @return QueryBuilderInterface
     */
    public function limit($limit): QueryBuilderInterface
    {
        $this->limit = (int) $limit;

        return $this;
    }

    /**
     * @param $offset
     * @return QueryBuilderInterface
     */
    public function offset($offset): QueryBuilderInterface
    {
        $this->offset = (int) $offset;

        return $this;
    }

    /**
     * Добавляет выражение ORDER
     *
     * Может принимать как строку, так и массив.
     * В случае передачи строки она будет использована в выражение в исходном виде.
     * В случае передачи массива может быть 2 варианты,
     * 1. Если нужно указать направление сортировки, ключи должны быть названиями колонок, а значениями направление сортировки.
     * Например, ['age' => 'ASC', 'status' => 'DESC']
     * 2. Если направление сортировки не нужны, значениями элементов указываются названия колонок
     * Например, ['age', 'status']
     *
     * @param $order
     * @return QueryBuilderInterface
     */
    public function order($order): QueryBuilderInterface
    {
        $this->order = (array) $order;

        return $this;
    }

    /**
     * Собирает SQL строку
     *
     * @return string
     * @throws \Exception
     */
    public function build(): string
    {
        if (empty($this->table)) {
            throw new \Exception('Table is required');
        }

        $sql = 'SELECT ' . implode(', ', $this->columns) . ' FROM ' . $this->table;

        if ($where = $this->buildWhere()) {
            $sql .= ' WHERE ' . $where;
        }

        if ($order = $this->buildOrder()) {
            $sql .= ' ORDER BY ' . $order;
        }

        if ($this->offset) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        return $sql;
    }

    /**
     * Собирает ORDER
     * @return string
     */
    protected function buildOrder()
    {
        $order = [];
        foreach ($this->order as $key => $value) {
            if (is_string($key)) {
                $order[] = $key . ' ' . strtoupper($value);
            } else {
                $order[] = $value;
            }
        }

        return implode(', ', $order);
    }

    /**
     * Собирает условие WHERE
     * @return string
     */
    protected function buildWhere()
    {
        $where = [];
        foreach ($this->where as $value) {
            if (is_array($value)) {
                $where[] = $value[0] . ' ' . $value[1] . "'" . $value[2] . "'";
            } else {
                $where[] = $value;
            }
        }

        return implode(' AND ', $where);
    }

    /**
     * Собирает строку запроса, получает и возвращает одну запись из базы данных
     *
     * @return array|null
     * @throws \Exception
     * @see QueryBuilder::build()
     */
    public function one(): ?array
    {
        $this->limit = 1;
        $sql = $this->build();

        $results = $this->db->query($sql);

        return $results[0];
    }

    /**
     * Собирает строку запроса, получает и возвращает коллекцию записей из базы данных
     *
     * @return array|null
     * @throws \Exception
     */
    public function all(): ?array
    {
        $sql = $this->build();

        return $this->db->query($sql);
    }
}