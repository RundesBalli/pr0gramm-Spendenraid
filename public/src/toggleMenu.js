function toggleMenu() {
  var x = document.getElementById("nav");
  x.classList.toggle("responsive");
}

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("toggle").addEventListener("click", toggleMenu);
});
