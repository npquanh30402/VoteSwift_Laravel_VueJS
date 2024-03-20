import './bootstrap';

const messagesElement = document.getElementById('messages');

const onlineUsers = document.getElementById('online-users');

Echo.join('chat')
    .here((users) => {
        users.forEach((user) => {
            let elem = document.createElement('li');
            elem.setAttribute('id', user.id);
            elem.innerHTML = `<a href="/user/profile/${user.id}" target="_blank">${user.name}</a>`
            onlineUsers.appendChild(elem);
        })
    })
    .joining((user) => {
        let elem = document.createElement('li');
        elem.setAttribute('id', user.id);
        elem.innerHTML = `<a href="/user/profile/${user.id}" target="_blank">${user.name}</a>`
        onlineUsers.appendChild(elem);
    })
    .leaving((user) => {
        const element = document.getElementById(user.id);

        element.parentNode.removeChild(element);
    })

Echo.private('chat.' + recipientId)
    .listen('MessageSent', (e) => {
        let formattedDate = new Date(e.messageObj.created_at).toLocaleString('en-GB', {
            timeZone: 'UTC',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
        let elem = document.createElement('li');
        elem.classList.add('vstack', 'list-group-item', 'mb-2');
        elem.innerHTML = `
    <div>
        <img src="${e.user.avatar}" alt="Avatar" class="img-fluid rounded-circle me-2" style="width: 30px; height: auto;">
        <strong>${e.user.username}</strong>: ${e.message ? e.message : ''}
    </div>
    <div class="d-block ms-auto">
        <span class="text-muted small">(${formattedDate})</span>
    </div>
`;


        if (e.filePath) {
            const fileExtension = e.filePath.split('.').pop().toLowerCase();

            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];

            if (imageExtensions.includes(fileExtension)) {
                elem.innerHTML += `<br><img src="${e.filePath}" alt="Image" class="img-fluid" style="width: 200px; height: auto;">`;
            } else {
                elem.innerHTML += `<br><a href="${e.filePath}" class="btn btn-primary" download>Download File: ${e.filePath.split('/').pop()}</a>`;
            }
        }
        messagesElement.appendChild(elem);
    });

Echo.private('chat.' + userId)
    .listen('MessageSent', (e) => {
        let formattedDate = new Date(e.messageObj.created_at).toLocaleString('en-GB', {
            timeZone: 'UTC',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
        let elem = document.createElement('li');
        elem.classList.add('vstack', 'list-group-item', 'mb-2');
        elem.innerHTML = `
    <div>
        <img src="${e.user.avatar}" alt="Avatar" class="img-fluid rounded-circle me-2" style="width: 30px; height: auto;">
        <strong>${e.user.username}</strong>: ${e.message ? e.message : ''}
    </div>
    <div class="d-block ms-auto">
        <span class="text-muted small">(${formattedDate})</span>
    </div>
`;

        if (e.filePath) {
            const fileExtension = e.filePath.split('.').pop().toLowerCase();

            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];

            if (imageExtensions.includes(fileExtension)) {
                elem.innerHTML += `<br><img src="${e.filePath}" alt="Image" class="img-fluid" style="width: 200px; height: auto;">`;
            } else {
                elem.innerHTML += `<br><a href="${e.filePath}" class="btn btn-primary" download>Download File: ${e.filePath.split('/').pop()}</a>`;
            }
        }
        messagesElement.appendChild(elem);
    });

const messageElement = document.getElementById('message');
const fileInput = document.getElementById('file');
const sendElement = document.getElementById('send');

sendElement.addEventListener('click', (e) => {
    e.preventDefault();

    const formData = new FormData();
    formData.append('message', messageElement.value);
    formData.append('file', fileInput.files[0]);

    window.axios.post('/user/chat/message/' + recipientId, formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    });

    messageElement.value = '';
    fileInput.value = '';
});
