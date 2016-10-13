<?php
/**
 * This file is part of FoxPanel, licensed under the MIT License
 *
 * Copyright (c) 2016. FoxDenStudio - http://foxdenstudio.net/
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

/**
 * Created by IntelliJ IDEA.
 * User: d4rkfly3r
 * Date: 10/11/2016
 * Time: 11:57 AM
 */

namespace Helpers;


class Database
{
    public static $prefix = '';
    protected static $_instance;
    protected $_mysqliInstance;
    protected $_query;
    protected $_lastQuery;
    protected $_queryOptions = [];
    protected $_join = [];
    protected $_where = [];
    protected $_joinAnd = [];
    protected $_having = [];
    protected $_orderBy = [];
    protected $_groupBy = [];
    protected $_tableLocks = [];
    protected $_tableLockMethod = "READ";
    protected $_bindParams = ['']; // Create the empty 0 index
    public $count = 0;
    public $totalCount = 0;
    protected $_stmtError;
    protected $_stmtErrno;

    protected $host;
    protected $username;
    protected $password;
    protected $database;
    protected $port;
    protected $charset;

    protected $isSubQuery = false;
    protected $_lastInsertId = null;
    protected $_updateColumns = null;
    public $returnType = 'array';
    protected $_nestJoin = false;
    private $_transaction_in_progress;
    private $_tableName = '';
    protected $_forUpdate = false;
    protected $_lockInShareMode = false;
    protected $_mapKey = null;
    public $pageLimit = 20;
    public $totalPages = 0;

    /**
     * Database constructor.
     *
     * @param string $host
     * @param string $port
     * @param string $username
     * @param string $password
     * @param string $database
     * @param string $charset
     * @throws \Exception
     */
    public function __construct($host = DB_HOST, $port = DB_PORT, $username = DB_USER, $password = DB_PASSWORD, $database = DB_NAME, $charset = DB_CHARSET)
    {

        $isSubQuery = false;

        // if params were passed as a k->v array
        if (is_array($host)) {
            foreach ($host as $key => $val) {
                $$key = $val;
            }
        } else {
            $this->host = $host;
        }

        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->port = $port;
        $this->charset = $charset;

        if (empty($this->host)) {
            throw new \Exception('MySQL host is not set!');
        }

        if ($isSubQuery) {
            $this->isSubQuery = true;
            return;
        }
        if (isset($prefix)) {
            $this->setPrefix($prefix);
        }
        self::$_instance = $this;
    }

    public function connect()
    {
        if ($this->isSubQuery) {
            return;
        }

        $this->_mysqliInstance = new \mysqli($this->host, $this->username, $this->password, $this->database, $this->port);

        if ($this->_mysqliInstance->connect_error) {
            throw new \Exception('MySQL Connection Error: ' . $this->_mysqliInstance->connect_errno . ': ' . $this->_mysqliInstance->connect_error, $this->_mysqliInstance->connect_errno);
        }

        if ($this->charset) {
            $this->_mysqliInstance->set_charset($this->charset);
        }
    }

    /**
     * @return \mysqli
     */
    public function mysqli()
    {
        if (!$this->_mysqliInstance) {
            $this->connect();
        }
        return $this->_mysqliInstance;
    }

    private static function getInstance()
    {
        return self::$_instance;
    }

    protected function reset()
    {
        $this->_where = [];
        $this->_having = [];
        $this->_join = [];
        $this->_joinAnd = [];
        $this->_orderBy = [];
        $this->_groupBy = [];
        $this->_bindParams = [''];
        $this->_query = null;
        $this->_queryOptions = [];
        $this->returnType = 'array';
        $this->_nestJoin = false;
        $this->_forUpdate = false;
        $this->_lockInShareMode = false;
        $this->_tableName = '';
        $this->_lastInsertId = null;
        $this->_updateColumns = null;
        $this->_mapKey = null;

        return $this;
    }

    public function jsonBuilder()
    {
        $this->returnType = 'json';
        return $this;
    }

    public function arrayBuilder()
    {
        $this->returnType = 'array';
        return $this;
    }

    public function objectBuilder()
    {
        $this->returnType = 'object';
        return $this;
    }

    public function setPrefix($prefix = '')
    {
        self::$prefix = $prefix;
        return $this;
    }

