<?php
session_start();
include("connect.php");
include("products_table.php");

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="tablestyle.css">
    <link rel="stylesheet" href="search.css">
    <title>Dashboard</title>

    
</head>
<body>
    <div class="container" id=dashboard>
        <h1 class="form-title">Finished Product Item Catalogue</h1>
        <form method ="post" action="addProduct.php">
            <input type="submit" class="btn" value="Add product" name="addProductButton">
        </form>
        <form method="post" action="removeItem.php">
            <input type="submit" class="btn" value="Remove Product" name="removeProductButton">
        </form>
        <form method ="post" action="editProduct.php">
            <input type="submit" class="btn" value="Edit Product" name="editProductButton">
        </form>
        <form method ="post">
            <input type="text" class="form-control" id="live_search" autocomplete="off" placeholder="Search ... ">
        </form>

        <div id="searchresult"></div>
         <!-- Display Filters Table -->
        <?php
         renderFiltersTable($conn);
         ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script type="text/javascript">
            $(document).ready(function () {

                $("#live_search").on("keyup", function () {
                    var input = $(this).val().trim(); // Get and trim the input value

                    if (input.length > 0) {
                        $.ajax({
                            url: "livesearch.php",
                            method: "POST",
                            data: { input: input },
                            success: function (data) {
                                $("#searchresult").html(data); 
                                $("#searchresult").css("display", "block"); //kapag may data, ipakita yung search result
                                $("#products_table").hide(); //tago muna yung products_table
                            }
                        });
                    } else {
                        $("#searchresult").html(""); 
                        $("#searchresult").css("display", "none"); //kapag ala na yung data, tago na yung search result
                        $("#products_table").show(); //ngayon, yung filters products_table naman ang ipapakita para mapakita lahat ng filter
                    }
                });
            });
    </script>
</body>
</html>