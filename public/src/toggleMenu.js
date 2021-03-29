"use strict";

let toggleMenu = function(){
    const x = document.getElementById("nav");
    x.classList.toggle("responsive");
}

document.addEventListener("DOMContentLoaded", () => document.getElementById("toggle").addEventListener("click", toggleMenu));
