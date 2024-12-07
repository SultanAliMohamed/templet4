<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>الملف الشخصي</h1>
        <?php if ($user['profile_image']): ?>
            <img src="uploads/<?php echo $user['profile_image']; ?>" alt="صورة الملف الشخصي" class="profile-image">
        <?php endif; ?>
        <div class="profile-info">
            <p><strong>الاسم:</strong> <?php echo $user['name']; ?></p>
            <p><strong>البريد الإلكتروني:</strong> <?php echo $user['email']; ?></p>
            <p><strong>اسم المستخدم:</strong> <?php echo $user['username']; ?></p>
        </div>
        <a href="edit_profile.php" class="link">تعديل الملف الشخصي</a>
        <a href="logout.php" class="link">تسجيل الخروج</a>
    </div>
</body>
</html>