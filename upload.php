<?php
$UPLOAD_DIR = __DIR__ . '/uploads';
$META_FILE = __DIR__ . '/uploads.json';
$MAX_SIZE = 200 * 1024 * 1024;
$ALLOWED = ['image/', 'video/'];

if (!is_dir($UPLOAD_DIR)) mkdir($UPLOAD_DIR, 0755, true);
if (!file_exists($META_FILE)) file_put_contents($META_FILE, json_encode([]));

function is_allowed($mime, $allowed) {
  foreach ($allowed as $a) if (strpos($mime, $a) === 0) return true;
  return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) die('Upload error');
  if (!isset($_POST['confirm_18']) || $_POST['confirm_18'] !== '1') die('ต้องยืนยันอายุ 18+');
  if (!isset($_POST['consent']) || $_POST['consent'] !== '1') die('ต้องยืนยันสิทธิ์เนื้อหา');
  if ($_FILES['file']['size'] > $MAX_SIZE) die('ไฟล์ใหญ่เกิน');

  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($_FILES['file']['tmp_name']);
  if (!is_allowed($mime, $ALLOWED)) die('ประเภทไฟล์ไม่อนุญาต');

  $id = bin2hex(random_bytes(8));
  $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
  $name = $id . ($ext ? '.' . $ext : '');
  move_uploaded_file($_FILES['file']['tmp_name'], $UPLOAD_DIR . '/' . $name);

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
