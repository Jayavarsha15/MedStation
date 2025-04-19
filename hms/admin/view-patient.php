<?php
session_start();
error_reporting(0);
include('include/config.php');
if(strlen($_SESSION['id']==0)) {
 header('location:logout.php');
  } else{
if(isset($_POST['submit'])) {
    
    $vid=$_GET['viewid'];
    $bp=$_POST['bp'];
    $bs=$_POST['bs'];
    $weight=$_POST['weight'];
    $temp=$_POST['temp'];
    $pres=$_POST['pres'];
    $quantity=$_POST['quantity']; // New field for medicine quantity
 
    $query = mysqli_query($con, "INSERT INTO tblmedicalhistory(PatientID,BloodPressure,BloodSugar,Weight,Temperature,MedicalPres,Quantity) VALUES ('$vid','$bp','$bs','$weight','$temp','$pres','$quantity')");
    
    if ($query) {
        echo '<script>alert("Medical history has been added.")</script>';
        echo "<script>window.location.href ='manage-patient.php'</script>";
    } else {
        echo '<script>alert("Something Went Wrong. Please try again")</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Doctor | Manage Patients</title>
    <!-- Include your styles and scripts here -->
</head>
<body>
    <div id="app">		
        <?php include('include/sidebar.php');?>
        <div class="app-content">
            <?php include('include/header.php');?>
            <div class="main-content">
                <div class="wrap-content container" id="container">
                    <section id="page-title">
                        <div class="row">
                            <div class="col-sm-8">
                                <h1 class="mainTitle">Doctor | Manage Patients</h1>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Doctor</span></li>
                                <li class="active"><span>Manage Patients</span></li>
                            </ol>
                        </div>
                    </section>

                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="over-title margin-bottom-15">Manage <span class="text-bold">Patients</span></h5>
                                <?php
                                $vid=$_GET['viewid'];
                                $ret=mysqli_query($con,"SELECT * FROM tblpatient WHERE ID='$vid'");
                                while ($row=mysqli_fetch_array($ret)) {
                                ?>
                                <table border="1" class="table table-bordered">
                                    <tr align="center">
                                        <td colspan="4" style="font-size:20px;color:blue">Patient Details</td>
                                    </tr>
                                    <tr>
                                        <th>Patient Name</th>
                                        <td><?php echo $row['PatientName'];?></td>
                                        <th>Patient Email</th>
                                        <td><?php echo $row['PatientEmail'];?></td>
                                    </tr>
                                    <!-- Additional details here -->
                                </table>
                                <?php } ?>

                                <!-- Form to Add New Medical History -->
                                <form method="post">
                                    <div class="form-group">
                                        <label for="bp">Blood Pressure:</label>
                                        <input type="text" name="bp" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="bs">Blood Sugar:</label>
                                        <input type="text" name="bs" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="weight">Weight:</label>
                                        <input type="text" name="weight" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="temp">Body Temperature:</label>
                                        <input type="text" name="temp" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="pres">Medical Prescription:</label>
                                        <input type="text" name="pres" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="quantity">Quantity:</label> <!-- New Quantity field -->
                                        <input type="number" name="quantity" class="form-control" required>
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-primary">Add Medical History</button>
                                </form>

                                <!-- Displaying Medical History -->
                                <?php
                                $ret=mysqli_query($con,"SELECT * FROM tblmedicalhistory WHERE PatientID='$vid'");
                                ?>
                                <table class="table table-bordered">
                                    <tr align="center">
                                        <th colspan="8">Medical History</th> 
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Blood Pressure</th>
                                        <th>Weight</th>
                                        <th>Blood Sugar</th>
                                        <th>Body Temperature</th>
                                        <th>Medical Prescription</th>
                                        <th>Quantity</th> <!-- Display Quantity -->
                                        <th>Visit Date</th>
                                    </tr>
                                    <?php  
                                    $cnt=1;
                                    while ($row=mysqli_fetch_array($ret)) { 
                                    ?>
                                    <tr>
                                        <td><?php echo $cnt;?></td>
                                        <td><?php echo $row['BloodPressure'];?></td>
                                        <td><?php echo $row['Weight'];?></td>
                                        <td><?php echo $row['BloodSugar'];?></td>
                                        <td><?php echo $row['Temperature'];?></td>
                                        <td><?php echo $row['MedicalPres'];?></td>
                                        <td><?php echo $row['Quantity'];?></td> <!-- Display Quantity -->
                                        <td><?php echo $row['CreationDate'];?></td>
                                    </tr>
                                    <?php $cnt++; } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Include your footer and settings here -->
    </div>
    <!-- Include JavaScript files here -->
</body>
</html>
<?php } ?>
