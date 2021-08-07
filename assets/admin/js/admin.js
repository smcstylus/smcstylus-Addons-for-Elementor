(function ($) {
  "use strict";
  // Some constants
  const admin_elements = {
    save_btn: $(".js-smc-addel-settings-save"),
    addons_tag:
      ".smc-addel-checkbox-container .smc-addel-checkbox.smc-addel-checkbox-addons input:enabled",
    spiner: `<svg id="smc-addel-spinner" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48"><circle cx="24" cy="4" r="4" fill="#fff"/><circle cx="12.19" cy="7.86" r="3.7" fill="#fffbf2"/><circle cx="5.02" cy="17.68" r="3.4" fill="#fef7e4"/><circle cx="5.02" cy="30.32" r="3.1" fill="#fef3d7"/><circle cx="12.19" cy="40.14" r="2.8" fill="#feefc9"/><circle cx="24" cy="44" r="2.5" fill="#feebbc"/><circle cx="35.81" cy="40.14" r="2.2" fill="#fde7af"/><circle cx="42.98" cy="30.32" r="1.9" fill="#fde3a1"/><circle cx="42.98" cy="17.68" r="1.6" fill="#fddf94"/><circle cx="35.81" cy="7.86" r="1.3" fill="#fcdb86"/></svg><span>${localize.i18n.smc_admin.save_data}</span>`,
  };
  const smcAddEl_UiSaveButton = () => {
    admin_elements["save_btn"]
      .addClass("save-now")
      .removeAttr("disabled")
      .css("cursor", "pointer");
  };

  const smcAddEl_UiError = (err = "") => {
    Swal.fire({
      type: "error",
      title: localize.i18n.smc_admin.err_title,
      text: localize.i18n.smc_admin.err_text,
    });
  };
  // Check all checkboxes status
  const checks_all_on = () => {
    let items = 0;
    $(admin_elements["addons_tag"]).each(function () {
      items += 1;
      if ($(this).prop("checked") !== true) items += -1;
    });
    return items > 0 ? true : false;
  };

  // Admin page tabs
  $(".smc-addel-tabs li a").on("click", function (e) {
    e.preventDefault();
    let el = $(this);
    $(".smc-addel-tabs li a").removeClass("active");
    el.addClass("active");
    let tab = el.attr("href");
    $(".smc-addel-settings-tab").removeClass("active");
    $(".smc-addel-settings-tabs").find(tab).addClass("active");
  });

  // Saving Data With Ajax Request
  admin_elements["save_btn"].on("click", function (e) {
    e.preventDefault();
    let el = $(this);

    if (el.hasClass("save-now")) {
      $.ajax({
        url: localize.ajaxurl,
        type: "post",
        data: {
          action: "save_settings_with_ajax",
          smc_nonce: localize.nonce,
          fields: $("form#smc-addel-settings").serialize(),
        },
        beforeSend: function () {
          el.html(admin_elements["spiner"]);
        },
        success: function (response) {
          console.log(response);
          setTimeout(function () {
            el.html(localize.i18n.smc_admin.save_settings);
            Swal.fire({
              type: "success",
              title: localize.i18n.smc_admin.saved_settings,
              footer: localize.i18n.smc_admin.footer_save_msg,
              showConfirmButton: false,
              timer: 2000,
            });
            el.toggleClass("save-now");
          }, 500);
        },
        error: function (err) {
          Swal.fire({
            type: "error",
            title: localize.i18n.smc_admin.err_title,
            text: localize.i18n.smc_admin.err_text,
          });

          //console.log(JSON.stringify(err));
        }, //smcAddEl_UiError(),
      });
    } else {
      el.attr("disabled", "true").css("cursor", "not-allowed");
    }
  });

  // Addons global control
  $(document).on("click", "#toogle_all_addons", function (e) {
    let check_all = $(this).prop("checked") === true ? true : false;
    $(admin_elements["addons_tag"]).each(function () {
      if ($(this).attr("id") != "toogle_all_addons")
        $(this).prop("checked", check_all).change();
    });
    smcAddEl_UiSaveButton();
  });

  // Addons individual control
  $(document).on("click", admin_elements["addons_tag"], function (e) {
    // Check all checkboxes status
    let checks = checks_all_on();
    // Change global checkbox status
    $("#toogle_all_addons").prop("checked", checks).change();

    smcAddEl_UiSaveButton();
  });

  // Popup
  $(document).on("click", ".smc-addel-admin-settings-popup", function (e) {
    let el = $(this);
    e.preventDefault();

    let title = el.data("title"),
      inputPlaceholder = el.data("placeholder"),
      type = el.data("option") || "text",
      options = el.data("options") || {},
      inputOptions = {},
      target = el.data("target"),
      val = $(target).val(),
      docSelector = el.data("doc"),
      footer = docSelector
        ? $(docSelector).clone().css("display", "block")
        : false;

    if (Object.keys(options).length > 0) {
      inputOptions["all"] = localize.i18n.smc_admin.all;

      for (let index in options) {
        inputOptions[index] = options[index].toUpperCase();
      }
    }

    Swal.fire({
      title: title,
      input: type,
      inputPlaceholder: inputPlaceholder,
      inputValue: val,
      inputOptions: inputOptions,
      footer: footer,
      preConfirm: function (res) {
        $(target).val(res);

        smcAddEl_UiSaveButton();
      },
    });
  });
})(jQuery);