    public function rawQuery($query, $bindParams = null)
    {
        $params = ['']; // Create the empty 0 index
        $this->_query = $query;
        $stmt = $this->_prepareQuery();
        if (is_array($bindParams) === true) {
            foreach ($bindParams as $prop => $val) {
                $params[0] .= $this->_determineType($val);
                array_push($params, $bindParams[$prop]);
            }
            call_user_func_array([$stmt, 'bind_param'], $this->refValues($params));
        }
        $stmt->execute();
        $this->count = $stmt->affected_rows;
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $this->_lastQuery = $this->replacePlaceHolders($this->_query, $params);
        $res = $this->_dynamicBindResults($stmt);
        $this->reset();
        return $res;
    }

    public function rawQueryOne($query, $bindParams = null)
    {
        $res = $this->rawQuery($query, $bindParams);
        if (is_array($res) && isset($res[0])) {
            return $res[0];
        }
        return null;
    }

    public function rawQueryValue($query, $bindParams = null)
    {
        $res = $this->rawQuery($query, $bindParams);
        if (!$res) {
            return null;
        }
        $limit = preg_match('/limit\s+1;?$/i', $query);
        $key = key($res[0]);
        if (isset($res[0][$key]) && $limit == true) {
            return $res[0][$key];
        }
        $newRes = Array();
        for ($i = 0; $i < $this->count; $i++) {
            $newRes[] = $res[$i][$key];
        }
        return $newRes;
    }

    public function query($query, $numRows = null)
    {
        $this->_query = $query;
        $stmt = $this->_buildQuery($numRows);
        $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $res = $this->_dynamicBindResults($stmt);
        $this->reset();
        return $res;
    }

    public function setQueryOption($options)
    {
        $allowedOptions = Array('ALL', 'DISTINCT', 'DISTINCTROW', 'HIGH_PRIORITY', 'STRAIGHT_JOIN', 'SQL_SMALL_RESULT',
            'SQL_BIG_RESULT', 'SQL_BUFFER_RESULT', 'SQL_CACHE', 'SQL_NO_CACHE', 'SQL_CALC_FOUND_ROWS',
            'LOW_PRIORITY', 'IGNORE', 'QUICK', 'MYSQLI_NESTJOIN', 'FOR UPDATE', 'LOCK IN SHARE MODE');

        if (!is_array($options)) {
            $options = Array($options);
        }
        foreach ($options as $option) {
            $option = strtoupper($option);
            if (!in_array($option, $allowedOptions)) {
                throw new \Exception('Wrong query option: ' . $option);
            }
            if ($option == 'MYSQLI_NESTJOIN') {
                $this->_nestJoin = true;
            } elseif ($option == 'FOR UPDATE') {
                $this->_forUpdate = true;
            } elseif ($option == 'LOCK IN SHARE MODE') {
                $this->_lockInShareMode = true;
            } else {
                $this->_queryOptions[] = $option;
            }
        }
        return $this;
    }

    public function withTotalCount()
    {
        $this->setQueryOption('SQL_CALC_FOUND_ROWS');
        return $this;
    }

    public function get($tableName, $numRows = null, $columns = '*')
    {
        if (empty($columns)) {
            $columns = '*';
        }

        $column = is_array($columns) ? implode(', ', $columns) : $columns;
        if (strpos($tableName, '.') === false) {
            $this->_tableName = self::$prefix . $tableName;
        } else {
            $this->_tableName = $tableName;
        }

        $this->_query = 'SELECT ' . implode(' ', $this->_queryOptions) . ' ' . $column . " FROM " . $this->_tableName;
        $stmt = $this->_buildQuery($numRows);
        if ($this->isSubQuery) {
            return $this;
        }

        $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $res = $this->_dynamicBindResults($stmt);
        $this->reset();
        return $res;
    }

    public function getOne($tableName, $columns = '*')
    {
        $res = $this->get($tableName, 1, $columns);
        if ($res instanceof Database) {
            return $res;
        } elseif (is_array($res) && isset($res[0])) {
            return $res[0];
        } elseif ($res) {
            return $res;
        }
        return null;
    }

    public function getValue($tableName, $column, $limit = 1)
    {
        $res = $this->arrayBuilder()->get($tableName, $limit, "{$column} AS retval");
        if (!$res) {
            return null;
        }
        if ($limit == 1) {
            if (isset($res[0]["retval"])) {
                return $res[0]["retval"];
            }
            return null;
        }
        $newRes = Array();
        for ($i = 0; $i < $this->count; $i++) {
            $newRes[] = $res[$i]['retval'];
        }
        return $newRes;
    }

    public function insert($tableName, $insertData)
    {
        return $this->_buildInsert($tableName, $insertData, 'INSERT');
    }

