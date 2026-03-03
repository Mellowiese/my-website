<?php
// ============================================================
// login.php — php/login.php
// Authenticates staff and starts session
// ============================================================

session_start();
require_once 'db.php';

// Already logged in? Go straight to dashboard
if (isset($_SESSION['staff'])) {
    header('Location: ../html/dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    if (empty($email) || empty($password)) {
        redirect_error('Please enter both email and password.');
    }

    $pdo  = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM staff WHERE Staff_Email = :e LIMIT 1");
    $stmt->execute([':e' => $email]);
    $staff = $stmt->fetch();

    if ($staff && password_verify($password, $staff['Staff_Password'])) {
        $_SESSION['staff'] = [
            'id'       => $staff['Staff_ID'],
            'name'     => $staff['Staff_Name'],
            'position' => $staff['Staff_Position'],
            'email'    => $staff['Staff_Email'],
        ];
        header('Location: ../html/dashboard.php');
        exit;
    } else {
        redirect_error('Invalid email or password.');
    }
} else {
    header('Location: ../html/login.html');
    exit;
}

function redirect_error($msg) {
    header('Location: ../html/login.html?error=' . urlencode($msg));
    exit;
}