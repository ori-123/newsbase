import {handleResponse} from "./utils.js";
import {frontendLog} from "./log.js";

document.addEventListener("DOMContentLoaded", function () {
    fetch('http://localhost:8000/api/user/logout.php', {
        method: 'GET',
        credentials: "include",
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(handleResponse)
        .then(data => {
            frontendLog('info', data.message);
            // Redirect to login page
            window.location.href = 'index.html';
        })
        .catch(error => {
            frontendLog('error', error.message);
        });
});