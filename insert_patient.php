<?php
// Programmer Name: 97
// Purpose: Insert a new patient

include 'db_connection.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ohip = mysqli_real_escape_string($connection, $_POST['ohip']);
    $firstname = mysqli_real_escape_string($connection, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($connection, $_POST['lastname']);
    $weight_lbs = $_POST['weight_lbs']; // Capture weight in lbs
    $birthdate = $_POST['birthdate'];
    $height_cm = $_POST['height_cm']; // Capture height in cm
    $treatsdocid = $_POST['treatsdocid'];

    // Convert weight from lbs to kg
    $weight_kg = $weight_lbs / 2.20462;

    // Convert height from cm to meters
    $height_m = $height_cm / 100;

    // Check if OHIP is unique
    $sql = "SELECT * FROM patient WHERE ohip = '$ohip'";
    $result = mysqli_query($connection, $sql);

    if (mysqli_num_rows($result) > 0) {
        $error = "OHIP number already exists.";
    } else {
        // Insert new patient
        $stmt = mysqli_prepare($connection, "INSERT INTO patient (ohip, firstname, lastname, weight, birthdate, height, treatsdocid) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssdsds", $ohip, $firstname, $lastname, $weight_kg, $birthdate, $height_m, $treatsdocid);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Patient added successfully.";
        } else {
            $error = "Error adding patient: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    }
}

// Get list of doctors
$doctor_sql = "SELECT docid, firstname, lastname FROM doctor";
$doctor_result = mysqli_query($connection, $doctor_sql);
if (!$doctor_result) {
    die("Query failed: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Insert New Patient</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Insert New Patient</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="post" action="insert_patient.php">
            <label>OHIP Number:</label>
            <input type="text" name="ohip" required><br>

            <label>First Name:</label>
            <input type="text" name="firstname" required><br>

            <label>Last Name:</label>
            <input type="text" name="lastname" required><br>

            <label>Weight (in lbs):</label>
            <input type="number" step="0.01" name="weight_lbs" required><br>

            <label>Birthdate:</label>
            <input type="date" name="birthdate" required><br>

            <label>Height (in cm):</label>
            <input type="number" step="0.01" name="height_cm" required><br>

            <label>Assign Doctor:</label>
            <select name="treatsdocid" required>
                <?php
                if (mysqli_num_rows($doctor_result) > 0) {
                    while ($doc = mysqli_fetch_assoc($doctor_result)) {
                        echo "<option value='" . htmlspecialchars($doc['docid']) . "'>" . htmlspecialchars($doc['firstname']) . " " . htmlspecialchars($doc['lastname']) . "</option>";
                    }
                } else {
                    echo "<option value=''>No doctors available</option>";
                }
                ?>
            </select><br><br>

            <input type="submit" value="Add Patient">
        </form>

        <div class="button-container">
            <a href="mainmenu.php">Back to Main Menu</a>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($connection);
?>

