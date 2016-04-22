/**
 * This file contains the JavaScript for the aboutUs page
 * @type {Object} car Contains global information
 */

car.aboutUs = (function ($) {

	var eventHandlers = {
		personButtonClick:function(){
			// name attribute contains person id
			var personId = $(this)[0].name;
			$('.personTable').each(function() {
				if (!$(this).hasClass('noDisplay')) {
					$(this).addClass('noDisplay');
				}
			});
			$('#person' + personId + 'Table').removeClass('noDisplay');
		}
	};

	return {
		initialize: function () {		// initializes event handlers

			var actualDate = new Date();

			$('.datepicker').pickadate({	// Default date format in input field i 'mmmm d yyyy'
				selectMonths: true, // Creates a dropdown to control month
				selectYears: 120, // Creates a dropdown of 120 years to control year
				min: new Date(actualDate.getFullYear() - 120, 0,1),		// earliest choosable date is January 1, 120 years ago
				max: actualDate
			});

			//$('#coupleTable').find('input, select').prop('disabled', true);
			$('#detailsSection').find('input, select').prop('disabled', true);

			$('.personButton').click(eventHandlers.personButtonClick);
		}
	}
})(jQuery);

/**
 * Fires when the DOM is loaded
 */
$(document).ready(function () {
	car.aboutUs.initialize();
});
