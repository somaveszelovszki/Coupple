/**
 /**
 * This file contains the JavaScript for the events page
 * @type {Object} car Contains global information
 */


car.events = (function ($) {

	var eventHandlers = {
		createEventButtonClick: function(){
			var html = '\
					<table>\
						<tr>\
							<td class="input-field">\
								<input type="text" id="eventNameField" class="validate"/>\
								<label for="eventNameField">Event name</label>\
							</td>\
						</tr>\
						<tr>\
							<td class="input-field">\
								<input type="text"  id="locationField"/>\
								<label for="locationField">Location</label>\
							</td>\
						</tr>\
						<tr>\
							<td class="input-field">\
								<input class="datepicker validate" type="text" id="eventDateField"/>\
								<label for="eventDateField">Date</label>\
							</td>\
							<td class="input-field">\
								<input class="timepicker validate" type="text" id="eventTimeField"/>\
								<label for="eventTimeField">Time</label>\
							</td>\
						</tr>\
						<tr>\
							<td class="input-field">\
								<input type=""  id="" class="validate"/>\
								<label for=""></label>\
							</td>\
						</tr>\
						<tr>\
							<td class="input-field">\
								<input type=""  id="" class="validate"/>\
								<label for=""></label>\
							</td>\
						</tr>\
					</table>';
			var options = {
				buttons: [
					{
						text: "Ok",
						click: function() {
							$( this ).dialog( "close" );
						}
					}
				],
				dialogClass: "",		// classes will be added to dialog
				draggable:false,
				minHeight: 150,
				minWidth: 200,
				resizable: false,
				title: "Create event",

			};
			$(html).dialog(options);
			$('.datepicker').pickadate();

		}
	};

	return {
		initialize: function () {		// initializes event handlers

			$('#createEventButton').click(eventHandlers.createEventButtonClick);
		}
	}
})(jQuery);

/**
 * Fires when the DOM is loaded
 */
$(document).ready(function () {
	car.events.initialize();
});
