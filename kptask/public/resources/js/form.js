/*

	Form validation & Draft plugins

	Should be able to validate forms (some basic fields for now)

*/

/*
	Should be able to send form data to remote server on window beforeunload event and configurable timeout interval
	
	$(selector).formdraft(
	{
		id: null,			// form id
		name: null,			// form name
		submitUrl: null,	// url to identify form
		formKey: null,		// unique value to identify the form if comming from sm url
		data: {}			// user form input serialized OR JSON stringified
	});

	$('#selector').getform(
	{
		validation: true, // default value, could be overwritten by extend
		validation_config:
		{
			// some validaton func config vars
		},
		draft: true, // default value, could be overwritten by extend
		draft_config:
		{
			// some draft func config vars
		}
	});


*/

/*
	var form = document.getElementById('sl-setup-rigs-form'),
		visibility = form.style;
	
	function trim(str)
	{
		return str.replace (/^\s+|\s+$/g, '');
	}

	if (trim(name_element.value) || '')
	{
		alert ('Please enter your name');
	}
*/


	//////////////////////////////////////////////////////////////////////////////////////////////////////////

	var boolInvalid;

	function checkForm(selector)
	{
		var form = document.querySelector(selector),
			fields = form.querySelectorAll('.required');

		forEachNode(fields, function(index, element)
		{
			switch (element.tagName)
			{
				case 'INPUT':
					validateInput(element);
					break;
				case 'SELECT':
					validateSelect(element);
					break;
				case 'textarea':
					validateTextarea(element);
					break;
				default:
					break;
			}
		});

		if (boolInvalid)
		{
			form.submit();
		}
		else
		{
			return false;
		}
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////

	function validateInput(element)
	{
		switch (element.type)
		{
			case 'text':
		//	case 'hidden':
		//	case 'password':
				boolInvalid = (element.value || '') ? true : false;
				element.classList.toggle('invalid', !boolInvalid);
	
				break;
			case 'radio':
				
				boolInvalid = (element.checked) ? true : false;
				element.classList.toggle('invalid', !boolInvalid);
				
				break;
			case 'checkbox':
				
				boolInvalid = (element.checked) ? true : false;
				element.classList.toggle('invalid', !boolInvalid);
				
				break;
			case 'number':
				//
				break;
			case 'email':
				//
				break;
			default:
				break;
				
		}
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	function validateURL(url)
	{
		var reurl = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/;
		return reurl.test(url);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	function validateEmail(url)
	{
		var reemail = /(.+)@(.+){2,}\.(.+){2,}/;
		return reemail.test(email);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	// http://stackoverflow.com/questions/11661187/form-serialize-javascript-no-framework#11661219
	function serialize(form)
	{
		if (!form || form.nodeName !== "FORM")
		{
				return;
		}

		var i, j, q = [];
		for (i = form.elements.length - 1; i >= 0; i = i - 1)
		{
			if (form.elements[i].name === "")
			{
				continue;
			}
			switch (form.elements[i].nodeName)
			{
				case 'INPUT':
					switch (form.elements[i].type)
					{
						case 'text':
						case 'hidden':
						case 'password':
						case 'button':
						case 'reset':
						case 'submit':
							q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
							break;
						case 'checkbox':
						case 'radio':
							if (form.elements[i].checked)
							{
								q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
							}                                               
							break;
					}
					break;
				case 'file':
					break; 
				case 'TEXTAREA':
					q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
					break;
				case 'SELECT':
					switch (form.elements[i].type)
					{
						case 'select-one':
							q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
							break;
						case 'select-multiple':
							for (j = form.elements[i].options.length - 1; j >= 0; j = j - 1)
							{
								if (form.elements[i].options[j].selected)
								{
									q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].options[j].value));
								}
							}
							break;
					}
					break;
				case 'BUTTON':
					switch (form.elements[i].type)
					{
						case 'reset':
						case 'submit':
						case 'button':
							q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
							break;
					}
					break;
			}
		}

		return q.reverse().join("&");
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////