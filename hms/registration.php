<?php
include_once('include/config.php');

if(isset($_POST['submit']))
{
    $fname = mysqli_real_escape_string($con, $_POST['full_name']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $city = mysqli_real_escape_string($con, $_POST['city']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure password hashing

    // Check if email already exists in 'users'
    $check_email = mysqli_query($con, "SELECT id FROM users WHERE email='$email'");
    if(mysqli_num_rows($check_email) > 0) {
        echo "<script>alert('Email already registered. Please use a different email.');</script>";
    } else {
        // Generate Unique Patient ID
        $result = mysqli_query($con, "SELECT PatientID FROM tblpatient ORDER BY id DESC LIMIT 1");
        $lastID = mysqli_fetch_assoc($result);
        if ($lastID) {
            $num = (int) substr($lastID['PatientID'], 4) + 1;
            $PatientID = "PTMS" . str_pad($num, 3, "0", STR_PAD_LEFT);
        } else {
            $PatientID = "PTMS001"; // First patient
        }

        // Insert into users table
        $stmt = $con->prepare("INSERT INTO users (fullname, address, city, gender, email, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $fname, $address, $city, $gender, $email, $password);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id; // Get the last inserted User ID
            $stmt->close();

            // Insert into tblpatient table
            $stmt_patient = $con->prepare("INSERT INTO tblpatient (PatientName, PatientContno, PatientEmail, PatientGender, PatientAge, PatientID, UserID) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $contact = ""; // Add contact number input if available
            $age = 0; // Add age input if available
            $stmt_patient->bind_param("ssssisi", $fname, $contact, $email, $gender, $age, $PatientID, $user_id);

            if ($stmt_patient->execute()) {
                echo "<script>alert('Successfully Registered. You can login now');</script>";
            } else {
                echo "<script>alert('Error inserting into tblpatient: " . mysqli_error($con) . "');</script>";
            }
            $stmt_patient->close();
        } else {
            echo "<script>alert('Registration failed. Please try again.');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Registration</title>
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="vendor/themify-icons/themify-icons.min.css">
    <link href="vendor/animate.css/animate.min.css" rel="stylesheet" media="screen">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.min.css" rel="stylesheet" media="screen">
    <link href="vendor/switchery/switchery.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/plugins.css">
    <link rel="stylesheet" href="assets/css/themes/theme-1.css" id="skin_color" />

    <script type="text/javascript">
    function valid() {
        if (document.registration.password.value != document.registration.password_again.value) {
            alert("Password and Confirm Password do not match!");
            document.registration.password_again.focus();
            return false;
        }
        return true;
    }

    function userAvailability() {
        $("#loaderIcon").show();
        jQuery.ajax({
            url: "check_availability.php",
            data: 'email=' + $("#email").val(),
            type: "POST",
            success: function (data) {
                $("#user-availability-status1").html(data);
                $("#loaderIcon").hide();
            },
            error: function () {}
        });
    }
    </script>
</head>

<body class="login">
    <div class="row">
        <div class="main-login col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
            <div class="logo margin-top-30">
                <a href="../index.php"><h2>MedStation | Patient Registration</h2></a>
            </div>
            <div class="box-register">
                <form name="registration" id="registration" method="post" onSubmit="return valid();">
                    <fieldset>
                        <legend>Sign Up</legend>
                        <p>Enter your personal details below:</p>
                        <div class="form-group">
                            <input type="text" class="form-control" name="full_name" placeholder="Full Name" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="address" placeholder="Address" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="city" placeholder="City" required>
                        </div>
                        <div class="form-group">
                            <label class="block">Gender</label>
                            <div class="clip-radio radio-primary">
                                <input type="radio" id="rg-female" name="gender" value="female" required>
                                <label for="rg-female">Female</label>
                                <input type="radio" id="rg-male" name="gender" value="male" required>
                                <label for="rg-male">Male</label>
                            </div>
                        </div>
                        <p>Enter your account details below:</p>
                        <div class="form-group">
                            <span class="input-icon">
                                <input type="email" class="form-control" name="email" id="email" onBlur="userAvailability()" placeholder="Email" required>
                                <i class="fa fa-envelope"></i>
                            </span>
                            <span id="user-availability-status1" style="font-size:12px;"></span>
                        </div>
                        <div class="form-group">
                            <span class="input-icon">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                <i class="fa fa-lock"></i>
                            </span>
                        </div>
                        <div class="form-group">
                            <span class="input-icon">
                                <input type="password" class="form-control" id="password_again" name="password_again" placeholder="Password Again" required>
                                <i class="fa fa-lock"></i>
                            </span>
                        </div>
                        <div class="form-group">
                            <div class="checkbox clip-check check-primary">
                                <input type="checkbox" id="agree" value="agree" checked="true" readonly="true">
                                <label for="agree">I agree</label>
                            </div>
                        </div>
                        <div class="form-actions">
                            <p>Already have an account?
                                <a href="user-login.php">Log-in</a>
                            </p>
                            <button type="submit" class="btn btn-primary pull-right" id="submit" name="submit">
                                Submit <i class="fa fa-arrow-circle-right"></i>
                            </button>
                        </div>
                    </fieldset>
                </form>
                <div class="copyright">
                    &copy; <span class="current-year"></span><span class="text-bold text-uppercase"> HMS</span>. <span>All rights reserved</span>
                </div>
            </div>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/modernizr/modernizr.js"></script>
    <script src="vendor/jquery-cookie/jquery.cookie.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="vendor/switchery/switchery.min.js"></script>
    <script src="vendor/jquery-validation/jquery.validate.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
