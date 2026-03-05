document.querySelectorAll("#div-checked-radio").forEach(card => {
    card.addEventListener("click", function() {
        let radioId = this.getAttribute("data-radio"); 
        document.getElementById(radioId).checked = true; 

        // Remove selected class from all cards
        document.querySelectorAll("div-checked-radio").forEach(c => c.classList.remove("selected"));
        
        // Add selected class to clicked card
        this.classList.add("selected");
    });
});
// not working po huhu