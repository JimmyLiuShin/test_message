<?php

namespace Way;

class Method
{
    public function setLocation($url = '')
    {
        if ($url) {
            header('Location:' . $url);
        }
    }
}