/*
 * Display alert
 * psk_sfb_alert('Error!','File has been deleted','error');
 * psk_sfb_alert('Info!','File has been deleted','info',4000);
 * psk_sfb_alert('Success!','File has been deleted','success');
 * psk_sfb_alert('Warning!','File has been deleted');
 */
function psk_sfb_alert(title, message, alert, time) {
	var alert = ( !alert ) ? '' : ' alert-' + alert;
	var time = ( !time ) ? 5000 : parseInt(time, 10);
	var al = '<div class="alert' + alert + '">';
	al += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
	al += "<strong>" + title + "</strong> " + message;
	al += "</div>";
	jQuery("#psk-alert-area").append(jQuery(al).delay(time).fadeOut("slow", function () {
		jQuery(this).remove();
	}));
}

var psk_sfb_html = function (value) {
	this.value = value.isHtmlObject ? value.value : value;
	this.isHtmlObject = true;
	this.toString = function () {
		return this.value.toString().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	};
};

function psk_sfb_basename(path) {
	return path.replace(/\\/g, '/').replace(/.*\//, '');
}
