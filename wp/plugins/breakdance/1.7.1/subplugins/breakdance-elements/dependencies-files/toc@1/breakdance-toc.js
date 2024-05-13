(function () {
  const { mergeObjects } = BreakdanceFrontend.utils;

  function slugify(text) {
    return text
      .toString()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .toLowerCase()
      .trim()
      .replace(/\s+/g, "-")
      .replace(/[^\w-]+/g, "")
      .replace(/--+/g, "-");
  }

  class BreakdanceTOC {
    options = {
      content: {
        table: {
          advanced: {
            container_selector: "body.breakdance",
          },
        },
      },
      design: {
        collapse: false,
        style: "none",
        scroll_offset: {
          number: 10,
        },
      },
      tocSelector: ".js-breakdance-toc",
    };

    constructor(selector, options) {
      this.selector = selector;
      this.element = document.querySelector(`${this.selector}`);
      if (!this.element) return;
      this.tocSelector = this.element.querySelector(
        `${this.options.tocSelector}`
      );
      this.options = mergeObjects(this.options, options || {});
      this.headingSelectors = this.generateSelectors();

      this.init();
    }

    generateSelectors() {
      const selectors = this.options.content.table.included_headings;
      if (selectors) {
        const keys = Object.keys(selectors)
          .filter((k) => selectors[k] === true)
          .toString();
        return keys;
      } else {
        return "h2,h3,h4";
      }
    }

    getHeadings() {
      const containerSelector =
        this.options.content.table.advanced.container_selector;
      if (containerSelector.length <= 1) return;
      const container = document.querySelector(containerSelector);

      if (!container) return;

      return [].slice.call(container.querySelectorAll(this.headingSelectors));
    }

    setHeadingIds() {
      const headings = this.getHeadings();

      headings.forEach((heading, index) => {
        const slug = slugify(heading.textContent);
        let ref = `${slug}-${index}`;

        if (!heading.id) {
          heading.setAttribute("id", ref);
        }
      });
    }

    maybeInitAccordion() {
      this.accordion = this.element.querySelector(".js-toc-accordion");
      if (!this.accordion) return;
      this.accordionClickHandler = this.accordionClick.bind(this);
      this.accordion.classList.remove("is-on");
      this.accordion.addEventListener("click", this.accordionClickHandler);
    }

    accordionClick(event) {
      event.preventDefault();
      this.accordion.classList.toggle("is-on");
    }

    destroyAccordionClick() {
      this.accordion.removeEventListener("click", this.accordionClickHandler);
    }

    generateJsonLd() {
      const links = [].slice
        .call(this.element.querySelectorAll(".toc-link"))
        .map((link) => {
          return {
            "@context": "https://schema.org",
            "@type": "SiteNavigationElement",
            "@id": "#bde-toc",
            name: link.textContent,
            url: `${link.href}`,
          };
        });

      const script = document.createElement("script");
      script.id = "bde-toc-json-ld";

      script.type = "application/ld+json";
      script.innerHTML = JSON.stringify({
        "@context": "https://schema.org",
        "@graph": [links],
      });

      document.head.appendChild(script);
    }

    initTocbot() {
      this.tocbotInstance = tocbot;
      const collapseDepth = this.options.design.collapse == true ? 0 : 6;

      this.tocbotInstance.init({
        // Where to render the table of contents.
        tocSelector: this.options.tocSelector,
        // Where to grab the headings to build the table of contents.
        contentSelector: this.options.content.table.advanced.container_selector,
        // Which headings to grab inside of the contentSelector element.
        headingSelector: this.headingSelectors,
        // Headings that match the ignoreSelector will be skipped.
        ignoreSelector:
          this.options.content.table.advanced.ignore_selector || ".toc-ignore",
        // How many heading levels should not be collapsed.
        // For example, number 6 will show everything since
        // there are only 6 heading levels and number 0 will collapse them all.
        // The sections that are hidden will open
        // and close as you scroll to headings within them.
        collapseDepth: collapseDepth,
        // Smooth scroll offset.
        scrollSmoothOffset: this.options.design.scroll_offset.number * -1,
        hasInnerContainers: true,
      });
      this.tocbotInstance.refresh();
    }

    init() {
      this.setHeadingIds();
      this.maybeInitAccordion();
      this.initTocbot();
      this.generateJsonLd();
    }

    update() {
      if (!this.tocbotInstance) return;
      this.tocbotInstance.refresh();
    }

    destroy() {
      if (!this.tocbotInstance) return;
      this.tocbotInstance.destroy();
      this.tocbotInstance = null;

      document.querySelector("#bde-toc-json-ld")?.remove();
    }
  }

  window.BreakdanceTOC = BreakdanceTOC;
})();
