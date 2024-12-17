const hamburger = document.getElementById("hamburger");
const menu = document.getElementById("menu");


hamburger.addEventListener("click", () => {
  console.log("hello")
  menu.classList.toggle("active"); 
});
