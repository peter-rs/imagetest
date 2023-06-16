<?php 
function str_ends_with(string $haystack, string $needle): bool
{
    $needle_len = strlen($needle);
    return ($needle_len === 0 || 0 === substr_compare($haystack, $needle, - $needle_len));
}
session_start(['name' => 'sessid',
'cookie_lifetime' => 25200,
'cookie_httponly' => true,
'use_strict_mode' => true,
'sid_length' => 64,
'cookie_samesite' => 'Strict']);
if (isset($_SESSION['authenticated']))
{

    header('Location: admin.php');
    exit();
}

if (isset($_POST['password']))
{
    $result = shell_exec(escapeshellcmd("python3 /home/docker/code/admin.py admin_password '" . escapeshellcmd($_POST['password']) . "' 2>&1"));
    $auth = preg_replace('/\s+/', '', $result);
    if ($auth == "auth")
    {
        $_SESSION['authenticated'] = true;
        echo "auth";
        exit();
    }
    else
    {
        $_SESSION['failed_auth'] = true;
        echo "noauth";
        exit();
    }
}
if ($_SESSION['failed_auth'] == true)
{
    echo '<script src="js/jquery-3.7.0.min.js"></script><script>var pass = prompt("Enter admin password", "Incorrect Password, Try Again");
    if (pass != null) {
        $.ajax(
                {
            url: "login.php",
            type: "POST",
            data: {
                password: pass
            }, success: function (data) {
                location.href = "admin.php";
            }
        })
    }</script>';
}
else {
    echo '<script src="js/jquery-3.7.0.min.js"></script><script>var pass = prompt("Enter admin password");
    if (pass != null) {
        $.ajax(
            {
            url: "login.php",
            type: "POST",
            data: {
                password: pass
            }, success: function (data) {
                location.href = "admin.php";
            }
        })
    }</script>';
}
?>