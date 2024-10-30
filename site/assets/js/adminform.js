function submitAdminForm(pressbutton) {
	var form = document.adminForm;

	if (pressbutton == 'edit' || pressbutton == 'publish' || pressbutton == 'unpublish' || pressbutton == 'copy' || pressbutton == 'delete') {
		var cids = document.getElementsByName('cid[]');

		var checked = false;
		for (var i = 0; i < cids.length; i++) {
			if (cids[i].checked == true) {
				checked = true;
				break;
			}
		}

		if (checked == false) {
			alert('Please first make a selection from the list');
		}
		else {
			Miwi.submitform(pressbutton);
		}
	}
	else {
		Miwi.submitform(pressbutton);
	}
}