/**
* @license
The Final Countdown for jQuery v2.2.0 (http://hilios.github.io/jQuery.countdown/)
Copyright (c) 2016 Edson Hilios

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
"use strict";
!(function (factory) {
  if ("function" == typeof define && define.amd) {
    define(["jquery"], factory);
  } else {
    factory(jQuery);
  }
})(function ($) {
  function parseDateString(value) {
    if (value instanceof Date) {
      return value;
    }
    if (String(value).match(rx)) {
      return (
        String(value).match(/^[0-9]*$/) && (value = Number(value)),
        String(value).match(/\-/) &&
          (value = String(value).replace(/\-/g, "/")),
        new Date(value)
      );
    }
    throw new Error("Couldn't cast `" + value + "` to a date object.");
  }
  function escapedRegExp(str) {
    var expressionRegexSource = str
      .toString()
      .replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1");
    return new RegExp(expressionRegexSource);
  }
  function strftime(offsetObject) {
    return function (expression) {
      var rules = expression.match(/%(-|!)?[A-Z]{1}(:[^;]+;)?/gi);
      if (rules) {
        var i = 0;
        var rulesCount = rules.length;
        for (; i < rulesCount; ++i) {
          var directive = rules[i].match(/%(-|!)?([a-zA-Z]{1})(:[^;]+;)?/);
          var regexp = escapedRegExp(directive[0]);
          var to = directive[1] || "";
          var unit = directive[3] || "";
          var value = null;
          directive = directive[2];
          if (DIRECTIVE_KEY_MAP.hasOwnProperty(directive)) {
            value = DIRECTIVE_KEY_MAP[directive];
            value = Number(offsetObject[value]);
          }
          if (null !== value) {
            if ("!" === to) {
              value = pluralize(unit, value);
            }
            if ("" === to && value < 10) {
              value = "0" + value.toString();
            }
            expression = expression.replace(regexp, value.toString());
          }
        }
      }
      return (expression = expression.replace(/%%/, "%"));
    };
  }
  function pluralize(item, num) {
    var i = "s";
    var c = "";
    return (
      item &&
        ((item = item.replace(/(:|;|\s)/gi, "").split(/,/)),
        1 === item.length ? (i = item[0]) : ((c = item[0]), (i = item[1]))),
      Math.abs(num) > 1 ? i : c
    );
  }
  var instances = [];
  var rx = [];
  var defaultOptions = {
    precision: 100,
    elapse: false,
    defer: false,
  };
  rx.push(/^[0-9]*$/.source);
  rx.push(/([0-9]{1,2}\/){2}[0-9]{4}( [0-9]{1,2}(:[0-9]{2}){2})?/.source);
  rx.push(/[0-9]{4}([\/\-][0-9]{1,2}){2}( [0-9]{1,2}(:[0-9]{2}){2})?/.source);
  rx = new RegExp(rx.join("|"));
  var DIRECTIVE_KEY_MAP = {
    Y: "years",
    m: "months",
    n: "daysToMonth",
    d: "daysToWeek",
    w: "weeks",
    W: "weeksToMonth",
    H: "hours",
    M: "minutes",
    S: "seconds",
    D: "totalDays",
    I: "totalHours",
    N: "totalMinutes",
    T: "totalSeconds",
  };
  var Countdown = function Countdown(el, finalDate, options) {
    this.el = el;
    this.$el = $(el);
    this.interval = null;
    this.offset = {};
    this.options = $.extend({}, defaultOptions);
    this.firstTick = true;
    this.instanceNumber = instances.length;
    instances.push(this);
    this.$el.data("countdown-instance", this.instanceNumber);
    if (options) {
      if ("function" == typeof options) {
        this.$el.on("update.countdown", options);
        this.$el.on("stoped.countdown", options);
        this.$el.on("finish.countdown", options);
      } else {
        this.options = $.extend({}, defaultOptions, options);
      }
    }
    this.setFinalDate(finalDate);
    if (this.options.defer === false) {
      this.start();
    }
  };
  $.extend(Countdown.prototype, {
    start: function update() {
      if (null !== this.interval) {
        clearInterval(this.interval);
      }
      var a = this;
      this.update();
      this.interval = setInterval(function () {
        a.update.call(a);
      }, this.options.precision);
    },
    stop: function stop() {
      clearInterval(this.interval);
      this.interval = null;
      this.dispatchEvent("stoped");
    },
    toggle: function updateInfoCard() {
      if (this.interval) {
        this.stop();
      } else {
        this.start();
      }
    },
    pause: function handle_compare() {
      this.stop();
    },
    resume: function attachDnode() {
      this.start();
    },
    remove: function Countdown() {
      this.stop.call(this);
      instances[this.instanceNumber] = null;
      delete this.$el.data().countdownInstance;
    },
    setFinalDate: function setFinalDate(value) {
      this.finalDate = parseDateString(value);
    },
    update: function next() {
      if (0 === this.$el.closest("html").length) {
        return void this.remove();
      }
      var newTotalSecsLeft;
      var now = new Date();
      return (
        (newTotalSecsLeft = this.finalDate.getTime() - now.getTime()),
        (newTotalSecsLeft = Math.ceil(newTotalSecsLeft / 1e3)),
        (newTotalSecsLeft =
          !this.options.elapse && newTotalSecsLeft < 0
            ? 0
            : Math.abs(newTotalSecsLeft)),
        this.totalSecsLeft === newTotalSecsLeft || this.firstTick
          ? void (this.firstTick = false)
          : ((this.totalSecsLeft = newTotalSecsLeft),
            (this.elapsed = now >= this.finalDate),
            (this.offset = {
              seconds: this.totalSecsLeft % 60,
              minutes: Math.floor(this.totalSecsLeft / 60) % 60,
              hours: Math.floor(this.totalSecsLeft / 60 / 60) % 24,
              days: Math.floor(this.totalSecsLeft / 60 / 60 / 24) % 7,
              daysToWeek: Math.floor(this.totalSecsLeft / 60 / 60 / 24) % 7,
              daysToMonth: Math.floor(
                (this.totalSecsLeft / 60 / 60 / 24) % 30.4368
              ),
              weeks: Math.floor(this.totalSecsLeft / 60 / 60 / 24 / 7),
              weeksToMonth:
                Math.floor(this.totalSecsLeft / 60 / 60 / 24 / 7) % 4,
              months: Math.floor(this.totalSecsLeft / 60 / 60 / 24 / 30.4368),
              years: Math.abs(this.finalDate.getFullYear() - now.getFullYear()),
              totalDays: Math.floor(this.totalSecsLeft / 60 / 60 / 24),
              totalHours: Math.floor(this.totalSecsLeft / 60 / 60),
              totalMinutes: Math.floor(this.totalSecsLeft / 60),
              totalSeconds: this.totalSecsLeft,
            }),
            void (this.options.elapse || 0 !== this.totalSecsLeft
              ? this.dispatchEvent("update")
              : (this.stop(), this.dispatchEvent("finish"))))
      );
    },
    dispatchEvent: function next(eventName) {
      var event = $.Event(eventName + ".countdown");
      event.finalDate = this.finalDate;
      event.elapsed = this.elapsed;
      event.offset = $.extend({}, this.offset);
      event.strftime = strftime(this.offset);
      this.$el.trigger(event);
    },
  });
  $.fn.countdown = function () {
    var argumentsArray = Array.prototype.slice.call(arguments, 0);
    return this.each(function () {
      var instanceNumber = $(this).data("countdown-instance");
      if (void 0 !== instanceNumber) {
        var instance = instances[instanceNumber];
        var method = argumentsArray[0];
        if (Countdown.prototype.hasOwnProperty(method)) {
          instance[method].apply(instance, argumentsArray.slice(1));
        } else {
          if (null === String(method).match(/^[$A-Z_][0-9A-Z_$]*$/i)) {
            instance.setFinalDate.call(instance, method);
            instance.start();
          } else {
            $.error(
              "Method %s does not exist on jQuery.countdown".replace(
                /%s/gi,
                method
              )
            );
          }
        }
      } else {
        new Countdown(this, argumentsArray[0], argumentsArray[1]);
      }
    });
  };
});
