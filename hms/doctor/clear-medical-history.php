<?php
session_start();
include('include/config.php');

// Check if the user is logged in
if (!isset($_SESSION['id']) || strlen($_SESSION['id']) == 0) {
    header('location:logout.php');
    exit();
}

// Validate patient ID from URL
if (isset($_GET['viewid']) && !empty($_GET['viewid'])) {
    $patientID = trim($_GET['viewid']);

    // Check if Patient ID exists in tblpatient before deleting
    $checkQuery = $con->prepare("SELECT id FROM tblpatient WHERE id = ?");
    $checkQuery->bind_param("i", $patientID);  // Changed "s" to "i"
    $checkQuery->execute();
    $result = $checkQuery->get_result();

    if ($result->num_rows == 0) {
        echo '<script>alert("Invalid patient ID."); window.location.href = "manage-patients.php";</script>';
        exit();
    }

    // **Corrected Delete Query**
    $query = $con->prepare("DELETE FROM tblmedicalhistory WHERE PatientID = ?"); // Changed QRCodePath to PatientID
    $query->bind_param("i", $patientID);

    if ($query->execute()) {
        if ($query->affected_rows > 0) {
            echo '<script>alert("Medical history cleared successfully.");</script>';
        } else {
            echo '<script>alert("No medical history found for this patient.");</script>';
        }
    } else {
        error_log("Error deleting medical history: " . $con->error);
        echo '<script>alert("An error occurred while clearing medical history. Please try again.");</script>';
    }

    // Redirect back to patient's page
    echo "<script>window.location.href = 'view-patient.php?viewid=$patientID';</script>";
    exit();
} else {
    echo '<script>alert("Invalid request."); window.location.href = "manage-patients.php";</script>';
    exit();
}
?>
