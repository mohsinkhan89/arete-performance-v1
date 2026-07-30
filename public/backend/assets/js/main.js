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
    { type: 'select' },
    { command: 'bold', icon: 'fa-bold', label: 'Bold' },
    { command: 'italic', icon: 'fa-italic', label: 'Italic' },
    { command: 'underline', icon: 'fa-underline', label: 'Underline' },
    { command: 'strikeThrough', icon: 'fa-strikethrough', label: 'Strike' },
    { command: 'insertUnorderedList', icon: 'fa-list-ul', label: 'Bullet list' },
    { command: 'insertOrderedList', icon: 'fa-list-ol', label: 'Numbered list' },
    { command: 'formatBlock', value: 'blockquote', icon: 'fa-quote-left', label: 'Quote' },
    { command: 'outdent', icon: 'fa-outdent', label: 'Outdent' },
    { command: 'indent', icon: 'fa-indent', label: 'Indent' },
    { command: 'justifyLeft', icon: 'fa-align-left', label: 'Align left' },
    { command: 'justifyCenter', icon: 'fa-align-center', label: 'Align center' },
    { command: 'justifyRight', icon: 'fa-align-right', label: 'Align right' },
    { command: 'createLink', icon: 'fa-link', label: 'Link', prompt: 'Enter link URL' },
    { command: 'insertImage', icon: 'fa-image', label: 'Image URL', prompt: 'Enter image URL' },
    { command: 'unlink', icon: 'fa-link-slash', label: 'Remove link' },
    { command: 'removeFormat', icon: 'fa-eraser', label: 'Clear format' },
    { command: 'undo', icon: 'fa-rotate-left', label: 'Undo' },
    { command: 'redo', icon: 'fa-rotate-right', label: 'Redo' },
  ];

  const hasHtml = (value) => /<\/?[a-z][\s\S]*>/i.test(value || '');

  const escapeMarkup = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
  }[char]));

  const plainTextToHtml = (value) => String(value || '')
    .split(/\n{2,}/)
    .map((block) => block.trim())
    .filter(Boolean)
    .map((block) => `<p>${escapeMarkup(block).replace(/\n/g, '<br>')}</p>`)
    .join('');

  const valueToHtml = (value) => {
    const cleanValue = String(value || '').trim();
    if (!cleanValue) return '';
    return hasHtml(cleanValue) ? cleanValue : plainTextToHtml(cleanValue);
  };

  const setCaretInside = (element) => {
    const selection = window.getSelection();
    if (!selection) return;

    const range = document.createRange();
    range.selectNodeContents(element);
    range.collapse(true);
    selection.removeAllRanges();
    selection.addRange(range);
  };

  const insertParagraphAfter = (target) => {
    if (!target?.parentNode) return null;

    const paragraph = document.createElement('p');
    paragraph.innerHTML = '<br>';
    target.parentNode.insertBefore(paragraph, target.nextSibling);
    setCaretInside(paragraph);

    return paragraph;
  };

  const closestNode = (node, selector, boundary) => {
    const element = node?.nodeType === Node.ELEMENT_NODE ? node : node?.parentElement;
    if (!element) return null;

    const match = element.closest(selector);
    return match && (!boundary || boundary.contains(match)) ? match : null;
  };

  const moveAfter = (node, target) => {
    if (!node || !target?.parentNode) return target;
    target.parentNode.insertBefore(node, target.nextSibling);
    return node;
  };

  const normalizeEditorStructure = (editor) => {
    let changed = false;
    const movableSelector = 'p, h2, h3, h4, ul, ol, blockquote, pre';

    editor.querySelectorAll('.product-specs, dl').forEach((specList) => {
      let insertionPoint = specList;

      specList.querySelectorAll(`dt ${movableSelector}, dd ${movableSelector}`).forEach((block) => {
        insertionPoint = moveAfter(block, insertionPoint);
        changed = true;
      });

      Array.from(specList.children).forEach((child) => {
        if (child.matches('div, dt, dd')) return;
        if (!child.matches(movableSelector)) return;
        insertionPoint = moveAfter(child, insertionPoint);
        changed = true;
      });
    });

    editor.querySelectorAll('td h2, td h3, td h4, th h2, th h3, th h4').forEach((heading) => {
      const table = heading.closest('table');
      if (!table || !editor.contains(table)) return;
      moveAfter(heading, table);
      changed = true;
    });

    return changed;
  };

  richTextareas.forEach((textarea) => {
    const editorShell = document.createElement('div');
    editorShell.className = 'rich-editor';
    editorShell.dataset.mode = 'visual';

    const toolbar = document.createElement('div');
    toolbar.className = 'rich-toolbar';

    const editor = document.createElement('div');
    editor.className = 'rich-editor-box';
    editor.contentEditable = 'true';
    editor.innerHTML = valueToHtml(textarea.value);
    editor.setAttribute('role', 'textbox');
    editor.setAttribute('aria-multiline', 'true');
    editor.dataset.placeholder = 'Write product description or paste formatted HTML...';

    const syncFromVisual = () => {
      normalizeEditorStructure(editor);
      textarea.value = editor.innerHTML.trim();
    };

    const syncToVisual = () => {
      editor.innerHTML = valueToHtml(textarea.value);
    };

    const makeButton = (item) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.title = item.label;
      button.setAttribute('aria-label', item.label);
      button.innerHTML = `<i class="fa-solid ${item.icon}"></i>`;

      button.addEventListener('click', () => {
        if (editorShell.dataset.mode === 'source') {
          syncToVisual();
          editorShell.dataset.mode = 'visual';
          textarea.classList.add('rich-text-hidden-source');
        }

        editor.focus();

        if (item.prompt) {
          const value = window.prompt(item.prompt, 'https://');
          if (!value) return;
          document.execCommand(item.command, false, value);
        } else {
          document.execCommand(item.command, false, item.value || null);
        }

        syncFromVisual();
      });

      return button;
    };

    richCommands.forEach((item) => {
      if (item.type === 'select') {
        const select = document.createElement('select');
        select.title = 'Format';
        select.setAttribute('aria-label', 'Text format');
        select.innerHTML = `
          <option value="p">Paragraph</option>
          <option value="h2">Heading 2</option>
          <option value="h3">Heading 3</option>
          <option value="h4">Heading 4</option>
          <option value="pre">Code block</option>
        `;
        select.addEventListener('change', () => {
          if (editorShell.dataset.mode === 'source') {
            syncToVisual();
            editorShell.dataset.mode = 'visual';
            textarea.classList.add('rich-text-hidden-source');
          }

          editor.focus();
          document.execCommand('formatBlock', false, select.value);
          syncFromVisual();
        });
        toolbar.appendChild(select);
        return;
      }

      toolbar.appendChild(makeButton(item));
    });

    const sourceToggle = document.createElement('button');
    sourceToggle.type = 'button';
    sourceToggle.className = 'rich-source-toggle';
    sourceToggle.title = 'HTML source';
    sourceToggle.setAttribute('aria-label', 'Toggle HTML source');
    sourceToggle.innerHTML = '<i class="fa-solid fa-code"></i><span>HTML</span>';
    sourceToggle.addEventListener('click', () => {
      const nextMode = editorShell.dataset.mode === 'source' ? 'visual' : 'source';

      if (nextMode === 'source') {
        syncFromVisual();
        textarea.classList.remove('rich-text-hidden-source');
        textarea.focus();
      } else {
        syncToVisual();
        textarea.classList.add('rich-text-hidden-source');
        editor.focus();
      }

      editorShell.dataset.mode = nextMode;
    });
    toolbar.appendChild(sourceToggle);

    editor.addEventListener('input', syncFromVisual);
    editor.addEventListener('keyup', (event) => {
      if (![' ', 'Enter'].includes(event.key)) return;
      if (normalizeEditorStructure(editor)) syncFromVisual();
    });
    editor.addEventListener('blur', syncFromVisual);
    editor.addEventListener('keydown', (event) => {
      if (event.key !== 'Enter' || event.shiftKey || event.ctrlKey || event.metaKey || event.altKey) return;

      const selection = window.getSelection();
      const focusNode = selection?.focusNode;
      if (!focusNode || !editor.contains(focusNode)) return;

      const tableCell = closestNode(focusNode, 'td, th', editor);
      const definitionValue = closestNode(focusNode, 'dd', editor);
      const specRow = closestNode(focusNode, '.product-specs > div, dl > div', editor);
      const specList = closestNode(focusNode, 'table, dl, .product-specs', editor);
      const exitTarget = specList || tableCell?.closest('table') || definitionValue?.closest('dl') || specRow;

      if (!tableCell && !definitionValue && !specRow) return;
      if (!exitTarget) return;

      event.preventDefault();
      insertParagraphAfter(exitTarget);
      syncFromVisual();
    });
    textarea.addEventListener('input', () => {
      if (editorShell.dataset.mode === 'source') return;
      syncToVisual();
    });
    textarea.form?.addEventListener('submit', () => {
      if (editorShell.dataset.mode === 'source') {
        syncToVisual();
      }
      syncFromVisual();
    });

    editorShell.appendChild(toolbar);
    editorShell.appendChild(editor);

    const label = textarea.closest('label');
    textarea.classList.add('rich-text-hidden-source', 'rich-text-source-box');
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
  const sidebarClose = document.querySelector('.sidebar-close');
  const sidebarBackdrop = document.querySelector('.sidebar-backdrop');
  const desktopSidebar = window.matchMedia('(min-width: 1200px)');
  const closeSidebar = () => {
    sidebar?.classList.remove('is-open');
    document.body.classList.remove('sidebar-open');
    toggle?.setAttribute('aria-expanded', 'false');
  };

  if (toggle && sidebar) {
    toggle.setAttribute('aria-expanded', 'false');
    toggle.addEventListener('click', () => {
      if (desktopSidebar.matches) {
        const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
        localStorage.setItem('adminSidebarCollapsed', String(isCollapsed));
        toggle.setAttribute('aria-expanded', String(!isCollapsed));
        return;
      }
      const isOpen = sidebar.classList.toggle('is-open');
      document.body.classList.toggle('sidebar-open', isOpen);
      toggle.setAttribute('aria-expanded', String(isOpen));
    });
  }

  if (desktopSidebar.matches && localStorage.getItem('adminSidebarCollapsed') === 'true') {
    document.body.classList.add('sidebar-collapsed');
  }

  desktopSidebar.addEventListener('change', () => {
    closeSidebar();
    if (!desktopSidebar.matches) document.body.classList.remove('sidebar-collapsed');
    else if (localStorage.getItem('adminSidebarCollapsed') === 'true') document.body.classList.add('sidebar-collapsed');
  });

  sidebarClose?.addEventListener('click', closeSidebar);
  sidebarBackdrop?.addEventListener('click', closeSidebar);
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeSidebar();
  });

  const fullscreenToggle = document.querySelector('.fullscreen-toggle');
  const fullscreenIcon = fullscreenToggle?.querySelector('i');
  const syncFullscreen = () => {
    const isFullscreen = Boolean(document.fullscreenElement);
    fullscreenIcon?.classList.toggle('fa-expand', !isFullscreen);
    fullscreenIcon?.classList.toggle('fa-compress', isFullscreen);
    fullscreenToggle?.setAttribute('aria-label', isFullscreen ? 'Exit fullscreen' : 'Enter fullscreen');
  };

  fullscreenToggle?.addEventListener('click', async () => {
    try {
      if (document.fullscreenElement) await document.exitFullscreen();
      else await document.documentElement.requestFullscreen();
    } catch (error) {
      // Fullscreen can be blocked by browser policy; leave the interface unchanged.
    }
  });
  document.addEventListener('fullscreenchange', syncFullscreen);

  const commandPalette = document.querySelector('.command-palette');
  const commandToggle = document.querySelector('.command-toggle');
  const commandBackdrop = document.querySelector('.command-backdrop');
  const commandInput = commandPalette?.querySelector('.command-search input');
  const commandLinks = [...(commandPalette?.querySelectorAll('.command-results a') || [])];
  const commandEmpty = commandPalette?.querySelector('.command-empty');
  let commandIndex = 0;

  const visibleCommandLinks = () => commandLinks.filter((link) => !link.hidden);
  const focusCommand = (index) => {
    const links = visibleCommandLinks();
    if (!links.length) return;
    commandIndex = (index + links.length) % links.length;
    links.forEach((link, linkIndex) => link.classList.toggle('is-selected', linkIndex === commandIndex));
    links[commandIndex].scrollIntoView({ block: 'nearest' });
  };
  const filterCommands = () => {
    const query = commandInput?.value.trim().toLowerCase() || '';
    commandLinks.forEach((link) => {
      link.hidden = query !== '' && !link.dataset.commandLabel.includes(query);
    });
    const links = visibleCommandLinks();
    if (commandEmpty) commandEmpty.hidden = links.length > 0;
    commandIndex = 0;
    focusCommand(0);
  };
  const openCommands = () => {
    if (!commandPalette) return;
    commandPalette.classList.add('is-open');
    commandPalette.setAttribute('aria-hidden', 'false');
    document.body.classList.add('command-open');
    if (commandInput) {
      commandInput.value = '';
      filterCommands();
      window.setTimeout(() => commandInput.focus(), 30);
    }
  };
  const closeCommands = () => {
    commandPalette?.classList.remove('is-open');
    commandPalette?.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('command-open');
  };

  commandToggle?.addEventListener('click', openCommands);
  commandBackdrop?.addEventListener('click', closeCommands);
  commandInput?.addEventListener('input', filterCommands);
  commandLinks.forEach((link) => {
    link.addEventListener('mouseenter', () => {
      const links = visibleCommandLinks();
      focusCommand(links.indexOf(link));
    });
  });
  document.addEventListener('keydown', (event) => {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
      event.preventDefault();
      commandPalette?.classList.contains('is-open') ? closeCommands() : openCommands();
      return;
    }
    if (!commandPalette?.classList.contains('is-open')) return;
    if (event.key === 'Escape') {
      closeCommands();
      return;
    }
    if (event.key === 'ArrowDown') {
      event.preventDefault();
      focusCommand(commandIndex + 1);
    } else if (event.key === 'ArrowUp') {
      event.preventDefault();
      focusCommand(commandIndex - 1);
    } else if (event.key === 'Enter') {
      const link = visibleCommandLinks()[commandIndex];
      if (link) {
        event.preventDefault();
        link.click();
      }
    }
  });

  const profileDropdown = document.querySelector('.profile-dropdown');
  const profileButton = document.querySelector('.profile-menu');
  const notificationDropdown = document.querySelector('.notification-dropdown');
  const notificationButton = document.querySelector('.notification-toggle');
  const notificationPanel = document.querySelector('.notification-panel');

  const closeProfile = () => {
    if (!profileDropdown || !profileButton) return;
    profileDropdown.classList.remove('is-open');
    profileButton.setAttribute('aria-expanded', 'false');
    profileDropdown.querySelector('.profile-panel')?.setAttribute('aria-hidden', 'true');
  };

  const closeNotifications = () => {
    if (!notificationDropdown || !notificationButton) return;
    notificationDropdown.classList.remove('is-open');
    notificationButton.setAttribute('aria-expanded', 'false');
    notificationPanel?.setAttribute('aria-hidden', 'true');
  };

  if (profileDropdown && profileButton) {
    profileButton.addEventListener('click', (event) => {
      event.stopPropagation();
      closeNotifications();
      const isOpen = profileDropdown.classList.toggle('is-open');
      profileButton.setAttribute('aria-expanded', String(isOpen));
      profileDropdown.querySelector('.profile-panel')?.setAttribute('aria-hidden', String(!isOpen));
    });
  }

  const notificationList = document.querySelector('[data-order-notifications]');
  const notificationCount = document.querySelector('[data-notification-count]');
  const paymentProofModal = document.querySelector('[data-payment-proof-modal]');
  const paymentProofForm = document.querySelector('[data-payment-proof-form]');
  const paymentProofTitle = document.querySelector('#paymentProofTitle');
  const paymentProofSummary = document.querySelector('[data-payment-proof-summary]');
  const paymentProofCurrent = document.querySelector('[data-payment-proof-current]');
  const paymentProofCurrentLink = document.querySelector('[data-payment-proof-current-link]');
  const paymentProofCurrentImage = document.querySelector('[data-payment-proof-current-image]');
  const paymentProofPreview = document.querySelector('[data-payment-proof-preview]');
  const paymentProofEmpty = document.querySelector('[data-payment-proof-empty]');

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

  const closePaymentProofModal = () => {
    if (!paymentProofModal) return;
    paymentProofModal.classList.remove('is-open');
    paymentProofModal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
    paymentProofForm?.reset();
  };

  const openPaymentProofModal = (button) => {
    if (!paymentProofModal || !paymentProofForm || !button) return;

    paymentProofForm.action = button.dataset.action || '';
    if (paymentProofTitle) {
      paymentProofTitle.textContent = `Payment Proof ${button.dataset.orderNumber ? `#${button.dataset.orderNumber}` : ''}`;
    }
    if (paymentProofSummary) {
      paymentProofSummary.innerHTML = [
        button.dataset.customer || 'Customer',
        button.dataset.total || '',
        button.dataset.status ? `Current: ${button.dataset.status}` : '',
      ].filter(Boolean).map(escapeHtml).join(' &middot; ');
    }

    const proofUrl = button.dataset.proofUrl || '';
    if (paymentProofCurrent) paymentProofCurrent.hidden = false;

    if (proofUrl) {
      if (paymentProofEmpty) paymentProofEmpty.hidden = true;
      if (paymentProofPreview) paymentProofPreview.hidden = false;
      if (paymentProofCurrentLink) {
        paymentProofCurrentLink.hidden = false;
        paymentProofCurrentLink.href = proofUrl;
      }
      if (paymentProofCurrentImage) paymentProofCurrentImage.src = proofUrl;
    } else {
      if (paymentProofEmpty) paymentProofEmpty.hidden = false;
      if (paymentProofPreview) paymentProofPreview.hidden = true;
      if (paymentProofCurrentLink) {
        paymentProofCurrentLink.hidden = true;
        paymentProofCurrentLink.href = '#';
      }
      if (paymentProofCurrentImage) paymentProofCurrentImage.src = '';
    }

    paymentProofModal.classList.add('is-open');
    paymentProofModal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
    window.setTimeout(() => paymentProofForm.querySelector('input[type="file"]')?.focus(), 80);
  };

  if (notificationDropdown && notificationButton) {
    notificationButton.addEventListener('click', (event) => {
      event.stopPropagation();
      closeProfile();
      const isOpen = notificationDropdown.classList.toggle('is-open');
      notificationButton.setAttribute('aria-expanded', String(isOpen));
      notificationPanel?.setAttribute('aria-hidden', String(!isOpen));
      if (isOpen) refreshNotifications();
    });

    refreshNotifications();
    setInterval(refreshNotifications, 30000);
  }

  document.addEventListener('click', (event) => {
    const target = event.target;
    const paymentProofTrigger = target.closest('[data-payment-proof-open]');
    const paymentProofClose = target.closest('[data-payment-proof-close]');

    if (paymentProofTrigger) {
      event.preventDefault();
      openPaymentProofModal(paymentProofTrigger);
      return;
    }

    if (paymentProofClose) {
      closePaymentProofModal();
      return;
    }

    if (profileDropdown && !profileDropdown.contains(target)) {
      closeProfile();
    }

    if (notificationDropdown && !notificationDropdown.contains(target)) {
      closeNotifications();
    }

    if (
      document.body.classList.contains('sidebar-open') &&
      sidebar &&
      !sidebar.contains(target) &&
      !toggle?.contains(target)
    ) {
      closeSidebar();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    closeProfile();
    closeNotifications();
    closePaymentProofModal();
    closeSidebar();
  });

  document.querySelectorAll('.admin-nav a').forEach((link) => {
    link.addEventListener('click', closeSidebar);
  });
});
