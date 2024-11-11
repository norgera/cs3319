<?php
// Programmer Name: 97
// Purpose: List doctors and their patients

include 'db_connection.php';

$sql = "SELECT doctor.firstname AS doc_firstname, doctor.lastname AS doc_lastname, patient.firstname AS pat_firstname, patient.lastname AS pat_lastname
        FROM doctor
        LEFT JOIN patient ON doctor.docid = patient.treatsdocid
        ORDER BY doctor.lastname, doctor.firstname";

$result = mysqli_query($connection, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctors and Their Patients</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Doctors and Their Patients</h1>

        <table>
            <?php
            $current_doctor = '';
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $doctor_name = htmlspecialchars($row['doc_firstname']) . ' ' . htmlspecialchars($row['doc_lastname']);

                    // Display a highlighted row for each doctor
                    if ($current_doctor != $doctor_name) {
                        if ($current_doctor != '') {
                            echo "<tr><td colspan='2' class='no-patients'></td></tr>";
                        }
                        $current_doctor = $doctor_name;
                        echo "<tr class='doctor-row'><td colspan='2'>Doctor: $current_doctor</td></tr>";
                        echo "<tr><th>Patient First Name</th><th>Patient Last Name</th></tr>";
                    }

                    // Display patient details or "No patients" if null
                    if ($row['pat_firstname'] != null) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['pat_firstname']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['pat_lastname']) . "</td>";
                        echo "</tr>";
                    } else {
                        echo "<tr><td colspan='2' class='no-patients'>No patients</td></tr>";
                    }
                }
                echo "</table>";
            } else {
                echo "<p>No doctors or patients found.</p>";
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
