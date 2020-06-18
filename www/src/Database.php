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
        $fields = $this->makeCommaSeparatedList(array_keys($data));
        $placeholders = $this->makePlaceholdersWithoutFields($data);
        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, $fields, $placeholders);

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
        $placeholders = $this->makePlaceholdersWithFields($data);
        $sql = sprintf('UPDATE %s SET %s WHERE id=:id', $table, $placeholders);

        $stmt = $this->prepareAndExecute($sql, $data);
        return $stmt->rowCount();
    }

    private function makePlaceholdersWithFields(array $data)
    {
        return $this->makePlaceholders($data, function ($param) {
            return $param . '=:' . $param;
        });
    }

    private function makePlaceholdersWithoutFields(array $data)
    {
        return $this->makePlaceholders($data, function ($param) {
            return ':' . $param;
        });
    }

    private function makePlaceholders(array $data, Callable $formatter)
    {
        $placeholders = array_map(
            $formatter,
            array_keys($data)
        );
        return $this->makeCommaSeparatedList($placeholders);
    }

    private function makeCommaSeparatedList(array $arr)
    {
        return implode(', ', $arr);
    }

    private function getErrorMessage(\PDOStatement $stmt): string
    {
        $errorInfo = $stmt->errorInfo();
        if (count($errorInfo) == 3) {
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
}