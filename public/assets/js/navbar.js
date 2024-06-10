window.addEventListener('resize', function() {
    // Your code to handle window size changes goes here
    console.log('Window size changed!');
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
if(elementsOverlap(element1, element2)){
	const computedStyles = window.getComputedStyle(element2);
	const topValue = computedStyles.getPropertyValue('top');
	console.log(topValue);
	if(topValue!=="20px")
	element2.style.marginTop= "-3%"
	
}else{
		element2.style.marginTop= "0"
}
});


