<?php
// upload.php - เวอร์ชันย่อ
$UPLOAD_DIR = __DIR__ . '/uploads';
$META_FILE = __DIR__ . '/uploads.json';
$MAX_SIZE = 200 * 1024 * 1024;
$ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/avi', 'video/mov'];

// สร้างโฟลเดอร์หากไม่มี
if (!is_dir($UPLOAD_DIR)) mkdir($UPLOAD_DIR, 0755, true);
if (!file_exists($META_FILE)) file_put_contents($META_FILE, '[]');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบข้อผิดพลาด
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        die('Upload error: ' . $_FILES['file']['error']);
    }

    // ตรวจสอบเงื่อนไข
    if (!isset($_POST['confirm_18']) || $_POST['confirm_18'] !== '1') {
        die('ต้องยืนยันอายุ 18+');
    }

    if (!isset($_POST['consent']) || $_POST['consent'] !== '1') {
        die('ต้องยืนยันสิทธิ์เนื้อหา');
    }

    if ($_FILES['file']['size'] > $MAX_SIZE) {
        die('ไฟล์ใหญ่เกิน');
    }

    // ตรวจสอบประเภทไฟล์
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES['file']['tmp_name']);
    if (!in_array($mime, $ALLOWED_TYPES)) {
        die('ประเภทไฟล์ไม่อนุญาต: ' . $mime);
    
    // สร้างชื่อไฟล์ใหม่
    $id = bin2hex(random_bytes(8));
    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $name = $id . ($ext ? '.' . $ext : '');
    
    // ย้ายไฟล์
    if (move_uploaded_file($_FILES['file']['tmp_name'], $UPLOAD_DIR . '/' . $name);

    // อัพเดทเมตาดาต้า
    $meta = json_decode(file_get_contents($META_FILE), true);
    array_unshift($meta, [
        'id' => $id,
        'orig' => $_FILES['file']['name'],
        'name' => $name,
        'mime' => $mime,
        'size' => $_FILES['file']['size'],
        'ts' => time()
    ]);
    file_put_contents($META_FILE, json_encode($meta, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

    header('Location: index.html');
    exit;
}
?>
  
