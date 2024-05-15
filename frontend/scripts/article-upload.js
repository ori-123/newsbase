document.addEventListener("DOMContentLoaded", function() {
    // Get the form element
    const form = document.getElementById("upload-form");

    // Add event listener for form submission
    form.addEventListener("submit", function(event) {
        event.preventDefault();

        // Get the form data
        const formData = new FormData(form);

        // Send the form data to the server using fetch
        fetch("http:localhost:8000/api/articles/save_article.php", {
            method: "POST",
            body: formData
        })
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error("Failed to upload article");
                }
            })
            .then(data => {
                // Article uploaded successfully
                console.log(data.message); // Log success message
            })
            .catch(error => {
                console.error("Error:", error.message); // Log error message
            });
    });
});
