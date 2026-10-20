<?php
// Requirements for connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "school_db";

// Method for connection
$conn = mysqli_connect("localhost","root","","school_db");

// Test connection
if ($conn) {
    echo("it connected");
}