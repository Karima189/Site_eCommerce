$(document).ready(function () {
    $(".ajouter_au_panier").on("click", (evtClick) => {
        evtClick.preventDefault();
        var id = $(evtClick.target).data('id');
        var checkbox = $('.taille_solo  input[type="checkbox"]');

        var taillesCochées = [];
        checkbox.each(function () {
            if ($(this).is(":checked")) {
                var checkboxValue = $(this).val();
                taillesCochées.push(checkboxValue); // Ajoutez la taille cochée au tableau
                console.log("Case cochée avec la valeur :", checkboxValue);
                // Vous pouvez envoyer la valeur dans votre requête AJAX ici
            }
        });
        $.ajax({
            url: '/ajouter-au-panier/' + id,
            dataType: "json",
            data: {
                taille: taillesCochées
            },
            success: (data) => {
                console.log(data);
                console.log(typeof data);
                $("#nombre").html(data.nbArticles);
            },
            error: (jqXHR, status, error) => {
                console.log("ERREUR AJAX", status, error);
            },
        });
    });


    var quantityInputs = document.querySelectorAll('.quantity-input');

    quantityInputs.forEach(function (input) {
        input.addEventListener('change', function () {
            var itemId = input.getAttribute('data-id');
            var itemPrice = parseFloat(document.querySelector('#total' + itemId).innerText);
            var prixUnitaire = parseFloat(document.querySelector('#prix' + itemId).innerText);
            var quantity = parseInt(input.value);
            var total = prixUnitaire * quantity;

            // Mettre à jour le total affiché
            document.querySelector('#total' + itemId).innerText = total;
            // Mettre à jour le total général
            updateTotal();
        });
    });


});
var articlesSelectionnes = [];
function cochageCommande() {
    $('.product-checkbox input[type=\'checkbox\']').on('change', function () {
        // Réinitialiser le tableau des articles sélectionnés
        articlesSelectionnes = [];
        console.log('test');
        // Parcourir toutes les cases à cocher
        $('.product-checkbox input[type=\'checkbox\']:checked').each(function () {
            // Récupérer les informations de l'article associé à la case cochée
            var img = $(this).closest('.product-checkbox').find('img').attr('src');
            var taille = $(this).closest('.product-checkbox').find('.tailles').text();
            var nom = $(this).closest('.product-checkbox').find('.descriptionArticle').text();
            var quantity = $(this).closest('.product-checkbox').find('.quantity-input').val();
            var total = $('#total-prix').text();
            var livraison = $('#livraison').val();
            var prix = $(this).closest('.product-checkbox').find('.item-prix').text();

            // Ajouter les informations de l'article au tableau des articles sélectionnés
            articlesSelectionnes.push({
                img: img,
                taille: taille,
                nom: nom,
                quantity: quantity,
                total: total,
                livraison: livraison,
                prix: prix
            });
        });
    })

    // Gestionnaire d'événement pour le bouton "Passer Ma Commande"
    $('#passer_commande').on('click', function () {
        // Envoyer les données des articles sélectionnés via une requête AJAX

        $.ajax({
            url: 'votre_url_de_traitement',
            type: 'POST', // ou 'GET' selon votre besoin
            contentType: 'application/json',
            data: JSON.stringify(articlesSelectionnes),
            success: function (response) {
                // Traiter la réponse si nécessaire
            },
            error: function (xhr, status, error) {
                // Gérer les erreurs de la requête AJAX
            }
        });
    });

};

// Fonction pour mettre à jour le total lorsque les cases à cocher ou les quantités changent
function updateTotal() {
    var totalPrix = 0;
    var checkboxes = document.querySelectorAll('.product-checkbox:checked');
    var livraisonSelect = document.getElementById('livraison');
    var prixLivraison = parseFloat(livraisonSelect.options[livraisonSelect.selectedIndex].getAttribute('data-prix'));

    checkboxes.forEach(function (checkbox) {
        var itemId = checkbox.value;
        var prixUnitaire = parseFloat(document.querySelector('#prix' + itemId).innerText);
        var quantity = parseInt(document.querySelector('#quantity' + itemId).value);
        totalPrix += prixUnitaire * quantity;
    });

    // Ajouter le prix de livraison au total
    totalPrix += prixLivraison;

    document.getElementById('total-prix').innerText = "Total: " + totalPrix.toFixed(2) + " €";
    cochageCommande();
}

function viderPanier() { // Utiliser une requête AJAX pour appeler la route de vidage du panier
    $.ajax({
        url: '/vider-panier',
        dataType: 'json',
        success: function (data) { // Mettre à jour l'affichage du panier ou rediriger vers une autre page si nécessaire
            window.location.reload(); // Recharger la page actuelle
        },
        error: function (jqXHR, status, error) {
            console.log('Erreur AJAX', status, error);
        }
    });
}