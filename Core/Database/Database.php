<?php

namespace App\Core\Database;

use PDO;
use Exception;
use PDOStatement;
use App\Core\Response;
use App\Core\Application;

class Database
{
    public PDO $connection;
    protected PDOStatement $statement;
    protected array|false $_results;
    protected string|null $_lastInsertId;
    protected int $_rowCount;
    protected int $_fetchType = PDO::FETCH_OBJ;
    protected $_class;
    protected bool $_error = false;
    protected static Database|null $_handler = null;

    /**
     * @throws Exception
     */
    public function __construct($config, $username = "root", $password = "")
    {
        try {
            $dsn = 'mysql:' . http_build_query($config, '', ';');

            $options = [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ];

            $this->connection = new PDO($dsn, $username, $password, $options);
        } catch (Exception $e) {
            abort(Response::INTERNAL_SERVER_ERROR);
//            throw new Exception("Database Error: {$e->getMessage()}");
        }
    }

    public static function getInstance(): Database
    {
        if (!self::$_handler) {
            self::$_handler = Application::$app->database;
        }
        return self::$_handler;
    }
    
    public function execute($sql, $bind = []): static
    {
        $this->_results = false;
        $this->_lastInsertId = null;
        $this->_error = false;
        $this->statement = $this->connection->prepare($sql);
        if (!$this->statement->execute($bind)) {
            $this->_error = true;
        } else {
            $this->_lastInsertId = $this->connection->lastInsertId();
        }

        return $this;
    }

    public function query($sql, $bind = []): static
    {
        $this->execute($sql, $bind);
        if (! $this->_error) {
            $this->_rowCount = $this->statement->rowCount();
            if ($this->_fetchType === PDO::FETCH_CLASS) {
                $this->_results = $this->statement->fetchAll($this->_fetchType, $this->_class);
            } else {
                $this->_results = $this->statement->fetchAll($this->_fetchType);
            }
        }
        return $this;
    }

    public function insert($table, $values): bool
    {
        $fields = [];
        $binds = [];
        foreach ($values as $key => $value) {
            $fields[] = $key;
            $binds[] = ":{$key}";
        }
        $fieldStr = implode('`, `', $fields);
        $bindStr = implode(', ', $binds);
        $sql = "INSERT INTO {$table} (`{$fieldStr}`) VALUES ({$bindStr})";
        $this->execute($sql, $values);
        return ! $this->_error;
    }

    public function update($table, $values, $conditions): bool
    {
        $binds = [];
        $valueStr = "";
        foreach ($values as $field => $value) {
            $valueStr .= ", `{$field}` = :{$field}";
            $binds[$field] = $value;
        }
        $valueStr = ltrim($valueStr, ', ');
        $sql = "UPDATE {$table} SET {$valueStr}";

        if (!empty($conditions)) {
            $conditionStr = " WHERE ";
            foreach ($conditions as $field => $value) {
                $conditionStr .= "`{$field}` = :cond{$field} AND ";
                $binds['cond' . $field] = $value;
            }
            $conditionStr = rtrim($conditionStr, ' AND ');
            $sql .= $conditionStr;
        }
        $this->execute($sql, $binds);
        return ! $this->_error;
    }

    public function results(): false|array
    {
        return $this->_results;
    }

    public function count(): int
    {
        return $this->_rowCount;
    }

    public function lastInsertId(): ?string
    {
        return $this->_lastInsertId;
    }

    public function setClass($class): void
    {
        $this->_class = $class;
    }

    public function getClass()
    {
        return $this->_class;
    }

    public function setFetchType($type): void
    {
        $this->_fetchType = $type;
    }

    public function getFetchType(): int
    {
        return $this->_fetchType;
    }

}