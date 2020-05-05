<?php

namespace Core;

use Core\Exception\ModelTableNotDefined;

abstract class Model implements ModelInterface
{

    private static $database;
    protected $table;
    private $sortField = null;
    private $sortOrder = null;

    /**
     * @throws ModelTableNotDefined
     */
    public function __construct(array $dbconfig)
    {
        if (!self::$database) {
            self::$database = new Database($dbconfig);
        }
        if (!$this->table) {
            throw new ModelTableNotDefined();
        }
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function setSortField(string $field): void
    {
        $this->sortField = $field;
    }

    public function setSortOrder(string $order): void
    {
        $this->sortOrder = $order;
    }

    public function findById(int $id)
    {
        $stmt = self::$database->query('SELECT * FROM ' . $this->table . ' WHERE id = :id',
            ['id' => $id]);
        $row = $stmt->fetch();
        return $row;
    }

    public function add(array $data = []): bool
    {
        return self::$database->insert($this->table, $data);
    }

    public function update(array $data = []): bool
    {
        return self::$database->update($this->table, $data);
    }

    /**
     * @return int
     * @throws Exception\DatabaseError
     */
    public function count(): int
    {
        $stmt = self::$database->query('SELECT count(*) AS cnt FROM ' . $this->table);

        $row = $stmt->fetch();
        return $row['cnt'];
    }

    /**
     * @param int $page
     * @param int $pagePerPage
     * @param array $fields
     * @return array
     * @throws Exception\DatabaseError
     */
    public function find(int $page, int $pagePerPage, array $fields = []): array
    {
        $filedsQuery = $fields ? implode(', ', $fields) : '*';
        $limitQuery = sprintf('LIMIT %d OFFSET %d', $pagePerPage, ($page - 1) * $pagePerPage);
        $orderQuery = sprintf('ORDER BY %s %s', $this->sortField, $this->sortOrder);
        $stmt = self::$database->query('SELECT ' . $filedsQuery . ' FROM ' . $this->table . ' ' .
            $orderQuery . ' ' . $limitQuery, []);

        $result = [];
        foreach ($stmt as $row) {
            $result[] = $row;
        }
        return $result;
    }
}