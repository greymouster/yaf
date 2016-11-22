function formateICCID(hmcode) {
  if (hmcode.length != 20) {
    return hmcode;
  }
  var reg = /^([a-zA-Z0-9]{5})([a-zA-Z0-9]{5})([a-zA-Z0-9]{5})([a-zA-Z0-9]{5})$/;
  var matches = reg.exec(hmcode);
  var newcode = matches[1] + ' ' + matches[2] + ' ' + matches[3] + ' ' + matches[4];
  return newcode;
}
