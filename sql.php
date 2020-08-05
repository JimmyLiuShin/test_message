<?php

/**
 * 資料庫處理
 */
class Sql
{
    /**
     * 表名對應
     *
     * @var array
     */
    protected $tableNameMap = [
        'message' => 'message_item',
    ];

    /**
     * 表欄位對應及型態
     *
     * @var array
     */
    protected $tableFieldMap = [
        'message' => [
            'id' => 'int',
            'message_person' => 'str',
            'message_content' => 'str',
            'message_time' => 'str',
            'message_status' => 'int',
        ]
    ];

    /**
     * 取得符合條件之資料個數
     *
     * @params string $table 表名
     * @params array $where SQL條件
     * @return integer
     */
    public function getCount($table = '', $where = [])
    {
        if ($table && !empty($where)) {
            $connPrepare = $this->prep('count', $table, $where);
            $connPrepare = $this->combine('count', $connPrepare, $where, $table);
            $result = $this->output('one', $connPrepare);

            if ($result) {
                return $result['COUNT(1)'];
            }
        }

        return 0;
    }

    /**
     * 取得一筆資料
     *
     * @params string $table 表名
     * @params array $where SQL條件
     * @return null
     */
    public function getOne($table = '', $where = [])
    {
        if ($table && !empty($where)) {
            $connPrepare = $this->prep('select', $table, $where);
            $connPrepare = $this->combine('select', $connPrepare, $where, $table);
            $result = $this->output('one', $connPrepare);

            if ($result) {
                return $result;
            }
        }

        return null;
    }

    /**
     * 取得多筆資料
     *
     * @params string $table 表名
     * @params array $where SQL條件
     * @params array $limit 輸出限制
     * @return array
     */
    public function getAll($table = '', $where = [], $limit = [])
    {
        if ($table) {
            $connPrepare = $this->prep('select', $table, $where, $limit);
            $connPrepare = $this->combine('select', $connPrepare, $where, $table);
            $result = $this->output('all', $connPrepare);

            if ($result) {
                return $result;
            }
        }

        return [];
    }

    /**
     * 新增資料
     *
     * @params string $table 表名
     * @params array $data 新增內容
     */
    public function insertData($table = '', $data = [])
    {
        if ($table && !empty($data)) {
            $connPrepare = $this->prep('insert', $table);
            $this->combine('insert', $connPrepare, $data, $table);
        }
    }

    /**
     * 更新資料
     *
     * @params string $table 表名
     * @params array $key 條件
     * @params array $update 更新內容
     */
    public function updateData($table = '', $key = [], $update = [])
    {
        if ($table && !empty($update)) {
            $connPrepare = $this->prep('update', $table, $update, $key);
            $this->combine('update', $connPrepare, $update, $table, $key);
        }
    }

    /**
     * SQL預處理
     *
     * @params string $method 處理方法
     * @params string $table 資料表名
     * @params array $data 資料內容 | 條件
     * @params array $limit 條件
     * @return object | null
     */
    protected function prep($method = '', $table = '', $data = [], $limit = [])
    {
        $string = '';
        $Connect = $this->connection();

        if ($table != '') {
            if ($method == 'count') {
                $string .= 'SELECT COUNT(1) ';
                $string .= 'FROM ' . $this->tableNameMap[$table] . ' ';
                $string .= $this->prepString($method, $data);
                $connPrepare = $Connect->prepare($string);
            }

            if ($method == 'select') {
                $string .= 'SELECT * ';
                $string .= 'FROM ' . $this->tableNameMap[$table] . ' ';
                $string .= $this->prepString($method, $data);
                $string .= $this->prepString($method, $limit, null, 'limit');
                $connPrepare = $Connect->prepare($string);
            }

            if ($method == 'insert') {
                $string .= 'INSERT INTO ' . $this->tableNameMap[$table];
                $string .= $this->prepString($method, null, $table, 'field');
                $string .= $this->prepString($method, null, $table);
                $connPrepare = $Connect->prepare($string);
            }

            if ($method == 'update') {
                $string .= 'UPDATE ' . $this->tableNameMap[$table];
                $string .= $this->prepString($method, $data, $table, 'value');
                $string .= $this->prepString($method, $limit, $table, 'field');
                $connPrepare = $Connect->prepare($string);
            }
        }

        if (isset($connPrepare)) {
            return $connPrepare;
        }

        return null;
    }

