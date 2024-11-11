<?php
// Programmer Name: 97
// Purpose: Modify patient's weight

include 'db_connection.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ohip'])) {
        $ohip = $_POST['ohip'];
        // Fetch patient info
        $sql = "SELECT firstname, lastname, weight FROM patient WHERE ohip = ?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "s", $ohip);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $firstname, $lastname, $current_weight_kg);
        if (mysqli_stmt_fetch($stmt)) {
            $patient_name = htmlspecialchars($firstname) . ' ' . htmlspecialchars($lastname);
            // Convert the current weight from kg to lbs for display
            $current_weight_lbs = round($current_weight_kg * 2.20462, 2);
        } else {
            $error = "Patient not found.";
        }
        mysqli_stmt_close($stmt);
    } elseif (isset($_POST['new_weight_lbs'])) {
        // Update weight
        $ohip = $_POST['ohip_hidden'];
        $new_weight_lbs = $_POST['new_weight_lbs'];

        // Convert new weight from lbs to kg
        $new_weight_kg = $new_weight_lbs / 2.20462;

        $sql = "UPDATE patient SET weight = ? WHERE ohip = ?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "ds", $new_weight_kg, $ohip);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Weight updated successfully.";
        } else {
            $error = "Error updating weight: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch patients list
if (!isset($patient_name) && !$error && !$success) {
    $sql = "SELECT ohip, firstname, lastname FROM patient";
    $patient_result = mysqli_query($connection, $sql);
    if (!$patient_result) {
        die("Query failed: " . mysqli_error($connection));
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modify Patient Weight</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Modify Patient Weight</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if (isset($patient_name)): ?>
            <p>Patient: <?php echo $patient_name; ?></p>
            <p>Current Weight: <?php echo $current_weight_lbs; ?> lbs</p>
            <form method="post" action="modify_patient.php">
                <label>New Weight (in lbs):</label>
                <input type="number" step="0.01" name="new_weight_lbs" required><br><br>
                <input type="hidden" name="ohip_hidden" value="<?php echo htmlspecialchars($ohip); ?>">
                <input type="submit" value="Update Weight">
            </form>
        <?php elseif (!$error && !$success): ?>
            <form method="post" action="modify_patient.php">
                <label>Select Patient:</label>
                <select name="ohip" required>
                    <?php
                    if (mysqli_num_rows($patient_result) > 0) {
                        while ($row = mysqli_fetch_assoc($patient_result)) {
                            echo "<option value='" . htmlspecialchars($row['ohip']) . "'>" . htmlspecialchars($row['firstname']) . " " . htmlspecialchars($row['lastname']) . " (" . htmlspecialchars($row['ohip']) . ")</option>";
                        }
                    } else {
                        echo "<option value=''>No patients available</option>";
                    }
                    ?>
                </select><br><br>
                <input type="submit" value="Select Patient">
            </form>
        <?php endif; ?>

        <div class="button-container">
            <a href="mainmenu.php">Back to Main Menu</a>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($connection);
?>