    public function insertMulti($tableName, array $multiInsertData, array $dataKeys = null)
    {
        // only auto-commit our inserts, if no transaction is currently running
        $autoCommit = (isset($this->_transaction_in_progress) ? !$this->_transaction_in_progress : true);
        $ids = [];
        if ($autoCommit) {
            $this->startTransaction();
        }
        foreach ($multiInsertData as $insertData) {
            if ($dataKeys !== null) {
                // apply column-names if given, else assume they're already given in the data
                $insertData = array_combine($dataKeys, $insertData);
            }
            $id = $this->insert($tableName, $insertData);
            if (!$id) {
                if ($autoCommit) {
                    $this->rollback();
                }
                return false;
            }
            $ids[] = $id;
        }
        if ($autoCommit) {
            $this->commit();
        }
        return $ids;
    }

    public function replace($tableName, $insertData)
    {
        return $this->_buildInsert($tableName, $insertData, 'REPLACE');
    }

    public function has($tableName)
    {
        $this->getOne($tableName, '1');
        return $this->count >= 1;
    }

    public function update($tableName, $tableData, $numRows = null)
    {
        if ($this->isSubQuery) {
            return false;
        }
        $this->_query = "UPDATE " . self::$prefix . $tableName;
        $stmt = $this->_buildQuery($numRows, $tableData);
        $status = $stmt->execute();
        $this->reset();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $this->count = $stmt->affected_rows;
        return $status;
    }

    public function delete($tableName, $numRows = null)
    {
        if ($this->isSubQuery) {
            return false;
        }
        $table = self::$prefix . $tableName;
        if (count($this->_join)) {
            $this->_query = "DELETE " . preg_replace('/.* (.*)/', '$1', $table) . " FROM " . $table;
        } else {
            $this->_query = "DELETE FROM " . $table;
        }
        $stmt = $this->_buildQuery($numRows);
        $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $this->reset();
        return ($stmt->affected_rows > 0);
    }

    public function where($whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        // forkaround for an old operation api
        if (is_array($whereValue) && ($key = key($whereValue)) != "0") {
            $operator = $key;
            $whereValue = $whereValue[$key];
        }
        if (count($this->_where) == 0) {
            $cond = '';
        }
        $this->_where[] = [$cond, $whereProp, $operator, $whereValue];
        return $this;
    }

    public function onDuplicate($updateColumns, $lastInsertId = null)
    {
        $this->_lastInsertId = $lastInsertId;
        $this->_updateColumns = $updateColumns;
        return $this;
    }

    public function orWhere($whereProp, $whereValue = 'DBNULL', $operator = '=')
    {
        return $this->where($whereProp, $whereValue, $operator, 'OR');
    }

    public function having($havingProp, $havingValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        // forkaround for an old operation api
        if (is_array($havingValue) && ($key = key($havingValue)) != "0") {
            $operator = $key;
            $havingValue = $havingValue[$key];
        }
        if (count($this->_having) == 0) {
            $cond = '';
        }
        $this->_having[] = [$cond, $havingProp, $operator, $havingValue];
        return $this;
    }

    public function orHaving($havingProp, $havingValue = null, $operator = null)
    {
        return $this->having($havingProp, $havingValue, $operator, 'OR');
    }

    public function join($joinTable, $joinCondition, $joinType = '')
    {
        $allowedTypes = ['LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER'];
        $joinType = strtoupper(trim($joinType));
        if ($joinType && !in_array($joinType, $allowedTypes)) {
            throw new \Exception('Wrong JOIN type: ' . $joinType);
        }
        if (!is_object($joinTable)) {
            $joinTable = self::$prefix . $joinTable;
        }
        $this->_join[] = Array($joinType, $joinTable, $joinCondition);
        return $this;
    }

    public function orderBy($orderByField, $orderbyDirection = "DESC", $customFields = null)
    {
        $allowedDirection = ["ASC", "DESC"];
        $orderbyDirection = strtoupper(trim($orderbyDirection));
        $orderByField = preg_replace("/[^-a-z0-9\.\(\),_`\*\'\"]+/i", '', $orderByField);
        // Add table prefix to orderByField if needed.
        //FIXME: We are adding prefix only if table is enclosed into `` to distinguish aliases
        // from table names
        $orderByField = preg_replace('/(\`)([`a-zA-Z0-9_]*\.)/', '\1' . self::$prefix . '\2', $orderByField);
        if (empty($orderbyDirection) || !in_array($orderbyDirection, $allowedDirection)) {
            throw new \Exception('Wrong order direction: ' . $orderbyDirection);
        }
        if (is_array($customFields)) {
            foreach ($customFields as $key => $value) {
                $customFields[$key] = preg_replace("/[^-a-z0-9\.\(\),_` ]+/i", '', $value);
            }
            $orderByField = 'FIELD (' . $orderByField . ', "' . implode('","', $customFields) . '")';
        }
        $this->_orderBy[$orderByField] = $orderbyDirection;
        return $this;
    }

