<?php
session_start();
error_reporting(0);
include('include/config.php');
include('include/checklogin.php');
check_login();

// Include the QR code library
include('phpqrcode/qrlib.php');

$vid = $_GET['viewid']; // Patient ID

// Fetch patient name
$patientQuery = mysqli_query($con, "SELECT PatientName FROM tblpatient WHERE ID='$vid'");
$patientData = mysqli_fetch_array($patientQuery);
$patientName = $patientData['PatientName'];

// Medicine prices (can be fetched from a database for dynamic pricing)
$medicinePrices = [
    "TabletA" => 10,
    "TabletB" => 20,
    "TabletC" => 10,
    "TabletD" => 20,
];

// Check if AJAX request for QR code generation is received
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_qr_ajax'])) {
    $medicines = $_POST['medicineName'];
    $quantities = $_POST['quantity'];

    // Ensure 'qrcodes' directory exists and is writable
    if (!is_dir('qrcodes')) {
        mkdir('qrcodes', 0777, true);
    }

    // Initialize array to hold all tablet quantities
    $allQuantities = [
        'TabletA' => 0,
        'TabletB' => 0,
        'TabletC' => 0,
        'TabletD' => 0,
    ];

    // Update quantities based on the posted data
    foreach ($medicines as $index => $medicine) {
        if (array_key_exists($medicine, $allQuantities)) {
            $allQuantities[$medicine] = $quantities[$index];
        }
    }

    // Prepare the QR code content with all 4 tablets
    $qrContent = '';
    foreach ($allQuantities as $medicine => $quantity) {
        $qrContent .= $medicine . $quantity; // Add medicine and its quantity
        $qrContent .= ':'; // Separate entries with colon
    }
    $qrContent = rtrim($qrContent, ':'); // Remove trailing colon

    // Generate QR code filename
    $qrFileName = 'qrcodes/qr_patient_' . $vid . '_' . time() . '.png';

    // Generate and save the QR code
    QRcode::png($qrContent, $qrFileName, QR_ECLEVEL_L, 4);

    // Return the path of the QR code as a response
    echo json_encode(['success' => true, 'qrFileName' => $qrFileName]);
    exit;
}

