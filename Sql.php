<?php

namespace Model;

use \PDO;

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
     * @params array $where SQL條件
     * @return integer
     */
    public function getCount($where = [])
    {
        if (!empty($where)) {
            $connect = $this->connection();
            $connPrepare = $this->prep($connect, 'count', $where);
            $connPrepare = $this->combine('count', $connPrepare, $where);
            $result = $this->output('one', $connPrepare);
            $connect = null;

            if ($result) {
                return $result['COUNT(1)'];
            }
        }

        return 0;
    }

    /**
     * 取得一筆資料
     *
     * @params array $where SQL條件
     * @return array
     */
    public function getOne($where = [])
    {
        if (!empty($where)) {
            $connect = $this->connection();
            $connPrepare = $this->prep($connect, 'select', $where);
            $connPrepare = $this->combine('select', $connPrepare, $where);
            $result = $this->output('one', $connPrepare);
            $connect = null;

            if ($result) {
                return $result;
            }
        }

        return [];
    }

    /**
     * 取得多筆資料
     *
     * @params array $where SQL條件
     * @params array $limit 輸出限制
     * @return array
     */
    public function getSelected($where = [], $limit = [])
    {
        $connect = $this->connection();
        $connPrepare = $this->prep($connect, 'select', $where, $limit);
        $connPrepare = $this->combine('select', $connPrepare, $where);
        $result = $this->output('all', $connPrepare);
        $connect = null;

        if ($result) {
            return $result;
        }

        return [];
    }

    /**
     * 新增資料
     *
     * @params array $data 新增內容
     * @return integer
     */
    public function insertData($data = [])
    {
        $insertId = 0;

        if (!empty($data)) {
            $connect = $this->connection();
            $connPrepare = $this->prep($connect, 'insert');
            $this->combine('insert', $connPrepare, $data);
            $insertId = $connect->lastInsertId();
            $connect = null;
        }

        return $insertId;
    }

    /**
     * 更新資料
     *
     * @params array $key 條件
     * @params array $update 更新內容
     * @return integer
     */
    public function updateData($key = [], $update = [])
    {
        $insertRow = 0;

        if (!empty($update)) {
            $connect = $this->connection();
            $connPrepare = $this->prep($connect, 'update', $update, $key);
            $connPrepare = $this->combine('update', $connPrepare, $update, $key);
            $insertRow = $connPrepare->rowCount();
            $connect = null;
        }

        return $insertRow;
    }

    /**
     * SQL預處理
     *
     * @params object $connect SQL連線
     * @params string $method 處理方法
     * @params array $data 資料內容 | 條件
     * @params array $limit 條件
     * @return object | null
     */
    protected function prep($connect, $method = '', $data = [], $limit = [])
    {
        $string = '';

        if ($method == 'count') {
            $string .= 'SELECT COUNT(1) ';
            $string .= 'FROM ' . $this->tableNameMap['message'] . ' ';
            $string .= $this->prepString($method, $data);

            $connPrepare = $connect->prepare($string);
        }

        if ($method == 'select') {
            $string .= 'SELECT * ';
            $string .= 'FROM ' . $this->tableNameMap['message'] . ' ';
            $string .= $this->prepString($method, $data);
            $string .= $this->prepString($method, $limit, 'limit');

            $connPrepare = $connect->prepare($string);
        }

        if ($method == 'insert') {
            $string .= 'INSERT INTO ' . $this->tableNameMap['message'] . '';
            $string .= $this->prepString($method, null, 'field') . ' ';
            $string .= $this->prepString($method, null);

            $connPrepare = $connect->prepare($string);
        }

        if ($method == 'update') {
            $string .= 'UPDATE ' . $this->tableNameMap['message'];
            $string .= $this->prepString($method, $data, 'value');
            $string .= $this->prepString($method, $limit, 'field');

            $connPrepare = $connect->prepare($string);
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
     * @params array $mode 功能
     * @return string
     */
    protected function prepString($method = '', $data = [], $mode = '')
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

            if (isset($this->tableFieldMap['message'])) {
                foreach ($this->tableFieldMap['message'] as $k => $v) {
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
     * @params array $key 條件
     * @return object | null
     */
    protected function combine($method, $prepare, $data = [], $key = [])
    {
        if ($prepare) {
            if ($method == 'count' || $method == 'select') {
                $sort = 0;
                foreach ($data as $k => $v) {
                    if (isset($this->tableFieldMap['message'][$k]) && $this->tableFieldMap['message'][$k] == 'int') {
                        $prepare->bindValue(':' . $sort, $v, PDO::PARAM_INT);
                    } else {
                        $prepare->bindValue(':' . $sort, $v, PDO::PARAM_STR);
                    }

                    $sort++;
                }
            }

            if ($method == 'insert') {
                foreach ($data as $k => $v) {
                    if (isset($this->tableFieldMap['message'][$k]) && $this->tableFieldMap['message'][$k] == 'int') {
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
                        if (isset($this->tableFieldMap['message'][$k]) && $this->tableFieldMap['message'][$k] == 'int') {
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
                        if (isset($this->tableFieldMap['message'][$k]) && $this->tableFieldMap['message'][$k] == 'int') {
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
