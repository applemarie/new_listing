
<?php
include("config.php");

class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "oop_php";
    private $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        $this->conn->close();
    }

    public function insertProduct($userId, $image, $product, $price, $description, $location) {
        $stmt = $this->conn->prepare("INSERT INTO product_sell (user_id, image, product, price, description, location) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssds", $userId, $image, $product, $price, $description, $location);
        
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Error: " . $stmt->error;
            return false;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $conn = $db->getConnection();

    $product = htmlspecialchars($_POST['product']);
    $price = floatval($_POST['price']);
    $description = htmlspecialchars($_POST['description']);
    $location = htmlspecialchars($_POST['location']);
    
    // Image upload handling
    $image = $_FILES['image']['name'];
    $target = "upload_storage/" . basename($image);
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        
        
        if (isset($_SESSION['userid'])) {
            $userId = $_SESSION['userid']; 
            
            if ($db->insertProduct($userId, $image, $product, $price, $description, $location)) {
                header("Location: profile.php");
                exit();
            } else {
                echo "Failed to insert product.";
            }
        } else {
            echo "User ID not found in session. Please check your login process.";
        }
    } else {
        echo "Failed to upload image.";
    }

    $db->closeConnection();
}
?>
