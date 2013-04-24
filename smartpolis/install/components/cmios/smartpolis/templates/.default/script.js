/**
 * Компонент Умный Полис
 *
 */


$(function () {
    var getRequestUrl = function () {
        return $("#ajax_url").val();
    };

    var updateField = function (select, args, after) {
        select.children().remove();
        $.getJSON(
            getRequestUrl(),
            args,
            function (r) {
                for (var i in r) {
                    $("<option value='" + r[i].id + "'>" + r[i].title + "</option>").appendTo(select);
                }
                if (typeof(after) == "function")
                    after();
            }
        );
    };

    var updateModels = function () {
        $("#smartpolis_car_models, #smartpolis_car_modifications").attr("disabled", "disabled");
        updateField(
            $("#smartpolis_car_models"),
            {
                "ajax": "Y",
                "type": "models",
                "brand": $("#smartpolis_car_marks").val()
            },
            updateModifications
        );
    };

    var updateModifications = function () {
        var models = $("#smartpolis_car_models");
        if (models.children().length)
            models.attr("disabled", null);
        $("#smartpolis_car_modifications").attr("disabled", "disabled");
        updateField(
            $("#smartpolis_car_modifications"),
            {
                "ajax": "Y",
                "type": "modifications",
                "brand": $("#smartpolis_car_marks").val(),
                "model": $("#smartpolis_car_models").val()
            },
            function () {
                var modifications = $("#smartpolis_car_modifications");
                if (modifications.children().length)
                    modifications.attr("disabled", null);
            }
        );
    };

    var driverProfile = $("#smartpolis_drivers_set").html();
    var updateDriversCount = function () {
        var value = $("#smartpolis_drivers_count").val();
        var newHtml = "";
        if (value == "MULTI") {
            newHtml = "Количество водителей не ограничено";
        } else {
            for (var i = 0; i < value; i++)
                newHtml += driverProfile;
        }
        $("#smartpolis_drivers_set").html(newHtml);
    };

    $("#smartpolis_car_marks").change(updateModels);
    $("#smartpolis_car_models").change(updateModifications);
    $("#smartpolis_drivers_count").change(updateDriversCount);
    $("#smartpolis_order_form").submit(function () {
        $("input[name=CAR_BRAND]").val($("#smartpolis_car_marks").val());
        $("input[name=CAR_MODEL]").val($("#smartpolis_car_models").val());
        $("input[name=CAR_MODIFICATION]").val($("#smartpolis_car_modifications").val());
        $("input[name=CAR_PRICE]").val($("#smartpolis_car_cost").val());
        $("input[name=CAR_YEAR]").val($("#smartpolis_car_manufacturing_year").val());
        $("input[name=DRIVER_QTY]").val($("#smartpolis_drivers_count").val());
        $("input[name=YOUR_NAME]").val($("#smartpolis_client_name").val());
        $("input[name=YOUR_EMAIL]").val($("#smartpolis_client_email").val());
        $("input[name=CONTACT_PHONE]").val($("#smartpolis_client_phone").val());

        var yrs = "";
        $("select.smartpolis_car_form_age").each(function () {
            yrs += (yrs.length ? "," : "") + $(this).val();
        });

        var experience = "";
        $("select.smartpolis_car_form_experience").each(function () {
            experience += (experience.length ? "," : "") + $(this).val();
        });

        var gender = "";
        $("select.smartpolis_car_form_gender").each(function () {
            gender += (gender.length ? "," : "") + $(this).val();
        });

        $("input[name=YEARS]").val(yrs);
        $("input[name=EXPERIENCE]").val(experience);
        $("input[name=GENDER]").val(gender);
    });

    var self = this;
    var smartpolis_show_type = jQuery('#smartpolis_car_form input[name=smartpolis_show_type]').val();
    var broker_name = " в Stabilitas";

    self.prepareForm = function() {
    // Кнопка продолжить или расчитать, в зависимости от режима работы плагина
    self.prepareSubmitButton();
    // Валидация формы на отправке и отправка запроса
    jQuery('#smartpolis_car_form').bind('submit', function() {
      if ( self.hasErrorForm() ) {
        return false;
      }
      self.getResult();
      return false;
    });
  }

  self.getResult = function() {
    jQuery('#smartpolis_message_before_form').css('display', 'block');
    jQuery('#smartpolis_car_form input:submit').attr('disabled', 'disabled').addClass('disabled');

    jQuery.getJSON(getRequestUrl(), jQuery('#smartpolis_car_form').serialize(), function(r) {
      var count_result = r.length;
      var headers_table_was_show = false;
      jQuery('#smartpolis_wait_count_result').html('Осталось расчитать: ' + count_result);
      jQuery('#smartpolis_result').children().remove();
      jQuery(r).each(function() {
        var company = this;
        jQuery.getJSON(getRequestUrl(), {'ajax': 'Y', 'type':'getResult', 'id':company.id }, function(r){
          count_result--;
          jQuery('#smartpolis_wait_count_result').html('Осталось расчитать: ' + count_result);
          if ( smartpolis_show_type != 'send_by_letter' ) {
            if ( ! headers_table_was_show ) {
              text = '<div class="pol1"></div>\
                  <div class="pol2"></div>\
                  <div class="row-th">\
                  <div class="td1"></div>\
                  <div class="td2">Тариф страховой компании</div>\
                  <div class="hidden smartpolis_broker_tariff_title td3">Тариф' + broker_name +' </div>\
                  </div><!--end row-th-->';
              jQuery(text).appendTo('#smartpolis_result');
              headers_table_was_show = true;
            }
            if (r.sum && r.sum!=0) {
              text ='<div class="row">\
                  <div class="td1"><img alt="" src="http://casco.cmios.ru/' + r.logo + '" style="width: 100px; height: 40px;margin-top: 7px;"/></div>\
                  <div class="td2">' + r.sum + ' руб.</div>\
                  <div class="smartpolis_broker_tariff td3"><div class="hidden">' + r.our_sum + ' руб. (-' + r.discount + '%) </div><a class="but" href="#" onclick="javascript: jQuery(\'#smartpolis_order_form\').css(\'display\', \'block\'); return false;"></a></div>\
                  </div><!--end row-->';


/*              text = '<tr>';
              text += '<td class="logo"><img src="http://casco.cmios.ru/' + r.logo + '" /></td>';
              text += '<td class="company_sum">' + r.sum + '</td>';

              if (r.sum == r.our_sum) {
                text += '<td class="our_sum">' + r.our_sum + '</td>';
                text += '<td class="order"><button type="submit" onclick="javascript: jQuery(\'#smartpolis_order_form\').css(\'display\', \'block\'); return false;">Купить ' + r.result_id + '</td>';
              } else {
                text += '<td class="our_sum">' + r.our_sum + ' (- ' + r.discount + ' %)</td>';
                text += '<td class="order"><button type="submit" onclick="javascript: jQuery(\'#smartpolis_order_form\').css(\'display\', \'block\'); return false;">Купить со скидкой ' + r.result_id + '</td>';
              }
              text += '</tr>';
*/              jQuery(text).appendTo('#smartpolis_result');
            }

          }
          else if ( count_result == 0 ) {
            jQuery('#smartpolis_car_form input:submit').removeAttr('disabled').removeClass('disabled');
            jQuery('#smartpolis_message_before_form').html('На указанный Вами email была отправлена ссылка на наше комерческое предложение.');
          }
        });
      });
    });
    console.log(smartpolis_show_type);
    return false;
  }

  self.prepareSubmitButton = function() {
    if ( smartpolis_show_type != 'form_after_show' ) {
      jQuery('#smartpolis_car_form input:submit').html('Продолжить');
    }
  }

  self.hasErrorRequiredFields = function() {
    error = false;
    jQuery('#smartpolis_car_marks, #smartpolis_car_models, #smartpolis_car_modifications, #smartpolis_car_cost, #smartpolis_car_manufacturing_year').removeClass('error');
    car_cost = jQuery('#smartpolis_car_cost').val();
    if ( ! parseInt(car_cost) || parseInt(jQuery('#smartpolis_car_cost').val()) == 0) {
      jQuery('#smartpolis_car_cost').addClass('error');
      return true;
    }
    return error;
    //console.log(jQuery('#smartpolis_car_marks').val());
    //console.log(jQuery('#smartpolis_car_models').val());
    //console.log(jQuery('#smartpolis_car_modifications').val());
    //console.log(jQuery('#smartpolis_car_cost').val());
    //console.log(jQuery('#smartpolis_car_manufacturing_year').val());
  }

  self.hasErrorContactFormRequiredFields = function() {
    error = false;
    jQuery('#smartpolis_client_name, #smartpolis_client_email, #smartpolis_client_phone').removeClass('error');
    smartpolis_client_name = jQuery('#smartpolis_client_name').val();
    smartpolis_client_email = jQuery('#smartpolis_client_email').val();
    smartpolis_client_phone = jQuery('#smartpolis_client_phone').val();
    if (smartpolis_client_name=='') {
      jQuery('#smartpolis_client_name').addClass('error');
      return true;
    }
    if (smartpolis_client_email=='') {
      jQuery('#smartpolis_client_email').addClass('error');
      return true;
    }
    if (smartpolis_client_phone=='') {
      jQuery('#smartpolis_client_phone').addClass('error');
      return true;
    }
    return error;
  }

  self.hasErrorForm = function() {
    if ( self.hasErrorRequiredFields() ) {
      return true;
    }

    if ( smartpolis_show_type != 'form_after_show' ) {
      if (jQuery('#smartpolis_contact_form:visible').length==0) {
        jQuery('#smartpolis_car_form button:submit').html('Расчитать');
        jQuery('#smartpolis_contact_form').css('display', 'block');
        return true;
      }

      if ( self.hasErrorContactFormRequiredFields() ) {
        return true;
      }
    }

    return false;
  }

  self.init = function() {
    jQuery.ajax({
      async: false
    });
    self.prepareForm();
  }

  self.init();
});