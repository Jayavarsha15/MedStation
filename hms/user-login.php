<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("include/config.php");

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);    
    $password = $_POST['password'];

    // Prepared Statement to prevent SQL Injection
    $stmt = $con->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $storedPassword = $user['password'];

        // Check if password matches (handle both password_hash() and MD5)
        if (password_verify($password, $storedPassword)) {
            $_SESSION['login'] = $email;
            $_SESSION['id'] = $user['id'];

            // Logging successful login
            $uip = $_SERVER['REMOTE_ADDR'];
            $status = 1;
            $log_stmt = $con->prepare("INSERT INTO userlog (username, userip, status) VALUES (?, ?, ?)");
            $log_stmt->bind_param("ssi", $email, $uip, $status);
            $log_stmt->execute();

            header("location: dashboard.php");
            exit();
        } elseif (md5($password) === $storedPassword) {
            // If old MD5 password is used, update it to password_hash()
            $new_hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_stmt = $con->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update_stmt->bind_param("ss", $new_hashed_password, $email);
            $update_stmt->execute();

            $_SESSION['login'] = $email;
            $_SESSION['id'] = $user['id'];

            header("location: dashboard.php");
            exit();
        } else {
            $errorMessage = "Invalid username or password";
        }
    } else {
        $errorMessage = "Invalid username or password";
    }

    // Logging failed login attempt
    $uip = $_SERVER['REMOTE_ADDR'];
    $status = 0;
    $log_stmt = $con->prepare("INSERT INTO userlog (username, userip, status) VALUES (?, ?, ?)");
    $log_stmt->bind_param("ssi", $email, $uip, $status);
    $log_stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User-Login</title>
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="login">
    <div class="row">
        <div class="main-login col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
            <div class="logo margin-top-30">
                <a href="../index.php"><h2> MedStation | Patient Login</h2></a>
            </div>

            <div class="box-login">
                <form class="form-login" method="post">
                    <fieldset>
                        <legend>Sign in to your account</legend>
                        <p>
                            Please enter your email and password to log in.<br />
                            <span style="color:red;">
                                <?php echo isset($errorMessage) ? $errorMessage : ''; ?>
                            </span>
                        </p>
                        <div class="form-group">
                            <span class="input-icon">
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                                <i class="fa fa-user"></i> 
                            </span>
                        </div>
                        <div class="form-group form-actions">
                            <span class="input-icon">
                                <input type="password" class="form-control" name="password" placeholder="Password" required>
                                <i class="fa fa-lock"></i>
                            </span>
                            <a href="forgot-password.php">Forgot Password?</a>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary pull-right" name="submit">
                                Login <i class="fa fa-arrow-circle-right"></i>
                            </button>
                        </div>
                        
                    </fieldset>
                </form>

                <div class="copyright">
                    <span class="text-bold text-uppercase"> MedStation</span>.
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
