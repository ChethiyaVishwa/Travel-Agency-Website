<?php
// Check if admin is logged in
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Define the TCPDF installation directory
$tcpdf_dir = dirname(__DIR__) . '/tcpdf';

// Check if TCPDF is already installed
if (is_dir($tcpdf_dir)) {
    echo "<h2>TCPDF is already installed!</h2>";
    echo "<p>You can now use the PDF download feature.</p>";
    echo "<p><a href='billing.php'>Go back to Billing</a></p>";
    exit;
}

// Create directory
if (!is_dir($tcpdf_dir) && !mkdir($tcpdf_dir, 0755, true)) {
    die('Failed to create TCPDF directory');
}

// URL of the TCPDF ZIP file
$tcpdf_url = 'https://github.com/tecnickcom/TCPDF/archive/refs/tags/6.6.2.zip';
$zip_file = $tcpdf_dir . '/tcpdf.zip';

// Download TCPDF
echo "<h2>Installing TCPDF...</h2>";
echo "<p>Downloading TCPDF...</p>";
$downloaded = file_put_contents($zip_file, file_get_contents($tcpdf_url));

if ($downloaded === false) {
    die('Error downloading TCPDF');
}

// Extract ZIP file
echo "<p>Extracting files...</p>";
$zip = new ZipArchive;
if ($zip->open($zip_file) === TRUE) {
    $zip->extractTo($tcpdf_dir);
    $zip->close();
    
    // Move files from the extracted folder to the main TCPDF folder
    $extracted_dir = $tcpdf_dir . '/TCPDF-6.6.2';
    if (is_dir($extracted_dir)) {
        $files = scandir($extracted_dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                rename($extracted_dir . '/' . $file, $tcpdf_dir . '/' . $file);
            }
        }
        // Remove the extracted directory
        rmdir($extracted_dir);
    }
    
    // Remove the ZIP file
    unlink($zip_file);
    
    echo "<p>TCPDF installed successfully!</p>";
    echo "<p>You can now use the PDF download feature.</p>";
    echo "<p><a href='billing.php'>Go back to Billing</a></p>";
} else {
    die('Error extracting TCPDF');
}
?> 