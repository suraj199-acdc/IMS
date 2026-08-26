<?php
session_start();

$conn = new mysqli("localhost","root","1234","ims");

if($conn->connect_error){
    die("Database Connection Failed");
}

if(!isset($_SESSION['user_id'])){
    header("Location:index.php");
    exit;
}

/* =========================================
   FILTERS
========================================= */

$start_date   = $_GET['start_date'] ?? '';
$end_date     = $_GET['end_date'] ?? '';
$report_type  = $_GET['report_type'] ?? 'sales';
$search       = $_GET['search'] ?? '';
$show_low_stock = isset($_GET['low_stock']);

/* =========================================
   SUMMARY
========================================= */

$total_sales = $conn->query("
SELECT IFNULL(SUM(quantity*price),0) as total
FROM sales
")->fetch_assoc()['total'];

$total_purchases = $conn->query("
SELECT IFNULL(SUM(quantity*price),0) as total
FROM purchases
")->fetch_assoc()['total'];

$total_profit = $total_sales - $total_purchases;

$total_products = $conn->query("
SELECT COUNT(*) as total
FROM products
")->fetch_assoc()['total'];

$low_stock = $conn->query("
SELECT COUNT(*) as total
FROM products
WHERE quantity<=5
")->fetch_assoc()['total'];

$total_customers = $conn->query("
SELECT COUNT(DISTINCT customer_name) as total
FROM sales
")->fetch_assoc()['total'];

/* =========================================
   QUERY LOGIC
========================================= */

if($report_type == "sales"){

    $sql = "
    SELECT *
    FROM sales
    WHERE 1=1
    ";

    $types = "";
    $params = [];

    /* DATE FILTER */

    if($start_date != "" && $end_date != ""){

        $sql .= "
        AND DATE(sale_date)
        BETWEEN ? AND ?
        ";

        $types .= "ss";

        $params[] = $start_date;
        $params[] = $end_date;
    }

    /* SEARCH */

    if($search != ""){

        $sql .= "
        AND (
            product_name LIKE ?
            OR customer_name LIKE ?
        )
        ";

        $searchTerm = "%$search%";

        $types .= "ss";

        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    /* LOW STOCK */

    if($show_low_stock){

        $sql .= "
        AND quantity <= 5
        ";
    }

    $sql .= "
    ORDER BY sale_date DESC
    ";

}else{

    $sql = "
    SELECT *
    FROM purchases
    WHERE 1=1
    ";

    $types = "";
    $params = [];

    /* DATE FILTER */

    if($start_date != "" && $end_date != ""){

        $sql .= "
        AND DATE(purchase_date)
        BETWEEN ? AND ?
        ";

        $types .= "ss";

        $params[] = $start_date;
        $params[] = $end_date;
    }

    /* SEARCH */

    if($search != ""){

        $sql .= "
        AND product_name LIKE ?
        ";

        $types .= "s";

        $params[] = "%$search%";
    }

    /* LOW STOCK */

    if($show_low_stock){

        $sql .= "
        AND quantity <= 5
        ";
    }

    $sql .= "
    ORDER BY purchase_date DESC
    ";
}

/* =========================================
   EXECUTE QUERY
========================================= */

$stmt = $conn->prepare($sql);

if(!empty($params)){
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();

/* =========================================
   TOP PRODUCTS
========================================= */

$top_products = $conn->query("
SELECT product_name,
SUM(quantity) as total_qty,
SUM(quantity*price) as total_amount
FROM sales
GROUP BY product_name
ORDER BY total_qty DESC
LIMIT 5
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Reports | IMS</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:linear-gradient(135deg,#667eea,#764ba2,#42a5f5,#7b1fa2);
    background-size:400% 400%;
    animation:gradientBG 15s ease infinite;
    min-height:100vh;
    color:white;
}

/* HEADER */

header{
    background:rgba(0,0,0,0.2);
    backdrop-filter:blur(10px);
    padding:20px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.buttons{
    display:flex;
    gap:10px;
}

.btn{
    padding:10px 18px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    text-decoration:none;
    font-weight:600;
}

.dashboard-btn{
    background:#ffeb3b;
    color:#333;
}

.print-btn{
    background:#28a745;
    color:white;
}

.logout-btn{
    background:#dc3545;
    color:white;
}

.filter-btn{
    background:#2196f3;
    color:white;
}

/* FILTER */

.filter-box{
    width:95%;
    margin:20px auto;
    background:rgba(255,255,255,0.1);
    padding:20px;
    border-radius:15px;
    display:flex;
    flex-wrap:wrap;
    gap:10px;
}

.filter-box input,
.filter-box select{
    padding:10px;
    border:none;
    border-radius:8px;
}

/* CARDS */

.cards{
    width:95%;
    margin:20px auto;
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
}

.card{
    background:rgba(255,255,255,0.12);
    backdrop-filter:blur(10px);
    border-radius:20px;
    padding:25px;
    text-align:center;
    transition:0.4s;
}

.card:hover{
    transform:translateY(-5px);
}

/* TABLE */

table{
    width:95%;
    margin:20px auto;
    border-collapse:collapse;
    background:rgba(255,255,255,0.1);
    border-radius:15px;
    overflow:hidden;
}

th{
    background:rgba(0,0,0,0.35);
}

th,td{
    padding:14px;
    text-align:center;
    border:1px solid rgba(255,255,255,0.1);
}

tr:hover{
    background:rgba(255,255,255,0.08);
}

img{
    width:70px;
    height:70px;
    border-radius:10px;
    object-fit:cover;
}

.section-title{
    width:95%;
    margin:40px auto 10px;
    font-size:28px;
}

.low-stock{
    background:#dc3545;
    padding:5px 12px;
    border-radius:6px;
}

@keyframes gradientBG{

0%{
    background-position:0% 50%;
}

50%{
    background-position:100% 50%;
}

100%{
    background-position:0% 50%;
}

}

</style>

</head>

<body>

<header>

<h2>Reports </h2>

<div class="buttons">

<a href="admin_dashboard.php"
class="btn dashboard-btn">
⬅ Dashboard
</a>

<button onclick="window.print()"
class="btn print-btn">
🖨 Print
</button>

<form method="post" action="logout.php">

<button class="btn logout-btn">
Logout
</button>

</form>

</div>

</header>

<!-- FILTERS -->

<form method="GET" class="filter-box">

<select name="report_type">

<option value="sales"
<?php if($report_type=="sales") echo "selected"; ?>>
Sales Reports
</option>

<option value="purchases"
<?php if($report_type=="purchases") echo "selected"; ?>>
Purchase Reports
</option>

</select>

<input type="date"
name="start_date"
value="<?php echo $start_date; ?>">

<input type="date"
name="end_date"
value="<?php echo $end_date; ?>">

<input type="text"
name="search"
placeholder="Search Product..."
value="<?php echo htmlspecialchars($search); ?>">

<button class="btn filter-btn">
Filter
</button>

<a href="reports.php?report_type=<?php echo $report_type; ?>&low_stock=1"
class="btn logout-btn">
⚠ Low Stock
</a>

<a href="reports.php"
class="btn dashboard-btn">
Reset
</a>

</form>

<!-- SUMMARY -->

<div class="cards">

<div class="card">
<h3>Total Sales</h3>
<h2>Rs <?php echo number_format($total_sales,2); ?></h2>
</div>

<div class="card">
<h3>Total Purchases</h3>
<h2>Rs <?php echo number_format($total_purchases,2); ?></h2>
</div>

<div class="card">
<h3>Total Profit</h3>
<h2>Rs <?php echo number_format($total_profit,2); ?></h2>
</div>

<div class="card">
<h3>Total Products</h3>
<h2><?php echo $total_products; ?></h2>
</div>

<div class="card">
<h3>Total Customers</h3>
<h2><?php echo $total_customers; ?></h2>
</div>

<div class="card">
<h3>Low Stock Items</h3>
<h2><?php echo $low_stock; ?></h2>
</div>

</div>

<!-- REPORT TABLE -->

<h2 class="section-title">

<?php

if($show_low_stock){

    echo "Low Stock Reports";

}else{

    if($report_type=="sales"){
        echo "Sales Reports";
    }else{
        echo "Purchase Reports";
    }

}

?>

</h2>

<table>

<?php if($report_type=="sales"): ?>

<tr>
<th>No</th>
<th>Product</th>
<th>Image</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
<th>Customer</th>
<th>Contact</th>
<th>Status</th>
<th>Date</th>
</tr>

<?php

$i=1;

while($row=$result->fetch_assoc()):

?>

<tr>

<td><?php echo $i++; ?></td>

<td><?php echo htmlspecialchars($row['product_name']); ?></td>

<td>
<img src="uploads/<?php echo $row['product_image']; ?>">
</td>

<td><?php echo $row['quantity']; ?></td>

<td>
Rs <?php echo number_format($row['price'],2); ?>
</td>

<td>
Rs <?php
echo number_format(
$row['quantity']*$row['price'],2
);
?>
</td>

<td>
<?php echo htmlspecialchars($row['customer_name']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['customer_contact']); ?>
</td>

<td>

<?php

if($row['quantity'] <= 5){

    echo "<span class='low-stock'>Low Stock</span>";

}else{

    echo "Available";
}

?>

</td>

<td><?php echo $row['sale_date']; ?></td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<th>No</th>
<th>Product</th>
<th>Image</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
<th>Status</th>
<th>Date</th>
</tr>

<?php

$i=1;

while($row=$result->fetch_assoc()):

?>

<tr>

<td><?php echo $i++; ?></td>

<td><?php echo htmlspecialchars($row['product_name']); ?></td>

<td>
<img src="uploads/<?php echo $row['product_image']; ?>">
</td>

<td><?php echo $row['quantity']; ?></td>

<td>
Rs <?php echo number_format($row['price'],2); ?>
</td>

<td>
Rs <?php
echo number_format(
$row['quantity']*$row['price'],2
);
?>
</td>

<td>

<?php

if($row['quantity'] <= 5){

    echo "<span class='low-stock'>Low Stock</span>";

}else{

    echo "Available";
}

?>

</td>

<td><?php echo $row['purchase_date']; ?></td>

</tr>

<?php endwhile; ?>

<?php endif; ?>

</table>

<!-- TOP PRODUCTS -->

<h2 class="section-title">
Top Selling Products
</h2>

<table>

<tr>
<th>Product</th>
<th>Total Quantity Sold</th>
<th>Total Revenue</th>
</tr>

<?php while($top=$top_products->fetch_assoc()): ?>

<tr>

<td>
<?php echo htmlspecialchars($top['product_name']); ?>
</td>

<td><?php echo $top['total_qty']; ?></td>

<td>
Rs <?php
echo number_format(
$top['total_amount'],2
);
?>
</td>

</tr>

<?php endwhile; ?>

</table>

</body>
</html>