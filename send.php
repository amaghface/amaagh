<?php
// تنظیمات ایمیل
$to = "amagh.pub@gmail.com";
$subject = "درخواست بررسی اثر جدید";

// دریافت داده‌های فرم
$name     = $_POST['نام و نام خانوادگی'] ?? '';
$book     = $_POST['نام کتاب'] ?? '';
$topic    = $_POST['موضوع کتاب'] ?? '';
$desc     = $_POST['توضیحات کتاب'] ?? '';
$phone    = $_POST['شماره تماس'] ?? '';
$email    = $_POST['ایمیل'] ?? '';

// ساخت پیام متنی
$body = "نام و نام خانوادگی: $name\n";
$body .= "نام کتاب: $book\n";
$body .= "موضوع کتاب: $topic\n";
$body .= "توضیحات: $desc\n";
$body .= "شماره تماس: $phone\n";
$body .= "ایمیل: $email\n";

// بررسی فایل پیوستی
$file_uploaded = isset($_FILES['فایل پیوستی']) && $_FILES['فایل پیوستی']['error'] === UPLOAD_ERR_OK;
$boundary = md5(time());
$headers = "From: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";

if ($file_uploaded) {
    $file_tmp  = $_FILES['فایل پیوستی']['tmp_name'];
    $file_name = $_FILES['فایل پیوستی']['name'];
    $file_type = $_FILES['فایل پیوستی']['type'];
    $file_data = chunk_split(base64_encode(file_get_contents($file_tmp)));

    $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

    $message = "--{$boundary}\r\n";
    $message .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message .= $body . "\r\n";

    $message .= "--{$boundary}\r\n";
    $message .= "Content-Type: {$file_type}; name=\"{$file_name}\"\r\n";
    $message .= "Content-Transfer-Encoding: base64\r\n";
    $message .= "Content-Disposition: attachment; filename=\"{$file_name}\"\r\n\r\n";
    $message .= $file_data . "\r\n";
    $message .= "--{$boundary}--";
} else {
    $headers .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
    $message = $body;
}

// ارسال ایمیل
$success = mail($to, $subject, $message, $headers);

// هدایت به فرم با پیام موفقیت
if ($success) {
    header("Location: https://amaagh.ir/#form?success=true");
    exit;
} else {
    echo "خطا در ارسال ایمیل. لطفاً دوباره تلاش کنید.";
}
?>