    /**
     * @param Database
     * @return $this
     */
    public function groupBy($groupByField)
    {
        $groupByField = preg_replace("/[^-a-z0-9\.\(\),_\*]+/i", '', $groupByField);
        $this->_groupBy[] = $groupByField;
        return $this;
    }

    /**
     * @param $method
     * @return Database
     * @throws \Exception
     */
    public function setLockMethod($method)
    {
        // Switch the uppercase string
        switch (strtoupper($method)) {
            // Is it READ or WRITE?
            case "READ" || "WRITE":
                // Succeed
                $this->_tableLockMethod = $method;
                break;
            default:
                // Else throw an exception
                throw new \Exception("Bad lock type: Can be either READ or WRITE");
                break;
        }
        return $this;
    }

    /**
     * @param $table
     * @return bool
     * @throws \Exception
     */
    public function lock($table)
    {
        // Main Query
        $this->_query = "LOCK TABLES";

        // Is the table an array?
        if (gettype($table) == "array") {
            // Loop trough it and attach it to the query
            foreach ($table as $key => $value) {
                if (gettype($value) == "string") {
                    if ($key > 0) {
                        $this->_query .= ",";
                    }
                    $this->_query .= " " . self::$prefix . $value . " " . $this->_tableLockMethod;
                }
            }
        } else {
            // Build the table prefix
            $table = self::$prefix . $table;

            // Build the query
            $this->_query = "LOCK TABLES " . $table . " " . $this->_tableLockMethod;
        }
        // Exceute the query unprepared because LOCK only works with unprepared statements.
        $result = $this->queryUnprepared($this->_query);
        $errno = $this->mysqli()->errno;

        // Reset the query
        $this->reset();
        // Are there rows modified?
        if ($result) {
            // Return true
            // We can't return ourself because if one table gets locked, all other ones get unlocked!
            return true;
        } else {
            throw new \Exception("Locking of table " . $table . " failed", $errno);
        }
    }

    /**
     * @return Database
     * @throws \Exception
     */
    public function unlock()
    {
        // Build the query
        $this->_query = "UNLOCK TABLES";
        // Exceute the query unprepared because UNLOCK and LOCK only works with unprepared statements.
        $result = $this->queryUnprepared($this->_query);
        $errno = $this->mysqli()->errno;
        // Reset the query
        $this->reset();
        // Are there rows modified?
        if ($result) {
            // return self
            return $this;
        } else {
            throw new \Exception("Unlocking of tables failed", $errno);
        }
    }

    public function getInsertId()
    {
        return $this->mysqli()->insert_id;
    }

    public function escape($str)
    {
        return $this->mysqli()->real_escape_string($str);
    }

    public function ping()
    {
        return $this->mysqli()->ping();
    }

    protected function _determineType($item)
    {
        switch (gettype($item)) {
            case 'NULL':
            case 'string':
                return 's';
                break;
            case 'boolean':
            case 'integer':
                return 'i';
                break;
            case 'blob':
                return 'b';
                break;
            case 'double':
                return 'd';
                break;
        }
        return '';
    }

    protected function _bindParam($value)
    {
        $this->_bindParams[0] .= $this->_determineType($value);
        array_push($this->_bindParams, $value);
    }

    protected function _bindParams($values)
    {
        foreach ($values as $value) {
            $this->_bindParam($value);
        }
    }

    /**
     * @param $operator
     * @param mixed $value
     * @return string
     */
    protected function _buildPair($operator, $value)
    {
        if (!is_object($value)) {
            $this->_bindParam($value);
            return ' ' . $operator . ' ? ';
        }
        $subQuery = $value->getSubQuery();
        $this->_bindParams($subQuery['params']);
        return " " . $operator . " (" . $subQuery['query'] . ") " . $subQuery['alias'];
    }

