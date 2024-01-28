const _FF = {
  cron2text: function () {
      var expression = $('#cronExpression').val();

      $.ajax({
          type: "POST",
          url: "https://www.freeformatter.com/quartz-cron2text",
          data: {
              "expression": expression
          },
          dataType: 'json',
          success: function (data) {
              if (data && data.description) {
                  FF.notifications(data.description, 'info');
              } else {
                  FF.notifications(data.error, 'danger');
              }
          }
      });
  },
  nextDates: function() {
      var expression = $('#cronExpression').val();

      $.ajax({
          type: "POST",
          url: "https://www.freeformatter.com/quartz-next-dates",
          data: {
              "expression": expression
          },
          dataType: 'json',
          success: function (data) {
              if (data && data.description) {
                  FF.notifications(data.description, 'info');
              } else {
                  FF.notifications(data.error, 'error');
              }
          }
      });
  },
  cron: function () {
      $(this).parents('.cron-option').children('input[type="radio"]').attr("checked", "checked");
     // _FF.seconds();
      _FF.minutes();
      _FF.hours();
      _FF.day();
      _FF.month();
      _FF.year();
      //var seconds = $('#cronResultSecond').text();
      var minutes = $('#cronResultMinute').text();
      var hours = $('#cronResultHour').text();
      var dom = $('#cronResultDom').text();
      var month = $('#cronResultMonth').text();
      var dow = $('#cronResultDow').text();
      var year = $('#cronResultYear').text();
      $('.cronResult').text(minutes + ' ' + hours + ' ' + dom + ' ' + month + ' ' + dow);
      update_human();
      siguientes_cron();
  },
  seconds: function () {
      var seconds = '';
      if ($('#cronEverySecond:checked').length) {
          seconds = '*';
      } else if ($('#cronSecondIncrement:checked').length) {
          seconds = $('#cronSecondIncrementStart').val();
          seconds += '/';
          seconds += $('#cronSecondIncrementIncrement').val();
      } else if ($('#cronSecondSpecific:checked').length) {
          $('[name="cronSecondSpecificSpecific"]:checked').each(function (i, chck) {
              seconds += $(chck).val();
              seconds += ',';
          });
          if (seconds === '') {
              seconds = '0';
          } else {
              seconds = seconds.slice(0, -1);
          }
      } else {
          seconds = $('#cronSecondRangeStart').val();
          seconds += '-';
          seconds += $('#cronSecondRangeEnd').val();
      }
      $('#cronResultSecond').text(seconds);
  },
  minutes: function () {
      var minutes = '';
      if ($('#cronEveryMinute:checked').length) {
          minutes = '*';
      } else if ($('#cronMinuteIncrement:checked').length) {
          minutes = $('#cronMinuteIncrementStart').val();
          minutes += '/';
          minutes += $('#cronMinuteIncrementIncrement').val();
      } else if ($('#cronMinuteSpecific:checked').length) {
          $('[name="cronMinuteSpecificSpecific"]:checked').each(function (i, chck) {
              minutes += $(chck).val();
              minutes += ',';
          });
          if (minutes === '') {
              minutes = '0';
          } else {
              minutes = minutes.slice(0, -1);
          }
      } else {
          minutes = $('#cronMinuteRangeStart').val();
          minutes += '-';
          minutes += $('#cronMinuteRangeEnd').val();
      }
      $('#cronResultMinute').text(minutes);
  },
  hours: function () {
      var hours = '';
      if ($('#cronEveryHour:checked').length) {
          hours = '*';
      } else if ($('#cronHourIncrement:checked').length) {
          hours = $('#cronHourIncrementStart').val();
          hours += '/';
          hours += $('#cronHourIncrementIncrement').val();
      } else if ($('#cronHourSpecific:checked').length) {
          $('[name="cronHourSpecificSpecific"]:checked').each(function (i, chck) {
              hours += $(chck).val();
              hours += ',';
          });
          if (hours === '') {
              hours = '0';
          } else {
              hours = hours.slice(0, -1);
          }
      } else {
          hours = $('#cronHourRangeStart').val();
          hours += '-';
          hours += $('#cronHourRangeEnd').val();
      }
      $('#cronResultHour').text(hours);
  },
  day: function () {
      var dow = '';
      var dom = '';

      if ($('#cronEveryDay:checked').length) {
          dow = '*';
          dom = '?';
      } else if ($('#cronDowIncrement:checked').length) {
          dow = $('#cronDowIncrementStart').val();
          dow += '/';
          dow += $('#cronDowIncrementIncrement').val();
          dom = '?';
      } else if ($('#cronDomIncrement:checked').length) {
          dom = $('#cronDomIncrementStart').val();
          dom += '/';
          dom += $('#cronDomIncrementIncrement').val();
          dow = '?';
      } else if ($('#cronDowSpecific:checked').length) {
          dom = '?';
          $('[name="cronDowSpecificSpecific"]:checked').each(function (i, chck) {
              dow += $(chck).val();
              dow += ',';
          });
          if (dow === '') {
              dow = 'SUN';
          } else {
              dow = dow.slice(0, -1);
          }
      } else if ($('#cronDomSpecific:checked').length) {
          dow = '?';
          $('[name="cronDomSpecificSpecific"]:checked').each(function (i, chck) {
              dom += $(chck).val();
              dom += ',';
          });
          if (dom === '') {
              dom = '1';
          } else {
              dom = dom.slice(0, -1);
          }
      } else if ($('#cronLastDayOfMonth:checked').length) {
          dow = '?';
          dom = 'L';
      } else if ($('#cronLastWeekdayOfMonth:checked').length) {
          dow = '?';
          dom = 'LW';
      } else if ($('#cronLastSpecificDom:checked').length) {
          dom = '?';
          dow = $('#cronLastSpecificDomDay').val();
          dow += 'L';
      } else if ($('#cronDaysBeforeEom:checked').length) {
          dow = '?';
          dom = 'L-';
          dom += $('#cronDaysBeforeEomMinus').val();
      } else if ($('#cronDaysNearestWeekdayEom:checked').length) {
          dow = '?';
          dom = $('#cronDaysNearestWeekday').val();
          dom += 'W';
      } else if ($('#cronNthDay:checked').length) {
          dom = '?';
          dow = $('#cronNthDayDay').val();
          dow += '#';
          dow += $('#cronNthDayNth').val();;
      }
      $('#cronResultDom').text(dom);
      $('#cronResultDow').text(dow);
  },
  month: function () {
      var month = '';
      if ($('#cronEveryMonth:checked').length) {
          month = '*';
      } else if ($('#cronMonthIncrement:checked').length) {
          month = $('#cronMonthIncrementStart').val();
          month += '/';
          month += $('#cronMonthIncrementIncrement').val();
      } else if ($('#cronMonthSpecific:checked').length) {
          $('[name="cronMonthSpecificSpecific"]:checked').each(function (i, chck) {
              month += $(chck).val();
              month += ',';
          });
          if (month === '') {
              month = '1';
          } else {
              month = month.slice(0, -1);
          }
      } else {
          month = $('#cronMonthRangeStart').val();
          month += '-';
          month += $('#cronMonthRangeEnd').val();
      }
      $('#cronResultMonth').text(month);
  },
  year: function () {
      var year = '';
      if ($('#cronEveryYear:checked').length) {
          year = '*';
      } else if ($('#cronYearIncrement:checked').length) {
          year = $('#cronYearIncrementStart').val();
          year += '/';
          year += $('#cronYearIncrementIncrement').val();
      } else if ($('#cronYearSpecific:checked').length) {
          $('[name="cronYearSpecificSpecific"]:checked').each(function (i, chck) {
              year += $(chck).val();
              year += ',';
          });
          if (year === '') {
              year = '2016';
          } else {
              year = year.slice(0, -1);
          }
      } else {
          year = $('#cronYearRangeStart').val();
          year += '-';
          year += $('#cronYearRangeEnd').val();
      }
      $('#cronResultYear').text(year);
  }
};


