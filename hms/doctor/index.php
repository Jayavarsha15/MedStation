<?php
session_start();
include("include/config.php");
error_reporting(0);

if (isset($_POST['submit'])) {
    $uname = mysqli_real_escape_string($con, $_POST['username']);  // Prevent SQL Injection
    $dpassword = md5($_POST['password']);	

    $query = "SELECT * FROM doctors WHERE docEmail='$uname' AND password='$dpassword'";
    $ret = mysqli_query($con, $query);
    $num = mysqli_fetch_array($ret);

    if ($num) {  // If login is successful
        $_SESSION['dlogin'] = $uname;
        $_SESSION['id'] = $num['id'];
        $uid = $num['id'];
        $uip = $_SERVER['REMOTE_ADDR'];
        $status = 1;

        // Insert login log
        mysqli_query($con, "INSERT INTO doctorslog(uid, username, userip, status) VALUES ('$uid', '$uname', '$uip', '$status')");

        echo "<script>window.location.href='dashboard.php';</script>"; 
        exit();
    } else {
        $uip = $_SERVER['REMOTE_ADDR'];
        $status = 0;
        
        // Insert failed login attempt log
        mysqli_query($con, "INSERT INTO doctorslog(username, userip, status) VALUES ('$uname', '$uip', '$status')");
        
        $_SESSION['errmsg'] = "Invalid username or password"; // Store error message
        echo "<script>window.location.href='doctor-login.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Doctor Login</title>
    
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
</head>

<body class="login">
    <div class="row">
        <div class="main-login col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
            <div class="logo margin-top-30">
                <a href="../../index.php"><h2> HMS | Doctor Login</h2></a>
            </div>

            <div class="box-login">
                <form class="form-login" method="post">
                    <fieldset>
                        <legend>Sign in to your account</legend>
                        <p>
                            Please enter your email and password to log in.<br />
                            <span style="color:red;">
                                <?php echo isset($_SESSION['errmsg']) ? $_SESSION['errmsg'] : ''; ?>
                            </span>
                            <?php $_SESSION['errmsg'] = ""; ?> <!-- Clear the error message -->
                        </p>

                        <div class="form-group">
                            <span class="input-icon">
                                <input type="text" class="form-control" name="username" placeholder="Email" required>
                                <i class="fa fa-user"></i> 
                            </span>
                        </div>

                        <div class="form-group form-actions">
                            <span class="input-icon">
                                <input type="password" class="form-control password" name="password" placeholder="Password" required>
                                <i class="fa fa-lock"></i>
                            </span>
                           
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary pull-right" name="submit">
                                Login <i class="fa fa-arrow-circle-right"></i>
                            </button>
                        </div>
                    </fieldset>
                </form>

               
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
    <script src="assets/js/login.js"></script>

    <script>
        jQuery(document).ready(function() {
            Main.init();
            Login.init();
        });
    </script>

</body>
</html>
