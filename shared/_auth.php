<?php
include __DIR__ . '/_data_base.php';
include __DIR__ . '/_helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
        'cookie_secure'   => true,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
        'use_strict_mode' => true,
    ]);
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function validateCsrf() {
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || $token !== ($_SESSION['csrf_token'] ?? '')) {
        throw new Exception("Critical Security Error (CSRF).");
    }
}

function register() {
    $errors = [];
    $old = [];
    
    try {
        if (isAuthenticated()) throw new Exception("You are already logged in.");
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            include __DIR__ . '/../auth/register.php';
            return;
        }
        validateCsrf();

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        $old = ['name' => $name, 'phone' => $phone, 'email' => $email];

        if (empty($name)) $errors['name'] = "Name is required";
        if (empty($phone)) $errors['phone'] = "Phone is required";
        if (empty($email)) $errors['email'] = "Email is required";
        if (empty($password)) $errors['password'] = "Password is required";
        if ($password !== $confirm_password) $errors['confirm_password'] = "Passwords do not match";

        if (!empty($errors)) throw new Exception("Please correct the errors in the form.");

        global $pdo;
        $stmt = $pdo->prepare("SELECT 1 FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = "Email already in use.";
            throw new Exception("Registration failed.");
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $email, $hashedPassword, $phone, UserRole::User->value]);

        return login($email, $password);

    } catch (Exception $e) {
        $errors['_general'] = $e->getMessage();
        include __DIR__ . '/../auth/register.php'; 
        exit;
    }
}

function login($email = null, $password = null) {
    $errors = [];
    $old = [];

    try {
        if (isAuthenticated()) throw new Exception("You are already logged in.");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $email = trim($_POST['email'] ?? $email);
            $password = $_POST['password'] ?? $password;
            $old['email'] = $email;
        }

        if (empty($email)) $errors['email'] = "Email is required";
        if (empty($password)) $errors['password'] = "Password is required";
        
        if (!empty($errors)) throw new Exception("Validation failed");

        global $pdo;
        $sql = "SELECT id, password, role FROM users WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
    
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['is_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = UserRole::from($user['role']);
            
            session_regenerate_id(true);
            header('Location: ../index.php');
            exit;
        } else {
            $errors['_general'] = "Invalid email or password";
            throw new Exception("Auth failed");
        }
    }
    catch (Exception $e) {
        if (empty($errors['_general'])) $errors['_general'] = $e->getMessage();
        include __DIR__ . '/../auth/login.php';
        exit;
    }
}

function logout() {
    if (!isAuthenticated()) {
        header('Location: ../index.php');
        return;
    }
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header('Location: ../index.php');
    exit;
}

$action = $_GET['action'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $action === 'logout') {
    switch ($action) {
        case 'login':    login(); break;
        case 'register': register(); break;
        case 'logout':   logout(); break;
        default:
            header('Location: /404.php');
            exit;
    }
}