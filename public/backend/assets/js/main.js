document.addEventListener('DOMContentLoaded', () => {
  const themeToggle = document.querySelector('.theme-toggle');
  const themeIcon = themeToggle?.querySelector('i');
  const savedTheme = localStorage.getItem('adminTheme') || 'light';

  const applyTheme = (theme) => {
    document.body.classList.toggle('admin-dark-page', theme === 'dark');
    document.body.classList.toggle('admin-light-page', theme !== 'dark');

    if (themeIcon) {
      themeIcon.classList.toggle('fa-moon', theme !== 'dark');
      themeIcon.classList.toggle('fa-sun', theme === 'dark');
    }

    themeToggle?.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
  };

  applyTheme(savedTheme);

  themeToggle?.addEventListener('click', () => {
    const nextTheme = document.body.classList.contains('admin-dark-page') ? 'light' : 'dark';
    localStorage.setItem('adminTheme', nextTheme);
    applyTheme(nextTheme);
  });

  const toggle = document.querySelector('.sidebar-toggle');
  const sidebar = document.querySelector('#adminSidebar');

  if (toggle && sidebar) {
    toggle.addEventListener('click', () => {
      sidebar.classList.toggle('is-open');
    });
  }

  const profileDropdown = document.querySelector('.profile-dropdown');
  const profileButton = document.querySelector('.profile-menu');

  if (profileDropdown && profileButton) {
    profileButton.addEventListener('click', (event) => {
      event.stopPropagation();
      const isOpen = profileDropdown.classList.toggle('is-open');
      profileButton.setAttribute('aria-expanded', String(isOpen));
      profileDropdown.querySelector('.profile-panel')?.setAttribute('aria-hidden', String(!isOpen));
    });

    document.addEventListener('click', (event) => {
      if (!profileDropdown.contains(event.target)) {
        profileDropdown.classList.remove('is-open');
        profileButton.setAttribute('aria-expanded', 'false');
        profileDropdown.querySelector('.profile-panel')?.setAttribute('aria-hidden', 'true');
      }
    });
  }
});
