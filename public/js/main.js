/**
 * This file contains the JavaScript for the main page
 * @type {Object} car Contains global information
 */

car.main = (function ($) {

	var eventHandlers = {};

	return {
		initialize: function () {		// initializes event handlers

			var actualDate = new Date();
			actualDate.setHours(0,0,0,0);

			var christmas = new Date(actualDate.getFullYear(), 11, 25);

			var lizaBirthday = new Date(actualDate.getFullYear(), 2, 11);

			var daysUntilChristmas = dateDifference(actualDate, christmas, true);
			var daysUntilLizaBirthday = dateDifference(actualDate, lizaBirthday, true);

			$(daysUntilChristmas > 0 ? ('<p style="margin-left:20px; display: inline-block">Only ' + daysUntilChristmas + (daysUntilChristmas === 1 ? " day" : " days") + " until Christmas!</p>") :
					'<p style="margin-left:20px; display: inline-block">Merry Christmas!</p>').insertAfter('#coupleData');
			$(daysUntilLizaBirthday > 0 ? ('<p style="margin-left:20px; display: inline-block">Only ' + daysUntilLizaBirthday + (daysUntilLizaBirthday === 1 ? " day" : " days") + " until Liza's birthday!</p>") :
					'<p style="margin-left:20px; display: inline-block">Happy birthday, Liza!</p>').insertAfter('#coupleData');
		}
	}
})(jQuery);

/**
 * Fires when the DOM is loaded
 */
$(document).ready(function () {
	car.main.initialize();
});