    /**
     * SQL預處理 - 預處理字串產生
     *
     * @params string $method 處理方法
     * @params string $data 資料
     * @params array $table 資料表名
     * @params array $mode 功能
     * @return string
     */
    protected function prepString($method = '', $data = [], $table = '', $mode = '')
    {
        if ($method == 'count' || $method == 'select') {
            $condition = [];

            if (!empty($data) && $mode == 'limit') {
                $result = ' LIMIT ' . $data['start'] . ', ' . $data['count'];
            } else {
                if (!empty($data)) {
                    $sort = 0;
                    foreach ($data as $k => $v) {
                        $condition[] = $k . ' = :' . $sort;
                        $sort++;
                    }
                }
                $result = !empty($condition) ? 'WHERE ' . implode(' and ', $condition) : '';
            }
        }

        if ($method == 'insert') {
            $condition = [];

            if (isset($this->tableFieldMap[$table])) {
                foreach ($this->tableFieldMap[$table] as $k => $v) {
                    if ($k != 'id') {
                        if ($mode == 'field') {
                            $condition[] = $k;
                        } else {
                            $condition[] = ':' . $k;
                        }
                    }
                }
            }

            if ($mode == 'field') {
                $result = !empty($condition) ? '(' . implode(', ', $condition) . ')' : '';
            } else {
                $result = !empty($condition) ? 'VALUES(' . implode(', ', $condition) . ')' : '';
            }
        }

        if ($method == 'update') {
            $condition = [];

            if (!empty($data)) {
                $sort = 0;
                foreach ($data as $k => $v) {
                    $condition[] = $k . ' = :' . $mode . $sort;
                    $sort++;
                }
            }

            if ($mode == 'field') {
                $result = !empty($condition) ? ' WHERE ' . implode(', ', $condition) : '';
            } else {
                $result = !empty($condition) ? ' SET ' . implode(', ', $condition) : '';
            }
        }
        return $result;
    }

    /**
     * 執行 SQL 預處理
     *
     * @params string $method 處理方法
     * @params string $prepare SQL預處理
     * @params array $data 資料
     * @params array $table 資料表名
     * @params array $key 條件
     * @return object | null
     */
    protected function combine($method, $prepare, $data = [], $table = '', $key = [])
    {
        if ($prepare) {
            if ($method == 'count' || $method == 'select') {
                $sort = 0;
                foreach ($data as $k => $v) {
                    if (isset($this->tableFieldMap[$table][$k]) && $this->tableFieldMap[$table][$k] == 'int') {
                        $prepare->bindValue(':' . $sort, $v, PDO::PARAM_INT);
                    } else {
                        $prepare->bindValue(':' . $sort, $v, PDO::PARAM_STR);
                    }
                    $sort++;
                }
            }

            if ($method == 'insert') {
                foreach ($data as $k => $v) {
                    if (isset($this->tableFieldMap[$table][$k]) && $this->tableFieldMap[$table][$k] == 'int') {
                        $prepare->bindValue(':' . $k, $v, PDO::PARAM_INT);
                    } else {
                        $prepare->bindValue(':' . $k, $v, PDO::PARAM_STR);
                    }
                }
            }

            if ($method == 'update') {
                if (!empty($data)) {
                    $sort = 0;
                    foreach ($data as $k => $v) {
                        if (isset($this->tableFieldMap[$table][$k]) && $this->tableFieldMap[$table][$k] == 'int') {
                            $prepare->bindValue(':value' . $sort, $v, PDO::PARAM_INT);
                        } else {
                            $prepare->bindValue(':value' . $sort, $v, PDO::PARAM_STR);
                        }
                        $sort++;
                    }
                }

                if (!empty($key)) {
                    $sort = 0;
                    foreach ($key as $k => $v) {
                        if (isset($this->tableFieldMap[$table][$k]) && $this->tableFieldMap[$table][$k] == 'int') {
                            $prepare->bindValue(':field' . $sort, $v, PDO::PARAM_INT);
                        } else {
                            $prepare->bindValue(':field' . $sort, $v, PDO::PARAM_STR);
                        }
                        $sort++;
                    }
                }
            }
            $prepare->execute();

            return $prepare;
        } else {
            return null;
        }
    }

    /**
     * 輸出 SQL 搜尋結果
     *
     * @params string $type 輸出方式
     * @params string $prepare SQL預處理結果
     * @return array
     */
    protected function output($type, $prepare)
    {
        $result = [];

        if (isset($prepare)) {
            if ($type == 'one') {
                $result = $prepare->fetch(PDO::FETCH_ASSOC);
            }

            if ($type == 'all') {
                $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        return $result;
    }

    /**
     * 資料庫連結
     *
     * @return object
     */
    protected function connection()
    {
        $databaseHost = 'localhost';
        $databaseUser = 'message';
        $databasePassword = '4W;<EH.FHB;rt2ugW%Pb';
        $databaseName = 'shin_message';
        $databaseConnect = 'mysql:host=' . $databaseHost . ';dbname=' . $databaseName;
        try {
            $conn = new PDO($databaseConnect, $databaseUser, $databasePassword);
            $conn->exec('SET CHARACTER SET utf8');
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;
        } catch (PDOException $e) {
            echo 'connection failed: ' . $e->getMessage();
            die;
        }
    }
}


