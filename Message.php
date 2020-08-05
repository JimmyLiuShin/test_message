<?php
require_once dirname(__FILE__) . '/Sql.php';
require_once dirname(__FILE__) . '/Method.php';

new Message();

/**
 * 留言板訊息
 */
class Message
{
    /**
     * 網頁位置位址
     *
     * @var array
     */
    protected $urlNameMap = [
        'index' => './',
    ];

    /**
     * SQL連接
     *
     * @var object
     */
    protected $sqlMap;

    /**
     * 提示
     *
     * @var string
     */
    protected $alertMap = '?alert=';

    public function __construct()
    {
        $this->sqlMap = new Sql();
        $this->selectFunction();
    }

    /**
     * 判斷引入來源
     */
    public function selectFunction()
    {
        $selected = isset($_POST['method']) ? $_POST['method'] : null;
        $selected = isset($_GET['method']) ? $_GET['method'] : $selected;
        $selected = $selected ? $selected : 'index';

        if (method_exists($this, $selected)) {
            $this->{$selected}();
        }
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

        $count = isset($_GET['count']) ? (int)$_GET['count'] : 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = $this->setPage($where, $page, $count);
        $list = $this->sqlMap->getSelected($where, $limit);
        $list = ($list === null) ? [] : $list;

        return ['list' => $list, 'limit' => $limit];
    }

    /**
     * 新增留言
     */
    protected function add()
    {
        $insertData = [
            'message_person' => isset($_POST['person']) ? urlencode($_POST['person']) : null,
            'message_content' => isset($_POST['content']) ? urlencode($_POST['content']) : null,
            'message_time' => date('Y-m-d H:i:s'),
            'message_status' => 1,
        ];

        if ($this->addCheck($insertData)) {
            $insertId = $this->sqlMap->insertData($insertData);

            if ($insertId > 0) {
                $this->alertMap .= 'success';
            } else {
                $this->alertMap .= 'error';
            }
        } else {
            $this->alertMap .= 'error';
        }

        Method::setLocation($this->urlNameMap['index'] . $this->alertMap);
    }

    /**
     * 搜尋單筆留言
     *
     * @return array
     */
    public function show()
    {
        $id = $_GET['id'] ? (int)$_GET['id'] : 0;

        if ($id > 0 && gettype($id) == 'integer') {
            $where = [
                'id' => $id,
                'message_status' => 1,
            ];

            $item = $this->sqlMap->getOne($where);

            if ($item) {
                return $item;
            }
        }

        $this->alertMap .= 'error';
        Method::setLocation($this->urlNameMap['index'] . $this->alertMap);
    }

    /**
     * 刪除留言
     */
    protected function delete()
    {
        $id = $_POST['id'] ? (int)$_POST['id'] : 0;

        if ($id > 0) {
            $rowCount = $this->sqlMap->updateData(['id' => $id], ['message_status' => 2]);

            if ($rowCount > 0) {
                $this->alertMap .= 'success';
            } else {
                $this->alertMap .= 'error';
            }
        } else {
            $this->alertMap .= 'error';
        }

        Method::setLocation($this->urlNameMap['index'] . $this->alertMap);
    }

    /**
     * 修改留言
     */
    protected function edit()
    {
        $id = $_POST['id'] ? (int)$_POST['id'] : 0;
        $updateData = [
            'message_person' => isset($_POST['person']) ? urlencode($_POST['person']) : null,
            'message_content' => isset($_POST['content']) ? urlencode($_POST['content']) : null,
        ];

        if ($id > 0 && $this->addCheck($updateData)) {
            $rowCount = $this->sqlMap->updateData(['id' => $id], $updateData);

            if ($rowCount > 0) {
                $this->alertMap .= 'success';
            } else {
                $this->alertMap .= 'error';
            }
        } else {
            $this->alertMap .= 'error';
        }

        Method::setLocation($this->urlNameMap['index'] . $this->alertMap);
    }

    /**
     * 檢查 新增內容
     *
     * @params array $data 資料
     * @return boolean
     */
    protected function addCheck($data)
    {
        if ($data['message_person'] == null) {
            return false;
        }

        if ($data['message_content'] == null) {
            return false;
        }

        return true;
    }

    /**
     * 組成頁碼資訊
     *
     * @params array $condition 條件
     * @params string $page 默認頁碼
     * @params string $count 每頁默認個數
     * @return array
     */
    protected function setPage($condition = [], $page = 1, $count = 10)
    {
        $total = $this->sqlMap->getCount($condition);
        $limit = [
            'total' => $total,
            'count' => $count,
            'page_now' => $page,
            'page_max' => ceil($total / $count),
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