    private function _buildInsert($tableName, $insertData, $operation)
    {
        if ($this->isSubQuery) {
            return false;
        }
        $this->_query = $operation . " " . implode(' ', $this->_queryOptions) . " INTO " . self::$prefix . $tableName;
        $stmt = $this->_buildQuery(null, $insertData);
        $status = $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $haveOnDuplicate = !empty ($this->_updateColumns);
        $this->reset();
        $this->count = $stmt->affected_rows;
        if ($stmt->affected_rows < 1) {
            // in case of onDuplicate() usage, if no rows were inserted
            if ($status && $haveOnDuplicate) {
                return true;
            }
            return false;
        }
        if ($stmt->insert_id > 0) {
            return $stmt->insert_id;
        }
        return true;
    }

    protected function _buildQuery($numRows = null, $tableData = null)
    {
        // $this->_buildJoinOld();
        $this->_buildJoin();
        $this->_buildInsertQuery($tableData);
        $this->_buildCondition('WHERE', $this->_where);
        $this->_buildGroupBy();
        $this->_buildCondition('HAVING', $this->_having);
        $this->_buildOrderBy();
        $this->_buildLimit($numRows);
        $this->_buildOnDuplicate($tableData);

        if ($this->_forUpdate) {
            $this->_query .= ' FOR UPDATE';
        }
        if ($this->_lockInShareMode) {
            $this->_query .= ' LOCK IN SHARE MODE';
        }
        $this->_lastQuery = $this->replacePlaceHolders($this->_query, $this->_bindParams);
        if ($this->isSubQuery) {
            return null;
        }
        // Prepare query
        $stmt = $this->_prepareQuery();
        // Bind parameters to statement if any
        if (count($this->_bindParams) > 1) {
            call_user_func_array([$stmt, 'bind_param'], $this->refValues($this->_bindParams));
        }
        return $stmt;
    }

    /**
     * @param $stmt \mysqli_stmt
     * @return array|string
     */
    protected function _dynamicBindResults($stmt)
    {
        $parameters = array();
        $results = array();
        /**
         * @see http://php.net/manual/en/mysqli-result.fetch-fields.php
         */
        $mysqlLongType = 252;
        $shouldStoreResult = false;
        $meta = $stmt->result_metadata();
        // if $meta is false yet sqlstate is true, there's no sql error but the query is
        // most likely an update/insert/delete which doesn't produce any results
        if (!$meta && $stmt->sqlstate)
            return [];
        $row = [];
        while ($field = $meta->fetch_field()) {
            if ($field->type == $mysqlLongType) {
                $shouldStoreResult = true;
            }
            if ($this->_nestJoin && $field->table != $this->_tableName) {
                $field->table = substr($field->table, strlen(self::$prefix));
                $row[$field->table][$field->name] = null;
                $parameters[] = &$row[$field->table][$field->name];
            } else {
                $row[$field->name] = null;
                $parameters[] = &$row[$field->name];
            }
        }
        // avoid out of memory bug in php 5.2 and 5.3. Mysqli allocates lot of memory for long*
        // and blob* types. So to avoid out of memory issues store_result is used
        // https://github.com/joshcam/PHP-MySQLi-Database-Class/pull/119
        if ($shouldStoreResult) {
            $stmt->store_result();
        }
        call_user_func_array([$stmt, 'bind_result'], $parameters);
        $this->totalCount = 0;
        $this->count = 0;
        while ($stmt->fetch()) {
            if ($this->returnType == 'object') {
                $result = new \stdClass ();
                foreach ($row as $key => $val) {
                    if (is_array($val)) {
                        $result->$key = new \stdClass ();
                        foreach ($val as $k => $v) {
                            $result->$key->$k = $v;
                        }
                    } else {
                        $result->$key = $val;
                    }
                }
            } else {
                $result = [];
                foreach ($row as $key => $val) {
                    if (is_array($val)) {
                        foreach ($val as $k => $v) {
                            $result[$key][$k] = $v;
                        }
                    } else {
                        $result[$key] = $val;
                    }
                }
            }
            $this->count++;
            if ($this->_mapKey) {
                $results[$row[$this->_mapKey]] = count($row) > 2 ? $result : end($result);
            } else {
                array_push($results, $result);
            }
        }
        if ($shouldStoreResult) {
            $stmt->free_result();
        }
        $stmt->close();
        // stored procedures sometimes can return more then 1 resultset
        if ($this->mysqli()->more_results()) {
            $this->mysqli()->next_result();
        }
        if (in_array('SQL_CALC_FOUND_ROWS', $this->_queryOptions)) {
            $stmt = $this->mysqli()->query('SELECT FOUND_ROWS()');
            $totalCount = $stmt->fetch_row();
            $this->totalCount = $totalCount[0];
        }
        if ($this->returnType == 'json') {
            return json_encode($results);
        }
        return $results;
    }

