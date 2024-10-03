function toggleText() {
    var hiddenText = document.getElementById("hiddenText");
    
    if (hiddenText.style.display === "none") {
    hiddenText.style.display = "block";
    } else {
    hiddenText.style.display = "none";
    }
    }

    
    
    // pour la taille : quand on clique
    function toggleCheckbox(element) {
    var checkbox = element.querySelector('input[type="checkbox"]'); // on choisit la premiere input de type checkbox
    checkbox.checked = ! checkbox.checked;
    
    // elle est egale à false si on clique elle devient true, et inversement
    
    // Ajoutez ou supprimez la classe 'checked' pour styliser visuellement
    if (checkbox.checked) {
    element.classList.add('checked');
    } else {
    element.classList.remove('checked');
    }
    }