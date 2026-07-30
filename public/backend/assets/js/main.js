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
    document.dispatchEvent(new CustomEvent('admin-theme-change', { detail: { theme } }));
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
  const setSidebarCollapsed = (isCollapsed) => {
    document.body.classList.toggle('sidebar-collapsed', isCollapsed);
    document.body.classList.remove('sidebar-hovered');
    localStorage.setItem('adminSidebarCollapsed', String(isCollapsed));
    toggle?.setAttribute('aria-expanded', String(!isCollapsed));
  };
  const closeSidebar = () => {
    sidebar?.classList.remove('is-open');
    document.body.classList.remove('sidebar-open');
    toggle?.setAttribute('aria-expanded', 'false');
  };

  if (toggle && sidebar) {
    toggle.setAttribute('aria-expanded', 'false');
    toggle.addEventListener('click', () => {
      if (desktopSidebar.matches) {
        setSidebarCollapsed(!document.body.classList.contains('sidebar-collapsed'));
        return;
      }
      const isOpen = sidebar.classList.toggle('is-open');
      document.body.classList.toggle('sidebar-open', isOpen);
      toggle.setAttribute('aria-expanded', String(isOpen));
    });
  }

  if (desktopSidebar.matches && localStorage.getItem('adminSidebarCollapsed') === 'true') {
    document.body.classList.add('sidebar-collapsed');
    toggle?.setAttribute('aria-expanded', 'false');
  }

  desktopSidebar.addEventListener('change', () => {
    closeSidebar();
    document.body.classList.remove('sidebar-hovered');
    if (!desktopSidebar.matches) document.body.classList.remove('sidebar-collapsed');
    else if (localStorage.getItem('adminSidebarCollapsed') === 'true') setSidebarCollapsed(true);
  });

  sidebarClose?.addEventListener('click', () => {
    if (desktopSidebar.matches) {
      if (document.body.classList.contains('sidebar-hovered')) {
        setSidebarCollapsed(false);
        return;
      }

      setSidebarCollapsed(!document.body.classList.contains('sidebar-collapsed'));
      return;
    }

    closeSidebar();
  });
  sidebar?.addEventListener('mouseenter', () => {
    if (!desktopSidebar.matches || !document.body.classList.contains('sidebar-collapsed')) return;
    document.body.classList.add('sidebar-hovered');
  });
  sidebar?.addEventListener('mouseleave', () => {
    document.body.classList.remove('sidebar-hovered');
  });
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

  const resourcePreviewModal = document.querySelector('[data-resource-preview-modal]');
  const resourcePreviewImage = resourcePreviewModal?.querySelector('[data-resource-preview-image]');
  const resourcePreviewTitle = resourcePreviewModal?.querySelector('[data-resource-preview-title]');
  const resourcePreviewSubtitle = resourcePreviewModal?.querySelector('[data-resource-preview-subtitle]');
  const resourcePreviewMeta = resourcePreviewModal?.querySelector('[data-resource-preview-meta]');
  const resourcePreviewEdit = resourcePreviewModal?.querySelector('[data-resource-preview-edit]');
  const closeResourcePreview = () => {
    resourcePreviewModal?.classList.remove('is-open');
    resourcePreviewModal?.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
  };

  document.querySelectorAll('[data-resource-preview]').forEach((button) => {
    button.addEventListener('click', () => {
      if (!resourcePreviewModal) return;
      if (resourcePreviewImage) {
        resourcePreviewImage.src = button.dataset.previewImage || '';
        resourcePreviewImage.alt = button.dataset.previewTitle || 'Product preview';
      }
      if (resourcePreviewTitle) resourcePreviewTitle.textContent = button.dataset.previewTitle || '';
      if (resourcePreviewSubtitle) resourcePreviewSubtitle.textContent = button.dataset.previewSubtitle || '';
      if (resourcePreviewEdit) resourcePreviewEdit.href = button.dataset.previewEdit || '#';
      if (resourcePreviewMeta) {
        resourcePreviewMeta.innerHTML = (button.dataset.previewMeta || '').split('|')
          .filter(Boolean)
          .map((value) => `<span>${value}</span>`)
          .join('');
      }
      resourcePreviewModal.classList.add('is-open');
      resourcePreviewModal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');
    });
  });
  resourcePreviewModal?.querySelectorAll('[data-resource-preview-close]').forEach((button) => button.addEventListener('click', closeResourcePreview));

  const userEditorModal = document.querySelector('[data-user-editor-modal]');
  const userEditorForm = userEditorModal?.querySelector('[data-user-modal-form]');
  const userEditorTitle = userEditorModal?.querySelector('#userModalTitle');
  const userEditorMethod = userEditorModal?.querySelector('[data-user-modal-method]');
  const userSubmitLabel = userEditorModal?.querySelector('[data-user-submit-label]');
  const userPassword = userEditorModal?.querySelector('[data-user-field="password"]');
  const userPasswordNote = userEditorModal?.querySelector('[data-user-password-note]');
  const setUserField = (field, value) => {
    const input = userEditorModal?.querySelector(`[data-user-field="${field}"]`);
    if (input) input.value = value || '';
  };
  const closeUserEditor = () => {
    userEditorModal?.classList.remove('is-open');
    userEditorModal?.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
  };

  document.querySelectorAll('[data-user-modal-open]').forEach((button) => {
    button.addEventListener('click', () => {
      if (!userEditorModal || !userEditorForm) return;

      const isEdit = button.dataset.mode === 'edit';
      userEditorForm.action = button.dataset.action || '';
      if (userEditorTitle) userEditorTitle.textContent = isEdit ? 'Edit User' : 'Add User';
      if (userSubmitLabel) userSubmitLabel.textContent = isEdit ? 'Update User' : 'Save User';

      if (userEditorMethod) {
        userEditorMethod.disabled = !isEdit;
        userEditorMethod.value = isEdit ? 'PUT' : '';
      }

      setUserField('name', isEdit ? button.dataset.name : '');
      setUserField('email', isEdit ? button.dataset.email : '');
      setUserField('phone', isEdit ? button.dataset.phone : '');
      setUserField('role', isEdit ? button.dataset.role : 'admin');
      setUserField('status', isEdit ? button.dataset.status : 'active');
      setUserField('password', '');

      if (userPassword) {
        userPassword.required = !isEdit;
        userPassword.placeholder = isEdit ? 'Leave blank to keep current password' : 'Minimum 6 characters';
      }

      if (userPasswordNote) {
        userPasswordNote.textContent = isEdit ? 'Leave blank to keep the current password.' : 'Required for new users.';
      }

      userEditorModal.classList.add('is-open');
      userEditorModal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');
      window.setTimeout(() => userEditorModal.querySelector('[data-user-field="name"]')?.focus(), 60);
    });
  });
  userEditorModal?.querySelectorAll('[data-user-modal-close]').forEach((button) => button.addEventListener('click', closeUserEditor));

  document.querySelectorAll('[data-user-view-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
      const target = document.getElementById(button.dataset.userViewToggle || '');
      if (!target) return;

      const isHidden = target.hasAttribute('hidden');
      document.querySelectorAll('.vx-user-detail-row').forEach((row) => {
        if (row !== target) row.setAttribute('hidden', '');
      });
      document.querySelectorAll('[data-user-view-toggle]').forEach((toggleButton) => {
        if (toggleButton !== button) toggleButton.classList.remove('is-active');
      });
      target.toggleAttribute('hidden', !isHidden);
      button.classList.toggle('is-active', isHidden);
    });
  });

  const resourceEditorModal = document.querySelector('[data-resource-editor-modal]');
  const resourceEditorForm = resourceEditorModal?.querySelector('[data-resource-modal-form]');
  const resourceEditorTitle = resourceEditorModal?.querySelector('#resourceModalTitle');
  const resourceEditorMethod = resourceEditorModal?.querySelector('[data-resource-modal-method]');
  const resourceSubmitLabel = resourceEditorModal?.querySelector('[data-resource-submit-label]');
  const setResourceField = (field, value) => {
    const input = resourceEditorModal?.querySelector(`[data-resource-field="${field}"]`);
    if (!input) return;

    if (input.type === 'checkbox') {
      input.checked = value === '1' || value === 'true' || value === true;
      return;
    }

    input.value = value || '';
    input.dispatchEvent(new Event('input', { bubbles: true }));
  };
  const closeResourceEditor = () => {
    resourceEditorModal?.classList.remove('is-open');
    resourceEditorModal?.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
  };

  document.querySelectorAll('[data-resource-modal-open]').forEach((button) => {
    button.addEventListener('click', () => {
      if (!resourceEditorModal || !resourceEditorForm) return;

      const isEdit = button.dataset.mode === 'edit';
      const resource = button.dataset.resource || 'resource';
      const singular = resource.replace(/s$/, '').replace(/-/g, ' ');
      resourceEditorForm.reset();
      resourceEditorForm.action = button.dataset.action || '';
      if (resourceEditorTitle) resourceEditorTitle.textContent = `${isEdit ? 'Edit' : 'Add'} ${singular.charAt(0).toUpperCase()}${singular.slice(1)}`;
      if (resourceSubmitLabel) resourceSubmitLabel.textContent = `${isEdit ? 'Update' : 'Save'} ${singular.charAt(0).toUpperCase()}${singular.slice(1)}`;

      if (resourceEditorMethod) {
        resourceEditorMethod.disabled = !isEdit;
        resourceEditorMethod.value = isEdit ? 'PUT' : '';
      }

      [
        'name', 'sku', 'slug', 'categoryId', 'shortDescription', 'description', 'price', 'salePrice',
        'stock', 'reviewsCount', 'status', 'isFeatured', 'isBestseller', 'sortOrder', 'image',
        'customerName', 'customerTitle', 'productId', 'rating', 'avatar', 'comment',
      ].forEach((field) => setResourceField(field, isEdit ? button.dataset[field] : ''));

      if (!isEdit) {
        setResourceField('stock', '1');
        setResourceField('reviewsCount', '0');
        setResourceField('sortOrder', '0');
        setResourceField('rating', '5');
        setResourceField('status', 'active');
      }

      resourceEditorModal.classList.add('is-open');
      resourceEditorModal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');
      window.setTimeout(() => resourceEditorModal.querySelector('input, select, textarea')?.focus(), 60);
    });
  });
  resourceEditorModal?.querySelectorAll('[data-resource-modal-close]').forEach((button) => {
    button.addEventListener('click', closeResourceEditor);
  });

  const orderEditorModal = document.querySelector('[data-order-editor-modal]');
  const orderEditorForm = orderEditorModal?.querySelector('[data-order-modal-form]');
  if (orderEditorModal && orderEditorModal.parentElement !== document.body) {
    document.body.appendChild(orderEditorModal);
  }
  const setOrderField = (field, value) => {
    const input = orderEditorModal?.querySelector(`[data-order-field="${field}"]`);
    if (input) input.value = value || '';
  };
  const closeOrderEditor = () => {
    orderEditorModal?.classList.remove('is-open');
    orderEditorModal?.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
  };

  document.querySelectorAll('[data-order-modal-open]').forEach((button) => {
    button.addEventListener('click', () => {
      if (!orderEditorModal || !orderEditorForm) return;

      orderEditorForm.action = button.dataset.action || '';
      [
        'customerName', 'email', 'phone', 'zip', 'address', 'address2', 'city', 'state',
        'status', 'paymentStatus', 'trackingStatus', 'trackingNumber', 'trackingNote', 'adminNote',
      ].forEach((field) => setOrderField(field, button.dataset[field]));

      orderEditorModal.classList.add('is-open');
      orderEditorModal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');
      window.setTimeout(() => orderEditorModal.querySelector('input, select, textarea')?.focus(), 60);
    });
  });
  orderEditorModal?.querySelectorAll('[data-order-modal-close]').forEach((button) => {
    button.addEventListener('click', closeOrderEditor);
  });

  const reportViewModal = document.querySelector('[data-report-view-modal]');
  if (reportViewModal && reportViewModal.parentElement !== document.body) {
    document.body.appendChild(reportViewModal);
  }
  const reportViewUrl = reportViewModal?.querySelector('[data-report-view-url]');
  const setReportText = (field, value) => {
    const element = reportViewModal?.querySelector(`[data-report-${field}]`);
    if (element) element.textContent = value || '-';
  };
  const closeReportView = () => {
    reportViewModal?.classList.remove('is-open');
    reportViewModal?.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
  };

  document.querySelectorAll('[data-report-modal-open]').forEach((button) => {
    button.addEventListener('click', () => {
      if (!reportViewModal) return;

      setReportText('order', button.dataset.order ? `Order ${button.dataset.order}` : 'Order Report');
      setReportText('customer', button.dataset.customer);
      setReportText('email', button.dataset.email);
      setReportText('total', button.dataset.total);
      setReportText('payment', button.dataset.payment);
      setReportText('tracking', button.dataset.tracking);
      setReportText('created', button.dataset.created);
      if (reportViewUrl) reportViewUrl.href = button.dataset.viewUrl || '#';

      reportViewModal.classList.add('is-open');
      reportViewModal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');
    });
  });
  reportViewModal?.querySelectorAll('[data-report-modal-close]').forEach((button) => {
    button.addEventListener('click', closeReportView);
  });

  document.querySelectorAll('[data-resource-view-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
      const target = document.getElementById(button.dataset.resourceViewToggle || '');
      if (!target) return;

      const isHidden = target.hasAttribute('hidden');
      document.querySelectorAll('.vx-user-detail-row').forEach((row) => {
        if (row !== target) row.setAttribute('hidden', '');
      });
      document.querySelectorAll('[data-resource-view-toggle], [data-user-view-toggle]').forEach((toggleButton) => {
        if (toggleButton !== button) toggleButton.classList.remove('is-active');
      });
      target.toggleAttribute('hidden', !isHidden);
      button.classList.toggle('is-active', isHidden);
    });
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeResourcePreview();
      closeUserEditor();
      closeResourceEditor();
      closeOrderEditor();
      closeReportView();
    }
  });
  document.addEventListener('fullscreenchange', syncFullscreen);

  const dashboardCanvas = document.querySelector('[data-dashboard-chart]');
  const dashboardChartData = document.querySelector('[data-dashboard-chart-data]');
  if (dashboardCanvas && dashboardChartData) {
    const chartData = JSON.parse(dashboardChartData.textContent || '{}');
    const drawDashboardChart = () => {
      const context = dashboardCanvas.getContext('2d');
      const bounds = dashboardCanvas.parentElement.getBoundingClientRect();
      const ratio = Math.min(window.devicePixelRatio || 1, 2);
      const width = Math.max(320, bounds.width);
      const height = Math.max(260, bounds.height);
      dashboardCanvas.width = width * ratio;
      dashboardCanvas.height = height * ratio;
      dashboardCanvas.style.width = `${width}px`;
      dashboardCanvas.style.height = `${height}px`;
      context.setTransform(ratio, 0, 0, ratio, 0, 0);
      context.clearRect(0, 0, width, height);

      const labels = chartData.labels || [];
      const revenue = chartData.revenue || [];
      const orders = chartData.orders || [];
      const padding = { top: 20, right: 18, bottom: 34, left: 48 };
      const chartWidth = width - padding.left - padding.right;
      const chartHeight = height - padding.top - padding.bottom;
      const maxRevenue = Math.max(...revenue, 1);
      const maxOrders = Math.max(...orders, 1);
      const dark = document.body.classList.contains('admin-dark-page');
      const gridColor = dark ? 'rgba(255,255,255,.09)' : 'rgba(47,43,61,.09)';
      const textColor = dark ? '#8f91a5' : '#a5a3ae';
      const xAt = (index) => padding.left + (labels.length <= 1 ? chartWidth / 2 : (index / (labels.length - 1)) * chartWidth);
      const revenueY = (value) => padding.top + chartHeight - (value / maxRevenue) * chartHeight;

      context.font = '11px Public Sans';
      context.fillStyle = textColor;
      context.textAlign = 'right';
      context.textBaseline = 'middle';
      for (let index = 0; index <= 4; index += 1) {
        const y = padding.top + (chartHeight / 4) * index;
        context.strokeStyle = gridColor;
        context.lineWidth = 1;
        context.beginPath();
        context.moveTo(padding.left, y);
        context.lineTo(width - padding.right, y);
        context.stroke();
        const value = maxRevenue * (1 - index / 4);
        context.fillText(`£${Math.round(value)}`, padding.left - 8, y);
      }

      const ordersY = (value) => padding.top + chartHeight - (value / maxOrders) * chartHeight;

      if (revenue.length) {
        const gradient = context.createLinearGradient(0, padding.top, 0, padding.top + chartHeight);
        gradient.addColorStop(0, 'rgba(247, 174, 26, .28)');
        gradient.addColorStop(1, 'rgba(247, 174, 26, 0)');
        context.beginPath();
        revenue.forEach((value, index) => {
          const x = xAt(index);
          const y = revenueY(value);
          if (index === 0) context.moveTo(x, y);
          else context.lineTo(x, y);
        });
        context.lineTo(xAt(revenue.length - 1), padding.top + chartHeight);
        context.lineTo(xAt(0), padding.top + chartHeight);
        context.closePath();
        context.fillStyle = gradient;
        context.fill();

        context.beginPath();
        revenue.forEach((value, index) => {
          const x = xAt(index);
          const y = revenueY(value);
          if (index === 0) context.moveTo(x, y);
          else context.lineTo(x, y);
        });
        context.strokeStyle = '#f7ae1a';
        context.lineWidth = 2.5;
        context.lineJoin = 'round';
        context.lineCap = 'round';
        context.stroke();
      }

      if (orders.length) {
        context.beginPath();
        orders.forEach((value, index) => {
          const x = xAt(index);
          const y = ordersY(value);
          if (index === 0) context.moveTo(x, y);
          else context.lineTo(x, y);
        });
        context.strokeStyle = '#28c76f';
        context.lineWidth = 2.5;
        context.lineJoin = 'round';
        context.lineCap = 'round';
        context.stroke();
      }

      const labelStep = Math.max(1, Math.ceil(labels.length / 6));
      context.fillStyle = textColor;
      context.textAlign = 'center';
      context.textBaseline = 'top';
      labels.forEach((label, index) => {
        if (index % labelStep !== 0 && index !== labels.length - 1) return;
        context.fillText(label, xAt(index), padding.top + chartHeight + 10);
      });
    };

    const chartResizeObserver = new ResizeObserver(drawDashboardChart);
    chartResizeObserver.observe(dashboardCanvas.parentElement);
    document.addEventListener('admin-theme-change', drawDashboardChart);
    drawDashboardChart();
  }

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
