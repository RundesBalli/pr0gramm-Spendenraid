"use strict";

/**
 * Toggle Mobile menu
 * @returns {any}
 */
let toggleMenu = function(){
    const x = document.getElementById("nav");
    x.classList.toggle("responsive");
};

/**
 * Toggle NSWF blur
 * @returns {any}
 */
let toggleBlur = function(){
    const x = document.querySelector("a.nsfw-blurred");
    x.classList.toggle("unblur");
};

document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("toggle").addEventListener("click", toggleMenu);
    document.getElementById("nsfw-blur-cb").addEventListener("click", toggleBlur);
});
