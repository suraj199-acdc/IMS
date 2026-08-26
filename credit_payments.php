<?php
session_start();

$conn = new mysqli("localhost","root","1234","ims");
if($conn->connect_error) die("DB Connection Error");

if(!isset($_SESSION['user_id'])){
    header("Location:index.php");
    exit;
}

$msg = "";

/* =========================
   UPDATE FULL PURCHASE
========================= */

if(isset($_POST['update'])){

    $id = (int)$_POST['id'];

    $vendor_name    = trim($_POST['vendor_name']);
    $vendor_phone   = trim($_POST['vendor_phone']);
    $vendor_address = trim($_POST['vendor_address']);

    $payment_method = $_POST['payment_method'];
    $payment_status = $_POST['payment_status'];

    $paid_amount = (float)$_POST['paid_amount'];

    $row = $conn->query("SELECT * FROM purchases WHERE id=$id")->fetch_assoc();

    if($row){

        $total = $row['quantity'] * $row['price'];

        // Clamp paid amount
        if($paid_amount > $total) $paid_amount = $total;
        if($paid_amount < 0) $paid_amount = 0;

        $due = $total - $paid_amount;

        // Status logic
        if($due == 0){
            $payment_status = "Paid";
        }elseif($paid_amount > 0){
            $payment_status = "Partial";
        }else{
            $payment_status = "Credit";
        }

        $stmt = $conn->prepare("
            UPDATE purchases SET
                vendor_name=?,
                vendor_phone=?,
                vendor_address=?,
                payment_method=?,
                payment_status=?,
                paid_amount=?,
                due_amount=?
            WHERE id=?
        ");

        $stmt->bind_param(
            "sssssddi",
            $vendor_name,
            $vendor_phone,
            $vendor_address,
            $payment_method,
            $payment_status,
            $paid_amount,
            $due,
            $id
        );

        $stmt->execute();

        $msg = "✅ Purchase Updated Successfully!";
    }
}

/* =========================
   DATA
========================= */

$purchases = $conn->query("
    SELECT * FROM purchases
    ORDER BY purchase_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>

<title>Credit & Payment Manager</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>

body{
    font-family:Poppins;
    background:linear-gradient(135deg,#667eea,#764ba2);
    color:white;
}

header{
    display:flex;
    justify-content:space-between;
    padding:15px;
}

a{
    background:#ffeb3b;
    padding:8px 15px;
    text-decoration:none;
    color:black;
    border-radius:8px;
}

table{
    width:95%;
    margin:20px auto;
    border-collapse:collapse;
    background:rgba(255,255,255,0.1);
}

th,td{
    padding:10px;
    border:1px solid rgba(255,255,255,0.2);
    text-align:center;
}

input,select,textarea{
    width:100%;
    padding:6px;
    border-radius:6px;
    border:none;
    margin:2px 0;
}

button{
    padding:6px 10px;
    border:none;
    border-radius:6px;
    background:#00c853;
    color:white;
    cursor:pointer;
}

.msg{
    text-align:center;
    font-weight:bold;
    margin:10px;
}

.paid{color:#00ff99;font-weight:bold;}
.partial{color:#ffeb3b;font-weight:bold;}
.credit{color:#ff5252;font-weight:bold;}

</style>
</head>

<body>

<header>
<h2>💰 Credit / Purchase </h2>
<a href="admin_dashboard.php">⬅ Dashboard</a>
</header>

<?php if($msg!=""): ?>
<div class="msg"><?php echo $msg; ?></div>
<?php endif; ?>

<table>

<tr>
<th>Product</th>
<th>Vendor</th>
<th>Payment Method</th>
<th>Status</th>
<th>Paid</th>
<th>Due</th>
<th>Edit Full Details</th>
</tr>

<?php while($r=$purchases->fetch_assoc()): ?>

<tr>

<td><?php echo $r['product_name']; ?></td>

<td>
<?php echo $r['vendor_name']; ?><br>
<?php echo $r['vendor_phone']; ?>
</td>

<td><?php echo $r['payment_method']; ?></td>

<td>
<span class="<?php echo strtolower($r['payment_status']); ?>">
<?php echo $r['payment_status']; ?>
</span>
</td>

<td>Rs <?php echo $r['paid_amount']; ?></td>
<td>Rs <?php echo $r['due_amount']; ?></td>

<td>

<form method="post">

<input type="hidden" name="id" value="<?php echo $r['id']; ?>">

<input type="text" name="vendor_name" value="<?php echo $r['vendor_name']; ?>" placeholder="Vendor Name">

<input type="text" name="vendor_phone" value="<?php echo $r['vendor_phone']; ?>" placeholder="Phone">

<textarea name="vendor_address"><?php echo $r['vendor_address']; ?></textarea>

<select name="payment_method">

<option <?php if($r['payment_method']=="Cash") echo "selected"; ?>>Cash</option>
<option <?php if($r['payment_method']=="eSewa") echo "selected"; ?>>eSewa</option>
<option <?php if($r['payment_method']=="Khalti") echo "selected"; ?>>Khalti</option>
<option <?php if($r['payment_method']=="Bank Transfer") echo "selected"; ?>>Bank Transfer</option>

</select>

<select name="payment_status">

<option <?php if($r['payment_status']=="Paid") echo "selected"; ?>>Paid</option>
<option <?php if($r['payment_status']=="Partial") echo "selected"; ?>>Partial</option>
<option <?php if($r['payment_status']=="Credit") echo "selected"; ?>>Credit</option>

</select>

<input type="number"
name="paid_amount"
step="0.01"
value="<?php echo $r['paid_amount']; ?>">

<button type="submit" name="update">
Update All
</button>

</form>

</td>

</tr>

<?php endwhile; ?>

</table>

</body>
</html>