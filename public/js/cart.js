$( document ).ready(function() {
    $("a.ajax").on("click", (evtClick) => {
      evtClick.preventDefault();
      var href = evtClick.target.getAttribute("href");
      console.log(href);
      $.ajax({
        url: href,
        dataType: "json",
        success: (data) => {
          $("#nombre").html(data);
          console.log(data);
        },
        error: (jqXHR, status, error) => {
          console.log("ERREUR AJAX", status, error);
        },
      });
    });
});