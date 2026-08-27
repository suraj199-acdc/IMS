<?php
session_start();

$conn = new mysqli(
    "sql303.infinityfree.com",
    "if0_42754298",
    "YOUR_VPANEL_PASSWORD",
    "if0_42754298_ims"
);

if ($conn->connect_error) {
    die("Database Connection Error: " . $conn->connect_error);
}
?>

// ================= SIGNUP =================
if(isset($\_POST['signup'])){
    $name = trim($\_POST['name']);
    $email = trim($\_POST['email']);
    $phone = trim($\_POST['phone'] ?? '');
    $password = $\_POST['password'];
    $cpassword = $\_POST['cpassword'];

    if($password !== $cpassword){
        $\_SESSION['signup\_error'] = "Passwords do not match!";
    } else {
        $hashed = password\_hash($password, PASSWORD\_DEFAULT);

        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind\_param("s",$email);
        $check->execute();
        $check->store\_result();

        if($check->num\_rows > 0){
            $\_SESSION['signup\_error'] = "Email already exists!";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name,email,phone,password,created\_at) VALUES (?,?,?,?,NOW())");
            $stmt->bind\_param("ssss",$name,$email,$phone,$hashed);

            if($stmt->execute()){
                $\_SESSION['user\_id'] = $conn->insert\_id;
                $\_SESSION['user\_name'] = $name;
                header("Location: admin\_dashboard.php");
                exit;
            }
        }
    }
}

// ================= LOGIN =================
if(isset($\_POST['login'])){
    $email = trim($\_POST['email']);
    $password = $\_POST['password'];

    $stmt = $conn->prepare("SELECT id,name,password FROM users WHERE email=?");
    $stmt->bind\_param("s",$email);
    $stmt->execute();
    $stmt->store\_result();
    $stmt->bind\_result($id,$name,$hash);

    if($stmt->num\_rows > 0){
        $stmt->fetch();

        if(password\_verify($password,$hash)){
            $\_SESSION['user\_id'] = $id;
            $\_SESSION['user\_name'] = $name;
            header("Location: admin\_dashboard.php");
            exit;
        } else {
            $\_SESSION['login\_error'] = "Wrong password!";
        }
    } else {
        $\_SESSION['login\_error'] = "User not found!";
    }
}
?>

\<!DOCTYPE html>
\<html lang="en">
\<head>
\<meta charset="UTF-8">
\<meta name="viewport" content="width=device-width, initial-scale=1.0">
\<title>IMS Portal\</title>

\<!-- FONT + ICONS -->
\<link href="[https://fonts.googleapis.com/css2?family=Poppins\:wght@300;400;500;600;700&display=swap](https://fonts.googleapis.com/css2?family=Poppins\:wght@300;400;500;600;700\&display=swap)" rel="stylesheet">
\<link rel="stylesheet" href="[https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css](https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css)">

\<style>
\*{margin:0;padding:0;box-sizing\:border-box;font-family:'Poppins',sans-serif;}

