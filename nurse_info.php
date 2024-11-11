<?php
// Programmer Name: 97
// Purpose: Display nurse information

include 'db_connection.php';

$error = '';
$total_hours = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nurseid = $_POST['nurseid'];
    
    // Get nurse's information
    $sql = "SELECT nurse.firstname AS nurse_firstname, nurse.lastname AS nurse_lastname, supervisor.firstname AS sup_firstname, supervisor.lastname AS sup_lastname
            FROM nurse
            LEFT JOIN nurse AS supervisor ON nurse.reporttonurseid = supervisor.nurseid
            WHERE nurse.nurseid = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "s", $nurseid);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nurse_firstname, $nurse_lastname, $sup_firstname, $sup_lastname);
    
    if (mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        
        // Get workingfor information
        $sql = "SELECT doctor.firstname AS doc_firstname, doctor.lastname AS doc_lastname, workingfor.hours
                FROM workingfor
                INNER JOIN doctor ON workingfor.docid = doctor.docid
                WHERE workingfor.nurseid = ?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "s", $nurseid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $error = "Nurse not found.";
        mysqli_stmt_close($stmt);
    }
}

// Fetch list of nurses if not submitted
if (!isset($nurse_firstname)) {
    $sql = "SELECT nurseid, firstname, lastname FROM nurse";
    $nurse_result = mysqli_query($connection, $sql);
    
    if (!$nurse_result) {
        die("Query failed: " . mysqli_error($connection));
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Nurse Information</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Nurse Information</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!isset($nurse_firstname) && !$error): ?>
            <form method="post" action="nurse_info.php">
                <label>Select Nurse:</label>
                <select name="nurseid" required>
                    <?php
                    if (mysqli_num_rows($nurse_result) > 0) {
                        while ($row = mysqli_fetch_assoc($nurse_result)) {
                            echo "<option value='" . htmlspecialchars($row['nurseid']) . "'>" . htmlspecialchars($row['firstname']) . " " . htmlspecialchars($row['lastname']) . " (" . htmlspecialchars($row['nurseid']) . ")</option>";
                        }
                    } else {
                        echo "<option value=''>No nurses available</option>";
                    }
                    ?>
                </select><br><br>
                <input type="submit" value="Get Information">
            </form>

            <div class="button-container">
                <a href="mainmenu.php">Back to Main Menu</a><br>
            </div>
        <?php else: ?>
            <p><strong>Nurse:</strong> <?php echo htmlspecialchars($nurse_firstname) . " " . htmlspecialchars($nurse_lastname); ?></p>
            
            <?php if (mysqli_num_rows($result) > 0): ?>
                <table>
                    <tr>
                        <th>Doctor First Name</th>
                        <th>Doctor Last Name</th>
                        <th>Hours Worked</th>
                    </tr>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['doc_firstname']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['doc_lastname']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['hours']) . "</td>";
                        echo "</tr>";
                        $total_hours += $row['hours'];
                    }
                    ?>
                </table>
                <p><strong>Total Hours Worked:</strong> <?php echo $total_hours; ?></p>
            <?php else: ?>
                <p>This nurse is not working for any doctors.</p>
            <?php endif; ?>

            <p><strong>Supervisor Nurse:</strong> <?php echo $sup_firstname ? htmlspecialchars($sup_firstname . " " . $sup_lastname) : "No supervisor."; ?></p>

            <div class="button-container">
                <a href="nurse_info.php">Select Another Nurse</a><br>
                <a href="mainmenu.php">Back to Main Menu</a><br>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
mysqli_close($connection);
?>

