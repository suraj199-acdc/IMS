<?php 
session_start();
$conn = new mysqli("localhost","root","1234","ims");
if($conn->connect_error) die("DB Connection Error");

$msg = "";
$conn = new mysqli("localhost","root","1234","ims");
if($conn->connect_error) die("DB Connection Error");

$id = $_GET['id'];

// Fetch existing product
$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

$success = $error = "";

// Handle form submission
if(isset($_POST['update'])){
    $name = $_POST['name'] ?? '';
    $department = $_POST['department'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? 0;
    $quantity = $_POST['quantity'] ?? 0;

    // Handle image upload
    $image = $product['image'] ?? ''; // keep old image by default
    if(isset($_FILES['image']) && $_FILES['image']['name'] != ''){
        $target_dir = "uploads/";
        $image = time().'_'.basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        if(!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)){
            $error = "Failed to upload new image.";
        }
    }

    if(!$error){
        $update = $conn->prepare("UPDATE products SET 
            name=?, department=?, category=?, price=?, quantity=?, image=? WHERE id=?");
        $update->bind_param("sssdisi", $name, $department, $category, $price, $quantity, $image, $id);
        if($update->execute()){
            $success = "Product updated successfully!";
            header("Location: inventory.php");
            exit;
        } else {
            $error = "Database error: ".$update->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Product</title>
<style>
body{font-family:'Poppins';background:#f4f6fb;}
.container{max-width:500px;margin:40px auto;background:white;padding:30px;border-radius:15px;box-shadow:0 8px 18px rgba(0,0,0,0.1);}
input, select, textarea{width:100%;padding:10px;margin:10px 0;border-radius:8px;border:1px solid #ccc;}
button{width:100%;padding:12px;border:none;border-radius:8px;background:#2575fc;color:white;font-weight:bold;cursor:pointer;}
button:hover{background:#1d5bcf;}
img{width:150px;height:150px;object-fit:cover;margin-bottom:10px;border-radius:8px;}
.success{background:#28a745;color:white;padding:10px;text-align:center;margin-bottom:10px;border-radius:8px;}
.error{background:#dc3545;color:white;padding:10px;text-align:center;margin-bottom:10px;border-radius:8px;}
.back-btn{margin:20px auto; display:block; padding:10px 20px; background:#2575fc;color:white;border:none;border-radius:8px; cursor:pointer;}
.back-btn:hover{background:#1d5bcf;}
</style>
</head>
<body>

<div class="container">
<h2>Edit Product</h2>

<?php if($success) echo "<div class='success'>$success</div>"; ?>
<?php if($error) echo "<div class='error'>$error</div>"; ?>

<form method="POST" enctype="multipart/form-data">
<label>Product Name</label>
<input type="text" name="name" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>

<label>Department</label>
<input type="text" name="department" value="<?php echo htmlspecialchars($product['department'] ?? ''); ?>" required>

<label>Category</label>
<input type="text" name="category" value="<?php echo htmlspecialchars($product['category'] ?? ''); ?>" required>

<label>Price</label>
<input type="number" step="0.01" name="price" value="<?php echo $product['price'] ?? 0; ?>" required>

<label>Quantity</label>
<input type="number" name="quantity" value="<?php echo $product['quantity'] ?? 0; ?>" required>

<label>Description</label>
<textarea name="description"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>

<label>Current Image</label><br>
<img src="uploads/<?php echo $product['image'] ?? 'default.png'; ?>" alt="Product Image"><br>

<label>Change Image</label>
<input type="file" name="image" accept="image/*">

<button type="submit" name="update">Update Product</button>
</form>
</div>

<button class="back-btn" onclick="location.href='admin_dashboard.php'">⬅ Back to Dashboard</button>

</body>
</html>
