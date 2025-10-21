// Espera o DOM carregar para garantir que os elementos existam
document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. Funcionalidade do Menu Hamburguer ---
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');

    hamburger.addEventListener('click', () => {
        // Anima o hamburguer (vira "X" ou volta ao normal)
        hamburger.classList.toggle('active');
        
        // Mostra/Esconde o menu dropdown
        navLinks.classList.toggle('active');
    });

    // (Bônus) Fecha o menu ao clicar em um link (útil em Single Page Applications)
    document.querySelectorAll('.nav-links li a').forEach(link => {
        link.addEventListener('click', () => {
            if (navLinks.classList.contains('active')) {
                hamburger.classList.remove('active');
                navLinks.classList.remove('active');
            }
        });
    });


    // --- 2. Diferencial: Sombra no Header ao Rolar ---
    const header = document.querySelector('header');
    
    window.addEventListener('scroll', () => {
        // Adiciona a classe 'scrolled' se o usuário rolar mais de 20 pixels
        if (window.scrollY > 20) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

});