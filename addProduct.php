<?php
session_start();
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitButton'])) {
    // Retrieve form data
    $oemCode = $_POST['code'];
    $partNumber = $_POST['pName'];
    $productName = $_POST['name'];
    $outsideDiameter = $_POST['odiam'];
    $outsideDiameterUnit = $_POST['odiamUnit'];

    $insideDiameter = $_POST['indiam'];
    $insideDiameterUnit = $_POST['indiamUnit'];

    $Height = $_POST['height'];
    $HeightUnit = $_POST['heightUnit'];

    // Handle file upload
    $targetDir = "uploads/"; // Define your upload directory
    $targetFile = $targetDir . basename($_FILES["pictures"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if file is a real image
    if (isset($_FILES["pictures"]["tmp_name"]) && !empty($_FILES["pictures"]["tmp_name"])) {
        $check = getimagesize($_FILES["pictures"]["tmp_name"]);
        if ($check === false) {
            echo '<script>
                alert("File is not an image.");
                window.history.back();
            </script>';
            exit;
        }
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo '<script>
            alert("Only JPG, JPEG, PNG & GIF files are allowed.");
            window.history.back();
        </script>';
        exit;
    }

    // Move the uploaded file to the target directory
    if (!move_uploaded_file($_FILES["pictures"]["tmp_name"], $targetFile)) {
        echo '<script>
            alert("There was an error uploading your file.");
            window.history.back();
        </script>';
        exit;
    }

    // Insert data into the database
    $insertQuery = "INSERT INTO finished (oemCode, partNumber, name, outsideDiameter, outsideDiameterUnit, insideDiameter, insideDiameterUnit, Height, HeightUnit, pictures) 
                    VALUES (?, ?, ?, ?, ?,?,?,?,?,?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssssssssss", $oemCode, $partNumber, $productName, $outsideDiameter, $outsideDiameterUnit, $insideDiameter, $insideDiameterUnit, $Height, $HeightUnit, $targetFile);

    if ($stmt->execute()) {
        echo '<script>
            alert("Product successfully added.");
            window.location.href = "dashboard.php";
        </script>';
    } else {
        echo '<script>
            alert("Error adding product: ' . $conn->error . '");
            window.history.back();
        </script>';
    }

    $stmt->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="select.css">
    <title>Add Product</title>
</head>
<body>
    <div class="container" id="addInterface" style="display:block;">
        <h1 class="form-title">Add Product</h1>
        <form method="post" action="" enctype="multipart/form-data">
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="text" name="code" id="code" placeholder="OEM Code" required>
                <label for="code">OEM Code</label>
            </div>
            <div class="input-group">
                <i class="fas fa-book"></i>
                <input type="text" name="pName" id="pName" placeholder="Part Number" required>
                <label for="pName">Part Number</label>
            </div>
            <div class="input-group">
                <i class="fas fa-book"></i>
                <input type="text" name="name" id="name" placeholder="Product Name" required>
                <label for="name">Product Name</label>
            </div>
            <div class="input-group">
                <i class="fas fa-ruler"></i>
                <input type="number" name="odiam" id="odiam" placeholder="Outside Diameter" required step="0.01">
                <label for="odiam">Outside Diameter</label>
                <select name="odiamUnit" id="odiamUnit" required>
                    <option value="cm">cm</option>
                    <option value="in">in</option>
                    <option value="mm">mm</option>
                    <option value="ft">ft</option>
                </select>
            </div>

            <div class="input-group">
                <i class="fas fa-ruler"></i>
                <input type="number" name="indiam" id="indiam" placeholder="Inside Diameter" required step="0.01">
                <label for="indiam">Inside Diameter</label>
                <select name="indiamUnit" id="indiamUnit" required>
                    <option value="cm">cm</option>
                    <option value="in">in</option>
                    <option value="mm">mm</option>
                    <option value="ft">ft</option>
                </select>
            </div>

            <div class="input-group">
                <i class="fas fa-ruler"></i>
                <input type="number" name="height" id="height" placeholder="Height" required step="0.01">
                <label for="height">Height</label>
                <select name="heightUnit" id="heightUnit" required>
                    <option value="cm">cm</option>
                    <option value="in">in</option>
                    <option value="mm">mm</option>
                    <option value="ft">ft</option>
                </select>
            </div>
            <div class="input-group">
                <i class="fas fa-image"></i>
                <input type="file" name="pictures" id="pictures" accept="image/*" required>
                <label for="pictures">Pictures</label>
            </div>
            <input type="submit" class="btn" value="Submit Product" name="submitButton">
        </form>
        <form method="post" action="dashboard.php">
            <input type="submit" class="btn" value="Back to Dashboard">
        </form> 
    </div>
</body>
</html>
