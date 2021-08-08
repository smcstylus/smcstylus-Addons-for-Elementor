/**
 * Custom JS extension
 */
(function ($) {
  "use strict";
  const custom_js_addScript = (id, js) => {
    let el = document.getElementById(id);
    if (el) el.remove();
    let s = document.createElement("script");
    s.setAttribute("id", id);
    s.appendChild(document.createTextNode(js));
    document.body.appendChild(s);
  };
  $(window).load(function () {
    elementor.channels.editor.on("SMC_runJS", () => {
      var custom_js = elementor.settings.page.model.get(
        "smcstylus_addel_custom_js_code"
      );
      custom_js_addScript("id", custom_js);
      //elementor.reloadPreview();
      //console.log(custom_js);
    });
  });
})(jQuery);
