document.addEventListener('DOMContentLoaded', function() {
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
});
