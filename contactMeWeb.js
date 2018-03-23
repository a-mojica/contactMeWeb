/*!
 * contactMeWeb.js
 * Copyright (c) 2018 Antonio Mojica
 * Licensed under the MIT license - http://opensource.org/licenses/MIT
 * https://amojica.ch
 */
$(function () {
    'use strict';
    window.addEventListener("load", function () {
        var forms = document.getElementsByClassName("needs-validation");
        var validation = Array.prototype.filter.call(forms, function (form) {
            form.addEventListener("submit", function (event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add("was-validated");
            }, false);
        });
    }, false);

    $("#i-recaptcha").validator(); // init validator
    $("#i-recaptcha").on("submit", function (e) {
        if (!e.isDefaultPrevented()) {
            $("#contactMeWebSubmit").prop("disabled", true);

            var url = "contactMeWeb.php";

            $.ajax({
                type: "POST",
                url: url,
                data: $(this).serialize(),
                success: function (data) {
                    var alert = "alert-" + data.type;
                    var text = data.message;
                    var notice = '<div class="alert ' + alert + ' alert-dismissable centering contactMeWeb__alert"><button type="button" class="close contactMeWeb__alert__close" data-dismiss="alert" aria-hidden="true">&times;</button>' + text + "</div>";

                    if (alert && text) {
                        $("#i-recaptcha").find("#contactMeWebNotification").html(notice);
                        $("#i-recaptcha")[0].reset();
                        grecaptcha.reset();
                        $("#contactMeWebSubmit").prop("disabled", false);
                    }
                }
            });
            return false;
        }
    });
});