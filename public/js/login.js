/**
 * This file contains the JavaScript for the login page
 * @type {Object} car Contains global information
 */

car.login = (function ($) {

	var eventHandlers = {

		/**
		 * Handles click event on login button
		 * Collects form information
		 * Sends post to controller to get couple
		 * If success: loads main page
		 */
		loginButtonClick: function () {

			var persons = {};
			for (var personId = 1; personId <= 2; personId++) {
				persons[personId] = {
					email    : $('#person' + personId + 'EmailField').val(),
					password: $('#person' + personId + 'PasswordField').val()
				};
			}

			var time = new Date().toMySQLDatetimeString();
			$.post(createURIText('coupple/ajaxLogInCouple'), {		// sends post to log in couple
						persons: persons,
						time: time
					},
					eventHandlers.getCoupleAjaxCallSuccess, 'json');
		},

		/**
		 * Handles ajax call success after log in post
		 * If search was successful: loads main page
		 * @param {Object} response
		 */
		getCoupleAjaxCallSuccess: function (response) {
			if (response.success) {
				if (response.result !== null && response.result.length == 1) {	// search found 1 couple that matched criteria
					window.location.href = createURIText('coupple/main');
				} else {
				}
			}
		}
	};

	return {
		initialize: function () {		// initializes event handlers
			$('#signupButton').click(function () {	// on signup button click: loads signup page
				window.location.href = createURIText('coupple/signup');
			});

			$('#loginButton').click(eventHandlers.loginButtonClick);
		}
	}
})(jQuery);

/**
 * Fires when the DOM is loaded
 */
$(document).ready(function () {
	car.login.initialize();
});