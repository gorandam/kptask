(function(window, document, undefined) {
	/*
	 * If you would like an application-wide config, change these defaults.
	 * Otherwise, use the setMessage() function to configure form specific messages.
	 */

	var defaults =
	{
		messages:
		{
			required: 'The %s field is required.',
			matches: 'The %s field does not match the %s field.',
			"default": 'The %s field is still set to default, please change.',
			valid_email: 'The %s field must contain a valid email address.',
			valid_emails: 'The %s field must contain all valid email addresses.',
			min_length: 'The %s field must be at least %s characters in length.',
			max_length: 'The %s field must not exceed %s characters in length.',
			exact_length: 'The %s field must be exactly %s characters in length.',
			greater_than: 'The %s field must contain a number greater than %s.',
			less_than: 'The %s field must contain a number less than %s.',
			alpha: 'The %s field must only contain alphabetical characters.',
			alpha_numeric: 'The %s field must only contain alpha-numeric characters.',
			alpha_dash: 'The %s field must only contain alpha-numeric characters, underscores, and dashes.',
			numeric: 'The %s field must contain only numbers.',
			integer: 'The %s field must contain an integer.',
			decimal: 'The %s field must contain a decimal number.',
			is_natural: 'The %s field must contain only positive numbers.',
			is_natural_no_zero: 'The %s field must contain a number greater than zero.',
			valid_ip: 'The %s field must contain a valid IP.',
			is_file_type: 'The %s field must contain only %s files.',
			valid_url: 'The %s field must contain a valid URL.',
		},
		callback: function(errors)
		{
			//
		}
	};
	
	/*
	 * Define the regular expressions that will be used
	 */

	var ruleRegex = /^(.+?)\[(.+)\]$/,
		numericRegex = /^[0-9]+$/,
		integerRegex = /^\-?[0-9]+$/,
		decimalRegex = /^\-?[0-9]*\.?[0-9]+$/,
		emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/,
		alphaRegex = /^[a-z]+$/i,
		alphaNumericRegex = /^[a-z0-9]+$/i,
		alphaDashRegex = /^[a-z0-9_\-]+$/i,
		naturalRegex = /^[0-9]+$/i,
		naturalNoZeroRegex = /^[1-9][0-9]*$/i,
		ipRegex = /^((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){3}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})$/i,
		numericDashRegex = /^[\d\-\s]+$/,
		urlRegex = /^((http|https):\/\/(\w+:{0,1}\w*@)?(\S+)|)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?$/;

	var validator = function(formNameOrNode, fields, callback)
	{
		this.callback = callback || defaults.callback;
		this.errors = [];
		this.fields = {};
		this.form = this._formByNameOrNode(formNameOrNode) || {};
		this.messages = {};
		this.handlers = {};
		this.conditionals = {};

		for (var i = 0, fieldLength = fields.length; i < fieldLength; i++)
		{
			var field = fields[i];

			// If passed in incorrectly, we need to skip the field.
			if ((!field.name && !field.names) || !field.rules)
			{
				console.warn('validate.js: The following field is being skipped due to a misconfiguration:');
				console.warn(field);
				console.warn('Check to ensure you have properly configured a name and rules for this field');
				continue;
			}

			/*
			 * Build the master fields array that has all the information needed to validate
			 */

			if (field.names)
			{
				for (var j = 0, fieldNamesLength = field.names.length; j < fieldNamesLength; j++)
				{
					this._addField(field, field.names[j]);
				}
			}
			else
			{
				this._addField(field, field.name);
			}
		}

		/*
		 * Attach an event callback for the form submission
		 */

		var _onsubmit = this.form.onsubmit;

		this.form.onsubmit = (function(that)
		{
			return function(evt)
			{
				try
				{
					return that._validateForm(evt) && (_onsubmit === undefined || _onsubmit());
				}
				catch(e)
				{}
			};
		})(this);
	},

	attributeValue = function (element, attributeName)
	{
		var i;
		if ((element.length > 0) && (element[0].type === 'radio' || element[0].type === 'checkbox'))
		{
			for (i = 0, elementLength = element.length; i < elementLength; i++)
			{
				if (element[i].checked)
				{
					return element[i][attributeName];
				}
			}
			return;
		}
		return element[attributeName];
	};
	
	/*
	 * @private
	 * Adds a file to the master fields array
	 */

	validator.prototype._addField = function(field, nameValue) 
	{
		this.fields[nameValue] =
		{
			name: nameValue,
			display: field.display || nameValue,
			rules: field.rules,
			depends: field.depends,
			id: null,
			element: null,
			type: null,
			value: null,
			checked: null
		};
	};
	
	/*
	 * @private
	 * Determines if a form dom node was passed in or just a string representing the form name
	 */

	validator.prototype._formByNameOrNode = function(formNameOrNode)
	{
		return (typeof formNameOrNode === 'object') ? formNameOrNode : document.forms[formNameOrNode];
	};
	
	validator.prototype._validateForm = function(evt)
	{
		this.errors = [];

		for (var key in this.fields)
		{
			if (this.fields.hasOwnProperty(key))
			{
				var field = this.fields[key] || {},
					element = this.form[field.name];

				if (element && element !== undefined)
				{
					field.id = attributeValue(element, 'id');
					field.element = element;
					field.type = (element.length > 0) ? element[0].type : element.type;
					field.value = attributeValue(element, 'value');
					field.checked = attributeValue(element, 'checked');

					/*
					 * Run through the rules for each field.
					 * If the field has a depends conditional, only validate the field
					 * if it passes the custom function
					 */

					if (field.depends && typeof field.depends === "function")
					{
						if (field.depends.call(this, field))
						{
							this._validateField(field);
						}
					}
					else if (field.depends && typeof field.depends === "string" && this.conditionals[field.depends])
					{
						if (this.conditionals[field.depends].call(this,field))
						{
							this._validateField(field);
						}
					}
					else
					{
						this._validateField(field);
					}
				}
			}
		}

		if (typeof this.callback === 'function')
		{
			this.callback(this.errors, evt);
		}

		if (this.errors.length > 0)
		{
			if (evt && evt.preventDefault)
			{
				evt.preventDefault();
			}
			else if (event)
			{
				// IE uses the global event variable
				event.returnValue = false;
			}
		}

		return true;
	};
	
	/*
	 * @private
	 * Looks at the fields value and evaluates it against the given rules
	 */

	validator.prototype._validateField = function(field)
	{
        var i, j,
            rules = field.rules.split('|'),
            indexOfRequired = field.rules.indexOf('required'),
            isEmpty = (!field.value || field.value === '' || field.value === undefined);

        /*
         * Run through the rules and execute the validation methods as needed
         */

        for (i = 0, ruleLength = rules.length; i < ruleLength; i++)
		{
            var method = rules[i],
                param = null,
                failed = false,
                parts = ruleRegex.exec(method);

            /*
             * If this field is not required and the value is empty, continue on to the next rule unless it's a callback.
             * This ensures that a callback will always be called but other rules will be skipped.
             */

            if (indexOfRequired === -1 && method.indexOf('!callback_') === -1 && isEmpty)
			{
                continue;
            }

            /*
             * If the rule has a parameter (i.e. matches[param]) split it out
             */

            if (parts)
			{
                method = parts[1];
                param = parts[2];
            }

            if (method.charAt(0) === '!')
			{
                method = method.substring(1, method.length);
            }

            /*
             * If the hook is defined, run it to find any validation errors
             */

            if (typeof this._hooks[method] === 'function')
			{
                if (!this._hooks[method].apply(this, [field, param]))
				{
                    failed = true;
                }
            } else if (method.substring(0, 9) === 'callback_')
			{
                // Custom method. Execute the handler if it was registered
                method = method.substring(9, method.length);

                if (typeof this.handlers[method] === 'function')
				{
                    if (this.handlers[method].apply(this, [field.value, param, field]) === false)
					{
                        failed = true;
                    }
                }
            }

            /*
             * If the hook failed, add a message to the errors array
             */

            if (failed)
			{
                // Make sure we have a message for this rule
                var source = this.messages[field.name + '.' + method] || this.messages[method] || defaults.messages[method],
                    message = 'An error has occurred with the ' + field.display + ' field.';

                if (source)
				{
                    message = source.replace('%s', field.display);

                    if (param)
					{
                        message = message.replace('%s', (this.fields[param]) ? this.fields[param].display : param);
                    }
                }

                var existingError;
                for (j = 0; j < this.errors.length; j += 1)
				{
                    if (field.id === this.errors[j].id)
					{
                        existingError = this.errors[j];
                    }
                }

                var errorObject = existingError ||
				{
                    id: field.id,
                    display: field.display,
                    element: field.element,
                    name: field.name,
                    message: message,
                    messages: [],
                    rule: method
                };
                errorObject.messages.push(message);
                if (!existingError) this.errors.push(errorObject);
            }
        }
    };

	/*
	 * @private
	 * Object containing all of the validation hooks
	 */

	validator.prototype._hooks =
	{
		required: function(field)
		{
			var value = field.value;

			if ((field.type === 'checkbox') || (field.type === 'radio'))
			{
				return (field.checked === true);
			}

			return (value !== null && value !== '');
		},

		"default": function(field, defaultName)
		{
			return field.value !== defaultName;
		},
		
		// ...
	};

	window.validator = validator;
})(window, document);