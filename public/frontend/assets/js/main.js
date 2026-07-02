document.addEventListener("DOMContentLoaded", () => {
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
  const cartCount = document.querySelector(".cart-count");
  const cartSubtotal = document.querySelector(".cart-subtotal");
  const productTrack = document.querySelector(".product-track");
  const reportLightbox = document.querySelector(".report-lightbox");
  const reportLightboxImage = document.querySelector("[data-report-lightbox-image]");
  const reportLightboxTitle = document.querySelector("[data-report-lightbox-title]");

  const money = (value) => `£${Number(value || 0).toFixed(2)}`;
  const findProduct = (id) => products.find((product) => product.id === id);
  const cartQty = () => [...cart.values()].reduce((total, qty) => total + qty, 0);
  const csrfToken = document.querySelector("meta[name='csrf-token']")?.content || "";

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

  function endpoint(base, id) {
    return `${base}/${id}`;
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

    if (!response.ok) throw new Error("Cart request failed.");

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
    if (!id) return;
    const safeQty = Math.max(1, Number(quantity) || 1);
    try {
      const data = await cartRequest(endpoint(route("cartAddBase", "/cart/add"), id), "POST", { quantity: safeQty });
      await renderCart(data);
      cartOverlay.classList.add("is-open");
      cartOverlay.setAttribute("aria-hidden", "false");
      setPanelState(cartDrawer, true);
      showToast("Product added to your cart.");
    } catch (error) {
      showToast("Unable to update cart right now.");
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

    cartCount.textContent = data.item_count;
    cartSubtotal.textContent = money(data.subtotal);
    cartEmpty.classList.toggle("is-visible", data.is_empty);
  }

  function renderSearchResults(query) {
    const normalized = query.trim().toLowerCase();
    const matches = products.filter((product) => `${product.name} ${product.meta}`.toLowerCase().includes(normalized));

    searchResults.innerHTML = matches.length ? matches.map((product) => `
      <article class="search-result">
        <img src="${product.image}" alt="${product.name}">
        <div class="me-auto">
          <strong>${product.name}</strong>
          <small>${product.meta} - ${money(product.price)}</small>
        </div>
        <button type="button" data-search-add="${product.id}" aria-label="Add ${product.name}"><i class="fa-solid fa-cart-plus"></i></button>
      </article>`).join("") : '<p class="no-results">No products found. Try searching "Peptides" or "HGH".</p>';
  }

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

  document.querySelector(".search-toggle").addEventListener("click", openSearch);
  document.querySelector(".search-close").addEventListener("click", closeSearch);
  searchPanel.addEventListener("click", (event) => {
    if (event.target === searchPanel) closeSearch();
  });
  searchInput.addEventListener("input", (event) => renderSearchResults(event.target.value));
  searchForm.addEventListener("submit", (event) => {
    event.preventDefault();
    const query = searchInput.value.trim();
    goToPage(`${route("search", "/search")}${query ? `?q=${encodeURIComponent(query)}` : ""}`);
  });

  document.querySelector(".cart-btn").addEventListener("click", openCart);
  document.querySelector(".cart-close").addEventListener("click", closeCart);
  cartOverlay.addEventListener("click", closeCart);
  document.querySelector(".slider-prev")?.addEventListener("click", () => productTrack.scrollBy({ left: -260, behavior: "smooth" }));
  document.querySelector(".slider-next")?.addEventListener("click", () => productTrack.scrollBy({ left: 260, behavior: "smooth" }));

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
    const labReport = event.target.closest("[data-lab-report]");
    const detailTapCard = event.target.closest(".product-benefit-strip > div, .description-assurance > div, .benefit-list > div, .dosage-steps > div, .ingredient-certifications > div, .review-card, .shop-trust-grid > div");
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
      goToPage(route("productDetails", "/product-details"));
    }

    if (relatedCard && event.target.closest("button")) {
      await addToCart(relatedCard.dataset.productId);
    } else if (relatedCard) {
      goToPage(route("productDetails", "/product-details"));
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

    if (productZoom && !event.target.closest("[data-product-image]")) {
      const isZoomed = productZoom.classList.toggle("is-zoomed");
      productZoom.setAttribute("aria-pressed", String(isZoomed));
    }

    if (addFromSearch) await addToCart(addFromSearch.dataset.searchAdd);
    if (inc) updateQty(inc.dataset.cartInc, 1, inc.dataset.cartQty || inc.parentElement?.querySelector("span")?.textContent);
    if (dec) updateQty(dec.dataset.cartDec, -1, dec.dataset.cartQty || dec.parentElement?.querySelector("span")?.textContent);
    if (remove) removeFromCart(remove.dataset.cartRemove);
    if (clearCart) clearServerCart();
  });

  document.querySelector("[data-product-zoom]")?.addEventListener("keydown", (event) => {
    if (event.key !== "Enter" && event.key !== " ") return;
    event.preventDefault();
    event.currentTarget.click();
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeReportLightbox();
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

  document.querySelectorAll("[data-uk-postcode]").forEach((input) => {
    input.addEventListener("input", () => {
      let value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, "").slice(0, 7);
      if (value.length > 3) value = `${value.slice(0, -3)} ${value.slice(-3)}`;
      input.value = value;
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
    }
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
    const fill = range.querySelector("[data-price-range-fill]");

    if (!minRange || !maxRange || !minInput || !maxInput) return;

    const minLimit = Number(minRange.min || 0);
    const maxLimit = Number(maxRange.max || 0);

    function clampValues(source) {
      let minValue = Number(minRange.value || minLimit);
      let maxValue = Number(maxRange.value || maxLimit);

      if (minValue > maxValue) {
        if (source === "min") maxValue = minValue;
        else minValue = maxValue;
      }

      minRange.value = minValue;
      maxRange.value = maxValue;
      minInput.value = minValue;
      maxInput.value = maxValue;
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

    clampValues();
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
  updateHeaderState();
});
