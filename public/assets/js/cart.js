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
        if (taillesCochées.length == 0) {
            taillesCochées = { taille: "" };
        }
        $.ajax({
            url: '/ajouter-au-panier/' + id,
            type : 'get',
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


// Fonction pour mettre à jour le total lorsque les cases à cocher ou les quantités changent
function updateTotal() {
    var totalPrix = 0;
    var checkboxes = document.querySelectorAll('.product-checkbox:checked');
    var livraisonSelect = document.getElementById('livraison');
    var prixLivraison = parseFloat(livraisonSelect.options[livraisonSelect.selectedIndex].getAttribute('data-prix'));

    articlesEnvoyes = [];

    checkboxes.forEach(function (checkbox) {
        var id = checkbox.value;
        var itemId = checkbox.getAttribute('data-taille');
        var prixUnitaire = parseFloat(document.querySelector('#prix' + itemId).innerText);
        var quantity = parseInt(document.querySelector('#quantity' + itemId).value);
        var totalArticle = prixUnitaire * quantity; // Calculer le total de chaque article
        var taille = document.getElementById('taille' + itemId).textContent;
        console.log(taille);

        totalPrix += totalArticle; // Ajouter le total de l'article au totalPrix

        // Ajouter les informations de l'article à la liste
        articlesEnvoyes.push({
            id: id,
            quantity: quantity,
            prixUnitaire: prixUnitaire,
            prixTotalProduit: totalArticle,
            taille: taille
        });
    });

    // Ajouter le prix de livraison au total
    totalPrix += prixLivraison;

    articlesEnvoyes.push({
        totalPrix: totalPrix
    })

    document.getElementById('total-prix').innerText = "Total: " + totalPrix.toFixed(2) + " €";
}


function getArticlesInfos() {
    return JSON.stringify(articlesEnvoyes);
}
$(document).ready(function () {

    // Gestionnaire d'événement pour le bouton "Passer Ma Commande"
    $('#passer_commande').on('click', function () {
        // Envoyer les données des articles sélectionnés via une requête AJAX
        var articlesSelectionnes = getArticlesInfos();
        console.log(articlesSelectionnes);
        $.ajax({
            url: '/commande/recap',
            type: 'POST', // ou 'GET' selon votre besoin
            contentType: 'application/json',
            data: articlesSelectionnes,
            success: function (response) {
                window.location.href = response.url;
            },
            error: function (xhr, status, error) {
                // Gérer les erreurs de la requête AJAX
            }
        });
    });

})

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


// Tout cocher 

$(document).ready(function () {
    // Sélection de la case à cocher "selectAll"
    var selectAll = $('#selectAll');

    // Gestion de l'événement de changement d'état de la case à cocher "selectAll"
    selectAll.change(function () {
        // Vérifie si la case à cocher "selectAll" est cochée ou non
        var isChecked = $(this).is(':checked');

        // Sélection de toutes les autres cases à cocher
        var otherCheckboxes = $('input[type="checkbox"]').not(this);

        // Si la case "selectAll" est cochée
        if (isChecked) {
            // Cocher toutes les autres cases à cocher
            otherCheckboxes.prop('checked', true);
            updateTotal();
        } else {
            // Sinon, décocher toutes les autres cases à cocher
            otherCheckboxes.prop('checked', false);
        }
    });
    
    var confirmer = $('#continue_to_form');

    confirmer.on('click', function () {
        window.location.href = "/verification/adresse";
    })

});
function connectRequired(){
    window.location.href = '/login';
}



