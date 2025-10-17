<?php
// ตั้งค่าที่อยู่โฟลเดอร์เก็บไฟล์
$targetDir = "uploads/";

// ถ้ายังไม่มีโฟลเดอร์ uploads ให้สร้างขึ้นมา
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// ตรวจสอบว่ามีไฟล์อัปโหลดมาจริงไหม
if (isset($_FILES["file"])) {
    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // กำหนดนามสกุลไฟล์ที่อนุญาตให้อัปได้
    $allowedTypes = array("jpg", "jpeg", "png", "gif", "mp4", "mov", "avi", "webm");

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
            echo "<h3 style='color:lime;'>อัปโหลดเสร็จเรียบร้อย!</h3>";
            echo "<p>ลิงก์ไฟล์ของมึง:</p>";
            echo "<a href='$targetFilePath' target='_blank'>$targetFilePath</a>";
        } else {
            echo "<h3 style='color:red;'>❌ มีปัญหาในการอัปโหลด</h3>";
        }
    } else {
        echo "<h3 style='color:red;'>❌ ประเภทไฟล์นี้ไม่อนุญาต</h3>";
    }
} else {
    echo "<h3 style='color:red;'>⚠️ ไม่มีไฟล์ถูกอัปโหลด</h3>";
}
?>
