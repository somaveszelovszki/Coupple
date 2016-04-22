/**
 * This file contains the JavaScript for the messages page
 * @type {Object} car Contains global information
 */

car.messages = (function ($) {
	var couple = sessionStorage.getObject('couple');	// copy of the object in session storage

	/**
	 * Updates messages - posts search, updates session storage and message area
	 */

	var eventHandlers = {
		sendButtonClick: function () {
			if (couple.accessId === 'couple') {		// logged in as couple -> cannot send messages!
				return;
			}

			var messageTextarea = $('#messageTextarea');

			var text = messageTextarea.val();		// message text

			if (text.length) {
				var time = new Date().toMySQLDatetimeString();
				$.post(createURIText('coupple/ajaxCreate/messages'), {		// sends post to register couple
							data: {
								text    : text,
								dateTime: time,
								coupleId: couple.id,
								fromId  : couple.accessId,
								toId    : (couple.accessId === couple.person1Id) ? couple.person2Id : couple.person1Id		// the other person's id
							},
							time: time
						},
						function (response) {
							if (response.success) {
								if (response.result && response.result.length == 1) {	// insert was successful
									messageTextarea.val('');
									messagesUpdater.updateMessagesArea();
								} else {
									console.log('Some error has happened during saving message :(');
								}
							} else {
								console.log(response.message ? response.message :
										'Some error has happened during saving message :(');
							}
						}, 'json');
			}
		}
	};

	return {
		initialize: function () {		// initializes event handlers
			messagesUpdater.updateUnreadMessages(true);

			$('#sendButton').click(eventHandlers.sendButtonClick);

			$(window).unload(function(){	// when user navigates away from page, messages object containing active messages is set to null
				car.isMessagesTabOpen = false;
				var messages = messagesUpdater.createMessagesObjectForSessionStorage();
				sessionStorage.setObject('messages', messages);
			});
		}
	}
})(jQuery);

/**
 * Fires when the DOM is loaded
 */
$(document).ready(function () {
	car.messages.initialize();
});