    public function _buildDataPairs($tableData, $tableColumns, $isInsert)
    {
        foreach ($tableColumns as $column) {
            $value = $tableData[$column];
            if (!$isInsert) {
                if (strpos($column, '.') === false) {
                    $this->_query .= "`" . $column . "` = ";
                } else {
                    $this->_query .= str_replace('.', '.`', $column) . "` = ";
                }
            }
            // Subquery value
            if ($value instanceof Database) {
                $this->_query .= $this->_buildPair("", $value) . ", ";
                continue;
            }
            // Simple value
            if (!is_array($value)) {
                $this->_bindParam($value);
                $this->_query .= '?, ';
                continue;
            }
            // Function value
            $key = key($value);
            $val = $value[$key];
            switch ($key) {
                case '[I]':
                    $this->_query .= $column . $val . ", ";
                    break;
                case '[F]':
                    $this->_query .= $val[0] . ", ";
                    if (!empty($val[1])) {
                        $this->_bindParams($val[1]);
                    }
                    break;
                case '[N]':
                    if ($val == null) {
                        $this->_query .= "!" . $column . ", ";
                    } else {
                        $this->_query .= "!" . $val . ", ";
                    }
                    break;
                default:
                    throw new \Exception("Wrong operation");
            }
        }
        $this->_query = rtrim($this->_query, ', ');
    }

    protected function _buildOnDuplicate($tableData)
    {
        if (is_array($this->_updateColumns) && !empty($this->_updateColumns)) {
            $this->_query .= " ON DUPLICATE KEY UPDATE ";
            if ($this->_lastInsertId) {
                $this->_query .= $this->_lastInsertId . "=LAST_INSERT_ID (" . $this->_lastInsertId . "), ";
            }
            foreach ($this->_updateColumns as $key => $val) {
                // skip all params without a value
                if (is_numeric($key)) {
                    $this->_updateColumns[$val] = '';
                    unset($this->_updateColumns[$key]);
                } else {
                    $tableData[$key] = $val;
                }
            }
            $this->_buildDataPairs($tableData, array_keys($this->_updateColumns), false);
        }
    }

    protected function _buildInsertQuery($tableData)
    {
        if (!is_array($tableData)) {
            return;
        }
        $isInsert = preg_match('/^[INSERT|REPLACE]/', $this->_query);
        $dataColumns = array_keys($tableData);
        if ($isInsert) {
            if (isset ($dataColumns[0]))
                $this->_query .= ' (`' . implode($dataColumns, '`, `') . '`) ';
            $this->_query .= ' VALUES (';
        } else {
            $this->_query .= " SET ";
        }
        $this->_buildDataPairs($tableData, $dataColumns, $isInsert);
        if ($isInsert) {
            $this->_query .= ')';
        }
    }

    protected function _buildCondition($operator, &$conditions)
    {
        if (empty($conditions)) {
            return;
        }
        //Prepare the where portion of the query
        $this->_query .= ' ' . $operator;
        foreach ($conditions as $cond) {
            list ($concat, $varName, $operator, $val) = $cond;
            $this->_query .= " " . $concat . " " . $varName;
            switch (strtolower($operator)) {
                case 'not in':
                case 'in':
                    $comparison = ' ' . $operator . ' (';
                    if (is_object($val)) {
                        $comparison .= $this->_buildPair("", $val);
                    } else {
                        foreach ($val as $v) {
                            $comparison .= ' ?,';
                            $this->_bindParam($v);
                        }
                    }
                    $this->_query .= rtrim($comparison, ',') . ' ) ';
                    break;
                case 'not between':
                case 'between':
                    $this->_query .= " $operator ? AND ? ";
                    $this->_bindParams($val);
                    break;
                case 'not exists':
                case 'exists':
                    $this->_query .= $operator . $this->_buildPair("", $val);
                    break;
                default:
                    if (is_array($val)) {
                        $this->_bindParams($val);
                    } elseif ($val === null) {
                        $this->_query .= ' ' . $operator . " NULL";
                    } elseif ($val != 'DBNULL' || $val == '0') {
                        $this->_query .= $this->_buildPair($operator, $val);
                    }
            }
        }
    }

    protected function _buildGroupBy()
    {
        if (empty($this->_groupBy)) {
            return;
        }
        $this->_query .= " GROUP BY ";
        foreach ($this->_groupBy as $key => $value) {
            $this->_query .= $value . ", ";
        }
        $this->_query = rtrim($this->_query, ', ') . " ";
    }

