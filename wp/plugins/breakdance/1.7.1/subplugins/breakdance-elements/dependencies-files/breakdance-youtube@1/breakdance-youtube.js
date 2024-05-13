// Wrapper for interacting with YouTube Embedded Videos
(function() {
  function BreakdanceYoutube() {
    return {
      instances: {},
      createInstance(id, config) {
        this.instances[id] = new YT.Player('youtubeEmbed' + id, {
          host: config.privacy_mode ? 'https://www.youtube-nocookie.com' : 'https://www.youtube.com',
          playerVars: {
            origin: window.location.origin,
            playsinline: config.playsinline ? 1 : 0,
            autoplay: 1,
            controls: 0,
            modestbranding: 1,
            rel: 0,
            mute: 1,
          },
          events: {
            onReady: (event) => {
              event.target.loadVideoById({
                videoId: config.videoId,
                startSeconds: config.start_time,
                endSeconds: config.end_time
              });
              if (config.pause_when_out_of_view === true) {
                this.pauseVideoWhenNotInViewport(id);
              }
            },
            onStateChange: (event) => {
              const playerState = event.data;
              if (playerState === YT.PlayerState.ENDED && config.loop) {
                if (config.start_time) {
                  event.target.seekTo(config.start_time);
                }
                event.target.playVideo();
              }
            }
          }
        });
      },
      updateInstance(element, id, config) {
        if (!this.instances[id]) {
          return;
        }
        this.destroyInstance(id);
        if (!document.querySelector("div#youtubeEmbed" + id)) {
          const playerElement = document.createElement("div");
          playerElement.id = "youtubeEmbed" + id;
          element.append(playerElement);
        }
        this.createInstance(id, config);
      },
      destroyInstance(id) {
        const player = this.instances[id];
        if (!player) {
          return;
        }
        player.destroy();
        delete this.instances[id];
      },
      pauseVideoWhenNotInViewport(id) {
        const element = document.getElementById('youtubeEmbed' + id);
        let isPaused = false;
        let observer = new IntersectionObserver(
          (entries, observer) => {
            entries.forEach(entry => {
              const player = this.instances[id];
              if (!player || !player.getPlayerState) {
                return;
              }
              if (
                entry.intersectionRatio !== 1 &&
                player.getPlayerState() === YT.PlayerState.PLAYING
              ) {
                player.pauseVideo();
                isPaused = true;
              } else if (isPaused) {
                player.playVideo();
                isPaused = false;
              }
            });
          },
          { threshold: 0.05 }
        );
        observer.observe(element);
      }
    };
  }

  window.breakdanceYoutube = BreakdanceYoutube();
})();
