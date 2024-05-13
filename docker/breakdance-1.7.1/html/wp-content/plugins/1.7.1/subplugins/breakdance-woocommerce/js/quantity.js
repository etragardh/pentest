(function ($) {
  const debounce = function (callback, wait, immediate) {
    let timeout;
    return function () {
      const context = this,
        args = arguments;
      const later = function () {
        timeout = null;
        if (!immediate) callback.apply(context, args);
      };
      const callNow = immediate && !timeout;
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
      if (callNow) callback.apply(context, args);
    };
  };

  const updateCartItem = async (input) => {
    const url = window.BreakdanceFrontend.data.ajaxUrl;
    const hash = input.getAttribute("name").replace(/cart\[(\w+)]\[qty]/g, "$1");
    const newQty = parseFloat(input.value);

    const data = new FormData();
    data.append("action", "breakdance_cart_update_qty");
    data.append("hash", hash);
    data.append("quantity", newQty);

    const payload = {
      method: "POST",
      credentials: "same-origin",
      body: data
    };

    const { makeAjaxRequest } = window.BreakdanceFrontend.utils;
    await makeAjaxRequest(url, payload);

    $(document.body).trigger("wc_fragment_refresh");
    $(document.body).trigger("wc_update_cart");
  };

  const delayedUpdateCartItem = debounce((input) => {
    updateCartItem(input);
    blockUI(input);
  }, 300);

  const afterInputChange = (input) => {
    // Works for Mini Cart, Single Product Page, Shop and Cart Page.
    if (isLoopProduct(input)) {
      getAddToCartButton(input)
        ?.setAttribute("data-quantity", input.value);
    } else if (isSingleProduct(input)) {
      // Do nothing. WooCommerce automatically picks up the input.
    } else {
      delayedUpdateCartItem(input);
    }
  };

  const isIncrement = (target) => {
    return target.matches(".bde-quantity-button--inc");
  };

  const isSingleProduct = (input) => {
    return !!input.closest("form.cart");
  };

  const isLoopProduct = (input) => {
    return !!input.closest("li.product");
  };

  const blockUI = (input) => {
    const row = input.closest(".woocommerce-mini-cart-item");

    $(row).block({
      message: null,
      overlayCSS: {
        opacity: 0.6
      }
    });
  };

  const getAddToCartButton = (input) => {
    return input.closest("li.product").querySelector(".add_to_cart_button");
  };

  const getQuantityInput = (event) => {
    return event.target.parentElement.querySelector(".qty");
  };

  const onMaybeButtonClick = (event) => {
    const isButton = event.target.matches(".bde-quantity-button");

    if (!isButton) return;

    event.preventDefault();

    const input = getQuantityInput(event);
    const action = isIncrement(event.target) ? "stepUp" : "stepDown";

    input[action](); // Increment or decrement input

    afterInputChange(input);
  };

  const onMaybeInputChange = (event) => {
    const isInput = event.target.matches("input.qty");

    if (!isInput) return;
    afterInputChange(event.target);
  }

  const maybeReloadCart = () => {
    const containsEl = document.querySelector(".bde-cart-contents");
    if (containsEl) location.reload();
  }

  addEventListener("DOMContentLoaded", () => {
    addEventListener("change", onMaybeInputChange);
    addEventListener("click", onMaybeButtonClick);
    $(document.body).on("wc_cart_emptied", maybeReloadCart);
  });
}(jQuery));
