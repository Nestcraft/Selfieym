
/**
 * Redirect URL
 * @param url
 */
function redirect(url) {
	window.location.replace(url);
	window.location.href = url;
}
$(document).ready(function () {
	$('input[name=purchase_code]').parent().hide();
});