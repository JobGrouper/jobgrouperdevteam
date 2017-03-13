var jg = jg || {};

/**
 * Adds all missing properties from second obj to first obj
 */
jg.extend = function(first, second){
    for (var prop in second){
        first[prop] = second[prop];
    }
};

jg.isArray = function( variable ) {
	if (Object.prototype.toString.call( variable ) === '[object Array]')
	  return true;
	else
	  return false;
}

jg.ElementMap = function(element) {

	var child = element.firstChild;

	while (child && child != null) {

		if (child.nodeType == 1) {

			childClass = child.className;
			childId = child.id;

			// HANDLE ID
			if (childId !== "") {
				this._addChild(childId, child);
			}

			// HANDLE CLASS
			//
			// Cases:
			// 0) No class name
			// 1) Single class (no spaces)
			// 2) Multiple classes
			//
			if (childClass !== "") {
				class_split = childClass.split(' ');

				if (class_split.length == 1)
				  this._addChild(childClass, child);
				else {

					for(var i = 0; i < class_split.length; i++) {
					  this._addChild(class_split[i], child);
					}
				}
			}

			// check to see if this has children
			//   if so, make recursive call
			if (child.childNodes.length > 0) {
				//this.extendMap( jg.mapChildren( child ) );
				//

				// VERSION 1.0
				var childMap = new jg.ElementMap( child );
				this._extendMap( childMap );
			} // endif

		} // endif

		child = child.nextSibling;
	}
};

jg.ElementMap.prototype = {

	// Registers an element in the map
	//  should skip functions
	//  
	//  Makes array if not already an array
	//
	_addChild : function(key, child) {

		if (this[key] !== undefined) {

			// check if is array
			if ( jg.isArray( this[key]) ) {
			  this[key].push( child );
			}
			else { // make it an array

				// create array and fill it with values
				var array = [];
				array.push(this[key]);
				array.push(child);

				// replace
				this[key] = array;
			}
		}
		else {
		  this[key] = child;
		}
	},
	// Can extend this map with other maps
	//
	//   Used primarily to drill down into other elements
	//      But can also cross-pollinate with other 
	//      elements across DOM
	_extendMap : function(map) {

		for (var prop in map){

			// if not in 
			if (!this[prop]) {
			  this[prop] = map[prop];
			}
			else {
				
				if (jg.isArray(this[prop])) {

					// concat if array, push if not
					if (jg.isArray(map[prop])) {
					  this[prop].concat( map[prop] );
					}
					else {
					  this[prop].push( map[prop] );
					}

				} 
				else if (typeof this[prop] === 'function') {
				  continue;
				}
				else { // make it an array
				  
					var arr = [];
					arr.push( this[prop] );

					// concat if array, push if not
					if (jg.isArray(map[prop])) {
					  arr.concat( map[prop] );
					}
					else {
					  arr.push( map[prop] );
					}

					this[prop] = arr;

				} // end if

			} // end if 
		}
	}
};

jg.Autoloader = function(func) {

	var prev_load = window.onload;

	if (prev_load == undefined) {
		window.onload = func;
	}
	else {
		window.onload = function() {
			// execute first function
			if (prev_load) {
			  prev_load();
			}
			// execute new function
			func();
		}
	}
};

jg.BuyerAdjuster = function(user_options) {

	this._options = {
		root : null,
		modal : {
			root: null,
			trigger: null
		},
		admin:null,
		admin_from_request:null,
		request:null,
		is_modal : false
	};

	jg.extend(this._options, user_options);

	this._modal_root = null;
	this._modal_trigger = null;

	if (this._options.modal.root != null) {
		this._options['is_modal'] = true;
		this._modal_root = this._options.modal['root'];
		this._modal_trigger = this._options.modal['trigger'];
	}

	this._map = new jg.ElementMap( this._options['root'] );
	this._job_id = this._map['ba_job_id_field'].value;
	this._request_id = null;

	if (this._options['admin_from_request']) {
	  this._request_id = this._map['ba_request_id_field'].value;
	}

	this._message_element = this._map['ba-message-field'];

	this._max_value = this._map['max-input'].value;
	this._min_value = this._map['min-input'].value;
	this._current_max = parseInt( this._map['current-max'].innerText );
	this._current_min = parseInt( this._map['current-min'].innerText );
	this._sales_count = this._map['sales-count'].innerText;
	this._init();
};

