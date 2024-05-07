(function($) {
  let activeProduct = null;
  let redirectUrl = null;

  function unbindModalListeners() {
    const elems = [
      ".bde-woo-quicklook-modal-close",
      ".bde-woo-quicklook-modal-overlay"
    ];

    elems.forEach(el => {
      document.querySelector(el).removeEventListener("click", hideModal);
    });

    removeEventListener("keyup", onEscClick);
  }

  function bindModalListeners() {
    const elems = [
      ".bde-woo-quicklook-modal-close",
      ".bde-woo-quicklook-modal-overlay"
    ];

    elems.forEach(el => {
      document.querySelector(el).addEventListener("click", hideModal);
    });

    document
      .querySelector(".bde-woo-quicklook-modal-prev")
      ?.addEventListener("click", goPrev);

    document
      .querySelector(".bde-woo-quicklook-modal-next")
      ?.addEventListener("click", goNext);

    document
      .querySelector(".bde-woo-quicklook-modal .single_add_to_cart_button")
      ?.addEventListener("click", addToCart);

    addEventListener("keyup", onEscClick);
  }

  function onEscClick(event) {
    if (event.keyCode === 27) {
      hideModal();
    }
  }

  /*
   * Prev Next Arrows
   */

  function maybeEnableArrows() {
    const prevButton = document.querySelector(".bde-woo-quicklook-modal-prev");
    const nextButton = document.querySelector(".bde-woo-quicklook-modal-next");

    if (!prevButton && !nextButton) return;

    if (!hasPrev()) {
      prevButton.classList.add("disabled");
    } else {
      prevButton.classList.remove("disabled");
    }

    if (!hasNext()) {
      nextButton.classList.add("disabled");
    } else {
      nextButton.classList.remove("disabled");
    }
  }

  function hasPrev() {
    return !!activeProduct.previousElementSibling;
  }

  function hasNext() {
    return !!activeProduct.nextElementSibling;
  }

  function goPrev() {
    const product = activeProduct.previousElementSibling;
    if (product) openProduct(product);
  }

  function goNext() {
    const product = activeProduct.nextElementSibling;
    if (product) openProduct(product);
  }

  /*
   * Loader
   */

  function showLoader() {
    $(".bde-woo-quicklook-modal-body").block({
      message: null,
      overlayCSS: {
        background: "#fff",
        opacity: 0.6
      }
    });
  }

  function hideLoader() {
    $.unblockUI();
  }

  /*
   * Modal Utils
   */

  function triggerWooFeatures(el) {
    // jQuery :(
    const forms = el.querySelectorAll(".variations_form");
    const galleries = el.querySelectorAll(".woocommerce-product-gallery");

    forms.forEach(form => $(form).wc_variation_form());
    galleries.forEach(gallery => $(gallery).wc_product_gallery());
  }

  function showModal(html) {
    const content = document.querySelector(".bde-woo-quicklook-modal-body");
    content.innerHTML = html;

    // Show the modal
    document.body.classList.add("is-bde-quicklook-visible");

    // Enable woo variations and gallery
    triggerWooFeatures(content);

    // Add close events
    bindModalListeners();
  }

  function hideModal() {
    document.body.classList.remove("is-bde-quicklook-visible");
    unbindModalListeners();
  }

  /*
   * Add to Cart
   */

  function onBeforeAddToCart(button, formData) {
    const $thisbutton = $(button);
    const data = Object.fromEntries(formData.entries());

    // Trigger event.
    $(document.body).trigger("adding_to_cart", [$thisbutton, data]);

    button.classList.remove("added");
    button.classList.add("loading");
  }

  function onAddedToCart(button, response) {
    const $thisbutton = $(button);

    if (!response) return;

    if (response.error && response.product_url) {
      window.location = response.product_url;
      return;
    }

    if (redirectUrl) {
      window.location = redirectUrl;
      return;
    }

    // Trigger event so themes can refresh other areas.
    $(document.body).trigger("added_to_cart", [
      response.fragments,
      response.cart_hash,
      $thisbutton
    ]);
  }

  async function addToCart(event) {
    const button = event.currentTarget;
    const isGroupedProduct = !!button.closest(".product-type-grouped");
    const isExternalProduct = !!button.closest(".product-type-external");

    if (isExternalProduct || isGroupedProduct) return;
    event.preventDefault();

    const ajaxUrl = window.BreakdanceFrontend.data.ajaxUrl;
    const form = button.closest("form");
    const formData = new FormData(form);

    formData.append("action", "breakdance_add_to_cart");
    formData.append("product_id", button.value || formData.get("variation_id"));

    onBeforeAddToCart(button, formData);

    const { makeAjaxRequest } = window.BreakdanceFrontend.utils;
    const request = await makeAjaxRequest(ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      body: formData
    });

    const response = await request.json();

    button.classList.add("added");
    button.classList.remove("loading");
    hideModal();

    onAddedToCart(button, response);
  }

  async function fetchProduct(productId) {
    const ajaxUrl = window.BreakdanceFrontend.data.ajaxUrl;
    const formData = new FormData();
    formData.append("action", "breakdance_quicklook");
    formData.append("productId", productId);

    const { makeAjaxRequest } = window.BreakdanceFrontend.utils;
    const request = await makeAjaxRequest(ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      body: formData
    });

    const response = await request.json();

    return response.html;
  }

  /*
   * Fetch Product
   */

  /**
   * @param {Element} product
   */
  async function openProduct(product) {
    const button = product.querySelector(".bde-woo-product-quicklook-button");
    const id = button.dataset.id;

    button.classList.add("loading");
    showLoader();

    const html = await fetchProduct(id);

    button.classList.remove("loading");
    hideLoader();

    redirectUrl = button.dataset.redirectUrl;
    activeProduct = product;

    maybeEnableArrows();
    showModal(html);
  }

  async function onQuicklookButtonClick(event) {
    event.preventDefault();
    const product = event.currentTarget.closest(".product");
    openProduct(product);
  }

  function mounted() {
    $(document).on(
      "click",
      ".bde-woo-product-quicklook-button",
      onQuicklookButtonClick
    );
  }

  document.addEventListener("DOMContentLoaded", mounted);
})(jQuery);
