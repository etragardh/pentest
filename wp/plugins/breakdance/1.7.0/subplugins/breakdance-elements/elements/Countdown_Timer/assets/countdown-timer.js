(function () {
  const { mergeObjects } = BreakdanceFrontend.utils;

  Number.prototype.zeroPad = function () {
    if (this < 10) {
      return ("0" + this).slice(-2);
    }
    return this;
  };

  class BreakdanceCountdownTimer {
    countdownInterval = null;
    countdownDate = null;
    defaultOptions = {
      builder: false,
      timer: {
        timer_type: "fixed",
        timezone: "local",
        year: "2023",
        month: "Jun",
        days: 0,
        hours: 0,
        minutes: 0,
        seconds: 0,
      },
      expire: {
        hide_timer_when_expired: false,
        expire_type: "redirect",
        redirect: "",
        show_message_preview: false,
      },
    };

    constructor(selector, options) {
      this.selector = selector;
      this.element = document.querySelector(
        `${this.selector} .js-countdown-timer`
      );
      this.days = this.element.querySelector(".js-days");
      this.hours = this.element.querySelector(".js-hours");
      this.minutes = this.element.querySelector(".js-minutes");
      this.seconds = this.element.querySelector(".js-seconds");
      this.daysWrap = this.element.querySelector(".js-days-wrap");
      this.hoursWrap = this.element.querySelector(".js-hours-wrap");
      this.minutesWrap = this.element.querySelector(".js-minutes-wrap");
      this.secondsWrap = this.element.querySelector(".js-seconds-wrap");
      this.donutDays = this.element.querySelector(".js-donut-days circle");
      this.donutHours = this.element.querySelector(".js-donut-hours circle");
      this.donutMinutes = this.element.querySelector(
        ".js-donut-minutes circle"
      );
      this.donutSeconds = this.element.querySelector(
        ".js-donut-seconds circle"
      );
      this.flipSeconds = this.element.querySelector(".js-flip-seconds");
      this.flipSecondsTens = this.element.querySelector(
        ".js-flip-seconds-tens"
      );
      this.flipMinutes = this.element.querySelector(".js-flip-minutes");
      this.flipMinutesTens = this.element.querySelector(
        ".js-flip-minutes-tens"
      );
      this.flipHours = this.element.querySelector(".js-flip-hours");
      this.flipHoursTens = this.element.querySelector(".js-flip-hours-tens");

      this.flipDays = this.element.querySelector(".js-flip-days");
      this.flipDaysTens = this.element.querySelector(".js-flip-days-tens");
      this.flipDaysHundreds = this.element.querySelector(
        ".js-flip-days-hundreds"
      );
      this.flipWrap = this.element.querySelector(".js-wrap-animation-flip");
      this.timeWrap = this.element.querySelector(".js-wrap");
      this.message = this.element.querySelector(".js-message");
      this.redirectMessage = this.element.querySelector(".js-redirect-message");
      this.storageId = `${this.selector.substr(1)}-evergreen-date`;
      this.options = mergeObjects(this.defaultOptions, {
        ...options.content,
        builder: options.builder,
      });
      this.donutCircumference = 295;
      this.firstPlay = true;

      this.init();
    }

    countdown() {
      const { timer } = this.options;

      if (timer.timer_type == "fixed") {
        this.countDownDate = new Date(
          `${timer.month} ${timer.days} ${timer.year} ${timer.hours}:${timer.minutes}:${timer.seconds}`
        ).getTime();
      }

      if (timer.timer_type == "fixed" && timer.timezone !== "local") {
        const tzDate = new Date(
          `${timer.month} ${timer.days} ${timer.year} ${timer.hours}:${timer.minutes}:${timer.seconds}`
        ).toLocaleString("en-US", { timeZone: timer.timezone });

        this.countDownDate = new Date(tzDate).getTime();
      }

      if (timer.timer_type == "evergreen") {
        this.countDownDate = this.getEvergreenDate(
          timer.days,
          timer.hours,
          timer.minutes,
          timer.seconds
        ).getTime();
      }

      this.countdownInterval = setInterval(() => {
        // Get today's date and time
        let now = new Date().getTime();

        // Find the distance between now and the count down date
        let distance = this.countDownDate - now;
        let time = this.getDistance(distance);

        this.showLabels(time);

        // Days
        if (this.days) {
          this.days.innerHTML =
            time.days.value > 0 ? time.days.value.zeroPad() : "00";

          // Donut animations
          if (this.donutDays) {
            this.donutDays.style.strokeDashoffset = parseFloat(
              this.donutCircumference * (1 - time.days.percentage)
            ).toFixed(2);
          }
        }

        // Hours
        if (this.hours) {
          this.hours.innerHTML =
            time.hours.value > 0 ? time.hours.value.zeroPad() : "00";

          if (this.donutHours) {
            this.donutHours.style.strokeDashoffset = parseFloat(
              this.donutCircumference * (1 - time.hours.percentage)
            ).toFixed(2);
          }
        }

        // Minutes
        if (this.minutes) {
          this.minutes.innerHTML =
            time.minutes.value > 0 ? time.minutes.value.zeroPad() : "00";

          if (this.donutMinutes) {
            this.donutMinutes.style.strokeDashoffset = parseFloat(
              this.donutCircumference * (1 - time.minutes.percentage)
            ).toFixed(2);
          }
        }

        // Seconds
        if (this.seconds) {
          this.seconds.innerHTML =
            time.seconds.value > 0 ? time.seconds.value.zeroPad() : "00";

          if (this.donutSeconds) {
            this.donutSeconds.style.strokeDashoffset = parseFloat(
              this.donutCircumference * (1 - time.seconds.percentage)
            ).toFixed(2);
          }
        }

        // Flip animation

        if (this.flipWrap && distance >= 1000) {
          // We need to set the digits on the first play
          if (this.firstPlay === true) {
            this.setFlipDigitDefault(this.flipSeconds, time.seconds.ones);
            this.setFlipDigitDefault(this.flipSecondsTens, time.seconds.tens);
            this.setFlipDigitDefault(this.flipMinutes, time.minutes.ones);
            this.setFlipDigitDefault(this.flipMinutesTens, time.minutes.tens);
            this.setFlipDigitDefault(this.flipHours, time.hours.ones);
            this.setFlipDigitDefault(this.flipHoursTens, time.hours.tens);
            this.setFlipDigitDefault(this.flipDays, time.days.ones);
            this.setFlipDigitDefault(this.flipDaysTens, time.days.tens);

            // Hide days hundreds if they are none
            if (time.days.hundreds == 0) {
              this.flipDaysHundreds.classList.add("is-hidden");
            } else {
              this.flipDaysHundreds.classList.remove("is-hidden");
              this.setFlipDigitDefault(
                this.flipDaysHundreds,
                time.days.hundreds
              );
            }

            this.firstPlay = false;
          }

          if (time.seconds.ones >= 0) {
            this.flip("seconds");
            this.setFlipDigit(this.flipSeconds, false, time.seconds.ones);

            if (time.seconds.ones === 0) {
              this.flip("seconds-tens");
              this.setFlipDigit(
                this.flipSecondsTens,
                "minutes",
                time.seconds.tens
              );
            }

            if (time.seconds.ones === 0 && time.seconds.tens === 0) {
              this.flip("minutes");
              this.setFlipDigit(this.flipMinutes, "seconds", time.minutes.ones);
            }

            if (
              time.minutes.ones === 0 &&
              time.seconds.ones === 0 &&
              time.seconds.tens === 0
            ) {
              this.flip("minutes-tens");
              this.setFlipDigit(
                this.flipMinutesTens,
                "minutes",
                time.minutes.tens
              );
            }

            if (
              time.minutes.tens === 0 &&
              time.minutes.ones === 0 &&
              time.seconds.ones === 0 &&
              time.seconds.tens === 0
            ) {
              this.flip("hours");
              this.setFlipDigit(this.flipHours, "hours", time.hours.ones);
            }

            if (
              time.hours.ones == 0 &&
              time.minutes.tens === 0 &&
              time.minutes.ones === 0 &&
              time.seconds.ones === 0 &&
              time.seconds.tens === 0
            ) {
              this.flip("hours-tens");
              this.setFlipDigit(
                this.flipHoursTens,
                "hours-tens",
                time.hours.tens
              );
            }

            if (
              time.hours.ones == 0 &&
              time.hours.tens == 0 &&
              time.minutes.tens === 0 &&
              time.minutes.ones === 0 &&
              time.seconds.ones === 0 &&
              time.seconds.tens === 0
            ) {
              this.flip("days");
              this.setFlipDigit(this.flipDays, false, time.hours.tens);
            }

            if (
              time.hours.tens == 0 &&
              time.hours.ones == 0 &&
              time.hours.tens == 0 &&
              time.minutes.tens === 0 &&
              time.minutes.ones === 0 &&
              time.seconds.ones === 0 &&
              time.seconds.tens === 0
            ) {
              this.flip("days-tens");
              this.setFlipDigit(this.flipDaysTens, false, time.days.tens);
            }

            if (
              time.hours.tens == 0 &&
              time.days.tens == 0 &&
              time.hours.ones == 0 &&
              time.hours.tens == 0 &&
              time.minutes.tens === 0 &&
              time.minutes.ones === 0 &&
              time.seconds.ones === 0 &&
              time.seconds.tens === 0
            ) {
              this.flip("days-hundreds");
              this.setFlipDigit(
                this.flipDaysHundreds,
                false,
                time.days.hundreds
              );
            }
          }
        }

        // If the count down is finished, proceed with the actions
        if (distance < 0 && this.options.expire.expire_type === "message") {
          clearInterval(this.countdownInterval);
          if (this.message) {
            this.message.classList.remove("is-hidden");
          }

          if (this.options.expire.hide_timer_when_expired === true) {
            this.timeWrap.classList.add("is-hidden");
          }
        }
        if (
          distance < 0 &&
          this.options.expire.expire_type === "redirect" &&
          this.options.expire.redirect.length > 0
        ) {
          clearInterval(this.countdownInterval);
          if (this.redirectMessage) {
            this.redirectMessage.classList.remove("is-hidden");
          }
          if (!this.options.builder) {
            window.location.href = this.options.expire.redirect;
          }
        }
      }, 1000);
    }

    setFlipDigitDefault(element, digit) {
      if (element) {
        element
          .querySelectorAll("li .inn")
          .forEach((el) => (el.innerHTML = digit));
      }
    }

    setFlipDigit(element, type, digit) {
      if (element) {
        element
          .querySelectorAll("li.before .inn")
          .forEach((el) => (el.innerHTML = digit));
        element
          .querySelectorAll("li.active .inn")
          .forEach((el) => (el.innerHTML = this.setDigitValue(type, digit)));
      }
    }

    setDigitValue(type, digit) {
      if (digit > 0) {
        return digit - 1;
      } else {
        if (type == "minutes") {
          return 5;
        } else if (type == "hours") {
          return 3;
        } else if (type == "hours-tens") {
          return 2;
        } else {
          return 9;
        }
      }
    }

    flip(target) {
      this.element.classList.remove("play");

      const liFirst = this.element.querySelectorAll(`.js-flip-${target} li`)[0];
      const liSecond = this.element.querySelectorAll(
        `.js-flip-${target} li`
      )[1];

      if (liFirst.className.indexOf("active") > -1) {
        liFirst.className = "before";
      } else {
        liFirst.className = "active";
      }

      if (liSecond.className.indexOf("before") > -1) {
        liSecond.className = "active";
      } else {
        liSecond.className = "before";
      }

      this.element.classList.add("play");
    }

    getDistance(distance) {
      // Time calculations for days, hours, minutes and seconds
      const days = Math.floor(distance / (1000 * 60 * 60 * 24));

      const daysPercentage =
        this.options.timer.timer_type == "evergreen"
          ? days / this.options.timer.days
          : days / 31;

      // Days
      const daysOnes = Math.floor(days % 10);
      const daysTens = Math.floor((days / 10) % 10);
      const daysHundreds = Math.floor((days / 100) % 10);

      // Hours
      const hours = Math.floor(
        (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
      );
      const hoursPercentage = hours / 24;
      const hoursOnes = Math.floor(hours % 10);
      const hoursTens = Math.floor(hours / 10);

      // Minutes
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const minutesPercentage = minutes / 60;
      const minutesOnes = Math.floor(minutes % 10);
      const minutesTens = Math.floor(minutes / 10);

      // Seconds
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      const secondsPercentage = seconds / 60;
      const secondsOnes = Math.floor(seconds % 10);
      const secondsTens = Math.floor(seconds / 10);

      return {
        seconds: {
          value: seconds,
          percentage: secondsPercentage,
          ones: secondsOnes,
          tens: secondsTens,
        },
        minutes: {
          value: minutes,
          percentage: minutesPercentage,
          ones: minutesOnes,
          tens: minutesTens,
        },
        hours: {
          value: hours,
          percentage: hoursPercentage,
          ones: hoursOnes,
          tens: hoursTens,
        },
        days: {
          value: days,
          percentage: daysPercentage,
          ones: daysOnes,
          tens: daysTens,
          hundreds: daysHundreds,
        },
      };
    }

    showLabels(time) {
      if (time.days.value == 1 && this.daysWrap) {
        this.daysWrap.classList.add("is-one");
      } else {
        this.daysWrap.classList.remove("is-one");
      }

      if (time.hours.value == 1 && this.hoursWrap) {
        this.hoursWrap.classList.add("is-one");
      } else {
        this.hoursWrap.classList.remove("is-one");
      }

      if (time.minutes.value == 1 && this.minutesWrap) {
        this.minutesWrap.classList.add("is-one");
      } else {
        this.minutesWrap.classList.remove("is-one");
      }

      if (time.seconds.value == 1 && this.secondsWrap) {
        this.secondsWrap.classList.add("is-one");
      } else {
        this.secondsWrap.classList.remove("is-one");
      }
    }

    // https://stackoverflow.com/a/1214753/18511
    dateAdd(date, interval, units) {
      if (!(date instanceof Date)) return undefined;
      let ret = new Date(date);
      const checkRollover = function () {
        if (ret.getDate() != date.getDate()) ret.setDate(0);
      };
      switch (String(interval).toLowerCase()) {
        case "year":
          ret.setFullYear(ret.getFullYear() + units);
          checkRollover();
          break;
        case "quarter":
          ret.setMonth(ret.getMonth() + 3 * units);
          checkRollover();
          break;
        case "month":
          ret.setMonth(ret.getMonth() + units);
          checkRollover();
          break;
        case "week":
          ret.setDate(ret.getDate() + 7 * units);
          break;
        case "day":
          ret.setDate(ret.getDate() + units);
          break;
        case "hour":
          ret.setTime(ret.getTime() + units * 3600000);
          break;
        case "minute":
          ret.setTime(ret.getTime() + units * 60000);
          break;
        case "second":
          ret.setTime(ret.getTime() + units * 1000);
          break;
        default:
          ret = undefined;
          break;
      }
      return ret;
    }

    getEvergreenDate(days = 0, hours = 0, minutes = 0, seconds = 0) {
      if (!localStorage.getItem(this.storageId)) {
        localStorage.setItem(this.storageId, Date.now());
      }

      let date = new Date(JSON.parse(localStorage.getItem(this.storageId)));

      date = this.dateAdd(date, "day", days);
      date = this.dateAdd(date, "hour", hours);
      date = this.dateAdd(date, "minute", minutes);
      date = this.dateAdd(date, "second", seconds);

      return date;
    }

    messagePreview() {
      if (
        this.options.builder === true &&
        this.message &&
        this.options.expire.show_message_preview === true
      ) {
        this.message.classList.remove("is-hidden");
      }
    }
    // Methods
    destroy() {
      if (localStorage.getItem(this.storageId)) {
        localStorage.removeItem(this.storageId);
      }
      clearInterval(this.countdownInterval);
      this.firstPlay = false;
      this.countdownInterval = null;
      this.countDownDate = null;
      if (this.message) {
        this.message.classList.add("is-hidden");
      }
      if (this.redirectMessage) {
        this.redirectMessage.classList.add("is-hidden");
      }
      this.timeWrap.classList.remove("is-hidden");
    }

    update(options = {}) {
      this.options = mergeObjects(this.options, {
        ...options.content,
        builder: options.builder,
      });
      this.destroy();
      this.init();
    }

    init() {
      this.messagePreview();
      this.countdown();
    }
  }

  window.BreakdanceCountdownTimer = BreakdanceCountdownTimer;
})();
