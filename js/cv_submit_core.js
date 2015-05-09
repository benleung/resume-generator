function ToggleState(checkbox, field) {
	if ($(checkbox).attr('checked')){
		$(field).css('display','none');
	}
	else{
		$(field).css('display','');
	}
}
		
function ToggleCheckboxState(selected) {
	$(selected).toggle();
}