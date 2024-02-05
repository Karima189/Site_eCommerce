$(document).ready(function () {
    $(".ajouter_au_panier").on("click", (evtClick) => {
        evtClick.preventDefault();
        var id = $(evtClick.target).data('id');
        $.ajax({
            url: '/ajouter-au-panier/' + id,
            dataType: "json",
            success: (data) => {
                console.log(typeof data);
                $("#nombre").html(data.nbArticles);
            },
            error: (jqXHR, status, error) => {
                console.log("ERREUR AJAX", status, error);
            },
        });
    });
});