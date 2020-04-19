<?php

namespace Core;

use Core\Exception\DatabaseError;

class Database
{

    private $pdo;

    public function __construct()
    {
        // @TODO move to config
        $dsn = 'pgsql:host=db;dbname=tasks;';
        $this->pdo = new \PDO($dsn, 'tasks', 'password');
    }

    private function getErrorMessage(\PDOStatement $stmt)
    {
        $errorInfo = $stmt->errorInfo();
        if(count($errorInfo) == 3){
            return $errorInfo[0]. ' : ' . $errorInfo[2];
        } else {
            return '';
        }
    }

    /**
     * @param string $sql
     * @param array $params
     * @return bool|\PDOStatement
     * @throws DatabaseError
     */
    public function query(string $sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute($params)) {
            return $stmt;
        } else {
            throw new DatabaseError($this->getErrorMessage($stmt));
        }
    }

    public function insert(string $table, array $data): bool
    {
        if (!$data) {
            return false;
        }
        $fieldsQuery = implode(', ', array_keys($data));
        $valuesQuery = implode(', ', array_map(
            function ($e) {
                return ':' . $e;
            },
            array_keys($data)
        ));
        $sql = sprintf('INSERT INTO %s(%s) VALUES(%s)', $table, $fieldsQuery, $valuesQuery);
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($data);
    }

    public function update(string $table, array $data): bool
    {
        if (!$data) {
            return false;
        }
        $valuesQuery = implode(', ', array_map(
            function ($e) {
                return $e . '=:' . $e;
            },
            array_keys($data)
        ));
        $sql = sprintf('UPDATE %s SET %s WHERE id=:id', $table, $valuesQuery);
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($data);
    }
}