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
     * 表欄位對應
     *
     * @var array
     */
    protected $tableFieldMap = [
        'message' => [
            'id'              => 'int',
            'message_person'  => 'str',
            'message_content' => 'str',
            'message_time'    => 'str',
            'message_status'  => 'int',
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
            $conn_prepare = $this->Prepare('count', $table, $where);
            $conn_prepare = $this->Combine('count', $conn_prepare, $where, $table);
            $result       = $this->Output('one', $conn_prepare);

            if ($result) {
                return $result['COUNT(1)'];
            }
        }

        return null;
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
            $conn_prepare = $this->Prepare('select', $table, $where);
            $conn_prepare = $this->Combine('select', $conn_prepare, $where, $table);
            $result       = $this->Output('one', $conn_prepare);

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
            $conn_prepare = $this->Prepare('select', $table, $where, $limit);
            $conn_prepare = $this->Combine('select', $conn_prepare, $where, $table);
            $result       = $this->Output('all', $conn_prepare);

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
            $conn_prepare = $this->Prepare('insert', $table);
            $this->Combine('insert', $conn_prepare, $data, $table);
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
            $conn_prepare = $this->Prepare('update', $table, $update, $key);
            $this->Combine('update', $conn_prepare, $update, $table, $key);
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
    protected function Prepare($method = '', $table = '', $data = [], $limit = [])
    {
        $string  = '';
        $Connect = $this->Connection();

        if ($table != '') {
            if ($method == 'count') {
                $string       .= 'SELECT COUNT(1) ';
                $string       .= 'FROM ' . $this->tableNameMap[$table] . ' ';
                $string       .= $this->PrepareString($method, $data);
                $conn_prepare = $Connect->prepare($string);
            }

            if ($method == 'select') {
                $string       .= 'SELECT * ';
                $string       .= 'FROM ' . $this->tableNameMap[$table] . ' ';
                $string       .= $this->PrepareString($method, $data);
                $string       .= $this->PrepareString($method, $limit, null, 'limit');
                $conn_prepare = $Connect->prepare($string);
            }

            if ($method == 'insert') {
                $string       .= 'INSERT INTO ' . $this->tableNameMap[$table];
                $string       .= $this->PrepareString($method, null, $table, 'field');
                $string       .= $this->PrepareString($method, null, $table);
                $conn_prepare = $Connect->prepare($string);
            }

            if ($method == 'update') {
                $string       .= 'UPDATE ' . $this->tableNameMap[$table];
                $string       .= $this->PrepareString($method, $data, $table, 'value');
                $string       .= $this->PrepareString($method, $limit, $table, 'field');
                $conn_prepare = $Connect->prepare($string);
            }
        }

        if (isset($conn_prepare)) {
            return $conn_prepare;
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
    protected function PrepareString($method = '', $data = [], $table = '', $mode = '')
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
    protected function Combine($method, $prepare, $data = [], $table = '', $key = [])
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
    protected function Output($type, $prepare)
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
     * @return object | die
     */
    protected function Connection()
    {
        $dbhost = 'localhost';
        $dbuser = 'message';
        $dbpass = '4W;<EH.FHB;rt2ugW%Pb';
        $dbname = 'shin_message';
        $dsn    = 'mysql:host=' . $dbhost . ';dbname=' . $dbname;
        try {
            $conn = new PDO($dsn, $dbuser, $dbpass);
            $conn->exec('SET CHARACTER SET utf8');
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
            die;
        }
    }
}


