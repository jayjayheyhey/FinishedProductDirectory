<?php
session_start();
include("connect.php");
include("products_table.php");

// Initialize variables
$row = null;
$error = false;

// Handle search request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['searchButton'])) {
    $oemCode = $_POST['code'];

    $query = "SELECT * FROM finished WHERE oemCode = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $oemCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        $error = true;
    }
    $stmt->close();
}

// Fetch Filter Codes for the search input
$oemCodes = [];
$query = "SELECT DISTINCT oemCode FROM finished";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    while ($rowCode = $result->fetch_assoc()) {
        $oemCodes[] = $rowCode['oemCode'];
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
    <link rel="stylesheet" href="edit.css">

    <title>Manage Filter</title>
</head>
<body>
    <div class="ShowTableContainer" id="searchInterface" style="display:block;">
        <h1 class="form-title">Search and Edit Filter</h1>

        <!-- Search Form -->
        <form method="post" action="#editInterface">
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input list="oemCodes" name="code" id="code" placeholder="OEM Code" required>
                <datalist id="oemCodes">
                    <?php foreach ($oemCodes as $code): ?>
                        <option value="<?php echo htmlspecialchars($code); ?>">
                    <?php endforeach; ?>
                </datalist>
                <label for="code">OEM Code</label>
            </div>
            <input type="submit" class="btn" value="Search" name="searchButton">
        </form>
        <form method="post" action="dashboard.php">
            <input type="submit" class="btn" value="Back to Dashboard">
        </form>

        <!-- Display Error Message -->
        <?php if ($error): ?>
            <script>alert("FILTER NOT FOUND");</script>
        <?php endif; ?>

        <!-- Display Filters Table -->
        <?php renderFiltersTable($conn); ?>
    </div>

    <!-- Edit Filter Form -->
    <?php if (isset($row) && !empty($row)): ?>
        <script>
            document.getElementById("searchInterface").style.display = "none";
        </script>
        <div class="container" id="editInterface" style="display:block;">
            <h1 class="form-title">UPDATE Filter</h1>
            <form method="post" action="updateFilter.php" enctype="multipart/form-data">
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="text" name="code" id="code" placeholder="OEM Code" required value="<?php echo isset($row['oemCode']) ? $row['oemCode'] : ''; ?>" disabled>
                    <label for="code">OEM Code:</label>
                </div>
                <input type="hidden" name="code" value="<?php echo isset($row['oemCode']) ? $row['oemCode'] : ''; ?>">

                <div class="input-group">
                    <i class="fas fa-cog"></i>
                    <input type="text" name="pName" id="pName" placeholder="Part Number" required value="<?php echo isset($row['partNumber']) ? $row['partNumber'] : ''; ?>">
                    <label for="pName">Edit Part Number:</label>
                </div>
                <div class="input-group">
                    <i class="fas fa-book"></i>
                    <input type="text" name="name" id="name" placeholder="Product Name" required value="<?php echo isset($row['name']) ? $row['name'] : ''; ?>">
                    <label for="name">Edit Product Name:</label>
                </div>
                <div class="input-group">
                    <textarea id="description" name="description" placeholder="Description" rows="4"><?php echo isset($row['description']) ? $row['description'] : ''; ?></textarea>
                    <label for="description">Description</label>
                </div>
                <div class="input-group">
                    <i class="fas fa-image"></i>
                    <input type="file" name="pictures" id="pictures" accept="image/*">
                    <label for="pictures">Pictures</label>
                </div>
                <input type="submit" class="btn" value="Update Product Details" name="updateButton">
            </form>
        </div>
    <?php endif; ?>
</body>
</html>
