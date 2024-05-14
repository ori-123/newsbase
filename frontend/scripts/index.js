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
        fetch('/backend/api/user/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username: username, password: password })
        })
            .then(response => {
                if (response.ok) {
                    // Redirect to article list page upon successful login
                    window.location.href = '/articles.html';
                } else {
                    // Handle login error
                    console.error('Login failed:', response.statusText);
                }
            })
            .catch(error => {
                console.error('Error during login:', error);
            });
    });

    registerForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const username = document.getElementById('register-username').value;
        const password = document.getElementById('register-password').value;

        // Make a request to backend to handle registration
        fetch('/backend/api/user/register.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username: username, password: password })
        })
            .then(response => {
                if (response.ok) {
                    // Redirect to the login page or perform any other actions upon successful registration
                    window.location.href = '/index.html';
                } else {
                    // Handle registration error
                    console.error('Registration failed:', response.statusText);
                }
            })
            .catch(error => {
                console.error('Error during registration:', error);
            });
    });
});
