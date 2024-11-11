<?php
// Programmer Name: 97
// Purpose: List all patients with sorting options

include 'db_connection.php';

$orderBy = 'lastname';
$orderDir = 'ASC';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['sort_by'])) {
        $orderBy = $_POST['sort_by'];
    }
    if (isset($_POST['order'])) {
        $orderDir = $_POST['order'];
    }
}

$sql = "SELECT patient.*, doctor.firstname AS doc_firstname, doctor.lastname AS doc_lastname
        FROM patient
        LEFT JOIN doctor ON patient.treatsdocid = doctor.docid
        ORDER BY $orderBy $orderDir";

$result = mysqli_query($connection, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>List of Patients</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>List of Patients</h1>

        <form method="post" action="list_patients.php">
            <label>Sort By:</label>
            <input type="radio" name="sort_by" value="firstname" <?php if ($orderBy == 'firstname') echo 'checked'; ?>>First Name
            <input type="radio" name="sort_by" value="lastname" <?php if ($orderBy == 'lastname') echo 'checked'; ?>>Last Name
            <br>
            <label>Order:</label>
            <input type="radio" name="order" value="ASC" <?php if ($orderDir == 'ASC') echo 'checked'; ?>>Ascending
            <input type="radio" name="order" value="DESC" <?php if ($orderDir == 'DESC') echo 'checked'; ?>>Descending
            <br>
            <input type="submit" value="Sort">
        </form>

        <table>
            <tr>
                <th>OHIP</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Weight (kg)</th>
                <th>Weight (lbs)</th>
                <th>Height (m)</th>
                <th>Height (ft & in)</th>
                <th>Birthdate</th>
                <th>Doctor</th>
            </tr>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $weight_kg = $row['weight'];
                    $weight_lbs = round($weight_kg * 2.20462, 2);
                    $height_m = $row['height'];
                    $total_inches = $height_m * 39.3701;
                    $feet = floor($total_inches / 12);
                    $inches = round($total_inches % 12);

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['ohip']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
                    echo "<td>" . htmlspecialchars($weight_kg) . " kg</td>";
                    echo "<td>" . htmlspecialchars($weight_lbs) . " lbs</td>";
                    echo "<td>" . htmlspecialchars($height_m) . " m</td>";
                    echo "<td>" . htmlspecialchars($feet) . " ft " . htmlspecialchars($inches) . " in</td>";
                    echo "<td>" . htmlspecialchars($row['birthdate']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['doc_firstname'] . " " . $row['doc_lastname']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No patients found.</td></tr>";
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
