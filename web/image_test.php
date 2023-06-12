<?php
$result = shell_exec("python3 /home/docker/code/retrieval.py random_nft 2>&1");
$array = explode (";", $result);
$nft_name = $array[0];
$nft_contract_address = $array[1];
$nft_image_url = $array[2];
$nft_key = $array[3];
$moderation = $array[4];
$votes_nsfw = $array[5];
$votes_sfw = preg_replace('/\s+/', '', $array[6]);
function str_starts_with($haystack, $needle) {
    return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <link rel="stylesheet" href="css/qfc-dark.css">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Image Classifier | <?php echo $nft_name;?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <script src="js/jquery-3.7.0.min.js"></script>
</head>
<noscript><h1><a href='https://www.enable-javascript.com/'>JAVASCRIPT MUST BE ENABLED FOR THIS SITE (enable-javascript.com)</a></h1></noscript>
<body style="max-height: 1080px;">

<div class="qfc-container-wide" style="padding: 10px; margin-top: 0px;">
    <a href="index.php"><img src="img/home.png" alt="Home" style="width: 50px; height: 50px; position: relative; top: 0; left: 0; padding: 10px;margin-bottom:0px;"></a>
    <a href="login.php" style="margin-left:10px;"><img src="img/admin.png" title="Admin Panel" style="width: 50px; height: 50px; position: relative; top: 0; right: 0; padding: 10px;margin-bottom:0px;"></a>
    <h1 style="margin-top: 0px;"><center><h3 style="margin-top: 0px;">Image Classifier</h3></center></h1>
    <div name="image_details">
        <h2><center><?php echo $nft_name;?></center></h2>
        <?php
        if (str_starts_with($nft_image_url, "https://cloudflare-ipfs.com/"))
        {
            echo "<p><center id='ipfsloading'>Loading image hosted on IPFS...</center></p>";
        }
        ?>
        <center><img src="<?php echo $nft_image_url;?>" width="500" height="500"></center>
        <h4><center>Contract Address: <?php echo $nft_contract_address;?></center><center>Key: <?php echo $nft_key;?></center></h4>
    </div>
    <div name="interactive" id='main'>
        <p class="label-p" style="text-align: center;">Is this image safe for work?</p>
        <button class='green-button' id='Allow' onclick='allow()' style='width: 100%'>Allow</button>
        <button class='red-button' id='Block' onclick='block()' style='width: 100%'>Block</button>
    </div>
    <div id="next" style="display: none;">
        <button class='blue-button' id='Next' onclick='window.location.reload()' style='width: 100%'>Next</button>
    </div>
</div>
</body>
<script>
        $(window).on('load', function() {
            $("#ipfsloading").html("");
        });
</script>
<script>
    function allow() {
        $.ajax({
            url: "count_vote.php",
            type: "GET",
            data: {
                contract_address: "<?php echo $nft_contract_address;?>",
                nft_key: "<?php echo $nft_key;?>",
                vote: "sfw"
            },
            success: function (data) {
            $("#main").html("<p class='label-p' style='text-align: center;'>You voted that this image is safe for work. The moderation result was <?php echo $moderation; ?>.</p><br><p class='label-p' style='text-align: center;'><?php echo ($votes_sfw + 1);?> votes for SFW, and <?php echo $votes_nsfw;?> votes for NSFW.</p>");
            $("#next").css("display", "block")
            }
        });
    }
    function block() {
        $.ajax({
            url: "count_vote.php",
            type: "GET",
            data: {
                contract_address: "<?php echo $nft_contract_address;?>",
                nft_key: "<?php echo $nft_key;?>",
                vote: "nsfw"
            },
            success: function (data) {
            $("#main").html("<p class='label-p' style='text-align: center;'>You voted that this image is not safe for work. The moderation result was <?php echo $moderation; ?>.</p><br><p class='label-p' style='text-align: center;'><?php echo ($votes_nsfw + 1);?> votes for NSFW, and <?php echo $votes_sfw;?> votes for SFW.</p>");
            $("#next").css("display", "block")
            }
        });
    }
</script>
</html>