<?php

function UserAgentRegCheck($regText)
{
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    return preg_match('@('.$regText.')@', $useragent);
}

function isIphone() {
    return UserAgentRegCheck('iPad|iPod|iPhone');
}

function isAndroid() {
    return UserAgentRegCheck('Android');
}

function isMobile(){
    return UserAgentRegCheck('iPad|iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS');
}

if(isIphone() || isAndroid() || isMobile()) {
    header('Location: /m/');
} else {
    header('Location: /index.php');
}

?>