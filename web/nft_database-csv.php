<?php
if (!isset($_SESSION['authenticated']))
{
    header("Location: login.php");
    exit();
}
$result = shell_exec("cd /home/docker/code && python3 /home/docker/code/admin.py admin_download 2>&1");
echo "<pre>";
readfile("/home/docker/code/nfts.csv");
echo "</pre>"
?>