<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('include/config.php');

if (strlen($_SESSION['id']) == 0) {
    header('location:logout.php');
    exit();
}

if (isset($_POST['submit'])) {
    $patname = mysqli_real_escape_string($con, trim($_POST['patname']));
    $patcontact = mysqli_real_escape_string($con, trim($_POST['patcontact']));
    $patemail = mysqli_real_escape_string($con, trim($_POST['patemail']));
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $pataddress = mysqli_real_escape_string($con, trim($_POST['pataddress']));
    $patage = intval($_POST['patage']);
    $medhis = mysqli_real_escape_string($con, trim($_POST['medhis']));
    $defaultPassword = "Patient@123";
    $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $patemail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email already exists! Please use another email.');</script>";
    } else {
        $docid = $_SESSION['id']; // Get doctor ID from session
        // Insert into tblpatient (without Docid)
        $stmt = $con->prepare("INSERT INTO tblpatient (Docid, PatientName, PatientContno, PatientEmail, PatientGender, PatientAdd, PatientAge, PatientMedhis) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssis", $docid, $patname, $patcontact, $patemail, $gender, $pataddress, $patage, $medhis);
        
        $success1 = $stmt->execute();

        // Insert into users table
        $stmt = $con->prepare("INSERT INTO users (fullName, email, password, role) VALUES (?, ?, ?, 'patient')");
        $stmt->bind_param("sss", $patname, $patemail, $hashedPassword);
        $success2 = $stmt->execute();

        if ($success1 && $success2) {
            echo "<script>alert('Patient added successfully! Default Password: $defaultPassword');</script>";
            echo "<script>window.location.href ='manage-patient.php'</script>";
        } else {
            echo "<script>alert('Error adding patient!');</script>";
        }
    }
    $stmt->close();
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Doctor | Add Patient</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <script>
    function userAvailability() {
        $("#loaderIcon").show();
        jQuery.ajax({
            url: "check_availability.php",
            data: 'email=' + $("#patemail").val(),
            type: "POST",
            success: function(data) {
                $("#user-availability-status1").html(data);
                $("#loaderIcon").hide();
            }
        });
    }
    </script>
</head>
<body>
    <div id="app">
        
        <div class="app-content">
            <?php include('include/header.php');?>
            <div class="main-content">
                <div class="container-fluid container-fullw bg-white">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="panel-title">Add Patient</h5>
                            <form role="form" method="post">
                                <div class="form-group">
                                    <label>Patient Name</label>
                                    <input type="text" name="patname" class="form-control" placeholder="Enter Patient Name" required>
                                </div>
                                <div class="form-group">
                                    <label>Patient Contact no</label>
                                    <input type="text" name="patcontact" class="form-control" placeholder="Enter Contact No" required maxlength="10" pattern="[0-9]+">
                                </div>
                                <div class="form-group">
                                    <label>Patient Email</label>
                                    <input type="email" id="patemail" name="patemail" class="form-control" placeholder="Enter Email" required onBlur="userAvailability()">
                                    <span id="user-availability-status1" style="font-size:12px;"></span>
                                </div>
                                <div class="form-group">
                                    <label>Gender</label><br>
                                    <input type="radio" name="gender" value="Female" required> Female
                                    <input type="radio" name="gender" value="Male" required> Male
                                </div>
                                <div class="form-group">
                                    <label>Patient Address</label>
                                    <textarea name="pataddress" class="form-control" placeholder="Enter Address" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Patient Age</label>
                                    <input type="text" name="patage" class="form-control" placeholder="Enter Age" required pattern="[0-9]+">
                                </div>
                                <div class="form-group">
                                    <label>Medical History</label>
                                    <textarea name="medhis" class="form-control" placeholder="Enter Medical History"></textarea>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary">Add</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      
    </div>
</body>
</html>