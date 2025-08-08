<?php
header('Content-Type: application/json');
require '../config/db.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'getAll') {
    try {
        $stmt = $pdo->query('SELECT o.id, c.name as customer_name, o.total, o.status, o.order_date 
                            FROM orders o 
                            JOIN customers c ON o.customer_id = c.id');
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($orders as &$order) {
            $order['order_date'] = date('d/m/Y', strtotime($order['order_date']));
        }
        echo json_encode($orders);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal mengambil data pesanan']);
    }
} elseif ($action === 'create') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['customerId'], $data['items'], $data['total'])) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit;
    }

    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare('INSERT INTO orders (customer_id, total, status) VALUES (?, ?, ?)');
        $stmt->execute([$data['customerId'], $data['total'], 'Pending']);
        $orderId = $pdo->lastInsertId();

        $stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
        foreach ($data['items'] as $item) {
            $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
        }

        $stmt = $pdo->prepare('DELETE FROM cart WHERE customer_id = ?');
        $stmt->execute([$data['customerId']]);

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan pesanan']);
    }
} elseif ($action === 'getStats') {
    try {
        $stmt = $pdo->query('SELECT COUNT(*) as total FROM products');
        $totalProducts = $stmt->fetchColumn();

        $stmt = $pdo->query('SELECT COUNT(*) as total FROM orders WHERE DATE(order_date) = CURDATE()');
        $dailyOrders = $stmt->fetchColumn();

        $stmt = $pdo->query('SELECT COUNT(*) as total FROM customers');
        $totalCustomers = $stmt->fetchColumn();

        $stmt = $pdo->query('SELECT SUM(total) as total FROM orders WHERE MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())');
        $monthlyRevenue = $stmt->fetchColumn() ?: 0;

        echo json_encode([
            'totalProducts' => $totalProducts,
            'dailyOrders' => $dailyOrders,
            'totalCustomers' => $totalCustomers,
            'monthlyRevenue' => $monthlyRevenue
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal mengambil statistik']);
    }
}
?>