import { handleResponse } from './utils.js';

document.addEventListener("DOMContentLoaded", function() {
    // Get the form element
    const form = document.getElementById("upload-form");

    // Add event listener for form submission
    form.addEventListener("submit", function(event) {
        event.preventDefault();

        // Get the form data
        const formData = new FormData(form);
        const articleData = {
            title: formData.get('title'),
            url: formData.get('url'),
            description: formData.get('description'),
            image_url: formData.get('image-url')
        };
        console.log(articleData);

        // Send the form data to the server using fetch
        fetch("http://localhost:8000/api/articles/save_article.php", {
            method: "POST",
            credentials: "include",
            body: JSON.stringify(articleData)
        })
            .then(handleResponse)
            .then(data => {
                // Article uploaded successfully
                console.log(data.message); // Log success message
                // Redirect to articles page
                window.location.href = '/frontend/public_html/articles.html';
            })
            .catch(error => {
                console.error("Error:", error.message); // Log error message
            });
    });
});
