<?php

namespace Core;

use Core\Exception\ModelTableNotDefined;

class Model implements ModelInterface
{

    private $database;
    protected $table;
    // @TODO move to config
    private $limit = 3;
    private $sortField = null;
    private $sortOrder = null;

    /**
     * @throws ModelTableNotDefined
     */
    public function __construct()
    {
        $this->database = new Database();
        if (!$this->table) {
            throw new ModelTableNotDefined();
        }
    }

    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    public function setSortField(string $field)
    {
        $this->sortField = $field;
    }

    public function setSortOrder(string $order)
    {
        $this->sortOrder = $order;
    }

    public function findById(int $id)
    {
        $stmt = $this->database->query('SELECT * FROM ' . $this->table . ' WHERE id = :id',
            ['id' => $id]);
        $row = $stmt->fetch();
        return $row;
    }

    public function add(array $data = [])
    {
        return $this->database->insert($this->table, $data);
    }

    public function update(array $data = [])
    {
        return $this->database->update($this->table, $data);
    }

    /**
     * @return int
     * @throws Exception\DatabaseError
     */
    public function count(): int
    {
        $stmt = $this->database->query('SELECT count(*) AS cnt FROM ' . $this->table);

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
        $stmt = $this->database->query('SELECT ' . $filedsQuery . ' FROM ' . $this->table . ' ' .
            $orderQuery . ' ' . $limitQuery, []);

        $result = [];
        foreach ($stmt as $row) {
            $result[] = $row;
        }
        return $result;
    }
}