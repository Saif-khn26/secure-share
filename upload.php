<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

$targetDir = "uploads/";
if (!is_dir($targetDir)) mkdir($targetDir);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file']) && isset($_POST['expire'])) {
    $filename = basename($_FILES["file"]["name"]);
    $targetFile = $targetDir . uniqid() . "_" . $filename;
    $expiryHours = intval($_POST['expire']);
    $expiryTime = date("Y-m-d H:i:s", time() + ($expiryHours * 3600));
    $otp = strval(rand(100000, 999999));  // Generate 6-digit OTP

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO files (filename, filepath, otp, expiry) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $filename, $targetFile, $otp, $expiryTime);
        $stmt->execute();
        $fileId = $conn->insert_id;

        echo json_encode([
            "status" => "success",
            "link" => "download.html?id=$fileId",
            "otp" => $otp,
            "filename" => $filename
          ]);
          
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to upload the file."
        ]);
    }
}
?>
