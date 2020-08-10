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
     * @params integer $count 單頁筆數
     * @params integer $page 頁數
     * @return array
     */
    public function index($count = 1, $page = 1)
    {
        $where = [
            'message_status' => 1,
        ];

        $limit = $this->setPage($where, $page, $count);
        $list = $this->sqlMap->getSelected($where, $limit);
        $list = is_null($list) ? [] : $list;

        return ['list' => $list, 'limit' => $limit];
    }

    /**
     * 新增留言
     *
     * @params string $person 留言人
     * @params string $content 留言內容
     * @return string
     */
    public function add($person, $content)
    {
        $insertData = [
            'message_person' => $person ? urlencode($person) : null,
            'message_content' => $content ? urlencode($content) : null,
            'message_time' => date('Y-m-d H:i:s'),
        ];

        if (!$insertData['message_person']) {
            return 'error_personNull';
        }

        if (!$insertData['message_content']) {
            return 'error_contentNull';
        }

        if (strlen($insertData['message_person']) > 50) {
            return 'error_personOverLimit';
        }

        if (strlen($insertData['message_content']) > 255) {
            return 'error_contentOverLimit';
        }

        $insertId = $this->sqlMap->insertData($insertData);

        return ($insertId > 0) ? 'success_add' : 'error_addFail';
    }

    /**
     * 搜尋單筆留言
     *
     * @params integer $id 留言ID
     * @return array
     */
    public function show($id = 0)
    {
        $default = [
            'id' => 0,
            'message_person' => '',
            'message_content' => '',
        ];

        if ($id > 0) {
            $where = [
                'id' => $id,
                'message_status' => 1,
            ];

            $item = $this->sqlMap->getOne($where);

            return !empty($item) ? $item : $default;
        }

        return $default;
    }

    /**
     * 刪除留言
     *
     * @params integer $id 留言ID
     * @return string
     */
    public function delete($id)
    {
        $select = $this->sqlMap->getOne(['id' => $id]);

        if (!empty($select)) {
            $rowCount = $this->sqlMap->updateData($id, ['message_status' => 2]);

            if ($rowCount > 0) {
                return 'success_delete';
            }
        }

        return 'error_delFail';
    }

    /**
     * 修改留言
     *
     * @params integer $id 留言ID
     * @params string $person 留言人
     * @params string $content 留言內容
     * @return string
     */
    public function edit($id = 0, $person = '', $content = '')
    {
        $updateData = [
            'message_person' => $person ? urlencode($person) : null,
            'message_content' => $content ? urlencode($content) : null,
        ];

        $select = $this->sqlMap->getOne(['id' => $id]);

        if (!empty($select)) {
            if (!$updateData['message_person']) {
                return 'error_personNull';
            }

            if (!$updateData['message_content']) {
                return 'error_contentNull';
            }

            if (strlen($updateData['message_person']) > 50) {
                return 'error_personOverLimit';
            }

            if (strlen($updateData['message_content']) > 255) {
                return 'error_contentOverLimit';
            }

            $rowCount = $this->sqlMap->updateData($id, $updateData);

            if ($rowCount > 0) {
                return 'success_edit';
            }
        }

        return 'error_editFail';
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
