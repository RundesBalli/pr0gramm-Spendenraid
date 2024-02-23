/**
 * Toggle NSFW blur
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

/**
 * Thanks to NullDev!
 * https://github.com/NullDev
 */
