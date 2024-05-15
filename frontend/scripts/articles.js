document.addEventListener('DOMContentLoaded', function() {
    // Initialize dropdown menu
    const profileBtn = document.querySelector('.profile-btn');

    // Get the dropdown content
    const dropdownContent = document.querySelector('.dropdown-content');

    // Add click event listener to the profile button/link
    profileBtn.addEventListener('click', function(event) {
        // Prevent default link behavior
        event.preventDefault();

        // Toggle the visibility of the dropdown content
        dropdownContent.classList.toggle('show');
    });

    // Close the dropdown when clicking outside of it
    document.addEventListener('click', function(event) {
        if (!event.target.matches('.profile-btn')) {
            // If the clicked element is not the profile button/link, close the dropdown
            dropdownContent.classList.remove('show');
        }
    });

    // Add event listener to logout button
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(event) {
            event.preventDefault();

            // Send request to backend to logout
            fetch('http://localhost:8000/api/user/logout.php', {
                method: 'GET'
            })
            .catch(error => {
                console.error('Error during logout:', error);
            });
        });
    }

    // Fetch saved articles
    function fetchSavedArticles() {
        fetch('http://localhost:8000/api/articles/get_articles.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error('Failed to fetch articles');
                }
            })
            .then(data => {
                // Clear previous articles
                const articlesList = document.getElementById('articles-list');
                articlesList.innerHTML = '';

                // Append each article to the articlesList
                data.forEach(article => {
                    const articleElement = document.createElement('div');
                    articleElement.classList.add('article');
                    articleElement.innerHTML = `
                    <h2>${article.title}</h2>
                    <p>${article.description}</p>
                    <a href="${article.url}" target="_blank">Read More</a>
                    <img src="${article.image_url}" alt="${article.title}">
                    <button class="btn delete-article-btn" data-article-id="${article.id}">Delete article</button>
                `;
                    articlesList.appendChild(articleElement);
                });
            })
            .catch(error => {
                console.error('Error fetching articles:', error.message);
            });
    }

    // Call fetchSavedArticles when the page loads
    fetchSavedArticles();

    // Add event listener to delete article buttons
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-article-btn')) {
            const articleId = event.target.dataset.articleId;
            deleteArticle(articleId);
        }
    });

    function deleteArticle(articleId) {
        if (confirm("Are you sure you want to delete this article?")) {
            fetch(`http:localhost:8000/api/articles/delete_article.php?id=${articleId}`, {
                method: "DELETE"
            })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        throw new Error("Failed to delete article");
                    }
                })
                .then(data => {
                    // Article deleted successfully
                    console.log(data.message);
                    window.location.reload();
                })
                .catch(error => {
                    console.error("Error:", error.message);
                    // Handle error: Display error message to user or retry the request
                });
        }
    }

});
