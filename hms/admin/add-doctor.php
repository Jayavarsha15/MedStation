<?php
session_start();
error_reporting(0);
include('include/config.php');

if(strlen($_SESSION['id'])==0) {
    header('location:logout.php');
} else {

    if(isset($_POST['submit'])) {
        $specilization = $_POST['Doctorspecialization'];
        $doctorName = $_POST['docname'];
        $address = $_POST['clinicaddress'];
        $docFees = $_POST['docfees'];
        $contactno = $_POST['doccontact'];
        $docEmail = $_POST['docemail'];
        $password = md5($_POST['npass']);

        $sql = mysqli_query($con, "INSERT INTO doctors (specilization, doctorName, address, docFees, contactno, docEmail, password, role) 
        VALUES ('$specilization', '$doctorName', '$address', '$docFees', '$contactno', '$docEmail', '$password', 'Doctor')");

        if($sql) {
            echo "<script>alert('Doctor info added Successfully');</script>";
            echo "<script>window.location.href ='manage-doctors.php'</script>";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Add Doctor</title>
    <link href="http://fonts.googleapis.com/css?family=Lato|Raleway|Crete+Round" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <script type="text/javascript">
    function valid() {
        if(document.adddoc.npass.value != document.adddoc.cfpass.value) {
            alert("Password and Confirm Password Field do not match!");
            document.adddoc.cfpass.focus();
            return false;
        }
        return true;
    }

    function checkemailAvailability() {
        $("#loaderIcon").show();
        jQuery.ajax({
            url: "check_availability.php",
            data:'emailid='+$("#docemail").val(),
            type: "POST",
            success:function(data){
                $("#email-availability-status").html(data);
                $("#loaderIcon").hide();
            },
            error:function (){}
        });
    }
    </script>
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
                            <h1 class="mainTitle">Admin | Add Doctor</h1>
                        </div>
                        <ol class="breadcrumb">
                            <li><span>Admin</span></li>
                            <li class="active"><span>Add Doctor</span></li>
                        </ol>
                    </div>
                </section>
                <div class="container-fluid container-fullw bg-white">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row margin-top-30">
                                <div class="col-lg-8 col-md-12">
                                    <div class="panel panel-white">
                                        <div class="panel-heading">
                                            <h5 class="panel-title">Add Doctor</h5>
                                        </div>
                                        <div class="panel-body">
                                            <form role="form" name="adddoc" method="post" onSubmit="return valid();">
                                                <div class="form-group">
                                                    <label for="DoctorSpecialization">Doctor Specialization</label>
                                                    <select name="Doctorspecialization" class="form-control" required>
                                                        <option value="">Select Specialization</option>
                                                        <?php 
                                                        $ret = mysqli_query($con, "select * from doctorspecilization");
                                                        while($row = mysqli_fetch_array($ret)) {
                                                        ?>
                                                        <option value="<?php echo htmlentities($row['specilization']); ?>">
                                                            <?php echo htmlentities($row['specilization']); ?>
                                                        </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="docname">Doctor Name</label>
                                                    <input type="text" name="docname" class="form-control" placeholder="Enter Doctor Name" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="clinicaddress">Doctor Clinic Address</label>
                                                    <textarea name="clinicaddress" class="form-control" placeholder="Enter Doctor Clinic Address" required></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label for="docfees">Doctor Consultancy Fees</label>
                                                    <input type="text" name="docfees" class="form-control" placeholder="Enter Doctor Consultancy Fees" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="doccontact">Doctor Contact No</label>
                                                    <input type="text" name="doccontact" class="form-control" placeholder="Enter Doctor Contact No" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="docemail">Doctor Email</label>
                                                    <input type="email" id="docemail" name="docemail" class="form-control" placeholder="Enter Doctor Email id" required onBlur="checkemailAvailability()">
                                                    <span id="email-availability-status"></span>
                                                </div>

                                                <div class="form-group">
                                                    <label for="npass">Password</label>
                                                    <input type="password" name="npass" class="form-control" placeholder="New Password" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="cfpass">Confirm Password</label>
                                                    <input type="password" name="cfpass" class="form-control" placeholder="Confirm Password" required>
                                                </div>

                                                <button type="submit" name="submit" class="btn btn-o btn-primary">Submit</button>
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
        <?php include('include/footer.php');?>
        <?php include('include/setting.php');?>
    </div>
</div>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
<?php 
} 
?>
