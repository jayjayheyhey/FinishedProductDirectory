<?php

include("connect.php");

if (isset($_POST['input'])) {
    $input = $_POST['input'];

    // SQL query to search both FilterName and FilterCode
    $sql = "SELECT * FROM finished WHERE oemCode LIKE ? OR name LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchInput = "%$input%"; // Add wildcard characters for partial match
    $stmt->bind_param("ss", $searchInput, $searchInput); // Bind the input for both columns
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<table>
    <thead>
        <tr>
            <th>OEM Code</th>
            <th>Part Number</th>
            <th>Name</th>
            <th>Description</th>
            <th>Pictures</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['oemCode'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($row['partNumber'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($row['name'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($row['description'] ?? 'N/A') . "</td>";
                echo "<td><img src='" . htmlspecialchars($row['pictures'] ?? 'uploads/default.jpg') . "' alt='Product Image' width='100'></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No product found</td></tr>";
        }
        ?>
    </tbody>
</table>