body{
    min-height:100vh;
    display\:flex;
    flex-direction\:column;
    align-items\:center;
    justify-content\:flex-start;
    background: linear-gradient(-45deg,#667eea,#764ba2,#42a5f5,#7b1fa2);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
}

/\* NAVBAR \*/
nav{
    width:100%;
    display\:flex;
    justify-content\:space-between;
    align-items\:center;
    padding:20px 50px;
    position\:fixed;
    top:0;
    left:0;
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(10px);
    z-index:1000;
    box-shadow:0 5px 20px rgba(0,0,0,0.2);
}
nav h1{font-size:2rem; font-weight:700; color\:white;}

nav button{
    margin-left:10px;
    padding:12px 22px;
    border\:none;
    border-radius:25px;
    cursor\:pointer;
    font-weight:600;
    display\:flex;
    align-items\:center;
    gap:8px;
    transition: all 0.3s ease;
}

nav button i{
    font-size:20px;
    transition:0.3s;
}

.signup{background:#00c853;color\:white;}
.signup\:hover{
    background:#00e676;
    transform\:translateY(-3px);
    box-shadow:0 0 15px rgba(255,255,255,0.5);
}
.login{background:#ffab00;color\:white;}
.login\:hover{
    background:#ffc400;
    transform\:translateY(-3px);
    box-shadow:0 0 15px rgba(255,255,255,0.5);
}

/\* ICON ANIMATION \*/
nav button\:hover i{
    transform: scale(1.3) rotate(10deg);
}

/\* HERO \*/
.hero{
    margin-top:140px;
    text-align\:center;
    color\:white;
    animation\:fadeIn 1.2s ease forwards;
}

.hero h2{
    font-size:3rem;
    margin-bottom:15px;
    text-shadow: 2px 2px 15px rgba(0,0,0,0.4);
}

.hero-icon{
    font-size:55px;
    margin-right:10px;
    animation: floatIcon 3s ease-in-out infinite;
}

.hero p{
    font-size:1.2rem;
    text-shadow: 1px 1px 10px rgba(0,0,0,0.3);
}

/\* MODAL \*/
.modal{
    display\:none;
    position\:fixed;
    width:100%;
    height:100%;
    top:0;
    left:0;
    background\:rgba(0,0,0,0.6);
    justify-content\:center;
    align-items\:center;
    z-index:2000;
}

.modal-box{
    background: rgba(255,255,255,0.1);
    border-radius:20px;
    padding:35px 30px;
    width:360px;
    text-align\:center;
    backdrop-filter: blur(15px);
    box-shadow:0 15px 40px rgba(0,0,0,0.3);
    animation\:modalFade 0.4s ease forwards;
}

.modal-box h3{
    margin-bottom:20px;
    color\:white;
}

.modal-box input{
    width:100%;
    padding:14px;
    margin:10px 0;
    border-radius:12px;
    border\:none;
    outline\:none;
    background\:rgba(255,255,255,0.15);
    color\:white;
}

.modal-box input::placeholder{
    color\:rgba(255,255,255,0.7);
}

.modal-box input\:focus{
    background: rgba(255,255,255,0.25);
    box-shadow:0 0 15px rgba(255,255,255,0.3);
}

/\* BUTTONS \*/
.modal-box button{
    width:100%;
    padding:14px;
    border\:none;
    border-radius:12px;
    background:#667eea;
    color\:white;
    margin-top:15px;
    font-weight:600;
    cursor\:pointer;
}

.close{
    background:#ff5252 !important;
}

/\* SHOW PASSWORD \*/
.show-pass{
    display\:flex;
    align-items\:center;
    justify-content\:flex-start;
    margin:5px 0 10px 0;
    color\:white;
}

/\* ERROR \*/
.error{
    color:#ff5252;
    font-size:0.9rem;
    margin-bottom:10px;
}

/\* ANIMATIONS \*/
@keyframes floatIcon{
    0%{transform\:translateY(0);}
    50%{transform\:translateY(-12px);}
    100%{transform\:translateY(0);}
}

@keyframes gradientBG{
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}

@keyframes fadeIn{
    from{opacity:0; transform\:translateY(-20px);}
    to{opacity:1; transform\:translateY(0);}
}

@keyframes modalFade{
    from{transform\:scale(0.7);opacity:0;}
    to{transform\:scale(1);opacity:1;}
}
\</style>
\</head>

\<body>

\<nav>
\<h1>IMS Portal\</h1>
\<div>
\<button class="signup" onclick="openModal('signup')">
\<i class="fas fa-user-plus">\</i> Sign Up
\</button>

\<button class="login" onclick="openModal('login')">
\<i class="fas fa-right-to-bracket">\</i> Login
\</button>
\</div>
\</nav>

\<div class="hero">
\<h2>
\<i class="fas fa-boxes hero-icon">\</i>
Smart Inventory Management
\</h2>
\<p>Manage your stock, sales & purchases with ease 🚀\</p>
\</div>

\<!-- SIGNUP -->
\<div class="modal" id="signup">
\<div class="modal-box">
\<h3>Create Account\</h3>

\<?php if(isset($\_SESSION['signup\_error'])): ?>
\<div class="error">\<?php echo $\_SESSION['signup\_error']; unset($\_SESSION['signup\_error']); ?>\</div>
\<?php endif; ?>

\<form method="post">
\<input type="text" name="name" placeholder="Full Name" required>
\<input type="email" name="email" placeholder="Email" required>
\<input type="text" name="phone" placeholder="Phone">
\<input type="password" name="password" id="signupPassword" placeholder="Password" required>
\<input type="password" name="cpassword" id="signupCPassword" placeholder="Confirm Password" required>

\<div class="show-pass">
\<input type="checkbox" onclick="togglePassword('signupPassword','signupCPassword')"> Show Password
\</div>

\<button type="submit" name="signup">Sign Up\</button>
\</form>

\<button class="close" onclick="closeModal('signup')">Close\</button>
\</div>
\</div>

\<!-- LOGIN -->
\<div class="modal" id="login">
\<div class="modal-box">
\<h3>Login\</h3>

\<?php if(isset($\_SESSION['login\_error'])): ?>
\<div class="error">\<?php echo $\_SESSION['login\_error']; unset($\_SESSION['login\_error']); ?>\</div>
\<?php endif; ?>

\<form method="post">
\<input type="email" name="email" placeholder="Email" required>
\<input type="password" name="password" id="loginPassword" placeholder="Password" required>

\<div class="show-pass">
\<input type="checkbox" onclick="togglePassword('loginPassword')"> Show Password
\</div>

\<button type="submit" name="login">Login\</button>
\</form>

\<button class="close" onclick="closeModal('login')">Close\</button>
\</div>
\</div>

\<script>
function openModal(id){document.getElementById(id).style.display="flex";}
function closeModal(id){document.getElementById(id).style.display="none";}

window\.onclick = function(e){
    let signup=document.getElementById('signup');
    let login=document.getElementById('login');
    if(e.target===signup) signup.style.display="none";
    if(e.target===login) login.style.display="none";
}

function togglePassword(passId, confirmId=null){
    let passField=document.getElementById(passId);
    passField.type=passField.type==='password'?'text':'password';
    if(confirmId){
        let cPassField=document.getElementById(confirmId);
        cPassField.type=cPassField.type==='password'?'text':'password';
    }
}
\</script>

\</body>
\</html> write this code into html code
