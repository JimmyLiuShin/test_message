<?php
require_once dirname(__FILE__) . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Model\Sql;

/**
 * 資料庫處理測試
 */
class SqlTest extends TestCase
{
    /**
     * 測試 __construct 成功
     *
     * @covers       Model\Sql::__construct
     * @covers       Model\Sql::connection
     * @covers       Model\Sql::__destruct
     * @covers       Model\Sql::disconnect
     */
    public function testConstructor()
    {
        $sql = new Sql();
        $this->assertTrue(is_object($sql));
    }

    public function connectionDataProviderInteger()
    {
        return [
            'integer 1' => [1, 'message', '4W;<EH.FHB;rt2ugW%Pb', 'shin_message'],
            'integer 2' => ['localhost', 1, '4W;<EH.FHB;rt2ugW%Pb', 'shin_message'],
            'integer 3' => ['localhost', 'message', 1, 'shin_message'],
            'integer 4' => ['localhost', 'message', '4W;<EH.FHB;rt2ugW%Pb', 1],
        ];
    }

    public function connectionDataProviderString()
    {
        return [
            'string 1' => ['localhost1', 'message', '4W;<EH.FHB;rt2ugW%Pb', 'shin_message'],
            'string 2' => ['localhost', 'message1', '4W;<EH.FHB;rt2ugW%Pb', 'shin_message'],
            'string 3' => ['localhost', 'message', '4W;<EH.FHB;rt2ugW%Pb1', 'shin_message'],
            'string 4' => ['localhost', 'message', '4W;<EH.FHB;rt2ugW%Pb', 'shin_message1'],
        ];
    }

    public function connectionDataProvidernull()
    {
        return [
            'null 1' => [null, 'message', '4W;<EH.FHB;rt2ugW%Pb', 'shin_message'],
            'null 2' => ['localhost', null, '4W;<EH.FHB;rt2ugW%Pb', 'shin_message'],
            'null 3' => ['localhost', 'message', null, 'shin_message'],
            'null 4' => ['localhost', 'message', '4W;<EH.FHB;rt2ugW%Pb', null],
        ];
    }

    /**
     * 測試 connection
     *
     * @dataProvider connectionDataProviderInteger
     * @dataProvider connectionDataProviderString
     * @dataProvider connectionDataProvidernull
     * @covers       Model\Sql::connection
     */
    public function testConnectionError($databaseHost, $databaseUser, $databasePassword, $databaseName)
    {
        $this->assertTrue(is_null(Sql::connection($databaseHost, $databaseUser, $databasePassword, $databaseName)));
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
