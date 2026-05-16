<?php
require_once __DIR__ . '/_data_base.php';
require_once __DIR__ . '/_helpers.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function validateCsrf(): void {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!$token || $token !== ($_SESSION['csrf_token'] ?? '')) {
        throw new Exception("Security error (CSRF). Please refresh and try again.");
    }
}

function register(): void {
    $errors = [];
    $old    = [];
    try {
        if (isAuthenticated()) {
            header('Location: ' . BASE_URL . 'index.php');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require __DIR__ . '/../auth/register.php';
            return;
        }
        validateCsrf();

        $name             = trim($_POST['name']             ?? '');
        $phone            = trim($_POST['phone']            ?? '');
        $email            = trim($_POST['email']            ?? '');
        $password         =      $_POST['password']         ?? '';
        $confirm_password =      $_POST['confirm_password'] ?? '';

        $old = compact('name', 'phone', 'email');

        if (empty($name))                           $errors['name']             = "Name is required";
        if (empty($phone))                          $errors['phone']            = "Phone is required";
        if (empty($email))                          $errors['email']            = "Email is required";
        if (empty($password))                       $errors['password']         = "Password is required";
        if ($password !== $confirm_password)        $errors['confirm_password'] = "Passwords do not match";
        if (!empty($errors)) throw new Exception("Please correct the errors below.");

        global $pdo;
        $stmt = $pdo->prepare("SELECT 1 FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = "Email already in use.";
            throw new Exception("Registration failed.");
        }

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?,?,?,?,?)");
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $phone, UserRole::User->value]);

        login($email, $password);

    } catch (Exception $e) {
        $errors['_general'] = $e->getMessage();
        require __DIR__ . '/../auth/register.php';
        exit;
    }
}

function login(?string $emailArg = null, ?string $passwordArg = null): void {
    $errors = [];
    $old    = [];
    try {
        if (isAuthenticated()) {
            header('Location: ' . BASE_URL . 'index.php');
            exit;
        }

        $email    = trim($_POST['email']    ?? $emailArg    ?? '');
        $password =      $_POST['password'] ?? $passwordArg ?? '';
        $old['email'] = $email;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') validateCsrf();

        if (empty($email))    $errors['email']    = "Email is required";
        if (empty($password)) $errors['password'] = "Password is required";
        if (!empty($errors))  throw new Exception("Validation failed");

        global $pdo;
        $stmt = $pdo->prepare("SELECT id, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['is_logged_in'] = true;
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['user_role']    = $user['role'];
            header('Location: ' . BASE_URL . 'index.php');
            exit;
        }

        $errors['_general'] = "Invalid email or password";
        throw new Exception("Auth failed");

    } catch (Exception $e) {
        if (empty($errors['_general'])) $errors['_general'] = $e->getMessage();
        require __DIR__ . '/../auth/login.php';
        exit;
    }
}

function logout(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
    }
    session_destroy();
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$action = $_GET['action'] ?? '';
switch ($action) {
    case 'login':    login();    break;
    case 'register': register(); break;
    case 'logout':   logout();   break;
}
