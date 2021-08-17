(function ($) {
  "use strict";

  /**
   * [SMCstylus Countdown Widget v.1.0.0]
   * @param {[string]} $scope
   * @package: SMCstylus Addons For Elementor
   */
  let smcstylusCountdown = ($scope, $) => {
    let countdown_elem = $scope.find(".smc-addel-countdown").eq(0);

    if (countdown_elem.length > 0) {
      let widget_options = countdown_elem.data("countdown");
      //console.log(widget_options);
      switch (widget_options.style) {
        // Circle countdown
        default:
        case "circle":
          let dayLabel = widget_options.customlabels.days,
            hourLabel = widget_options.customlabels.hours,
            minuteLabel = widget_options.customlabels.minutes,
            secondLabel = widget_options.customlabels.seconds;

          if (widget_options.status.hideLabels === true) {
            dayLabel = hourLabel = minuteLabel = secondLabel = "";
          }

          let circleTimerArgs = {
            animation: widget_options.animation.style,
            direction: widget_options.animation.direction,
            labelSize: widget_options.labelSize / 100,
            numberSize: widget_options.numberSize / 100,
            innerFill: widget_options.inner.fill,
            innerUseGradient: widget_options.inner.useGradient,
            innerBackgroundColors: widget_options.inner.colors,
            innerCenterSize: widget_options.inner.centerSize,
            innerShadowBlur: widget_options.inner.shadowBlur,
            innerShadowColor: widget_options.inner.shadowColor,
            pastTimeLineShow: widget_options.pastTime.showLines,
            pastTimeLineWith: widget_options.pastTime.lineWidth,
            pastTimeLineUseGradient: widget_options.pastTime.useGradient, //
            pastTimeLineColors: widget_options.pastTime.lineColors,
            pastTimeLineGradPoints: widget_options.pastTime.gradientPoints,
            pastTimeLineShadowBlur: widget_options.pastTime.shadowBlur,
            pastTimeLineShadowColor: widget_options.pastTime.shadowColor,
            pastTimeLineShadowCoordinates:
              widget_options.pastTime.shadowCoordinates,
            leftTimeLineWidth: widget_options.leftTime.lineWidth,
            leftTimeLineCap: widget_options.leftTime.linesCap,
            leftTimeLineUseGradient: widget_options.leftTime.useGradient,
            leftTimeLineColors: widget_options.leftTime.lineColors,
            leftTimeLineIndividualColors:
              widget_options.leftTime.useEachLineColor,
            leftTimeLineShadowBlur: widget_options.leftTime.shadowBlur,
            leftTimeLineShadowColor: widget_options.leftTime.shadowColor,
            leftTimeLineShadowCoordinates:
              widget_options.leftTime.shadowCoordinates,
            leftTimeLineUIEffect: widget_options.leftTime.UIeffect,
            time: {
              Days: {
                show: widget_options.status.showDays,
                text: dayLabel,
                color: widget_options.leftTime.daysColors,
                gradient: widget_options.leftTime.daysGrads,
              },
              Hours: {
                show: widget_options.status.showHours,
                text: hourLabel,
                color: widget_options.leftTime.hoursColors,
                gradient: widget_options.leftTime.hoursGrads,
              },
              Minutes: {
                show: widget_options.status.showMinutes,
                text: minuteLabel,
                color: widget_options.leftTime.minutesColors,
                gradient: widget_options.leftTime.minutesGrads,
              },
              Seconds: {
                show: widget_options.status.showSeconds,
                text: secondLabel,
                color: widget_options.leftTime.secondsColors,
                gradient: widget_options.leftTime.secondsGrads,
              },
            },
          };

          // Init and add listener
          countdown_elem
            .smcstylusCircleTimers(circleTimerArgs)
            .addListener(countdownCompleteListener);

          // Actions function
          function countdownComplete(total, el) {
            let widget_options = $(el).data("countdown"),
              e = $(el).find(".smcstylusCircleTimers").eq(0);

            if (total <= 0) {
              displayMessage(el, e, widget_options);
            }
          }

          // Action on live view
          function countdownCompleteListener(unit, value, total) {
            countdownComplete(total, this);
          }

          // Action on document load
          $(document).ready(() => {
            if (countdown_elem.data("tc-act") === "ended") {
              let el = countdown_elem.smcstylusCircleTimers(circleTimerArgs);
              countdownComplete(0, el.elements[0]);
            }
          });

          // Rebuild on resize
          $(window).resize(() => {
            countdown_elem.smcstylusCircleTimers().rebuild();
          });
          break;

        // Flipper countdown
        case "flipper":
          $(document).ready(function () {
            countdown_elem.append(
              `<div class="smcFlipperTimers flipper"></div>`
            );

            let flipper = countdown_elem.find(".smcFlipperTimers").eq(0);

            let dueDate =
              widget_options.timerType === "dueDate"
                ? countdown_elem.data("date")
                : widget_options.timerType;

            let flipperTimersArgs = {
              datetime: dueDate,
              time: {
                days: {
                  show: widget_options.status.showDays,
                  label: widget_options.customlabels.days,
                  template: widget_options.timeTemplate.days,
                },
                hours: {
                  show: widget_options.status.showHours,
                  label: widget_options.customlabels.hours,
                  template: widget_options.timeTemplate.hours,
                },
                minutes: {
                  show: widget_options.status.showMinutes,
                  label: widget_options.customlabels.minutes,
                  template: widget_options.timeTemplate.minutes,
                },
                seconds: {
                  show: widget_options.status.showSeconds,
                  label: widget_options.customlabels.seconds,
                  template: widget_options.timeTemplate.seconds,
                },
              },
              showLabels: widget_options.status.hideLabels !== true,
              useTrigger: true,
              callback: function (trigger) {
                let e = flipper,
                  el = e.parent();
                if (trigger === true) {
                  displayMessage(el, e, widget_options);
                }
              },
            };
            // Draw countdown widget
            flipper.flipTimer(flipperTimersArgs);
            // Add template
            flipper.addClass(`flipper-${widget_options.cssTemplate}`);
          });
          break;

        // Normal countdown
        case "flat":
          countdown_elem
            .countdown(widget_options.due_date["date"], (e) => {
              let due_date, days, hours, minutes, seconds;

              if (widget_options.status.hideLabels === true) {
                days = flat_timeTpl("days", "D", "", true);
                hours = flat_timeTpl("hours", "H", "", true);
                minutes = flat_timeTpl("minutes", "M", "", true);
                seconds = flat_timeTpl("seconds", "S", "", true);
              } else {
                days = flat_timeTpl(
                  "days",
                  "D",
                  widget_options.customlabels.days
                );
                hours = flat_timeTpl(
                  "hours",
                  "H",
                  widget_options.customlabels.hours
                );
                minutes = flat_timeTpl(
                  "minutes",
                  "M",
                  widget_options.customlabels.minutes
                );
                seconds = flat_timeTpl(
                  "seconds",
                  "S",
                  widget_options.customlabels.seconds
                );
              }

              // Generate date
              if (widget_options.status.showDays === false) {
                days = "";
              }
              if (widget_options.status.showHours === false) {
                hours = "";
              }
              if (widget_options.status.showMinutes === false) {
                minutes = "";
              }
              if (widget_options.status.showSeconds === false) {
                seconds = "";
              }
              due_date = days + hours + minutes + seconds;

              // Initiate Countdown
              countdown_elem.html(e.strftime(due_date));
            })
            .on("finish.countdown", function (event) {
              let widget_options = $(this).data("countdown"),
                e = $(this),
                el = $(this).parent();
              displayMessage(el, e, widget_options);
            });
          break;
      }
    }
  };

  // Print message
  function displayMessage(el, e, opt) {
    switch (opt.due_date.action) {
      case "redirect":
        window.location.href = opt.due_date.redirect;
        break;
      case "hide":
        e.fadeOut("slow", function () {
          $(this).hide().css({ display: "none" });
        });
        break;
      case "message_keep":
        $(el).after(opt.due_date.message);
        break;
      case "message_hide":
        e.fadeOut("slow", function () {
          $(this).css({ display: "none" });
        });
        $(el).append(opt.due_date.message);
        break;
      case "donothing":
      default:
        // do nothing here :)))
        break;
    }
  }

  // Print flat timer HTML
  const flat_timeTpl = (
    type = "seconds",
    time = "S",
    label = "",
    hideLabel = false
  ) => {
    let isLabel =
      hideLabel === true
        ? ""
        : `<span class="smc-addel-countdown--timer-label">${label}</span>`;
    return `<div class="smc-addel-countdown--timer ${type}"><span class="smc-addel-countdown--timer-time">%${time}</span>${isLabel}</div>`;
  };
  /*
   * Run this code under Elementor.
   */
  $(window).on("elementor/frontend/init", () => {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/countdown.default",
      smcstylusCountdown
    );
  });
})(jQuery);
