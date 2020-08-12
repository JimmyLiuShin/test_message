<?php
require_once dirname(__FILE__) . '/autoload.php';

use PHPUnit\Framework\TestCase;
use Model\Sql;

/**
 * 留言板訊息
 */
class SqlTest extends TestCase
{
    /**
     * 測試 connection 成功
     *
     * @covers       Model\Sql::connection
     */
    public function testConnection()
    {
        $sql = new Sql();
        $this->assertTrue($sql->connection());
    }

    public function getCountDataProviderWhere()
    {
        return [
            'where 1' => [['message_status' => 1]],
            'where 2' => [['message_status' => 2]],
            'where 3' => [['message_content' => 1]],
            'where 4' => [['id' => 1]],
            'where 5' => [[]],
        ];
    }

    /**
     * 測試 getCount 成功
     *
     * @dataProvider getCountDataProviderWhere
     * @covers       Model\Sql::getCount
     */
    public function testGetCount($where)
    {
        $sql = new Sql();
        $count = $sql->getCount($where);
        $this->assertTrue(is_numeric($count));
    }

    /**
     * 測試 getOne 成功
     *
     * @dataProvider getCountDataProviderWhere
     * @covers       Model\Sql::getOne
     */
    public function testGetOne($where)
    {
        $sql = new Sql();
        $result = $sql->getOne($where);
        $this->assertTrue(is_array($result));
    }

    public function getSelectedDataProviderWhere()
    {
        $limit = [
            'start' => 1,
            'count' => 50,
        ];
        return [
            'where 1' => [['message_status' => 1], $limit],
            'where 2' => [['message_status' => 2], $limit],
            'where 3' => [['message_content' => 1], $limit],
            'where 4' => [['id' => 1], $limit],
            'where 5' => [[], $limit],
            'where 6' => [[], []],
        ];
    }

    /**
     * 測試 getSelected 成功
     *
     * @dataProvider getSelectedDataProviderWhere
     * @covers       Model\Sql::getSelected
     */
    public function testGetSelected($where, $limit)
    {
        $sql = new Sql();
        $result = $sql->getSelected($where, $limit);
        $this->assertTrue(is_array($result));
    }

    public function insertDataDataProviderFormat()
    {
        return [
            'format 1' => [
                [
                    'message_person' => 1,
                    'message_content' => 1,
                    'message_time' => date('Y-m-d H:i:s'),
                    'message_status' => 1,
                ]
            ],
            'format 2' => [
                [
                    'message_person' => 'aaa',
                    'message_content' => 'aaa',
                    'message_time' => date('Y-m-d H:i:s'),
                    'message_status' => 1,
                ]
            ],
        ];
    }

    /**
     * 測試 insertData 成功
     *
     * @dataProvider insertDataDataProviderFormat
     * @covers       Model\Sql::insertData
     */
    public function testInsertData($data)
    {
        $sql = new Sql();
        $insert = $sql->insertData($data);
        $this->assertTrue(is_numeric($insert));
    }

    public function updateDataDataProviderFormat()
    {
        return [
            'format 1' => [
                1,
                [
                    'message_person' => 1,
                    'message_content' => 1,
                    'message_time' => date('Y-m-d H:i:s'),
                    'message_status' => 1,
                ]
            ],
            'format 2' => [
                1,
                [
                    'message_person' => 'aaa',
                    'message_content' => 'aaa',
                    'message_time' => date('Y-m-d H:i:s'),
                    'message_status' => 1,
                ]
            ],
        ];
    }

    /**
     * 測試 updateData 成功
     *
     * @dataProvider updateDataDataProviderFormat
     * @covers       Model\Sql::updateData
     */
    public function testUpdateData($id, $data)
    {
        $sql = new Sql();
        $update = $sql->updateData($id, $data);
        $this->assertTrue(is_numeric($update));
    }
}
