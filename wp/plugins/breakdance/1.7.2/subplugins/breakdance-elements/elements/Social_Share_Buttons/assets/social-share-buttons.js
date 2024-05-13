(function () {
  class BreakdanceSocialShareButtons {
    constructor(selector) {
      this.selector = selector;
      this.buttons = document.querySelectorAll(
        `${this.selector} .js-breakdance-share-button`
      );
      this.buttonMobile = document.querySelector(
        `${this.selector} .js-breakdance-share-mobile`
      );
      this.init();
    }

    // Methods
    destroy() {
      if (!this.buttons) return;
      this.detachClickEvents();
      this.buttons = null;
    }

    update() {
      this.destroy();
      this.init();
    }

    detachClickEvents() {
      if (this.buttons) {
        this.buttons.forEach((socialButton) => {
          socialButton.onclick = "";
          socialButton.classList.remove("is-visible");
        });
        this.buttonMobile.onclick = "";
      }
    }

    attachClickEvents() {
      if (this.buttons) {
        this.buttonMobile.onclick = () => {
          this.buttons.forEach((socialButton) =>
            socialButton.classList.toggle("is-visible")
          );
        };
        this.buttons.forEach((socialButton) => {
          socialButton.onclick = function () {
            let url = this.dataset.url
              ? this.dataset.url
              : window.location.href;
            let title = this.dataset.text ? this.dataset.text : document.title;
            let text = title;
            let image = "";

            const SOCIAL_SHARE_URLS = {
              twitter: `https://twitter.com/intent/tweet?text=${text}\x20${url}`,
              pinterest: `https://www.pinterest.com/pin/create/button/?url=${url}&media=${image}`,
              facebook: `https://www.facebook.com/sharer.php?u=${url}`,
              vk: `https://vkontakte.ru/share.php?url=${url}&title=${title}&description=${text}&image=${image}`,
              linkedin: `https://www.linkedin.com/shareArticle?mini=true&url=${url}&title=${title}&summary=${text}&source=${url}`,
              tumblr: `https://tumblr.com/share/link?url=${url}`,
              digg: `https://digg.com/submit?url=${url}`,
              reddit: `https://reddit.com/submit?url=${url}&title=${title}`,
              stumbleupon: `https://www.stumbleupon.com/submit?url=${url}`,
              pocket: `https://getpocket.com/edit?url=${url}`,
              whatsapp: `https://api.whatsapp.com/send?text=${text} ${url}`,
              xing: `https://www.xing.com/app/user?op=share&url=${url}`,
              print: `print`,
              email: `mailto:?subject=${title}&body=${text} ${url}`,
              telegram: `https://telegram.me/share/url?url=${url}&text=${text}`,
              skype: `https://web.skype.com/share?url=${url}`,
            };

            let share_url =
              SOCIAL_SHARE_URLS[this.dataset.network.toLowerCase()];
            if (share_url == "print") {
              window.print();
            } else if (share_url) {
              window.open(share_url, "popup", "width=600,height=480");
            }
          };
        });
      }
    }

    init() {
      this.attachClickEvents();
    }
  }

  window.BreakdanceSocialShareButtons = BreakdanceSocialShareButtons;
})();
