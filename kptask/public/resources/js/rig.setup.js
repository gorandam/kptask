document.addEventListener('DOMContentLoaded', function ()
{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////

	var wellBoxes = document.querySelectorAll('.sl-well-box');

	// Add new well popup opener
	document.getElementById('sl-new-well').addEventListener('click', function(e)
	{
		document.getElementById('sl-popup-modal').style.display = 'block';
		document.getElementById('sl-popup-well').style.display = 'block';

		document.querySelector('#sl-popup-well').querySelector('#sl-well-id').innerText = Number(wellBoxes[wellBoxes.length - 1].children[0].innerText) + 1;
	});
	// Add new well popup close
	document.getElementById('sl-well-cancel').addEventListener('click', function(e)
	{
		document.getElementById('sl-popup-modal').style.display = 'none';
		document.getElementById('sl-popup-well').style.display = 'none';
	});

	// Add new well popup submit
	document.getElementById('sl-add-well').addEventListener('click', function(e)
	{
		checkForm('#sl-add-well-form');
	});

	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	// EDIT WELL POPUP START

	// Bind edit popup opener clicks, popuplate popup fields with proper well data
	var wellsEdit = document.querySelectorAll('.sl-well-edit');
	forEachNode(wellsEdit, function(index, element)
	{
		element.addEventListener('click', function(e)
		{
			var arrWell = this.getAttribute('id').split('|'),
				wellName = arrWell[0],					//this.parentNode.children[0].innerText,
				wellId = arrWell[1];					//this.parentNode.parentNode.children[0].innerText;
			document.getElementById('sl-popup-modal').style.display = 'block';

			var popup = document.querySelector('#sl-popup-edit-well');
			popup.style.display = 'block';
			popup.querySelector('#sl-well-name').setAttribute('value', wellName);
			popup.querySelector('#sl-well-name').setAttribute('name', 'label');
			popup.querySelector('#sl-well-display').innerText = wellId;
			popup.querySelector('#sl-well-id').setAttribute('value', wellId);
			popup.querySelector('#sl-well-id').setAttribute('name', 'wellId');
		});
	});
	// Edit well popup close
	document.getElementById('sl-well-edit-cancel').addEventListener('click', function(e)
	{
		document.getElementById('sl-popup-modal').style.display = 'none';
		document.getElementById('sl-popup-edit-well').style.display = 'none';
	});
	// Edit well popup submit
	document.getElementById('sl-well-edit').addEventListener('click', function(e)
	{
		checkForm('#' + this.id + '-form');
	});

	// EDIT WELL POPUP END
	//////////////////////////////////////////////////////////////////////////////////////////////////////////

	// Add hover on rows, display action buttons
	forEachNode(wellBoxes, function(index, element)
	{
		element.addEventListener('mouseenter', function(e)
		{
			element.querySelector('.sl-well-edit').style.display = 'block';
			element.querySelector('.sl-well-delete').style.display = 'block';
		});
		element.addEventListener('mouseleave', function(e)
		{
			element.querySelector('.sl-well-edit').style.display = 'none';
			element.querySelector('.sl-well-delete').style.display = 'none';
		});
	});

	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	// DELETE WELL ACTION

	// Bind remove well buttons clicks
	var wellsDelete = document.querySelectorAll('.sl-well-delete');
	forEachNode(wellsDelete, function(index, element)
	{
		element.addEventListener('click', function(e)
		{
			var remove = confirm("Are you sure?");
			if (remove) {
				document.querySelector('#sl-well-delete-form > input').setAttribute('value', this.id);
				document.querySelector('#sl-well-delete-form').submit();

				this.parentNode.parentNode.remove();
			}
		});
	});

	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	// ADD NEW RIG POPUP START

	var rigBoxes = document.querySelectorAll('.sl-rig-box');

	// Add new rig popup opener
	document.getElementById('sl-new-rig').addEventListener('click', function(e)
	{
		document.getElementById('sl-popup-modal').style.display = 'block';
		document.getElementById('sl-popup-rig').style.display = 'block';

		document.querySelector('#sl-popup-rig').querySelector('#sl-rig-id').innerText = Number(rigBoxes[rigBoxes.length - 1].children[0].innerText) + 1;
	});
	// Add new rig popup close
	document.getElementById('sl-rig-cancel').addEventListener('click', function(e)
	{
		document.getElementById('sl-popup-modal').style.display = 'none';
		document.getElementById('sl-popup-rig').style.display = 'none';
	});
	// Add new rig popup submit
	document.getElementById('sl-add-rig').addEventListener('click', function(e)
	{
		checkForm('#' + this.id + '-form');
	});

	// ADD NEW RIG POPUP END
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	// EDIT RIG POPUP START

	// Bind edit popup opener clicks, popuplate popup fields with proper rig data
	var rigsEdit = document.querySelectorAll('.sl-rig-edit');
	forEachNode(rigsEdit, function(index, element)
	{
		element.addEventListener('click', function(e)
		{
			var arrRig = this.getAttribute('id').split('|'),
				rigName = arrRig[0],				//this.parentNode.children[0].innerText,
				rigId = arrRig[1];					//this.parentNode.parentNode.children[0].innerText;
			document.getElementById('sl-popup-modal').style.display = 'block';

			var popup = document.querySelector('#sl-popup-edit-rig');
			popup.style.display = 'block';
			popup.querySelector('#sl-rig-name').setAttribute('value', rigName);
			popup.querySelector('#sl-rig-name').setAttribute('name', 'label');
			popup.querySelector('#sl-rig-display').innerText = rigId;
			popup.querySelector('#sl-rig-id').setAttribute('value', rigId);
			popup.querySelector('#sl-rig-id').setAttribute('name', 'rigId');
		});
	});
	// Edit rig popup close
	document.getElementById('sl-rig-edit-cancel').addEventListener('click', function(e)
	{
		document.getElementById('sl-popup-modal').style.display = 'none';
		document.getElementById('sl-popup-edit-rig').style.display = 'none';
	});
	// Edit rig popup submit
	document.getElementById('sl-rig-edit').addEventListener('click', function(e)
	{
		checkForm('#' + this.id + '-form');
	});

	// EDIT RIG POPUP END
	//////////////////////////////////////////////////////////////////////////////////////////////////////////

	// Add hover on rows, display action buttons
	forEachNode(rigBoxes, function(index, element)
	{
		element.addEventListener('mouseenter', function(e)
		{
			element.querySelector('.sl-rig-edit').style.display = 'block';
			element.querySelector('.sl-rig-delete').style.display = 'block';
		});
		element.addEventListener('mouseleave', function(e)
		{
			element.querySelector('.sl-rig-edit').style.display = 'none';
			element.querySelector('.sl-rig-delete').style.display = 'none';
		});
	});

	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	// DELETE RIG ACTION

	// Bind remove rig buttons clicks
	var rigsDelete = document.querySelectorAll('.sl-rig-delete');
	forEachNode(rigsDelete, function(index, element)
	{
		element.addEventListener('click', function(e)
		{
			var remove = confirm("Are you sure?");
			if (remove) {
				document.querySelector('#sl-rig-delete-form > input').setAttribute('value', this.id);
				document.querySelector('#sl-rig-delete-form').submit();

				this.parentNode.parentNode.remove();
			}
		});
	});

	//
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
});

//////////////////////////////////////////////////////////////////////////////////////////////////////////

function ajax(url, data)
{
	// Send Ajax POST request
	var request = new XMLHttpRequest();
	request.open('POST', url, true);
	request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
	request.send(data);
}