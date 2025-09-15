// Password toggle
document.getElementById("passwordToggle").addEventListener("click", function() {
    let input = document.getElementById("password");
    input.type = input.type === "password" ? "text" : "password";
    this.classList.toggle("show-password");
});

//overlay cards auto switch and navigate
const cards = document.querySelectorAll(".overlay-cards .card");
const dots = document.querySelectorAll(".dots .dot");
let currentIndex = 0;

function showCard(index) {
    cards.forEach((card, i) => {
        card.classList.toggle("active", i === index);
        dots[i].classList.toggle("active", i === index);
    });
    currentIndex = index;
}

// Auto slide every 5 seconds
setInterval(() => {
    let nextIndex = (currentIndex + 1) % cards.length;
    showCard(nextIndex);
}, 5000);

// Dot navigation
dots.forEach((dot, i) => {
    dot.addEventListener("click", () => {
        showCard(i);
    });
});