
$(document).ready(function () {
    $(".ajouter_au_panier").on("click", (evtClick) => {
        evtClick.preventDefault();
        var id = $(evtClick.target).data('id');// pour récupérer l'id du produit stocké dans data-id
        var checkbox = $('.taille_solo  input[type="checkbox"]');

        var taillesCochées = [];

        checkbox.each(function () {
            if ($(this).is(":checked")) {
                var checkboxValue = $(this).val(); // 
                taillesCochées.push(checkboxValue); // Ajoutez la valeur de la taille cochée au tableau
                console.log("Case cochée avec la valeur :", checkboxValue);
                // Vous pouvez envoyer la valeur dans votre requête AJAX ici
            }
        });
        if (taillesCochées.length == 0) {
            taillesCochées = { taille: "" };// tailleCochés c'est un tableau d'objets javascript ou taille est la clé et "" est la valeur
        }
        $.ajax({ 
            url: '/ajouter-au-panier/' + id,
            type: 'get',
            dataType: "json", 
            data: {
                taille: taillesCochées
            },
            success: (data) => {
                console.log(data);
                console.log(typeof data);
                $("#nombre").html(data.nbArticles);
                if (data.erreur_message) { 
                    Swal.fire({
                        title: 'Oups !',
                        text: data.erreur_message,
                        icon: 'error',
                       
                      })
                }
            },
            error: (jqXHR, status, error) => {
                console.log("ERREUR AJAX", status, error);
            },
        });
    });
  // 
    var quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(function (input) {
        input.addEventListener('change', function () {
            // console.log(this);
            var itemId = input.getAttribute('data-id');// id du produit basé sur sa taille
            // var itemPrice = parseFloat(document.querySelector('#total' + itemId).innerText);
            var prixUnitaire = parseFloat(document.querySelector('#prix' + itemId).innerText);
            var quantity = parseInt(input.value);
            var total = prixUnitaire * quantity;

            // Mettre à jour le total affiché
            document.querySelector('#total' + itemId).innerText = total  + " €";
            // Mettre à jour le total général
            updateTotal();
        });
    });


});



var articlesEnvoyes = [];
// Fonction pour mettre à jour le total lorsque les cases à cocher ou les quantités changent
function updateTotal() {
    articlesEnvoyes = [];
    var totalPrix = 0;
    var checkboxes = document.querySelectorAll('.product-checkbox:checked');
    var livraisonSelect = document.getElementById('livraison');
    var prixLivraison = parseFloat(livraisonSelect.options[livraisonSelect.selectedIndex].getAttribute('data-prix')); //recupère le prix de la livraison choisie 


    checkboxes.forEach(function (checkbox) {
        var id = checkbox.value;// recuperer id du produit
        var itemId = checkbox.getAttribute('data-taille'); // identifiant unique de l'article basé sur la taille du produit. Cette valeur est stockée dans la variable itemId.
    
        var prixUnitaire = parseFloat(document.querySelector('#prix' + itemId).innerText);
        
        var quantity = parseInt(document.querySelector('#quantity' + itemId).value);
        var totalArticle = prixUnitaire * quantity; // Calculer le total de chaque article
        var taille = document.getElementById('taille' + itemId).textContent;

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
        prixLivraison: prixLivraison,
        totalPrix: totalPrix
    })

    document.getElementById('total-prix').innerText = "Total: " + totalPrix.toFixed(2) + " €";

}


function getArticlesInfos() {
    console.log(articlesEnvoyes);
    return JSON.stringify(articlesEnvoyes);// on transforme le tableau en une chaine JSON
}
$(document).ready(function () {

    // Gestionnaire d'événement pour le bouton "Passer Ma Commande"
    $('#passer_commande').on('click', function () {
        // Envoyer les données des articles sélectionnés via une requête AJAX
        var articlesSelectionnes = getArticlesInfos();
        if (articlesEnvoyes.length>1){
            console.log(articlesSelectionnes);
            $.ajax({
                url: '/commande/recap',
                type: 'POST',
                contentType: 'application/json',// type de ce que ajax envoie
                data: articlesSelectionnes,
                success: function (response) {
    
                    if (response.url == 'Veuillez séléctionner un produit ') {
                        // alert(response.url);
                        Swal.fire({
                            title: 'Oups !',
                            text: 'Veuillez séléctionner un produit',
                            icon: 'error',
                           
                          })
                    } else {
                        window.location.href = response.url;
                    }
                },
                error: function (xhr, status, error) {
                    // Gérer les erreurs de la requête AJAX
                }
            });
        }else{
        //    alert('Veuillez séléctionner un produit') ;
           Swal.fire({
            title: 'Oups !',
            text: 'Veuillez séléctionner un produit',
            icon: 'error',
           
          })
        }
       
    });

})

// function viderPanier() { // Utiliser une requête AJAX pour appeler la route de vidage du panier
//     $.ajax({
//         url: '/vider-panier',
//         dataType: 'json',
//         success: function (data) { // Mettre à jour l'affichage du panier ou rediriger vers une autre page si nécessaire
//             window.location.reload(); // Recharger la page actuelle
//         },
//         error: function (jqXHR, status, error) {
//             console.log('Erreur AJAX', status, error);
//         }
//     });
// }


// Tout séléctionner

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
function connectRequired() {
    window.location.href = '/login';
}



