<?php
$password = "123456";

echo "MD5: " . md5($password) . "<br>";
echo "SHA256: " . hash('sha256', $password) . "<br>";
echo "SHA1: " . sha1($password) . "<br>";
?>
