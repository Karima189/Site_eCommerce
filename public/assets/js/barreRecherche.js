document.addEventListener("DOMContentLoaded", function () {
    var recherche = document.getElementById("searchInput");
    var articles = document.querySelectorAll(".listeProduits div");
    
    recherche.addEventListener("input", function () {
    var searchTerm = recherche.value.trim().toLowerCase(); // Assurez-vous de supprimer les espaces blancs
    
    articles.forEach(function (article) {
    var articleNameElement = article.querySelector("h2");
    if (articleNameElement) { // Vérifie si l'élément h2 existe
    var articleName = articleNameElement.textContent.trim().toLowerCase();
    // Assurez-vous de supprimer les espaces blancs
    // Vérifiez si le terme de recherche est inclus dans le titre de l'article
    if (articleName.includes(searchTerm)) {
    article.style.display = "block";
    } else {
    article.style.display = "none";
    }
    }
    });
    });
    });