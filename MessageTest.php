<?php
require_once dirname(__FILE__) . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Controller\Message;

/**
 * 留言板測試
 */
class MessageTest extends TestCase
{
    /**
     * 測試 __construct 成功
     *
     * @covers       Controller\Message::__construct
     */
    public function testConstructor()
    {
        $message = new Message();
        $this->assertTrue(is_object($message));
    }

    public function indexDataProviderInteger()
    {
        return [
            'integer 1' => [10, 1],
            'integer 2' => [1, 1],
            'integer 3' => [0, 0],
        ];
    }

    public function indexDataProviderString()
    {
        return [
            'string 1' => ['a', 1],
            'string 2' => [1, 'a'],
            'string 3' => ['a', 'a'],
        ];
    }

    public function indexDataProviderNull()
    {
        return [
            'null 1' => [null, 1],
            'null 2' => [1, null],
            'null 3' => [null, null],
        ];
    }

    /**
     * 測試 index 成功
     *
     * @dataProvider indexDataProviderInteger
     * @dataProvider indexDataProviderString
     * @dataProvider indexDataProviderNull
     * @covers       Controller\Message::index
     */
    public function testIndex($count, $page)
    {
        $message = new Message();
        $limit = $message->index($page, $count);
        $this->assertTrue(is_array($limit));
    }

    public function addDataProviderMaximum()
    {
        $string50 = str_repeat('A', 50);
        $string51 = str_repeat('A', 51);
        $string255 = str_repeat('A', 255);
        $string256 = str_repeat('A', 256);
        return [
            'max 1' => [$string50, $string255],
            'max 2' => [$string50, $string256],
            'max 3' => [$string51, $string255],
            'max 4' => [$string51, $string256],
        ];
    }

    /**
     * 測試 add 成功
     *
     * @dataProvider indexDataProviderInteger
     * @dataProvider indexDataProviderString
     * @dataProvider indexDataProviderNull
     * @dataProvider addDataProviderMaximum
     * @covers       Controller\Message::add
     */
    public function testAdd($person, $content)
    {
        $resultArray = [
            'error_personNull',
            'error_contentNull',
            'error_personOverLimit',
            'error_contentOverLimit',
            'success_add',
            'error_addFail',
        ];
        $message = new Message();
        $insert = $message->add($person, $content);
        $this->assertContains($insert, $resultArray);
    }

    public function showDataProviderInteger()
    {
        return [
            'integer 1' => [0],
            'integer 2' => [35],
            'integer 3' => [223],
            'integer 4' => [11111111111111],
        ];
    }

    public function showDataProviderString()
    {
        return [
            'string 1' => ['a'],
            'string 2' => ['A'],
        ];
    }

    public function showDataProviderNull()
    {
        return [
            'null 1' => [null],
        ];
    }

    /**
     * 測試 show 成功
     *
     * @dataProvider showDataProviderInteger
     * @dataProvider showDataProviderString
     * @dataProvider showDataProviderNull
     * @covers       Controller\Message::show
     */
    public function testShow($id)
    {
        $message = new Message();
        $limit = $message->show($id);
        $this->assertTrue(is_array($limit));
    }

    /**
     * 測試 delete 成功
     *
     * @dataProvider showDataProviderInteger
     * @dataProvider showDataProviderString
     * @dataProvider showDataProviderNull
     * @covers       Controller\Message::delete
     */
    public function testDelete($id)
    {
        $resultArray = [
            'success_delete',
            'error_delFail',
        ];
        $message = new Message();
        $delete = $message->delete($id);
        $this->assertContains($delete, $resultArray);
    }

    public function editDataProviderInteger()
    {
        return [
            'integer 1' => [10, 1, 55],
            'integer 2' => [1, 1, 10],
            'integer 3' => [0, 0, 552],
        ];
    }

    public function editDataProviderString()
    {
        return [
            'string 1' => [1, 'a', 'a'],
            'string 2' => ['a', 1, 'a'],
            'string 3' => ['a', 'a', 1],
        ];
    }

    public function editDataProviderNull()
    {
        return [
            'null 1' => [1, null, null],
            'null 2' => [1, 1, null],
            'null 3' => [null, 1, null],
            'null 4' => [null, null, 1],
        ];
    }

    public function editDataProviderMaximum()
    {
        $string50 = str_repeat('A', 50);
        $string51 = str_repeat('A', 51);
        $string255 = str_repeat('A', 255);
        $string256 = str_repeat('A', 256);
        return [
            'max 1' => [1, $string50, $string255],
            'max 2' => [1, $string50, $string256],
            'max 3' => [1, $string51, $string255],
            'max 4' => [1, $string51, $string256],
        ];
    }

    /**
     * 測試 edit 成功
     *
     * @dataProvider editDataProviderInteger
     * @dataProvider editDataProviderString
     * @dataProvider editDataProviderNull
     * @dataProvider editDataProviderMaximum
     * @covers       Controller\Message::edit
     */
    public function testEdit($id, $person, $content)
    {
        $resultArray = [
            'error_personNull',
            'error_contentNull',
            'error_personOverLimit',
            'error_contentOverLimit',
            'success_edit',
            'error_editFail',
        ];
        $message = new Message();
        $edit = $message->edit($id, $person, $content);
        $this->assertContains($edit, $resultArray);
    }

    public function setPageDataProviderInteger()
    {
        return [
            'integer 1' => [1, 55],
            'integer 2' => [999, 999],
            'integer 3' => [0, 552],
            'integer 4' => [10, 55],
        ];
    }

    public function setPageDataProviderString()
    {
        return [
            'string 1' => [1, 'a'],
            'string 2' => ['a', 1],
        ];
    }

    public function setPageDataProviderNull()
    {
        return [
            'null 1' => [1, null],
            'null 2' => [null, 1],
        ];
    }

    /**
     * 測試 setPage 成功
     *
     * @dataProvider setPageDataProviderInteger
     * @dataProvider setPageDataProviderString
     * @dataProvider setPageDataProviderNull
     * @covers       Controller\Message::setPage
     */
    public function testSetPage($page, $count)
    {
        $condition = ['message_status' => 1];
        $message = new Message();
        $page = $message->setPage($condition, $page, $count);
        $this->assertTrue(is_array($page));
    }
}
