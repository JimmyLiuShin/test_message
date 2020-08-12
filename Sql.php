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

    /**
     * 資料庫連結
     *
     * @return object
     */
    public function connection()
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

    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * 取消資料庫連結
     *
     * @params object $connect SQL
     */
    public function disconnect()
    {
        !empty($this->connectMap) && $this->connectMap = null;
    }

    /**
     * 取得符合條件之資料個數
     *
     * @params array $where SQL條件
     * @return integer
     */
    public function getCount($where = [])
    {
        $string = 'SELECT COUNT(1) ';
        $string .= 'FROM message_item ';

        if (!empty($where)) {
            $string .= 'WHERE ';
            $sort = 0;

            foreach ($where as $key => $value) {
                $string .= ($sort > 0 ? 'AND ' : '') . $key . ' = :' . $key . ' ';
                $sort++;
            }

            $string .= '';
        }

        $connPrepare = $this->connectMap->prepare($string);

        if (!empty($where)) {
            foreach ($where as $key => $value) {
                if (isset($this->tableFieldMap['message'][$key]) && $this->tableFieldMap['message'][$key] === 'int') {
                    $connPrepare->bindValue(':' . $key, $value, PDO::PARAM_INT);
                } else {
                    $connPrepare->bindValue(':' . $key, $value, PDO::PARAM_STR);
                }
            }
        }

        $connPrepare->execute();
        $result = $connPrepare->fetch(PDO::FETCH_ASSOC);

        return isset($result['COUNT(1)']) ? $result['COUNT(1)'] : 0;
    }

    /**
     * 取得一筆資料
     *
     * @params array $where SQL條件
     * @return array
     */
    public function getOne($where = [])
    {
        $string = 'SELECT * ';
        $string .= 'FROM message_item ';

        if (!empty($where)) {
            $string .= 'WHERE ';
            $sort = 0;

            foreach ($where as $key => $value) {
                $string .= ($sort > 0 ? 'AND ' : '') . $key . ' = :' . $key . ' ';
                $sort++;
            }

            $string .= '';
        }

        $connPrepare = $this->connectMap->prepare($string);

        if (!empty($where)) {
            foreach ($where as $key => $value) {
                if (isset($this->tableFieldMap['message'][$key]) && $this->tableFieldMap['message'][$key] == 'int') {
                    $connPrepare->bindValue(':' . $key, $value, PDO::PARAM_INT);
                } else {
                    $connPrepare->bindValue(':' . $key, $value, PDO::PARAM_STR);
                }

                $sort++;
            }
        }

        $connPrepare->execute();
        $result = $connPrepare->fetch(PDO::FETCH_ASSOC);

        return !empty($result) ? $result : [];
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

        if (!empty($where)) {
            $string .= 'WHERE ';
            $sort = 0;

            foreach ($where as $key => $value) {
                $string .= ($sort > 0 ? 'AND ' : '') . $key . ' = :' . $key . ' ';
                $sort++;
            }

            $string .= '';
        }

        if (!empty($limit)) {
            $string .= 'LIMIT ' . $limit['start'] . ', ' . $limit['count'];
        }

        $connPrepare = $this->connectMap->prepare($string);

        if (!empty($where)) {
            foreach ($where as $key => $value) {
                if (isset($this->tableFieldMap['message'][$key]) && $this->tableFieldMap['message'][$key] == 'int') {
                    $connPrepare->bindValue(':' . $key, $value, PDO::PARAM_INT);
                } else {
                    $connPrepare->bindValue(':' . $key, $value, PDO::PARAM_STR);
                }
            }
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
        $string = 'INSERT INTO message_item';

        if (!empty($data)) {
            $string .= '(';
            $sort = 0;

            foreach ($this->tableFieldMap['message'] as $key => $value) {
                if ($key != 'id' && isset($data[$key])) {
                    $string .= ($sort > 0 ? ', ' : '') . $key;
                    $sort++;
                }
            }

            $string .= ') VALUES(';
            $sort = 0;

            foreach ($this->tableFieldMap['message'] as $key => $value) {
                if ($key != 'id' && isset($data[$key])) {
                    $string .= ($sort > 0 ? ', ' : '') . ':' . $key;
                    $sort++;
                }
            }

            $string .= ')';
        }

        $connPrepare = $this->connectMap->prepare($string);

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if (isset($this->tableFieldMap['message'][$key]) && $this->tableFieldMap['message'][$key] == 'int') {
                    $connPrepare->bindValue(':' . $key, $value, PDO::PARAM_INT);
                } else {
                    $connPrepare->bindValue(':' . $key, $value, PDO::PARAM_STR);
                }
            }
        }

        $connPrepare->execute();
        $insertId = $this->connectMap->lastInsertId();

        return $insertId;
    }

    /**
     * 更新資料
     *
     * @params array $key 條件
     * @params array $data 更新內容
     * @return integer
     */
    public function updateData($id, $data = [])
    {
        $string = 'UPDATE message_item ';

        if (!empty($data)) {
            $string .= 'SET ';
            $sort = 0;

            foreach ($data as $key => $value) {
                $string .= ($sort > 0 ? ', ' : '') . $key . ' = :' . $key . ' ';
                $sort++;
            }

            $string .= 'WHERE id = :id';
        }

        $connPrepare = $this->connectMap->prepare($string);

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if (isset($this->tableFieldMap['message'][$key]) && $this->tableFieldMap['message'][$key] == 'int') {
                    $connPrepare->bindValue(':' . $key, $value, PDO::PARAM_INT);
                } else {
                    $connPrepare->bindValue(':' . $key, $value, PDO::PARAM_STR);
                }
            }

            $connPrepare->bindValue(':id', $id, PDO::PARAM_INT);
        }

        $connPrepare->execute();
        $insertRow = $connPrepare->rowCount();

        return $insertRow;
    }
}
