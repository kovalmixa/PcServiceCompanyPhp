<?php
include __DIR__ . '/_data_base.php';

session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure'   => true,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
]);

enum UserRole: int 
{
    case Admin = 1;
    case Staff = 2;
    case User = 3;
}

if (session_status() === PHP_SESSION_NONE) session_start();

function register(){
    if (isAuthenticated()) return false;
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        include 'login_view.php';
        return;
    }

    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT 1 FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) throw new Exception("Email already in use.");

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $email, $hashedPassword, $phone, $role->value]);

        return login($email, $password);
    } catch (Exception $e) {
        error_log($e->getMessage()); 
        return false;
    }
}

function login($email, $password) {
    if (isAuthenticated()) return false;
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
    }

    try {
        global $pdo;
        $sql = "SELECT id, password, role FROM users WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
    
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['is_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] =  UserRole::from($user['role']);
        } else throw new Exception("Email or password is incorrect.");
        session_regenerate_id(true);
        header('Location: index.php?action=dashboard');
        exit;
    }
    catch (Exception $e){
        $error = "Error: " . $e->getMessage();
        include 'login_view.php';
    }
}

function logoutUser() {
    if (!isAuthenticated()) return false;
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
?>