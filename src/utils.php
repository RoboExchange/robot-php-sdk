<?php
function logger($log)
{
    $log_path = '/var/log/robot/application.log';
    if (!file_exists($log_path)) {
        file_put_contents($log_path, '');
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    $time = date('y/m/d h:iA', time());
    $contents = file_get_contents($log_path);
    $contents .= "$time [$ip] $log\r\n";

    file_put_contents($log_path, $contents);
}