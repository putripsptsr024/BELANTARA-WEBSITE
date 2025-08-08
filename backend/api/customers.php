<?php
header('Content-Type: application/json');
require '../config/db.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'getAll') {
    try {
        $stmt = $pdo->query('SELECT id, name, phone, email, join_date FROM customers');
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($customers);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal mengambil data pelanggan']);
    }
} elseif ($action === 'register') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['name'], $data['phone'], $data['email'], $data['password'])) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit;
    }

    try {
        $stmt = $pdo->prepare('SELECT id FROM customers WHERE email = ? OR phone = ?');
        $stmt->execute([$data['email'], $data['phone']]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email atau nomor HP sudah terdaftar']);
            exit;
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO customers (name, phone, email, password) VALUES (?, ?, ?, ?)');
        $stmt->execute([$data['name'], $data['phone'], $data['email'], $hashedPassword]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal mendaftar']);
    }
} elseif ($action === 'login') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['email'], $data['password'], $data['userType'])) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit;
    }

    try {
        if ($data['userType'] === 'admin' && $data['email'] === 'admin' && $data['password'] === 'admin123') {
            echo json_encode(['success' => true, 'user' => ['id' => 0, 'name' => 'Administrator', 'type' => 'admin', 'email' => 'admin']]);
            exit;
        }

        $stmt = $pdo->prepare('SELECT * FROM customers WHERE email = ?');
        $stmt->execute([$data['email']]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($customer && password_verify($data['password'], $customer['password'])) {
            echo json_encode(['success' => true, 'user' => [
                'id' => $customer['id'],
                'name' => $customer['name'],
                'phone' => $customer['phone'],
                'email' => $customer['email'],
                'type' => 'customer'
            ]]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Email atau password salah']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal login']);
    }
}
?>