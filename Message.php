<?php

namespace Controller;

use Model\Sql;

/**
 * 留言板訊息
 */
class Message
{
    /**
     * SQL連接
     *
     * @var object
     */
    protected $sqlMap = null;

    public function __construct()
    {
        $this->sqlMap = new Sql();
    }

    /**
     * index.php 引用
     *
     * @return array
     */
    public function index()
    {
        $where = [
            'message_status' => 1,
        ];

        $count = (isset($_GET['count']) && $_GET['count'] > 0) ? (int)$_GET['count'] : 10;
        $page = (isset($_GET['page']) && $_GET['page'] > 0) ? (int)$_GET['page'] : 1;
        $limit = $this->setPage($where, $page, $count);
        $list = $this->sqlMap->getSelected($where, $limit);
        $list = ($list === null) ? [] : $list;

        return ['list' => $list, 'limit' => $limit];
    }

    /**
     * 新增留言
     */
    public function add()
    {
        $insertData = [
            'message_person' => isset($_POST['person']) ? urlencode($_POST['person']) : null,
            'message_content' => isset($_POST['content']) ? urlencode($_POST['content']) : null,
            'message_time' => date('Y-m-d H:i:s'),
        ];

        if ($this->addCheck($insertData)) {
            $insertId = $this->sqlMap->insertData($insertData);

            if ($insertId > 0) {
                header('Location:./?alert=success');
                exit;
            }
        }

        header('Location:./?alert=error');
    }

    /**
     * 搜尋單筆留言
     *
     * @return array
     */
    public function show()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id > 0 && gettype($id) == 'integer') {
            $where = [
                'id' => $id,
                'message_status' => 1,
            ];

            $item = $this->sqlMap->getOne($where);

            return !empty($item) ? $item : [];
        }

        return [];
    }

    /**
     * 刪除留言
     */
    public function delete()
    {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($id > 0 && gettype($id) == 'integer') {
            $rowCount = $this->sqlMap->updateData(['id' => $id], ['message_status' => 2]);

            if ($rowCount > 0) {
                header('Location:./?alert=success');
                exit;
            }
        }

        header('Location:./?alert=error');
    }

    /**
     * 修改留言
     */
    public function edit()
    {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $updateData = [
            'message_person' => isset($_POST['person']) ? urlencode($_POST['person']) : null,
            'message_content' => isset($_POST['content']) ? urlencode($_POST['content']) : null,
        ];

        if ($id > 0 && gettype($id) == 'integer' && $this->addCheck($updateData)) {
            $rowCount = $this->sqlMap->updateData(['id' => $id], $updateData);

            if ($rowCount > 0) {
                header('Location:./?alert=success');
                exit;
            }
        }

        header('Location:./?alert=error');
    }

    /**
     * 檢查新增內容
     *
     * @params array $data 資料
     * @return boolean
     */
    protected function addCheck($data)
    {
        return (($data['message_person'] == null) || ($data['message_content'] == null)) ? false : true;
    }

    /**
     * 組成頁碼資訊
     *
     * @params array $condition 條件
     * @params string $page 默認頁碼
     * @params string $count 每頁默認個數
     * @return array
     */
    protected function setPage($condition = [], $page = 1, $count = 5)
    {
        $total = $this->sqlMap->getCount($condition);
        $limit = [
            'total' => $total,
            'count' => $count,
            'page_now' => $page,
            'page_max' => ceil($total / $count) < 1 ? 1 : ceil($total / $count),
            'start' => $count * ($page - 1),
            'final' => ($count * $page) - 1,
        ];

        if ($limit['page_now'] <= 0) {
            $limit = $this->setPage($condition, 1, $count);
        }

        if ($limit['page_now'] > $limit['page_max']) {
            $limit = $this->setPage($condition, $limit['page_max'], $count);
        }

        return $limit;
    }
}
