<?php 
session_start();
$conn = new mysqli("localhost","root","1234","ims");
if($conn->connect_error) die("DB Connection Error");

$msg = "";

/* ---------- FILTERS ---------- */
$filter_dept = $_GET['department'] ?? '';
$show_low_stock = isset($_GET['low_stock']) ? true : false;

/* ---------- INVENTORY SUMMARY ---------- */
$summary = $conn->query("SELECT 
    COUNT(*) as total_products,
    SUM(quantity) as total_stock,
    SUM(CASE WHEN quantity <= 5 THEN 1 ELSE 0 END) as low_stock
FROM products")->fetch_assoc();

/* ---------- MAIN QUERY ---------- */
if($show_low_stock){

    if($filter_dept != ""){
        $stmt = $conn->prepare("SELECT * FROM products WHERE quantity <= 5 AND department=? ORDER BY id DESC");
        $stmt->bind_param("s", $filter_dept);
    } else {
        $stmt = $conn->prepare("SELECT * FROM products WHERE quantity <= 5 ORDER BY id DESC");
    }

} else {

    if($filter_dept != ""){
        $stmt = $conn->prepare("SELECT * FROM products WHERE department=? ORDER BY id DESC");
        $stmt->bind_param("s", $filter_dept);
    } else {
        $stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC");
    }
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Inventory | IMS</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
header .buttons{display:flex; gap:10px;}

button, .back-btn{
    padding:10px 20px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
}

button.logout{background:#ff5252;color:white;}
.back-btn{background:#ffeb3b;color:#333;}

/* TOP ACTION BUTTON */
.top-actions{
    width:95%;
    margin:15px auto;
    display:flex;
    gap:10px;
}

.top-btn{
    background:#ffffff20;
    color:white;
    padding:10px 15px;
    border-radius:8px;
    text-decoration:none;
    font-weight:600;
    backdrop-filter:blur(5px);
}

.top-btn:hover{
    background:#ffffff40;
}

/* ACTIVE LOW STOCK BUTTON */
.active{
    background:#dc3545 !important;
}

/* CARDS */
.cards{width:95%;margin:20px auto;display:flex;gap:20px;flex-wrap:wrap;}
.card{flex:1;min-width:150px;padding:25px;border-radius:20px;text-align:center;}
.blue{background:#2575fc;}
.green{background:#28a745;}
.red{background:#dc3545;}

/* TABLE */
table{width:95%;margin:20px auto;border-collapse:collapse;background:rgba(255,255,255,0.15);}
th,td{padding:12px;text-align:center;border:1px solid rgba(255,255,255,0.2);}
img{width:80px;height:80px;object-fit:cover;}

.badge-low{background:#dc3545;padding:5px 10px;border-radius:6px;}
.badge-ok{background:#28a745;padding:5px 10px;border-radius:6px;}

@keyframes gradientBG{
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}
</style>
</head>

<body>

<header>
    <h2>Inventory Management</h2>
    <div class="buttons">
        <button class="back-btn" onclick="location.href='admin_dashboard.php'">⬅ Dashboard</button>
        <form method="post" action="logout.php">
            <button class="logout">Logout</button>
        </form>
    </div>
</header>

<!-- TOP ACTION BUTTONS -->
<div class="top-actions">

    <a class="top-btn <?php if(!$show_low_stock) echo 'active'; ?>" 
       href="inventory.php">
       All Products
    </a>

    <a class="top-btn <?php if($show_low_stock) echo 'active'; ?>" 
       href="inventory.php?low_stock=1">
       ⚠ Low Stock Only
    </a>

</div>

<!-- SUMMARY -->
<div class="cards">
    <div class="card blue">
        <h3>Total Products</h3>
        <h2><?php echo $summary['total_products']; ?></h2>
    </div>

    <div class="card green">
        <h3>Total Stock</h3>
        <h2><?php echo $summary['total_stock']; ?></h2>
    </div>

    <div class="card red">
        <h3>Low Stock Items</h3>
        <h2><?php echo $summary['low_stock']; ?></h2>
    </div>
</div>

<!-- TABLE -->
<table>
<tr>
<th>No</th>
<th>ID</th>
<th>Name</th>
<th>Image</th>
<th>Department</th>
<th>Price</th>
<th>Stock</th>
<th>Action</th>
</tr>

<?php $i=1; while($row=$result->fetch_assoc()): ?>
<tr>
<td><?php echo $i++; ?></td>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><img src="uploads/<?php echo $row['image']; ?>"></td>
<td><?php echo $row['department']; ?></td>
<td>Rs <?php echo $row['price']; ?></td>

<td>
<?php 
if($row['quantity'] <= 5){
    echo "<a href='purchase.php?product_id=".$row['id']."'>
            <span class='badge-low'>".$row['quantity']." ⚠</span>
          </a>";
} else {
    echo "<span class='badge-ok'>".$row['quantity']."</span>";
}
?>
</td>
<td>

<!-- EDIT -->
<a href="edit_product.php?id=<?php echo $row['id']; ?>" 
   style="display:inline-block;padding:6px 10px;background:#ffc107;color:black;border-radius:6px;text-decoration:none;font-weight:600;">
   Edit
</a>

<br><br>

<!-- DELETE -->
<a href="delete_product.php?id=<?php echo $row['id']; ?>" 
   onclick="return confirm('⚠ Are you sure you want to delete this product?');"
   style="display:inline-block;padding:6px 10px;background:#dc3545;color:white;border-radius:6px;text-decoration:none;font-weight:600;">
   Delete
</a>

</td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>