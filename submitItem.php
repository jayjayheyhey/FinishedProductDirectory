<?php 

include 'connect.php';

if(isset($_POST['submitButton'])){
    $FilterCode=$_POST['fCode'];
    $PartNumber=$_POST['pName'];
    $FilterName=$_POST['fName'];
    $Materials=$_POST['materials'];
    $Quantity=$_POST['quantity'];
    $MaxStock=$_POST['maxStock'];
    $LowStockSignal=$_POST['lowStock'];


     $checkCode="SELECT * From filters where FilterCode='$FilterCode'";
     $result=$conn->query($checkCode);
     if($result->num_rows>0){
        echo '<script>
                    alert("ERROR: Filter Code Already Exists.");
                    window.location.href = "addInterface.php";
                </script>';
     } else{
        if ($Quantity > $MaxStock) {
            echo '<script>
                    alert("ERROR: Quantity can not be larger than the maximum stock.");
                    window.location.href = "addInterface.php";
                </script>';
        } elseif ($Quantity < 0) {
            echo '<script>
                    alert("ERROR: Quantity can not be lower than 0.");
                    window.location.href = "addInterface.php";
                </script>';
        } else{
            $insertQuery="INSERT INTO filters(FilterCode,PartNumber,FilterName,Materials,Quantity,MaxStock,LowStockSignal)
                        VALUES ('$FilterCode','$PartNumber','$FilterName','$Materials','$Quantity','$MaxStock','$LowStockSignal')";
                if($conn->query($insertQuery)==TRUE){
                    echo '<script>
                            alert("Filter successfully updated");
                            window.location.href = "homepage.php";
                        </script>';
                    exit();
                }   
                else{
                    echo "Error:".$conn->error;
                }
        }
    }
}

?>