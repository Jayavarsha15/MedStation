<?php
// Include the QR code library
include('phpqrcode/qrlib.php');

// Test string for the QR code content
$qrContent = "This is a test QR code";

// Path to save the generated QR code image
$qrFileName = 'qrcodes/test_qr_code.png';

// Ensure 'qrcodes' directory exists and is writable
if (is_writable('qrcodes')) {
    // Generate and save the QR code
    QRcode::png($qrContent, $qrFileName, QR_ECLEVEL_L, 4);

    echo "QR Code generated and saved successfully! <br>";
    echo "<img src='$qrFileName' alt='QR Code'>";
} else {
    echo "The 'qrcodes' folder is not writable.";
}
?>
