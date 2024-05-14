document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

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
                    // Handle login error, display error message to the user, etc.
                    console.error('Login failed:', response.statusText);
                }
            })
            .catch(error => {
                console.error('Error during login:', error);
            });
    });
});
