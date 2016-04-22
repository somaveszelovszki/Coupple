/**
 * This file contains the JavaScript for the signup page
 * @type {Object} car Contains global information
 */

car.signup = (function ($) {

	var eventHandlers = {

		/**
		 * Handles click event on signup button
		 * Collects form information and sends post to controller to create new couple in database
		 * If success: loads main page
		 */
		signupButtonClick : function(){

			// data that will be posted to controller
			var data = {
				couple:{},	// data of couple
				persons:{	// data of the 2 people
					'person1':{},			// data of first person
					'person2': {}			// data of second person
				}
			};

			data.couple.name = $('#coupleNameField').val();

			var beginningDateString = $('#coupleRelationshipBeginningDateField').val();

			data.couple.relationshipBeginningDate = beginningDateString ? new Date(beginningDateString).toMySQLDatetimeString() : null;
			data.couple.relationshipBeginningShortDescription = $('#coupleRelationshipBeginningShortDescriptionField').val();

			// selected relationship state
			var relationshipStateSelect = $('#relationshipStateSelect');
			data.couple.relationshipStateId = relationshipStateSelect[0].options[relationshipStateSelect.prop('selectedIndex')].value;

			for (var personId = 1; personId <= 2; personId++) {

				var form = $('#person' + personId + 'Form');

				data.persons['person' + personId].firstName = form.find('#person' + personId + 'FirstNameField').val();
				data.persons['person' + personId].surname = form.find('#person' + personId + 'SurnameField').val();
				data.persons['person' + personId].email = form.find('#person' + personId + 'EmailField').val();

				data.persons['person' + personId].password = form.find('#person' + personId + 'PasswordField').val();

				// if password doesn't match password confirm
				if (data.persons['person' + personId].password !== form.find('#person' + personId + 'ConfirmPasswordField').val()) {
					console.log('Confirmed password does not match original password');
					return;
				}

				data.persons['person' + personId].birthday = form.find('#person' + personId + 'BirthdayField').val();

				var genderSelect = form.find('.genderSelect');

				for (var i = 0; i < genderSelect.length; i++) {
					data.persons['person' + personId].genderId = undefined;	// just to create variable, so that we can check if its empty
					if (genderSelect[i].checked) {
						data.persons['person' + personId].genderId = genderSelect[i].id.replace('person' + personId + 'gender', '');
						break;
					}
				}
			}

			if (hasEmptyValue(data)) {
				console.log('Incomplete data');
			} else {
				var time = new Date().toMySQLDatetimeString();
				$.post(createURIText('coupple/ajaxRegisterCouple'), {		// sends post to register couple
							data: data,
							time: time
						},
						function(response){
							if (response.success) {
								if (response.result && response.result.length == 1) {	// search found 1 couple that matched criteria
									window.location.href = createURIText('coupple/main');
								} else {
									console.log('Some error has happened during sign up :(');
								}
							} else {
								console.log(response.message ? response.message : 'Some error has happened during sign up :(');
							}
						}, 'json');
			}
		}
	};

	return {
		initialize: function () {		// initializes event handlers
			$('#signupButton').click(eventHandlers.signupButtonClick);
		}
	}
})(jQuery);

/**
 * Fires when the DOM is loaded
 */
$(document).ready(function () {
	car.signup.initialize();
});
