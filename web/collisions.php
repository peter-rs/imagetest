<?php
session_start(['name' => 'sessid',
'cookie_lifetime' => 25200,
'cookie_httponly' => true,
'use_strict_mode' => true,
'sid_length' => 64,
'cookie_samesite' => 'Strict']);
if (!isset($_SESSION['authenticated']))
{
    header("Location: login.php");
    exit();
}
$result = shell_exec("cd /home/docker/code && python3 /home/docker/code/admin.py admin_collisions 2>&1");
echo "<pre>";
readfile("/home/docker/code/collisions.json");
echo "</pre>"
?>