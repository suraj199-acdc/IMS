<?php 
session_start();
$conn = new mysqli("localhost","root","1234","ims");
if($conn->connect_error) die("DB Connection Error");

$msg = "";
$success = $error = "";

// Handle form submission
if(isset($_POST['add_product'])){
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $quantity = trim($_POST['quantity']);
    $department = trim($_POST['department']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);


    // Handle image upload
    $image = null;
    if(isset($_FILES['image']) && $_FILES['image']['name'] != ''){
        $target_dir = "uploads/";
        if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $image = time().'_'.basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;

        if(!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)){
            $error = "Failed to upload image.";
        }
    }

    if(!$error){
        // Check if product already exists
        $check = $conn->prepare("SELECT id, quantity FROM products WHERE name=?");
        $check->bind_param("s", $name);
        $check->execute();
        $result = $check->get_result();

        if($result->num_rows > 0){
            // Product exists → update stock
            $row = $result->fetch_assoc();
            $new_qty = $row['quantity'] + $quantity;

            $update = $conn->prepare("UPDATE products SET quantity=? WHERE id=?");
            $update->bind_param("ii", $new_qty, $row['id']);

            if($update->execute()){
                $success = "Stock updated! New quantity: " . $new_qty;
            } else {
                $error = "Error updating stock!";
            }

        } else {
            // New product → insert
            $stmt = $conn->prepare("INSERT INTO products 
                (name, price, quantity, department, category, description, image, reorder_level) 
                VALUES (?,?,?,?,?,?,?,?)");

            $stmt->bind_param(
                "sddssssi",
                $name, 
                $price, 
                $quantity, 
                $department, 
                $category,
                $description,
                $image,
                $reorder_level
            );

            if($stmt->execute()){
                $success = "Product added successfully!";
            } else {
                $error = "Database error: ".$stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Product</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
body {
    font-family:'Poppins',sans-serif;
    background: linear-gradient(135deg,#667eea,#764ba2,#42a5f5,#7b1fa2);
    background-size:400% 400%;
    animation: gradientBG 15s ease infinite;
    margin:0;
    padding:0;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
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
    margin-bottom:20px;
    color:#333;
}

input, textarea {
    width:100%;
    padding:12px;
    margin:8px 0;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:1rem;
}

input:focus, textarea:focus {
    border-color:#667eea;
    outline:none;
    box-shadow:0 0 5px rgba(102,126,234,0.5);
}

button {
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    background:#28a745;
    color:white;
    font-size:1rem;
    cursor:pointer;
    margin-top:10px;
    transition:0.3s;
}

button:hover {
    background:#218838;
    transform:scale(1.02);
}

.back-btn {
    background:#2575fc;
    margin-top:10px;
}

.back-btn:hover {
    background:#1d5bcf;
    transform:scale(1.02);
}

.success-msg {color:green; text-align:center; margin-bottom:10px;}
.error-msg {color:red; text-align:center; margin-bottom:10px;}
.image-preview {width:100px; height:100px; object-fit:cover; margin-top:10px; border-radius:8px;}
label {font-weight:500;}
</style>
</head>
<body>

<div class="container">
    <h2>Add Product</h2>

    <?php if($success) echo "<div class='success-msg'>$success</div>"; ?>
    <?php if($error) echo "<div class='error-msg'>$error</div>"; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="number" step="0.01" name="price" placeholder="Price" required>
        <input type="number" name="quantity" placeholder="Quantity" min="0" required>
        <input type="text" name="department" placeholder="Department" required>
        <input type="text" name="category" placeholder="Category" required>
        <textarea name="description" placeholder="Product Description" rows="4"></textarea>
        <label>Product Image:</label>
        <input type="file" name="image" accept="image/*" onchange="previewImage(event)">
        <img id="imgPreview" class="image-preview" style="display:none;">
        <button type="submit" name="add_product">Add Product</button>
    </form>

    <button class="back-btn" onclick="location.href='admin_dashboard.php'">⬅ Back to Dashboard</button>
</div>

<script>
function previewImage(event){
    var reader = new FileReader();
    reader.onload = function(){
        var output = document.getElementById('imgPreview');
        output.src = reader.result;
        output.style.display = 'block';
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>