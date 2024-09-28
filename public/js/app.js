function setSelect2(allowClear) {
  var allowClear = (allowClear == undefined) ? false : allowClear

  $(".set-select2").select2({
    placeholder: label_choose,
    allowClear: allowClear,
    width: "100%"
  })
}