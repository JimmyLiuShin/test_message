<?php
require_once dirname(__FILE__) . '/sql.php';

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
        'index' => './'
    ];

    /**
     * SQL連接
     *
     * @var object
     */
    protected $sqlMap;

    /**
     * 建構子
     */
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
        $selected = isset($_POST['method']) ? $_POST['method'] : (isset($_GET['method']) ? $_GET['method'] : null);
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
            'message_status' => 1
        ];
        $count = isset($_GET['count']) ? (int)$_GET['count'] : 10;
        $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = $this->setPage($where, $page, $count);
        $list  = $this->sqlMap->getAll('message', $where, $limit);
        $list  = ($list === null) ? [] : $list;

        return ['list' => $list, 'limit' => $limit];
    }

    /**
     * 新增 message
     */
    protected function add()
    {
        $insertData = [
            'message_person'  => isset($_POST['person']) ? urlencode($_POST['person']) : '',
            'message_content' => isset($_POST['content']) ? urlencode($_POST['content']) : null,
            'message_time'    => date('Y-m-d H:i:s'),
            'message_status'  => 1,
        ];
        $warning    = $this->addCheck($insertData);

        if ($warning == null) {
            $this->sqlMap->insertData('message', $insertData);
            setcookie('success', '新增成功');
            header('Location:' . $this->urlNameMap['index']);
        } else {
            setcookie('warning', $warning);
            header('Location:' . $this->urlNameMap['index']);
        }
    }

    /**
     * 搜尋單筆 message
     *
     * @return array
     */
    public function show()
    {
        $id = $_GET['id'] ? (int)$_GET['id'] : 0;

        if ($id > 0 && gettype($id) == 'integer') {
            $where = [
                'id'             => $id,
                'message_status' => 1
            ];
            $item  = $this->sqlMap->getOne('message', $where);

            if ($item) {
                return $item;
            }
        }
        setcookie('warning', '找不到該筆資料');
        header('Location:' . $this->urlNameMap['index']);
    }

    /**
     * 刪除 message
     */
    protected function delete()
    {
        $id = $_POST['id'] ? (int)$_POST['id'] : 0;

        if ($id > 0) {
            $this->sqlMap->updateData('message', ['id' => $id], ['message_status' => 2]);
            setcookie('success', '刪除成功');
        } else {
            setcookie('warning', '刪除失敗');
        }
        header('Location:' . $this->urlNameMap['index']);
    }

    /**
     * 修改 message
     */
    protected function edit()
    {
        $id         = $_POST['id'] ? (int)$_POST['id'] : 0;
        $updateData = [
            'message_person'  => isset($_POST['person']) ? urlencode($_POST['person']) : '',
            'message_content' => isset($_POST['content']) ? urlencode($_POST['content']) : null
        ];
        $warning    = $this->addCheck($updateData);

        if ($id > 0 && $warning == null) {
            $this->sqlMap->updateData('message', ['id' => $id], $updateData);
            setcookie('success', '修改成功');
            header('Location:' . $this->urlNameMap['index']);
        } else {
            setcookie('warning', $warning);
            header('Location:' . $this->urlNameMap['index']);
        }
    }

    /**
     * 檢查 新增內容
     *
     * @params array $data 資料
     * @return string | null
     */
    protected function addCheck($data)
    {
        if ($data['message_content'] == null) {
            return '請輸入內容';
        }

        return null;
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
        $total = $this->sqlMap->getCount('message', $condition);
        $limit = [
            'total'    => $total,
            'count'    => $count,
            'page_now' => $page,
            'page_max' => ceil($total / $count),
            'start'    => $count * ($page - 1),
            'final'    => ($count * $page) - 1
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