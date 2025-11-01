<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
$me = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');

// logout handling
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>AplChat - <?php echo $me; ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app">
  <header>
    <h1>AplChat</h1>
    <div class="hdr-right">
      <span><?php echo $me; ?></span>
      <a class="link" href="index.php?logout=1">Logout</a>
    </div>
  </header>

  <main>
    <aside class="sidebar">
      <h3>Mulai chat</h3>
      <input id="recipient" placeholder="Masukkan username tujuan">
      <button id="startBtn">Start Chat</button>
      <hr>
      <h4>Daftar pengguna</h4>
      <ul id="userList"></ul>
    </aside>

    <section class="chat-area">
      <div id="chatHeader">Pilih penerima untuk mulai chat</div>
      <div id="messages" class="messages"></div>
      <form id="sendForm" class="send-form" onsubmit="return false;">
        <input id="messageInput" placeholder="Tulis pesan..." autocomplete="off">
        <button id="sendBtn">Kirim</button>
      </form>
    </section>
  </main>
</div>

<script>
  const ME = "<?php echo $me; ?>";
</script>
<script src="chat.js"></script>
</body>
</html>
