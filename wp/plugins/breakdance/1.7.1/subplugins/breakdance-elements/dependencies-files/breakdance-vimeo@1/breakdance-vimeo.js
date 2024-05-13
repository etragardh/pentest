// Wrapper for interacting with Vimeo Embedded Videos
(function () {
    function BreakdanceVimeo() {
        return {
            instances: {},
            createInstance(element, id, config) {
                const player = new Vimeo.Player(element, {
                    id: config.id,
                    muted: true,
                    loop: config.loop,
                    controls: false,
                    autoplay: true,
                    playsinline: config.playsinline,
                });
                this.instances[id] = player;

                player.on('loaded', () => {
                    if (config.start_time) {
                        player.setCurrentTime(config.start_time);
                    }
                    if (config.pause_when_out_of_view) {
                        this.pauseVideoWhenNotInViewport(element, player)
                    }
                });

                player.on('timeupdate', (data) => {
                    if (config.end_time !== null && data.seconds > config.end_time) {
                        if (config.loop === true) {
                            return player.setCurrentTime(config.start_time ?? 0);
                        }
                        return player.pause();
                    }
                });
            },
            updateInstance(element, id, config) {
                if (this.instances[id]) {
                    this.destroyInstance(id);
                }
                this.createInstance(element, id, config);
            },
            pauseVideoWhenNotInViewport(element, player) {
                let isPaused = false;
                let observer = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.intersectionRatio < 0.2) {
                            player.getPaused().then((paused) => {
                                if (!paused) {
                                    player.pause().then(() => {
                                        isPaused = true;
                                    })
                                }
                            });
                        } else {
                            if (isPaused) {
                                player.play().then(() => {
                                    isPaused = false
                                });
                            }
                        }
                    });
                }, {threshold: 0.2});
                observer.observe(element.querySelector('iframe'));
            },
            destroyInstance(id) {
                if (!this.instances[id]) {
                    return;
                }
                this.instances[id].destroy();
                delete this.instances[id];
            }
        }
    }

    window.breakdanceVimeo = BreakdanceVimeo();
})();
