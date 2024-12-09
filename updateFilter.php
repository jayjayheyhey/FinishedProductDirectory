<?php 
include 'connect.php';

if(isset($_POST['updateButton'])){
    // Retrieve the form data
    $oemCode = $_POST['code'];
    $partNumber = $_POST['pName'];
    $productName = $_POST['name'];
    $description = $_POST['description'];

    // Handle file upload (assuming `pictures` is the file input name)
    $targetDir = "uploads/"; // Define your upload directory
    $targetFile = $targetDir . basename($_FILES["pictures"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if file is a real image
    if (isset($_FILES["pictures"]["tmp_name"]) && !empty($_FILES["pictures"]["tmp_name"])) {
        $check = getimagesize($_FILES["pictures"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo '<script>
                alert("File is not an image.");
                window.history.back();
            </script>';
            exit;
        }
    }

    // Allow certain file formats
    if ($imageFileType !== "jpg" && $imageFileType !== "png" && $imageFileType !== "jpeg" && $imageFileType !== "gif") {
        echo '<script>
            alert("Only JPG, JPEG, PNG & GIF files are allowed.");
            window.history.back();
        </script>';
        exit;
    }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo '<script>
                alert("Your file was not uploaded.");
                window.history.back();
            </script>';
            exit;
        } else {
            if (move_uploaded_file($_FILES["pictures"]["tmp_name"], $targetFile)) {
                // File uploaded successfully
            } else {
                echo '<script>
                    alert("There was an error uploading your file.");
                    window.history.back();
                </script>';
                exit;
            }
        }

    // Update query
    $updateQuery = "UPDATE finished 
                    SET partNumber = ?, 
                        name = ?, 
                        description = ?, 
                        pictures = ?
                    WHERE oemCode = ?";

    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssss", $partNumber, $productName, $description, $targetFile, $oemCode);

    if($stmt->execute()){
        echo '<script>
            alert("Filter successfully updated.");
            window.location.href = "dashboard.php";
        </script>';
    } else {
        echo '<script>
            alert("Error updating the filter: ' . $conn->error . '");
            window.history.back();
        </script>';
    }

    $stmt->close();
}
?>
