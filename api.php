<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "belantara_db");

if ($conn->connect_error) {
    die(json_encode(["error" => "Koneksi gagal: " . $conn->connect_error]));
}

$action = $_GET['action'] ?? $_POST['action'];

if ($action === 'get_products') {
    $result = $conn->query("SELECT * FROM products");
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    echo json_encode($products);
} elseif ($action === 'add_product' && isset($_POST['name'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $price = $conn->real_escape_string($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);
    $seller = $conn->real_escape_string($_POST['seller']);
    $whatsapp = $conn->real_escape_string($_POST['whatsapp']);
    $image = $_FILES['image']['name'] ? 'uploads/' . $_FILES['image']['name'] : '';

    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $_FILES['image']['name']);
    }

    $sql = "INSERT INTO products (name, price, description, seller, whatsapp, image) VALUES ('$name', '$price', '$description', '$seller', '$whatsapp', '$image')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Produk ditambahkan"]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal: " . $conn->error]);
    }
} elseif ($action === 'remove_product' && isset($_POST['id'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $sql = "DELETE FROM products WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Produk dihapus"]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal: " . $conn->error]);
    }
}

$conn->close();
?>