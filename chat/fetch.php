<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'db.php';

if (isset($_GET['users'])) {
    // list users
    $res = $conn->query('SELECT username FROM users ORDER BY username ASC');
    $out = [];
    while ($r = $res->fetch_assoc()) $out[] = $r;
    echo json_encode($out);
    exit;
}

if (!isset($_SESSION['username'])) {
    echo json_encode([]);
    exit;
}
$me = $_SESSION['username'];
$with = isset($_GET['with']) ? trim($_GET['with']) : null;
$last = isset($_GET['last']) ? (int)$_GET['last'] : 0;

if (!$with) {
    echo json_encode([]);
    exit;
}

// ambil pesan antar me dan with yang id_pesan > last
$stmt = $conn->prepare('SELECT id_pesan, sender, receiver, message, created_at FROM pesan
    WHERE ((sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?)) AND id_pesan > ?
    ORDER BY id_pesan ASC LIMIT 500');
$stmt->bind_param('ssssi', $me, $with, $with, $me, $last);
$stmt->execute();
$res = $stmt->get_result();
$out = [];
while ($row = $res->fetch_assoc()) $out[] = $row;
echo json_encode($out);
