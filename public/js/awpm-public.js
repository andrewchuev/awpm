jQuery(document).ready(function ($) {
	'use strict';

	$('#project_manager').select2({ placeholder: 'Project manager' }).val('').trigger('change');
	$('#profile').select2({ placeholder: 'Profile' }).val('').trigger('change');
	$('#country').select2({ placeholder: 'Country' }).val('').trigger('change');
	$('#developers').select2({ placeholder: 'Developers' }).val('').trigger('change');

	$('#priority').on('change', function () {
		//$('#priority').css('background-color', $(this).val()).trigger('change');
	});

	$('#add_project').on('click', function () {
		var project = {
			project_title: $('#project_title').val(),
			project_manager_id: $('#project_manager').select2("val"),
			profile_id: $('#profile').select2("val"),
			country: $('#country').select2("val"),
			developers: $('#developers').select2("val").toString(),
			priority_color: $('#priority').val(),
			deadline: $("#deadline").val(),
			project_type: $('#project_type').val(),
			cost: $('#cost').val(),
			communication: $('#communication').val(),
			notes: $('#notes').val()
		};

		$.ajax({
			url: ajaxurl,
			type: "POST",
			data: {	action: 'add_project', project: project },
			success: function (response) {
				console.log(response);
				get_projects();
			}
		});
	});

	$('#save_project').on('click', function () {
		var project = {
			project_id: $('#project_id').val(),
			project_title: $('#project_title').val(),
			project_manager_id: $('#project_manager').select2("val"),
			profile_id: $('#profile').select2("val"),
			country: $('#country').select2("val"),
			developers: $('#developers').select2("val").toString(),
			priority_color: $('#priority').val(),
			deadline: $("#deadline").val(),
			project_type: $('#project_type').val(),
			cost: $('#cost').val(),
			communication: $('#communication').val(),
			notes: $('#notes').val()
		};

		$.ajax({
			url: ajaxurl,
			type: "POST",
			data: {	action: 'save_project', project: project },
			success: function (response) {
				console.log(response);
				get_projects();
			}
		});
	});

	function get_projects( order ) {
		$('#project_inputs select, #project_inputs input, #project_inputs textarea').val('').trigger('change');
		//$('#project_id').val('').trigger('change');
		$.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				action: 'get_projects', order: order
			},
			success: function (response) {
				var response = $.parseJSON(response);
				var project, developers, devids;
				var th = '<tr>' +
					'<th data-order="project_title">Title</th>' +
					'<th data-order="project_manager">PM</th>' +
					'<th data-order="profile_id">Profile</th>' +
					'<th data-order="country">Country</th>' +
					'<th data-order="developers">Developers</th>' +
					'<th data-order="priority_color">Priority</th>' +
					'<th data-order="deadline">Deadline</th>' +
					'<th data-order="project_type">Type</th>' +
					'<th data-order="cost">Cost</th>' +
					'<th data-order="communication">Communication</th>' +
					'<th data-order="notes">Notes</th>' +
					'</tr>';
				$('#projects_list').empty();
				$('#projects_list').append(th);
				response.forEach(function (entry) {
					developers = entry.devs_names;
					project = '<tr class="project" data-id="' + entry.project_id + '">' +
						'<td class="project_title" >' + entry.project_title + '</td>' +
						'<td>' + entry.project_manager + '</td>' +
						'<td>' + entry.profile_name + '</td>' +
						'<td>' + entry.country + '</td>' +
						'<td>' + developers + '</td>' +
						'<td style="background-color: '+ entry.priority_color +'"></td>' +
						'<td>' + entry.deadline + '</td>' +
						'<td>' + entry.project_type + '</td>' +
						'<td>' + entry.cost + '</td>' +
						'<td>' + entry.communication + '</td>' +
						'<td>' + entry.notes + '</td>' +
						'</tr>';
					$('#projects_list').append(project);
				});
			}
		});
	}

	get_projects();

	$('#projects_list').on('click', '.project', function () {
		var project_id = $(this).data('id');
		$.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				action: 'get_projects', project_id: project_id
			},
			success: function (response) {
				$('#project_inputs').show();
				var response = $.parseJSON(response);
				var project = response[0];

				$('#project_id').val(project.project_id).trigger('change');
				$('#project_title').val(project.project_title);
				$('#project_manager').val(project.project_manager_id).trigger('change');
				$('#profile').val(project.profile_id).trigger('change');
				$('#country').val(project.country).trigger('change');
				$('#developers').val(project.developers.split(',')).trigger('change');
				$('#priority').val(project.priority_color);
				$('#deadline').val(project.deadline);
				$('#project_type').val(project.project_type);
				$('#cost').val(project.cost);
				$('#communication').val(project.communication);
				$('#notes').val(project.notes);
			}
		});
	});

	$('#projects_list').on('click', 'th', function () {
		var order = $(this).data('order');
		console.log(order);
		get_projects( order );
	});

	$('#project_id').on('change', function () {
		console.log('project_id change ', $(this).val());

		if ($(this).val() == '') {
			$('#add_project_wrap').show();
			$('#edit_project_wrap').hide();
		} else {
			$('#add_project_wrap').hide();
			$('#edit_project_wrap').show();
		}
	});

	$('.cancel_project').on('click', function () {
		$('#project_id').val('').trigger('change');
		$('#project_inputs').hide();
		$('#project_inputs select, #project_inputs input, #project_inputs textarea').val('').trigger('change');

	});

	$('#delete_project').on('click', function () {
		if (confirm('Delete project?')) {
			var project_id = $('#project_id').val();
			$.ajax({
				url: ajaxurl,
				type: "POST",
				data: {	action: 'delete_project', project_id: project_id },
				success: function (response) {
					console.log(response);
					get_projects();
				}
			});
		}
	});

	$('#new_project').on('click', function () {
		$( '#project_inputs' ).slideToggle( "slow", function() {
			// Animation complete.
		});

	});
});

