import { handleResponse } from './utils.js';
import {frontendLog} from "./log.js";

document.addEventListener("DOMContentLoaded", function () {
    const editProfileForm = document.getElementById("edit-profile-form");

    // Add event listener for form submission
    editProfileForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission behavior

        // Get form data
        const currentPassword = document.getElementById("current-password").value;
        const newUsername = document.getElementById("new-username").value;
        const newPassword = document.getElementById("new-password").value;
        const confirmPassword = document.getElementById("confirm-password").value;

        // Validate form data
        if (newPassword !== confirmPassword) {
            alert("New password and confirm password do not match.");
            return;
        }

        // Create request body
        const requestBody = {
            current_password: currentPassword,
            new_username: newUsername,
            new_password: newPassword
        };

        // Make a PUT request to the edit endpoint
        fetch("http://localhost:8000/api/user/edit.php", {
            method: "PUT",
            credentials: "include",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(requestBody)
        })
            .then(handleResponse)
            .then(data => {
                alert(data.message);
            })
            .catch(error => {
                frontendLog('error', error)
                console.error("Error:", error);
            });
    });
});
