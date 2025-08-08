<?php
header('Content-Type: application/json');
require '../config/db.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'getAll') {
    try {
        $stmt = $pdo->query('SELECT * FROM products');
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($products);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal mengambil data produk']);
    }
} elseif ($action === 'add') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['name'], $data['price'], $data['description'], $data['seller'], $data['whatsapp'], $data['icon'])) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit;
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO products (name, price, description, seller, whatsapp, icon) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['name'],
            $data['price'],
            $data['description'],
            $data['seller'],
            $data['whatsapp'],
            $data['icon']
        ]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan produk']);
    }
}
?>