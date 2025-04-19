<?php
session_start();
error_reporting(0);
include('include/config.php');

if (strlen($_SESSION['id']) == 0) {
    header('location:logout.php');
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Doctor | Manage Patients</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
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
                            <table class="table table-hover" id="patient-table">
                                <thead>
                                    <tr>
                                        <th class="center">#</th>
                                        <th>Patient Name</th>
                                        <th>Contact Number</th>
                                        <th>Gender</th>
                                        <th>Creation Date</th>
                                        <th>Updation Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $id = mysqli_real_escape_string($con, $_SESSION['id']);

                                    // Ensure Docid exists in doctors table
                                    $query = "SELECT tblpatient.* 
                                              FROM tblpatient 
                                              JOIN doctors ON tblpatient.Docid = doctors.id
                                              WHERE tblpatient.Docid = '$id'";

                                    $result = mysqli_query($con, $query);

                                    if (!$result) {
                                        echo "<tr><td colspan='7' class='text-center text-danger'>Error: " . mysqli_error($con) . "</td></tr>";
                                    } elseif (mysqli_num_rows($result) > 0) {
                                        $cnt = 1;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td class='center'>{$cnt}.</td>";
                                            echo "<td>{$row['PatientName']}</td>";
                                            echo "<td>{$row['PatientContno']}</td>";
                                            echo "<td>{$row['PatientGender']}</td>";
                                            echo "<td>{$row['CreationDate']}</td>";
                                            echo "<td>{$row['UpdationDate']}</td>";
                                            echo "<td>
                                                    <a href='edit-patient.php?editid={$row['id']}' class='btn btn-primary btn-sm' target='_blank'>Edit</a>
                                                    <a href='view-patient.php?viewid={$row['id']}' class='btn btn-warning btn-sm' target='_blank'>View</a>
                                                  </td>";
                                            echo "</tr>";
                                            $cnt++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center'>No patients found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('include/footer.php'); ?>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
<?php } ?>
