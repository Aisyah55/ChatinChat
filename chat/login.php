<?php
session_start();
require_once 'db.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $err = 'Isi username & password.';
    } else {
        $stmt = $conn->prepare('SELECT password FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $username;
                header('Location: index.php');
                exit;
            } else {
                $err = 'Username atau password salah.';
            }
        } else {
            $err = 'Username atau password salah.';
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Login - AplChat</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-box">
  <h2>Login</h2>
  <?php if ($err): ?><div class="error"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
  <form method="post">
    <input name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>
  <p>Belum punya akun? <a href="register.php">Daftar</a></p>
</div>
</body>
</html>