    protected function _buildOrderBy()
    {
        if (empty($this->_orderBy)) {
            return;
        }
        $this->_query .= " ORDER BY ";
        foreach ($this->_orderBy as $prop => $value) {
            if (strtolower(str_replace(" ", "", $prop)) == 'rand()') {
                $this->_query .= "rand(), ";
            } else {
                $this->_query .= $prop . " " . $value . ", ";
            }
        }
        $this->_query = rtrim($this->_query, ', ') . " ";
    }

    protected function _buildLimit($numRows)
    {
        if (!isset($numRows)) {
            return;
        }
        if (is_array($numRows)) {
            $this->_query .= ' LIMIT ' . (int)$numRows[0] . ', ' . (int)$numRows[1];
        } else {
            $this->_query .= ' LIMIT ' . (int)$numRows;
        }
    }

    protected function _prepareQuery()
    {
        if (!$stmt = $this->mysqli()->prepare($this->_query)) {
            $msg = $this->mysqli()->error . " query: " . $this->_query;
            $num = $this->mysqli()->errno;
            $this->reset();
            throw new \Exception($msg, $num);
        }
        return $stmt;
    }

    public function __destruct()
    {
        if ($this->isSubQuery) {
            return;
        }
        if ($this->_mysqliInstance) {
            $this->_mysqliInstance->close();
            $this->_mysqliInstance = null;
        }
    }

    protected function refValues(array &$arr)
    {
        //Reference in the function arguments are required for HHVM to work
        //https://github.com/facebook/hhvm/issues/5155
        //Referenced data array is required by mysqli since PHP 5.3+
        if (strnatcmp(phpversion(), '5.3') >= 0) {
            $refs = array();
            foreach ($arr as $key => $value) {
                $refs[$key] = &$arr[$key];
            }
            return $refs;
        }
        return $arr;
    }

    protected function replacePlaceHolders($str, $vals)
    {
        $i = 1;
        $newStr = "";
        if (empty($vals)) {
            return $str;
        }
        while ($pos = strpos($str, "?")) {
            $val = $vals[$i++];
            if (is_object($val)) {
                $val = '[object]';
            }
            if ($val === null) {
                $val = 'NULL';
            }
            $newStr .= substr($str, 0, $pos) . "'" . $val . "'";
            $str = substr($str, $pos + 1);
        }
        $newStr .= $str;
        return $newStr;
    }

    public function getLastQuery()
    {
        return $this->_lastQuery;
    }

    public function getLastError()
    {
        if (!$this->_mysqliInstance) {
            return "mysqli is null";
        }
        return trim($this->_stmtError . " " . $this->mysqli()->error);
    }

    public function getLastErrno()
    {
        return $this->_stmtErrno;
    }

    public function getSubQuery()
    {
        if (!$this->isSubQuery) {
            return null;
        }
        array_shift($this->_bindParams);
        $val = Array('query' => $this->_query,
            'params' => $this->_bindParams,
            'alias' => $this->host
        );
        $this->reset();
        return $val;
    }

    public function interval($diff, $func = "NOW()")
    {
        $types = ["s" => "second", "m" => "minute", "h" => "hour", "d" => "day", "M" => "month", "Y" => "year"];
        $incr = '+';
        $items = '';
        $type = 'd';
        if ($diff && preg_match('/([+-]?) ?([0-9]+) ?([a-zA-Z]?)/', $diff, $matches)) {
            if (!empty($matches[1])) {
                $incr = $matches[1];
            }
            if (!empty($matches[2])) {
                $items = $matches[2];
            }
            if (!empty($matches[3])) {
                $type = $matches[3];
            }
            if (!in_array($type, array_keys($types))) {
                throw new \Exception("invalid interval type in '{$diff}'");
            }
            $func .= " " . $incr . " interval " . $items . " " . $types[$type] . " ";
        }
        return $func;
    }

    public function now($diff = null, $func = "NOW()")
    {
        return ["[F]" => [$this->interval($diff, $func)]];
    }

    public function inc($num = 1)
    {
        if (!is_numeric($num)) {
            throw new \Exception('Argument supplied to inc must be a number');
        }
        return ["[I]" => "+" . $num];
    }

    public function dec($num = 1)
    {
        if (!is_numeric($num)) {
            throw new \Exception('Argument supplied to dec must be a number');
        }
        return ["[I]" => "-" . $num];
    }

    public function not($col = null)
    {
        return ["[N]" => (string)$col];
    }

