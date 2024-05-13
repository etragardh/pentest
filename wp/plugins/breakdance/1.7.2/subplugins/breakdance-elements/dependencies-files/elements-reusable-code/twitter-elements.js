(function () {
  class BreakdanceTwitter {
    constructor(element) {
      this.loadScript.bind(this);
      this.selector = element;
      this.element = this.queryElement(element);
      this.embed = this.queryElement(`${this.selector}.js-twitter-embed`);
      this.init();
    }

    queryElement(element) {
      if (typeof element == "string") {
        return document.querySelector(element);
      }
      return element;
    }

    loadScript() {
      return new Promise(function (resolve, reject) {
        let script = document.createElement("script");
        script.src = `https://platform.twitter.com/widgets.js`;
        script.onload = resolve;
        script.async = true;
        script.onerror = reject;
        script.id = "twitter-api";
        document.head.append(script);
      });
    }

    getTweetData(url) {
      let match;
      const regex =
        /^https?:\/\/twitter\.com\/(?:#!\/)?(\w+)\/status(es)?\/(\d+)/;

      if ((match = regex.exec(url)) !== null) {
        return {
          id: match[3] ? match[3] : null,
          username: match[1] ? match[1] : null,
        };
      }
    }

    createElement(dataset) {
      if (!dataset) return;
      this.embed.innerHTML = "";

      return new Promise((resolve) => {
        if (dataset.twitterEmbed == "button") {
          const type = dataset.type;

          if (type == "share") {
            twttr.widgets.createShareButton(dataset.shareUrl, this.embed, {
              size: dataset.size,
              text: dataset.tweetText,
              via: dataset.via,
            });
          }
          if (type == "follow") {
            twttr.widgets.createFollowButton(dataset.followUser, this.embed, {
              size: dataset.size,
              showCount: dataset.showCount,
              showScreenName: dataset.showScreenName,
            });
          }
          if (type == "mention") {
            twttr.widgets.createMentionButton(dataset.mentionUser, this.embed, {
              size: dataset.size,
            });
          }
          if (type == "hashtag") {
            twttr.widgets.createHashtagButton(dataset.hashtag, this.embed, {
              size: dataset.size,
            });
          }
          if (type == "dm") {
            twttr.widgets.createDMButton(dataset.dmUser, this.embed, {
              size: dataset.size,
            });
          }
        }
        if (dataset.twitterEmbed == "tweet") {
          const tweet = this.getTweetData(dataset.url);
          twttr.widgets.createTweet(tweet.id, this.embed, {
            theme: dataset.theme,
            conversation: dataset.conversation,
            cards: dataset.cards,
          });
        }
        if (dataset.twitterEmbed == "timeline") {
          twttr.widgets.createTimeline(
            {
              sourceType: "profile",
              screenName: dataset.username,
            },
            this.embed,
            {
              height: dataset.height,
              chrome: dataset.chrome,
              width: dataset.width,
              limit: dataset.limit,
              theme: dataset.theme,
              showReplies: dataset.showReplies,
              tweetLimit: dataset.limit,
            }
          );
        }
        resolve();
      });
    }

    async update() {
      if (typeof twttr !== "undefined") {
        await this.createElement(this.embed.dataset);
        twttr.widgets.load(this.element);
      }
    }

    destroy() {
      this.element = null;
      if (this.embed) {
        this.embed.innerHTML = "";
      }
    }

    async init() {
      if (typeof twttr === "undefined") {
        await this.loadScript();
        this.update(this.element);
      } else {
        this.update(this.element);
      }
    }
  }

  window.BreakdanceTwitter = BreakdanceTwitter;
})();
