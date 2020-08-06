<?php
require_once dirname(__FILE__) . '/autoload.php';

use Controller\Message;

$Message = new Message();
$method = isset($_POST['method']) ? $_POST['method'] : null;

if (method_exists($Message, $method)) {
    $Message->{$method}();
    exit;
}

header('Location:./?alert=error');
