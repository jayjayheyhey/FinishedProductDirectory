<?php
session_start();
include("connect.php"); // Include database connection
include("products_table.php");

$oemCode = ""; // Initialize OEM Code variable
$partName = ""; // To hold the name of the part being removed
$confirmation = false; // To track whether the confirmation step should be shown

// Fetch OEM Codes from the database
$oemCodes = [];
$query = "SELECT DISTINCT oemCode FROM finished";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $oemCodes[] = $row['oemCode'];
    }
} else {
    $errorMessage = "Error fetching OEM codes: " . htmlspecialchars($conn->error);
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['oemCode']) && !empty($_POST['oemCode'])) {
        // Sanitize user input
        $oemCode = $conn->real_escape_string($_POST['oemCode']);

        // Check if the item exists in the database
        $stmt = $conn->prepare("SELECT name FROM finished WHERE oemCode = ?");
        $stmt->bind_param("s", $oemCode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $partName = $row['name'];

            // If confirmation is provided, delete the item
            if (isset($_POST['confirmDelete']) && $_POST['confirmDelete'] === 'Yes') {
                $stmt = $conn->prepare("DELETE FROM finished WHERE oemCode = ?");
                $stmt->bind_param("s", $oemCode);

                if ($stmt->execute()) {
                    $successMessage = "Product successfully removed.";
                } else {
                    $errorMessage = "Error deleting product: " . htmlspecialchars($conn->error);
                }
            } else {
                // Trigger confirmation step
                $confirmation = true;
            }
        } else {
            $errorMessage = "Product not found.";
        }
    } else {
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="tablestyle.css">
    <title>Remove Product</title>
</head>
<body>
    <div class="ShowTableContainer" id="removeItem">
        <h1 class="form-title">Remove Product</h1>
        
        <!-- Display success/error errorMessage -->
        <?php if (!empty($successMessage)): ?>
            <p class="popup" id="success"><?php echo $successMessage; ?></p>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <p class="popup"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        
        <!-- Show the confirmation form after OEM Code input -->
        <?php if ($confirmation) { ?>
            <p style= "color: hsl(327,90%,28%);">Are you sure you want to delete this product?</p>
            <p><strong>OEM Code:</strong> <?php echo htmlspecialchars($oemCode); ?></p>
            <p><strong>Part Name:</strong> <?php echo htmlspecialchars($partName); ?></p>
            <form method="post" action="">
                <input type="hidden" name="oemCode" value="<?php echo htmlspecialchars($oemCode); ?>">
                <input type="submit" class="btn" value="Yes" name="confirmDelete">
            </form>
            <form method="get" action="removeItem.php">
                <input type="submit" class="btn" value="Cancel">
            </form>
        <?php } else { ?>
            <!-- Form for entering the OEM Code -->
            <form method="post" action="">
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input list="oemCodes" name="oemCode" id="oemCode" placeholder="OEM Code" required>
                    <datalist id="oemCodes">
                        <?php foreach ($oemCodes as $code): ?>
                            <option value="<?php echo htmlspecialchars($code); ?>">
                        <?php endforeach; ?>
                    </datalist>
                    <label for="oemCode">OEM Code</label>
                </div>
                <input type="submit" class="btn" value="Delete Product">
            </form>
        <?php } ?>
        
        <!-- Back to dashboard button -->
        <form method="post" action="dashboard.php">
            <input type="submit" class="btn" value="Back to Dashboard">
        </form>

        <!-- Display Filters Table -->
        <?php renderFiltersTable($conn); ?>
    </div>
</body>
</html>