    public function func($expr, $bindParams = null)
    {
        return ["[F]" => [$expr, $bindParams]];
    }

    public static function subQuery($subQueryAlias = "")
    {
        return new self(['host' => $subQueryAlias, 'isSubQuery' => true]);
    }

    public function copy()
    {
        $copy = unserialize(serialize($this));
        $copy->_mysqli = null;
        return $copy;
    }

    public function startTransaction()
    {
        $this->mysqli()->autocommit(false);
        $this->_transaction_in_progress = true;
        register_shutdown_function(array($this, "_transaction_status_check"));
    }

    public function commit()
    {
        $result = $this->mysqli()->commit();
        $this->_transaction_in_progress = false;
        $this->mysqli()->autocommit(true);
        return $result;
    }

    public function rollback()
    {
        $result = $this->mysqli()->rollback();
        $this->_transaction_in_progress = false;
        $this->mysqli()->autocommit(true);
        return $result;
    }

    public function _transaction_status_check()
    {
        if (!$this->_transaction_in_progress) {
            return;
        }
        $this->rollback();
    }

    public function tableExists($tables)
    {
        $tables = !is_array($tables) ? Array($tables) : $tables;
        $count = count($tables);
        if ($count == 0) {
            return false;
        }
        foreach ($tables as $i => $value)
            $tables[$i] = self::$prefix . $value;
        $this->where('table_schema', $this->database);
        $this->where('table_name', $tables, 'in');
        $this->get('information_schema.tables', $count);
        return $this->count == $count;
    }

    public function map($idField)
    {
        $this->_mapKey = $idField;
        return $this;
    }

    public function paginate($table, $page, $fields = null)
    {
        $offset = $this->pageLimit * ($page - 1);
        $res = $this->withTotalCount()->get($table, [$offset, $this->pageLimit], $fields);
        $this->totalPages = ceil($this->totalCount / $this->pageLimit);
        return $res;
    }

    public function joinWhere($whereJoin, $whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        $this->_joinAnd[$whereJoin][] = [$cond, $whereProp, $operator, $whereValue];
        return $this;
    }

    public function joinOrWhere($whereJoin, $whereProp, $whereValue = 'DBNULL', $operator = '=')
    {
        return $this->joinWhere($whereJoin, $whereProp, $whereValue, $operator, 'OR');
    }

    protected function _buildJoin()
    {
        if (empty ($this->_join))
            return;
        foreach ($this->_join as $data) {
            list ($joinType, $joinTable, $joinCondition) = $data;
            if (is_object($joinTable))
                $joinStr = $this->_buildPair("", $joinTable);
            else
                $joinStr = $joinTable;
            $this->_query .= " " . $joinType . " JOIN " . $joinStr . " on " . $joinCondition;
            // Add join and query
            if (!empty($this->_joinAnd) && isset($this->_joinAnd[$joinStr])) {
                foreach ($this->_joinAnd[$joinStr] as $join_and_cond) {
                    list ($concat, $varName, $operator, $val) = $join_and_cond;
                    $this->_query .= " " . $concat . " " . $varName;
                    $this->conditionToSql($operator, $val);
                }
            }
        }
    }

    private function conditionToSql($operator, $val)
    {
        switch (strtolower($operator)) {
            case 'not in':
            case 'in':
                $comparison = ' ' . $operator . ' (';
                if (is_object($val)) {
                    $comparison .= $this->_buildPair("", $val);
                } else {
                    foreach ($val as $v) {
                        $comparison .= ' ?,';
                        $this->_bindParam($v);
                    }
                }
                $this->_query .= rtrim($comparison, ',') . ' ) ';
                break;
            case 'not between':
            case 'between':
                $this->_query .= " $operator ? AND ? ";
                $this->_bindParams($val);
                break;
            case 'not exists':
            case 'exists':
                $this->_query .= $operator . $this->_buildPair("", $val);
                break;
            default:
                if (is_array($val))
                    $this->_bindParams($val);
                else if ($val === null)
                    $this->_query .= $operator . " NULL";
                else if ($val != 'DBNULL' || $val == '0')
                    $this->_query .= $this->_buildPair($operator, $val);
        }
    }


    private function queryUnprepared($query)
    {
        // Execute query
        $stmt = $this->mysqli()->query($query);
        // Failed?
        if (!$stmt) {
            throw new \Exception("Unprepared Query Failed, ERRNO: " . $this->mysqli()->errno . " (" . $this->mysqli()->error . ")", $this->mysqli()->errno);
        };

        // return stmt for future use
        return $stmt;
    }

}