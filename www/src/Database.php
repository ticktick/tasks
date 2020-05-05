<?php

namespace Core;

use Core\Exception\DatabaseError;

class Database
{

    private $pdo;

    public function __construct(array $config)
    {
        $dsn = sprintf('%s:host=%s;dbname=%s;', $config['driver'], $config['host'], $config['dbname']);
        $this->pdo = new \PDO($dsn, $config['username'], $config['password']);
    }

    private function getErrorMessage(\PDOStatement $stmt): string
    {
        $errorInfo = $stmt->errorInfo();
        if(count($errorInfo) == 3){
            [$sqlState, $driverCode, $driverMessage] = $errorInfo;
            return sprintf('SQLSTATE: %s; Error message: %s; Error code: %s',
                $sqlState, $driverMessage, $driverCode);
        } else {
            return '';
        }
    }

    /**
     * @param string $sql
     * @param array $params
     * @return \PDOStatement
     * @throws DatabaseError
     */
    private function prepareAndExecute(string $sql, array $data): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        if ($stmt && $stmt->execute($data)) {
            return $stmt;
        } else {
            throw new DatabaseError($this->getErrorMessage($stmt));
        }
    }

    /**
     * @param string $sql
     * @param array $data
     * @return \PDOStatement
     * @throws DatabaseError
     */
    public function query(string $sql, array $data = []): \PDOStatement
    {
        return $this->prepareAndExecute($sql, $data);
    }

    /**
     * @param string $table
     * @param array $data
     * @return int
     * @throws DatabaseError
     */
    public function insert(string $table, array $data): int
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

        $stmt = $this->prepareAndExecute($sql, $data);
        return $stmt->rowCount();
    }

    /**
     * @param string $table
     * @param array $data
     * @return int
     * @throws DatabaseError
     */
    public function update(string $table, array $data): int
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

        $stmt = $this->prepareAndExecute($sql, $data);
        return $stmt->rowCount();
    }
}