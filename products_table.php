<?php
function renderFiltersTable($conn) {
    ?>
    <div id="products_table">
        <table>
            <thead>
                <tr>
                    <th>OEM Code</th>
                    <th>Part Number</th>
                    <th>Product Name</th>
                    <th>Dimensions</th>
                    <th>Pictures</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch data from `finished` table
                $sql = "SELECT * FROM finished"; 
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $dimensions = "{$row['outsideDiameter']}{$row['outsideDiameterUnit']} x
                                       {$row['insideDiameter']}{$row['insideDiameterUnit']} x
                                       {$row['height']}{$row['heightUnit']}";

                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['oemCode'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['partNumber'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['name'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($dimensions) . "</td>";  
                        echo "<td><img src='" . htmlspecialchars($row['pictures'] ?? 'uploads/default.jpg') . "' alt='Product Image' width='100'></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No products found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>
