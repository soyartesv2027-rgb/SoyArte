const menuBtnMusic = document.getElementById("menuBtnMusic");

const sidebarMusic = document.getElementById("sidebarMusic");

const overlayMusic = document.getElementById("overlayMusic");


menuBtnMusic.addEventListener("click", () => {

    sidebarMusic.classList.toggle("active");

    overlayMusic.classList.toggle("active");

});


overlayMusic.addEventListener("click", () => {

    sidebarMusic.classList.remove("active");

    overlayMusic.classList.remove("active");

});