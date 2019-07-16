export const hoverConfig = (el) => {
  $('.defaultForm').removeClass('pp-settings-link-on');
  $('.page-head-tabs a').removeClass('pp-settings-link-on pp__border-b-primary');
	el.addClass('pp-settings-link-on');
	$('html, body').animate({
		scrollTop: el.offset().top - 200 + "px"
	}, 900);
}

export const hoverTabConfig = () => {
  let tabs = document.querySelectorAll('.page-head-tabs a'),
		currentTab = $('.page-head-tabs a.current');
	tabs.forEach( el => {
		let checkoutTab = $(el).attr('href').includes('AdminPayPalCustomizeCheckout'),
		 	setupTab = $(el).attr('href').includes('AdminPayPalSetup');
		if ((currentTab.attr('href').includes('AdminPayPalCustomizeCheckout') && setupTab)
			|| (currentTab.attr('href').includes('AdminPayPalSetup') && checkoutTab)) {
			$(el).addClass('pp-settings-link-on pp__border-b-primary');
		}
	})
	$('html, body').animate({
		scrollTop: $('.page-head-tabs').offset().top - 200 + "px"
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
