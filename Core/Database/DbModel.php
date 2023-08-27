<?php

namespace App\Core\Database;

use PDO;
use Exception;
use App\Core\Model;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use AllowDynamicProperties;
use App\Core\Database\Database;

#[AllowDynamicProperties] abstract class DbModel extends Model
{
    abstract public static function tableName(): string;
    private static $columns = false;
    protected bool $_validationPassed = true;
    protected array $_errors = [];
    protected array $_skipUpdate = [];

    protected static function getDb($setFetchClass = false): Database
    {
        $db = Database::getInstance();
        if ($setFetchClass) {
            $db->setClass(get_called_class());
            $db->setFetchType(PDO::FETCH_CLASS);
        }
        return $db;
    }

    public static function insert($values): bool
    {
        $tableName = static::tableName();
        $db = static::getDb();
        return $db->insert($tableName, $values);
    }

    public static function update($values, $conditions): bool
    {
        $tableName = static::tableName();
        $db = static::getDb();
        return $db->update($tableName, $values, $conditions);
    }

    public function delete(): Database
    {
        $db = static::getDb();
        $tableName = static::tableName();
        $params = [
            'conditions' => "id = :id",
            'bind' => ['id' => $this->id]
        ];
        list('sql' => $conds, 'bind' => $bind) = self::queryParamBuilder($params);
        $sql = "DELETE FROM {$tableName} {$conds}";
        return $db->execute($sql, $bind);
    }

    public static function find($params = []): false|array
    {
        $db = static::getDb(true);
        list('sql' => $sql, 'bind' => $bind) = self::selectBuilder($params);
        return $db->query($sql, $bind)->results();
    }

    public static function rssfind($params = []): false|array
    {
        $db = static::getDb(true);
        $db->setFetchType(PDO::FETCH_ASSOC);
        list('sql' => $sql, 'bind' => $bind) = self::selectBuilder($params);
        return $db->query($sql, $bind)->results();
    }

    /**
     * @throws Exception
     */
    public static function findOrAbort($params = []): false|array
    {
        $result = self::find($params);

        if (!$result) {
            abort(Response::LARAGON_RESULT);
        }

        return $result;
    }

    public static function findFirst($params = [])
    {
        $db = static::getDb(true);
        list('sql' => $sql, 'bind' => $bind) = self::selectBuilder($params);
        $results = $db->query($sql, $bind)->results();
        return $results[0] ?? false;
    }

    public static function findById($id)
    {
        return static::findFirst(
            [
                'conditions' => "id = :id",
                'bind' => ['id' => $id]
            ]
        );
    }

    public static function findTotal($params = [])
    {
        unset($params['limit']);
        unset($params['offset']);
        $tableName = static::tableName();
        $sql = "SELECT COUNT(*) AS total FROM {$tableName}";
        list('sql' => $conds, 'bind' => $bind) = self::queryParamBuilder($params);
        $sql .= $conds;
        $db = static::getDb();
        $results = $db->query($sql, $bind);
        return sizeof($results->results()) > 0 ? $results->results()[0]->total : 0;
    }

    public function save(): bool
    {
        $tableName = static::tableName();
        $save = false;
        $this->beforeSave();
        if ($this->_validationPassed) {
            $db = static::getDb();
            $values = $this->getValuesForSave();
            if ($this->isNew()) {
                $save = $db->insert($tableName, $values);
                if ($save) {
                    $this->id = $db->lastInsertId();
                }
            } else {
                $save = $db->update($tableName, $values, ['id' => $this->id]);
            }
        }
        return $save;
    }

    public function isNew(): bool
    {
        return empty($this->id);
    }
    public static function selectBuilder($params = []): array
    {
        $columns = array_key_exists('columns', $params) ? $params['columns'] : "*";
        $tableName = static::tableName();
        $sql = "SELECT {$columns} FROM {$tableName}";
        list('sql' => $conds, 'bind' => $bind) = self::queryParamBuilder($params);
        $sql .= $conds;
        return ['sql' => $sql, 'bind' => $bind];
    }

    public static function queryParamBuilder($params = []): array
    {
        $sql = "";
        $bind = array_key_exists('bind', $params) ? $params['bind'] : [];
        // joins
        // [['table2', 'table1.id = table2.key', 'tableAlias', 'LEFT' ]]
        if (array_key_exists('joins', $params)) {
            $joins = $params['joins'];
            foreach ($joins as $join) {
                $joinTable = $join[0];
                $joinOn = $join[1];
                $joinAlias = $join[2] ?? "";
                $joinType = isset($join[3]) ? "{$join[3]} JOIN" : "JOIN";
                $sql .= " {$joinType} {$joinTable} {$joinAlias} ON {$joinOn}";
            }
        }

        // where
        if (array_key_exists('conditions', $params)) {
            $conds = $params['conditions'];
            $sql .= " WHERE {$conds}";
        }

        // group
        if (array_key_exists('group', $params)) {
            $group = $params['group'];
            $sql .= " GROUP BY {$group}";
        }

        // order
        if (array_key_exists('order', $params)) {
            $order = $params['order'];
            $sql .= " ORDER BY {$order}";
        }

        // limit
        if (array_key_exists('limit', $params)) {
            $limit = $params['limit'];
            $sql .= " LIMIT {$limit}";
        }

        // offset
        if (array_key_exists('offset', $params)) {
            $offset = $params['offset'];
            $sql .= " OFFSET {$offset}";
        }
        return ['sql' => $sql, 'bind' => $bind];
    }

    public function getValuesForSave(): array
    {
        $columns = static::getColumns();
        $values = [];
        foreach ($columns as $column) {
            if (!in_array($column, $this->_skipUpdate)) {
                $values[$column] = $this->{$column};
            }
        }
        return $values;
    }

    public static function getColumns(): bool|array
    {
        if (!static::$columns) {
            $db = static::getDb();
            $tableName = static::tableName();
            $sql = "SHOW COLUMNS FROM {$tableName}";
            $results = $db->query($sql)->results();
            $columns = [];
            foreach ($results as $column) {
                $columns[] = $column->Field;
            }
            static::$columns = $columns;
        }
        return static::$columns;
    }

    public function runValidation($validator): void
    {
        $validates = $validator->runValidation();
        if (!$validates) {
            $this->_validationPassed = false;
            $this->_errors[$validator->field] = $validator->msg;
        }
    }

    public function getErrors(): array
    {
        return $this->_errors;
    }

    public function setError($name, $value): void
    {
        $this->_errors[$name] = $value;
        $this->_validationPassed = false;
    }

    /**
     * @throws Exception
     */
    public function timeStamps(): void
    {
        date_default_timezone_set(Config::get('time_zone'));
        
        $dt = new \DateTime("now", new \DateTimeZone(Config::get('time_zone')));
        $now = $dt->format('Y-m-d H:i:s');
        $this->updated_at = $now;
        if ($this->isNew()) {
            $this->created_at = $now;
        }
    }

    public static function mergeWithPagination($params = [])
    {
        $request = new Request();
        $page = $request->get('page');
        if (!$page || $page < 1)
            $page = 1;
        $limit = $request->get('limit') ? $request->get('limit') : 25;
        $offset = ($page - 1) * $limit;
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        return $params;
    }

    public function beforeSave(): void
    {
    }

}