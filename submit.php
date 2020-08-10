<?php
require_once dirname(__FILE__) . '/autoload.php';

use Controller\Message;

$message = new Message();
$method = isset($_POST['method']) ? $_POST['method'] : null;

if ($method === 'add' && method_exists($message, 'add')) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $person = isset($_POST['person']) ? $_POST['person'] : null;
    $content = isset($_POST['content']) ? $_POST['content'] : null;
    $alertType = $message->add($person, $content);
}

if ($method === 'edit' && method_exists($message, 'edit')) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $person = isset($_POST['person']) ? $_POST['person'] : null;
    $content = isset($_POST['content']) ? $_POST['content'] : null;
    $alertType = $message->edit($id, $person, $content);
}

if ($method === 'delete' && method_exists($message, 'delete')) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $alertType = $message->delete($id);
}

if ($alertType) {
    header('Location:./?alert=' . $alertType);
} else {
    header('Location:./?alert=error_noFunction');
}
