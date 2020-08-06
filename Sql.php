<?php

namespace Model;

use \PDO;

/**
 * 資料庫處理
 */
class Sql
{
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
     * SQL連接
     *
     * @var object
     */
    protected $connectMap = null;

    public function __construct()
    {
        $this->connectMap = $this->connection();
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * 取得符合條件之資料個數
     *
     * @params array $where SQL條件
     * @return integer
     */
    public function getCount($where = [])
    {
        if (!empty($where)) {
            $string = 'SELECT COUNT(1) ';
            $string .= 'FROM message_item ';
            $string .= $this->prepString('count', $where);
            $connPrepare = $this->connectMap->prepare($string);
            $sort = 0;

            foreach ($where as $k => $v) {
                if (isset($this->tableFieldMap['message'][$k]) && $this->tableFieldMap['message'][$k] === 'int') {
                    $connPrepare->bindValue(':' . $sort, $v, PDO::PARAM_INT);
                } else {
                    $connPrepare->bindValue(':' . $sort, $v, PDO::PARAM_STR);
                }

                $sort++;
            }

            $connPrepare->execute();
            $result = $connPrepare->fetch(PDO::FETCH_ASSOC);
            return isset($result['COUNT(1)']) ? $result['COUNT(1)'] : 0;
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
            $string = 'SELECT * ';
            $string .= 'FROM message_item ';
            $string .= $this->prepString('select', $where);
            $connPrepare = $this->connectMap->prepare($string);
            $sort = 0;

            foreach ($where as $k => $v) {
                if (isset($this->tableFieldMap['message'][$k]) && $this->tableFieldMap['message'][$k] == 'int') {
                    $connPrepare->bindValue(':' . $sort, $v, PDO::PARAM_INT);
                } else {
                    $connPrepare->bindValue(':' . $sort, $v, PDO::PARAM_STR);
                }

                $sort++;
            }

            $connPrepare->execute();
            $result = $connPrepare->fetch(PDO::FETCH_ASSOC);

            return !empty($result) ? $result : [];
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
        $string = 'SELECT * ';
        $string .= 'FROM message_item ';
        $string .= !empty($limit) ? $this->prepString('select', $where) : '';
        $string .= !empty($limit) ? $this->prepString('select', $limit, 'limit') : '';
        $connPrepare = $this->connectMap->prepare($string);
        $sort = 0;

        foreach ($where as $k => $v) {
            if (isset($this->tableFieldMap['message'][$k]) && $this->tableFieldMap['message'][$k] == 'int') {
                $connPrepare->bindValue(':' . $sort, $v, PDO::PARAM_INT);
            } else {
                $connPrepare->bindValue(':' . $sort, $v, PDO::PARAM_STR);
            }

            $sort++;
        }

        $connPrepare->execute();
        $result = $connPrepare->fetchAll(PDO::FETCH_ASSOC);

        return !empty($result) ? $result : [];
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
            $string = 'INSERT INTO message_item';
            $string .= $this->prepString('insert', null, 'field');
            $string .= $this->prepString('insert', null);
            $connPrepare = $this->connectMap->prepare($string);

            foreach ($data as $k => $v) {
                if (isset($this->tableFieldMap['message'][$k]) && $this->tableFieldMap['message'][$k] == 'int') {
                    $connPrepare->bindValue(':' . $k, $v, PDO::PARAM_INT);
                } else {
                    $connPrepare->bindValue(':' . $k, $v, PDO::PARAM_STR);
                }
            }

            $connPrepare->execute();
            $insertId = $this->connectMap->lastInsertId();
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
            $string = 'UPDATE message_item';
            $string .= $this->prepString('update', $update, 'value');
            $string .= !empty($key) ? $this->prepString('update', $key, 'field') : '';
            $connPrepare = $this->connectMap->prepare($string);
            $sort = 0;

            foreach ($update as $k => $v) {
                if (isset($this->tableFieldMap['message'][$k]) && $this->tableFieldMap['message'][$k] == 'int') {
                    $connPrepare->bindValue(':value' . $sort, $v, PDO::PARAM_INT);
                } else {
                    $connPrepare->bindValue(':value' . $sort, $v, PDO::PARAM_STR);
                }

                $sort++;
            }

            if (!empty($key)) {
                $sort = 0;

                foreach ($key as $k => $v) {
                    if (isset($this->tableFieldMap['message'][$k]) && $this->tableFieldMap['message'][$k] == 'int') {
                        $connPrepare->bindValue(':field' . $sort, $v, PDO::PARAM_INT);
                    } else {
                        $connPrepare->bindValue(':field' . $sort, $v, PDO::PARAM_STR);
                    }

                    $sort++;
                }
            }

            $connPrepare->execute();
            $insertRow = $connPrepare->rowCount();
        }

        return $insertRow;
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
                    if ($k != 'id' && $k != 'message_status') {
                        if ($mode == 'field') {
                            $condition[] = $k;
                        } else {
                            $condition[] = ':' . $k;
                        }
                    }
                }
            }

            if ($mode == 'field') {
                $result = !empty($condition) ? '(' . implode(', ', $condition) . ') ' : ' ';
            } else {
                $result = !empty($condition) ? 'VALUES(' . implode(', ', $condition) . ')' : ' ';
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

    /**
     * 取消資料庫連結
     *
     * @params object $connect SQL
     */
    protected function disconnect()
    {
        !empty($this->connectMap) && $this->connectMap = null;
    }
}
