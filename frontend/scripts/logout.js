import { handleResponse } from './utils.js';

document.addEventListener("DOMContentLoaded", function () {
    fetch('http://localhost:8000/api/user/logout.php', {
        method: 'GET',
        credentials: "include",
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(handleResponse)
        .then(() => {window.location.href = '/frontend/public_html/index.html'})
});