<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'db.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(['success'=>false,'error'=>'Not authenticated']);
    exit;
}
$me = $_SESSION['username'];

// expect JSON body
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || empty($data['receiver']) || empty(trim($data['message']))) {
    echo json_encode(['success'=>false,'error'=>'Invalid input']);
    exit;
}
$receiver = trim($data['receiver']);
$message = trim($data['message']);
if (mb_strlen($message) > 2000) {
    echo json_encode(['success'=>false,'error'=>'Pesan terlalu panjang']);
    exit;
}

$stmt = $conn->prepare('INSERT INTO pesan (sender, receiver, message) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $me, $receiver, $message);
if ($stmt->execute()) {
    $id = $stmt->insert_id;
    // return created_at
    $q = $conn->prepare('SELECT created_at FROM pesan WHERE id_pesan = ?');
    $q->bind_param('i', $id);
    $q->execute();
    $res = $q->get_result();
    $row = $res->fetch_assoc();
    echo json_encode(['success'=>true,'id_pesan'=>$id,'created_at'=>$row['created_at']]);
} else {
    echo json_encode(['success'=>false,'error'=>'DB error']);
}
