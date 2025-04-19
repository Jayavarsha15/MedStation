<?php
session_start();
error_reporting(0);
include('include/config.php');
include('include/checklogin.php');
check_login();

if (isset($_POST['submit'])) {
    $vid = $_GET['viewid'];
    $prescriptions = $_POST['prescriptions'];

    foreach ($prescriptions as $prescription) {
        $medicineName = $prescription['medicineName'];
        $quantity = $prescription['quantity'];
        $unitPrice = $prescription['unitPrice'];
        $totalAmount = $quantity * $unitPrice;

        $query = mysqli_query($con, "INSERT INTO tblprescriptiondetails (PatientID, MedicineName, Quantity, UnitPrice, TotalAmount) VALUES ('$vid', '$medicineName', '$quantity', '$unitPrice', '$totalAmount')");

        if (!$query) {
            echo '<script>alert("Something went wrong. Please try again.")</script>';
        }
    }

    echo '<script>alert("Prescription details submitted successfully.")</script>';
    echo "<script>window.location.href ='manage-patient.php'</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Users | Medical History</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <script>
        function calculateAmount(row) {
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unitPrice').value) || 0;
            const totalAmount = quantity * unitPrice;
            row.querySelector('.totalAmount').value = totalAmount.toFixed(2);
        }

        function generateQRCode() {
            const qrData = [];
            document.querySelectorAll('#prescriptionTable tbody tr').forEach(row => {
                const medicineName = row.querySelector('.medicineName').value;
                const quantity = row.querySelector('.quantity').value;
                const totalAmount = row.querySelector('.totalAmount').value;
                qrData.push(`${medicineName}: ${quantity} units, Total: ₹${totalAmount}`);
            });
            alert('QR Code Data:\n' + qrData.join('\n'));
        }
    </script>
</head>
<body>
<div id="app">
<?php include('include/sidebar.php'); ?>
<div class="app-content">
<?php include('include/header.php'); ?>
<div class="main-content">
    <div class="container-fluid bg-white">
        <h1 class="mainTitle">Users | Medical History</h1>
        <div class="row">
            <div class="col-md-12">
                <h5>Prescription Details</h5>
                <form method="post">
                    <table class="table table-bordered" id="prescriptionTable">
                        <thead>
                            <tr>
                                <th>Medicine Name</th>
                                <th>Quantity</th>
                                <th>Unit Price (₹)</th>
                                <th>Total Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $vid = $_GET['viewid'];
                        $query = mysqli_query($con, "SELECT * FROM tblprescriptions WHERE PatientID='$vid'");
                        while ($row = mysqli_fetch_array($query)) {
                            ?>
                            <tr>
                                <td>
                                    <input type="text" name="prescriptions[][medicineName]" class="form-control medicineName" value="<?php echo $row['MedicineName']; ?>" readonly>
                                </td>
                                <td>
                                    <input type="number" name="prescriptions[][quantity]" class="form-control quantity" oninput="calculateAmount(this.closest('tr'))" min="1" required>
                                </td>
                                <td>
                                    <input type="number" name="prescriptions[][unitPrice]" class="form-control unitPrice" oninput="calculateAmount(this.closest('tr'))" value="0" min="0" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control totalAmount" readonly>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" onclick="generateQRCode()">Generate QR Code</button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
</body>
</html>
