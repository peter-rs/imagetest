<?php
// TODO: fix user passed variables into a shell command 
$contract_address = escapeshellarg($_GET['contract_address']);
$nft_key = escapeshellarg($_GET['nft_key']);
$vote =  strtolower($_GET['vote']);
if ($vote != "nsfw" and $vote != "sfw") {
    echo "Invalid vote.";
    exit();
}
shell_exec(escapeshellcmd("python3 /home/docker/code/retrieval.py vote ". $contract_address . " " . $nft_key . " " . $vote . " 2>&1"));
?>
