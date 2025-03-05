function loadMessages() {
    const receiverId = <?= isset($receiver_id) ? $receiver_id : 0 ?>; 
    fetch(`get_messages.php?receiver_id=${receiverId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('chat-box').innerHTML = data;
            // Прокручиваем чат вниз
            const chatBox = document.getElementById('chat-box');
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

setInterval(loadMessages, 5000);

document.getElementById('send-btn').addEventListener('click', () => {
    const message = document.getElementById('message').value;
    const receiverId = <?= isset($receiver_id) ? $receiver_id : 0 ?>; 

    fetch('send_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `receiver_id=${receiverId}&message=${encodeURIComponent(message)}`,
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
        loadMessages();
    });
});