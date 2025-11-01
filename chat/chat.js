// chat.js - polling 1 detik
let recipient = null;
let lastId = 0;
let polling = null;

const messagesEl = document.getElementById('messages');
const recipientInput = document.getElementById('recipient');
const startBtn = document.getElementById('startBtn');
const sendForm = document.getElementById('sendForm');
const messageInput = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');
const chatHeader = document.getElementById('chatHeader');
const userList = document.getElementById('userList');

startBtn.addEventListener('click', () => {
  const val = recipientInput.value.trim();
  if (!val) return alert('Masukkan username tujuan.');
  if (val === ME) return alert('Tidak bisa chat dengan diri sendiri.');
  recipient = val;
  lastId = 0;
  messagesEl.innerHTML = '';
  chatHeader.textContent = 'Chat dengan: ' + recipient;
  if (polling) clearInterval(polling);
  polling = setInterval(fetchMessages, 1000);
  fetchMessages();
});

sendForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  if (!recipient) return alert('Pilih penerima dulu.');
  const text = messageInput.value.trim();
  if (!text) return;
  sendBtn.disabled = true;
  try {
    const res = await fetch('send.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({receiver: recipient, message: text})
    });
    const j = await res.json();
    if (j.success) {
      messageInput.value = '';
      appendMessage({id_pesan: j.id_pesan, sender: ME, message: text, created_at: j.created_at}, true);
    } else {
      alert(j.error || 'Gagal mengirim');
    }
  } catch (err) {
    console.error(err); alert('Error saat mengirim');
  } finally { sendBtn.disabled = false; }
});

async function fetchMessages() {
  if (!recipient) return;
  try {
    const res = await fetch(`fetch.php?with=${encodeURIComponent(recipient)}&last=${lastId}`);
    const j = await res.json();
    if (Array.isArray(j)) {
      j.forEach(m => {
        appendMessage(m, m.sender === ME);
        if (m.id_pesan > lastId) lastId = m.id_pesan;
      });
    }
  } catch (err) {
    console.error('fetchMessages error', err);
  }
}

function appendMessage(m, isMe) {
  if (document.getElementById('msg-' + m.id_pesan)) return;
  const div = document.createElement('div');
  div.className = 'message ' + (isMe ? 'me' : 'them');
  div.id = 'msg-' + m.id_pesan;
  const time = new Date(m.created_at).toLocaleTimeString();
  div.innerHTML = `<div class="meta"><small>${escapeHtml(m.sender)} · ${time}</small></div><div class="text">${escapeHtml(m.message)}</div>`;
  messagesEl.appendChild(div);
  messagesEl.scrollTop = messagesEl.scrollHeight;
}

function escapeHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}

// load users
async function loadUsers() {
  try {
    const res = await fetch('fetch.php?users=1');
    const j = await res.json();
    userList.innerHTML = '';
    j.filter(u => u.username !== ME).forEach(u => {
      const li = document.createElement('li');
      li.textContent = u.username;
      li.addEventListener('click', () => { recipientInput.value = u.username; startBtn.click(); });
      userList.appendChild(li);
    });
  } catch (e) { console.error(e); }
}

loadUsers();
setInterval(loadUsers, 5000);
