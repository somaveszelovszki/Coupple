/**
 * This file contains common JavaScript functions
 */

/**
 * Converts date to MySQL DATETIME string
 * @returns {string} MySQL DATETIME string (e.g. 'Mon Sep 21 2015 19:53:04 GMT+0200 (CEST)' will become '2015-09-21 19:53:04')
 */
Date.prototype.toMySQLDatetimeString = function () {
	// this.toISOString ignores timezone, so we need to set time manually to match timezone
	this.setHours(this.getHours() - this.getTimezoneOffset() / 60);
	return this.toISOString().substring(0, 19).replace('T', ' ');
};

/**
 * Adds given quantity of given interval to date object (e.g. 1 day)    -    HAS NOT BEEN TESTED!!!!!!!!
 * @param {int} quantity
 * @param {string} interval
 */
Date.prototype.add = function (quantity, interval) {
	switch (interval.toLowerCase()) {
		case 'day':
		case 'days':
			this.setDate(this.getDate() + quantity);
			break;
		case 'millisecond':
		case 'milliseconds':
			this.setMilliseconds(this.getMilliseconds() + quantity);
			break;
		case 'year':
		case 'years':
			this.setFullYear(this.getFullYear() + quantity);
			break;
	}
	return this;
};

/**
 * Saves object to session storage - needs to be saved as string
 * @param {string} key
 * @param object
 */
Storage.prototype.setObject = function (key, object) {
	// converts object to string and saved it in session storage
	this.setItem(key, JSON.stringify(object));
};
/**
 * Returns object saved in session storage - stored as string so has to be converted back to object
 * @param {string} key
 * @returns {Object}
 */
Storage.prototype.getObject = function (key) {
	// converts string back to object
	var object = JSON.parse(this.getItem(key));

	// pattern for date string e.g. '2015-11-05T22:45:57.000Z'
	var dateStringRegexp = /[\d]{4}-[\d]{2}-[\d]{2}T[\d]{2}:[\d]{2}:[\d]{2}.[\d]{3}Z/;

	for (var key in object) {
		if (typeof object[key] === 'string' && dateStringRegexp.test(object[key])) {
			object[key] = new Date(object[key]);
		}
	}

	return object;
};

Array.prototype.removeMatchingValues = function (theOtherArray) {
	for (var i = 0; i < this.length; i++) {
		for (var j = 0; j < theOtherArray.length; j++) {
			if (this[i] === theOtherArray[j]) {
				this.splice(i--, 1);
				break;
			}
		}
	}
	return this;
};

/**
 * Creates URI text from text
 * URI pattern: base url + 'index.php?/controller/method
 * @param {string} text
 * @returns {string}
 */
function createURIText(text) {
	// removes base url and 'index.php?' - with or without question mark
	return car.baseUrl + 'index.php?/' + text.replace(/index\.php(\?)?/, '').replace(car.baseUrl, '');
}

/**
 * Counts the difference between two dates in days and returns with it
 * @param {Date} earlierDate
 * @param {Date} laterDate
 * @param {Boolean} isSecondDateAlwaysLater determines if laterDate has to be later than earlierDate (if not, return value can be negative)
 * @returns {Number} difference in days
 */
function dateDifference(earlierDate, laterDate, isSecondDateAlwaysLater) {
	// second date must be later by default
	isSecondDateAlwaysLater = isSecondDateAlwaysLater != undefined ? isSecondDateAlwaysLater : true;

	if (isSecondDateAlwaysLater && laterDate < earlierDate) {
		laterDate.setFullYear(earlierDate.getFullYear() + 1);
	}

	return parseInt(Math.ceil((laterDate - earlierDate) / 1000 / 60 / 60 / 24), 10);
}

/**
 * Determines if variable is empty
 * @param variable
 * @returns {boolean}
 */
function isEmpty(variable) {
	if (variable == '' || variable == null || variable == 'undefined') {	// '==null' is true if variable is undefined
		return true;
	}
	if (typeof variable === 'object') {		// if variable is an object, its length determines if it's empty
		return variable.length == 0;
	}

	if (variable === 'datetime') {
		return false;
	}

	return false;
}

/**
 * Determines if object has empty value
 * @param object
 * @param {boolean} allLevelSearch determines if search should continue to all levels (member objects in object)
 * @returns {boolean}
 */
function hasEmptyValue(object, allLevelSearch) {
	allLevelSearch = allLevelSearch != undefined ? allLevelSearch : true;		// default value for 'allLevels' is true

	if (isEmpty(object)) {
		return true;
	}

	// if variable is an object we check its member variables
	if (typeof object == 'object') {
		for (var key in object) {
			if (isEmpty(object[key])) {
				return true;
			}

			// if search should continue to all levels, function calls itself recursively on all member objects
			if (allLevelSearch && typeof object[key] == 'object' && hasEmptyValue(object[key], true)) {
				return true;
			}
		}
	}

	return false;
}

