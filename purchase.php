<?php
session_start();

$conn = new mysqli("localhost","root","1234","ims");

if($conn->connect_error){
    die("DB Connection Error");
}

if(!isset($_SESSION['user_id'])){
    header("Location:index.php");
    exit;
}

$msg = "";

/* =========================================
   LOW STOCK
========================================= */

$lowStock = $conn->query("
SELECT *
FROM products
WHERE quantity <= 5
ORDER BY quantity ASC
");

/* =========================================
   HANDLE MULTIPLE PURCHASE
========================================= */

if(isset($_POST['purchase'])){

    $product_ids = $_POST['product_id'];
    $quantities  = $_POST['quantity'];

    /* VENDOR DETAILS */

    $vendor_name    = trim($_POST['vendor_name']);
    $vendor_phone   = trim($_POST['vendor_phone']);
    $vendor_address = trim($_POST['vendor_address']);

    /* PAYMENT */

    $payment_method = $_POST['payment_method'];
    $payment_status = $_POST['payment_status'];

    $success = 0;

    for($i=0; $i<count($product_ids); $i++){

        $product_id = (int)$product_ids[$i];
        $quantity   = (int)$quantities[$i];

        if($product_id <=0 || $quantity <=0){
            continue;
        }

        $product = $conn->query("
        SELECT *
        FROM products
        WHERE id=$product_id
        ")->fetch_assoc();

        if($product){

            /* UPDATE STOCK */

            $new_stock =
            $product['quantity'] + $quantity;

            $stmt = $conn->prepare("
            UPDATE products
            SET quantity=?
            WHERE id=?
            ");

            $stmt->bind_param(
                "ii",
                $new_stock,
                $product_id
            );

            $stmt->execute();

            /* TOTAL */

            $total_amount =
            $quantity * $product['price'];

            /* PAYMENT */

            if($payment_status=="Paid"){

                $paid_amount = $total_amount;
                $due_amount  = 0;

            }else{

                $paid_amount = 0;
                $due_amount  = $total_amount;
            }

            /* INSERT PURCHASE */

            $stmt2 = $conn->prepare("
            INSERT INTO purchases
            (
                product_id,
                product_name,
                quantity,
                price,
                product_image,

                vendor_name,
                vendor_phone,
                vendor_address,

                payment_method,
                payment_status,

                paid_amount,
                due_amount
            )

            VALUES

            (
                ?,?,?,?,?,?,?,?,?,?,?,?
            )
            ");

            $stmt2->bind_param(
                "isidssssssdd",

                $product_id,
                $product['name'],
                $quantity,
                $product['price'],
                $product['image'],

                $vendor_name,
                $vendor_phone,
                $vendor_address,

                $payment_method,
                $payment_status,

                $paid_amount,
                $due_amount
            );

            $stmt2->execute();

            $success++;
        }
    }

    if($success > 0){

        $msg =
        "✅ $success Products Purchased Successfully!";

    }else{

        $msg =
        "⚠ Purchase Failed!";
    }
}

/* =========================================
   PRODUCTS
========================================= */

$products = $conn->query("
SELECT *
FROM products
ORDER BY name ASC
");

/* =========================================
   RECENT PURCHASES
========================================= */

$recent = $conn->query("
SELECT *
FROM purchases
ORDER BY purchase_date DESC
LIMIT 20
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Multi Purchase | IMS</title>

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
    border-radius:10px;
    cursor:pointer;
    font-weight:600;
}

.dashboard-btn{
    background:#ffeb3b;
    color:#333;
}

.low-btn{
    background:#dc3545;
    color:white;
}

/* LOW STOCK */

.alert-box{
    width:90%;
    margin:20px auto;
    background:rgba(255,0,0,0.2);
    padding:20px;
    border-radius:15px;
    display:none;
}

.low-stock-container{
    display:flex;
    gap:15px;
    overflow-x:auto;
}

.low-card{
    min-width:140px;
    background:rgba(255,255,255,0.15);
    padding:12px;
    border-radius:12px;
    text-align:center;
}

.low-card img{
    width:70px;
    height:70px;
    border-radius:10px;
    object-fit:cover;
}

/* MESSAGE */

.message{
    width:90%;
    margin:15px auto;
    text-align:center;
    font-weight:600;
}

/* FORM */

form{
    width:90%;
    max-width:900px;
    margin:25px auto;
    background:rgba(255,255,255,0.12);
    backdrop-filter:blur(10px);
    padding:25px;
    border-radius:20px;
}

form h3{
    margin:20px 0 10px;
}

input,
select,
textarea{
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    margin:8px 0;
}

textarea{
    resize:none;
    height:80px;
}

/* PRODUCT ROW */

.product-row{
    display:flex;
    gap:10px;
    margin-bottom:12px;
    align-items:center;
}

.product-row select{
    flex:2;
}

.product-row input{
    flex:1;
}

.remove-btn{
    background:#dc3545;
    color:white;
    border:none;
    border-radius:8px;
    padding:12px;
    cursor:pointer;
}

.add-btn{
    background:#2196f3;
    color:white;
    border:none;
    padding:12px 18px;
    border-radius:8px;
    cursor:pointer;
    margin-top:10px;
}

.submit-btn{
    width:100%;
    background:#00c853;
    color:white;
    border:none;
    padding:14px;
    border-radius:10px;
    margin-top:20px;
    font-size:16px;
    cursor:pointer;
}

/* TABLE */

.table-container{
    width:95%;
    margin:25px auto;
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    background:rgba(255,255,255,0.12);
}

th,td{
    padding:12px;
    text-align:center;
    border:1px solid rgba(255,255,255,0.1);
}

th{
    background:rgba(0,0,0,0.3);
}

table img{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:8px;
}

/* ANIMATION */

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

<h2>🛒 Purchase</h2>

<div class="buttons">

<button
class="btn low-btn"
onclick="toggleAlert()">
⚠ Low Stock
</button>

<button
class="btn dashboard-btn"
onclick="location.href='admin_dashboard.php'">
⬅ Dashboard
</button>

</div>

</header>

<!-- LOW STOCK -->

<div class="alert-box" id="alertBox">

<h3>⚠ Low Stock Products</h3>

<br>

<div class="low-stock-container">

<?php while($p=$lowStock->fetch_assoc()): ?>

<div class="low-card">

<img src="uploads/<?php echo $p['image']; ?>">

<p>
<?php echo htmlspecialchars($p['name']); ?>
</p>

<p>
Stock:
<?php echo $p['quantity']; ?>
</p>

</div>

<?php endwhile; ?>

</div>

</div>

<!-- MESSAGE -->

<?php if($msg!=""): ?>

<div class="message">

<?php echo $msg; ?>

</div>

<?php endif; ?>

<!-- FORM -->

<form method="post">

<h3>📦 Products</h3>

<div id="productContainer">

<div class="product-row">

<select name="product_id[]" required>

<option value="">
-- Select Product --
</option>

<?php while($p=$products->fetch_assoc()): ?>

<option value="<?php echo $p['id']; ?>">

<?php
echo htmlspecialchars($p['name'])
." (Stock: ".$p['quantity'].")";
?>

</option>

<?php endwhile; ?>

</select>

<input
type="number"
name="quantity[]"
placeholder="Quantity"
min="1"
required
>

<button
type="button"
class="remove-btn"
onclick="removeRow(this)">
❌
</button>

</div>

</div>

<button
type="button"
class="add-btn"
onclick="addProductRow()">
➕ Add Product
</button>

<!-- VENDOR -->

<h3>🏪 Vendor Details</h3>

<input
type="text"
name="vendor_name"
placeholder="Vendor Name"
required
>

<input
type="text"
name="vendor_phone"
placeholder="Vendor Phone"
>

<textarea
name="vendor_address"
placeholder="Vendor Address">
</textarea>

<!-- PAYMENT -->

<h3>💳 Payment Details</h3>

<select name="payment_method" required>

<option value="">
-- Payment Method --
</option>

<option value="Cash">Cash</option>
<option value="eSewa">eSewa</option>
<option value="Khalti">Khalti</option>
<option value="Bank Transfer">Bank Transfer</option>

</select>

<select name="payment_status" required>

<option value="">
-- Payment Status --
</option>

<option value="Paid">Paid</option>
<option value="Credit">Credit</option>

</select>

<button
type="submit"
name="purchase"
class="submit-btn">
✅ Record Purchase
</button>

</form>

<!-- RECENT PURCHASES -->

<h3 style="text-align:center;">
📋 Recent Purchases
</h3>

<div class="table-container">

<table>

<tr>

<th>No</th>
<th>Product</th>
<th>Vendor</th>
<th>Image</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
<th>Status</th>
<th>Date</th>

</tr>

<?php $count=1; ?>

<?php while($row=$recent->fetch_assoc()): ?>

<tr>

<td><?php echo $count++; ?></td>

<td>
<?php echo $row['product_name']; ?>
</td>

<td>
<?php echo $row['vendor_name']; ?>
</td>

<td>
<img src="uploads/<?php echo $row['product_image']; ?>">
</td>

<td><?php echo $row['quantity']; ?></td>

<td>
Rs <?php echo $row['price']; ?>
</td>

<td>
Rs <?php
echo $row['quantity'] * $row['price'];
?>
</td>

<td>
<?php echo $row['payment_status']; ?>
</td>

<td>
<?php echo $row['purchase_date']; ?>
</td>

</tr>

<?php endwhile; ?>

</table>

</div>

<script>

/* LOW STOCK */

function toggleAlert(){

    let box =
    document.getElementById('alertBox');

    box.style.display =
    box.style.display === 'block'
    ? 'none'
    : 'block';
}

/* ADD PRODUCT ROW */

function addProductRow(){

    let container =
    document.getElementById(
        'productContainer'
    );

    let row =
    document.querySelector(
        '.product-row'
    );

    let clone =
    row.cloneNode(true);

    clone.querySelector(
        'select'
    ).selectedIndex = 0;

    clone.querySelector(
        'input'
    ).value = '';

    container.appendChild(clone);
}

/* REMOVE ROW */

function removeRow(btn){

    let rows =
    document.querySelectorAll(
        '.product-row'
    );

    if(rows.length > 1){

        btn.parentElement.remove();
    }
}

</script>

</body>
</html>