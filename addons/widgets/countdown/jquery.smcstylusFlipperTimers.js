/**
 * jQuery.smcstylusFlipperTimers is a jQuery plugin that can display a timer or countdown with a flipp clock design.
 *
 * Version: 1.0.0
 * Author: Mihai Calin Simion
 * E-mail: simion.mihai.calin@gmail.com
 * Website: http://www.smcstylus.com
 *
 * Changes/addons regarding the original plugin
 * - change of plugin structure and code: (now using prototype) based on class and instances, refracting code, clear timers,
 * - options instead of data,
 * - auto direction (clock or countdown),
 * - show/hide labels,
 * - date template validation,
 * - countdown test option (5, 15, 70 seconds) -  useful for testing,
 * - suport callback function,
 * - destroy function
 * - from the original plugin is keeping the options: responsive, hide timers
 *
 * Note: Use fix-timers.js for the delay when you change the tabs on some browsers !!!
 *
 * This software is based on the original https://github.com/ArthurShlain/jQuery-ResponsiveFlipper
 *
 */
jQuery(function ($) {
  // https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Global_Objects/String/repeat
  if (!String.prototype.repeat) {
    String.prototype.repeat = function (count) {
      "use strict";
      if (this == null) {
        throw new TypeError("can't convert " + this + " to object");
      }
      let str = "" + this;
      count = +count;
      if (count != count) {
        count = 0;
      }
      if (count < 0) {
        throw new RangeError("repeat count must be non-negative");
      }
      if (count == Infinity) {
        throw new RangeError("repeat count must be less than infinity");
      }
      count = Math.floor(count);
      if (str.length == 0 || count == 0) {
        return "";
      }
      // Обеспечение того, что count является 31-битным целым числом, позволяет нам значительно
      // соптимизировать главную часть функции. Впрочем, большинство современных (на август
      // 2014 года) браузеров не обрабатывают строки, длиннее 1 << 28 символов, так что:
      if (str.length * count >= 1 << 28) {
        throw new RangeError(
          "repeat count must not overflow maximum string size"
        );
      }
      let rpt = "",
        i;
      for (i = 0; i < count; i++) {
        rpt += str;
      }
      return rpt;
    };
  }

  // https://github.com/uxitten/polyfill/blob/master/string.polyfill.js
  // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/padStart
  if (!String.prototype.padStart) {
    String.prototype.padStart = function padStart(targetLength, padString) {
      targetLength = targetLength >> 0; //truncate if number or convert non-number to 0;
      padString = String(typeof padString !== "undefined" ? padString : " ");
      if (this.length > targetLength) {
        return String(this);
      } else {
        targetLength = targetLength - this.length;
        if (targetLength > padString.length) {
          padString += padString.repeat(targetLength / padString.length); //append to original to ensure we are longer than needed
        }
        return padString.slice(0, targetLength) + String(this);
      }
    };
  }

  /**
   * Array.prototype.indexOf fallback for IE8
   * @param {Mixed} mixed
   * @returns {Number}
   */
  if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (elt /*, from*/) {
      let len = this.length >>> 0;

      let from = Number(arguments[1]) || 0;
      from = from < 0 ? Math.ceil(from) : Math.floor(from);
      if (from < 0) from += len;

      for (; from < len; from++) {
        if (from in this && this[from] === elt) return from;
      }
      return -1;
    };
  }

  /**
   * Function s4() and guid() originate from:
   * http://stackoverflow.com/questions/105034/how-to-create-a-guid-uuid-in-javascript
   */
  function s4() {
    return Math.floor((1 + Math.random()) * 0x10000)
      .toString(16)
      .substring(1);
  }

  /**
   * Creates a unique id
   * @returns {String}
   */
  function guid() {
    return (
      s4() +
      s4() +
      "-" +
      s4() +
      "-" +
      s4() +
      "-" +
      s4() +
      "-" +
      s4() +
      s4() +
      s4()
    );
  }
  let Flipper_Instance_List = {};

  let Flipper_Instance = function (element, options) {
    this.el = element;

    // Attach default options to instance
    this.defaultOptions = this.settings.defaults;

    this.vars = {
      time: {
        days: null,
        hours: null,
        minutes: null,
        seconds: null,
      },
      timer: null,
      timerFlip: null,
      clearTimers: false,
    };

    this.options = null;
    this.setOptions(options);
    this.initialize();
  };
  Flipper_Instance.prototype.setOptions = function (options) {
    if (this.options === null) {
      // Merge default options with user options and attach to instance
      this.options = $.extend(this.defaultOptions, options);
      // Set the animation direction for displaying as clock or countdown
      this.options["reverse"] =
        this.options.datetime === "now" || this.options.datetime === "clock"
          ? false
          : true;
    }
  };

  Flipper_Instance.prototype.settings = {
    defaults: {
      datetime: "now",
      time: {
        days: {
          show: true,
          label: "Days",
          template: "ddd",
        },
        hours: {
          show: true,
          label: "Hours",
          template: "HH",
        },
        minutes: {
          show: true,
          label: "Minutes",
          template: "ii",
        },
        seconds: {
          show: true,
          label: "Seconds",
          template: "ss",
        },
      },
      showLabels: true,
      callback: null,
      useTrigger: false,
    },
    datesTemplate: ["d", "dd", "ddd", "H", "HH", "i", "ii", "s", "ss"],
    time: {
      now: Date.now(),
      flip: 400,
    },
  };

  Flipper_Instance.prototype.initialize = function () {
    let el = $(this.el);
    let _this = this;

    if (el.hasClass("flipper-initialized")) {
      console.warn("Flipper already initialized.");
      return;
    }
    el.addClass("flipper-initialized");

    el.attr("data-fc-active", "true");
    let n,
      timers = [],
      timer,
      rev = this.options.reverse ? "reverse" : "";

    // Build the timers object deppending by if 'show' value is true and template is corect set
    Object.keys(this.options.time).forEach((key) => {
      if (
        _this.options.time[key].show === true &&
        _this.settings.datesTemplate.indexOf(
          _this.options.time[key].template
        ) !== -1
      )
        timers.push(_this.options.time[key]);
    });

    // Stop if no timers to show
    if (timers.length < 1) return;

    timers.forEach((key, index) => {
      // Add delimiter
      if (index > 0) {
        el.append('<div class="flipper-group flipper-delimiter">:</div>');
      }

      el.append(
        '<div class="flipper-group flipper-' + key.template + '"></div>'
      );

      timer = el.find(".flipper-group.flipper-" + key.template);

      // Display labels
      if (_this.options.showLabels)
        timer.append("<label>" + key.label + "</label>");

      if (
        key.template === "d" ||
        key.template === "H" ||
        key.template === "i" ||
        key.template === "s"
      ) {
        timer.append('<div class="flipper-digit ' + rev + '"></div>');
      }

      if (
        key.template === "dd" ||
        key.template === "ddd" ||
        key.template === "HH" ||
        key.template === "ii" ||
        key.template === "ss"
      ) {
        timer.append('<div class="flipper-digit ' + rev + '"></div>');
        timer.append('<div class="flipper-delimiter"></div>');
        timer.append('<div class="flipper-digit ' + rev + '"></div>');
        if (key.template === "ddd") {
          timer.append('<div class="flipper-delimiter"></div>');
          timer.append('<div class="flipper-digit ' + rev + '"></div>');
        }
      }

      if (key.template === "d") {
        for (n = 0; n <= 31; n++) {
          _this.printDigitFace(timer, 0, n);
          _this.printDigitFace(timer, 1, n);
        }
      }
      if (key.template === "H") {
        for (n = 0; n <= 23; n++) {
          _this.printDigitFace(timer, 0, n);
          _this.printDigitFace(timer, 1, n);
        }
      }
      if (key.template === "i" || key.template === "s") {
        for (n = 0; n <= 59; n++) {
          _this.printDigitFace(timer, 0, n);
          _this.printDigitFace(timer, 1, n);
        }
      }
      if (key.template === "dd" || key.template === "ddd") {
        for (n = 0; n <= 9; n++) {
          _this.printDigitFace(timer, 0, n);
          _this.printDigitFace(timer, 1, n);
          if (key.template === "ddd") {
            _this.printDigitFace(timer, 2, n);
          }
        }
      }
      if (key.template === "HH") {
        for (n = 0; n <= 2; n++) {
          _this.printDigitFace(timer, 0, n);
        }
        for (n = 0; n <= 9; n++) {
          _this.printDigitFace(timer, 1, n);
        }
      }
      if (key.template === "ii" || key.template === "ss") {
        for (n = 0; n <= 5; n++) {
          _this.printDigitFace(timer, 0, n);
        }
        for (n = 0; n <= 9; n++) {
          _this.printDigitFace(timer, 1, n);
        }
      }
    });

    // Draw timers
    this.startTimer(el);

    // Resize timers
    this.resizeFlipper(el);
    $(window).on("resize", function () {
      _this.resizeFlipper(el);
    });

    // window.addEventListener("resize", () => {
    //   _this.resizeFlipper($(_this.el));
    // });

    clearInterval(this.vars.timerFlip);
    this.vars.timerFlip = setInterval(function () {
      if (_this.vars.clearTimers) {
        clearInterval(_this.vars.timerFlip);
        return;
      }

      el.find(".flipper-digit[data-value]").each(function () {
        let $digit = $(this);
        if ($digit.find(".active").html() === $digit.attr("data-value")) {
          return; //
        }
        if (!$digit.is(".r")) {
          _this.flipDigit($digit);
        }
      });
    }, _this.settings.time.flip / 4);
  };

  Flipper_Instance.prototype.printDigitFace = function (el, index, val) {
    el.find(`.flipper-digit:eq(${index})`).append(
      `<div class="digit-face">${val}</div>`
    );
  };

  Flipper_Instance.prototype.setDigitAnimateValue = function (
    el,
    type,
    index,
    val
  ) {
    el.find(`.flipper-${type}`)
      .find(`.flipper-digit:eq(${index})`)
      .attr("data-value", val);
  };

  Flipper_Instance.prototype.setDigitStaticValue = function (
    el,
    type,
    index,
    val
  ) {
    el.find(
      `.flipper-${type} .flipper-digit:eq(${index}) .digit-face:contains(${val})`
    ).addClass("active");
  };

  Flipper_Instance.prototype.GUI = function (el) {
    el.find(".flipper-digit").each(function () {
      let $digit = $(this);
      let value = $digit.find(".digit-face.active").html();
      $digit.find(".digit-top").remove();
      $digit.find(".digit-top2").remove();
      $digit.find(".digit-bottom").remove();
      $digit.find(".digit-next").remove();
      $digit.prepend('<div class="digit-top">' + value + "</div>");
      $digit.prepend('<div class="digit-top2">' + value + "</div>");
      $digit.prepend('<div class="digit-bottom">' + value + "</div>");
      $digit.prepend('<div class="digit-next"></div>');
    });
  };

  Flipper_Instance.prototype.flipDigit = function ($digit) {
    let _this = this;
    if (!$digit.closest(".flipper").is(".flipper-initialized")) {
      return;
    }
    if ($digit.hasClass("r")) {
      setTimeout(function () {
        _this.flipDigit($digit);
      }, _this.settings.time.flip + 1);
      return;
    }
    $digit.addClass("r");

    let $currentTop = $digit.find(".digit-top"),
      $currentTop2 = $digit.find(".digit-top2"),
      $currentBottom = $digit.find(".digit-bottom"),
      $activeDigit = $digit.find(".digit-face.active"),
      $firstDigit = $digit.find(".digit-face:first"),
      $prevDigit = $activeDigit.prev(".digit-face"),
      $nextDigit = $activeDigit.next(".digit-face"),
      $lastDigit = $digit.find(".digit-face:last"),
      $next;

    if ($digit.hasClass("reverse")) {
      $next = $prevDigit.length ? $prevDigit : $lastDigit;
    } else {
      $next = $nextDigit.length ? $nextDigit : $firstDigit;
    }

    //let current = parseInt($currentTop.html());
    let next = $next.html();
    $digit.find(".digit-next").html(next);
    $digit.find(".digit-face").removeClass("active");
    $next.addClass("active");
    $currentTop.addClass("r");
    $currentTop2.addClass("r");
    $currentBottom.addClass("r");
    if (next.toString() === $digit.attr("data-value")) {
      $digit.removeAttr("data-value");
    }
    setTimeout(function () {
      $currentTop.html(next).hide();
      $currentTop2.html(next);
      setTimeout(function () {
        $currentBottom.html(next).removeClass("r");
        $currentTop.removeClass("r").show();
        $currentTop2.html(next).removeClass("r");
        $digit.removeClass("r");
      }, _this.settings.time.flip / 2);
    }, _this.settings.time.flip / 2);
  };

  Flipper_Instance.prototype.resizeFlipper = function (el) {
    let parentWidth,
      flipperWidth,
      maxFontSize = 1000,
      fontSize = maxFontSize,
      i = 0,
      minFontSize = 0;

    el.css("font-size", fontSize + "px");
    while (i < 20) {
      i++;
      parentWidth = el.innerWidth();
      el.css("width", "9999px");
      flipperWidth = 0;
      el.find(".flipper-group").each(function () {
        let w = parseFloat($(this).outerWidth());
        flipperWidth += w;
      });
      if (parentWidth - flipperWidth < 10 && parentWidth - flipperWidth > 0) {
        el.css("width", "");
        return;
      }
      if (flipperWidth > parentWidth) {
        maxFontSize = fontSize < maxFontSize ? fontSize : maxFontSize;
      } else {
        minFontSize = fontSize > minFontSize ? fontSize : minFontSize;
      }
      fontSize = (maxFontSize + minFontSize) / 2;
      el.css("width", "");
      el.css("font-size", fontSize + "px");
    }
  };
  Flipper_Instance.prototype.renderFlipperDate = function (
    el,
    dateString,
    animate
  ) {
    animate = animate || false;
    if (!el.is(":visible")) {
      el.addClass("flipper-invisible");
      return;
    }
    if (el.hasClass("flipper-invisible")) {
      el.removeClass("flipper-invisible");
      this.resizeFlipper(el);
      this.renderFlipperDate(el, this.options.datetime, false);
    }

    let timestamp,
      now,
      days,
      hours,
      minutes,
      seconds,
      dueDate,
      daysStr,
      hoursStr,
      minutesStr,
      secondsStr,
      addSeconds = 15000;

    // Test countdown
    if (
      dateString === "test5" ||
      dateString === "test15" ||
      dateString === "test70"
    ) {
      addSeconds = 1000 * parseInt(dateString.replace("test", ""), 10);
      dateString = "test";
    }

    if (dateString === "test") {
      // Now
      now = Date.now();
      // Now + 5sec  when  whas initializated
      timestamp = this.settings.time.now + addSeconds;
      dueDate = (timestamp - now) / 1000;
      days = Math.floor(dueDate / 60 / 60 / 24);
      dueDate -= days * 60 * 60 * 24;
      hours = Math.floor(dueDate / 60 / 60);
      dueDate -= hours * 60 * 60;
      minutes = Math.floor(dueDate / 60);
      dueDate -= minutes * 60;
      seconds = Math.floor(dueDate);
    }
    // Clock mode
    else if (dateString === "now" || dateString === "clock") {
      now = new Date();
      seconds = now.getSeconds();
      minutes = now.getMinutes();
      hours = now.getHours();
      days = now.getDate();
    }
    // Countdown
    else {
      now = Date.now();
      timestamp = Date.parse(this.formatDate(dateString));
      dueDate = (timestamp - now) / 1000;

      days = Math.floor(dueDate / 60 / 60 / 24);
      dueDate -= days * 60 * 60 * 24;
      hours = Math.floor(dueDate / 60 / 60);
      dueDate -= hours * 60 * 60;
      minutes = Math.floor(dueDate / 60);
      dueDate -= minutes * 60;
      seconds = Math.floor(dueDate);
    }
    // Reset to zero if timer is to big or under 0
    if (
      days < 0 ||
      (days <= 0 && hours === 0 && minutes === 0 && seconds <= 0) ||
      days > 999 ||
      (days == 999 && hours == 23 && minutes == 59 && seconds == 59)
    ) {
      days = hours = minutes = seconds = 0;
      clear = true;
    }

    daysStr = days.toString().padStart(3, "0");
    hoursStr = hours.toString().padStart(2, "0");
    minutesStr = minutes.toString().padStart(2, "0");
    secondsStr = seconds.toString().padStart(2, "0");

    if (animate) {
      // one section
      this.setDigitAnimateValue(el, "d", 0, days);
      this.setDigitAnimateValue(el, "H", 0, hours);
      this.setDigitAnimateValue(el, "i", 0, minutes);
      this.setDigitAnimateValue(el, "s", 0, seconds);

      // two sections
      this.setDigitAnimateValue(el, "dd", 0, daysStr[1]);
      this.setDigitAnimateValue(el, "dd", 1, daysStr[2]);
      this.setDigitAnimateValue(el, "HH", 0, hoursStr[0]);
      this.setDigitAnimateValue(el, "HH", 1, hoursStr[1]);
      this.setDigitAnimateValue(el, "ii", 0, minutesStr[0]);
      this.setDigitAnimateValue(el, "ii", 1, minutesStr[1]);
      this.setDigitAnimateValue(el, "ss", 0, secondsStr[0]);
      this.setDigitAnimateValue(el, "ss", 1, secondsStr[1]);

      // three sections
      this.setDigitAnimateValue(el, "dd", 0, daysStr[0]);
      this.setDigitAnimateValue(el, "dd", 1, daysStr[1]);
      this.setDigitAnimateValue(el, "dd", 2, daysStr[2]);
    } else {
      el.find(".flipper-group .flipper-digit").removeAttr("data-value");
      el.find(".digit-face.active").removeClass("active");

      // one section
      this.setDigitStaticValue(el, "d", 0, days);
      this.setDigitStaticValue(el, "H", 0, hours);
      this.setDigitStaticValue(el, "i", 0, minutes);
      this.setDigitStaticValue(el, "s", 0, seconds);

      // two sections
      this.setDigitStaticValue(el, "dd", 0, daysStr[1]);
      this.setDigitStaticValue(el, "dd", 1, daysStr[2]);
      this.setDigitStaticValue(el, "HH", 0, hoursStr[0]);
      this.setDigitStaticValue(el, "HH", 1, hoursStr[1]);
      this.setDigitStaticValue(el, "ii", 0, minutesStr[0]);
      this.setDigitStaticValue(el, "ii", 1, minutesStr[1]);
      this.setDigitStaticValue(el, "ss", 0, secondsStr[0]);
      this.setDigitStaticValue(el, "ss", 1, secondsStr[1]);

      // three sections
      this.setDigitStaticValue(el, "ddd", 0, daysStr[0]);
      this.setDigitStaticValue(el, "ddd", 1, daysStr[1]);
      this.setDigitStaticValue(el, "ddd", 2, daysStr[2]);

      this.GUI(el);
    }

    this.setTime("days", days);
    this.setTime("hours", hours);
    this.setTime("minutes", minutes);
    this.setTime("seconds", seconds);
  };

  Flipper_Instance.prototype.getTime = function (key) {
    return this.vars.time[key];
  };

  Flipper_Instance.prototype.getTimers = function () {
    return [
      this.vars.time.days,
      this.vars.time.hours,
      this.vars.time.minutes,
      this.vars.time.seconds,
    ];
  };

  Flipper_Instance.prototype.setTime = function (key, val) {
    this.vars.time[key] = val;
  };

  Flipper_Instance.prototype.startTimer = function (el) {
    var _this = this;

    clearInterval(this.vars.timer);
    this.renderFlipperDate(el, this.options.datetime, false);
    this.vars.timer = setInterval(function () {
      // If timer runs out stop the timer
      if (
        _this.vars.time.days === 0 &&
        _this.vars.time.hours === 0 &&
        _this.vars.time.minutes === 0 &&
        _this.vars.time.seconds === 0
      ) {
        _this.vars.clearTimers = true;
        $(_this.el).attr("data-fc-active", "false");
        // Call the callback function if exists
        let fn = _this.options.callback;

        if ($.isFunction(fn)) {
          if (
            _this.options.useTrigger != null &&
            _this.options.useTrigger === true
          ) {
            fn.call(_this, true);
          }
        }
        // Stop
        clearInterval(_this.vars.timer);
        return;
      }
      _this.renderFlipperDate(el, _this.options.datetime, true);
    }, 1000);
  };

  Flipper_Instance.prototype.formatDate = function (dateString) {
    let a = dateString.replace(/\s{2,}/g, " ").split(" ");

    let d = a[0].split("-");
    let t = a[1].split(":");

    let date = new Date(d[0], d[1] - 1, d[2], t[0], t[1], t[2]);
    return date;
  };

  Flipper_Instance.prototype.destroy = function () {
    clearInterval(this.vars.timer);
    clearInterval(this.vars.timerFlip);

    this.vars = {
      time: {
        days: null,
        hours: null,
        minutes: null,
        seconds: null,
      },
      timer: null,
      timerFlip: null,
      clearTimers: false,
    };

    //this.renderFlipperDate($(this.el), this.options.datetime, false);
    $(this.el).removeAttr("data-fc-id");
    $(this.el).removeData("fc-id");
    $(this.el).removeAttr("data-fc-active");
    $(this.el).removeData("fc-active");
    $(this.el).removeClass("flipper-initialized");
    $(this.el).html("");
  };

  // Class
  var Flipper_Class = function (elements, options) {
    this.elements = elements;
    this.options = options;
    this.foreach();
  };

  Flipper_Class.prototype.getInstance = function (element) {
    var instance;

    var cur_id = $(element).data("fc-id");
    if (typeof cur_id === "undefined") {
      cur_id = guid();
      $(element).attr("data-fc-id", cur_id);
    }
    if (typeof Flipper_Instance_List[cur_id] === "undefined") {
      var options = this.options;
      var element_options = $(element).data("options");

      if (typeof element_options === "string") {
        element_options = JSON.parse(element_options);
      }
      if (typeof element_options === "object") {
        options = $.extend(true, {}, this.options, element_options);
      }
      instance = new Flipper_Instance(element, options);
      Flipper_Instance_List[cur_id] = instance;
    } else {
      instance = Flipper_Instance_List[cur_id];
      if (typeof this.options !== "undefined") {
        instance.setOptions(this.options);
      }
    }

    return instance;
  };

  Flipper_Class.prototype.foreach = function (callback) {
    var _this = this;
    this.elements.each(function () {
      var instance = _this.getInstance(this);
      if (typeof callback === "function") {
        callback(instance);
      }
    });
    return this;
  };

  Flipper_Class.prototype.destroy = function () {
    this.foreach(function (instance) {
      instance.destroy();
    });
    return this;
  };

  Flipper_Class.prototype.rebuild = function () {
    return this.getInstance(this.elements[0]).initialize();
  };

  Flipper_Class.prototype.end = function () {
    return this.elements;
  };

  $.fn.flipTimer = function (options) {
    return new Flipper_Class(this, options);
  };
});