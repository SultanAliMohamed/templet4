<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    
    // التحقق من وجود اسم المستخدم أو البريد الإلكتروني
    $check_query = "SELECT * FROM users WHERE (username = '$username' OR email = '$email') AND id != $user_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = 'اسم المستخدم أو البريد الإلكتروني مستخدم بالفعل';
    } else {
        $profile_image_sql = '';
        
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $filename = $_FILES['profile_image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid() . '.' . $ext;
                $upload_path = 'uploads/' . $new_filename;
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                    $profile_image_sql = ", profile_image = '$new_filename'";
                    
                    // حذف الصورة القديمة
                    $old_image_query = "SELECT profile_image FROM users WHERE id = $user_id";
                    $old_image_result = mysqli_query($conn, $old_image_query);
                    $old_image = mysqli_fetch_assoc($old_image_result)['profile_image'];
                    if ($old_image) {
                        unlink('uploads/' . $old_image);
                    }
                }
            }
        }
        
        $query = "UPDATE users SET name = '$name', email = '$email', username = '$username' $profile_image_sql 
                 WHERE id = $user_id";
        
        if (mysqli_query($conn, $query)) {
            $success = 'تم تحديث البيانات بنجاح';
        } else {
            $error = 'حدث خطأ أثناء تحديث البيانات';
        }
    }
}

$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الملف الشخصي</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>تعديل الملف الشخصي</h1>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php if ($user['profile_image']): ?>
            <img src="uploads/<?php echo $user['profile_image']; ?>" alt="صورة الملف الشخصي" class="profile-image">
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">الاسم</label>
                <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="username">اسم المستخدم</label>
                <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required>
            </div>
            <div class="form-group">
                <label for="profile_image">تغيير صورة الملف الشخصي</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
            </div>
            <button type="submit">حفظ التغييرات</button>
        </form>
        <a href="profile.php" class="link">العودة للملف الشخصي</a>
    </div>
</body>
</html>