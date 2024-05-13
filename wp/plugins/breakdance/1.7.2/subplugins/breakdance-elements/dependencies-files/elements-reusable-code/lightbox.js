/* global lightGallery, lgAutoplay, lgFullscreen, lgZoom, lgVideo, lgThumbnail */
(function () {
  const { mergeObjects } = BreakdanceFrontend.utils;

  class BreakdanceLightbox {
    defaultOptions = {
      type: "default",
      items: [],
      itemSelector: ".ee-gallery-item",
      autoplay: false,
      speed: { number: 3000 },
      autoplay_videos: false,
      thumbnails: false,
      animated_thumbnails: false,
      watchAttrs: false,
    };

    constructor(element, options) {
      this.itemize = this.itemize.bind(this);

      this.options = mergeObjects(this.defaultOptions, options);
      this.selector = element;
      this.element = this.queryElement(element);

      this.init();
    }

    createGallery(localOptions) {
      const options = this.getLibOptions();

      this.lightbox = lightGallery(this.element, {
        ...options,
        ...localOptions,
      });

      this.detectDuplicateSwiperSlides();
      this.addThumbnailsToVideos();
    }

    queryElement(element) {
      if (typeof element == "string") {
        return document.querySelector(element);
      }

      return element;
    }

    getNamespace() {
      if (this.element.dataset.lightboxId) {
        return this.element.dataset.lightboxId;
      }

      if (this.selector) {
        const tokens = this.selector
          .split("-")
          .filter((t) => !isNaN(Number(t)))
          .slice(0, 2); // Post ID and Node ID only.

        return tokens.join('-');
      }

      return "default";
    }

    getLibOptions() {
      const settings = this.options;
      const namespace = this.getNamespace();
      const className = `bde-lightbox bde-lightbox-${namespace}`;

      return {
        plugins: [lgAutoplay, lgFullscreen, lgZoom, lgVideo, lgThumbnail],
        licenseKey: "9E9C183D-14564BA5-B09B8BE0-F8B54A20",
        addClass: className,
        autoplay: settings.autoplay,
        slideShowAutoplay: settings.autoplay,
        slideShowInterval: settings.speed.number,
        autoplayVideoOnSlide: settings.autoplay_videos, // TODO: This option is broken in the library
        thumbnail: settings.thumbnails,
        animateThumb: settings.animated_thumbnails,
        zoomFromOrigin: settings.animated_thumbnails,
        zoom: true,
        download: false,
        mobileSettings: {
          controls: true,
          showCloseIcon: true,
        }
      };
    }

    getExtension(url) {
      return url.split(".").pop();
    }

    isVideo(url) {
      const types = ["mp4", "mov", "wmv", "avi", "mpg", "ogg", "3gp", "3g2"];
      const extension = this.getExtension(url);
      return types.includes(extension);
    }

    getVideoObject(url, caption, mime) {
      if (!this.isVideo(url)) {
        return {
          src: url,
          subHtml: caption,
        };
      }

      if (!mime) {
        const extension = this.getExtension(url);
        mime = `video/${extension}`;
      }

      return {
        video: {
          source: [
            {
              src: url,
              type: mime,
            },
          ],
          attributes: {
            preload: false,
            controls: true,
          },
        },
        subHtml: caption,
        subHtmlUrl: "",
      };
    }

    filterItems(item) {
      const isImg = item.type === "image" && item.image;
      const isVideo = item.type === "video" && item.video;
      return isImg || isVideo;
    }

    itemize(item) {
      // HTML5 videos or oEmbeds.
      if (item.type === "video") {
        return this.getVideoObject(item.video.url, item.caption);
      }

      // Handle HTML5 videos in WP Media Input
      if (item.image && item.image.type === "video") {
        return this.getVideoObject(
          item.image.url,
          item.caption,
          item.image.mime
        );
      }

      return {
        src: item.image.url,
        type: item.image.type,
        thumb: item.image.sizes.thumbnail.url,
        subHtml: item.caption,
      };
    }

    getItems() {
      return this.options.items.filter(this.filterItems).map(this.itemize);
    }

    detectDuplicateSwiperSlides() {
      this.element.addEventListener("click", (event) => {
        const isDuplicateSlide = event.target.closest(
          ".swiper-slide-duplicate"
        );

        if (!isDuplicateSlide) return;

        event.preventDefault();
        const index = isDuplicateSlide.getAttribute("data-swiper-slide-index");
        this.lightbox.openGallery(index);
      });
    }

    addThumbnailsToVideos() {
      const videoPlaceholder =
        "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMAAAACYCAAAAACXarlBAAAACXBIWXMAAC4jAAAuIwF4pT92AAAB6UlEQVR42u3ZWU+DQBSGYf//7zKaAOew2FLQqomaaF1aXFqXaMfi2qJWLwQ58f2uJnM1T84Ms7DijGcFAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP4p4DjfrCbPr5zbyy5NAE784EN8r3AuW5WRBcCWF39IFJ471/dVhxYA/peAWGXYfkB/CSCpR9AcIK6nBk0CZoKRbUBcw0puFlDDLGoY8PsruWFAWYNzi4B8TV6z1plaA1w4t999Oxv1di0CrJxGPwPEwYFxQBgNJnMZX1sDxJHoXLx0ag2wGMkAAAAAAMBygM5/+EWftzNRsQKQ9KQYvaYYbJQCDQ+LvtgAhJ2bhc6rKJyNv7wNb4kJQLBX6c1FtCgbA88GYL8K8ORp/GYA1Qp0vZfXlCObgGnn1NkGTJxtwHsA/NEUejAOuN+5MT6FkuTWFqC6kWXr6b3pCuTip081OLYB0Mq7yW2isd+7m7V2AxvHad0ezz1kXWTlGTToFpMDtXKhWXjIUn35OaMScaUEAABAvYDNnwB6LQbseN8DgtS1FzBJfP0momctBri74XC0PGdj12ZA8wEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAE3lEeS3kwDalXS7AAAAAElFTkSuQmCC";

      const lgItems = this.lightbox.galleryItems;

      if (!lgItems) return;

      const items = lgItems.map((item) => {
        if (item.video) {
          return {
            ...item,
            thumb: item.thumb || videoPlaceholder,
            src: null, // lightGallery goes crazy if the src key is present for videos.
          };
        }

        return item;
      });

      this.lightbox.refresh(items);
    }

    update(options) {
      this.options = mergeObjects(this.defaultOptions, options);
      this.destroy();
      this.init();
    }

    destroy() {
      if (this.lightbox) {
        this.lightbox.destroy();
        this.lightbox = null;
      }
    }

    initDynamic() {
      const items = this.getItems();

      this.element.addEventListener("click", () => {
        this.createGallery({
          dynamic: true,
          dynamicEl: items,
        });

        // We wrap the method in a setTimeout, so that clicking the element
        // won't fire a resize event before the gallery is ready.
        //
        // By clicking the element, the property panel opens up thus decreasing
        // the iframe's width and triggering a resize event.
        setTimeout(() => this.lightbox.openGallery(), 5);
      });
    }

    getItemFromAnchor(el) {
      const src = el.href;
      return this.isVideo(src) ? this.getVideoObject(src) : { src };
    }

    initFromDOM() {
      const onClick = (event) => {
        const isLinkALightbox = event.currentTarget.dataset.type === "lightbox";

        if (this.options.watchAttrs && !isLinkALightbox) return;

        const item = this.getItemFromAnchor(this.element);

        if (this.lightbox) {
          this.lightbox.refresh([item]);
        } else {
          this.createGallery({
            dynamic: true,
            dynamicEl: [item]
          });
        }

        this.lightbox.openGallery();
      };

      this.element.addEventListener("click", onClick);
    }

    initDefault() {
      this.createGallery({
        selector: this.options.itemSelector,
      });
    }

    init() {
      const { type } = this.options;

      if (type === "dynamic") {
        // Lightbox Gallery Element
        // Show multiple images from an array
        this.initDynamic();
      } else if (type === "single") {
        // Text Link & Image Elements
        // Show an image from a href attribute
        this.initFromDOM();
      } else {
        // Gallery Element
        // Show multiple images from DOM elements with the same css class
        this.initDefault();
      }

      this.element.addEventListener("click", (event) => event.preventDefault());
      this.element.classList.add("is-lightbox-active");
    }

    static autoload(parent = null) {
      const element = parent ?? document;
      // Autoload lightboxes from macros
      const selector =
        ".breakdance-link[data-type='lightbox']:not(.is-lightbox-active)";
      const links = element.querySelectorAll(selector);

      links.forEach((link) => {
        new this(link, {
          type: "single",
          watchAttrs: true,
        });
      });
    }
  }

  BreakdanceLightbox.autoload();
  window.BreakdanceLightbox = BreakdanceLightbox;
})();
