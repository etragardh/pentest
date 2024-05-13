/**
 * Copyright 2019 Paul Irish
 * https://github.com/paulirish/lite-youtube-embed
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * A lightweight youtube embed. Still should feel the same to the user, just MUCH faster to initialize and paint.
 *
 * Thx to these as the inspiration
 *   https://storage.googleapis.com/amp-vs-non-amp/youtube-lazy.html
 *   https://autoplay-youtube-player.glitch.me/
 *
 * Once built it, I also found these:
 *   https://github.com/ampproject/amphtml/blob/master/extensions/amp-youtube (👍👍)
 *   https://github.com/Daugilas/lazyYT
 *   https://github.com/vb/lazyframe
 */

/**
 * Beware this code is modified from source to add:
 *
 * - A "logo" by using a "--logoUrl" css property in the inline styles
 * - Added a "title" attribute, inspired by https://github.com/paulirish/lite-youtube-embed/pull/88
 */

class LiteYTEmbed extends HTMLElement {
  connectedCallback() {
    this.videoId = this.getAttribute("videoid");

    let playBtnEl = this.querySelector(".lty-playbtn");
    // A label for the button takes priority over a [playlabel] attribute on the custom-element
    this.playLabel =
      (playBtnEl && playBtnEl.textContent.trim()) ||
      this.getAttribute("playlabel") ||
      "Play";

    /**
     * Lo, the youtube placeholder image!  (aka the thumbnail, poster image, etc)
     *
     * See https://github.com/paulirish/lite-youtube-embed/blob/master/youtube-thumbnail-urls.md
     *
     * TODO: Do the sddefault->hqdefault fallback
     *       - When doing this, apply referrerpolicy (https://github.com/ampproject/amphtml/pull/3940)
     * TODO: Consider using webp if supported, falling back to jpg
     */
    if (!this.style.backgroundImage) {
      this.style.backgroundImage = `url("https://i.ytimg.com/vi/${this.videoId}/hqdefault.jpg")`;
    }

    // Breakdance: in the builder, only add this once, otherwise we'd repeat the element on drag/move
    if (!this.topWrapper) {
      this.topWrapper = document.createElement("div");
      this.topWrapper.classList.add("lyt-top-wrapper");

      // Breakdance: initialize even if they don't have value. Otherwise, we lose Builder reactivity
      const logoEl = document.createElement("div");
      logoEl.classList.add("lyt-logo");
      this.topWrapper.append(logoEl);

      this.title = this.getAttribute("title") || "";
      this.titleEl = document.createElement("div");
      this.titleEl.classList.add("lyt-title-text");
      this.titleEl.textContent = this.title;
      this.topWrapper.append(this.titleEl);

      this.append(this.topWrapper);
    }

    // Set up play button, and its visually hidden label
    if (!playBtnEl) {
      playBtnEl = document.createElement("button");
      playBtnEl.type = "button";
      playBtnEl.classList.add("lty-playbtn");
      this.append(playBtnEl);
    }
    if (!playBtnEl.textContent) {
      const playBtnLabelEl = document.createElement("span");
      playBtnLabelEl.className = "lyt-visually-hidden";
      playBtnLabelEl.textContent = this.playLabel;
      playBtnEl.append(playBtnLabelEl);
    }
    playBtnEl.removeAttribute("href");

    // On hover (or tap), warm up the TCP connections we're (likely) about to use.
    this.addEventListener("pointerover", LiteYTEmbed.warmConnections, {
      once: true
    });

    // Once the user clicks, add the real iframe and drop our play button
    // TODO: In the future we could be like amp-youtube and silently swap in the iframe during idle time
    //   We'd want to only do this for in-viewport or near-viewport ones: https://github.com/ampproject/amphtml/pull/5003
    this.addEventListener("click", this.addIframe);

    // Chrome & Edge desktop have no problem with the basic YouTube Embed with ?autoplay=1
    // However Safari desktop and most/all mobile browsers do not successfully track the user gesture of clicking through the creation/loading of the iframe,
    // so they don't autoplay automatically. Instead we must load an additional 2 sequential JS files (1KB + 165KB) (un-br) for the YT Player API
    // TODO: Try loading the the YT API in parallel with our iframe and then attaching/playing it. #82
    this.needsYTApiForAutoplay =
      navigator.vendor.includes("Apple") ||
      navigator.userAgent.includes("Mobi");
  }

