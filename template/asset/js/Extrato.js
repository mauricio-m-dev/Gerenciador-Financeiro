/*
|--------------------------------------------------------------------------
| Extrato.js
|--------------------------------------------------------------------------
| Este script é executado quando o DOM está totalmente carregado.
| Ele gerencia todas as interatividades da página Extrato.
*/
document.addEventListener("DOMContentLoaded", () => {

  /*
  |--------------------------------------------------------------------------
  | Seção 1: Navbar e Interações do Header
  |--------------------------------------------------------------------------
  */
  
  // Gerencia o menu hamburguer em telas móveis
  const hamburger = document.querySelector(".hamburger");
  const navLinks = document.querySelector(".nav-links");

  if (hamburger && navLinks) {
    hamburger.addEventListener("click", () => {
      hamburger.classList.toggle("active");
      navLinks.classList.toggle("active");
    });

    // Fecha o menu ao clicar em um link
    document.querySelectorAll(".nav-links li a").forEach((link) => {
      link.addEventListener("click", () => {
        if (navLinks.classList.contains("active")) {
          hamburger.classList.remove("active");
          navLinks.classList.remove("active");
        }
      });
    });
  }

  // Adiciona sombra ao header durante a rolagem
  const header = document.querySelector("header");

  if (header) {
    window.addEventListener("scroll", () => {
      if (window.scrollY > 20) {
        header.classList.add("scrolled");
      } else {
        header.classList.remove("scrolled");
      }
    });
  }
});