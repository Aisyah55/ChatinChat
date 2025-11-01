const express = require("express");
const http = require("http");
const { Server } = require("socket.io");
const mysql = require("mysql2");

const app = express();
const server = http.createServer(app);
const io = new Server(server, { cors: { origin: "*" } });

const db = mysql.createConnection({
  host: "localhost",
  user: "root",
  password: "",
  database: "chatdb"
});

db.connect(err => {
  if (err) throw err;
  console.log("Terhubung ke database MySQL");
});

io.on("connection", (socket) => {
  console.log("User terhubung:", socket.id);

  socket.on("join", (username) => {
    console.log(username + " bergabung");
  });

  socket.on("chat message", (data) => {
    const { userId, username, msg } = data;

    // Simpan ke database
    db.query("INSERT INTO messages (sender_id, message) VALUES (?, ?)", [userId, msg]);

    io.emit("chat message", { username, msg });
  });
});

server.listen(3000, () => {
  console.log("Server berjalan di http://localhost:3000");
});
