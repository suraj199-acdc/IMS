<?php
session_start();
$conn = new mysqli("localhost","root","","ims");
if(!isset($_SESSION['user_id'])) header("Location:index.php");

$msg = "";


$id = $_GET['id'];
$conn->query("DELETE FROM orders WHERE id='$id'");
header("Location: admin_view_orders.php");
exit;
?>
