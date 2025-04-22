<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('include/config.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prescription'])) {
    $prescription = mysqli_real_escape_string($con, $_POST['prescription']);
    $query = "SELECT quantityavailable FROM tblmedicine_stock WHERE medicinename = '$prescription'";
    $result = mysqli_query($con, $query);

    $data = [];

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $data['quantity'] = $row['quantityavailable'];
    } else {
        $data['quantity'] = '';
    }

    echo json_encode($data);
    exit(); 
}

if (strlen($_SESSION['id']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_POST['submit'])) {
        $vid = $_GET['viewid'];
        $MedicalPres = $_POST['pres']; // Selected tablet name
        $MedicineQuantity = $_POST['medicineQuantity']; // Medicine quantity
        $MedicineTime = $_POST['medicineTime']; // Medicine time
        $QRCodePath = ''; // If needed, update it with actual QR code path
        $MaxQuantity = 10; // Default max quantity
        $Quantity = $MedicineQuantity; 
        $MedTime = $MedicineTime;
        $qr_scanned = 0; // Default value

        $query = "SELECT quantityavailable FROM tblmedicine_stock WHERE medicinename = '$MedicalPres'";
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);
        if($row['quantityavailable']< $MedicineQuantity)
        {
            echo '<script>alert("Please enter a value under available medicine quantity: ' . $row['quantityavailable'] . '");</script>';
            exit();
        }
       $stmt = $con->prepare("INSERT INTO tblmedicalhistory (PatientID, MedicalPres, QRCodePath, MaxQuantity, Quantity, MedicineQuantity, MedicineTime, qr_scanned) 
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
       
       $stmt->bind_param("issiiisi", $vid, $MedicalPres, $QRCodePath, $MaxQuantity, $Quantity, $MedicineQuantity, $MedicineTime, $qr_scanned);
       

        if ($stmt->execute()) {
            echo '<script>alert("Medical history has been added successfully.")</script>';
            echo "<script>window.location.href ='view-patient.php?viewid=$vid'</script>";
        } else {
            echo '<script>alert("Something went wrong. Please try again.")</script>';
        }
        $stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Doctor | Manage Patients</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="vendor/themify-icons/themify-icons.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div id="app">		
        <?php include('include/sidebar.php'); ?>
        <div class="app-content">
            <?php include('include/header.php'); ?>
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
                                $vid = $_GET['viewid'];
                                $ret = mysqli_query($con, "SELECT * FROM tblpatient WHERE id='$vid'");
                                while ($row = mysqli_fetch_array($ret)) {
                                ?>
                                <table border="1" class="table table-bordered">
                                    <tr align="center">
                                        <td colspan="4" style="font-size:20px;color:blue">Patient Details</td>
                                    </tr>
                                    <tr>
                                        <th>Patient Name</th>
                                        <td><?php echo $row['PatientName']; ?></td>
                                        <th>Patient Email</th>
                                        <td><?php echo $row['PatientEmail']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Patient Mobile Number</th>
                                        <td><?php echo $row['PatientContno']; ?></td>
                                        <th>Patient Address</th>
                                        <td><?php echo $row['PatientAdd']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Patient Gender</th>
                                        <td><?php echo $row['PatientGender']; ?></td>
                                        <th>Patient Age</th>
                                        <td><?php echo $row['PatientAge']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Patient Medical History (if any)</th>
                                        <td><?php echo $row['PatientMedhis']; ?></td>
                                        <th>Patient Reg Date</th>
                                        <td><?php echo $row['CreationDate']; ?></td>
                                    </tr>
                                </table>
                                <?php } ?>
                                
                                <?php  
                               $ret = mysqli_query($con, "SELECT * FROM tblmedicalhistory WHERE PatientID='$vid'");

                                ?>
                                <div style="text-align: right; margin-bottom: 10px;">
                                    <a href="clear-medical-history.php?viewid=<?php echo $vid; ?>" class="btn btn-danger">Clear Medical History</a>
                                </div>
                                <table class="table table-bordered">
                                    <tr align="center">
                                        <th colspan="5">Medical History</th> 
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Medical Prescription</th>
                                        <th>Medicine Quantity</th>
                                        <th>Medicine Time</th>
                                        <th>Visit Date</th>
                                    </tr>
                                    <?php  
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_array($ret)) { 
                                    ?>
                                    <tr>
                                        <td><?php echo $cnt; ?></td>
                                        <td><?php echo $row['MedicalPres']; ?></td>
                                        <td><?php echo $row['MedicineQuantity']; ?></td>
                                        <td><?php echo $row['MedicineTime']; ?></td>
                                        <td><?php echo $row['CreationDate']; ?></td> 
                                    </tr>
                                    <?php $cnt++; } ?>
                                </table>

                                <p align="center">                            
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">Add Medical History</button>
                                </p>  

                                <div class="modal fade" id="myModal" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Add Medical History</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th>Medical Prescription :</th>
                                                            <td>
                                                                <select name="pres" class="form-control" required>
                                                                    <option value="">Select Tablet</option>
                                                                    <option value="TabletA">TabletA</option>
                                                                    <option value="TabletB">TabletB</option>
                                                                    <option value="TabletC">TabletC</option>
                                                                    <option value="TabletD">TabletD</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Medicine Quantity :</th>
                                                            <td>
                                                                <input id="medicineQuantity" name="medicineQuantity" type="number" class="form-control" required>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Medicine Time :</th>
                                                            <td>
                                                                <input name="medicineTime" type="text" class="form-control" required>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" align="center">
                                                                <button type="submit" name="submit" class="btn btn-primary">Add</button>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/main.js"></script>
<script>
$(document).ready(function () {
    $('select[name="pres"]').on('change', function () {
        var selectedPrescription = $(this).val();

        if (selectedPrescription !== '') {
            $.ajax({
                url: '',
                type: 'POST',
                data: { prescription: selectedPrescription },
                success: function (response) {
                    try {
                        var data = JSON.parse(response);
                        if (data.quantity !== undefined) {
                            $('#medicineQuantity').val(data.quantity);
                        } else {
                            $('#medicineQuantity').val('');
                        }
                    } catch (e) {
                        console.error('Invalid JSON:', response);
                        $('#medicineQuantity').val('');
                    }
                },
                error: function () {
                    console.error('AJAX error');
                    $('#medicineQuantity').val('');
                }
            });
        } else {
            $('#medicineQuantity').val('');
        }
    });
});
</script>
</body>
</html>

<?php } ?>
