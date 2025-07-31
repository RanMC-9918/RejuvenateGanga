document.addEventListener('DOMContentLoaded', () => {
  const currentPath = window.location.pathname.split('/').pop().toLowerCase(); // Get the current file name in lowercase
  const navLinks = document.querySelectorAll('.navbar a');
  
  navLinks.forEach(link => {
    const linkHref = link.getAttribute('href').toLowerCase();
    
    // Check if the link matches the current page
    if (linkHref === currentPath) {
      link.classList.add('active');
      link.style.pointerEvents = 'none'; // Disable the link
      link.style.color = 'Grey'; // Change color to indicate disabled state
      link.style.cursor = 'not-allowed'; // Change cursor to not-allowed when hovering over the disabled link
    }
  });
});
// Select all the buttons and the content container
const buttons = document.querySelectorAll('.nav-button');
const contentContainer = document.getElementById('content');

