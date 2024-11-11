<?php
// Programmer Name: 97
// Purpose: Delete a patient

include 'db_connection.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == 'Yes') {
        // Proceed with deletion
        $ohip = $_POST['ohip'];
        $sql = "DELETE FROM patient WHERE ohip = ?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "s", $ohip);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Patient deleted successfully.";
        } else {
            $error = "Error deleting patient: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } elseif (isset($_POST['ohip'])) {
        // Confirm deletion
        $ohip = $_POST['ohip'];
        $sql = "SELECT firstname, lastname FROM patient WHERE ohip = ?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "s", $ohip);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $firstname, $lastname);
        if (mysqli_stmt_fetch($stmt)) {
            $patient_name = htmlspecialchars($firstname) . ' ' . htmlspecialchars($lastname);
        } else {
            $error = "Patient not found.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch list of patients if not in delete confirmation step
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
    <title>Delete Patient</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Delete Patient</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($patient_name)): ?>
            <p>Are you sure you want to delete patient: <?php echo $patient_name; ?>?</p>
            <form method="post" action="delete_patient.php">
                <input type="hidden" name="ohip" value="<?php echo htmlspecialchars($ohip); ?>">
                <input type="submit" name="confirm_delete" value="Yes">
                <input type="submit" name="confirm_delete" value="No">
            </form>
        <?php elseif (!$error && !$success): ?>
            <form method="post" action="delete_patient.php">
                <label>Select Patient to Delete:</label>
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
                <input type="submit" value="Delete Patient">
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
