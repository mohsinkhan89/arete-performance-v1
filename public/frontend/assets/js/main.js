document.addEventListener("DOMContentLoaded", () => {
  const siteLoader = document.querySelector("[data-site-loader]");
  const loaderProgress = document.querySelector("[data-loader-progress]");
  const loaderPercent = document.querySelector("[data-loader-percent]");
  const loaderStorageKey = "arete_loader_seen";
  let loaderValue = 0;
  let loaderTimer = null;

  const hasSeenLoader = () => {
    try {
      return localStorage.getItem(loaderStorageKey) === "true";
    } catch (error) {
      return false;
    }
  };

  const markLoaderSeen = () => {
    try {
      localStorage.setItem(loaderStorageKey, "true");
    } catch (error) {
      // Storage can be unavailable in private browsing; the loader still works normally.
    }
  };

  const setLoaderProgress = (value) => {
    loaderValue = Math.max(loaderValue, Math.min(100, Math.round(value)));
    if (loaderProgress) loaderProgress.style.width = `${loaderValue}%`;
    if (loaderPercent) loaderPercent.textContent = `${loaderValue}%`;
  };

  const hideSiteLoader = () => {
    setLoaderProgress(100);
    markLoaderSeen();
    window.setTimeout(() => {
      siteLoader?.classList.add("is-hidden");
      document.documentElement.classList.add("loader-seen");
    }, 220);
    if (loaderTimer) window.clearInterval(loaderTimer);
  };

  const showSiteLoader = () => {
    if (hasSeenLoader()) return;
    loaderValue = 0;
    siteLoader?.classList.remove("is-hidden");
    setLoaderProgress(18);
  };

  if (siteLoader) {
    if (hasSeenLoader()) {
      siteLoader.classList.add("is-hidden");
      document.documentElement.classList.add("loader-seen");
    } else {
      setLoaderProgress(12);
      loaderTimer = window.setInterval(() => {
        if (loaderValue < 68) setLoaderProgress(loaderValue + Math.random() * 9);
      }, 170);
      window.setTimeout(() => setLoaderProgress(72), 620);
      window.setTimeout(hideSiteLoader, 1450);
      window.addEventListener("load", () => window.setTimeout(hideSiteLoader, 1450));
      window.addEventListener("pageshow", (event) => {
        if (event.persisted) hideSiteLoader();
      });
    }
  }

  document.addEventListener("click", (event) => {
    const link = event.target.closest("a[href]");
    if (!link || !siteLoader) return;

    const href = link.getAttribute("href") || "";
    const isNewTab = link.target && link.target !== "_self";
    const isUtilityLink = href.startsWith("#") || href.startsWith("mailto:") || href.startsWith("tel:") || href.startsWith("javascript:");

    if (isNewTab || isUtilityLink || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

    const url = new URL(link.href, window.location.href);
    if (url.origin === window.location.origin) showSiteLoader();
  });

  document.addEventListener("submit", (event) => {
    if (!siteLoader || event.target.matches(".search-form")) return;
    showSiteLoader();
  });

  const cookieConsent = document.querySelector("[data-cookie-consent]");
  const cookieAccept = document.querySelector("[data-cookie-accept]");
  const cookieReject = document.querySelector("[data-cookie-reject]");
  const cookieToggle = document.querySelector("[data-cookie-toggle]");
  const cookieCustomize = document.querySelector("[data-cookie-customize]");
  const cookieAnalytics = document.querySelector("[data-cookie-analytics]");
  const cookieMarketing = document.querySelector("[data-cookie-marketing]");
  const cookieStorageKey = "arete_cookie_preferences";

  const openCookieConsent = () => {
    if (!cookieConsent) return;
    cookieConsent.classList.add("is-visible");
    cookieConsent.setAttribute("aria-hidden", "false");
  };

  const closeCookieConsent = () => {
    if (!cookieConsent) return;
    cookieConsent.classList.remove("is-visible");
    cookieConsent.setAttribute("aria-hidden", "true");
  };

  const saveCookiePreferences = (preferences) => {
    localStorage.setItem(cookieStorageKey, JSON.stringify({
      ...preferences,
      necessary: true,
      savedAt: new Date().toISOString(),
    }));
    closeCookieConsent();
  };

  if (cookieConsent && !localStorage.getItem(cookieStorageKey)) {
    window.setTimeout(openCookieConsent, 1700);
  }

  cookieAccept?.addEventListener("click", () => saveCookiePreferences({ analytics: true, marketing: true }));
  cookieReject?.addEventListener("click", () => saveCookiePreferences({ analytics: false, marketing: false }));
  cookieToggle?.addEventListener("click", () => {
    const isOpen = cookieCustomize?.classList.toggle("is-open");
    cookieToggle.textContent = isOpen ? "Save Preferences" : "Customize";

    if (!isOpen) {
      saveCookiePreferences({
        analytics: Boolean(cookieAnalytics?.checked),
        marketing: Boolean(cookieMarketing?.checked),
      });
    }
  });

  const autoDismissAlerts = (selector, delay = 4000) => {
    document.querySelectorAll(selector).forEach((alert) => {
      window.setTimeout(() => {
        alert.classList.add("is-hiding");
        alert.addEventListener("animationend", () => alert.remove(), { once: true });
      }, delay);
    });
  };

  autoDismissAlerts(".alert, .track-alert");

  const products = [
    { id: "anavar-50", name: "Anavar 50", meta: "Oxandrolone", price: 59.99, image: "assets/images/product-bottle.png" },
    { id: "cardarine", name: "Cardarine", meta: "GW-501516", price: 69.99, image: "assets/images/product-bottle.png" },
    { id: "nolvadex-20", name: "Nolvadex 20", meta: "Tamoxifen", price: 49.99, image: "assets/images/categories-imgs/post-cycle-therapy.png" },
    { id: "hgh-191aa", name: "HGH 191AA", meta: "10 IU", price: 149.99, image: "assets/images/product-bottle.png" },
    { id: "bpc-157", name: "BPC-157 5mg", meta: "Peptides", price: 59.99, image: "assets/images/categories-imgs/peptides.png" },
    { id: "pct-complete-stack", name: "PCT Complete Stack", meta: "Post Cycle Therapy", price: 89.99, image: "assets/images/category-boxes.svg" },
    { id: "testosterone-enanthate", name: "Testosterone Enanthate", meta: "Hormones", price: 49.99, image: "assets/images/product-bottle.png" },
    { id: "male-enhancement-stack", name: "Male Enhancement Stack", meta: "Sexual Health", price: 79.99, image: "assets/images/categories-imgs/sexual-health.png" },
    { id: "clenbuterol", name: "Clenbuterol 40mcg", meta: "Fat Burners", price: 49.99, image: "assets/images/categories-imgs/fat-burrners.png" },
    { id: "insulin-syringes", name: "Insulin Syringes 1ml", meta: "Syringes & Needles", price: 9.99, image: "assets/images/categories-imgs/injection.png" },
    { id: "winstrol", name: "Winstrol 10mg", meta: "Orals", price: 54.99, image: "assets/images/categories-imgs/orals.png" },
    { id: "cjc-1295", name: "CJC-1295 2mg", meta: "Peptides", price: 74.99, image: "assets/images/categories-imgs/peptides.png" },
    { id: "trenbolone-acetate", name: "Trenbolone Acetate", meta: "Hormones", price: 64.99, image: "assets/images/product-bottle.png" },
    { id: "whey-protein-isolate", name: "Whey Protein Isolate", meta: "Protein", price: 59.99, image: "assets/images/product-bottle.png" },
    { id: "whey-protein-concentrate", name: "Whey Protein Concentrate", meta: "Protein", price: 49.99, image: "assets/images/categories-imgs/peptides.png" },
    { id: "whey-hydrolysate", name: "Whey Hydrolysate", meta: "Protein", price: 64.99, image: "assets/images/product-bottle.png" },
    { id: "whey-protein-blend", name: "Whey Protein Blend", meta: "Protein", price: 54.99, image: "assets/images/categories-imgs/orals.png" },
    { id: "whey-isolate-unflavored", name: "Whey Isolate (Unflavored)", meta: "Protein", price: 59.99, image: "assets/images/product-bottle.png" },
    { id: "whey-protein-shaker", name: "Whey Protein + Shaker", meta: "Protein", price: 69.99, image: "assets/images/category-boxes.svg" },
    { id: "whey-isolate-chocolate", name: "Whey Isolate (Chocolate)", meta: "Protein", price: 59.99, image: "assets/images/product-bottle.png" },
    { id: "whey-concentrate", name: "Whey Concentrate", meta: "Protein", price: 44.99, image: "assets/images/categories-imgs/sexual-health.png" },
    { id: "whey-isolate-vanilla", name: "Whey Isolate (Vanilla)", meta: "Protein", price: 59.99, image: "assets/images/product-bottle.png" },
    { id: "whey-mass-gainer", name: "Whey Mass Gainer", meta: "Protein", price: 69.99, image: "assets/images/product-bottle.png" },
    { id: "whey-protein-strawberry", name: "Whey Protein (Strawberry)", meta: "Protein", price: 54.99, image: "assets/images/categories-imgs/orals.png" },
    { id: "whey-blend-cookies", name: "Whey Blend (Cookies & Cream)", meta: "Protein", price: 54.99, image: "assets/images/product-bottle.png" },
    { id: "ostarine", name: "Ostarine MK-2866", meta: "Enobosarm", price: 49.99, image: "assets/images/product-bottle.png" },
    { id: "masteron-enanthate", name: "Masteron Enanthate", meta: "Performance", price: 49.99, image: "assets/images/categories-imgs/peptides.png" },
  ];

  const cart = new Map([
    ["cardarine", 1],
    ["anavar-50", 1],
    ["pct-complete-stack", 1],
  ]);

  if (document.body.classList.contains("order-success-page")) {
    cart.clear();
  }

  const navLinks = document.querySelectorAll(".navbar .nav-link");
  const siteHeader = document.querySelector(".site-header");
  const navbarCollapse = document.querySelector("#mainNav");
  const toastElement = document.querySelector("#cartToast");
  const toast = window.bootstrap && toastElement ? bootstrap.Toast.getOrCreateInstance(toastElement, { delay: 1800 }) : null;
  const toastBody = toastElement?.querySelector(".toast-body");

  const searchPanel = document.querySelector(".search-panel");
  const searchInput = document.querySelector("#siteSearch");
  const searchResults = document.querySelector(".search-results");
  const searchForm = document.querySelector(".search-form");
  const cartOverlay = document.querySelector(".cart-overlay");
  const cartDrawer = document.querySelector(".cart-drawer");
  const cartItems = document.querySelector(".cart-items");
  const cartEmpty = document.querySelector(".cart-empty");
  const cartCounts = document.querySelectorAll(".cart-count");
  const cartSubtotal = document.querySelector(".cart-subtotal");
  const productTracks = document.querySelectorAll(".product-track");
  const reportLightbox = document.querySelector(".report-lightbox");
  const reportLightboxImage = document.querySelector("[data-report-lightbox-image]");
  const reportLightboxTitle = document.querySelector("[data-report-lightbox-title]");
  const stockNotifyModal = document.createElement("div");
  stockNotifyModal.className = "stock-notify-modal";
  stockNotifyModal.setAttribute("aria-hidden", "true");
  stockNotifyModal.innerHTML = `
    <div class="stock-notify-dialog" role="dialog" aria-modal="true" aria-labelledby="stockNotifyTitle">
      <button class="stock-notify-close" type="button" data-stock-notify-close aria-label="Close notification form"><i class="fa-solid fa-xmark"></i></button>
      <span><i class="fa-solid fa-bell"></i></span>
      <h2 id="stockNotifyTitle">Request Notification</h2>
      <p data-stock-notify-product>Share your details and we will contact you as soon as stock returns.</p>
      <form data-stock-notify-form>
        <input type="hidden" name="product_id" data-stock-notify-id>
        <label>Full Name<input type="text" name="name" required maxlength="120"></label>
        <label>Email Address<input type="email" name="email" required maxlength="255"></label>
        <label>Phone Number<input type="tel" name="phone" maxlength="30"></label>
        <label>Required Quantity<input type="number" name="quantity" min="1" max="999" value="1"></label>
        <label>Additional information<textarea name="message" maxlength="1000" rows="3"></textarea></label>
        <button class="btn btn-gold" type="submit">Send Request <i class="fa-solid fa-paper-plane"></i></button>
      </form>
    </div>`;
  document.body.appendChild(stockNotifyModal);

  const money = (value) => `£${Number(value || 0).toFixed(2)}`;
  const findProduct = (id) => products.find((product) => product.id === id);
  const cartQty = () => [...cart.values()].reduce((total, qty) => total + qty, 0);
  const csrfToken = document.querySelector("meta[name='csrf-token']")?.content || "";
  const stockNotifyStorageKey = "arete_stock_notified_products";
  let activeStockNotifyButton = null;

  function notifiedProductIds() {
    try {
      return JSON.parse(localStorage.getItem(stockNotifyStorageKey) || "[]");
    } catch (error) {
      return [];
    }
  }

  function rememberStockNotified(productId) {
    if (!productId) return;
    const ids = new Set(notifiedProductIds().map(String));
    ids.add(String(productId));
    localStorage.setItem(stockNotifyStorageKey, JSON.stringify([...ids]));
  }

  function setStockButtonNotified(button) {
    if (!button) return;
    button.disabled = true;
    button.classList.add("is-notified");
    button.setAttribute("aria-disabled", "true");
    button.innerHTML = 'Notified <i class="fa-solid fa-check"></i>';
  }

  function applyStockNotifiedState(root = document) {
    const ids = new Set(notifiedProductIds().map(String));
    if (!ids.size) return;
    root.querySelectorAll("[data-stock-notify]").forEach((button) => {
      if (ids.has(String(button.dataset.stockNotify))) {
        setStockButtonNotified(button);
      }
    });
  }

  function prepareAnimatedHeadings() {
    document.querySelectorAll(".section-heading h2, .why-intro h2").forEach((heading) => {
      if (heading.dataset.animatedHeading === "true") return;
      const text = heading.textContent.trim();
      if (!text) return;

      let charIndex = 0;
      heading.innerHTML = text.split(" ").map((word) => {
        const letters = [...word].map((letter) => {
          const span = `<span class="heading-char" style="--char-index:${charIndex}">${letter}</span>`;
          charIndex += 1;
          return span;
        }).join("");
        return `<span class="heading-word">${letters}</span>`;
      }).join('<span class="heading-space" aria-hidden="true"></span>');

      heading.classList.add("animated-heading");
      heading.dataset.animatedHeading = "true";
      heading.setAttribute("aria-label", text);
    });
  }

  function preparePageAnimations() {
    const revealSelectors = [
      ".category-card",
      ".benefit",
      ".product-card",
      ".quote-card",
      ".testimonial-stats > div",
      ".footer-main > *",
      ".footer-feature-strip > div",
      ".footer-bottom",
      ".shop-sidebar",
      ".shop-toolbar",
      ".bundle-banner",
      ".shop-trust-grid > div",
      ".shop-newsletter-inner > *",
      ".cart-items-panel",
      ".order-summary",
      ".checkout-step",
      ".checkout-summary",
      ".success-card",
      ".recommended-products",
      ".product-gallery",
      ".product-purchase",
      ".product-benefit-strip > div",
      ".tab-content-card",
      ".related-card",
      ".reviews-panel",
      ".usage-panel"
    ];

    const cards = document.querySelectorAll([
      ".category-card",
      ".product-card",
      ".shop-product-card",
      ".quote-card",
      ".success-card",
      ".cart-items-panel",
      ".order-summary",
      ".checkout-summary",
      ".product-info-card",
      ".related-card"
    ].join(","));

    document.querySelectorAll(revealSelectors.join(",")).forEach((item, index) => {
      if (!item.classList.contains("reveal-up") && !item.classList.contains("reveal-on-scroll")) {
        item.classList.add("reveal-on-scroll");
      }
      if (!item.dataset.anim) {
        item.dataset.anim = index % 5 === 0 ? "scale" : index % 3 === 0 ? "left" : index % 3 === 1 ? "right" : "soft";
      }
    });

    cards.forEach((card) => card.classList.add("motion-card"));

    document.querySelectorAll(".hero-bottle, .product-main-image > img, .benefits-product-shot img, .bundle-products img").forEach((asset, index) => {
      asset.classList.add("float-asset");
      asset.style.animationDelay = `${(index % 4) * 160}ms`;
    });

    document.querySelectorAll(".btn-gold, .success-check, .product-category-pill").forEach((item) => {
      item.classList.add("pulse-glow");
    });
  }

  function updateHeaderState() {
    siteHeader.classList.toggle("is-scrolled", window.scrollY > 20);
  }

  function showToast(message) {
    if (!toast || !toastBody) return;
    toastBody.textContent = message;
    toast.show();
  }

  function openReportLightbox(src, title = "Test Report") {
    if (!reportLightbox || !reportLightboxImage) return;
    reportLightboxImage.src = src;
    reportLightboxImage.alt = title;
    if (reportLightboxTitle) reportLightboxTitle.textContent = title;
    reportLightbox.classList.add("is-open");
    reportLightbox.setAttribute("aria-hidden", "false");
    document.body.classList.add("panel-open");
  }

  function closeReportLightbox() {
    if (!reportLightbox) return;
    reportLightbox.classList.remove("is-open");
    reportLightbox.setAttribute("aria-hidden", "true");
    document.body.classList.remove("panel-open");
    if (reportLightboxImage) reportLightboxImage.src = "";
  }

  function openStockNotify(productId, productName = "Selected product", triggerButton = null) {
    activeStockNotifyButton = triggerButton;
    stockNotifyModal.querySelector("[data-stock-notify-id]").value = productId || "";
    stockNotifyModal.querySelector("[data-stock-notify-product]").textContent = `${productName}: share your details and we will contact you as soon as stock returns.`;
    stockNotifyModal.classList.add("is-open");
    stockNotifyModal.setAttribute("aria-hidden", "false");
    document.body.classList.add("panel-open");
    setTimeout(() => stockNotifyModal.querySelector("input[name='name']")?.focus(), 120);
  }

  function closeStockNotify() {
    stockNotifyModal.classList.remove("is-open");
    stockNotifyModal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("panel-open");
    syncBodyLock();
  }

  async function submitStockNotify(form) {
    const productId = form.querySelector("[data-stock-notify-id]")?.value;
    if (!productId) return;

    const submitButton = form.querySelector("button[type='submit']");
    submitButton.disabled = true;
    submitButton.innerHTML = 'Sending... <i class="fa-solid fa-spinner fa-spin"></i>';

    try {
      const response = await fetch(endpoint(route("stockNotifyBase", "/stock-notify"), productId), {
        method: "POST",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify(Object.fromEntries(new FormData(form).entries()))
      });
      if (!response.ok) throw new Error("Stock notify request failed.");
      const data = await response.json();
      showToast(data.message || "Thanks. We will contact you when this product is available.");
      rememberStockNotified(productId);
      setStockButtonNotified(activeStockNotifyButton);
      applyStockNotifiedState();
      form.reset();
      closeStockNotify();
    } catch (error) {
      showToast("Unable to send request right now.");
    } finally {
      submitButton.disabled = false;
      submitButton.innerHTML = 'Send Request <i class="fa-solid fa-paper-plane"></i>';
      activeStockNotifyButton = null;
    }
  }

  function initProductDescriptionSections() {
    const source = document.querySelector("[data-product-description-source]");
    if (!source) return;

    if (source.dataset.fallbackDescription === "1") {
      const availableTabs = document.querySelector('[data-tab-card="reviews"]')?.dataset.serverContent === "1"
        ? ["description", "reviews"]
        : ["description"];

      document.querySelectorAll("[data-tab-card]").forEach((card) => {
        const key = card.dataset.tabCard;
        card.classList.toggle("is-hidden", !availableTabs.includes(key));
      });

      document.querySelectorAll(".product-tabs").forEach((nav) => {
        const externalTabs = nav.querySelectorAll("[data-external-tab]").length;
        nav.style.setProperty("--visible-tabs", String(availableTabs.length + externalTabs));
        nav.querySelectorAll("a").forEach((link) => {
          if (link.hasAttribute("data-external-tab")) {
            link.hidden = false;
            link.classList.remove("is-hidden");
            return;
          }
          const key = link.getAttribute("href")?.replace("#", "");
          const isVisible = availableTabs.includes(key);
          link.hidden = !isVisible;
          link.classList.toggle("is-hidden", !isVisible);
        });
      });

      return;
    }

    const targets = {
      description: document.querySelector('[data-description-section="description"]'),
      specs: document.querySelector('[data-description-section="specs"]'),
      // assurance: document.querySelector('[data-description-section="assurance"]'),
      benefits: document.querySelector('[data-description-section="benefits"]'),
      dosage: document.querySelector('[data-description-section="dosage"]'),
      dosageSteps: document.querySelector('[data-description-section="dosage-steps"]'),
      dosageNote: document.querySelector('[data-description-section="dosage-note"]'),
      ingredients: document.querySelector('[data-description-section="ingredients"]'),
      ingredientCards: document.querySelector('[data-description-section="ingredient-cards"]'),
      faq: document.querySelector('[data-description-section="faq"]'),
    };

    const tabOrder = ["description", "benefits", "dosage", "ingredients", "reviews", "faq"];
    const sectionTitles = {};
    const buckets = Object.fromEntries(["description", "benefits", "dosage", "ingredients", "faq"].map((key) => [key, []]));
    const renderExactDescriptionLayout = () => {
      if (!source.querySelector(".product-info-grid")) return false;

      const descriptionCard = document.querySelector('[data-tab-card="description"]');
      const nav = descriptionCard?.querySelector(".product-tabs");
      if (!descriptionCard || !nav) return false;

      [...descriptionCard.children].forEach((child) => {
        if (child !== nav) child.remove();
      });

      const holder = document.createElement("div");
      holder.innerHTML = source.innerHTML;
      holder.querySelectorAll(".product-tabs").forEach((sourceNav) => sourceNav.remove());

      holder.querySelectorAll(":scope > .product-info-grid, :scope > .description-assurance").forEach((child) => {
        descriptionCard.appendChild(child.cloneNode(true));
      });

      return true;
    };

    const hasExactDescriptionLayout = renderExactDescriptionLayout();

    const sectionFromHeading = (text) => {
      const value = String(text || "").toLowerCase();
      if (value.includes("key benefit") || value.includes("benefit")) return "benefits";
      if (value.includes("recommended dosage") || value.includes("recommanded dosage") || value.includes("dosage") || value.includes("dose") || value.includes("usage")) return "dosage";
      if (value.includes("ingredient") || value.includes("composition")) return "ingredients";
      if (value.includes("faq") || value.includes("faqs") || value.includes("question")) return "faq";
      if (value.includes("description") || value.includes("about")) return "description";
      return null;
    };

    const iconForText = (text, fallback = "fa-circle-check") => {
      const value = String(text || "").toLowerCase();
      const matches = [
        [["add to cart", "cart", "basket"], "fa-cart-plus"],
        [["place order", "order", "checkout"], "fa-clipboard-check"],
        [["press send", "send", "whatsapp", "message"], "fa-paper-plane"],
        [["we confirm", "confirm", "confirmed"], "fa-circle-check"],
        [["fast", "secure delivery", "discreet shipping", "shipping", "delivery"], "fa-truck-fast"],
        [["delivery all over uk", "worldwide", "all over", "uk", "country", "countries", "global"], "fa-globe"],
        [["flat delivery", "£4.99", "4.99", "price", "fee"], "fa-sterling-sign"],
        [["pharma grade", "pharma", "grade", "premium"], "fa-prescription-bottle-medical"],
        [["lab tested", "lab", "tested", "verified", "report"], "fa-flask-vial"],
        [["new arrivals", "new arrival"], "fa-box-open"],
        [["top rated", "rated", "review", "reviews"], "fa-star"],
        [["featured", "recommended", "highly recommended"], "fa-medal"],
        [["increased strength", "strength", "power output"], "fa-weight-hanging"],
        [["lean muscle", "muscle growth", "muscle", "growth"], "fa-dumbbell"],
        [["recover", "recovery", "fatigue"], "fa-bolt"],
        [["performance", "endurance", "athletic"], "fa-person-running"],
        [["lean", "weight", "cut", "fat"], "fa-weight-hanging"],
        [["dose", "dosage", "tablet", "capsule", "mg"], "fa-prescription-bottle-medical"],
        [["week", "cycle", "phase", "routine"], "fa-calendar-days"],
        [["target", "goal", "result", "progress"], "fa-bullseye"],
        [["ingredient", "pure", "leaf", "natural"], "fa-leaf"],
        [["safe", "quality", "trusted", "premium"], "fa-shield-halved"],
      ];
      return matches.find(([words]) => words.some((word) => value.includes(word)))?.[1] || fallback;
    };

    const cleanText = (value) => String(value || "").replace(/\s+/g, " ").trim();

    const sectionMarkerSelector = "strong, b, h2, h3, h4";
    const isSectionMarker = (node) => sectionFromHeading(cleanText(node?.textContent));
    const hasSectionMarkers = (node) => [...(node.querySelectorAll?.(sectionMarkerSelector) || [])].some(isSectionMarker);

    const itemFromNode = (node, fallbackTitle = "Detail") => {
      const clone = node.cloneNode(true);
      const titleNode = clone.querySelector?.("strong, b, h3, h4");
      let title = cleanText(titleNode?.textContent);

      if (titleNode) titleNode.remove();

      let copy = cleanText(clone.textContent);
      if (!title) {
        const text = cleanText(node.textContent);
        const split = text.match(/^([^:.-]{3,70})[:.-]\s*(.+)$/);
        title = cleanText(split?.[1]) || fallbackTitle;
        copy = cleanText(split?.[2]) || text;
      }

      if (copy === title) copy = cleanText(node.textContent);

      return { title, copy, original: node };
    };

    const textUntilNextTitle = (marker) => {
      const parts = [];
      let sibling = marker.nextSibling;

      while (sibling) {
        if (sibling.nodeType === Node.ELEMENT_NODE && sibling.matches(sectionMarkerSelector)) break;
        parts.push(sibling.textContent || "");
        sibling = sibling.nextSibling;
      }

      return cleanText(parts.join(" "));
    };

    const collectContentItems = (nodes, fallbackTitle) => {
      const wrapper = document.createElement("div");
      nodes.forEach((node) => wrapper.appendChild(node.cloneNode(true)));

      const listItems = [...wrapper.querySelectorAll("li")];
      if (listItems.length) return listItems.map((item) => itemFromNode(item, fallbackTitle));

      const inlineTitles = [...wrapper.querySelectorAll("strong, b")]
        .filter((title) => !isSectionMarker(title) && !title.closest("table"));

      if (inlineTitles.length) {
        return inlineTitles.map((title) => {
          const parentCopy = textUntilNextTitle(title) || cleanText(title.parentElement?.textContent).replace(cleanText(title.textContent), "");
          return {
            title: cleanText(title.textContent) || fallbackTitle,
            copy: cleanText(parentCopy),
            original: title,
          };
        }).filter((item) => item.title || item.copy);
      }

      const items = [];
      const children = [...wrapper.children];
      for (let index = 0; index < children.length; index += 1) {
        const child = children[index];
        if (/^H[3-6]$/.test(child.tagName) && children[index + 1]?.tagName === "P") {
          items.push({
            title: cleanText(child.textContent) || fallbackTitle,
            copy: cleanText(children[index + 1].textContent),
            original: child,
          });
          index += 1;
          continue;
        }

        if (child.tagName === "P" || child.tagName === "DIV") {
          items.push(itemFromNode(child, fallbackTitle));
        }
      }

      return items;
    };

    const looksLikeSpecBlock = (node) => {
      const text = node.textContent?.toLowerCase() || "";
      return text.includes("product name") && text.includes("sku");
    };

    const updateSpecs = (node) => {
      if (!targets.specs || !node) return;

      const rows = [];
      node.querySelectorAll("dt").forEach((term) => {
        const value = term.nextElementSibling?.matches("dd") ? term.nextElementSibling : null;
        if (value) rows.push([cleanText(term.textContent), cleanText(value.textContent)]);
      });

      node.querySelectorAll("tr").forEach((row) => {
        const cells = [...row.children].map((cell) => cleanText(cell.textContent)).filter(Boolean);
        if (cells.length >= 2) rows.push([cells[0], cells.slice(1).join(" ")]);
      });

      if (!rows.length) {
        const lines = cleanText(node.textContent)
          .split(/(?=(?:Product Name|Generic Name|Strength|Form|Quantity|Category|Brand|SKU|Price|Stock)\s*:)/i)
          .map((line) => line.trim())
          .filter(Boolean);

        lines.forEach((line) => {
          const match = line.match(/^([^:]+):\s*(.+)$/);
          if (match) rows.push([cleanText(match[1]) + ":", cleanText(match[2])]);
        });
      }

      if (!rows.length) return;

      targets.specs.innerHTML = "";
      rows.forEach(([label, value]) => {
        const item = document.createElement("div");
        const dt = document.createElement("dt");
        const dd = document.createElement("dd");
        dt.textContent = label.endsWith(":") ? label : `${label}:`;
        dd.textContent = value;
        item.append(dt, dd);
        targets.specs.appendChild(item);
      });
    };

    const pullSpecsFromDescription = () => {
      if (!targets.description) return;

      targets.description.querySelectorAll("dl, table, p, div").forEach((node) => {
        if (!looksLikeSpecBlock(node)) return;
        updateSpecs(node);
        node.remove();
      });
    };

    const moveAfter = (node, target) => {
      if (!node || !target?.parentNode) return target;
      target.parentNode.insertBefore(node, target.nextSibling);
      return node;
    };

    const normalizeSource = () => {
      const movableSelector = "p, h2, h3, h4, ul, ol, blockquote, pre";
      source.querySelectorAll(".product-specs, dl").forEach((specList) => {
        let insertionPoint = specList;

        specList.querySelectorAll(`dt ${movableSelector}, dd ${movableSelector}`).forEach((block) => {
          insertionPoint = moveAfter(block, insertionPoint);
        });

        Array.from(specList.children).forEach((child) => {
          if (child.matches("div, dt, dd")) return;
          if (!child.matches(movableSelector)) return;
          insertionPoint = moveAfter(child, insertionPoint);
        });
      });

      source.querySelectorAll("td h2, td h3, td h4, th h2, th h3, th h4").forEach((heading) => {
        const table = heading.closest("table");
        if (table) moveAfter(heading, table);
      });
    };

    const buildBenefitCards = (nodes) => {
      const items = collectContentItems(nodes, "Benefit");
      if (!items.length) return nodes;

      return items.map((item) => {
        const card = document.createElement("div");
        const title = document.createElement("strong");
        const copy = document.createElement("span");
        const icon = document.createElement("i");
        icon.className = `fa-solid ${iconForText(`${item.title} ${item.copy}`, "fa-dumbbell")}`;
        title.textContent = item.title;
        copy.textContent = item.copy;
        card.append(icon, title, copy);
        return card;
      });
    };

    const benefitItemsFromRenderedContent = (target) => {
      if (!target || target.querySelector(":scope > div > i")) return [];

      const items = [];
      const titleNodes = [...target.querySelectorAll("h3, h4, strong, b")]
        .filter((node) => !node.closest("figure, table") && !isSectionMarker(node));

      titleNodes.forEach((titleNode) => {
        const title = cleanText(titleNode.textContent);
        if (!title) return;

        let sibling = titleNode.nextElementSibling;
        while (sibling && !cleanText(sibling.textContent)) sibling = sibling.nextElementSibling;

        const copy = sibling && !sibling.matches("h3, h4, strong, b")
          ? cleanText(sibling.textContent)
          : textUntilNextTitle(titleNode);

        items.push({ title, copy, original: titleNode });
      });

      return items.filter((item) => item.title || item.copy);
    };

    const ensureBenefitIconRows = () => {
      if (!targets.benefits) return;

      const items = benefitItemsFromRenderedContent(targets.benefits);
      if (!items.length) return;

      targets.benefits.innerHTML = "";
      buildIconCards(items, "fa-dumbbell").forEach((node) => targets.benefits.appendChild(node));
    };

    const buildIconCards = (items, fallbackIcon = "fa-circle-check") => items.map((item) => {
      const card = document.createElement("div");
      const icon = document.createElement("i");
      const title = document.createElement("strong");
      const copy = document.createElement("span");
      icon.className = `fa-solid ${iconForText(`${item.title} ${item.copy}`, fallbackIcon)}`;
      title.textContent = item.title;
      copy.textContent = item.copy;
      card.append(icon, title, copy);
      return card;
    });

    const appendHtmlToBucket = (html, bucketKey) => {
      if (!bucketKey || !buckets[bucketKey] || !cleanText(html.replace(/<[^>]*>/g, " "))) return;
      const holder = document.createElement("div");
      holder.innerHTML = html.trim();
      const children = [...holder.childNodes].filter((child) => cleanText(child.textContent));

      if (!children.length) return;
      children.forEach((child) => {
        if (child.nodeType === Node.TEXT_NODE) {
          const paragraph = document.createElement("p");
          paragraph.textContent = cleanText(child.textContent);
          buckets[bucketKey].push(paragraph);
          return;
        }

        buckets[bucketKey].push(child.cloneNode(true));
      });
    };

    const splitInlineSections = (node, initialSection) => {
      if (node.matches?.("table")) return false;

      const clone = node.cloneNode(true);
      const markers = [...clone.querySelectorAll(sectionMarkerSelector)]
        .map((marker) => ({ marker, section: isSectionMarker(marker) }))
        .filter((entry) => entry.section);

      if (!markers.length) return false;

      let markedHtml = clone.innerHTML;
      markers.forEach(({ marker, section }) => {
        sectionTitles[section] = sectionTitles[section] || cleanText(marker.textContent);
        markedHtml = markedHtml.replace(marker.outerHTML, `<!--PRODUCT_SECTION:${section}-->`);
      });

      let activeSection = initialSection;
      markedHtml.split(/<!--PRODUCT_SECTION:(description|benefits|dosage|ingredients|faq)-->/).forEach((part, index) => {
        if (!part) return;
        if (index % 2 === 1) {
          activeSection = part;
          return;
        }

        appendHtmlToBucket(part, activeSection);
      });

      return true;
    };

    const dosageStepItems = (nodes) => {
      const wrapper = document.createElement("div");
      nodes.forEach((node) => wrapper.appendChild(node.cloneNode(true)));

      const timelineTitles = [...wrapper.querySelectorAll("h3, h4, strong, b")]
        .filter((title) => {
          const text = cleanText(title.textContent).toLowerCase();
          return text && !isSectionMarker(title) && /week|post\s*cycle|cycle|phase/.test(text);
        });

      if (!timelineTitles.length) return collectContentItems(nodes, "Step");

      return timelineTitles.map((titleNode) => {
        const title = cleanText(titleNode.textContent);
        let phase = "";
        let copy = "";
        const container = titleNode.parentElement && titleNode.parentElement !== wrapper ? titleNode.parentElement : titleNode;
        const containerText = cleanText(container.textContent).replace(title, "").trim();
        let sibling = container.nextElementSibling;

        if (containerText) {
          const split = containerText.match(/^([^.:â€“-]{3,60})[.:â€“-]\s*(.+)$/);
          phase = cleanText(split?.[1]);
          copy = cleanText(split?.[2]) || containerText;
        }

        while (sibling && !cleanText(sibling.textContent)) sibling = sibling.nextElementSibling;

        if (!copy && sibling && !sibling.matches("h3, h4, strong, b")) {
          const phaseNode = sibling.querySelector?.("strong, b");
          if (phaseNode && !isSectionMarker(phaseNode)) {
            phase = cleanText(phaseNode.textContent);
            copy = cleanText(sibling.textContent).replace(phase, "").trim();
          } else {
            const text = cleanText(sibling.textContent);
            const split = text.match(/^([^.:â€“-]{3,60})[.:â€“-]\s*(.+)$/);
            phase = cleanText(split?.[1]);
            copy = cleanText(split?.[2]) || text;
          }

          if (!copy && sibling.nextElementSibling && !sibling.nextElementSibling.matches("h3, h4, strong, b")) {
            copy = cleanText(sibling.nextElementSibling.textContent);
          }
        }

        return {
          title,
          copy: [phase, copy].filter(Boolean).join(": "),
          original: titleNode,
        };
      }).filter((item) => item.title || item.copy);
    };

    const renderDosageIntro = (nodes) => {
      if (!targets.dosage) return;

      const titleNode = targets.dosage.querySelector("[data-section-title]");
      targets.dosage.innerHTML = "";
      if (titleNode) targets.dosage.appendChild(titleNode);

      for (const node of nodes) {
        const text = cleanText(node.textContent);
        if (/^note\b|week\s*\d|post\s*cycle/i.test(text)) break;
        if (node.querySelector?.("h3, h4, strong, b") && /week\s*\d|post\s*cycle/i.test(text)) break;
        targets.dosage.appendChild(node.cloneNode(true));
      }
    };

    const buildDosageCards = (nodes) => {
      const items = dosageStepItems(nodes);
      if (!items.length) return [];

      return items.slice(0, 4).map((item, index) => {
        const card = document.createElement("div");
        const icon = document.createElement("i");
        const title = document.createElement("strong");
        const label = document.createElement("span");
        const copy = document.createElement("small");
        const splitCopy = cleanText(item.copy).match(/^([^.:–-]{3,60})[.:–-]\s*(.+)$/);
        const stepIcons = ["fa-flask-vial", "fa-chart-line", "fa-bullseye", "fa-rotate"];
        icon.className = `fa-solid ${stepIcons[index] || iconForText(`${item.title} ${item.copy}`, "fa-chart-line")}`;
        title.textContent = item.title || `Step ${index + 1}`;
        label.textContent = cleanText(splitCopy?.[1]) || item.title;
        copy.textContent = cleanText(splitCopy?.[2]) || item.copy || item.title;
        card.append(icon, title, label, copy);
        return card;
      });
    };

    const buildDosageNote = (nodes) => {
      const noteNode = nodes.find((node) => /^note\b/i.test(cleanText(node.textContent)));
      if (!noteNode) return false;

      const text = cleanText(noteNode.textContent).replace(/^note\s*:?\s*/i, "");
      if (!text || !targets.dosageNote) return false;

      const icon = document.createElement("i");
      const paragraph = document.createElement("p");
      const title = document.createElement("strong");
      icon.className = "fa-solid fa-info";
      title.textContent = "Note:";
      paragraph.append(title, document.createTextNode(` ${text}`));
      targets.dosageNote.innerHTML = "";
      targets.dosageNote.append(icon, paragraph);
      return true;
    };

    const buildIngredientCards = (nodes) => {
      const wrapper = document.createElement("div");
      nodes.forEach((node) => wrapper.appendChild(node.cloneNode(true)));

      const listItems = [...wrapper.querySelectorAll("li")];
      const inlineTitles = [...wrapper.querySelectorAll("strong, b")]
        .filter((title) => !isSectionMarker(title) && !title.closest("table"));
      const items = listItems.length
        ? listItems.map((item) => itemFromNode(item, "Ingredient"))
        : inlineTitles.map((title) => ({
            title: cleanText(title.textContent) || "Ingredient",
            copy: textUntilNextTitle(title),
            original: title,
          }));

      if (!items.length) return [];

      return items.slice(0, 3).map((item) => {
        const card = document.createElement("div");
        const icon = document.createElement("i");
        const title = document.createElement("strong");
        const copy = document.createElement("span");
        icon.className = `fa-solid ${iconForText(`${item.title} ${item.copy}`, "fa-leaf")}`;
        title.textContent = item.title;
        copy.textContent = item.copy;
        card.append(icon, title, copy);
        return card;
      });
    };

    const normalizePastedAdminHtml = () => {
      if (!source.querySelector(".product-description, .product-specs") || !/key benefits|recommended dosage|ingredients/i.test(source.textContent || "")) {
        return false;
      }

      const stripInlineFormatting = (node) => {
        node.querySelectorAll?.("[style], font").forEach((child) => {
          child.removeAttribute("style");
          if (child.tagName === "FONT") {
            child.replaceWith(...child.childNodes);
          }
        });
        node.removeAttribute?.("style");
        return node;
      };

      const textTokens = (() => {
        const walker = document.createTreeWalker(source, NodeFilter.SHOW_TEXT);
        const tokens = [];
        let node = walker.nextNode();

        while (node) {
          const text = cleanText(node.textContent);
          if (text && text !== "\u00a0") tokens.push(text);
          node = walker.nextNode();
        }

        return tokens;
      })();

      const indexOfToken = (pattern, from = 0) => textTokens.findIndex((token, index) => index >= from && pattern.test(token));
      const sliceBetween = (startPattern, endPattern) => {
        const start = indexOfToken(startPattern);
        if (start < 0) return [];
        const end = endPattern ? indexOfToken(endPattern, start + 1) : -1;
        return textTokens.slice(start + 1, end > start ? end : undefined);
      };

      const pairSequentialItems = (tokens) => {
        const items = [];
        for (let index = 0; index < tokens.length; index += 2) {
          const title = cleanText(tokens[index]);
          const copy = cleanText(tokens[index + 1]);
          if (!title || /^(key benefits|recommended dosage|ingredients)$/i.test(title)) continue;
          if (title.length > 80) continue;
          items.push({ title, copy });
        }
        return items;
      };

      const appendTitle = (target, key, fallback) => {
        const title = document.createElement("h2");
        title.dataset.sectionTitle = key;
        title.textContent = sectionTitles[key] || fallback;
        target.appendChild(title);
      };

      const productDescription = source.querySelector(".product-description");
      if (productDescription && targets.description) {
        targets.description.innerHTML = "";
        [...productDescription.children]
          .filter((child) => !(child.matches?.("h1, h2, h3, h4") && cleanText(child.textContent).toLowerCase() === "description"))
          .forEach((child) => targets.description.appendChild(stripInlineFormatting(child.cloneNode(true))));
      }

      const specs = source.querySelector(".product-specs, dl");
      if (specs) updateSpecs(specs);

      const dosageHeadingPattern = /^(?:recommended\s+)?dosage(?:\s*&\s*safety)?$/i;
      const benefitsTokens = sliceBetween(/^key benefits$/i, dosageHeadingPattern);
      const benefitItems = pairSequentialItems(benefitsTokens).slice(0, 6);
      if (benefitItems.length && targets.benefits) {
        sectionTitles.benefits = "Key Benefits";
        targets.benefits.innerHTML = "";
        buildIconCards(benefitItems, "fa-dumbbell").forEach((node) => targets.benefits.appendChild(node));
      }

      const dosageTokens = sliceBetween(dosageHeadingPattern, /^ingredients$/i);
      if (dosageTokens.length && targets.dosage) {
        sectionTitles.dosage = "Recommended Dosage";
        const noteIndex = dosageTokens.findIndex((token) => /^note\b/i.test(token));
        const cleanDosageTokens = noteIndex >= 0 ? dosageTokens.slice(0, noteIndex) : dosageTokens;
        const noteText = noteIndex >= 0 ? dosageTokens.slice(noteIndex).join(" ").replace(/^note\s*:?\s*/i, "") : "";
        const dosageStepPattern = /^(?:week\s*\d+|post\s*cycle|phase\s*\d+|follow-up\s*phase)$/i;
        const firstStep = cleanDosageTokens.findIndex((token) => dosageStepPattern.test(token));
        const intro = firstStep > 0 ? cleanDosageTokens.slice(0, firstStep).join(" ") : cleanDosageTokens[0] || "";
        const stepTokens = firstStep >= 0 ? cleanDosageTokens.slice(firstStep) : [];

        targets.dosage.innerHTML = "";
        appendTitle(targets.dosage, "dosage", "Recommended Dosage");
        if (intro) {
          const paragraph = document.createElement("p");
          paragraph.textContent = intro;
          targets.dosage.appendChild(paragraph);
        }

        if (targets.dosageSteps && stepTokens.length) {
          const steps = [];
          for (let index = 0; index < stepTokens.length; index += 3) {
            const title = cleanText(stepTokens[index]);
            if (!dosageStepPattern.test(title)) {
              index -= 2;
              continue;
            }
            steps.push({
              title,
              phase: cleanText(stepTokens[index + 1]),
              copy: cleanText(stepTokens[index + 2]),
            });
          }

          targets.dosageSteps.innerHTML = "";
          steps.slice(0, 4).forEach((step, index) => {
            const card = document.createElement("div");
            const icon = document.createElement("i");
            const title = document.createElement("strong");
            const label = document.createElement("span");
            const copy = document.createElement("small");
            const stepIcons = ["fa-flask-vial", "fa-chart-line", "fa-bullseye", "fa-rotate"];
            icon.className = `fa-solid ${stepIcons[index] || "fa-chart-line"}`;
            title.textContent = step.title;
            label.textContent = step.phase || step.title;
            copy.textContent = step.copy || step.phase || step.title;
            card.append(icon, title, label, copy);
            targets.dosageSteps.appendChild(card);
          });
        }

        if (targets.dosageNote) {
          if (noteText) {
            const icon = document.createElement("i");
            const paragraph = document.createElement("p");
            const title = document.createElement("strong");
            icon.className = "fa-solid fa-info";
            title.textContent = "Note:";
            paragraph.append(title, document.createTextNode(` ${noteText}`));
            targets.dosageNote.innerHTML = "";
            targets.dosageNote.append(icon, paragraph);
            targets.dosageNote.hidden = false;
          } else {
            targets.dosageNote.hidden = true;
          }
        }
      }

      const ingredientsTokens = sliceBetween(/^ingredients$/i);
      if (ingredientsTokens.length && targets.ingredients) {
        sectionTitles.ingredients = "Ingredients";
        targets.ingredients.innerHTML = "";
        appendTitle(targets.ingredients, "ingredients", "Ingredients");

        const ingredientIntro = ingredientsTokens.find((token) => /^(?:each\b|the\s+declared\b|this\s+product\b)/i.test(token));
        if (ingredientIntro) {
          const paragraph = document.createElement("p");
          paragraph.textContent = ingredientIntro;
          targets.ingredients.appendChild(paragraph);
        }

        const table = source.querySelector(".ingredient-table, table");
        if (table) {
          targets.ingredients.appendChild(stripInlineFormatting(table.cloneNode(true)));
        }

        if (targets.ingredientCards) {
          const cardStart = ingredientsTokens.findIndex((token) => /pure ingredients/i.test(token));
          const cardItems = cardStart >= 0 ? pairSequentialItems(ingredientsTokens.slice(cardStart)).slice(0, 3) : [];
          targets.ingredientCards.innerHTML = "";
          if (cardItems.length) {
            buildIconCards(cardItems, "fa-leaf").forEach((node) => targets.ingredientCards.appendChild(node));
            targets.ingredientCards.hidden = false;
          } else {
            targets.ingredientCards.hidden = true;
          }
        }
      }

      if (targets.assurance) targets.assurance.hidden = true;
      if (targets.dosageSteps) targets.dosageSteps.hidden = !targets.dosageSteps.children.length;
      if (targets.specs) targets.specs.hidden = !targets.specs.children.length;
      targets.dosageSteps?.closest(".dosage-tab-grid")?.classList.toggle("is-single-column", !targets.dosageSteps?.children.length);
      targets.ingredientCards?.closest(".ingredients-tab-grid")?.classList.toggle("is-single-column", !targets.ingredientCards?.children.length);

      return true;
    };

    const formatIngredientTables = () => {
      targets.ingredients?.querySelectorAll("table").forEach((table) => {
        table.classList.add("ingredient-table");
        const firstRow = table.querySelector("tr");
        const firstCells = firstRow ? [...firstRow.children] : [];

        if (firstCells.length && !firstCells.some((cell) => cell.tagName === "TH")) {
          const rowText = cleanText(firstRow.textContent).toLowerCase();
          if (rowText.includes("ingredient") || rowText.includes("amount")) {
            firstCells.forEach((cell) => {
              const th = document.createElement("th");
              th.innerHTML = cell.innerHTML;
              cell.replaceWith(th);
            });
          }
        }

        table.querySelectorAll("tr").forEach((row) => {
          if (cleanText(row.firstElementChild?.textContent).toLowerCase() === "total") {
            row.classList.add("ingredient-total-row");
          }
        });
      });
    };

    const removeCardItemsFromContent = (target, sourceItems) => {
      if (!target || !sourceItems.length) return;
      target.querySelectorAll("ul, ol").forEach((list) => list.remove());

      sourceItems.forEach((item) => {
        const title = cleanText(item.title);
        if (!title) return;

        target.querySelectorAll("p, div").forEach((node) => {
          const strong = node.querySelector?.("strong, b");
          if (strong && cleanText(strong.textContent) === title && !node.querySelector("table")) {
            node.remove();
          }
        });
      });
    };

    const removeMatchingTextNodes = (target, matcher) => {
      if (!target) return;
      target.querySelectorAll("p, div").forEach((node) => {
        if (!node.querySelector("table") && matcher(cleanText(node.textContent))) node.remove();
      });
    };

    const hasSectionData = (key) => {
      if (key === "description") return Boolean(hasExactDescriptionLayout || buckets.description.length || targets.description?.children.length || targets.specs?.children.length);
      if (key === "reviews") return document.querySelector('[data-tab-card="reviews"]')?.dataset.serverContent === "1";
      if (key === "benefits") return Boolean(buckets.benefits?.length || targets.benefits?.children.length);
      if (key === "dosage") return Boolean(buckets.dosage?.length || targets.dosage?.children.length || targets.dosageSteps?.children.length);
      if (key === "ingredients") return Boolean(buckets.ingredients?.length || targets.ingredients?.children.length || targets.ingredientCards?.children.length);
      if (key === "faq") return Boolean(buckets.faq?.length || targets.faq?.children.length);
      return Boolean(buckets[key]?.length);
    };

    const refreshVisibleTabs = () => {
      const availableTabs = tabOrder.filter(hasSectionData);
      document.querySelectorAll("[data-tab-card]").forEach((card) => {
        const key = card.dataset.tabCard;
        card.classList.toggle("is-hidden", !availableTabs.includes(key));
      });

      document.querySelectorAll(".product-tabs").forEach((nav) => {
        const externalTabs = nav.querySelectorAll("[data-external-tab]").length;
        nav.style.setProperty("--visible-tabs", String((availableTabs.length || 1) + externalTabs));
        nav.querySelectorAll("a").forEach((link) => {
          if (link.hasAttribute("data-external-tab")) {
            link.hidden = false;
            link.classList.remove("is-hidden");
            return;
          }
          const key = link.getAttribute("href")?.replace("#", "");
          const isVisible = availableTabs.includes(key);
          link.hidden = !isVisible;
          link.classList.toggle("is-hidden", !isVisible);
        });

        const visibleLinks = [...nav.querySelectorAll("a")].filter((link) => !link.hidden);
        if (!visibleLinks.some((link) => link.classList.contains("active")) && visibleLinks[0]) {
          visibleLinks[0].classList.add("active");
        }
      });
    };

    const buildFaqItems = (nodes) => {
      const wrapper = document.createElement("div");
      nodes.forEach((node) => wrapper.appendChild(node.cloneNode(true)));
      const detailsItems = [...wrapper.querySelectorAll("details")];
      if (detailsItems.length) return detailsItems;

      const listItems = [...wrapper.querySelectorAll("li")];
      const paragraphItems = [...wrapper.querySelectorAll("p")];
      const sourceItems = listItems.length ? listItems : paragraphItems;
      if (!sourceItems.length) return nodes;

      return sourceItems.map((item, index) => {
        const details = document.createElement("details");
        if (index === 0) details.open = true;
        const summary = document.createElement("summary");
        const paragraph = document.createElement("p");
        const text = cleanText(item.textContent);
        const titleNode = item.querySelector?.("strong, b");
        const parts = text.split("?");
        summary.textContent = cleanText(titleNode?.textContent) || (parts.length > 1 ? `${parts.shift()}?` : `Question ${index + 1}`);
        paragraph.textContent = cleanText(parts.join("?")) || text.replace(summary.textContent, "").trim() || text;
        details.append(summary, paragraph);
        return details;
      });
    };

    const updateSectionTitles = () => {
      document.querySelectorAll("[data-section-title]").forEach((title) => {
        const key = title.dataset.sectionTitle;
        const value = sectionTitles[key] || "";
        title.textContent = value;
        title.hidden = !value;
      });
    };

    const updateAssuranceCards = () => {
      if (!targets.assurance) return;
      if (hasExactDescriptionLayout) return;

      const sourceItems = collectContentItems(buckets.description, "Feature")
        .filter((item) => /stock|shipping|delivery|lab|test|certified|quality|support|secure|worldwide/i.test(`${item.title} ${item.copy}`));

      if (!sourceItems.length) {
        targets.assurance.hidden = true;
        return;
      }

      targets.assurance.innerHTML = "";
      buildIconCards(sourceItems.slice(0, 4), "fa-shield-halved").forEach((node) => targets.assurance.appendChild(node));
      targets.assurance.hidden = false;
      removeCardItemsFromContent(targets.description, sourceItems);
    };

    if (normalizePastedAdminHtml()) {
      formatIngredientTables();
      refreshVisibleTabs();
      return;
    }

    normalizeSource();

    let current = "description";
    [...source.children].forEach((node) => {
      if (hasExactDescriptionLayout && node.matches?.(".product-info-grid, .description-assurance")) {
        return;
      }

      if (looksLikeSpecBlock(node) && !hasSectionMarkers(node)) {
        updateSpecs(node);
        return;
      }

      if (node.matches?.("table") && /ingredient|amount per tablet|composition/i.test(node.textContent || "")) {
        current = "ingredients";
        buckets.ingredients.push(node.cloneNode(true));
        return;
      }

      const headingSection = /^H[1-6]$/.test(node.tagName) ? sectionFromHeading(node.textContent) : null;
      if (headingSection) {
        current = headingSection;
        sectionTitles[current] = cleanText(node.textContent);
        return;
      }

      if (splitInlineSections(node, current)) return;

      buckets[current].push(node.cloneNode(true));
    });

    Object.entries({
      description: targets.description,
      benefits: targets.benefits,
      dosage: targets.dosage,
      ingredients: targets.ingredients,
      faq: targets.faq,
    }).forEach(([key, target]) => {
      if (!target || !buckets[key].length) return;
      if (key === "description" && hasExactDescriptionLayout) return;

      const titleNode = target.querySelector("[data-section-title]");
      target.innerHTML = "";
      if (titleNode) target.appendChild(titleNode);

      const nodes = key === "benefits" ? buildBenefitCards(buckets[key]) : key === "faq" ? buildFaqItems(buckets[key]) : buckets[key];
      nodes.forEach((node) => target.appendChild(node));
    });

    ensureBenefitIconRows();
    pullSpecsFromDescription();
    updateSectionTitles();
    updateAssuranceCards();

    const dosageItems = dosageStepItems(buckets.dosage);
    const dosageCards = buildDosageCards(buckets.dosage);
    if (targets.dosageSteps && dosageCards.length) {
      targets.dosageSteps.innerHTML = "";
      dosageCards.forEach((node) => targets.dosageSteps.appendChild(node));
      renderDosageIntro(buckets.dosage);
    }
    if (!buildDosageNote(buckets.dosage) && targets.dosageNote) targets.dosageNote.hidden = true;
    removeMatchingTextNodes(targets.dosage, (text) => /^note\b/i.test(text));

    const ingredientItems = collectContentItems(buckets.ingredients, "Ingredient");
    const ingredientCards = buildIngredientCards(buckets.ingredients);
    if (targets.ingredientCards && ingredientCards.length) {
      targets.ingredientCards.innerHTML = "";
      ingredientCards.forEach((node) => targets.ingredientCards.appendChild(node));
      removeCardItemsFromContent(targets.ingredients, ingredientItems);
    }
    if (targets.ingredientCards && !ingredientCards.length) targets.ingredientCards.hidden = true;
    if (targets.dosageSteps && !dosageCards.length) targets.dosageSteps.hidden = true;
    if (targets.specs && !targets.specs.children.length) targets.specs.hidden = true;
    targets.dosageSteps?.closest(".dosage-tab-grid")?.classList.toggle("is-single-column", !dosageCards.length);
    targets.ingredientCards?.closest(".ingredients-tab-grid")?.classList.toggle("is-single-column", !ingredientCards.length);

    formatIngredientTables();
    refreshVisibleTabs();
  }

  function syncBodyLock() {
    const hasOpenPanel = searchPanel.classList.contains("is-open") || cartDrawer.classList.contains("is-open");
    document.body.classList.toggle("panel-open", hasOpenPanel);
  }

  function setPanelState(panel, open) {
    panel.classList.toggle("is-open", open);
    panel.setAttribute("aria-hidden", String(!open));
    syncBodyLock();
  }

  function openSearch() {
    setPanelState(searchPanel, true);
    renderSearchResults("");
    setTimeout(() => searchInput.focus(), 120);
  }

  function closeSearch() {
    setPanelState(searchPanel, false);
  }

  function goToPage(url) {
    if (!url) return;
    window.location.href = url;
  }

  const routes = window.appRoutes || {};
  const route = (name, fallback) => routes[name] || fallback;
  let searchTimer = null;
  let searchRequestIndex = 0;

  function endpoint(base, id) {
    return `${base}/${id}`;
  }

  function setButtonLoading(button, isLoading, loadingText = "Please wait") {
    if (!button) return;

    if (!button.dataset.originalHtml) {
      button.dataset.originalHtml = button.innerHTML;
    }

    button.disabled = isLoading;
    button.classList.toggle("is-loading", isLoading);
    button.setAttribute("aria-busy", String(isLoading));
    button.innerHTML = isLoading
      ? `<span><i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> ${loadingText}</span>`
      : button.dataset.originalHtml;
  }

  function escapeHtml(value) {
    const div = document.createElement("div");
    div.textContent = value ?? "";
    return div.innerHTML;
  }

  async function cartRequest(url, method = "GET", body = null) {
    const response = await fetch(url, {
      method,
      headers: {
        "Accept": "application/json",
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken
      },
      body: body ? JSON.stringify(body) : null
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.message || "Cart request failed.");
    }

    return response.json();
  }

  async function openCart() {
    await renderCart();
    cartOverlay.classList.add("is-open");
    cartOverlay.setAttribute("aria-hidden", "false");
    setPanelState(cartDrawer, true);
  }

  function closeCart() {
    cartOverlay.classList.remove("is-open");
    cartOverlay.setAttribute("aria-hidden", "true");
    setPanelState(cartDrawer, false);
  }

  async function addToCart(id, quantity = 1) {
    if (!id) return false;
    const safeQty = Math.max(1, Number(quantity) || 1);
    try {
      const data = await cartRequest(endpoint(route("cartAddBase", "/cart/add"), id), "POST", { quantity: safeQty });
      await renderCart(data);
      cartOverlay.classList.add("is-open");
      cartOverlay.setAttribute("aria-hidden", "false");
      setPanelState(cartDrawer, true);
      showToast("Product added to your cart.");
      return true;
    } catch (error) {
      showToast(error.message || "Unable to update cart right now.");
      return false;
    }
  }

  async function updateQty(id, direction, currentQty = null) {
    const nextQty = Math.max(0, Number(currentQty ?? 1) + direction);
    try {
      const data = await cartRequest(endpoint(route("cartUpdateBase", "/cart/update"), id), "PATCH", { quantity: nextQty });
      renderCart(data);
      if (document.querySelector("[data-cart-row]")) window.location.reload();
    } catch (error) {
      showToast("Unable to update quantity.");
    }
  }

  async function removeFromCart(id) {
    try {
      const data = await cartRequest(endpoint(route("cartRemoveBase", "/cart/remove"), id), "DELETE");
      renderCart(data);
      if (document.querySelector("[data-cart-row]")) window.location.reload();
    } catch (error) {
      showToast("Unable to remove item.");
    }
  }

  async function clearServerCart() {
    try {
      const data = await cartRequest(route("cartClear", "/cart/clear"), "DELETE");
      renderCart(data);
      if (document.querySelector("[data-cart-row]")) window.location.reload();
    } catch (error) {
      showToast("Unable to clear cart.");
    }
  }

  async function renderCart(cartData = null) {
    const data = cartData || await cartRequest(route("cartJson", "/cart/json"));

    cartItems.innerHTML = data.items.map((product) => {
      return `
        <article class="cart-item">
          <img src="${product.image}" alt="${product.name}">
          <div>
            <h3>${product.name}</h3>
            <small>${product.meta} - ${money(product.unit_price)}</small>
            <div class="qty-control" aria-label="Quantity controls">
              <button type="button" data-cart-dec="${product.id}" data-cart-qty="${product.quantity}" aria-label="Decrease ${product.name}">-</button>
              <span>${product.quantity}</span>
              <button type="button" data-cart-inc="${product.id}" data-cart-qty="${product.quantity}" aria-label="Increase ${product.name}">+</button>
            </div>
          </div>
          <button class="remove-item" type="button" data-cart-remove="${product.id}" aria-label="Remove ${product.name}"><i class="fa-solid fa-trash-can"></i></button>
        </article>`;
    }).join("");

    cartCounts.forEach((cartCount) => { cartCount.textContent = data.item_count; });
    cartSubtotal.textContent = money(data.subtotal);
    cartEmpty.classList.toggle("is-visible", data.is_empty);
  }

  async function renderSearchResults(query) {
    const requestIndex = ++searchRequestIndex;
    const normalized = query.trim();
    const url = new URL(route("searchProducts", "/search/products"), window.location.origin);
    if (normalized) url.searchParams.set("q", normalized);

    searchResults.innerHTML = '<p class="no-results">Loading products...</p>';

    try {
      const response = await fetch(url, { headers: { Accept: "application/json" } });
      if (!response.ok) throw new Error("Search request failed.");

      const data = await response.json();
      if (requestIndex !== searchRequestIndex) return;

      const matches = Array.isArray(data.products) ? data.products : [];
      searchResults.innerHTML = matches.length ? matches.map((product) => `
      <article class="search-result">
        <img src="${escapeHtml(product.image)}" alt="${escapeHtml(product.name)}">
        <div class="me-auto">
          <strong>${escapeHtml(product.name)}</strong>
          <small>${escapeHtml(product.meta)} - ${money(product.price)}${product.in_stock ? "" : " - Out of Stock"}</small>
        </div>
        ${product.in_stock
          ? `<button type="button" data-search-add="${escapeHtml(product.id)}" aria-label="Add ${escapeHtml(product.name)}"><i class="fa-solid fa-cart-plus"></i></button>`
          : `<button class="notify-stock-btn" type="button" data-stock-notify="${escapeHtml(product.id)}" data-product-name="${escapeHtml(product.name)}">Inform Me</button>`}
      </article>`).join("") : '<p class="no-results">No products found.</p>';
      applyStockNotifiedState(searchResults);
    } catch (error) {
      if (requestIndex === searchRequestIndex) {
        searchResults.innerHTML = '<p class="no-results">Unable to load products right now.</p>';
      }
    }
  }

  // Set active link on page load
  const currentPathname = window.location.pathname;
  const currentHashname = window.location.hash;

  navLinks.forEach((link) => {
    const linkUrl = new URL(link.href, window.location.origin);
    const linkPathname = linkUrl.pathname;
    const linkHashname = linkUrl.hash;

    let isActive = false;

    if (linkHashname === "#contact") {
      isActive = (currentPathname === linkPathname && currentHashname === "#contact");
    } else if (linkPathname === "/") {
      isActive = (currentPathname === "/" && (currentHashname === "" || currentHashname === "#home" || !currentHashname));
    } else {
      isActive = (currentPathname === linkPathname);
    }

    link.classList.toggle("active", isActive);
  });

  navLinks.forEach((link) => {
    link.addEventListener("click", () => {
      navLinks.forEach((item) => item.classList.remove("active"));
      link.classList.add("active");

      if (window.bootstrap && navbarCollapse.classList.contains("show")) {
        bootstrap.Collapse.getOrCreateInstance(navbarCollapse).hide();
      }
    });
  });

  navbarCollapse.addEventListener("show.bs.collapse", () => siteHeader.classList.add("menu-open"));
  navbarCollapse.addEventListener("hidden.bs.collapse", () => siteHeader.classList.remove("menu-open"));
  window.addEventListener("scroll", updateHeaderState, { passive: true });

  document.querySelectorAll(".search-toggle").forEach((button) => button.addEventListener("click", openSearch));
  document.querySelector(".search-close").addEventListener("click", closeSearch);
  searchPanel.addEventListener("click", (event) => {
    if (event.target === searchPanel) closeSearch();
  });
  searchInput.addEventListener("input", (event) => {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(() => renderSearchResults(event.target.value), 220);
  });
  searchForm.addEventListener("submit", (event) => {
    event.preventDefault();
    const query = searchInput.value.trim();
    goToPage(`${route("search", "/search")}${query ? `?q=${encodeURIComponent(query)}` : ""}`);
  });

  document.querySelectorAll(".cart-btn").forEach((button) => button.addEventListener("click", openCart));
  document.querySelector(".cart-close").addEventListener("click", closeCart);
  cartOverlay.addEventListener("click", closeCart);
  productTracks.forEach((productTrack) => {
    const productSlider = productTrack.closest(".product-slider");
    const productSlides = [...productTrack.querySelectorAll(".bestseller-product-column")];
    const productPrev = productSlider?.querySelector(".slider-prev");
    const productNext = productSlider?.querySelector(".slider-next");
    let productAutoSlide = null;

    const productSlideStep = () => productSlides[0]?.getBoundingClientRect().width || 260;
    const productSliderHasOverflow = () => productTrack.scrollWidth - productTrack.clientWidth > 8;

    const updateProductSlider = () => {
      const hasOverflow = productSliderHasOverflow();
      if (productPrev) productPrev.hidden = !hasOverflow;
      if (productNext) productNext.hidden = !hasOverflow;

      if (!hasOverflow && productAutoSlide) {
        window.clearInterval(productAutoSlide);
        productAutoSlide = null;
      }

      return hasOverflow;
    };

    const moveProductSlider = (direction = 1) => {
      const maxScroll = productTrack.scrollWidth - productTrack.clientWidth;
      const reachedEnd = productTrack.scrollLeft >= maxScroll - 8;
      const reachedStart = productTrack.scrollLeft <= 8;
      const left = direction > 0 && reachedEnd
        ? 0
        : direction < 0 && reachedStart
          ? maxScroll
          : productTrack.scrollLeft + (productSlideStep() * direction);

      productTrack.scrollTo({ left, behavior: "smooth" });
    };

    const startProductAutoSlide = () => {
      if (productAutoSlide || !updateProductSlider()) return;
      productAutoSlide = window.setInterval(() => {
        if (!document.hidden) moveProductSlider(1);
      }, 4000);
    };

    const stopProductAutoSlide = () => {
      if (!productAutoSlide) return;
      window.clearInterval(productAutoSlide);
      productAutoSlide = null;
    };

    productPrev?.addEventListener("click", () => {
      moveProductSlider(-1);
      stopProductAutoSlide();
      startProductAutoSlide();
    });
    productNext?.addEventListener("click", () => {
      moveProductSlider(1);
      stopProductAutoSlide();
      startProductAutoSlide();
    });
    productSlider?.addEventListener("mouseenter", stopProductAutoSlide);
    productSlider?.addEventListener("mouseleave", startProductAutoSlide);
    productSlider?.addEventListener("focusin", stopProductAutoSlide);
    productSlider?.addEventListener("focusout", startProductAutoSlide);
    window.addEventListener("resize", () => {
      stopProductAutoSlide();
      updateProductSlider();
      startProductAutoSlide();
    });

    updateProductSlider();
    startProductAutoSlide();
  });

  document.querySelectorAll("[data-testimonial-slider]").forEach((slider) => {
    const track = slider.querySelector(".testimonial-track");
    const slides = [...slider.querySelectorAll(".testimonial-slide")];
    const pagination = slider.querySelector(".testimonial-pagination");
    const prev = slider.querySelector(".testimonial-prev");
    const next = slider.querySelector(".testimonial-next");
    let activeIndex = 0;

    function visibleCount() {
      if (!slides.length) return 1;
      return Math.max(1, Math.round(track.clientWidth / slides[0].getBoundingClientRect().width));
    }

    function maxIndex() {
      return Math.max(0, slides.length - visibleCount());
    }

    function setActiveDot(index) {
      pagination.querySelectorAll("button").forEach((dot, dotIndex) => {
        const isActive = dotIndex === index;
        dot.classList.toggle("is-active", isActive);
        dot.setAttribute("aria-current", isActive ? "true" : "false");
      });
    }

    function goTo(index) {
      activeIndex = Math.max(0, Math.min(index, maxIndex()));
      const target = slides[activeIndex];
      if (target) track.scrollTo({ left: target.offsetLeft - track.offsetLeft, behavior: "smooth" });
      setActiveDot(activeIndex);
    }

    function buildPagination() {
      const dots = maxIndex() + 1;
      pagination.innerHTML = Array.from({ length: dots }, (_, index) => (
        `<button type="button" aria-label="Show testimonial ${index + 1}"></button>`
      )).join("");
      pagination.querySelectorAll("button").forEach((dot, index) => {
        dot.addEventListener("click", () => goTo(index));
      });
      goTo(Math.min(activeIndex, maxIndex()));
    }

    function syncActiveFromScroll() {
      const width = slides[0]?.getBoundingClientRect().width || 1;
      const gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap) || 0;
      activeIndex = Math.max(0, Math.min(maxIndex(), Math.round(track.scrollLeft / (width + gap))));
      setActiveDot(activeIndex);
    }

    prev?.addEventListener("click", () => goTo(activeIndex - 1));
    next?.addEventListener("click", () => goTo(activeIndex + 1));
    track.addEventListener("scroll", syncActiveFromScroll, { passive: true });
    window.addEventListener("resize", buildPagination);
    buildPagination();
  });

  document.querySelectorAll(".quote-card p").forEach((text) => {
    text.setAttribute("tabindex", "0");
    text.setAttribute("role", "button");
    text.setAttribute("aria-expanded", "false");
    text.title = "Click to read full testimonial";

    function toggleTestimonial() {
      const card = text.closest(".quote-card");
      const isExpanded = card.classList.toggle("is-expanded");
      text.setAttribute("aria-expanded", String(isExpanded));
    }

    text.addEventListener("click", toggleTestimonial);
    text.addEventListener("keydown", (event) => {
      if (event.key !== "Enter" && event.key !== " ") return;
      event.preventDefault();
      toggleTestimonial();
    });
  });

  document.addEventListener("click", async (event) => {
    const productCard = event.target.closest(".product-card");
    const relatedCard = event.target.closest(".related-card");
    const categoryCard = event.target.closest(".category-card");
    const addFromSearch = event.target.closest("[data-search-add]");
    const stockNotify = event.target.closest("[data-stock-notify]");
    const stockNotifyClose = event.target.closest("[data-stock-notify-close]");
    const goTrigger = event.target.closest("[data-go]");
    const accountButton = event.target.closest(".icon-btn[aria-label='Account']");
    const clearFilters = event.target.closest("[data-clear-filters]");
    const clearSearch = event.target.closest("[aria-label='Clear search']");
    const sortButton = event.target.closest(".shop-toolbar button");
    const productThumb = event.target.closest("[data-product-image]");
    const productAdd = event.target.closest("[data-product-add]");
    const buyNow = event.target.closest("[data-buy-now]");
    const productQtyInc = event.target.closest("[data-product-qty-inc]");
    const productQtyDec = event.target.closest("[data-product-qty-dec]");
    const cardQtyInc = event.target.closest("[data-card-qty-inc]");
    const cardQtyDec = event.target.closest("[data-card-qty-dec]");
    const testReport = event.target.closest("[data-test-report]");
    const productZoom = event.target.closest("[data-product-zoom]");
    const productExpand = event.target.closest("[data-product-expand]");
    const labReport = event.target.closest("[data-lab-report]");
    const detailTapCard = event.target.closest(".product-benefit-strip > div > div, .benefit-list > div, .dosage-steps > div, .ingredient-certifications > div, .review-card, .shop-trust-grid > div");
    const inc = event.target.closest("[data-cart-inc]");
    const dec = event.target.closest("[data-cart-dec]");
    const remove = event.target.closest("[data-cart-remove]");
    const clearCart = event.target.closest("[data-cart-clear]");
    const drawerCheckout = event.target.closest(".cart-summary .btn");

    const qtyOutput = document.querySelector("[data-product-qty]");
    const getProductQty = () => Math.max(1, Number(qtyOutput?.textContent) || 1);
    const getCardQty = (button) => Math.max(1, Number(button?.closest(".product-card")?.querySelector("[data-card-qty]")?.textContent) || 1);
    const setCardQty = (button, qty) => {
      const output = button?.closest(".product-card")?.querySelector("[data-card-qty]");
      if (!output) return;
      output.textContent = String(Math.max(1, Math.min(99, qty)));
    };
    const setProductQty = (qty) => {
      if (!qtyOutput) return;
      qtyOutput.textContent = String(Math.max(1, Math.min(99, qty)));
    };

    if (stockNotify) {
      event.preventDefault();
      if (stockNotify.disabled) return;
      openStockNotify(stockNotify.dataset.stockNotify, stockNotify.dataset.productName || stockNotify.closest("[data-product-name]")?.dataset.productName, stockNotify);
      return;
    }

    if (stockNotifyClose || event.target === stockNotifyModal) {
      closeStockNotify();
      return;
    }

    if (goTrigger) goToPage(goTrigger.dataset.go);
    if (accountButton) goToPage(route("cart", "/my-cart"));
    if (clearFilters) goToPage(document.body.classList.contains("search-page") ? route("search", "/search") : route("shop", "/shop"));
    if (clearSearch) goToPage(route("search", "/search"));
    if (sortButton) goToPage(document.body.classList.contains("search-page") ? route("search", "/search") : route("shop", "/shop"));
    if (categoryCard && !event.target.closest("button, a")) goToPage(categoryCard.dataset.categoryUrl || route("shop", "/shop"));

    if (testReport) {
      openReportLightbox(testReport.dataset.testReport, testReport.dataset.testReportTitle || "Test Report");
      return;
    }

    if (productExpand) {
      const detailImage = document.querySelector("[data-product-main-image]");
      if (detailImage) openReportLightbox(detailImage.currentSrc || detailImage.src, detailImage.alt || "Product image");
      return;
    }

    if (cardQtyInc) {
      setCardQty(cardQtyInc, getCardQty(cardQtyInc) + 1);
      return;
    }

    if (cardQtyDec) {
      setCardQty(cardQtyDec, getCardQty(cardQtyDec) - 1);
      return;
    }

    if (event.target.closest("[data-cart-add]")) {
      const addButton = event.target.closest("[data-cart-add]");
      await addToCart(addButton.dataset.cartAdd, getCardQty(addButton));
    } else if (productCard && event.target.closest("button") && !event.target.closest("[data-card-qty-inc], [data-card-qty-dec], [data-test-report]")) {
      const index = [...document.querySelectorAll(".product-card")].indexOf(productCard);
      await addToCart(productCard.dataset.productId || products[index]?.id);
    } else if (productCard && !event.target.closest("a")) {
      goToPage(productCard.dataset.productUrl || route("productDetails", "/product-details"));
    }

    if (relatedCard && event.target.closest("button")) {
      await addToCart(relatedCard.dataset.productId);
    } else if (relatedCard) {
      goToPage(relatedCard.dataset.productUrl || route("productDetails", "/product-details"));
    }

    if (productThumb) {
      const mainImage = document.querySelector("[data-product-main-image]");
      if (mainImage) {
        productThumb.parentElement.querySelectorAll("[data-product-image]").forEach((thumb) => thumb.classList.remove("active"));
        productThumb.classList.add("active");
        mainImage.closest(".product-main-image")?.classList.add("is-swapping");
        mainImage.src = productThumb.dataset.productImage;
        mainImage.alt = productThumb.dataset.productAlt || "Product image";
        setTimeout(() => mainImage.closest(".product-main-image")?.classList.remove("is-swapping"), 360);
      }
    }

    if (productQtyInc) setProductQty(getProductQty() + 1);
    if (productQtyDec) setProductQty(getProductQty() - 1);
    if (productAdd) await addToCart(productAdd.dataset.productAdd, getProductQty());
    if (buyNow) {
      await addToCart(buyNow.dataset.buyNow, getProductQty());
      window.location.href = route("checkout", "/checkout");
    }
    if (labReport) {
      const detailReport = document.querySelector("[data-product-report]");
      if (detailReport?.dataset.productReport) {
        openReportLightbox(detailReport.dataset.productReport, detailReport.dataset.productReportTitle || "Test Report");
      } else {
        showToast("Lab report preview is opening soon.");
      }
    }
    if (drawerCheckout) goToPage(route("checkout", "/checkout"));

    if (detailTapCard && !event.target.closest("button, a, summary")) {
      detailTapCard.classList.remove("is-tapped");
      requestAnimationFrame(() => detailTapCard.classList.add("is-tapped"));
      setTimeout(() => detailTapCard.classList.remove("is-tapped"), 520);
    }

    if (addFromSearch) {
      const added = await addToCart(addFromSearch.dataset.searchAdd);
      if (added) closeSearch();
    }
    if (inc) updateQty(inc.dataset.cartInc, 1, inc.dataset.cartQty || inc.parentElement?.querySelector("span")?.textContent);
    if (dec) updateQty(dec.dataset.cartDec, -1, dec.dataset.cartQty || dec.parentElement?.querySelector("span")?.textContent);
    if (remove) removeFromCart(remove.dataset.cartRemove);
    if (clearCart) clearServerCart();
  });

  const productZoomArea = document.querySelector("[data-product-zoom]");

  if (productZoomArea && window.matchMedia("(hover: hover) and (pointer: fine)").matches) {
    const zoomImage = productZoomArea.querySelector("[data-product-main-image]");
    const zoomLens = productZoomArea.querySelector("[data-product-zoom-lens]");
    const zoomLevel = 2.35;

    productZoomArea.addEventListener("mousemove", (event) => {
      if (!zoomImage || !zoomLens) return;

      const bounds = productZoomArea.getBoundingClientRect();
      const x = event.clientX - bounds.left;
      const y = event.clientY - bounds.top;

      if (x < 0 || y < 0 || x > bounds.width || y > bounds.height) {
        zoomLens.style.display = "none";
        return;
      }

      const lensWidth = Math.min(230, bounds.width * 0.58);
      const lensHeight = Math.min(230, bounds.height * 0.58);
      const left = Math.max(0, Math.min(x - lensWidth / 2, bounds.width - lensWidth));
      const top = Math.max(0, Math.min(y - lensHeight / 2, bounds.height - lensHeight));
      const backgroundWidth = bounds.width * zoomLevel;
      const backgroundHeight = bounds.height * zoomLevel;
      const backgroundX = Math.max(0, Math.min(x * zoomLevel - lensWidth / 2, backgroundWidth - lensWidth));
      const backgroundY = Math.max(0, Math.min(y * zoomLevel - lensHeight / 2, backgroundHeight - lensHeight));

      zoomLens.style.display = "block";
      zoomLens.style.width = `${lensWidth}px`;
      zoomLens.style.height = `${lensHeight}px`;
      zoomLens.style.left = `${left}px`;
      zoomLens.style.top = `${top}px`;
      zoomLens.style.backgroundImage = `url("${zoomImage.currentSrc || zoomImage.src}")`;
      zoomLens.style.backgroundSize = `${backgroundWidth}px ${backgroundHeight}px`;
      zoomLens.style.backgroundPosition = `-${backgroundX}px -${backgroundY}px`;
    });

    productZoomArea.addEventListener("mouseleave", () => {
      if (zoomLens) zoomLens.style.display = "none";
    });
  }

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeReportLightbox();
      closeStockNotify();
      closeSearch();
      closeCart();
    }
  });

  document.querySelector(".report-lightbox-close")?.addEventListener("click", closeReportLightbox);
  reportLightbox?.addEventListener("click", (event) => {
    if (event.target === reportLightbox) closeReportLightbox();
  });

  document.querySelector(".newsletter")?.addEventListener("submit", (event) => {
    event.preventDefault();
    showToast("Thanks for joining our newsletter.");
    event.currentTarget.reset();
  });

  stockNotifyModal.querySelector("[data-stock-notify-form]")?.addEventListener("submit", (event) => {
    event.preventDefault();
    submitStockNotify(event.currentTarget);
  });

  document.querySelectorAll("[data-uk-postcode]").forEach((input) => {
    input.addEventListener("input", () => {
      let value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, "").slice(0, 7);
      if (value.length > 3) value = `${value.slice(0, -3)} ${value.slice(-3)}`;
      input.value = value;
    });

    input.addEventListener("change", async () => {
      const postcode = input.value.trim();
      if (!postcode) return;
      try {
        const response = await fetch(`${route("postcodeLookup", "/checkout/postcode")}?postcode=${encodeURIComponent(postcode)}`, {
          headers: { Accept: "application/json" },
        });
        const data = await response.json();
        if (response.ok && data.found && Array.isArray(data.addresses) && data.addresses.length > 0) {
          const address = data.addresses[0];
          const cityInput = document.querySelector("#city");
          const countyInput = document.querySelector("#state");
          if (cityInput && !cityInput.value) cityInput.value = address.city || "";
          if (countyInput && !countyInput.value) countyInput.value = address.state || "";
        }
      } catch (e) {
        // silent fail
      }
    });
  });

  const manualAddress = document.querySelector("[data-manual-address]");
  const postcodeStatus = document.querySelector("[data-postcode-status]");
  const postcodeInput = document.querySelector("[data-uk-postcode]");
  const postcodePicker = document.querySelector("[data-postcode-address-picker]");
  const postcodeSelect = document.querySelector("[data-postcode-address]");
  const findPostcodeButton = document.querySelector("[data-find-postcode]");
  const enterManualButton = document.querySelector("[data-enter-manual]");
  const usePostcodeButton = document.querySelector("[data-use-postcode]");
  const checkoutForm = document.querySelector("#checkoutForm");
  const placeOrderButton = document.querySelector("[data-place-order]");
  const streetInput = document.querySelector("#streetAddress");
  const addressTwoInput = document.querySelector("#addressTwo");
  const cityInput = document.querySelector("#city");
  const countyInput = document.querySelector("#state");

  function setAddressFieldsVisible(visible) {
    if (!manualAddress) return;
    manualAddress.classList.toggle("is-visible", visible);
    manualAddress.querySelectorAll("[data-address-required]").forEach((input) => {
      input.toggleAttribute("required", visible);
    });
  }

  function setPostcodePickerVisible(visible) {
    if (!postcodePicker) return;
    postcodePicker.hidden = !visible;
    if (!visible && postcodeSelect) {
      postcodeSelect.innerHTML = '<option value="">Select your address</option>';
    }
  }

  function clearAddressFields() {
    [streetInput, addressTwoInput, cityInput, countyInput].forEach((input) => {
      if (input) input.value = "";
    });
  }

  function setPostcodeMode(mode) {
    const manualMode = mode === "manual";
    if (findPostcodeButton) findPostcodeButton.hidden = manualMode;
    if (enterManualButton) enterManualButton.hidden = manualMode;
    if (usePostcodeButton) usePostcodeButton.hidden = !manualMode;
    if (manualMode) setPostcodePickerVisible(false);
  }

  function fillAddress(address) {
    if (streetInput) streetInput.value = address.address || address.label || "";
    if (addressTwoInput) addressTwoInput.value = address.address_2 || "";
    if (cityInput) cityInput.value = address.city || "";
    if (countyInput) countyInput.value = address.state || "";
  }

  setAddressFieldsVisible(manualAddress?.classList.contains("is-visible"));
  setPostcodeMode(manualAddress?.classList.contains("is-visible") ? "manual" : "search");

  enterManualButton?.addEventListener("click", () => {
    setPostcodeMode("manual");
    setAddressFieldsVisible(true);
    setPostcodePickerVisible(false);
    if (postcodeStatus) postcodeStatus.textContent = "Enter your address manually.";
    streetInput?.focus();
  });

  usePostcodeButton?.addEventListener("click", () => {
    setPostcodeMode("search");
    setAddressFieldsVisible(false);
    setPostcodePickerVisible(false);
    clearAddressFields();
    if (postcodeStatus) postcodeStatus.textContent = "Enter a UK post code and click Find Post Code.";
    postcodeInput?.focus();
  });

  findPostcodeButton?.addEventListener("click", async () => {
    const postcode = postcodeInput?.value.trim();
    setAddressFieldsVisible(false);
    setPostcodePickerVisible(false);
    clearAddressFields();

    if (!postcode) {
      if (postcodeStatus) postcodeStatus.textContent = "Please enter a UK post code first.";
      postcodeInput?.focus();
      return;
    }

    setButtonLoading(findPostcodeButton, true);
    if (postcodeStatus) postcodeStatus.textContent = "Finding post code...";

    try {
      const response = await fetch(`${route("postcodeLookup", "/checkout/postcode")}?postcode=${encodeURIComponent(postcode)}`, {
        headers: { Accept: "application/json" },
      });
      const data = await response.json();

      if (!response.ok || !data.found || !Array.isArray(data.addresses) || data.addresses.length === 0) {
        throw new Error("Post code not found.");
      }

      postcodeSelect.innerHTML = '<option value="">Select your address</option>';
      data.addresses.forEach((address, index) => {
        const option = document.createElement("option");
        option.value = String(index);
        option.textContent = address.label || `Address ${index + 1}`;
        option.dataset.address = JSON.stringify(address);
        postcodeSelect.append(option);
      });

      setPostcodePickerVisible(true);
      if (postcodeStatus) postcodeStatus.textContent = data.source === "addresses" ? "Post code found. Select your address." : "Post code found. Full address list needs an address API key; use Enter Manually for street details.";
      postcodeSelect?.focus();
    } catch (error) {
      setAddressFieldsVisible(false);
      setPostcodePickerVisible(false);
      if (postcodeStatus) postcodeStatus.textContent = "Post code not found. Check the UK post code or use Enter Manually.";
      postcodeInput?.focus();
    } finally {
      setButtonLoading(findPostcodeButton, false);
    }
  });

  checkoutForm?.addEventListener("submit", () => {
    if (!checkoutForm.checkValidity()) {
      setButtonLoading(placeOrderButton, false);
      return;
    }

    setButtonLoading(placeOrderButton, true);
  });

  postcodeSelect?.addEventListener("change", () => {
    const option = postcodeSelect.selectedOptions[0];
    if (!option?.dataset.address) {
      setAddressFieldsVisible(false);
      return;
    }

    const selectedAddress = JSON.parse(option.dataset.address);
    selectedAddress.label = selectedAddress.label || option.textContent.trim();
    fillAddress(selectedAddress);
    setAddressFieldsVisible(false);
    if (postcodeStatus) postcodeStatus.textContent = streetInput?.value ? "Address selected." : "Address selected. Use Enter Manually if you need to add street details.";
  });

  document.querySelectorAll("[data-price-range]").forEach((range) => {
    const minRange = range.querySelector("[data-price-min-range]");
    const maxRange = range.querySelector("[data-price-max-range]");
    const form = range.closest("form");
    const minInput = form?.querySelector("[data-price-min-input]");
    const maxInput = form?.querySelector("[data-price-max-input]");
    const minLabel = form?.querySelector("[data-price-min-label]");
    const maxLabel = form?.querySelector("[data-price-max-label]");
    const sortInput = form?.querySelector("[data-price-sort-field]");
    const fill = range.querySelector("[data-price-range-fill]");

    if (!minRange || !maxRange || !minInput || !maxInput) return;

    const minLimit = Number(minRange.min || 0);
    const maxLimit = Number(maxRange.max || 0);
    const hasInitialMin = minInput.value !== "";
    const hasInitialMax = maxInput.value !== "";

    function clampValues(source, syncInputs = true) {
      let minValue = Number(minRange.value || minLimit);
      let maxValue = Number(maxRange.value || maxLimit);

      if (minValue > maxValue) {
        if (source === "min") maxValue = minValue;
        else minValue = maxValue;
      }

      minRange.value = minValue;
      maxRange.value = maxValue;
      if (syncInputs) {
        minInput.value = minValue;
        maxInput.value = maxValue;
      }
      if (source && sortInput) sortInput.value = "price_asc";
      if (minLabel) minLabel.textContent = minValue;
      if (maxLabel) maxLabel.textContent = maxValue;

      const span = Math.max(1, maxLimit - minLimit);
      const left = ((minValue - minLimit) / span) * 100;
      const right = 100 - ((maxValue - minLimit) / span) * 100;
      if (fill) {
        fill.style.left = `${left}%`;
        fill.style.right = `${right}%`;
      }
    }

    minRange.addEventListener("input", () => clampValues("min"));
    maxRange.addEventListener("input", () => clampValues("max"));
    minInput.addEventListener("input", () => {
      minRange.value = minInput.value || minLimit;
      clampValues("min");
    });
    maxInput.addEventListener("input", () => {
      maxRange.value = maxInput.value || maxLimit;
      clampValues("max");
    });

    clampValues(null, hasInitialMin || hasInitialMax);
  });

  document.querySelectorAll("[data-uk-phone]").forEach((input) => {
    input.addEventListener("input", () => {
      let value = input.value.replace(/[^\d+]/g, "");
      if (value.startsWith("+44")) {
        const digits = value.replace(/\D/g, "").slice(0, 12);
        value = `+44 ${digits.slice(2, 6)} ${digits.slice(6, 9)} ${digits.slice(9, 12)}`.trim();
      } else {
        const digits = value.replace(/\D/g, "").slice(0, 11);
        value = [digits.slice(0, 5), digits.slice(5, 8), digits.slice(8, 11)].filter(Boolean).join(" ");
      }
      input.value = value;
    });
  });

  const tabCards = [...document.querySelectorAll(".tab-content-card")];
  if (tabCards.length) {
    initProductDescriptionSections();

    const tabIds = ["description", "benefits", "dosage", "ingredients", "reviews", "faq"];

    function getTabCard(id) {
      const target = document.getElementById(id);
      return target?.classList.contains("tab-content-card") ? target : target?.closest(".tab-content-card");
    }

    function showProductTab(id, updateHash = true) {
      const nextId = tabIds.includes(id) ? id : "description";
      const nextCard = getTabCard(nextId) || tabCards[0];

      tabCards.forEach((card) => {
        const isActive = card === nextCard;
        card.classList.toggle("is-hidden", !isActive);
        card.querySelectorAll(".product-tabs a").forEach((link) => {
          link.classList.toggle("active", link.getAttribute("href") === `#${nextId}`);
        });
      });

      nextCard.classList.add("is-visible");
      if (updateHash) history.replaceState(null, "", `#${nextId}`);
    }

    document.addEventListener("click", (event) => {
      const tabLink = event.target.closest(".product-tabs a");
      if (!tabLink) return;
      const id = tabLink.getAttribute("href")?.replace("#", "");
      if (!tabIds.includes(id)) return;
      event.preventDefault();
      showProductTab(id);
    });

    showProductTab(location.hash.replace("#", ""), false);
  }

  document.querySelectorAll("[data-peptide-calculator]").forEach((calculator) => {
    const activeChoice = (group) => calculator.querySelector(`[data-peptide-group="${group}"] .active`);
    const groupValue = (group) => {
      const choice = activeChoice(group);
      const custom = calculator.querySelector(`[data-peptide-custom="${group}"]`);
      return choice?.hasAttribute(`data-peptide-other`) ? Number(custom?.value) || 0 : Number(choice?.dataset.value) || 0;
    };
    const setText = (selector, value) => calculator.querySelectorAll(selector).forEach((node) => { node.textContent = value; });

    const syncCustomInput = (group) => {
      const input = calculator.querySelector(`[data-peptide-custom="${group}"]`);
      if (input) input.classList.toggle("active", activeChoice(group)?.hasAttribute("data-peptide-other"));
    };

    const buildScale = (units) => {
      calculator.querySelectorAll("[data-syringe-scale-ticks]").forEach((scale) => {
        const step = units <= 50 ? 5 : 10;
        scale.innerHTML = Array.from({ length: (units / step) + 1 }, (_, index) => {
          const value = index * step;
          return `<span style="left:${(value / units) * 100}%"><em>${value}</em></span>`;
        }).join("");
      });
    };

    const calculate = () => {
      const syringe = activeChoice("syringe");
      const syringeMl = groupValue("syringe");
      const syringeUnits = Number(syringe?.dataset.units) || 100;
      const vialMg = groupValue("vial");
      const waterMl = groupValue("water");
      const doseMcg = groupValue("dose");
      const concentration = waterMl > 0 ? (vialMg * 1000) / waterMl : 0;
      const pullMl = concentration > 0 ? doseMcg / concentration : 0;
      const pullUnits = pullMl * 100;
      const usage = syringeMl > 0 ? (pullMl / syringeMl) * 100 : 0;
      const displayUnits = pullUnits >= 10 ? pullUnits.toFixed(1) : pullUnits.toFixed(2);

      setText("[data-peptide-result]", displayUnits);
      setText("[data-peptide-result-inline]", displayUnits);
      setText("[data-peptide-summary-result]", `${displayUnits} units`);
      setText("[data-peptide-dose-inline]", `${doseMcg || 0} mcg`);
      setText("[data-peptide-units]", `${displayUnits} units`);
      setText("[data-peptide-ml]", `${pullMl.toFixed(3)} ml`);
      setText("[data-peptide-percent]", `${Math.min(usage, 999).toFixed(1)}%`);
      setText("[data-syringe-label]", syringe?.dataset.label || "");
      setText("[data-syringe-capacity]", `${syringeMl} ml / ${syringeUnits} units`);
      setText("[data-syringe-marking]", `${syringeUnits} unit markings`);

      calculator.querySelectorAll("[data-peptide-fill]").forEach((fill) => { fill.style.width = `${Math.min(usage, 100)}%`; });
      calculator.querySelectorAll("[data-syringe-scale-marker]").forEach((marker) => { marker.style.left = `${Math.min(Math.max(usage, 0), 100)}%`; });
      calculator.querySelector("[data-peptide-warning]")?.classList.toggle("active", pullMl > syringeMl);
      const note = calculator.querySelector("[data-peptide-summary-note]");
      if (note) note.textContent = pullMl > syringeMl ? "Selected syringe is not sufficient for this amount." : "Use the selected syringe and pull to the calculated unit line.";
      buildScale(syringeUnits);
    };

    calculator.querySelectorAll(".peptide-choice, .peptide-syringe-option").forEach((choice) => {
      choice.addEventListener("click", () => {
        const group = choice.closest("[data-peptide-group]");
        if (!group) return;
        group.querySelectorAll(".peptide-choice, .peptide-syringe-option").forEach((item) => item.classList.remove("active"));
        choice.classList.add("active");
        syncCustomInput(group.dataset.peptideGroup);
        calculate();
      });
    });
    calculator.querySelectorAll(".peptide-custom-input").forEach((input) => input.addEventListener("input", calculate));
    ["vial", "water", "dose"].forEach(syncCustomInput);
    calculate();
  });

  document.querySelectorAll("[data-hero-feature-slider]").forEach((slider) => {
    const track = slider.querySelector(".hero-features");
    const slides = [...slider.querySelectorAll(".hero-feature-slide")];
    const pagination = slider.querySelector(".hero-feature-pagination");
    const responsiveQuery = window.matchMedia("(max-width: 991.98px)");
    const mobileQuery = window.matchMedia("(max-width: 767.98px)");
    let activePage = 0;
    let autoSlide = null;
    let scrollFrame = null;

    const visibleSlides = () => 2;
    const pageCount = () => Math.ceil(slides.length / visibleSlides());
    const pageWidth = () => track.clientWidth;

    const updateDots = () => {
      pagination?.querySelectorAll("button").forEach((dot, index) => {
        dot.classList.toggle("is-active", index === activePage);
        dot.setAttribute("aria-current", index === activePage ? "true" : "false");
      });
    };

    const goToPage = (page, behavior = "smooth") => {
      const total = pageCount();
      activePage = (page + total) % total;
      track.scrollTo({ left: activePage * pageWidth(), behavior });
      updateDots();
    };

    const stopAutoSlide = () => {
      if (autoSlide) window.clearInterval(autoSlide);
      autoSlide = null;
    };

    const startAutoSlide = () => {
      stopAutoSlide();
      if (!responsiveQuery.matches || pageCount() < 2) return;
      autoSlide = window.setInterval(() => goToPage(activePage + 1), 3800);
    };

    const buildPagination = () => {
      stopAutoSlide();
      activePage = 0;
      track.scrollTo({ left: 0, behavior: "auto" });
      if (!pagination) return;
      pagination.innerHTML = responsiveQuery.matches
        ? Array.from({ length: pageCount() }, (_, index) =>
          `<button type="button" aria-label="Show delivery benefits ${index + 1}"></button>`
        ).join("")
        : "";
      pagination.querySelectorAll("button").forEach((dot, index) => {
        dot.addEventListener("click", () => {
          goToPage(index);
          startAutoSlide();
        });
      });
      updateDots();
      startAutoSlide();
    };

    track.addEventListener("scroll", () => {
      if (!responsiveQuery.matches || scrollFrame) return;
      scrollFrame = window.requestAnimationFrame(() => {
        activePage = Math.max(0, Math.min(pageCount() - 1, Math.round(track.scrollLeft / pageWidth())));
        updateDots();
        scrollFrame = null;
      });
    }, { passive: true });

    slider.addEventListener("pointerenter", stopAutoSlide);
    slider.addEventListener("pointerleave", startAutoSlide);
    slider.addEventListener("touchstart", stopAutoSlide, { passive: true });
    slider.addEventListener("touchend", startAutoSlide, { passive: true });
    responsiveQuery.addEventListener("change", buildPagination);
    mobileQuery.addEventListener("change", buildPagination);
    buildPagination();
  });

  prepareAnimatedHeadings();
  preparePageAnimations();

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add("is-visible");
      if (entry.target.classList.contains("animated-heading")) {
        entry.target.classList.add("heading-ready");
      }
      observer.unobserve(entry.target);
    });
  }, { threshold: 0.14 });

  document.querySelectorAll(".reveal-on-scroll, .reveal-group > *").forEach((item, index) => {
    item.style.transitionDelay = `${Math.min(index % 6, 5) * 70}ms`;
    observer.observe(item);
  });

  document.querySelectorAll(".animated-heading").forEach((heading) => {
    observer.observe(heading);
  });

  renderCart();
  applyStockNotifiedState();
  updateHeaderState();
});
