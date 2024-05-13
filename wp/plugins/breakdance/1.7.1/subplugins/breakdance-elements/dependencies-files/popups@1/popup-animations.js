/* global gsap */
(function () {
    const {
        mergeObjects,
        prefersReducedMotion
    } = BreakdanceFrontend.utils;

    class BreakdancePopupAnimation {
        animatingClass = 'breakdance-popup-animating';
        completedClass = 'breakdance-popup-open';

        slideWidth = 200;
        slideHeight = 200;
        flipDistance = 100;

        entranceAnimations = {
            fade: [
                { alpha: 0 },
                { alpha: 1 }
            ],

            slideUp: [
                { y: this.slideHeight },
                { y: 0 }
            ],
            slideDown: [
                { y: -this.slideHeight },
                { y: 0 }
            ],
            slideRight: [
                { x: -this.slideWidth },
                { x: 0 }
            ],
            slideLeft: [
                { x: this.slideWidth },
                { x: 0 }
            ],

            flipUp: [
                { perspective: 2500, rotateX: -this.flipDistance },
                { rotateX: 0 }
            ],

            flipDown: [
                { perspective: 2500, rotateX: this.flipDistance },
                { rotateX: 0 }
            ],

            flipLeft: [
                { perspective: 2500, rotateY: -this.flipDistance },
                { rotateY: 0 }
            ],

            flipRight: [
                { perspective: 2500, rotateY: this.flipDistance },
                { rotateY: 0 }
            ],

            zoomIn: [
                { scale: 0.6 },
                { scale: 1 }
            ],
            zoomOut: [
                { scale: 1.2 },
                { scale: 1 }
            ],
        };


        exitAnimations = {
            fade: [
                { alpha: 1 },
                { alpha: 0 }
            ],

            slideDown: [
                { y: 0 },
                { y: this.slideHeight },
            ],
            slideUp: [
                { y: 0 },
                { y: -this.slideHeight },
            ],
            slideRight: [
                { x: 0 },
                { x: this.slideWidth },
            ],
            slideLeft: [
                { x: 0 },
                { x: -this.slideWidth },
            ],

            flipUp: [
                { rotateX: 0 },
                { perspective: 2500, rotateX: this.flipDistance },
            ],

            flipDown: [
                { rotateX: 0 },
                { perspective: 2500, rotateX: -this.flipDistance },
            ],

            flipLeft: [
                { rotateY: 0 },
                { perspective: 2500, rotateY: this.flipDistance },
            ],

            flipRight: [
                { rotateY: 0 },
                { perspective: 2500, rotateY: -this.flipDistance },
            ],

            zoomOut: [
                { scale: 1 },
                { scale: 0.6 },
            ],
            zoomIn: [
                { scale: 1 },
                { scale: 1.2 },
            ],
        };

        defaultOptions = {
            animation_type: null,
            duration: { number: 500, unit: 'ms', style: '500ms' },
            delay: { number: 0, unit: 'ms', style: '0ms' },
            ease: 'power1.out',
        };

        initialized = false;

        constructor(element, wrapper, options, isExitAnimation = false) {
            this.element = element;
            this.wrapper = wrapper;
            this.options = mergeObjects(this.defaultOptions, options);
            this.isExitAnimation = isExitAnimation;

            this.init();
        }

        getDuration(value) {
            if (!value) return value;
            if (value.unit === 's') return value.number;
            return value.number / 1000; // Convert MS to S
        }

        createTween() {
            const type = this.options.animation_type;
            const animations = this.isExitAnimation ? this.exitAnimations : this.entranceAnimations;

            if (!animations[type]) {
                console.log(`[POPUP] The selected ${type} animation is invalid.`);
                return;
            }

            const [from, to] = animations[type];
            const ease = this.options.ease;
            const duration = this.getDuration(this.options.duration);
            const delay = this.getDuration(this.options.delay);

            const anim = gsap.timeline({
                paused: true
            });

            anim.fromTo(this.wrapper, {
                alpha: this.isExitAnimation ? 1 : 0
            }, {
                alpha: this.isExitAnimation ? 0 : 1,
                duration,
                delay,
            }, 0);

            anim.fromTo(this.element, {
                    ...from,
                    autoAlpha: this.isExitAnimation ? 1 : 0
                },
                {
                    ...to,
                    autoAlpha: this.isExitAnimation ? 0 : 1,
                    duration,
                    delay,
                    ease,
                    clearProps: 'all',
                    onStart: () => {
                        this.wrapper.classList.add(this.animatingClass);
                    },
                    onComplete: () => {
                        this.wrapper.classList.remove(this.animatingClass);
                        if (this.isExitAnimation) {
                            this.wrapper.classList.remove(this.completedClass);
                        } else {
                            this.wrapper.classList.add(this.completedClass);
                        }
                    }
                }, 0);

            return anim;
        }

        play() {
            if (!this.tween) return;
            return this.tween.progress(0).play();
        }

        initTween() {
            if (this.initialized) return;
            this.initialized = true;

            this.element.classList.remove(this.completedClass);
            this.tween = this.createTween();
        }

        destroy() {
            this.initialized = false;

            if (!this.element) return;

            this.element.classList.add(this.completedClass);

            if (!this.tween) return;

            this.tween.kill();
            this.tween = null;

            // Remove all inline styles
            gsap.set(this.element, { clearProps: 'all' });
        }

        init() {
            if (!this.options.animation_type) return;

            if (prefersReducedMotion()) {
                console.log('[POPUP] Not playing animations. "Prefers Reduced Motion" is enabled.')
                this.element.classList.add(this.completedClass);
                return;
            }

            this.initTween();
        }
    }

    window.BreakdancePopupAnimation = BreakdancePopupAnimation;
}());
