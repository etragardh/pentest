const { mergeObjects } = BreakdanceFrontend.utils;

class BreakdanceInstagramPost {
  defaultOptions = {
    url: "https://www.instagram.com/p/CTVIfBvLRXX/",
    hide_caption: false,
  };
  constructor(selector, options) {
    this.selector = selector;
    this.element = document.querySelector(`${this.selector}`);
    this.wrapper = this.element.querySelector(".js-ee-instagram-post");
    this.options = mergeObjects(this.defaultOptions, options);
    this.init();
  }

  update() {
    this.destroy();
    this.init();
  }

  destroy() {
    this.wrapper.innerHTML = "";
  }

  init() {
    this.wrapper.innerHTML = `<blockquote class="instagram-media" ${
      this.options.hide_caption == true ? "" : "data-instgrm-captioned"
    } data-instgrm-permalink="${
      this.options.url
    }" data-instgrm-version="14"></blockquote>`;
    if (window.instgrm) {
      window.instgrm.Embeds.process();
    }
  }
}

window.BreakdanceInstagramPost = BreakdanceInstagramPost;
