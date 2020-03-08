function toggleMenu() {
  var x = document.getElementById("nav");
  if (x.className === "") {
      x.className += "responsive";
  } else {
      x.className = "";
  }
}

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("toggle").addEventListener("click", toggleMenu);
});
