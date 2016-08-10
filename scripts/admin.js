/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

$(document).ready(function() {
	$('.navbar-toggle').click(function(e) {
		e.preventDefault();

		if($('body.sidebar-collapse').length == 0) {
			$('body').addClass('sidebar-collapse');
		} else {
			$('body').removeClass('sidebar-collapse');
		}
	});

	$('.sidebar-menu>li').each(function() {

		if($(this).children('ul').length != 0) {
			$(this).children('a').click(function (e) {
				e.preventDefault();

				if ($(this).parent().hasClass('active')) {
					$(this).parent().removeClass('active');
				} else {
					$(this).parent().addClass('active');
				}
			});
		}
	});
});
