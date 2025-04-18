<?php
session_start();

if (!isset($_SESSION['professor_id'])) {
    header("Location: ../_FRONT_END/login.php");
    exit();
}

require '../conn.php';

$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = '';
if (isset($_GET['search'])) {
    $search = htmlspecialchars(trim($_GET['search']));
}

$sql = "SELECT * FROM livros WHERE titulo LIKE ? OR isbn LIKE ? ORDER BY titulo ASC LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($sql);
$searchTerm = "%$search%";
$stmt->bind_param('ss', $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$sql_total = "SELECT COUNT(*) as total FROM livros WHERE titulo LIKE ? OR isbn LIKE ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param('ss', $searchTerm, $searchTerm);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_books = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_books / $limit);
?>