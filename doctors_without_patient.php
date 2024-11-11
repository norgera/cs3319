<?php
// Programmer Name: 97
// Purpose: List doctors without patients

include 'db_connection.php';

$sql = "SELECT doctor.docid, doctor.firstname, doctor.lastname
        FROM doctor
        LEFT JOIN patient ON doctor.docid = patient.treatsdocid
        WHERE patient.ohip IS NULL";

$result = mysqli_query($connection, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctors Without Patients</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Doctors Without Patients</h1>

        <table>
            <tr>
                <th>Doctor ID</th>
                <th>First Name</th>
                <th>Last Name</th>
            </tr>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($doc = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($doc['docid']) . "</td>";
                    echo "<td>" . htmlspecialchars($doc['firstname']) . "</td>";
                    echo "<td>" . htmlspecialchars($doc['lastname']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>All doctors have patients.</td></tr>";
            }
            ?>
        </table>

        <div class="button-container">
            <a href="mainmenu.php">Back to Main Menu</a>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($connection);
?>
