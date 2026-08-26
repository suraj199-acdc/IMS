<?php 
session_start();
$conn = new mysqli("localhost","root","1234","ims");
if($conn->connect_error) die("DB Connection Error");
$q = $_GET['q'] ?? '';
$q = $conn->real_escape_string($q);
$data = [];
if($q){
    $res = $conn->query("SELECT id,name,price,quantity,image FROM products WHERE name LIKE '%$q%' LIMIT 10");
    while($r = $res->fetch_assoc()) $data[]=$r;
}
header('Content-Type: application/json');
echo json_encode($data);