// Check if QR has been scanned and erase it after the first scan
if (isset($_GET['scan_qr']) && $_GET['scan_qr'] == 'true') {
    // Remove the QR code file after scanning
    $qrFileName = $_GET['qrFileName'];
    if (file_exists($qrFileName)) {
        unlink($qrFileName);
    }

    // Redirect to prevent scanning again
    header("Location: medical-history.php?viewid=" . $vid);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Users | Medical History</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/fontawesome/css/font-awesome.min.css" rel="stylesheet">
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
                            <h1 class="mainTitle">Users | Medical History</h1>
                        </div>
                        <ol class="breadcrumb">
                            <li><span>Users</span></li>
                            <li class="active"><span>Medical History</span></li>
                        </ol>
                    </div>
                </section>

                <div class="container-fluid container-fullw bg-white">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="over-title margin-bottom-15">Users <span class="text-bold">Medical History</span></h5>

                            <!-- Display Patient Name -->
                            <h5><strong>Patient Name:</strong> <?php echo htmlspecialchars($patientName); ?></h5>

                            <!-- Display QR Code if generated -->
                            <div id="qrCodeDisplay" style="display: none;">
                                <h5>QR Code:</h5>
                                <img id="qrImage" src="" alt="QR Code" style="width: 100px; height: 100px; cursor: pointer;" onclick="openQrModal()" />
                                <br><br>
                            </div>

                            <!-- QR Code Modal -->
                            <div id="qrModal" class="modal" style="display: none;">
                                <div class="modal-content">
                                    <span class="close" onclick="closeQrModal()">&times;</span>
                                    <img id="modalQrImage" src="" alt="QR Code" style="width: 300px; height: 300px;" />
                                </div>
                            </div>

                            <h5>Generate QR Code for Prescription:</h5>
                            <form id="qrForm">
                                <table class="table table-bordered">
                                    <tr align="center">
                                        <th colspan="9">Medical History</th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Medical Prescription</th>
                                        <th>Quantity</th>
                                        <th>Time Suggested</th>
                                        <th>Visit Date</th>
                                        <th>Amount</th>
                                    </tr>
                                    <?php
                                    $ret = mysqli_query($con, "SELECT mh.*, mh.MedicineTime, p.PatientName FROM tblmedicalhistory mh JOIN tblpatient p ON mh.PatientID = p.ID WHERE mh.PatientID='$vid'");
                                    $cnt = 1;
                                    $totalAmount = 0;
                                    while ($row = mysqli_fetch_array($ret)) {
                                        $medicineName = $row['MedicalPres'];
                                        $unitPrice = isset($medicinePrices[$medicineName]) ? $medicinePrices[$medicineName] : 0;
                                        $quantity = 0; // Default quantity set to 0
                                        $amount = $unitPrice * $quantity;
                                        $totalAmount += $amount;
                                    ?>
                                    <tr>
                                        <td><?php echo $cnt; ?></td>
                                        <td>
                                            <input type="hidden" name="medicineName[]" value="<?php echo $medicineName; ?>" />
                                            <?php echo $medicineName; ?>
                                        </td>
                                        <td>
                                            <select name="quantity[]" class="quantity" data-unitprice="<?php echo $unitPrice; ?>" required>
                                                <option value="0">0</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                        </td>
                                        <td><?php echo $row['MedicineTime']; ?></td>
                                        <td><?php echo $row['CreationDate']; ?></td>
                                        <td class="amount"><?php echo $amount; ?></td>
                                    </tr>
                                    <?php 
                                    $cnt++; 
                                    } 
                                    ?>
                                    <tr>
                                        <td colspan="5" align="right"><strong>Total Amount:</strong></td>
                                        <td id="totalAmount"><?php echo $totalAmount; ?></td>
                                    </tr>
                                </table>
                                <button type="button" id="razorpayButton" class="btn btn-primary" disabled>Pay with Razorpay</button>
                            </form>

                            <div id="generatePrescriptionButton" style="display: none;">
                                <button type="submit" name="generate_qr" class="btn btn-success" onclick="generateQrCode()">Generate Prescription QR</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('include/footer.php'); ?>
    <?php include('include/setting.php'); ?>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        $('.quantity').on('change', function () {
            var unitPrice = $(this).data('unitprice');
            var quantity = $(this).val();
            var amount = unitPrice * quantity;

            $(this).closest('tr').find('.amount').text(amount);

            var totalAmount = 0;
            $('.amount').each(function () {
                totalAmount += parseFloat($(this).text()) || 0;
            });
            $('#totalAmount').text(totalAmount);

            var allQuantitySelected = true;
            $('.quantity').each(function () {
                if ($(this).val() === "") {
                    allQuantitySelected = false;
                }
            });

            if (allQuantitySelected) {
                $('#razorpayButton').prop('disabled', false);
            } else {
                $('#razorpayButton').prop('disabled', true);
            }
        });

        $('#razorpayButton').on('click', function () {
            var totalAmount = $('#totalAmount').text();
            var options = {
                "key": "rzp_test_qxvKzjO5BvrV30",
                "amount": totalAmount * 100,
                "currency": "INR",
                "name": "medStation",
                "description": "Prescription Payment",
                "image": "logo.png",
                "handler": function (response) {
                    alert("Payment Successful. Now generating QR Code...");
                    $('#generatePrescriptionButton').show();
                },
                "prefill": {
                    "name": "<?php echo $patientName; ?>",
                    "email": "patient@example.com"
                },
                "notes": {
                    "patient_id": "<?php echo $vid; ?>"
                },
                "theme": {
                    "color": "#F37254"
                }
            };
            var rzp = new Razorpay(options);
            rzp.open();
        });
    });

    function generateQrCode() {
        var medicines = [];
        var quantities = [];
        $('input[name="medicineName[]"]').each(function () {
            medicines.push($(this).val());
        });
        $('select[name="quantity[]"]').each(function () {
            quantities.push($(this).val());
        });

        $.ajax({
            url: '',
            type: 'POST',
            data: {
                generate_qr_ajax: true,
                medicineName: medicines,
                quantity: quantities
            },
            success: function (response) {
                var result = JSON.parse(response);
                if (result.success) {
                    $('#qrCodeDisplay').show();
                    $('#qrImage').attr('src', result.qrFileName);
                    $('#modalQrImage').attr('src', result.qrFileName);
                } else {
                    alert('Failed to generate QR code.');
                }
            }
        });
    }

    function openQrModal() {
        var qrImage = $('#qrImage').attr('src');
        $('#modalQrImage').attr('src', qrImage);
        $('#qrModal').show();
    }

    function closeQrModal() {
        $('#qrModal').hide();
    }
</script>

</body>
</html>