  static get observedAttributes() {
    return ["title"];
  }

  attributeChangedCallback(name, oldValue, newValue) {
    // update when "title" attribute changes value
    if (name === "title" && oldValue !== newValue && this.titleEl) {
      this.titleEl.textContent = newValue;
    }
  }

  /**
   * Add a <link rel={preload | preconnect} ...> to the head
   */
  static addPrefetch(kind, url, as) {
    const linkEl = document.createElement("link");
    linkEl.rel = kind;
    linkEl.href = url;
    if (as) {
      linkEl.as = as;
    }
    document.head.append(linkEl);
  }

  /**
   * Begin pre-connecting to warm up the iframe load
   * Since the embed's network requests load within its iframe,
   *   preload/prefetch'ing them outside the iframe will only cause double-downloads.
   * So, the best we can do is warm up a few connections to origins that are in the critical path.
   *
   * Maybe `<link rel=preload as=document>` would work, but it's unsupported: http://crbug.com/593267
   * But TBH, I don't think it'll happen soon with Site Isolation and split caches adding serious complexity.
   */
  static warmConnections() {
    if (LiteYTEmbed.preconnected) return;

    // The iframe document and most of its subresources come right off youtube.com
    LiteYTEmbed.addPrefetch("preconnect", "https://www.youtube-nocookie.com");
    // The botguard script is fetched off from google.com
    LiteYTEmbed.addPrefetch("preconnect", "https://www.google.com");

    // Not certain if these ad related domains are in the critical path. Could verify with domain-specific throttling.
    LiteYTEmbed.addPrefetch(
      "preconnect",
      "https://googleads.g.doubleclick.net"
    );
    LiteYTEmbed.addPrefetch("preconnect", "https://static.doubleclick.net");

    LiteYTEmbed.preconnected = true;
  }

  fetchYTPlayerApi() {
    if (window.YT || (window.YT && window.YT.Player)) return;

    this.ytApiPromise = new Promise((res, rej) => {
      var el = document.createElement("script");
      el.src = "https://www.youtube.com/iframe_api";
      el.async = true;
      el.onload = _ => {
        YT.ready(res);
      };
      el.onerror = rej;
      this.append(el);
    });
  }

  async addYTPlayerIframe(params) {
    this.fetchYTPlayerApi();
    await this.ytApiPromise;

    const videoPlaceholderEl = document.createElement("div");
    this.append(videoPlaceholderEl);

    const paramsObj = Object.fromEntries(params.entries());

    new YT.Player(videoPlaceholderEl, {
      width: "100%",
      videoId: this.videoId,
      playerVars: paramsObj,
      events: {
        onReady: event => {
          event.target.playVideo();
        }
      }
    });
  }

  async addIframe() {
    if (this.classList.contains("lyt-activated")) return;
    this.classList.add("lyt-activated");

    const params = new URLSearchParams(this.getAttribute("params") || []);
    params.append("autoplay", "1");
    params.append("playsinline", "1");

    if (this.needsYTApiForAutoplay) {
      return this.addYTPlayerIframe(params);
    }

    const iframeEl = document.createElement("iframe");
    iframeEl.width = 560;
    iframeEl.height = 315;
    // No encoding necessary as [title] is safe. https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html#:~:text=Safe%20HTML%20Attributes%20include
    iframeEl.title = this.playLabel;
    iframeEl.allow =
      "accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture";
    iframeEl.allowFullscreen = true;
    // AFAIK, the encoding here isn't necessary for XSS, but we'll do it only because this is a URL
    // https://stackoverflow.com/q/64959723/89484
    iframeEl.src = `https://www.youtube-nocookie.com/embed/${encodeURIComponent(
      this.videoId
    )}?${params.toString()}`;
    this.append(iframeEl);

    // Set focus for a11y
    iframeEl.focus();
  }
}
// Register custom element
customElements.define("lite-youtube", LiteYTEmbed);
