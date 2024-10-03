window.addEventListener('resize', function() {
    // Your code to handle window size changes goes here
    console.log('Window size changed! ' + window.innerWidth );
	function elementsOverlap(el1, el2) {
    const domRect1 = el1.getBoundingClientRect();
    const domRect2 = el2.getBoundingClientRect();
    return !(
        domRect1.top > domRect2.bottom ||
        domRect1.right < domRect2.left ||
        domRect1.bottom < domRect2.top ||
        domRect1.left > domRect2.right
    );
}

// Example usage:
const element1 = document.getElementById('pan'); // Replace with your actual element IDs
const element2 = document.getElementById('hello');
console.log(elementsOverlap(element1, element2)); // Returns true if they overlap
if( window.innerWidth<1360 && window.innerWidth>1024){
	const computedStyles = window.getComputedStyle(element2);
	const topValue = computedStyles.getPropertyValue('top');
	console.log(topValue);
	if(topValue!=="20px"){
        const maxWidth = 1360; //c'est ici ou il y'avait le problème (chevauchement)
        const minWidth = 800; // Set your desired minimum window width
    
        // Calculate the proportional value of w
        const normalizedWidth = Math.max(minWidth, Math.min(maxWidth, window.innerWidth));
        w = (maxWidth - normalizedWidth) / (maxWidth - minWidth) * 100;
        x=(142+Math.trunc(w))*-1 +"px"
        console.log(x);
        console.log('Updated w:', w);
        element2.style.marginLeft= x     
        element2.style.marginTop= "-18%"    
        if(window.innerWidth<1024){
            console.log("<1024");
            element2.style.marginTop= "0"
            element2.style.marginLeft= "0"
        } 
    }
	

   
	
}else if( window.innerWidth>1360 || window.innerWidth<1024 ){
    console.log("test");
    console.log(window.innerWidth);
    element2.style.marginTop= "0"
    element2.style.marginLeft= "0"
}

else{
    element2.style.marginTop= "0"
    element2.style.marginLeft= "0"
}
});


