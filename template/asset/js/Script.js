/*
|--------------------------------------------------------------------------
| Script da Landing Page (NOVYX)
|--------------------------------------------------------------------------
| Este script cuida do menu móvel (hamburguer) e da rolagem suave
| com offset para o cabeçalho fixo.
*/

document.addEventListener("DOMContentLoaded", () => {
  
  const hamburgerBtn = document.getElementById("hamburger-btn");
  const navLinksMenu = document.getElementById("nav-links");
  const header = document.querySelector('.header-fixed');
  
  // ================================== 
  // 1. Lógica do Menu Móvel (Hamburguer)
  // ================================== 
  if (hamburgerBtn && navLinksMenu) {
    hamburgerBtn.addEventListener("click", () => {
      navLinksMenu.classList.toggle("active");
      // Muda o ícone do hamburguer para 'X' e vice-versa
      const icon = hamburgerBtn.querySelector('i');
      if (navLinksMenu.classList.contains("active")) {
        icon.classList.remove('bx-menu');
        icon.classList.add('bx-x');
        hamburgerBtn.setAttribute("aria-label", "Fechar menu");
      } else {
        icon.classList.remove('bx-x');
        icon.classList.add('bx-menu');
        hamburgerBtn.setAttribute("aria-label", "Abrir menu");
      }
    });
  }

  // ================================== 
  // 2. Lógica de Rolagem Suave com Offset
  // ================================== 
  const allNavLinks = document.querySelectorAll('a[href^="#"]');

  allNavLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault(); // Impede o salto padrão
      
      const targetId = this.getAttribute('href');
      const targetElement = document.querySelector(targetId);

      if (targetElement) {
        // MUDANÇA: Pega a altura do header dinamicamente
        const headerHeight = header.offsetHeight;
        const targetPosition = targetElement.offsetTop - headerHeight;

        // Rola a página para a posição correta
        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });
        
        // Fecha o menu móvel (se estiver aberto) ao clicar em um link
        if (navLinksMenu && navLinksMenu.classList.contains('active')) {
          navLinksMenu.classList.remove('active');
          const icon = hamburgerBtn.querySelector('i');
          icon.classList.remove('bx-x');
          icon.classList.add('bx-menu');
          hamburgerBtn.setAttribute("aria-label", "Abrir menu");
        }
      }
    });
  });

  // ================================== 
  // 3. Lógica de "Scrollspy" (Marca o link ativo)
  // ==================================
  const sections = document.querySelectorAll('section[id]');
  const navLinksForScroll = document.querySelectorAll('.nav-link[href^="#"]');

  const updateActiveLink = () => {
    const headerHeight = header.offsetHeight;
    let currentSectionId = '';
    const scrollPosition = window.scrollY + headerHeight + 50; // Posição com offset

    sections.forEach(section => {
      const sectionTop = section.offsetTop;
      const sectionHeight = section.offsetHeight;
      if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
        currentSectionId = section.getAttribute('id');
      }
    });

    // Atualiza os links de navegação
    navLinksForScroll.forEach(link => {
      link.classList.remove('active');
      // MUDANÇA: Atualiza aria-current para acessibilidade
      link.removeAttribute('aria-current'); 
      if (link.getAttribute('href') === `#${currentSectionId}`) {
        link.classList.add('active');
        link.setAttribute('aria-current', 'page');
      }
    });
    
    // Caso especial para o topo da página (seção "inicio")
    // MUDANÇA: Lógica levemente ajustada
    if (currentSectionId === '' && window.scrollY < sections[0].offsetTop) {
      const inicioLink = document.querySelector('.nav-link[href="#inicio"]');
      if (inicioLink) {
        inicioLink.classList.add('active');
        inicioLink.setAttribute('aria-current', 'page');
      }
    }
  };

  window.addEventListener('scroll', updateActiveLink);
  updateActiveLink(); // Executa uma vez no carregamento

  
  // ================================== 
  // 4. MUDANÇA: Lógica para Rolar ao Hash na Carga
  //    (Corrige links como novyx.com/#faq)
  // ================================== 
  if (window.location.hash) {
    const hash = window.location.hash;
    const targetElement = document.querySelector(hash);
    
    if (targetElement) {
      // Espera um instante para garantir que o layout foi calculado
      setTimeout(() => {
        const headerHeightOnLoad = header.offsetHeight;
        const targetPosition = targetElement.offsetTop - headerHeightOnLoad;
        
        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth' 
        });
      }, 100); // 100ms de delay é geralmente suficiente
    }
  }

});