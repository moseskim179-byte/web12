<?php
include("connection.php");

$id = (int)$_GET["id"];

// STEP A: when the Update button is pressed, save the changes
if (isset($_POST["update"])) {
    $fname = $_POST["first_name"];
    $lname = $_POST["last_name"];
    $age   = $_POST["age"];

    $stmt = mysqli_prepare($conn,
        "UPDATE users SET first_name = ?, last_name = ?, age = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssii", $fname, $lname, $age, $id);
    mysqli_stmt_execute($stmt);

    header("Location: form.php");
    exit;
}

// STEP B: load the person so the form can be pre-filled
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$person = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit person</title>
</head>
<body>
    <h2>Edit person</h2>

    <?php if ($person) { ?>
    <form action="" method="POST">
        <label for="first_name">first name</label>
        <input type="text" name="first_name" id="first_name"
               value="<?php echo htmlspecialchars($person['first_name']); ?>" required>
        <br>
        <label for="last_name">last name</label>
        <input type="text" name="last_name" id="last_name"
               value="<?php echo htmlspecialchars($person['last_name']); ?>" required>
        <br>
        <label for="age">age</label>
        <input type="number" name="age" id="age"
               value="<?php echo $person['age']; ?>" required>
        <br>
        <input type="submit" name="update" value="Update">
    </form>
    <?php } else { ?>
        <p>Person not found.</p>
    <?php } ?>

    <a href="form.php">Back</a>
</body>
</html>