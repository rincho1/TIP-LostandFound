<?php
$host = "sql100.infinityfree.com";
$user = "if0_40268124";
$pass = "RCmroQNvFd";
$db = "if0_40268124_lost_found";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");
?>
