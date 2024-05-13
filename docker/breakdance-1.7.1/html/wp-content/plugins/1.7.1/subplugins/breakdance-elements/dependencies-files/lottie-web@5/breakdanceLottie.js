(function() {
  function init(selector, options) {
    const { mergeObjects } = BreakdanceFrontend.utils;

    const optionDefaults = {
      loop_animation: false,
      // TODO replace this with a nicer animation, maybe one for Breakdance?
      asset_url:
        "https://assets7.lottiefiles.com/private_files/lf30_t7xfgnn4.json",
      animation_speed: 1,
      trigger: "viewport",
      hover_area: "animation",
      on_hover_out: "default",
      on_viewport_out: "default",
      times_to_loop: null,
      reverse_on_finish: null,
      frames: [0, 100]
    };

    if (options.trigger === "hover") {
      // a user can select reverse_on_finish = true, then switch to hover where it's not available, but we'll still get reverse_on_finish = true
      options.reverse_on_finish = null;
    }

    const settings = mergeObjects(optionDefaults, options);
    const element = document.querySelector(selector);

    if (!element) return;

    destroyAnimationAndClearEvents(element, selector);

    const animation = lottie.loadAnimation({
      container: element,
      renderer: "svg",
      // loop: true creates bugs with start/end and revert, so we mimic it with some logic in the 'onComplete' event
      loop: false,
      autoplay: false,
      path: settings.asset_url
    });

    if (!window.BreakdanceLottieInstances) {
      window.BreakdanceLottieInstances = {};
    }

    window.BreakdanceLottieInstances[selector] = {
      animation,
      settings,
      // Need these shenanigans to maintain the reference between init() calls to remove event listeners on destroy
      onHoverOut: () => _onHoverOut(animation, settings),
      playAnimation: () => _playAnimation(animation),
      playAnimationFromStart: () => _playAnimationFromStart(animation),
      viewportObserver: null
    };

    const setInitialValuesOnDomLoaded = () => {
      // animation.totalFrames value is set only on load, so it must be inside DOMLoaded
      // and "play()" depends on it, so we run everything in this event to prevent bugs
      registerAnimationSegments(animation, settings);
      registerTrigger(element, selector, animation, settings);
      animation.setSpeed(settings.animation_speed);
      startLoopAndReverse(animation, settings);

      animation.removeEventListener("DOMLoaded", setInitialValuesOnDomLoaded);
    };

    animation.addEventListener("DOMLoaded", setInitialValuesOnDomLoaded);
  }

  // --- Functions ---

  function registerTrigger(element, selector, animation, settings) {
    switch (settings.trigger) {
      case "click":
        element.addEventListener(
          "click",
          window.BreakdanceLottieInstances[selector].playAnimationFromStart
        );
        break;
      case "hover":
        addHoverEventListeners(selector, element, settings);
        break;
      case "viewport":
        addViewportEventListener(element, selector, animation);
        break;
      case "none":
      default:
        _playAnimationFromStart(animation);
        break;
    }
  }

  function startLoopAndReverse(animation, settings) {
    // can't use `animation.playCount` because it doesn't add if we're reverting
    let loop = 0;

    animation.onComplete = () => {
      if (settings.reverse_on_finish) {
        // forward = 1. Backward = -1
        const newDirection = animation.playDirection * -1;
        animation.setDirection(newDirection);
        animation.play();

        // reverse counts as part of the initial animation, only count a full loop once it finished the reverse animation
        if (newDirection === 1) {
          loop++;
        }
      } else {
        _playAnimation(animation, settings);
        loop++;
      }

      if (
        (settings.times_to_loop > 0 && loop >= settings.times_to_loop) ||
        (!settings.loop_animation && loop >= 1)
      ) {
        animation.stop();
      }
    };
  }

  function addHoverEventListeners(selector, element, settings) {
    const hoverElement = getElementToAttachHoverListenersTo(element, settings);

    if (!hoverElement) return;

    const instance = getLottieInstance(selector);

    if (!instance) return;

    hoverElement.addEventListener("mouseover", instance.playAnimation);
    hoverElement.addEventListener("mouseout", instance.onHoverOut);
  }

  function addViewportEventListener(element, selector, animation) {
    let options = {
      // could someone create an animation bigger than the screen and this not triggering?
      threshold: 0.5
    };

    const instance = getLottieInstance(selector);

    if (!instance) return;

    let observer = new IntersectionObserver(entries => {
      const entry = entries[0];

      if (entry.isIntersecting) {
        _playAnimation(animation);
      }
    }, options);

    observer.observe(element);

    window.BreakdanceLottieInstances[selector].viewportObserver = observer;
  }

  function destroyAnimationAndClearEvents(element, selector) {
    const previousInstance = getLottieInstance(selector);

    if (previousInstance) {
      previousInstance.animation.destroy();

      const oldSettings = previousInstance.settings;

      element.removeEventListener(
        "click",
        previousInstance.playAnimationFromStart
      );

      const hoverElement = getElementToAttachHoverListenersTo(
        element,
        oldSettings
      );

      if (hoverElement) {
        hoverElement.removeEventListener(
          "mouseover",
          previousInstance.playAnimation
        );
        hoverElement.removeEventListener(
          "mouseout",
          previousInstance.onHoverOut
        );
      }

      if (previousInstance.viewportObserver) {
        previousInstance.viewportObserver.unobserve(element);
      }
    }
  }

  function getElementToAttachHoverListenersTo(element, settings) {
    let elementToAttachHoverEventTo = element;

    if (settings.hover_area === "parent") {
      elementToAttachHoverEventTo = element.parentElement;
    } else if (settings.hover_area === "section") {
      elementToAttachHoverEventTo = element.parentElement.closest("section");
    }

    if (!elementToAttachHoverEventTo) return false;

    return elementToAttachHoverEventTo;
  }

  function getLottieInstance(selector) {
    if (
      window.BreakdanceLottieInstances &&
      window.BreakdanceLottieInstances[selector]
    ) {
      return window.BreakdanceLottieInstances[selector];
    }

    return null;
  }

  function _onHoverOut(animation, settings) {
    if (settings.on_hover_out === "pause") {
      animation.pause();
    } else if (settings.on_hover_out === "reverse") {
      animation.setDirection(-1);
    }
  }

  // currentFrame doesn't account for the last frame
  // https://github.com/airbnb/lottie-web/issues/2613#issuecomment-902975488
  function animationReachedLastFrame(animation) {
    return animation.currentFrame + 1 >= animation.totalFrames;
  }

  function _playAnimation(animation) {
    if (animationReachedLastFrame(animation)) {
      _playAnimationFromStart(animation);
    } else {
      animation.setDirection(1);
      animation.play();
    }
  }

  function _playAnimationFromStart(animation) {
    if (animation.isPaused) {
      animation.goToAndPlay(0);
    }
  }

  function registerAnimationSegments(animation, settings) {
    const totalFrames = animation.totalFrames;
    const startingFrame = totalFrames * (settings.frames[0] / 100);
    const finalFrame = totalFrames * (settings.frames[1] / 100);

    // if start/end frames aren't set, the default values will play the entire animation
    // the lib is smart and once this is set, it'll use it as the defaults from here on out
    animation.playSegments([startingFrame, finalFrame], true);
    // let the trigger handle when to start the animation. We just want to set the segments here
    animation.stop();
  }

  window.BreakdanceLottie = init;
})();
