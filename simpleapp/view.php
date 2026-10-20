<?php
include("connection.php");

$id = (int)$_GET["id"];

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$person = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>View person</title>
</head>
<body>
    <h2>Person details</h2>
    <?php if ($person) { ?>
        <p>SN: <?php echo $person['id']; ?></p>
        <p>First name: <?php echo $person['first_name']; ?></p>
        <p>Last name: <?php echo $person['last_name']; ?></p>
        <p>Age: <?php echo $person['age']; ?></p>
    <?php } else { ?>
        <p>Person not found.</p>
    <?php } ?>
    <a href="form.php">Back</a>
</body>
</html>