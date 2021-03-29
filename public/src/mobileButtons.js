// @ts-nocheck
"use strict";

/**
 * Handle Button Click
 *
 * @param {Event & { target: HTMLElement }} e
 */
let handleMobileBtnClick = function(e){
    e.preventDefault();
    document.getElementById("value-input").value = e.target.innerHTML;
    document.getElementById("valuation-form").submit();
};

if (window.matchMedia("(min-width: 600px)")){
    document.querySelectorAll("a.msb-btn").forEach(/** @type {HTMLElement} e */ (e) => e.addEventListener("click", handleMobileBtnClick));
}
