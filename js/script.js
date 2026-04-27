//mobile-menu design
const mobileBtn = document.getElementById("mobile-cta");
const nav = document.querySelector("nav");
const mobileBtnExit = document.getElementById("mobile-exit");

mobileBtn.addEventListener("click", () => {
  nav.classList.add("menu-btn");
});
mobileBtnExit.addEventListener("click", () => {
  nav.classList.remove("menu-btn");
});

//Services-dropdown-menu
var dropdownIcon = document.getElementById("dropdown-icon");
var dropdown = document.getElementById("dropdown-ul");

dropdownIcon.addEventListener("click", () => {
  dropdown.style.display = "block";
  dropdown.classList.toggle("mystyle");
  dropdown.style.display = "none";
});

//section-1 container
var waving = document.querySelectorAll(".waves");
var waving = [...waving];

var ctas = document.querySelectorAll(".ctas");

for (let i = 0; i < waving.length; i++) {
  const waved = waving[i];
  const cta = ctas[i]; // Select the corresponding .ctas element

  waved.addEventListener("mouseover", () => {
    cta.style.display = "block";
  });
  
  waved.addEventListener("mouseout", () => {
    cta.style.display = "none";
  });
}

//faqpage.html
// Get all the buttons and answer divs
const toggleButtons = document.querySelectorAll(".faq-button");
const answers = document.querySelectorAll(".faq-button-ans");

// Add click event listeners to each button
toggleButtons.forEach((button, index) => {
  button.addEventListener("click", () => {
    // Close all other open answer divs
    answers.forEach((answer, i) => {
      if (i !== index) {
        answer.classList.remove("active");
      }
    });

    // Toggle the active class on the corresponding answer div
    answers[index].classList.toggle("active");
  });
});
