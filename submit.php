<?php
require_once dirname(__FILE__) . '/autoload.php';

use Controller\Message;

$message = new Message();
$method = isset($_POST['method']) ? $_POST['method'] : null;

if (method_exists($message, $method)) {
    $message->{$method}();
    exit;
}

header('Location:./?alert=error_noFunction');
