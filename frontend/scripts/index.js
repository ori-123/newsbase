import {handleResponse} from "./utils.js";
import {frontendLog} from "./log.js";

document.addEventListener('DOMContentLoaded', function() {
    // Get forms from document
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    // Add submit event listener to login form
    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const username = document.getElementById('login-username').value;
        const password = document.getElementById('login-password').value;

        // Send request to backend to handle login
        fetch('http://localhost:8000/api/user/login.php', {
            method: 'POST',
            credentials: "include",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username: username, password: password })
        })
            .then(handleResponse)
            .then(data => {
                // Redirect to article list page upon successful login
                window.location.href = '/newsbase/frontend/public_html/articles.html';
            })
            .catch(error => {
                frontendLog('error', error.message);
                alert('Could not log in, please try again')
            });
    });

    // Add submit event listener to register form
    registerForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const username = document.getElementById('register-username').value;
        const password = document.getElementById('register-password').value;

        // Make a request to backend to handle registration
        fetch('http://localhost:8000/api/user/register.php', {
            method: 'POST',
            credentials: "include",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username: username, password: password })
        })
            .then(handleResponse)
            .then(data => {
                window.location.href = '/frontend/public_html/index.html';
            })
            .catch(error => {
                frontendLog('error', error.message);
                alert('Could not sign up, please try again later')
            });
    });
});
