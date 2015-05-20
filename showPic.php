<?php
header("Content-Type: image/jpeg");
readfile($_GET['picurl']);
?>