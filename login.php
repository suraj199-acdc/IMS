<?php 
session_start();
$conn = new mysqli("localhost","root","1234","ims");
if($conn->connect_error) die("DB Connection Error");
// ================= SIGNUP =================
if(isset($_POST['signup'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if($password !== $cpassword){
        $_SESSION['error'] = "Passwords do not match!";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s",$email);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0){
            $_SESSION['error'] = "Email already exists!";
        } else {
            $hash = password_hash($password,PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users(name,email,password,role) VALUES(?,?,?,'admin')");
            $stmt->bind_param("sss",$name,$email,$hash);

            if($stmt->execute()){
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['role'] = 'admin';

                header("Location: admin_dashboard.php");
                exit;
            } else {
                $_SESSION['error'] = "Signup failed!";
            }
        }
    }
}

// ================= LOGIN =================
if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id,name,password,role FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id,$name,$hash,$role);

    if($stmt->num_rows > 0){
        $stmt->fetch();

        if(password_verify($password,$hash)){
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['role'] = $role;

            header("Location: admin_dashboard.php");
            exit;
        } else {
            $_SESSION['error'] = "Wrong password!";
        }
    } else {
        $_SESSION['error'] = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>IMS Auth</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Poppins;}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#667eea,#764ba2);
}

.container{
    text-align:center;
    color:white;
}

button{
    padding:12px 25px;
    margin:10px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:bold;
    transition:0.3s;
}

.login{background:#ff9800;}
.signup{background:#00c853;}

button:hover{transform:scale(1.05);}

/* MODAL */
.modal{
    display:none;
    position:fixed;
    top:0;left:0;
    width:100%;height:100%;
    background:rgba(0,0,0,0.7);
    justify-content:center;
    align-items:center;
}

.box{
    background:white;
    padding:25px;
    border-radius:12px;
    width:320px;
    text-align:center;
}

input{
    width:100%;
    padding:12px;
    margin:8px 0;
    border-radius:8px;
    border:1px solid #ccc;
}

.error{
    color:red;
    margin-bottom:10px;
}
</style>
</head>

<body>

<div class="container">
    <h1>IMS Portal 🚀</h1>

    <button class="login" onclick="openModal('login')">Login</button>
    <button class="signup" onclick="openModal('signup')">Sign Up</button>
</div>

<!-- LOGIN -->
<div class="modal" id="login">
<div class="box">

<h3>Login</h3>

<?php if(isset($_SESSION['error'])): ?>
<div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<form method="post">
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>

<button name="login">Login</button>
</form>

<button onclick="closeModal('login')">Close</button>
</div>
</div>

<!-- SIGNUP -->
<div class="modal" id="signup">
<div class="box">

<h3>Sign Up</h3>

<form method="post">
<input type="text" name="name" placeholder="Full Name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<input type="password" name="cpassword" placeholder="Confirm Password" required>

<button name="signup">Sign Up</button>
</form>

<button onclick="closeModal('signup')">Close</button>
</div>
</div>

<script>
function openModal(id){
    document.getElementById(id).style.display="flex";
}
function closeModal(id){
    document.getElementById(id).style.display="none";
}
</script>

</body>
</html>