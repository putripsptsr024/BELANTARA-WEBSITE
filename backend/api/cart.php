<?php
header('Content-Type: application/json');
require '../config/db.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'add') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['customerId'], $data['productId'])) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit;
    }

    try {
        $stmt = $pdo->prepare('SELECT * FROM cart WHERE customer_id = ? AND product_id = ?');
        $stmt->execute([$data['customerId'], $data['productId']]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $pdo->prepare('UPDATE cart SET quantity = quantity + 1 WHERE customer_id = ? AND product_id = ?');
            $stmt->execute([$data['customerId'], $data['productId']]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO cart (customer_id, product_id, quantity) VALUES (?, ?, 1)');
            $stmt->execute([$data['customerId'], $data['productId']]);
        }

        $cart = getCart($pdo, $data['customerId']);
        echo json_encode(['success' => true, 'cart' => $cart]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal menambah ke keranjang']);
    }
} elseif ($action === 'remove') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['customerId'], $data['productId'])) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit;
    }

    try {
        $stmt = $pdo->prepare('DELETE FROM cart WHERE customer_id = ? AND product_id = ?');
        $stmt->execute([$data['customerId'], $data['productId']]);

        $cart = getCart($pdo, $data['customerId']);
        echo json_encode(['success' => true, 'cart' => $cart]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus dari keranjang']);
    }
} elseif ($action === 'update') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['customerId'], $data['productId'], $data['change'])) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit;
    }

    try {
        $stmt = $pdo->prepare('SELECT quantity FROM cart WHERE customer_id = ? AND product_id = ?');
        $stmt->execute([$data['customerId'], $data['productId']]);
        $current = $stmt->fetchColumn();

        if ($current) {
            $newQuantity = $current + $data['change'];
            if ($newQuantity <= 0) {
                $stmt = $pdo->prepare('DELETE FROM cart WHERE customer_id = ? AND product_id = ?');
                $stmt->execute([$data['customerId'], $data['productId']]);
            } else {
                $stmt = $pdo->prepare('UPDATE cart SET quantity = ? WHERE customer_id = ? AND product_id = ?');
                $stmt->execute([$newQuantity, $data['customerId'], $data['productId']]);
            }
        }

        $cart = getCart($pdo, $data['customerId']);
        echo json_encode(['success' => true, 'cart' => $cart]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui kuantitas']);
    }
}

function getCart($pdo, $customerId) {
    $stmt = $pdo->prepare('SELECT c.product_id as id, p.name, p.price, p.description, p.seller, p.whatsapp, p.icon, c.quantity 
                          FROM cart c 
                          JOIN products p ON c.product_id = p.id 
                          WHERE c.customer_id = ?');
    $stmt->execute([$customerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>