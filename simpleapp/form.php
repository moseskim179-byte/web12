<?php
include("process.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Document</title>
</head>
<body>
    <h2>Simple for data submission</h2>
    <form action="" method="POST">
        <label for="first_name">first name</label>
        <input type="text" name="first_name" id="first_name">
        <br>
        <label for="last_name">last name</label>
        <input type="text" name="last_name" id="last_name">
        <br>
        <label for="age">age</label>
        <input type="number" name="age" id="age">
        <br>
        <input type="submit" name="submit" id="submit">
    </form>
    <hr>
   <?php
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id");
?>
<table border="2">
    <thead>
        <tr>
            <th>sn</th>
            <th>first name</th>
            <th>last name</th>
            <th>age</th>
            <th>action</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['first_name']; ?></td>
            <td><?php echo $row['last_name']; ?></td>
            <td><?php echo $row['age']; ?></td>
            <td>
                <a href="view.php?id=<?php echo $row['id']; ?>">view</a>
                  <a href="edit.php?id=<?php echo $row['id']; ?>">edit</a>
                  <a href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this person?')">delete</a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
