<?php
$conn = new mysqli("localhost","root","","ims");

$data = $conn->query("
SELECT i.*, p.name 
FROM invoices i
JOIN products p ON i.product_id=p.id
ORDER BY i.id DESC
");
?>

<h2>All Invoices</h2>

<table border="1" cellpadding="10">
<tr>
<th>ID</th>
<th>Product</th>
<th>Qty</th>
<th>Total</th>
<th>Type</th>
<th>Date</th>
</tr>

<?php while($row = $data->fetch_assoc()): ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['quantity']; ?></td>
<td><?php echo $row['total_amount']; ?></td>
<td><?php echo $row['type']; ?></td>
<td><?php echo $row['created_at']; ?></td>
</tr>
<?php endwhile; ?>
</table>