import './bootstrap';
$.ajaxSetup({
  headers: {
    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
  },
})

$(document).ready(function () {
  $(".form-block .btn-submit").click(function (e) {
    var loading = ($(this).data("loading")) ? $(this).data("loading") : "LOADING..."
    $(this).html("<i class='fa-solid fa-circle-notch fa-spin'></i> &nbsp;&nbsp;" + loading).attr("disabled", true)
    $(this).closest(".form-block").submit()
  })

  $(".set-tooltip").tooltip({
    container: 'body'
  })
})

function setNotifSuccess(message, redirect)
{
  Swal.fire({
    icon: "success",
    title: label_success,
    text: message,
  }).then((result) => {
    if (result.isConfirmed) {
      if (redirect === false)
        Swal.close()
      else if (redirect == "reload")
        location.reload()
      else
        window.location = redirect
    } else {
      Swal.close()
    }
  })
}

function setNotifFail(message)
{
  Swal.fire(label_failed, message, "error")
}

function setNotifInfo(message, redirect) {
  Swal.fire({
    icon: "info",
    title: label_info,
    text: message,
  }).then((result) => {
    if (result.isConfirmed) {
      if (redirect === undefined)
        Swal.close()
      else if (redirect == "reload")
        location.reload()
      else
        window.location = redirect
    } else {
      Swal.close()
    }
  })
}

function ajaxError(status) {
  Swal.fire(label_failed, "Maaf telah terjadi kesalahan, harap laporkan error ini", "error")
}

function ajaxLaravelError(xhr) {
  if (xhr.status == 422) {
    for (index in xhr.responseJSON.errors)

      setNotifFail(xhr.responseJSON.errors[index][0])
  } else
    ajaxError(xhr.status)
}

function deleteConfirm(url, redirect, grid) {
  console.log(url)
  Swal.fire({
    icon: "warning",
    title: 'KONFIRMASI',
    text: 'Apakah anda yakin akan menghapus data ini?',
    showCancelButton: true,
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal',
    showLoaderOnConfirm: true,
    preConfirm: () => {
      $.ajax({
        type: "DELETE",
        url: url,
        dataType: "json",
        success: function (response) {
          if (response.status) {
            if (grid != undefined) {
              window.LaravelDataTables[grid].ajax.reload()
            }

            setNotifSuccess(response.message, redirect)
          } else
            setNotifFail(response.message)
        },
        error: function (xhr, ajaxOptions, thrownError) {
          ajaxError(xhr.status)
        }
      })

      return true
    }
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.close()
    }
  })
}

function htmlEntities(str) {
  return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function phoneFormat(phone) {
  const len = phone.length
  const phone1 = phone.substring(0, 4);
  const phone2 = phone.substring(4, 8);
  const phone3 = phone.substring(8, 12);
  const phone4 = (len > 12) ? '-' + phone.substring(12, len) : '';

  return phone1 + '-' + phone2 + '-' + phone3 + phone4;
}

function moneyFormat(a) {
  return a.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
}

function monthFormat(month, format) {
  var format = (format == undefined) ? "mmmm" : format

  if (format == 'mmmm') {
    var fm = month_mmmm;
  } else if (format == 'mmm') {
    var fm = month_mmm;
  } else if (format == 'romawi') {
    var fm = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
  }

  return fm[month];
}

function dayFormat(day, format) {
  var index = day - 1
  var format = (format == undefined) ? "dddd" : format

  if (format == 'dddd') {
    var fd = day_dddd;
  } else if (format == 'ddd') {
    var fd = day_ddd;
  }

  return fd[index];
}

function dateFormat(date, format) {
  var format = (format == undefined) ? "{dd} {mmmm} {yyyy}" : format
  var date = new Date(date)
  var dd = date.getDate()
  var dddd = dayFormat(date.getDay())
  var mmmm = monthFormat(date.getMonth())
  var mmm = monthFormat(date.getMonth(), "mmm")
  var mm = date.getMonth() + 1
  var yyyy = date.getFullYear()
  var yyy = yyyy.toString().substring(2, 4)
  var hh = date.getHours()
  var ii = date.getMinutes()

  dd = (dd < 10) ? "0" + dd : dd
  mm = (mm < 10) ? "0" + mm : mm
  hh = (hh < 10) ? "0" + hh : hh
  ii = (ii < 10) ? "0" + ii : ii

  var result = format.replace("{dd}", dd)
    .replace("{dddd}", dddd)
    .replace("{mmmm}", mmmm)
    .replace("{mmm}", mmm)
    .replace("{mm}", mm)
    .replace("{yyyy}", yyyy)
    .replace("{yyy}", yyy)
    .replace("{hh}", hh)
    .replace("{ii}", ii)

  return result
}

function dateDiffInDay(start, end)
{
  const startdate = new Date(start)
  const enddate = new Date(end)

  return Math.round((enddate - startdate) / (1000 * 60 * 60 * 24)) + 1
}

function setSelect2(allowClear) {
  var allowClear = (allowClear == undefined) ? false : allowClear

  $(".set-select2").select2({
    placeholder: label_choose,
    allowClear: allowClear,
    width: "100%"
  })
}

function setCounter() {
  $(".counter-number").countTo({
    onComplete: function (value) {
      $(this).html($(this).data("number"))
    }
  })
}

function setDatePicker()
{
  $(".date-picker").daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    autoUpdateInput: false,
    locale: {
      format: "DD-MM-YYYY"
    }
  }).on("apply.daterangepicker", function (ev, picker) {
    $(this).val(picker.startDate.format('DD-MM-YYYY'))
  })
}

function setDateRangePicker(startInput, endInput)
{
  $(".date-range-picker").daterangepicker({
    showDropdowns: true,
    autoUpdateInput: false,
    locale: {
      format: "DD MMM YYYY"
    }
  }).on("apply.daterangepicker", function (ev, picker) {
    const start = dateFormat(picker.startDate.format('YYYY-MM-DD'), "{dd} {mmm} {yyyy}")
    const end = dateFormat(picker.endDate.format('YYYY-MM-DD'), "{dd} {mmm} {yyyy}")

    $(startInput).val(picker.startDate.format('YYYY-MM-DD'))
    $(endInput).val(picker.endDate.format('YYYY-MM-DD'))
    $(this).val(`${start} - ${end}`)
  })
}

function setTimePicker()
{
  $(".time-picker").clockpicker({
    autoclose: true
  })
}

function setTextEditor(id, tools, extra) {
    if (tools == undefined) {
        tools = [
            // Full Version
            // { name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
            // { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            // { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
            // { name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
            // '/',
            // { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
            // { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
            // { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            // { name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
            // '/',
            // { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
            // { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            // { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
            // { name: 'about', items: [ 'About' ] }

            // Custom
            { name: 'document', items: ['Source', 'Preview'] },
            { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
            { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'] },
            { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
            { name: 'colors', items: ['TextColor', 'BGColor'] },
            { name: 'tools', items: ['Maximize', 'ShowBlocks'] },
            { name: 'about', items: ['About'] },
            '/',
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
            { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
            { name: 'insert', items: ['HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak'] },
        ]
    }

    if (extra == undefined)
        extra = 'justify'

    var id = (id == undefined) ? 'text-editor' : id
    CKEDITOR.replace(id, {
        toolbar: tools,
        removeButtons: 'Format',
        extraPlugins: extra,
        contentsCss: ["body {font-size: 16px;font-family: Roboto, sans-serif;}"]
    });
}

function randomString() {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    for (var i = 0; i < 5; i++) text += possible.charAt(Math.floor(Math.random() * possible.length));
    return text;
}
