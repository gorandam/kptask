// forEach method, could be shipped as part of an Object Literal/Module
var forEachNode = function(nodeList, callback, scope)
{
	if (nodeList.length > 0)
	{
		for (var i = 0; i < nodeList.length; i++)
		{
			callback.call(scope, i, nodeList[i]);
		}
	}
	return false;
};

//////////////////////////////////////////////////////////////////////////////////////////////////////////

// I know that augmenting native DOM functions isn't always the best or most popular solution, but this works fine for modern browsers.
// Note: this solution doesn't work for IE 7 and below. For more info about extending the DOM read this article[http://perfectionkills.com/whats-wrong-with-extending-the-dom/].

Element.prototype.remove = function()
{
    this.parentElement.removeChild(this);
};

//////////////////////////////////////////////////////////////////////////////////////////////////////////

NodeList.prototype.remove = HTMLCollection.prototype.remove = function()
{
    for(var i = this.length - 1; i >= 0; i--)
	{
        if (this[i] && this[i].parentElement)
		{
            this[i].parentElement.removeChild(this[i]);
        }
    }
};

//////////////////////////////////////////////////////////////////////////////////////////////////////////

function hide(elements)
{
	elements = elements.length ? elements : [elements];
	for (var index = 0; index < elements.length; index++)
	{
		if (!isHidden(elements[index]))
		{
			elements[index].style.display = 'none';
		}
	}
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////

function show(elements, specifiedDisplay)
{
	elements = elements.length ? elements : [elements];
	for (var index = 0; index < elements.length; index++)
	{
		if (isHidden(elements[index]))
		{
			elements[index].style.display = specifiedDisplay || 'block';
		}
	}
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////

function toggle(elements, specifiedDisplay)
{
	var element, index;

	elements = elements.length ? elements : [elements];
	for (index = 0; index < elements.length; index++)
	{
		element = elements[index];

		if (isHidden(element))
		{
			element.style.display = '';

			// If the element is still hidden after removing the inline display
			if (isHidden(element))
			{
				element.style.display = specifiedDisplay || 'block';
			}
		}
		else
		{
		element.style.display = 'none';
		}
	}
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////

function isHidden(element)
{
	return window.getComputedStyle(element, null).getPropertyValue('display') === 'none';
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////

function getChildren(n, skipMe)
{
    var r = [];
    for ( ; n; n = n.nextSibling ) 
       if ( n.nodeType == 1 && n != skipMe)
          r.push( n );        
    return r;
};

//////////////////////////////////////////////////////////////////////////////////////////////////////////

function getSiblings(n)
{
    return getChildren(n.parentNode.firstChild, n);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////

document.addEventListener('DOMContentLoaded', function ()
{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	// mainboard/index.twig start

    //display rig wells path
    $('.rigMovement').change(function() {
        //list wells for selected rig
        drawRigPath($(this).val());
    });

	// mainboard/index.twig end
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	// header.twig start
	
	// Handle global document clicks to close lists, toggle stuff etc..
    document.addEventListener('click', function(e)
    {
        // Hide main navigation list when clicked elsewhere
        if (((e.target.id != 'sl-nav-more') && (e.target.parentNode.id != 'sl-nav-more')) && (!isHidden(document.querySelector('#sl-nav-more-container'))) )
        {
            hide(document.getElementById('sl-nav-more-container'));
        }
    });

	// Show main navigation list options
	forEachNode(document.querySelectorAll('#sl-nav-more, #sl-nav-more>span'), function(index, element)
	{
		element.addEventListener('click', function(e)
		{
			e.preventDefault();
			e.stopPropagation();
			toggle(document.getElementById('sl-nav-more-container'));
		});
	});

	// header.twig end
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	// user/index.twig start

	var userForm = document.querySelector('#sl-add-form');
	if (userForm !== null)
	{
		userForm.addEventListener('click', function(e)
		{
			document.location = '/user/form';
		});
	}

	// user/index.twig end
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	// login/form.twig start

	var msgs = [
		'Please enter username',
		'Please enter password',
		'Please enter username and password',
		'Invalid username and password'
	];

	var domuname = document.querySelector('#sl-uname');
	var dompass = document.querySelector('#sl-upass');
	var dommsg = document.querySelector('#sl-form-input-msg');
	var domform = document.getElementById('add-edit-form');	

	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	// TODO: validate input format & password strength (???)

	var userName = document.querySelector('#sl-uname');
	if (userName !== null)
	{
		userName.addEventListener('keydown', function(e)
		{
			if (e.keyCode == 13)
			{
				if (dompass.value == '')
				{
					if (this.value == '')
					{
						dommsg.textContent = msgs[2];
						domuname.classList.add('invalid');
						dompass.classList.add('invalid');
					}
					else
					{
						dommsg.textContent = msgs[1];
						domuname.classList.remove('invalid');
						dompass.focus();
					}
				}
				else
				{
					(this.value == '') ? msgs[0] : domform.submit();
				}
			}
		});
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	var userPass = document.querySelector('#sl-upass'); 
	if (userPass !== null)
	{
		userPass.addEventListener('keydown', function(e)
		{
			if (e.keyCode == 13)
			{
				if (domuname.value == '')
				{
					if (this.value == '')
					{
						dommsg.textContent = msgs[2];
						domuname.classList.add('invalid');
						dompass.classList.add('invalid');
					}
					else
					{
						dommsg.textContent = msgs[1];
						dompass.classList.remove('invalid');
						domuname.focus();
					}
				}
				else
				{
					(this.value == '') ? msgs[0] : domform.submit();
				}
			}
		});
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////

	$('.editRigWellRelation').click(function(e) {
		e.preventDefault();
		var text = $(this).siblings('span').text();
		$(this).siblings('.wellSuggest').val(text).attr('type', 'text').focus();
		$(this).siblings('span').hide();
		$(this).hide();
		// @TODO: missing cancel functionality
		// console.log($(this).siblings('.wellSuggest').attr('type', 'text'));
	});

	$( ".wellSuggest" ).autocomplete({
		source: function (request, response) {
			$.ajax({
				url: "/well/search/" + request.term,
				success: function (data) {
					response(data);
				},
				error: function () {
					response([]);
				},
				dataType: 'JSON'
			});
		},
		minLength: 2,
		select: function( e, ui ) {
			$(e.target).val(ui.item.value);
			$(e.target).siblings('input').val(ui.item.id);
		}
	});

	var loginSubmit = document.querySelector('#sl-loginform-submit');
	if (loginSubmit !== null)
	{
		loginSubmit.addEventListener('click', function(e)
		{
			if (domuname.value == '')
			{
				if (this.value == '')
				{
					dommsg.textContent = msgs[2];
					domuname.classList.add('invalid');
					dompass.classList.add('invalid');
				}
				else
				{
					dommsg.textContent = msgs[1];
					dompass.classList.remove('invalid');
					domuname.focus();
				}
			}
			else
			{
				(this.value == '') ? msgs[0] : domform.submit();
			}
		});
	}
	
	// login/form.twig end
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	// user/form.twig end
	
	var addUser = document.querySelector('#sl-adduser');
	if (addUser !== null)
	{
		addUser.addEventListener('click', function(e)
		{
			checkForm('#' + this.id + '-form');
		});
	}

	var cancelUser = document.querySelector('#sl-adduser-cancel');
	if (cancelUser !== null)
	{
		cancelUser.addEventListener('click', function(e)
		{
			window.location = '/user/index';
		});
	}

	// user/form.twig end
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	// map/form.twig start

	var mapCancel = document.querySelector('#sl-cancel');
	if (mapCancel !== null)
	{
		mapCancel.addEventListener('click', function(e)
		{
			document.location = '/map/index';
		});
	}

	var mapCreate = document.querySelector('#sl-createmap');
	if (mapCreate !== null)
	{
		mapCreate.addEventListener('click', function(e)
		{
			checkForm('#sl-createmap-form');
		});
	}

	// map/form.twig end
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	// map/index.twig start
	
	var formCreate = document.querySelector('#sl-create-map');
	if (formCreate != null)
	{
		formCreate.addEventListener('click', function(e)
		{
			document.location = '/map/form';
		});
	}
	
	// map/index/twig end
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	// rig/relations start

	var saveSetup = document.querySelector('#sl-save-setup');
	if (saveSetup !== null)
	{
		saveSetup.addEventListener('click', function(e)
		{
			//checkForm('#sl-save-setup-form');
			document.querySelector('#sl-save-setup-form').submit();
		});
	}

	// rig/relations end
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	// mainboard/importexport start

	var newRig = document.querySelector('#sl-newmsg');
	if (newRig !== null)
	{
		newRig.addEventListener('click', function(e)
		{
			document.querySelector('#sl-popup-modal').style.display = 'block';
			document.querySelector('#sl-popup-msg').style.display = 'block';
		});
	}

	var cancelRig = document.querySelector('#sl-newmsg-cancel');
	if (cancelRig !== null)
	{
		cancelRig.addEventListener('click', function(e)
		{
			document.querySelector('#sl-popup-modal').style.display = 'none';
			document.querySelector('#sl-popup-msg').style.display = 'none';
		});
	}

	var exportData = document.querySelector('#sl-export');
	if (exportData !== null)
	{
		exportData.addEventListener('click', function(e)
		{
			document.querySelector('#sl-popup-modal').style.display = 'block';
			document.querySelector('#sl-popup-data').style.display = 'block';
		});
	}
	
	var cancelExport = document.querySelector('#sl-export-cancel');
	if (cancelExport !== null)
	{
		cancelExport.addEventListener('click', function(e)
		{
			document.querySelector('#sl-popup-modal').style.display = 'none';
			document.querySelector('#sl-popup-data').style.display = 'none';
		});
	}

	// mainboard/importexport end
	//////////////////////////////////////////////////////////////////////////////////////////////////////////

	// Bind remove well buttons clicks
	var usersDelete = document.querySelectorAll('.sl-user-delete');
	forEachNode(usersDelete, function(index, element)
	{
		element.addEventListener('click', function(e)
		{
			var remove = confirm("Are you sure?");
			if (remove) {
				document.querySelector('#sl-user-delete-form > input').setAttribute('value', this.id);
				document.querySelector('#sl-user-delete-form').submit();

				this.parentNode.parentNode.remove();
			}
		});
	});
});
