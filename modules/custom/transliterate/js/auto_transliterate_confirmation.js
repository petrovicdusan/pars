jQuery(document).ready(function($) {
	let conf;
	const inSync = drupalSettings.autoTransliterate.IN_SYNC;
  const message = drupalSettings.autoTransliterate.MESSAGE;
	$("#edit-submit").click(function(e) {
		if(inSync == false) {
			conf = confirm(message);
			if(conf == false) {
				return false;
			}
		}
	});
});
