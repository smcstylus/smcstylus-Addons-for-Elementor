/**
 * Custom CSS extension
 */
(function ($) {
  "use strict";
  $(window).load(function () {
    //const add_PageCustomCss = function () {
    var add_PageCustomCss = () => {
      let custom_css = elementor.settings.page.model.get(
        localize.smc_extension.custom_css_slug + "custom_css_code"
      );

      if (custom_css) {
        custom_css = custom_css.replace(
          /widget/g,
          ".elementor-page-" + elementor.config.document.id
        );
        elementor.settings.page.controlsCSS.elements.$stylesheetElement.append(
          custom_css
        );
      }
    };

    //const add_CustomCss = function (css, context) {
    var add_CustomCss = (css, context) => {
      if (!context) {
        return;
      }

      let model = context.model,
        custom_css = model
          .get("settings")
          .get(localize.smc_extension.custom_css_slug + "custom_css_code");
      let widgetID = ".elementor-element.elementor-element-" + model.get("id");

      if ("document" === model.get("elType")) {
        widgetID = elementor.config.document.settings.cssWrapperSelector;
      }

      if (custom_css) {
        css += custom_css.replace(/widget/g, widgetID);
      }
      return css;
    };

    elementor.hooks.addFilter("editor/style/styleText", add_CustomCss);
    elementor.settings.page.model.on("change", add_PageCustomCss);
    elementor.on("preview:loaded", add_PageCustomCss);
  });
})(jQuery);
