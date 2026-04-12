<?php
require_once __DIR__ . '/config.php';

function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => false, // set true in production with HTTPS
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
    }
}

function isLoggedIn(): bool {
    startSession();
    return isset($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . APP_URL . '/pages/login.php');
        exit;
    }
}

function currentUser(): array {
    startSession();
    return [
        'id'     => $_SESSION['user_id'] ?? null,
        'name'   => $_SESSION['user_name'] ?? '',
        'email'  => $_SESSION['user_email'] ?? '',
        'avatar' => $_SESSION['user_avatar'] ?? '🧑',
    ];
}

function loginUser(string $email, string $password): array {
    $db   = getDB();
    $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }

    startSession();
    session_regenerate_id(true);
    $_SESSION['user_id']     = $user['id'];
    $_SESSION['user_name']   = $user['name'];
    $_SESSION['user_email']  = $user['email'];
    $_SESSION['user_avatar'] = $user['avatar'];

    return ['success' => true];
}

function registerUser(string $name, string $email, string $password): array {
    if (strlen($name) < 2)        return ['success' => false, 'message' => 'Name must be at least 2 characters.'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return ['success' => false, 'message' => 'Invalid email address.'];
    if (strlen($password) < 6)    return ['success' => false, 'message' => 'Password must be at least 6 characters.'];

    $db   = getDB();
    $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) return ['success' => false, 'message' => 'Email already registered.'];

    $avatars = ['🧑','👩','🧔','👨','🧕','👱'];
    $avatar  = $avatars[array_rand($avatars)];
    $hash    = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $db->prepare('INSERT INTO users (name, email, password, avatar) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $email, $hash, $avatar]);
    $userId = $db->lastInsertId();

    // Create default goals
    $db->prepare('INSERT INTO goals (user_id) VALUES (?)')->execute([$userId]);

    return ['success' => true, 'user_id' => $userId];
}

function logoutUser(): void {
    startSession();
    session_destroy();
    header('Location: ' . APP_URL . '/pages/login.php');
    exit;
}

function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
