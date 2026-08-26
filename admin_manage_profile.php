<?php
session_start();
$conn = new mysqli("localhost","root","1234","ims");
if(!isset($_SESSION['user_id'])) header("Location:index.php");

$msg = "";

$admin_id = $_SESSION['user_id'];
$success = $error = "";

// Fetch logged-in admin data
$stmt = $conn->prepare("SELECT id, name, email, phone, password, created_at FROM users WHERE id=?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if(!$admin){
    die("Admin not found.");
}

// Handle form submission
if(isset($_POST['update'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // Password change validation
    if(!empty($new_password)){
        if(empty($current_password)){
            $error = "Enter current password to change password.";
        } elseif(!password_verify($current_password, $admin['password'])){
            $error = "Current password incorrect.";
        }
    }

    if(!$error){
        if(!empty($new_password)){
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, password=? WHERE id=?");
            $update->bind_param("ssssi", $name, $email, $phone, $new_hash, $admin_id);
        } else {
            $update = $conn->prepare("UPDATE users SET name=?, email=?, phone=? WHERE id=?");
            $update->bind_param("sssi", $name, $email, $phone, $admin_id);
        }

        if($update->execute()){
            $success = "Profile updated successfully!";
            $_SESSION['user_name'] = $name;

            // Refresh admin data
            $stmt = $conn->prepare("SELECT id, name, email, phone, password, created_at FROM users WHERE id=?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $admin = $stmt->get_result()->fetch_assoc();
        } else {
            $error = "Update failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Manage Profile</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg,#667eea,#764ba2,#42a5f5,#7b1fa2);
    background-size:400% 400%;
    animation: gradientBG 15s ease infinite;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

@keyframes gradientBG{
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}

.container {
    max-width:600px;
    width:100%;
    background:white;
    border-radius:15px;
    box-shadow:0 20px 50px rgba(0,0,0,0.2);
    padding:30px;
    color:#333;
}

h2 {
    text-align:center;
    margin-bottom:25px;
    color:#333;
}

.details {
    background:#eef2ff;
    padding:20px;
    border-radius:10px;
    margin-bottom:25px;
    line-height:1.6;
}

input {
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:1rem;
}

input:focus {
    border-color:#667eea;
    outline:none;
    box-shadow:0 0 5px rgba(102,126,234,0.5);
}

button {
    padding:12px 20px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
    font-size:1rem;
    margin-top:10px;
    transition:0.3s;
}

.update-btn {
    background:#667eea;
    color:white;
}

.update-btn:hover {
    background:#556cd6;
    transform:scale(1.02);
}

.back-btn {
    display:inline-block;
    margin-top:15px;
    background:#ffeb3b;
    color:#333;
    text-decoration:none;
    padding:10px 18px;
    border-radius:8px;
    transition:0.3s;
}

.back-btn:hover {
    background:#fdd835;
    transform:scale(1.02);
}

.success {
    text-align:center;
    color:#28a745;
    font-weight:600;
    margin-bottom:15px;
}

.error {
    text-align:center;
    color:#dc3545;
    font-weight:600;
    margin-bottom:15px;
}

hr {
    border:none;
    border-top:1px solid #ccc;
    margin:20px 0;
}

strong {
    display:block;
    margin-bottom:10px;
}
</style>
</head>
<body>

<div class="container">
    <h2>Manage Profile</h2>

    <?php if($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
    <?php if($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>

    <div class="details">
        <strong>Current Details:</strong>
        ID: <?php echo $admin['id']; ?><br>
        Name: <?php echo htmlspecialchars($admin['name']); ?><br>
        Email: <?php echo htmlspecialchars($admin['email']); ?><br>
        Phone: <?php echo htmlspecialchars($admin['phone']); ?><br>
        Created At: <?php echo $admin['created_at']; ?>
    </div>

    <form method="post">
        <input type="text" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" placeholder="Name" required>
        <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" placeholder="Email" required>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($admin['phone']); ?>" placeholder="Phone">

        <hr>
        <strong>Change Password (Optional)</strong>
        <input type="password" name="current_password" placeholder="Current Password">
        <input type="password" name="new_password" placeholder="New Password">

        <button type="submit" name="update" class="update-btn">Update Profile</button>
    </form>

    <a href="admin_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
</div>

</body>
</html>