//<![CDATA[
window.onload = function() {
    setTimeout(doAjax, 0);   
}
var interval = 3000;  // 1000 = 1 second, 3000 = 3 seconds
function doAjax() {
    Q_ID = $('#scroll > input.Q_ID').val();
    console.log( $('#scroll > input.Q_ID').val() );
}