<?php
session_start();
include("connect.php"); // Include database connection
include("filters_table.php");

$message = ""; // Initialize the message variable
$FilterCode = ""; // Initialize FilterCode variable
$FilterName = ""; // To hold the name of the filter being removed
$confirmation = false; // To track whether the confirmation step should be shown

// Fetch Filter Codes from the database
$filterCodes = [];
$query = "SELECT DISTINCT FilterCode FROM filters";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $filterCodes[] = $row['FilterCode'];
    }
} else {
    $message = "Error fetching filter codes: " . htmlspecialchars($conn->error);
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['FilterCode']) && !empty($_POST['FilterCode'])) {
        // Sanitize user input
        $FilterCode = $conn->real_escape_string($_POST['FilterCode']);

        // Check if the filter exists in the database
        $stmt = $conn->prepare("SELECT FilterName FROM filters WHERE FilterCode = ?");
        $stmt->bind_param("s", $FilterCode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $FilterName = $row['FilterName'];

            // If confirmation is provided, delete the filter
            if (isset($_POST['confirmDelete']) && $_POST['confirmDelete'] === 'Yes') {
                $stmt = $conn->prepare("DELETE FROM filters WHERE FilterCode = ?");
                $stmt->bind_param("s", $FilterCode);

                if ($stmt->execute()) {
                    $message = "Filter successfully removed.";
                } else {
                    $message = "Error deleting filter: " . htmlspecialchars($conn->error);
                }
            } else {
                // Trigger confirmation step
                $confirmation = true;
            }
        } else {
            $message = "Filter not found.";
        }
    } else {
        $message = "Filter Code is required.";
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
    <link rel="stylesheet" href="tablestyle.css">
    <title>Remove Filter</title>
</head>
<body>
    <div class="ShowTableContainer" id="removeItem">
        <h1 class="form-title">Remove Filter</h1>
        
        <!-- Display success/error message -->
        <?php if (!empty($message)) { ?>
            <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
        <?php } ?>
        
        <!-- Show the confirmation form after FilterCode input -->
        <?php if ($confirmation) { ?>
            <p>Are you sure you want to delete this filter?</p>
            <p><strong>Filter Code:</strong> <?php echo htmlspecialchars($FilterCode); ?></p>
            <p><strong>Filter Name:</strong> <?php echo htmlspecialchars($FilterName); ?></p>
            <form method="post" action="">
                <input type="hidden" name="FilterCode" value="<?php echo htmlspecialchars($FilterCode); ?>">
                <input type="submit" class="btn" value="Yes" name="confirmDelete">
            </form>
            <form method="get" action="removeItem.php">
                <button type="submit" class="btn">Cancel</button>
            </form>
        <?php } else { ?>
            <!-- Form for entering the FilterCode -->
            <form method="post" action="">
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
                <input type="submit" class="btn" value="Delete Filter">
            </form>
        <?php } ?>
        
        <!-- Back to dashboard button -->
        <form method="post" action="homepage.php">
            <input type="submit" class="btn" value="Back to Dashboard">
        </form>

        <!-- Display Filters Table -->
        <?php renderFiltersTable($conn); ?>
    </div>
</body>
</html>
