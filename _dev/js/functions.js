export const hoverConfig = (el) => {

	$('.defaultForm').removeClass('pp-settings-link-on');
	$('#subtab-AdminPayPalCustomizeCheckout').removeClass('pp-settings-link-on pp__border-b-primary');
	$('#subtab-AdminPayPalSetup').removeClass('pp-settings-link-on pp__border-b-primary');
	el.addClass('pp-settings-link-on');
	$('html, body').animate({
		scrollTop: el.offset().top - 200 + "px"
	}, 900);
}

export const hoverTabConfig = () => {
	if ($('#subtab-AdminPayPalCustomizeCheckout').hasClass('current')) {
		$('#subtab-AdminPayPalSetup').addClass('pp-settings-link-on pp__border-b-primary');
	} else if ($('#subtab-AdminPayPalSetup').hasClass('current')) {
		$('#subtab-AdminPayPalCustomizeCheckout').addClass('pp-settings-link-on pp__border-b-primary');
	}
	$('html, body').animate({
		scrollTop: $('#head_tabs').offset().top - 200 + "px"
	}, 900);
}

export const selectOption = (select, el) => {
	if (select) {
		select.on('change', (e) => {
			let index = e.target.selectedIndex;
			if (index == 0) {
				el.show();
			} else {
				el.hide();
			}
		})
	}
}
