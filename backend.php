<?php
session_start();
$conn = new mysqli("localhost","root","","ims");
if($conn->connect_error) die("DB Error");

// ----------- User Signup -----------
if(isset($_POST['user_signup'])){
    $name=$_POST['user_name'];
    $email=$_POST['user_email'];
    $number=$_POST['user_number'];
    $pass=password_hash($_POST['user_password'],PASSWORD_DEFAULT);

    $stmt=$conn->prepare("INSERT INTO users(name,email,phone,password) VALUES(?,?,?,?)");
    $stmt->bind_param("ssss",$name,$email,$number,$pass);
    $stmt->execute();
    header("Location:index.html");
    exit;
}

// ----------- User Login -----------
if(isset($_POST['user_login'])){
    $id=$_POST['user_id'];
    $pass=$_POST['user_password'];
    $stmt=$conn->prepare("SELECT id,name,password FROM users WHERE email=? OR phone=?");
    $stmt->bind_param("ss",$id,$id);
    $stmt->execute();
    $res=$stmt->get_result();
    if($res->num_rows>0){
        $row=$res->fetch_assoc();
        if(password_verify($pass,$row['password'])){
            $_SESSION['user_id']=$row['id'];
            $_SESSION['user_name']=$row['name'];
            header("Location:user_dashboard.php");
        }else header("Location:index.html?error=wrongpass");
    }else header("Location:index.html?error=nouser");
    exit;
}

// ----------- Admin Signup -----------
if(isset($_POST['admin_signup'])){
    $company=$_POST['admin_company'];
    $email=$_POST['admin_email'];
    $pan=$_POST['admin_pan'];
    $pass=password_hash($_POST['admin_password'],PASSWORD_DEFAULT);

    $stmt=$conn->prepare("INSERT INTO admins(company,email,pan,password) VALUES(?,?,?,?)");
    $stmt->bind_param("ssss",$company,$email,$pan,$pass);
    $stmt->execute();
    header("Location:index.html");
    exit;
}

// ----------- Admin Login -----------
if(isset($_POST['admin_login'])){
    $email=$_POST['admin_login_email'];
    $pass=$_POST['admin_login_password'];
    $stmt=$conn->prepare("SELECT id,company,password FROM admins WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $res=$stmt->get_result();
    if($res->num_rows>0){
        $row=$res->fetch_assoc();
        if(password_verify($pass,$row['password'])){
            $_SESSION['admin_id']=$row['id'];
            $_SESSION['admin_company']=$row['company'];
            header("Location:admin_dashboard.php");
        }else header("Location:index.html?error=admin_wrongpass");
    }else header("Location:index.html?error=admin_nouser");
    exit;
}
?>
