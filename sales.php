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
   SEARCH SALES
========================= */
$search = $_GET['search'] ?? '';
$search_query = '';

if($search){
    $search_safe = $conn->real_escape_string($search);
    $search_query = "WHERE product_name LIKE '%$search_safe%' 
    OR customer_name LIKE '%$search_safe%'";
}

/* =========================
   MULTI SALE HANDLER
========================= */
if(isset($_POST['sale'])){

    $product_ids = $_POST['product_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_contact = $_POST['customer_contact'] ?? '';
    $payment_method = $_POST['payment_method'] ?? 'Cash';

    $success = 0;
    $failed = 0;

    for($i=0;$i<count($product_ids);$i++){

        $pid = (int)$product_ids[$i];
        $qty = (int)$quantities[$i];

        if($pid<=0 || $qty<=0) continue;

        $p = $conn->query("SELECT * FROM products WHERE id=$pid")->fetch_assoc();

        if($p && $p['quantity'] >= $qty){

            $new = $p['quantity'] - $qty;
            $conn->query("UPDATE products SET quantity=$new WHERE id=$pid");

            $stmt = $conn->prepare("
                INSERT INTO sales 
                (product_id,product_name,quantity,price,customer_name,customer_contact,product_image)
                VALUES (?,?,?,?,?,?,?)
            ");

            $stmt->bind_param(
                "isidsss",
                $pid,
                $p['name'],
                $qty,
                $p['price'],
                $customer_name,
                $customer_contact,
                $p['image']
            );

            $stmt->execute();
            $success++;

        } else {
            $failed++;
        }
    }

    $msg = "✅ Sale completed: $success success, $failed failed";
}

$products = $conn->query("SELECT * FROM products ORDER BY name ASC");
$recent = $conn->query("SELECT * FROM sales $search_query ORDER BY sale_date DESC LIMIT 20");
?>

<!DOCTYPE html>
<html>
<head>
<title>IMS POS</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Poppins;}

body{
background:linear-gradient(135deg,#667eea,#764ba2,#42a5f5,#7b1fa2);
background-size:400% 400%;
animation:bg 12s ease infinite;
color:white;
min-height:100vh;
}

@keyframes bg{
0%{background-position:0% 50%;}
50%{background-position:100% 50%;}
100%{background-position:0% 50%;}
}

header{
display:flex;
justify-content:space-between;
align-items:center;
padding:20px;
background:rgba(0,0,0,0.25);
}

form{
width:90%;
max-width:850px;
margin:20px auto;
background:rgba(255,255,255,0.12);
padding:20px;
border-radius:12px;
}

.row{
display:flex;
gap:10px;
align-items:center;
margin-bottom:10px;
}

select,input{
width:100%;
padding:8px;
border:none;
border-radius:6px;
outline:none;
}

.searchBox{
width:100%;
padding:6px;
margin-bottom:5px;
border-radius:6px;
border:none;
}

img{
width:60px;
height:60px;
object-fit:cover;
border-radius:6px;
}

button{
border:none;
padding:10px;
border-radius:6px;
cursor:pointer;
}

.add{background:#007bff;color:white;}
.submit{background:#28a745;color:white;width:100%;}

table{
width:95%;
margin:auto;
border-collapse:collapse;
background:rgba(255,255,255,0.1);
margin-top:20px;
}

th,td{
padding:10px;
border:1px solid rgba(255,255,255,0.2);
text-align:center;
}
</style>
</head>

<body>

<header>
<h2>$Sales</h2>
 <button class="back-btn" onclick="location.href='admin_dashboard.php'">⬅ Back to Dashboard</button>
</header>

<?php if($msg!="") echo "<p style='text-align:center;font-weight:bold;'>$msg</p>"; ?>

<!-- CART -->
<form method="post">

<div id="cart">

<div class="row">

<div style="width:100%">

<input type="text" class="searchBox" placeholder="Search product..." onkeyup="filter(this)">

<select name="product_id[]" onchange="update(this)" required>
<option value="">Select Product</option>
<?php
$products->data_seek(0);
while($p=$products->fetch_assoc()):
?>
<option value="<?php echo $p['id']; ?>"
data-img="uploads/<?php echo $p['image']; ?>"
data-price="<?php echo $p['price']; ?>">
<?php echo $p['name']." (Stock: ".$p['quantity'].")"; ?>
</option>
<?php endwhile; ?>
</select>

</div>

<input type="number" name="quantity[]" placeholder="Qty" min="1" required oninput="calc()">

<img class="preview" style="display:none">

</div>

</div>

<button type="button" class="add" onclick="addRow()">+ Add Product</button>

<h2 style="text-align:center;margin-top:10px;">
Total: Rs <span id="total">0.00</span>
</h2>

<!-- PAYMENT -->
<h3 style="text-align:center;">Payment Method</h3>

<select name="payment_method" required>
<option value="Cash">Cash</option>
<option value="Card">Card</option>
<option value="Mobile">Mobile Payment</option>
</select>

<input type="text" name="customer_name" placeholder="Customer Name" required>
<input type="text" name="customer_contact" placeholder="Contact" required>

<button class="submit" name="sale">Record Sales</button>

</form>

<!-- SEARCH -->
<form method="get">
<input type="text" name="search" placeholder="Search sales..." value="<?php echo htmlspecialchars($search); ?>">
<button>Search</button>
</form>

<!-- TABLE -->
<h3 style="text-align:center;">Sales Records</h3>

<table>
<tr>
<th>#</th>
<th>Product</th>
<th>Image</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
<th>Customer</th>
<th>Contact</th>
<th>Date</th>
</tr>

<?php $i=1; while($r=$recent->fetch_assoc()): ?>
<tr>
<td><?php echo $i++; ?></td>
<td><?php echo htmlspecialchars($r['product_name']); ?></td>
<td><img src="uploads/<?php echo $r['product_image']; ?>"></td>
<td><?php echo $r['quantity']; ?></td>
<td><?php echo $r['price']; ?></td>
<td><?php echo $r['price']*$r['quantity']; ?></td>
<td><?php echo htmlspecialchars($r['customer_name']); ?></td>
<td><?php echo htmlspecialchars($r['customer_contact']); ?></td>
<td><?php echo $r['sale_date']; ?></td>
</tr>
<?php endwhile; ?>

</table>

<script>

/* =========================
   FILTER
========================= */
function filter(input){
    let f = input.value.toLowerCase();
    let sel = input.parentNode.querySelector("select");

    for(let o of sel.options){
        o.style.display = o.text.toLowerCase().includes(f) ? "" : "none";
    }
}

/* =========================
   IMAGE + CALC
========================= */
function update(sel){
    let row = sel.closest(".row");
    let img = row.querySelector(".preview");

    let src = sel.selectedOptions[0]?.dataset.img;

    if(src){
        img.src = src;
        img.style.display = "block";
    }

    calc();
}

/* =========================
   FIXED LIVE CALCULATOR
========================= */
function calc(){

    let rows = document.querySelectorAll("#cart .row");
    let total = 0;

    rows.forEach(row=>{

        let sel = row.querySelector("select");
        let qty = row.querySelector("input[type='number']");

        let price = sel.selectedOptions[0]?.dataset.price;
        price = parseFloat(price) || 0;
        qty = parseFloat(qty.value) || 0;

        total += price * qty;
    });

    document.getElementById("total").innerText = total.toFixed(2);
}

/* =========================
   ADD ROW
========================= */
function addRow(){

    let cart = document.getElementById("cart");
    let row = cart.querySelector(".row").cloneNode(true);

    row.querySelector("select").value="";
    row.querySelector("input").value="";
    row.querySelector(".preview").style.display="none";
    row.querySelector(".searchBox").value="";

    cart.appendChild(row);
}

</script>

</body>
</html>