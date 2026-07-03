document.addEventListener('DOMContentLoaded', () => {
  const autoDismissAlerts = (selector, delay = 4000) => {
    document.querySelectorAll(selector).forEach((alert) => {
      window.setTimeout(() => {
        alert.classList.add('is-hiding');
        alert.addEventListener('animationend', () => alert.remove(), { once: true });
      }, delay);
    });
  };

  autoDismissAlerts('.flash-message');

  const richTextareas = document.querySelectorAll('textarea[data-rich-text]');

  const richCommands = [
    { command: 'formatBlock', value: 'p', icon: 'fa-paragraph', label: 'Paragraph' },
    { command: 'formatBlock', value: 'h3', icon: 'fa-heading', label: 'Heading' },
    { command: 'bold', icon: 'fa-bold', label: 'Bold' },
    { command: 'italic', icon: 'fa-italic', label: 'Italic' },
    { command: 'underline', icon: 'fa-underline', label: 'Underline' },
    { command: 'insertUnorderedList', icon: 'fa-list-ul', label: 'Bullet list' },
    { command: 'insertOrderedList', icon: 'fa-list-ol', label: 'Numbered list' },
    { command: 'createLink', icon: 'fa-link', label: 'Link', prompt: 'Enter link URL' },
    { command: 'removeFormat', icon: 'fa-eraser', label: 'Clear format' },
  ];

  richTextareas.forEach((textarea) => {
    const editorShell = document.createElement('div');
    editorShell.className = 'rich-editor';

    const toolbar = document.createElement('div');
    toolbar.className = 'rich-toolbar';

    const editor = document.createElement('div');
    editor.className = 'rich-editor-box';
    editor.contentEditable = 'true';
    editor.innerHTML = textarea.value || '';
    editor.setAttribute('role', 'textbox');
    editor.setAttribute('aria-multiline', 'true');

    richCommands.forEach((item) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.title = item.label;
      button.setAttribute('aria-label', item.label);
      button.innerHTML = `<i class="fa-solid ${item.icon}"></i>`;

      button.addEventListener('click', () => {
        editor.focus();

        if (item.prompt) {
          const value = window.prompt(item.prompt, 'https://');
          if (!value) return;
          document.execCommand(item.command, false, value);
        } else {
          document.execCommand(item.command, false, item.value || null);
        }

        textarea.value = editor.innerHTML.trim();
      });

      toolbar.appendChild(button);
    });

    const syncSource = () => {
      textarea.value = editor.innerHTML.trim();
    };

    editor.addEventListener('input', syncSource);
    editor.addEventListener('blur', syncSource);
    textarea.form?.addEventListener('submit', syncSource);

    editorShell.appendChild(toolbar);
    editorShell.appendChild(editor);

    const label = textarea.closest('label');
    textarea.classList.add('rich-text-hidden-source');
    label?.classList.add('rich-source-label');
    (label || textarea).after(editorShell);
  });

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

  const notificationDropdown = document.querySelector('.notification-dropdown');
  const notificationButton = document.querySelector('.notification-toggle');
  const notificationPanel = document.querySelector('.notification-panel');
  const notificationList = document.querySelector('[data-order-notifications]');
  const notificationCount = document.querySelector('[data-notification-count]');

  const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
  }[char]));

  const renderNotifications = (orders = []) => {
    if (!notificationList) return;

    if (!orders.length) {
      notificationList.innerHTML = '<div class="notification-empty">No orders yet.</div>';
      return;
    }

    notificationList.innerHTML = orders.map((order) => {
      const payment = String(order.payment_status || 'unpaid');
      const paymentLabel = payment.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());
      const tracking = String(order.tracking_status || 'placed').replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());

      return `
        <a class="notification-item" href="${escapeHtml(order.url)}">
          <span class="notification-dot payment-status-${escapeHtml(payment)}"></span>
          <div>
            <strong>#${escapeHtml(order.order_number)} &middot; ${escapeHtml(order.customer_name)}</strong>
            <small>${escapeHtml(paymentLabel)} &middot; ${escapeHtml(tracking)} &middot; &pound;${escapeHtml(order.total)} &middot; ${escapeHtml(order.created || '')}</small>
          </div>
        </a>
      `;
    }).join('');
  };

  const refreshNotifications = async () => {
    if (!window.adminRoutes?.orderNotifications || !notificationList) return;

    try {
      const response = await fetch(window.adminRoutes.orderNotifications, {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
      });

      if (!response.ok) return;

      const payload = await response.json();
      if (notificationCount) notificationCount.textContent = payload.count ?? 0;
      renderNotifications(payload.orders || []);
    } catch (error) {
      // Keep the last rendered notification list if refresh fails.
    }
  };

  if (notificationDropdown && notificationButton) {
    notificationButton.addEventListener('click', (event) => {
      event.stopPropagation();
      const isOpen = notificationDropdown.classList.toggle('is-open');
      notificationButton.setAttribute('aria-expanded', String(isOpen));
      notificationPanel?.setAttribute('aria-hidden', String(!isOpen));
      if (isOpen) refreshNotifications();
    });

    document.addEventListener('click', (event) => {
      if (!notificationDropdown.contains(event.target)) {
        notificationDropdown.classList.remove('is-open');
        notificationButton.setAttribute('aria-expanded', 'false');
        notificationPanel?.setAttribute('aria-hidden', 'true');
      }
    });

    refreshNotifications();
    setInterval(refreshNotifications, 30000);
  }
});
