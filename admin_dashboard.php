<?php 
session_start();
$conn = new mysqli("localhost","root","1234","ims");
if($conn->connect_error) die("DB Connection Error");

// SINGLE USER
if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit;
}

$user_name = $_SESSION['user_name'];

// DASHBOARD STATS
$total_products = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$salesTotal = $conn->query("SELECT IFNULL(SUM(quantity * price),0) as total_amount FROM sales")->fetch_assoc()['total_amount'];
$purchaseTotal = $conn->query("SELECT IFNULL(SUM(quantity * price),0) as total_amount FROM purchases")->fetch_assoc()['total_amount'];

// LOW STOCK (<=5)
$lowStockCount = $conn->query("SELECT COUNT(*) as total FROM products WHERE quantity <= 5")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | IMS</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}

body{
    background: linear-gradient(135deg,#667eea,#764ba2,#42a5f5,#7b1fa2);
    background-size:400% 400%;
    animation: gradientBG 15s ease infinite;
    min-height:100vh;
    color:white;
}

/* HEADER */
header{
    background: rgba(0,0,0,0.2);
    backdrop-filter: blur(8px);
    padding:20px 50px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
header h2{font-weight:600;}

header a{
    background:#ff5252;
    color:white;
    padding:10px 20px;
    border-radius:10px;
    text-decoration:none;
    transition:0.3s;
}
header a:hover{
    background:#ff1744;
    transform:scale(1.05);
}

/* 🔴 LOW STOCK BAR */
.low-stock-bar{
    width:95%;
    max-width:900px;
    margin:25px auto 0;
    padding:18px 25px;
    border-radius:12px;
    display:flex;
    align-items:center;
    gap:15px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

.low{
    background:rgba(255,0,0,0.2);
    border-left:6px solid red;
    animation:pulse 1.5s infinite;
}

.safe{
    background:rgba(0,255,120,0.2);
    border-left:6px solid #00e676;
}

.low-stock-bar i{
    font-size:28px;
}

.low-text{
    font-size:1rem;
}

/* GRID */
.container{
    width:95%;
    max-width:900px;
    margin:40px auto;
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:25px;
}

/* CARD */
.card{
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(12px);
    border-radius:20px;
    padding:30px 15px;
    text-align:center;
    color:white;
    box-shadow:0 15px 40px rgba(0,0,0,0.3);
    transition:0.4s;
    cursor:pointer;
    position:relative;
}

.card i{
    font-size:45px;
    margin-bottom:12px;
    display:block;
    animation: floatIcon 3s ease-in-out infinite;
}

.card p{
    font-size:1.1rem;
    font-weight:500;
}

.card:hover{
    transform:translateY(-10px) scale(1.05);
}

/* ANIMATIONS */
@keyframes floatIcon{
    0%{transform:translateY(0);}
    50%{transform:translateY(-10px);}
    100%{transform:translateY(0);}
}

@keyframes gradientBG{
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}

@keyframes pulse{
    0%{transform:scale(1);}
    50%{transform:scale(1.02);}
    100%{transform:scale(1);}
}
</style>
</head>

<body>

<header>
    <h2>Welcome, <?php echo htmlspecialchars($user_name); ?></h2>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</header>

<!-- 🔴 LOW STOCK RECTANGLE -->
<div class="low-stock-bar <?php echo ($lowStockCount>0)?'low':'safe'; ?>"
     onclick="location.href='purchase.php'">

    <i class="fas <?php echo ($lowStockCount>0)?'fa-triangle-exclamation':'fa-circle-check'; ?>"></i>

    <div class="low-text">
        <?php if($lowStockCount>0): ?>
            <?php echo $lowStockCount; ?> products low in stock . Click to restock now.
        <?php else: ?>
            All products have sufficient stock ✅
        <?php endif; ?>
    </div>

</div>

<!-- NORMAL DASHBOARD -->
<div class="container">

<div class="card" onclick="location.href='sales.php'">
<i class="fas fa-dollar-sign"></i>
<p>Sales</p>
</div>

<div class="card" onclick="location.href='purchase.php'">
<i class="fas fa-cart-shopping"></i>
<p>Purchase</p>
</div>

<div class="card" onclick="location.href='inventory.php'">
<i class="fas fa-boxes-stacked"></i>
<p>Manage Inventory</p>
</div>

<div class="card" onclick="location.href='add_product.php'">
<i class="fas fa-plus-circle"></i>
<p>Add Product</p>
</div>

<div class="card" onclick="location.href='reports.php'">
<i class="fas fa-chart-line"></i>
<p>Reports</p>
</div>
<div class="card" onclick="location.href='credit_payments.php'">
    <i class="fas fa-money-check-dollar"></i>
    <p>Credits & Payments</p>
</div>

<div class="card" onclick="location.href='admin_manage_profile.php'">
<i class="fas fa-user-gear"></i>
<p>Profile</p>
</div>

</div>

</body>
</html>