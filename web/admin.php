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
$result = shell_exec("python3 /home/docker/code/admin.py admin_stats 2>&1");
$array = explode (";", $result);
$total_nfts = $array[0];
$total_votes = $array[1];
$total_nsfw = $array[2];
$total_sfw = $array[3];
$total_collisions = $array[4];
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <link rel="stylesheet" href="css/qfc-dark.css">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Image Classifier</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
</head>
<noscript><h1><a href='https://www.enable-javascript.com/'>JAVASCRIPT MUST BE ENABLED FOR THIS SITE (enable-javascript.com)</a></h1></noscript>
<body>
<div class="qfc-container-wide">
    <a href="index.php"><img src="img/home.png" alt="Home" style="width: 50px; height: 50px; position: relative; top: 0; left: 0; padding: 10px;margin-bottom:0px;"></a>
    <a href="login.php" style="margin-left:10px;"><img src="img/admin.png" title="Admin Panel" style="width: 50px; height: 50px; position: relative; top: 0; right: 0; padding: 10px;margin-bottom:0px;"></a>
    <h1 style="margin-top: 0px;"><center><h3 style="margin-top: 0px;">Admin Panel</h3></center></h1>
    <h2><center>Stats</center></h2>
    <pre>Total NFTs in Database: <?php echo $total_nfts;?></pre>
    <pre>Total Votes in Database: <?php echo $total_votes;?></pre>
    <pre>Total NSFW NFTs in Database: <?php echo $total_nsfw;?></pre>
    <pre>Total SFW NFTs in Database: <?php echo $total_sfw;?></pre>
    <h2><center title="A Collision is when the voting result is not in agreement with the classifier result, meaning that either the classifier or the voters are wrong.">Collisions</center></h2>
    <pre>Total Collisions: <?php echo $total_collisions;?></pre>
    <a href="collisions.php"><button class='blue-button' id='Next' style='width: 100%'>View Collisions</button></a>
    <h2><center>Downloads</center></h2>
    <a href="nft_database-json.php"><button class='blue-button' id='Next' style='width: 100%'>Download NFT Database (JSON)</button></a>
    <a href="nft_database-csv.php"><button class='blue-button' id='Next' style='width: 100%'>Download NFT Database (CSV)</button></a>
</div>
</body>
</html>