jg.BuyerAdjuster.prototype = {

	_init: function() {

		var self = this;

		this._enableButtons();

		// REGISTER EVENTS

		// ToDoo: 
		// - Adjust font when close to a limit
		// - try and make set value functions to clean up
		// - Keep track of whether or not job has already started
		// -	- if so...
		// -	-	- minimum must not go above level of current buyers
		//

		if (this._options['is_modal']) {

			this._modal_trigger.onclick = function() {
				$( self._modal_root ).fadeIn("fast");
			}
		}

		this._map['min-input'].onchange = function() {

			// Minimum must not be:
			// - less than one
			// - less greater than max
			//
			if (parseInt(this.value) < 1) {
				alert('We have to have at least one buyer');
				self._min_value = 1;
				self._map['min-input'].value = self._min_value;
			}
			else if( parseInt(this.value) > parseInt(self._max_value) ) {
				alert('The minimum number of buyers cannot be greater than the maximum');
				self._min_value = self._max_value;
				self._map['min-input'].value = self._max_value;
			}
			else {
				self._min_value = this.value;
			}
		}

		this._map['max-input'].onchange = function() {

			// Max must not be:
			// - less than one
			// - less than minimum
			// - less than the total number of sales
			//
			if ( parseInt(this.value) < 1) {
				alert('We have to have at least one buyer');
				self._max_value = 1;
				self._map['max-input'].value = self._max_value;
			}
			else if( parseInt(this.value) < parseInt(self._sales_count) ) {
				alert('The maximum number of buyers can\'t be less than the current number of sales');
				self._max_value = self._sales_count;
				self._map['max-input'].value = self._max_value;
			}
			else if( parseInt(this.value) < parseInt(self._min_value)) {

				alert('The maximum number of buyers cannot be less than the minimum');
				self._max_value = self._min_value;
				self._map['max-input'].value = self._max_value;
			}
			else {
				self._max_value = this.value;
			}
		}

		this._map['min-down'].onclick = function(e) {

			e.preventDefault();

			if ( parseInt(self._min_value) == 1) {
				alert('We have to have at least one buyer');
			}
			else {
				// Alert that this will start the job
				if ((self._min_value - 1) == self._sales_count ) {
					alert('Warning: Setting ' + self._sales_count + ' as the minimum will cause the job to start.');
				}

				self._min_value--;
				self._map['min-input'].value = self._min_value;
			}
		}

		this._map['min-up'].onclick = function(e) {

			e.preventDefault();

			if ( parseInt(self._min_value) == parseInt(self._max_value) ) {
				alert('The minimum number of buyers cannot be greater than the maximum');
			}
			else {
				self._min_value++;
				self._map['min-input'].value = self._min_value;
			}
		}

		this._map['max-down'].onclick = function(e) {

			e.preventDefault();

			if ( parseInt(self._max_value) == parseInt(self._min_value) ) {
				alert('The maximum number of buyers cannot be greater than the minimum');
			}
			else if( parseInt(self._max_value) == parseInt(self._sales_count) ) {
				alert('The maximum number of buyers can\'t be less than the current number of sales');
			}
			else {
				self._max_value--;
				self._map['max-input'].value = self._max_value;
			}
		}

		this._map['max-up'].onclick = function(e) {

			e.preventDefault();

			self._max_value++;
			self._map['max-input'].value = self._max_value;
		}

		if (this._options['admin'] || this._options['admin_from_request']) {

			this._map['buyer-adjuster-start-work-button'].onclick = function(e) {
				e.preventDefault();
				self._startWorkNow();
			}
			this._map['buyer-adjuster-submit-button'].onclick = function(e) {
				e.preventDefault();
				self._makeBuyerAdjustment();
			}
		}

		if (this._options['admin_from_request']) {

			this._map['buyer-adjuster-deny-request-button'].onclick = function(e) {
				e.preventDefault();
				self._denyBuyerAdjustment();

			}
		}

		if (this._options['request']) {

			this._map['request-start-work-button'].onclick = function(e) {
				e.preventDefault();
				self._requestWorkNow();
			}
			this._map['request-submit-button'].onclick = function(e) {
				e.preventDefault();
				self._requestBuyerAdjustment();
			}
		}
	},
	_requestWorkNow: function() {

		var self = this;
		var post_data = $( this._map['buyer-adjuster-form'] ).serialize();

		/*
		if (this._sales_count == 0) {
		   self._setMessage('Cannot start work without buyers', 'error');
		   return;
		}
		*/

		$.ajax({
			type: "POST",
			url: "/api/requestStartWorkNow",
			data: post_data,
			datatype: "json",
			success: function(response) {

				if(response.status == 'X') {
		   		   self._setMessage(response.message, 'error');
				} else {
					self._setMessage('Your request has gone through', 'success'); 
					self._disableButtons();
					self._disableJobStuff();
				}
			}
		});
	},
	_requestBuyerAdjustment: function() {

		var self = this;
		var post_data = $( this._map['buyer-adjuster-form'] ).serialize();

		$.ajax({
			type: "POST",
			url: "/api/buyerAdjustmentRequest",
			data: post_data,
			datatype: "json",
			success: function(response) {

				if(response.status == 0) {

				} else {
					self._disableButtons();
					self._disableJobStuff();
				}
			}
		});
	},
	_startWorkNow: function() {

		var self = this;
		var post_data = $( this._map['buyer-adjuster-form'] ).serialize();

		if (this._sales_count == 0) {
		   self._setMessage('Cannot start work without buyers', 'error');
		   return;
		}

		$.ajax({
			type: "POST",
			url: "/api/startWorkNow",
			data: post_data,
			datatype: "json",
			success: function(response) {

				if(response.status == 'X') {
		   			self._setMessage(response.message, 'error');
				} else {
		   			self._setMessage(response.message, 'success');
					self._disableButtons();
				}
			}
		});
	},
	_makeBuyerAdjustment: function() {

		var self = this;
		var post_data = $( this._map['buyer-adjuster-form'] ).serialize();
			
		if (!this._hasChanged()) {
		   this._setMessage('No changes have been made.', 'error');
		   return;
		}

		$.ajax({
			type: "POST",
			url: "/api/buyerAdjustment",
			data: post_data,
			datatype: "json",
			success: function(response) {

				if(response.status == 0) {
				  
					var message = self._handleValidatorError(response);
					self._setMessage(message, 'error');

				} else {
				  self._setMessage('The adjustment has gone through', 'success');
				  self._disableButtons();
				}
			}
		});
	},
	_denyBuyerAdjustment: function() {

		var self = this;
		var post_data = '';

		$.ajax({
			type: "POST",
			url: "/api/denyBuyerAdjustmentRequest/" + this._request_id,
			data: post_data,
			datatype: "string",
			success: function(response) {

				if(response.status == 0) {
				  self._setMessage('Server error', 'error');
				} else {
				  self._setMessage('Adjustment denied', 'success');
				  self._disableButtons();
				}
			}
		});
	},
	_enableButtons: function() {
		
		if (this._options['request']) {
			$( this._map['request-start-work-button'] ).prop('disabled', false);
			$( this._map['request-submit-button'] ).prop('disabled', false);
		}

		if (this._options['admin'] || this._options['admin_from_request']) {
			$( this._map['buyer-adjuster-start-work-button'] ).prop('disabled', false);
			$( this._map['buyer-adjuster-submit-button'] ).prop('disabled', false);
		}

		if (this._options['admin_from_request']) {
			$( this._map['buyer-adjuster-deny-request-button'] ).prop('disabled', false);
		}
	},
	_disableButtons: function() {

		if (this._options['request']) {
			$( this._map['request-start-work-button'] ).prop('disabled', true);
			$( this._map['request-submit-button'] ).prop('disabled', true);
		}

		if (this._options['admin'] || this._options['admin_from_request']) {
			$( this._map['buyer-adjuster-start-work-button'] ).prop('disabled', true);
			$( this._map['buyer-adjuster-submit-button'] ).prop('disabled', true);
		}

		if (this._options['admin_from_request']) {
			$( this._map['buyer-adjuster-deny-request-button'] ).prop('disabled', true);
		}
	},
	_setMessage: function(message, style) {

		var message_class = null;
		if (style == 'success')
		  message_class = 'text-success';
		else if (style == 'error')
		  message_class = 'text-danger';

		$( this._message_element ).hide();

		$( this._message_element ).addClass( message_class );
		$( this._message_element ).text( message );

		$( this._message_element ).fadeIn('fast').delay(3000).fadeOut('slow');
	},
	_disableJobStuff: function() {

		// class: nostyle -> pending
		// message: Your request is pending
		$( '#buyer-adjustment-alert-button' ).removeClass('nostyleyet');
		$( '#buyer-adjustment-alert-button' ).addClass('pending');
		$( '#buyer-adjustment-alert-button' ).text('Your request is pending');
		$( this._modal_root ).fadeOut("fast");
	},
	_hasChanged: function() {

		if (this._max_value == this._current_max && 
			this._min_value == this._current_min) {

			return false;
		}
		
		return true;
	},
	_handleValidatorError: function(responseObject) {

		var keys = Object.keys(responseObject.data);
		var message = '';
		
		for (var i=0; i<keys.length; i++) {
			message += responseObject.data[ keys[i] ];
		}

		return message;
	}
};
