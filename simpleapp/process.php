<?php
include "connection.php";
if (isset($_POST["submit"])) {
$fname = $_POST["first_name"];
$lname = $_POST["last_name"];
$age = $_POST["age"];

$formdata = "INSERT INTO users (first_name,last_name,age) VALUES ('$fname','$lname',$age)";
if(mysqli_query($conn, $formdata)){
    echo "connect successfull";
    }
    header("location:form.php");

$datas="SELECT*FROM users";
$my_data = mysqli_query($conn, $datas);
}
 if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $editdt = "SELECT * FROM student WHERE id = $id";
    $edit_data= mysqli_query($conn, $editdt);
    
 }