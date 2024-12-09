<?php
session_start();
include("connect.php");
include("products_table.php");

if (isset($_GET['error']) && $_GET['error'] == 1) {
    echo '<script>alert("FILTER NOT FOUND");</script>';
}

// Fetch Filter Codes from the database
$oemCodes = [];
$query = "SELECT DISTINCT oemCode FROM finished";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $oemCodes[] = $row['oemCode'];
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
    <title>Search Filter</title>
</head>
<body>
    <div class="ShowTableContainer" id="searchInterface" style="display:block;">
        <h1 class="form-title">Edit Filter Code</h1>
        <form method="post" action="editFilter.php">
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

        <!-- Display Filters Table -->
        <?php
         renderFiltersTable($conn);
        ?>
    </div>
</body>
</html>
