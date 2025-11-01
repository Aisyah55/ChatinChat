<?php
session_start();
require_once 'db.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($username === '' || $password === '' ) {
        $err = 'Isi semua kolom.';
    } elseif (!preg_match('/^[A-Za-z0-9_.-]{3,100}$/', $username)) {
        $err = 'Username 3-100 karakter (huruf, angka, _ . -).';
    } elseif ($password !== $password2) {
        $err = 'Password tidak cocok.';
    } else {
        // cek apakah username ada
        $stmt = $conn->prepare('SELECT id_users FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $err = 'Username sudah digunakan.';
        } else {
            // insert
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            $ins->bind_param('ss', $username, $hash);
            if ($ins->execute()) {
                $_SESSION['username'] = $username;
                header('Location: index.php');
                exit;
            } else {
                $err = 'Gagal mendaftar. Coba lagi.';
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Register - AplChat</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-box">
  <h2>Daftar</h2>
  <?php if ($err): ?><div class="error"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
  <form method="post">
    <input name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="password2" placeholder="Ulangi password" required>
    <button type="submit">Daftar</button>
  </form>
  <p>Sudah punya akun? <a href="login.php">Login</a></p>
</div>
</body>
</html>
