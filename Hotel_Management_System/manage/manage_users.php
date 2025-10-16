<?php
require_once "../includes/db.php";
session_start();
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: /hotel_management_system/signin.php");
    exit;
}
$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);
if ($action && $id) {
    if ($action === 'block') {
        $stmt = $conn->prepare("UPDATE users SET status='blocked' WHERE id=?");
        $stmt->bind_param("i",$id); $stmt->execute(); $stmt->close();
    } elseif ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE users SET status='active' WHERE id=?");
        $stmt->bind_param("i",$id); $stmt->execute(); $stmt->close();
    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i",$id); $stmt->execute(); $stmt->close();
    }
}
include("../includes/header.php");?>
<div class="container">
<h2>Manage Users</h2>
<div class="list">
<table style="width:100%; border-collapse:collapse;">
<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>NID/Passport</th><th>Type</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
<tbody>
<?php
$res = $conn->query("SELECT id,fullname,email,usertype,status,nid_passport,created_at FROM users ORDER BY id DESC");
while ($u = $res->fetch_assoc()) {
    echo "<tr style='border-top:1px solid #eee;'>
      <td>{$u['id']}</td>
      <td>".htmlspecialchars($u['fullname'])."</td>
      <td>".htmlspecialchars($u['email'])."</td>
      <td>".htmlspecialchars($u['nid_passport'])."</td>
      <td>{$u['usertype']}</td>
      <td>{$u['status']}</td>
      <td>{$u['created_at']}</td>
      <td>";
    if ($u['status']=='active') echo "<a href='?action=block&id={$u['id']}'>Block</a> | ";
    else echo "<a href='?action=approve&id={$u['id']}'>Approve</a> | ";
    echo "<a href='?action=delete&id={$u['id']}' onclick='return confirm(\"Delete user?\")'>Delete</a></td></tr>";
}
?>
</tbody>
</table>
</div>
</div>
<?php include("../includes/footer.php"); ?>
