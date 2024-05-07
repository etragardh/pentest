(() => {
  window.BreakdancePostsList = {
    loadMorePostsInit,
    infiniteScrollInit
  };

  async function loadMorePostsAndAppend({
    selector,
    postId,
    id,
    paged,
    ssrFilePath
  }) {
    const body = new FormData();

    body.append("action", "breakdance_posts_pagination_load_more");
    body.append("postId", postId);
    body.append("elementId", id);
    body.append("paged", `${paged}`);
    body.append("ssrFilePath", ssrFilePath);

    /**
     * @typedef {Object} data
     * @property {string} html
     * @property {number} maxNumberOfPages
     * @property {string[]} inlineScripts
     */

    const { makeAjaxRequest } = window.BreakdanceFrontend.utils;

    /**
     * @type {data|null}
     */
    const response = await makeAjaxRequest(BreakdanceFrontend.data.ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      body
    })
      .then(res => res.json())
      .then(data => data?.data)
      .catch(e => {
        console.error(e);
      });

    if (response?.html) {
      const element = document.querySelector(selector);
      const container = element.querySelector(".ee-posts");

      // Convert string to DOM elements
      const parser = new DOMParser();
      const parsedDocument = parser.parseFromString(response.html, "text/html");

      const newPosts = parsedDocument.querySelectorAll(".ee-post");
      const fragment = document.createDocumentFragment();

      newPosts.forEach((post) => {
        fragment.appendChild(post);
      });

      container.appendChild(fragment);

      // Trigger event as the next page has been successfully loaded
      const event = new CustomEvent("breakdance_infinite_scroll_loaded", {
        detail: newPosts
      });

      element.dispatchEvent(event);
    }

    if (response?.inlineScripts) {
      response.inlineScripts.forEach(scriptText => {
        const scriptEle = document.createElement("script");
        scriptEle.textContent = scriptText;
        document.body.appendChild(scriptEle);
      });
    }

    return response;
  }

  function loadMorePostsInit({ selector, postId, id }) {
    const loadMoreButton = document.querySelector(
      `${selector} .bde-posts-pagination-loadmore-button`
    );

    if (!loadMoreButton) return;

    const ssrFilePath =
      loadMoreButton.closest("[data-ssr-path]")?.dataset.ssrPath || "";

    // load more won't change the URL, so we page it ourselves
    let paged = 1;

    loadMoreButton.addEventListener("click", async function() {
      paged++;

      loadMoreButton.classList.add("is-loading");

      const response = await loadMorePostsAndAppend({
        selector,
        postId,
        id,
        paged,
        ssrFilePath
      });

      if (!response || !response.html || response.maxNumberOfPages <= paged) {
        loadMoreButton.style.display = "none";
        return;
      }

      loadMoreButton.classList.remove("is-loading");
    });
  }

  // https://github.com/you-dont-need/You-Dont-Need-Lodash-Underscore#_throttle
  function throttle(func, timeFrame) {
    let lastTime = 0;
    return function(...args) {
      const now = new Date();
      if (now - lastTime >= timeFrame) {
        func(...args);
        lastTime = now;
      }
    };
  }

  function infiniteScrollInit({ selector, postId, id }) {
    const element = document.querySelector(selector);
    const loadingIndicatorElement = document.querySelector(
      `${selector} .breakdance-form-loader`
    );
    let paged = 2;
    let loading = false;
    const ssrFilePath =
      element.querySelector("[data-ssr-path]")?.dataset?.ssrPath || "";

    async function scrollFunction() {
      const scrollTop =
        window.pageYOffset || document.documentElement.scrollTop;
      const scrollBottom = scrollTop + window.innerHeight;

      const { top, height } = element.getBoundingClientRect();
      // "top" is relative to the viewport, to make it absolute we add scrollTop
      const elementBottom = top + scrollTop + height;

      if (scrollBottom >= elementBottom + 50 && !loading) {
        if (loadingIndicatorElement) {
          loadingIndicatorElement.style.display = "inline";
        }

        loading = true;
        const response = await loadMorePostsAndAppend({
          selector,
          postId,
          id,
          paged,
          ssrFilePath
        });

        loading = false;

        if (loadingIndicatorElement) {
          loadingIndicatorElement.style.display = "none";
        }

        if (!response || !response.html || response.maxNumberOfPages <= paged) {
          document.removeEventListener("scroll", throttledScrollFunc);
        }

        paged++;
      }
    }

    const throttledScrollFunc = throttle(scrollFunction, 150);

    document.addEventListener("scroll", throttledScrollFunc);
  }
})();
