<?php
session_start();
include('connect.php');
include("filters_table.php");

// Display error messages based on query parameters
if (isset($_GET['error'])) {
    if ($_GET['error'] == 1) {
        echo '<script>alert("ERROR: The resulting quantity cannot be less than 0.");</script>';
    } else if ($_GET['error'] == 2) {
        echo '<script>alert("ERROR: The resulting quantity cannot exceed the maximum stock.");</script>';
    }
}

// Fetch available FilterCodes for the dropdown
$filterCodes = [];
$query = "SELECT DISTINCT FilterCode FROM filters";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $filterCodes[] = $row['FilterCode'];
    }
} else {
    echo '<script>alert("Error fetching FilterCodes: ' . htmlspecialchars($conn->error) . '");</script>';
}

// Initialize variables
$FilterCode = '';
$currentQuantity = 0;
$maxStock = 0;

if (isset($_GET['FilterCode']) && !empty($_GET['FilterCode'])) {
    $FilterCode = $_GET['FilterCode'];

    // Fetch current quantity and max stock
    $stmt = $conn->prepare("SELECT Quantity, MaxStock FROM filters WHERE FilterCode = ?");
    $stmt->bind_param("s", $FilterCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentQuantity = $row['Quantity'];
        $maxStock = $row['MaxStock'];
    } else {
        echo '<script>alert("Error: Filter Code not found.");</script>';
        $FilterCode = '';
    }
}

// Process form submission for quantity update
if (isset($_POST['submitQuantityButton'])) {
    // Ensure FilterCode is passed
    if (empty($_POST['FilterCode'])) {
        echo '<script>alert("Invalid request. Filter Code is missing.");</script>';
        exit();
    }

    // Retrieve data from form
    $quantityChangeAdd = isset($_POST['quantityAdd']) ? (int)$_POST['quantityAdd'] : 0;
    $quantityChangeSubtract = isset($_POST['quantitySubtract']) ? (int)$_POST['quantitySubtract'] : 0;

    // Calculate the new quantity
    $newQuantity = $currentQuantity + $quantityChangeAdd - $quantityChangeSubtract;

    // Validate the new quantity
    if ($newQuantity < 0) {
        header("Location: changeQuantity.php?FilterCode=" . urlencode($FilterCode) . "&error=1");
        exit();
    }
    if ($newQuantity > $maxStock) {
        header("Location: changeQuantity.php?FilterCode=" . urlencode($FilterCode) . "&error=2");
        exit();
    }

    // Update the database
    $stmt = $conn->prepare("UPDATE filters SET Quantity = ? WHERE FilterCode = ?");
    $stmt->bind_param("is", $newQuantity, $FilterCode);

    if ($stmt->execute()) {
        echo '<script>alert("Quantity updated successfully!");</script>';
        header("Location: homepage.php"); // Redirect to dashboard
        exit();
    } else {
        echo '<script>alert("Error updating quantity: ' . htmlspecialchars($conn->error) . '");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Quantity</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="tablestyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Section to select FilterCode -->
    <div class="ShowTableContainer" id="enterFilterCode" style="<?php echo empty($FilterCode) ? 'display:block;' : 'display:none;'; ?>">
        <h1 class="form-title">Edit Quantity</h1>
        <form method="get" action="changeQuantity.php">
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input list="filterCodes" name="FilterCode" id="FilterCode" placeholder="Filter Code" required>
                <datalist id="filterCodes">
                    <?php foreach ($filterCodes as $code): ?>
                        <option value="<?php echo htmlspecialchars($code); ?>">
                    <?php endforeach; ?>
                </datalist>
                <label for="FilterCode">Filter Code</label>
            </div>
            <input type="submit" class="btn" value="Submit Filter Code" name="submitFilterCode">
        </form>
        <form method="post" action="homepage.php">
            <input type="submit" class="btn" value="Back to Dashboard">
        </form>
        <?php renderFiltersTable($conn); ?>
    </div>

    <!-- Section to update quantity -->
    <div class="container" id="updateQuantity" style="<?php echo !empty($FilterCode) ? 'display:block;' : 'display:none;'; ?>">
        <h1 class="form-title">Change Quantity</h1>
        <p><strong>Code:</strong> <?php echo htmlspecialchars($FilterCode); ?></p>
        <p><strong>Current Quantity:</strong> <?php echo htmlspecialchars($currentQuantity); ?></p>
        <p><strong>Max Stock:</strong> <?php echo htmlspecialchars($maxStock); ?></p><br>

        <form method="post" action="changeQuantity.php?FilterCode=<?php echo urlencode($FilterCode); ?>">
            <input type="hidden" name="FilterCode" value="<?php echo htmlspecialchars($FilterCode); ?>">
            <div class="input-group">
                <i class="fas fa-calculator"></i>
                <input type="number" name="quantityAdd" id="quantityAdd" placeholder="Add Quantity" min="0">
                <label for="quantityAdd">Add Quantity</label>
            </div>
            <div class="input-group">
                <i class="fas fa-calculator"></i>
                <input type="number" name="quantitySubtract" id="quantitySubtract" placeholder="Subtract Quantity" min="0">
                <label for="quantitySubtract">Subtract Quantity</label>
            </div>
            <input type="submit" class="btn" value="Submit Quantity Change" name="submitQuantityButton">
        </form>
    </div>
</body>
</html>
