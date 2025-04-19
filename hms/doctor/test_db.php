<?php
include('include/config.php');  // Include your config file

if ($con) {
    echo "Database Connected Successfully!";
} else {
    echo "Database Connection Failed: " . mysqli_connect_error();
}
?>
