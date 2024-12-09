<?php
include("connect.php");

if (isset($_GET['add']) && $_GET['add'] == 'success') {
    $successMessage = "Product successfully added.";
}
$errorMessage = "";
$submittedData = []; // To hold user-submitted data

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

    // Store submitted data so we can re-populate the form if needed
    $submittedData = [
        'code' => $oemCode,
        'pName' => $partNumber,
        'name' => $productName,
        'odiam' => $outsideDiameter,
        'odiamUnit' => $outsideDiameterUnit,
        'indiam' => $insideDiameter,
        'indiamUnit' => $insideDiameterUnit,
        'height' => $Height,
        'heightUnit' => $HeightUnit,
    ];

    // Handle file upload
    $targetDir = "uploads/"; // Define your upload directory
    $targetFile = $targetDir . basename($_FILES["pictures"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if file is a real image
    if (isset($_FILES["pictures"]["tmp_name"]) && !empty($_FILES["pictures"]["tmp_name"])) {
        $check = getimagesize($_FILES["pictures"]["tmp_name"]);
        if ($check === false) {
            $errorMessage = "File is not an image.";
            exit();
        }
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $errorMessage = "Only JPG, JPEG, PNG & GIF files are allowed.";
        exit();
    }

    // Move the uploaded file to the target directory
    if (!move_uploaded_file($_FILES["pictures"]["tmp_name"], $targetFile)) {
        $errorMessage = "There was an error uploading your file.";
        exit();
    }


    $checkCode = "SELECT * FROM finished WHERE oemCode = '$oemCode'";
    $checkName = "SELECT * FROM finished WHERE name = '$productName'";
    $checkPart = "SELECT * FROM finished WHERE partNumber = '$partNumber'";

    if ($conn->query($checkCode)->num_rows > 0) {
        $errorMessage = "OEM code already exists.";
    } elseif ($conn->query($checkName)->num_rows > 0) {
        $errorMessage = "Product name already exists.";
    } elseif ($conn->query($checkPart)->num_rows > 0) {
        $errorMessage = "Part number already exists.";
    } elseif ($outsideDiameter < 0 || $outsideDiameter >= 10000) {
        $errorMessage = "Invalid Outside Diameter.";
    } elseif ($insideDiameter < 0 || $insideDiameter >= 10000) {
        $errorMessage = "Invalid Inside Diameter.";
    } elseif ($Height < 0 || $Height >= 10000) {
        $errorMessage = "Invalid Height.";
    } else {
    // Insert data into the database
    $insertQuery = "INSERT INTO finished (oemCode, partNumber, name, outsideDiameter, outsideDiameterUnit, insideDiameter, insideDiameterUnit, Height, HeightUnit, pictures) 
                    VALUES (?, ?, ?, ?, ?,?,?,?,?,?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssssssssss", $oemCode, $partNumber, $productName, $outsideDiameter, $outsideDiameterUnit, $insideDiameter, $insideDiameterUnit, $Height, $HeightUnit, $targetFile);

    if ($stmt->execute()) {
        $submittedData = []; // Clear form data on success  
        header("Location: addProduct.php?add=success");
        exit;

    } else {
        echo '<script>
            alert("Error adding product: ' . $conn->error . '");
            window.history.back();
        </script>';
    }

    $stmt->close();
    exit;
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
    <link rel="stylesheet" href="select.css">
    <title>Add Product</title>
</head>
<body>
    <div class="container" id="addInterface" style="display:block;">
        <h1 class="form-title">Add Product</h1>
        <?php if (!empty($successMessage)): ?>
            <p class="popup" id="success"><?php echo $successMessage; ?></p>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <p class="popup"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <form method="post" action="" enctype="multipart/form-data">
        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="text" name="code" id="code" placeholder="OEM Code" required value="<?php echo isset($_POST['code']) ? $_POST['code'] : ''; ?>">
            <label for="code">OEM Code</label>
        </div>

        <div class="input-group">
            <i class="fas fa-book"></i>
            <input type="text" name="pName" id="pName" placeholder="Part Number" required value="<?php echo isset($_POST['pName']) ? $_POST['pName'] : ''; ?>">
            <label for="pName">Part Number</label>
        </div>

        <div class="input-group">
            <i class="fas fa-book"></i>
            <input type="text" name="name" id="name" placeholder="Product Name" required value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>">
            <label for="name">Product Name</label>
        </div>

        <div class="input-group">
            <i class="fas fa-ruler"></i>
            <input type="number" name="odiam" id="odiam" placeholder="Outside Diameter" required step="0.01" value="<?php echo isset($_POST['odiam']) ? $_POST['odiam'] : ''; ?>">
            <label for="odiam">Outside Diameter</label>
            <select name="odiamUnit" id="odiamUnit" required>
                <option value="cm" <?php echo isset($_POST['odiamUnit']) && $_POST['odiamUnit'] == 'cm' ? 'selected' : ''; ?>>cm</option>
                <option value="in" <?php echo isset($_POST['odiamUnit']) && $_POST['odiamUnit'] == 'in' ? 'selected' : ''; ?>>in</option>
                <option value="mm" <?php echo isset($_POST['odiamUnit']) && $_POST['odiamUnit'] == 'mm' ? 'selected' : ''; ?>>mm</option>
                <option value="ft" <?php echo isset($_POST['odiamUnit']) && $_POST['odiamUnit'] == 'ft' ? 'selected' : ''; ?>>ft</option>
            </select>
        </div>

        <div class="input-group">
            <i class="fas fa-ruler"></i>
            <input type="number" name="indiam" id="indiam" placeholder="Inside Diameter" required step="0.01" value="<?php echo isset($_POST['indiam']) ? $_POST['indiam'] : ''; ?>">
            <label for="indiam">Inside Diameter</label>
            <select name="indiamUnit" id="indiamUnit" required>
                <option value="cm" <?php echo isset($_POST['indiamUnit']) && $_POST['indiamUnit'] == 'cm' ? 'selected' : ''; ?>>cm</option>
                <option value="in" <?php echo isset($_POST['indiamUnit']) && $_POST['indiamUnit'] == 'in' ? 'selected' : ''; ?>>in</option>
                <option value="mm" <?php echo isset($_POST['indiamUnit']) && $_POST['indiamUnit'] == 'mm' ? 'selected' : ''; ?>>mm</option>
                <option value="ft" <?php echo isset($_POST['indiamUnit']) && $_POST['indiamUnit'] == 'ft' ? 'selected' : ''; ?>>ft</option>
            </select>
        </div>

        <div class="input-group">
            <i class="fas fa-ruler"></i>
            <input type="number" name="height" id="height" placeholder="Height" required step="0.01" value="<?php echo isset($_POST['height']) ? $_POST['height'] : ''; ?>">
            <label for="height">Height</label>
            <select name="heightUnit" id="heightUnit" required>
                <option value="cm" <?php echo isset($_POST['heightUnit']) && $_POST['heightUnit'] == 'cm' ? 'selected' : ''; ?>>cm</option>
                <option value="in" <?php echo isset($_POST['heightUnit']) && $_POST['heightUnit'] == 'in' ? 'selected' : ''; ?>>in</option>
                <option value="mm" <?php echo isset($_POST['heightUnit']) && $_POST['heightUnit'] == 'mm' ? 'selected' : ''; ?>>mm</option>
                <option value="ft" <?php echo isset($_POST['heightUnit']) && $_POST['heightUnit'] == 'ft' ? 'selected' : ''; ?>>ft</option>
            </select>
        </div>

        <div class="input-group">
            <i class="fas fa-image"></i>
            <input type="file" name="pictures" id="pictures" accept="image/*" <?php echo isset($_POST['pictures']) ? '' : 'required'; ?>>
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

