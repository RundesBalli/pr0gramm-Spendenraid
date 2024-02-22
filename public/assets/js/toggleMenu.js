"use strict";

/**
 * Toggle Mobile menu
 * @returns {any}
 */
let toggleMenu = function(){
    document.getElementById("navbar").classList.toggle("responsive");
};


document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("toggleElement").addEventListener("click", toggleMenu);
});

/**
 * Thanks to NullDev!
 * https://github.com/NullDev
 */
