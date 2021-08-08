/**
 * jQuery SMCstylus Circle Timers is a jQuery plugin that provides beautiful animated countdowns in circle shapes.
 *
 * Version: 1.0.0
 * Author: Mihai Calin Simion
 * E-mail: simion.mihai.calin@gmail.com
 * Website: http://www.smcstylus.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 *
 * Features: You can fill the center of circles, the past time lines and the left time lines with solid or gradient colors. There is aslo the possibility to change the cap style of animated line. Add separate shadow (color and diffuse) for main timer and left time lines and more.
 *
 * Please read the documentation for all the options.
 *
 *
 * This software is based on the original TimeCircles by Wim Barelds ( https://github.com/wimbarelds/TimeCircles ).
 *
 **/
(function ($) {
  var useWindow = window;

  // From https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/keys
  if (!Object.keys) {
    Object.keys = (function () {
      "use strict";
      var hasOwnProperty = Object.prototype.hasOwnProperty,
        hasDontEnumBug = !{ toString: null }.propertyIsEnumerable("toString"),
        dontEnums = [
          "toString",
          "toLocaleString",
          "valueOf",
          "hasOwnProperty",
          "isPrototypeOf",
          "propertyIsEnumerable",
          "constructor",
        ],
        dontEnumsLength = dontEnums.length;

      return function (obj) {
        if (
          typeof obj !== "object" &&
          (typeof obj !== "function" || obj === null)
        ) {
          throw new TypeError("Object.keys called on non-object");
        }

        var result = [],
          prop,
          i;

        for (prop in obj) {
          if (hasOwnProperty.call(obj, prop)) {
            result.push(prop);
          }
        }

        if (hasDontEnumBug) {
          for (i = 0; i < dontEnumsLength; i++) {
            if (hasOwnProperty.call(obj, dontEnums[i])) {
              result.push(dontEnums[i]);
            }
          }
        }
        return result;
      };
    })();
  }

  // Used to disable some features on IE8
  var limited_mode = false;
  var tick_duration = 200; // in ms

  var debug = location.hash === "#debug";
  function debug_log(msg) {
    if (debug) {
      console.log(msg);
    }
  }

  var allUnits = ["Days", "Hours", "Minutes", "Seconds"];
  var nextUnits = {
    Seconds: "Minutes",
    Minutes: "Hours",
    Hours: "Days",
    Days: "Years",
  };
  var secondsIn = {
    Seconds: 1,
    Minutes: 60,
    Hours: 3600,
    Days: 86400,
    Months: 2678400,
    Years: 31536000,
  };

  function isCanvasSupported() {
    var elem = document.createElement("canvas");
    return !!(elem.getContext && elem.getContext("2d"));
  }

  function timerUIEffects(ef) {
    var effect = [];
    switch (ef) {
      case "1":
        effect = [1, 1];
        break;
      case "2":
        effect = [1, 5];
        break;
      case "3":
        effect = [5, 1];
        break;
      case "4":
        effect = [1, 5, 5];
        break;
      case "5":
        effect = [3, 3];
        break;
      case "6":
        effect = [10, 10];
        break;
      case "0":
      default:
        effect = [];
        break;
    }
    return effect;
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

  function checkPoint(val, def) {
    var v = parseInt(val);
    return v < 0 || v > 180 ? def : v;
  }

  /**
   * Array.prototype.indexOf fallback for IE8
   * @param {Mixed} mixed
   * @returns {Number}
   */
  if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (elt /*, from*/) {
      var len = this.length >>> 0;

      var from = Number(arguments[1]) || 0;
      from = from < 0 ? Math.ceil(from) : Math.floor(from);
      if (from < 0) from += len;

      for (; from < len; from++) {
        if (from in this && this[from] === elt) return from;
      }
      return -1;
    };
  }

  function parse_date(str) {
    var match = str.match(
      /^[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{1,2}:[0-9]{2}:[0-9]{2}$/
    );
    if (match !== null && match.length > 0) {
      var parts = str.split(" ");
      var date = parts[0].split("-");
      var time = parts[1].split(":");
      return new Date(date[0], date[1] - 1, date[2], time[0], time[1], time[2]);
    }
    // Fallback for different date formats
    var d = Date.parse(str);
    if (!isNaN(d)) return d;
    d = Date.parse(str.replace(/-/g, "/").replace("T", " "));
    if (!isNaN(d)) return d;
    // Cant find anything
    return new Date();
  }

  function parse_times(diff, old_diff, totalDuration, units, floor) {
    var raw_time = {};
    var raw_old_time = {};
    var time = {};
    var pct = {};
    var old_pct = {};
    var old_time = {};

    var greater_unit = null;
    for (var i = 0; i < units.length; i++) {
      var unit = units[i];
      var maxUnits;

      if (greater_unit === null) {
        maxUnits = totalDuration / secondsIn[unit];
      } else {
        maxUnits = secondsIn[greater_unit] / secondsIn[unit];
      }

      var curUnits = diff / secondsIn[unit];
      var oldUnits = old_diff / secondsIn[unit];

      if (floor) {
        if (curUnits > 0) curUnits = Math.floor(curUnits);
        else curUnits = Math.ceil(curUnits);
        if (oldUnits > 0) oldUnits = Math.floor(oldUnits);
        else oldUnits = Math.ceil(oldUnits);
      }

      if (unit !== "Days") {
        curUnits = curUnits % maxUnits;
        oldUnits = oldUnits % maxUnits;
      }

      raw_time[unit] = curUnits;
      time[unit] = Math.abs(curUnits);
      raw_old_time[unit] = oldUnits;
      old_time[unit] = Math.abs(oldUnits);
      pct[unit] = Math.abs(curUnits) / maxUnits;
      old_pct[unit] = Math.abs(oldUnits) / maxUnits;

      greater_unit = unit;
    }

    return {
      raw_time: raw_time,
      raw_old_time: raw_old_time,
      time: time,
      old_time: old_time,
      pct: pct,
      old_pct: old_pct,
    };
  }

  var TC_Instance_List = {};
  function updateUsedWindow() {
    if (typeof useWindow.TC_Instance_List !== "undefined") {
      TC_Instance_List = useWindow.TC_Instance_List;
    } else {
      useWindow.TC_Instance_List = TC_Instance_List;
    }
    initializeAnimationFrameHandler(useWindow);
  }

  function initializeAnimationFrameHandler(w) {
    var vendors = ["webkit", "moz"];
    for (var x = 0; x < vendors.length && !w.requestAnimationFrame; ++x) {
      w.requestAnimationFrame = w[vendors[x] + "RequestAnimationFrame"];
      w.cancelAnimationFrame = w[vendors[x] + "CancelAnimationFrame"];
    }

    if (!w.requestAnimationFrame || !w.cancelAnimationFrame) {
      w.requestAnimationFrame = function (callback, element, instance) {
        if (typeof instance === "undefined")
          instance = { data: { lastFrame: 0 } };
        var currTime = new Date().getTime();
        var timeToCall = Math.max(0, 16 - (currTime - instance.data.lastFrame));
        var id = w.setTimeout(function () {
          callback(currTime + timeToCall);
        }, timeToCall);
        instance.data.lastFrame = currTime + timeToCall;
        return id;
      };
      w.cancelAnimationFrame = function (id) {
        clearTimeout(id);
      };
    }
  }

  var TC_Instance = function (element, options) {
    this.element = element;
    this.container;
    this.listeners = null;
    this.data = {
      paused: false,
      timer: false,
      totalDuration: null,
      prevTime: null,
      lastFrame: 0,
      animationFrame: null,
      intervalFallback: null,
      drawnUnits: [],
      textElements: {
        Days: null,
        Hours: null,
        Minutes: null,
        Seconds: null,
      },
      attributes: {
        gradPoints: [],
        canvas: null,
        context: null,
        itemSize: null,
        lineWidth: null,
        lineCap: null,
        radius: null,
        outerRadius: null,
      },
      state: {
        fading: {
          Days: false,
          Hours: false,
          Minutes: false,
          Seconds: false,
        },
      },
      time: {
        Days: {
          show: false,
        },
        Hours: {
          show: false,
        },
        Minutes: {
          show: false,
        },
        Seconds: {
          show: false,
        },
      },
    };
    this.config = null;
    this.setOptions(options);
    this.initialize();
  };

  TC_Instance.prototype.clearListeners = function () {
    this.listeners = { all: [], visible: [] };
  };

  TC_Instance.prototype.addTime = function (seconds_to_add) {
    if (this.data.attributes.referenceDate instanceof Date) {
      var d = this.data.attributes.referenceDate;
      d.setSeconds(d.getSeconds() + seconds_to_add);
    } else if (!isNaN(this.data.attributes.referenceDate)) {
      this.data.attributes.referenceDate += seconds_to_add * 1000;
    }
  };

  TC_Instance.prototype.initialize = function (clear_listeners) {
    // Initialize drawn units
    this.data.drawnUnits = [];
    for (var i = 0; i < Object.keys(this.config.time).length; i++) {
      var unit = Object.keys(this.config.time)[i];
      //this.data.time[unit].show = this.config.time[unit].show;
      if (this.config.time[unit].show) {
        this.data.drawnUnits.push(unit);
      }
    }

    // Avoid stacking
    $(this.element).children("div.smcstylusCircleTimers").remove();

    if (typeof clear_listeners === "undefined") clear_listeners = true;
    if (clear_listeners || this.listeners === null) {
      this.clearListeners();
    }
    this.container = $("<div>");
    this.container.addClass("smcstylusCircleTimers").appendTo(this.element);
    $(this.element).attr("data-tc-act", "running");
    // Determine the needed width and height of TimeCircles
    var height = this.element.offsetHeight;
    var width = this.element.offsetWidth;
    if (height === 0) height = $(this.element).height();
    if (width === 0) width = $(this.element).width();

    if (height === 0 && width > 0) height = width / this.data.drawnUnits.length;
    else if (width === 0 && height > 0)
      width = height * this.data.drawnUnits.length;

    var gradPoints = this.config.pastTimeLineGradPoints;
    this.data.attributes.gradPoints[0] = checkPoint(gradPoints[0], 0);
    this.data.attributes.gradPoints[1] = checkPoint(gradPoints[1], 100);
    this.data.attributes.gradPoints[2] = checkPoint(gradPoints[2], 0);
    this.data.attributes.gradPoints[3] = checkPoint(gradPoints[3], 0);

    // Create our canvas and set it to the appropriate size
    var canvasElement = document.createElement("canvas");
    canvasElement.width = width;
    canvasElement.height = height;

    // Add canvas elements
    this.data.attributes.canvas = $(canvasElement);
    this.data.attributes.canvas.appendTo(this.container);

    // Check if the browser has browser support
    var canvasSupported = isCanvasSupported();
    // If the browser doesn't have browser support, check if explorer canvas is loaded
    // (A javascript library that adds canvas support to browsers that don't have it)
    if (!canvasSupported && typeof G_vmlCanvasManager !== "undefined") {
      G_vmlCanvasManager.initElement(canvasElement);
      limited_mode = true;
      canvasSupported = true;
    }
    if (canvasSupported) {
      this.data.attributes.context = canvasElement.getContext("2d");
    }

    this.data.attributes.itemSize = Math.min(
      width / this.data.drawnUnits.length,
      height
    );

    this.data.attributes.lineCap = this.config.leftTimeLineCap;
    this.data.attributes.lineWidth =
      this.data.attributes.itemSize * this.config.leftTimeLineWidth;
    this.data.attributes.radius =
      (this.data.attributes.itemSize * 0.8 - this.data.attributes.lineWidth) /
      2;
    this.data.attributes.outerRadius =
      this.data.attributes.radius +
      0.5 *
        Math.max(
          this.data.attributes.lineWidth,
          this.data.attributes.lineWidth * this.config.pastTimeLineWith
        );

    // Prepare Time Elements
    var i = 0;
    for (var key in this.data.textElements) {
      if (!this.config.time[key].show) continue;

      var textElement = $("<div>");
      textElement
        .addClass(
          "smcstylusCircleTimers--inner smcstylusCircleTimers--inner-" + key
        )
        .css("top", Math.round(0.35 * this.data.attributes.itemSize))
        .css("left", Math.round(i++ * this.data.attributes.itemSize))
        .css("width", this.data.attributes.itemSize)
        .appendTo(this.container);

      var headerElement = $("<h4>");
      headerElement
        .addClass("smcstylusCircleTimers--label")
        .text(this.config.time[key].text) // Options
        .css(
          "font-size",
          Math.round(this.config.labelSize * this.data.attributes.itemSize)
        )
        .appendTo(textElement);

      var numberElement = $("<span>");
      numberElement
        .addClass("smcstylusCircleTimers--number")
        .css(
          "font-size",
          Math.round(this.config.numberSize * this.data.attributes.itemSize)
        )
        .appendTo(textElement);

      this.data.textElements[key] = numberElement;
    }

    this.start();
    if (!this.config.start) {
      this.data.paused = true;
    }

    // Set up interval fallback
    var _this = this;
    this.data.intervalFallback = useWindow.setInterval(function () {
      _this.update.call(_this, true);
    }, 100);
  };

  TC_Instance.prototype.update = function (nodraw) {
    if (typeof nodraw === "undefined") {
      nodraw = false;
    } else if (nodraw && this.data.paused) {
      return;
    }

    if (limited_mode) {
      //Per unit clearing doesn't work in IE8 using explorer canvas, so do it in one time. The downside is that radial fade cant be used
      this.data.attributes.context.clearRect(
        0,
        0,
        this.data.attributes.canvas[0].width,
        this.data.attributes.canvas[0].hright
      );
    }
    var diff, old_diff;

    var prevDate = this.data.prevTime;
    var curDate = new Date();
    this.data.prevTime = curDate;

    if (prevDate === null) prevDate = curDate;

    // If not counting past zero, and time < 0, then simply draw the zero point once, and call stop
    if (!this.config.countPastZero) {
      if (curDate > this.data.attributes.referenceDate) {
        for (var i = 0; i < this.data.drawnUnits.length; i++) {
          var key = this.data.drawnUnits[i];

          // Set the text value
          this.data.textElements[key].text("0");
          var x =
            i * this.data.attributes.itemSize +
            this.data.attributes.itemSize / 2;
          var y = this.data.attributes.itemSize / 2;
          var color = this.config.time[key].color;
          this.drawArc(x, y, color, 0, key);
        }
        $(this.element).attr("data-tc-act", "ended");
        this.stop();
        return;
      }
    }

    // Compare current time with reference
    diff = (this.data.attributes.referenceDate - curDate) / 1000;
    old_diff = (this.data.attributes.referenceDate - prevDate) / 1000;

    var floor = this.config.animation !== "smooth";

    var visible_times = parse_times(
      diff,
      old_diff,
      this.data.totalDuration,
      this.data.drawnUnits,
      floor
    );
    var all_times = parse_times(
      diff,
      old_diff,
      secondsIn["Years"],
      allUnits,
      floor
    );

    var i = 0;
    var j = 0;
    var lastKey = null;

    var cur_shown = this.data.drawnUnits.slice();
    for (var i in allUnits) {
      var key = allUnits[i];

      // Notify (all) listeners
      if (
        Math.floor(all_times.raw_time[key]) !==
        Math.floor(all_times.raw_old_time[key])
      ) {
        this.notifyListeners(
          key,
          Math.floor(all_times.time[key]),
          Math.floor(diff),
          "all"
        );
      }

      if (cur_shown.indexOf(key) < 0) continue;

      // Notify (visible) listeners
      if (
        Math.floor(visible_times.raw_time[key]) !==
        Math.floor(visible_times.raw_old_time[key])
      ) {
        this.notifyListeners(
          key,
          Math.floor(visible_times.time[key]),
          Math.floor(diff),
          "visible"
        );
      }

      if (!nodraw) {
        // Set the text value
        var now = Math.floor(Math.abs(visible_times.time[key]));
        this.data.textElements[key].text(now);
        // TODO
        //if (this.config.effects.autoFade === true) {
        //  if (key.toLowerCase() !== "seconds" && now == 0) {
        //    //$(this.data.textElements[key]).parent().fadeOut(500);
        //  }
        //}
        if (this.config.effects.fadeSeconds === true) {
          if (key.toLowerCase() === "seconds") {
            $(this.data.textElements[key]).animate({ opacity: 0 }, 500);
            $(this.data.textElements[key]).animate({ opacity: 1 }, 500);
          }
        }

        var x =
          j * this.data.attributes.itemSize + this.data.attributes.itemSize / 2;
        var y = this.data.attributes.itemSize / 2;
        var color = this.config.time[key].color;

        if (this.config.animation === "smooth") {
          if (lastKey !== null && !limited_mode) {
            if (
              Math.floor(visible_times.time[lastKey]) >
              Math.floor(visible_times.old_time[lastKey])
            ) {
              this.radialFade(x, y, color, 1, key);
              this.data.state.fading[key] = true;
            } else if (
              Math.floor(visible_times.time[lastKey]) <
              Math.floor(visible_times.old_time[lastKey])
            ) {
              this.radialFade(x, y, color, 0, key);
              this.data.state.fading[key] = true;
            }
          }
          if (!this.data.state.fading[key]) {
            this.drawArc(x, y, color, visible_times.pct[key], key);
          }
        } else {
          this.animateArc(
            x,
            y,
            color,
            visible_times.pct[key],
            visible_times.old_pct[key],
            new Date().getTime() + tick_duration,
            key
          );
        }
      }
      lastKey = key;
      j++;
    }

    // Dont request another update if we should be paused
    if (this.data.paused || nodraw) {
      return;
    }

    // We need this for our next frame either way
    var _this = this;
    var update = function () {
      _this.update.call(_this);
    };

    // Either call next update immediately, or in a second
    if (this.config.animation === "smooth") {
      // Smooth animation, Queue up the next frame
      this.data.animationFrame = useWindow.requestAnimationFrame(
        update,
        _this.element,
        _this
      );
    } else {
      // Tick animation, Don't queue until very slightly after the next second happens
      var delay = (diff % 1) * 1000;
      if (delay < 0) delay = 1000 + delay;
      delay += 50;

      _this.data.animationFrame = useWindow.setTimeout(function () {
        _this.data.animationFrame = useWindow.requestAnimationFrame(
          update,
          _this.element,
          _this
        );
      }, delay);
    }
  };

  TC_Instance.prototype.animateArc = function (
    x,
    y,
    color,
    target_pct,
    cur_pct,
    animation_end,
    key
  ) {
    if (this.data.attributes.context === null) return;

    var diff = cur_pct - target_pct;
    if (Math.abs(diff) > 0.5) {
      if (target_pct === 0) {
        this.radialFade(x, y, color, 1, key);
      } else {
        this.radialFade(x, y, color, 0, key);
      }
    } else {
      var progress =
        (tick_duration - (animation_end - new Date().getTime())) /
        tick_duration;
      if (progress > 1) progress = 1;

      var pct = cur_pct * (1 - progress) + target_pct * progress;
      this.drawArc(x, y, color, pct, key);

      //var show_pct =
      if (progress >= 1) return;
      var _this = this;
      useWindow.requestAnimationFrame(function () {
        _this.animateArc(x, y, color, target_pct, cur_pct, animation_end, key);
      }, this.element);
    }
  };

  TC_Instance.prototype.drawArc = function (x, y, color, pct, key) {
    var att = this.data.attributes;
    if (this.data.attributes.context === null) return;

    var clear_radius = Math.max(
      this.data.attributes.outerRadius,
      this.data.attributes.itemSize / 2
    );
    if (!limited_mode) {
      this.data.attributes.context.clearRect(
        x - clear_radius,
        y - clear_radius,
        clear_radius * 2,
        clear_radius * 2
      );
    }
    // Reset
    this.data.attributes.context.lineWidth = 0;
    this.data.attributes.context.setLineDash([]);
    this.data.attributes.context.strokeStyle = "#00000000";
    this.data.attributes.context.shadowBlur = 0;
    this.data.attributes.context.shadowOffsetX = 0;
    this.data.attributes.context.shadowOffsetY = 0;
    this.data.attributes.context.shadowColor = "#00000000";
    this.data.attributes.context.fillStyle = "#00000000";

    if (
      this.config.pastTimeLineShow === true ||
      this.config.innerFill === true
    ) {
      this.data.attributes.context.beginPath();
      this.data.attributes.context.arc(
        x,
        y,
        this.data.attributes.radius,
        0,
        2 * Math.PI,
        false
      );

      if (this.config.innerFill === true) {
        var circleGradient = this.data.attributes.context.createRadialGradient(
          x,
          y,
          parseInt(this.config.innerCenterSize),
          x,
          y,
          this.data.attributes.radius
        );

        circleGradient.addColorStop(0, this.config.innerBackgroundColors[0]);
        if (this.config.innerUseGradient === true) {
          circleGradient.addColorStop(1, this.config.innerBackgroundColors[1]);
        }
        this.data.attributes.context.fillStyle = circleGradient;

        this.data.attributes.context.shadowBlur = this.config.innerShadowBlur;
        this.data.attributes.context.shadowColor = this.config.innerShadowColor;
        this.data.attributes.context.fill();
      }

      // show past tl
      if (this.config.pastTimeLineShow === true) {
        var pastLineGradient, pastLineColor;

        // Reset
        this.data.attributes.context.lineWidth = 0;
        this.data.attributes.context.setLineDash([]);
        this.data.attributes.context.strokeStyle = "#00000000";
        this.data.attributes.context.shadowBlur = 0;
        this.data.attributes.context.shadowOffsetX = 0;
        this.data.attributes.context.shadowOffsetY = 0;
        this.data.attributes.context.shadowColor = "transparent";
        this.data.attributes.context.fillStyle = "#00000000";
        //console.log(this.config.pastTimeLineColors.length);
        if (
          this.config.pastTimeLineUseGradient !== true ||
          this.config.pastTimeLineColors.length === 1
        ) {
          pastLineColor = this.config.pastTimeLineColors[0];
        } else {
          pastLineGradient = this.data.attributes.context.createLinearGradient(
            this.config.pastTimeLineGradPoints[0],
            this.config.pastTimeLineGradPoints[1],
            this.config.pastTimeLineGradPoints[2],
            this.config.pastTimeLineGradPoints[3]
          );
          pastLineGradient.addColorStop(0, this.config.pastTimeLineColors[0]);
          pastLineGradient.addColorStop(1, this.config.pastTimeLineColors[1]);
          pastLineColor = pastLineGradient;
        }

        this.data.attributes.context.lineWidth =
          this.data.attributes.lineWidth * this.config.pastTimeLineWith;
        this.data.attributes.context.strokeStyle = pastLineColor;
        this.data.attributes.context.shadowBlur =
          this.config.pastTimeLineShadowBlur;
        this.data.attributes.context.shadowColor =
          this.config.pastTimeLineShadowColor;
        this.data.attributes.context.shadowOffsetX =
          this.config.pastTimeLineShadowCoordinates[0];
        this.data.attributes.context.shadowOffsetY =
          this.config.pastTimeLineShadowCoordinates[1];
        this.data.attributes.context.stroke();
      }
    }

    // Reset
    this.data.attributes.context.lineWidth = 0;
    this.data.attributes.context.setLineDash([]);
    this.data.attributes.context.strokeStyle = "#00000000";
    this.data.attributes.context.shadowBlur = 0;
    this.data.attributes.context.shadowOffsetX = 0;
    this.data.attributes.context.shadowOffsetY = 0;
    this.data.attributes.context.shadowColor = "#00000000";
    this.data.attributes.context.fillStyle = "#00000000";

    // Direction
    var startAngle, endAngle, counterClockwise;
    var defaultOffset = -0.5 * Math.PI;
    var fullCircle = 2 * Math.PI;
    startAngle = defaultOffset + (this.config.startAngle / 360) * fullCircle;
    var offset = 2 * pct * Math.PI;

    //Draw  left time
    if (this.config.direction.toLowerCase() === "both") {
      counterClockwise = false;
      startAngle -= offset / 2;
      endAngle = startAngle + offset;
    } else {
      if (this.config.direction.toLowerCase() === "clockwise") {
        counterClockwise = false;
        endAngle = startAngle + offset;
      } else {
        counterClockwise = true;
        endAngle = startAngle - offset;
      }
    }

    var leftLineGradient, leftLineColor;
    if (this.config.leftTimeLineIndividualColors === true) {
      // Individual
      if (
        this.config.time[key].gradient === true ||
        this.config.time[key].color.length > 1
      ) {
        leftLineGradient = this.data.attributes.context.createLinearGradient(
          0,
          100,
          0,
          0
        );
        leftLineGradient.addColorStop(0, this.config.time[key].color[0]);
        leftLineGradient.addColorStop(1, this.config.time[key].color[1]);
        leftLineColor = leftLineGradient;
      } else {
        leftLineColor = color[0];
      }
    } else {
      // One for all
      if (this.config.leftTimeLineUseGradient === true) {
        leftLineGradient = this.data.attributes.context.createLinearGradient(
          0,
          100,
          0,
          0
        );
        leftLineGradient.addColorStop(0, this.config.leftTimeLineColors[0]);
        leftLineGradient.addColorStop(1, this.config.leftTimeLineColors[1]);
        leftLineColor = leftLineGradient;
      } else {
        leftLineColor = this.config.leftTimeLineColors[0];
      }
    }

    // Remaining
    this.data.attributes.context.shadowOffsetX = 0;
    this.data.attributes.context.shadowOffsetY = 0;
    this.data.attributes.context.shadowBlur =
      this.config.leftTimeLineShadowBlur;
    this.data.attributes.context.shadowColor =
      this.config.leftTimeLineShadowColor;

    this.data.attributes.context.beginPath();
    this.data.attributes.context.arc(
      x,
      y,
      this.data.attributes.radius,
      startAngle,
      endAngle,
      counterClockwise
    );
    this.data.attributes.context.setLineDash(
      timerUIEffects(this.config.leftTimeLineUIEffect)
    );
    this.data.attributes.context.lineWidth = this.data.attributes.lineWidth;
    this.data.attributes.context.strokeStyle = leftLineColor; //color;
    this.data.attributes.context.lineCap = this.data.attributes.lineCap;
    this.data.attributes.context.stroke();
  };

  TC_Instance.prototype.radialFade = function (x, y, color, from, key) {
    var _this = this; // We have a few inner scopes here that will need access to our instance

    var step = 0.2 * (from === 1 ? -1 : 1);
    var i;
    for (i = 0; from <= 1 && from >= 0; i++) {
      // Create inner scope so our variables are not changed by the time the Timeout triggers
      (function () {
        var delay = 50 * i;

        useWindow.setTimeout(function () {
          _this.drawArc(x, y, color, 1, key);
        }, delay);
      })();
      from += step;
    }
    if (typeof key !== undefined) {
      useWindow.setTimeout(function () {
        _this.data.state.fading[key] = false;
      }, 50 * i);
    }
  };

  TC_Instance.prototype.timeLeft = function () {
    if (this.data.paused && typeof this.data.timer === "number") {
      return this.data.timer;
    }
    var now = new Date();
    return (this.data.attributes.referenceDate - now) / 1000;
  };

  TC_Instance.prototype.start = function () {
    useWindow.cancelAnimationFrame(this.data.animationFrame);
    useWindow.clearTimeout(this.data.animationFrame);

    // Check if a date was passed in html attribute or jquery data
    var attr_data_date = $(this.element).data("date");
    if (typeof attr_data_date === "undefined") {
      attr_data_date = $(this.element).attr("data-date");
    }
    if (typeof attr_data_date === "string") {
      this.data.attributes.referenceDate = parse_date(attr_data_date);
    }
    // Check if this is an unpause of a timer
    else if (typeof this.data.timer === "number") {
      if (this.data.paused) {
        this.data.attributes.referenceDate =
          new Date().getTime() + this.data.timer * 1000;
      }
    } else {
      // Try to get data-timer
      var attr_data_timer = $(this.element).data("timer");
      if (typeof attr_data_timer === "undefined") {
        attr_data_timer = $(this.element).attr("data-timer");
      }
      if (typeof attr_data_timer === "string") {
        attr_data_timer = parseFloat(attr_data_timer);
      }
      if (typeof attr_data_timer === "number") {
        this.data.timer = attr_data_timer;
        this.data.attributes.referenceDate =
          new Date().getTime() + attr_data_timer * 1000;
      } else {
        // data-timer and data-date were both not set
        // use config date
        this.data.attributes.referenceDate = this.config.referenceDate;
      }
    }

    // Start running
    this.data.paused = false;
    this.update.call(this);
  };

  TC_Instance.prototype.restart = function () {
    this.data.timer = false;
    this.start();
  };

  TC_Instance.prototype.stop = function () {
    if (typeof this.data.timer === "number") {
      this.data.timer = this.timeLeft(this);
    }
    // Stop running
    this.data.paused = true;
    useWindow.cancelAnimationFrame(this.data.animationFrame);
  };

  TC_Instance.prototype.destroy = function () {
    this.clearListeners();
    this.stop();
    useWindow.clearInterval(this.data.intervalFallback);
    this.data.intervalFallback = null;

    this.container.remove();
    $(this.element).removeAttr("data-tc-id");
    $(this.element).removeData("tc-id");
  };

  TC_Instance.prototype.setOptions = function (options) {
    if (this.config === null) {
      this.default_options.referenceDate = new Date();
      this.config = $.extend(true, {}, this.default_options);
    }
    $.extend(true, this.config, options);
    // Use window.top if useTopFrame is true
    if (this.config.useTopFrame) {
      useWindow = window.top;
    } else {
      useWindow = window;
    }
    updateUsedWindow();

    this.data.totalDuration = this.config.totalDuration;
    if (typeof this.data.totalDuration === "string") {
      if (typeof secondsIn[this.data.totalDuration] !== "undefined") {
        // If set to Years, Months, Days, Hours or Minutes, fetch the secondsIn value for that
        this.data.totalDuration = secondsIn[this.data.totalDuration];
      } else if (this.data.totalDuration === "Auto") {
        // If set to auto, totalDuration is the size of 1 unit, of the unit type bigger than the largest shown
        for (var i = 0; i < Object.keys(this.config.time).length; i++) {
          var unit = Object.keys(this.config.time)[i];
          if (this.config.time[unit].show) {
            this.data.totalDuration = secondsIn[nextUnits[unit]];
            break;
          }
        }
      } else {
        // If it's a string, but neither of the above, user screwed up.
        this.data.totalDuration = secondsIn["Years"];
        console.error(
          "Valid values for SMCstylus Circle Timers config.totalDuration are: either numeric, or (string) Years, Months, Days, Hours, Minutes, Auto"
        );
      }
    }
  };

  TC_Instance.prototype.addListener = function (f, context, type) {
    if (typeof f !== "function") return;
    if (typeof type === "undefined") type = "visible";
    this.listeners[type].push({ func: f, scope: context });
  };

  TC_Instance.prototype.notifyListeners = function (unit, value, total, type) {
    for (var i = 0; i < this.listeners[type].length; i++) {
      var listener = this.listeners[type][i];
      listener.func.apply(listener.scope, [unit, value, total]);
    }
  };

  TC_Instance.prototype.default_options = {
    referenceDate: new Date(), //
    countPastZero: false, //
    useTopFrame: false, //
    labelSize: 0.07, //
    numberSize: 0.28, //
    start: true, //
    animation: "smooth", //
    direction: "Clockwise", //
    totalDuration: "Auto", //
    startAngle: 0, //
    innerFill: false, //
    innerUseGradient: false, //
    innerBackgroundColors: ["#f6008b11", "transparent"],
    innerCenterSize: 5, // %
    innerShadowBlur: 0,
    innerShadowColor: "#55cc00",
    pastTimeLineShow: true, // true, false
    pastTimeLineWith: 1.2, //
    pastTimeLineUseGradient: true, //
    pastTimeLineColors: ["#ffcc33", "#f6008b44"], //
    pastTimeLineGradPoints: [60, 180, 0, 0],
    pastTimeLineShadowBlur: 10,
    pastTimeLineShadowColor: "rgba(0,0,0, 0.5)",
    pastTimeLineShadowCoordinates: [0, 0],
    leftTimeLineWidth: 0.1, //
    leftTimeLineCap: "round",
    leftTimeLineUseGradient: true, //
    leftTimeLineColors: ["#f6008b", "#cc55ff"], //
    leftTimeLineIndividualColors: false,
    leftTimeLineShadowBlur: 10,
    leftTimeLineShadowColor: "red",
    leftTimeLineShadowCoordinates: [0, 0],
    leftTimeLineUIEffect: 0,
    effects: {
      fadeSeconds: false,
      autoFade: false,
    },
    time: {
      Days: {
        show: true,
        text: "Days",
        color: ["#f6008b"],
        gradient: true,
      },
      Hours: {
        show: true,
        text: "Hours",
        color: ["#f6008b"],
        gradient: true,
      },
      Minutes: {
        show: true,
        text: "Minutes",
        color: ["#f6008b"], //, "#cc55ff"
        gradient: true,
      },
      Seconds: {
        show: true,
        text: "Seconds",
        color: ["#f6008b"],
        gradient: true,
      },
    },
  };

  // Time circle class
  var TC_Class = function (elements, options) {
    this.elements = elements;
    this.options = options;
    this.foreach();
  };

  TC_Class.prototype.getInstance = function (element) {
    var instance;

    var cur_id = $(element).data("tc-id");
    if (typeof cur_id === "undefined") {
      cur_id = guid();
      $(element).attr("data-tc-id", cur_id);
    }
    if (typeof TC_Instance_List[cur_id] === "undefined") {
      var options = this.options;
      var element_options = $(element).data("options");

      if (typeof element_options === "string") {
        element_options = JSON.parse(element_options);
      }
      if (typeof element_options === "object") {
        options = $.extend(true, {}, this.options, element_options);
      }
      instance = new TC_Instance(element, options);
      TC_Instance_List[cur_id] = instance;
    } else {
      instance = TC_Instance_List[cur_id];
      if (typeof this.options !== "undefined") {
        instance.setOptions(this.options);
      }
    }

    return instance;
  };

  TC_Class.prototype.addTime = function (seconds_to_add) {
    this.foreach(function (instance) {
      instance.addTime(seconds_to_add);
    });
  };

  TC_Class.prototype.foreach = function (callback) {
    var _this = this;
    this.elements.each(function () {
      var instance = _this.getInstance(this);
      if (typeof callback === "function") {
        callback(instance);
      }
    });
    return this;
  };

  TC_Class.prototype.start = function () {
    this.foreach(function (instance) {
      instance.start();
    });
    return this;
  };

  TC_Class.prototype.stop = function () {
    this.foreach(function (instance) {
      instance.stop();
    });
    return this;
  };

  TC_Class.prototype.restart = function () {
    this.foreach(function (instance) {
      instance.restart();
    });
    return this;
  };

  TC_Class.prototype.rebuild = function () {
    this.foreach(function (instance) {
      instance.initialize(false);
    });
    return this;
  };

  TC_Class.prototype.getTime = function () {
    return this.getInstance(this.elements[0]).timeLeft();
  };

  TC_Class.prototype.addListener = function (f, type) {
    if (typeof type === "undefined") type = "visible";
    var _this = this;
    this.foreach(function (instance) {
      instance.addListener(f, _this.elements, type);
    });
    return this;
  };

  TC_Class.prototype.destroy = function () {
    this.foreach(function (instance) {
      instance.destroy();
    });
    return this;
  };

  TC_Class.prototype.end = function () {
    return this.elements;
  };

  $.fn.smcstylusCircleTimers = function (options) {
    return new TC_Class(this, options);
  };
})(jQuery);
