<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $linkId = $_POST["id"];
    $otp = $_POST["otp"];

    $stmt = $conn->prepare("SELECT filename, filepath, otp, expiry FROM files WHERE id = ?");
    $stmt->bind_param("i", $linkId);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($filename, $filepath, $storedOtp, $expiry);

    if ($stmt->num_rows === 1 && $stmt->fetch()) {
        if (new DateTime() > new DateTime($expiry)) {
            http_response_code(403);
            exit("Link expired.");
        }

        if ($otp !== $storedOtp) {
            http_response_code(403);
            exit("Invalid OTP.");
        }

        // ✅ Set headers to preserve original file name
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\"");
        header("Content-Length: " . filesize($filepath));
        flush();
        readfile($filepath);
        exit;
    } else {
        http_response_code(404);
        exit("File not found.");
    }
}
?>