function fillObjectWithTrash(object) {

	if (typeof object == 'object') {
		for (var key in object) {
			object[key] = fillObjectWithTrash(object[key]);
		}
	} else {
		object = 'someData';
	}
	return object;
}

/**
 * Returns max value of given attribute of objects in the array
 * @param {string} key
 * @param {string} type Type of attribute (number, string, datetime)
 * @returns
 */
Array.prototype.getMax = function (key, type) {
	if (isEmpty(this)) {
		return null;
	}
	type = !isEmpty(type) ? type.toLowerCase() : 'number';
	var max = Math.max.apply(null, this.map(
			function (obj) {
				// numbers can be easily compared, but strings or dates have to be changed, so that they can be compared effectively
				switch (type) {
					case 'number':
					case 'integer':
						return obj[key];
					case 'datetime':
					case 'date':
					case 'time':
						return new Date(obj[key]);

				}
			}));
	switch (type) {
		case 'number':
		case 'integer':
			return max;
		case 'datetime':
		case 'date':
		case 'time':
			return new Date(max);	// max date will be returned in Date format (not in milliseconds, which is the current format)
	}
};

var messagesUpdater = {
	createMessagesObjectForSessionStorage: function () {
		var messages = {};
		messages.activeMessages = [];		// array of active messages (displayed in messages area)
		messages.lastMessageDateTime = null;	// lastMessageDateTime is a Date object containing date of last message
		messages.initializeLimit = 20;		// number of messages to load when loading page
		messages.unreadMessages = [];		// array of unread messages

		return messages;
	},

	/**
	 *
	 * @param {int} frequency in milliseconds
	 * @returns {number}
	 */
	setUnreadMessagesUpdateFrequency: function (frequency) {
		var couple = sessionStorage.getObject('couple');
		if (isEmpty(couple)) {
			return;
		}
		if (false) {		// CREATES RANDOM MESSAGE!!!!!!!!!!! only to check if update is working
			var time = new Date().toMySQLDatetimeString();
			$.post(createURIText('coupple/ajaxCreate/messages'), {		// sends post to register couple
						data: {
							text    : Math.random() * 10,
							dateTime: time,
							coupleId: couple.id,
							fromId  : couple.accessId,
							toId    : (couple.accessId === 'couple') ? 1 : couple.accessId	// the other person's id
						},
						time: time
					},
					function (response) {
						if (response.success) {

						} else {
							console.log(response.message ? response.message :
									'Some error has happened during saving message :(');
						}
					}, 'json');
		}

		messagesUpdater.updateUnreadMessages();

		// calls itself again with a delay (frequency)
		setTimeout(function() {
			messagesUpdater.setUnreadMessagesUpdateFrequency(frequency);
		}, frequency);
	},

	updateUnreadMessages: function (messagesAreaInitializingUpdate) {

		// true if this update initializes messages area
		messagesAreaInitializingUpdate =
				!isEmpty(messagesAreaInitializingUpdate) ? messagesAreaInitializingUpdate : false;

		var couple = sessionStorage.getObject('couple');

		if (isEmpty(couple)) {
			return;
		}

		var time = new Date().toMySQLDatetimeString();
		$.post(createURIText('coupple/ajaxSearch/messages'), {		// sends post to register couple
					data: {
						toId  : couple.accessId != 'couple' ? couple.persons[couple.accessId].id : null,
						unread: 1
					},
					time: time
				},
				function (response) {
					if (response.success) {
						if (response.result) {	// search found messages

							var messages = sessionStorage.getObject('messages');
							messages = isEmpty(messages) ? messagesUpdater.createMessagesObjectForSessionStorage() :
									messages;

							if (response.result.length > 0) {

								console.log('unread: ' + response.result.length);

								messages.unreadMessages = response.result;

								sessionStorage.setObject('messages', messages);

							} else {
								console.log('No messages found');
							}

							if (car.isMessagesTabOpen &&
									(messagesAreaInitializingUpdate || messages.unreadMessages.length)) {	// updates messages area
								messagesUpdater.updateMessagesArea(messagesAreaInitializingUpdate);
							} else {		// updates new message indicator
								messagesUpdater.updateNewMessageIndicator(messages.unreadMessages.length);
							}

						} else {
							console.log('Some error has happened :(');
						}
					} else {
						console.log(response.message ? response.message : 'Some error has happened :(');
					}
				}, 'json');

	},

	updateMessagesArea: function (messagesAreaInitializingUpdate) {

		// true if this update initializes messages area - scroll has to be set to bottom
		messagesAreaInitializingUpdate =
				!isEmpty(messagesAreaInitializingUpdate) ? messagesAreaInitializingUpdate : false;

		var couple = sessionStorage.getObject('couple');	// copy of the object in session storage

		if (isEmpty(couple)) {
			return;
		}

		var messages = sessionStorage.getObject('messages');

		messages = isEmpty(messages) ? messagesUpdater.createMessagesObjectForSessionStorage() : messages;

		var unreadMessagesNum = isEmpty(messages.unreadMessages) ? 0 : messages.unreadMessages.length;

		var time = new Date().toMySQLDatetimeString();
		$.post(createURIText('coupple/ajaxSearch/messages'), {		// sends message search post
					data: {
						limit      : messages.activeMessages.length ? 'no' :
								Math.max(messages.initializeLimit, unreadMessagesNum),
						minDateTime: !isEmpty(messages.lastMessageDateTime) ?
								messages.lastMessageDateTime.toMySQLDatetimeString() : null
					},
					time: time
				},
				function (response) {
					if (response.success) {
						if (response.result) {	// search found messages

							// there can be values that are shown on page and also listed in the search - needed to be removed from array
							response.result = response.result.removeMatchingValues(messages.activeMessages);

							// adds new messages to active messages array
							messages.activeMessages = messages.activeMessages.concat(response.result);

							var pastMessagesArea = $('#pastMessagesArea');

							if (pastMessagesArea) {

								// true if scroll is at bottom position (at newest messages)
								var scrollAtBottom = pastMessagesArea.scrollTop() + pastMessagesArea.height() ===
										pastMessagesArea[0].scrollHeight;

								response.result.forEach(appendMessageToChain);


								if (scrollAtBottom || messagesAreaInitializingUpdate) {
									// sets scroll position to bottom, so last messages will be seen
									pastMessagesArea.scrollTop(pastMessagesArea[0].scrollHeight);
								}

							}

							function appendMessageToChain(element) {
								var senderId = couple.accessId === 'couple' ? couple.person1Id : couple.accessId;
								var html = '\
										<div id="message' + element.id + '" class="messageRow">\
											<div class="messageBox ' +
										((element.fromId === senderId) ? 'sent' : 'received') + '">' +
										'<span class="name">'
										+ couple.persons[element.fromId].firstName +
										'</span>\
										<div class="messageTextBox">\
											<span>'
										+ element.text +
										'</span>\
									</div>\
								</div>\
							</div>';

								pastMessagesArea.append(html);
							}

							// lastMessageDateTime is a Date object
							messages.lastMessageDateTime = messages.activeMessages.getMax('dateTime', 'datetime');

							// empties array of unread messages, because they have been loaded
							messages.unreadMessages = [];

							// updates session storage
							sessionStorage.setObject('messages', messages);

							if (!isEmpty(response.result) && !isEmpty(response.result.length) &&
									response.result.length > 0) {

								var ids = [];

								for (var i = 0; i < response.result.length; i++) {
									ids.push(response.result[i].id);
								}

								// sends update post that sets column 'unread' to false to every message in array
								var time = new Date().toMySQLDatetimeString();
								$.post(createURIText('coupple/ajaxUpdate/messages'), {		// sends message update post
											criteria: {		// selects (by ids) which rows needs to be updated
												id: ids
											},
											data    : {		// update data (column, value) -> sets 'unread' to false
												unread: 0
											},
											time    : time
										},
										function (response) {
											if (response.success) {
												if (response.result) {	// search found messages

												} else {
													console.log('Some error has happened :(');
												}
											} else {
												console.log(response.message ? response.message :
														'Some error has happened :(');
											}
										}, 'json');
							}

						} else {
							console.log('Some error has happened :(');
						}
					} else {
						console.log(response.message ? response.message : 'Some error has happened during :(');
					}
				}, 'json');
	},

	updateNewMessageIndicator: function (newMessagesNum) {
		if (!newMessagesNum) {
			return;
		}

		$('#topMenuBar>#messages span.badge').removeClass('noDisplay').text(newMessagesNum);

	}

};

$(document).ready(function () {

	var today = new Date();

	$('.datepicker').pickadate({	// Default date format in input field i 'mmmm d yyyy'
		selectMonths: true, // Creates a dropdown to control month
		selectYears: 120, // Creates a dropdown of 120 years to control year
		min: new Date(today.getFullYear() - 120, 0,1),		// earliest choosable date is January 1, 120 years ago
		max: today
	});

	$('textarea.autoHeight').on('input recalculateHeight', function () {

		var paddingTop = parseInt($(this).css('padding-top'));
		var paddingBottom = parseInt($(this).css('padding-bottom'));

		if (this.scrollHeight < paddingTop + this.style.height + paddingBottom) {
			this.style.height = "0px";	// Decrease size immediately when deleting
		}

		this.style.height = (this.scrollHeight - paddingBottom - paddingTop) + "px";
	});

	// sets unread messages update frequency
	messagesUpdater.setUnreadMessagesUpdateFrequency(5000);

});