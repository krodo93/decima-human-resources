@extends('layouts.base')

@section('container')
{!! Form::hidden('hr-em-new-action', null, array('id' => 'hr-em-new-action')) !!}
{!! Form::hidden('hr-em-edit-action', null, array('id' => 'hr-em-edit-action', 'data-content' => Lang::get('decima-human-resources::employee-management.editHelpText'))) !!}
{!! Form::hidden('hr-em-remove-action', null, array('id' => 'hr-em-remove-action', 'data-content' => Lang::get('decima-human-resources::employee-management.editHelpText'))) !!}
{!! Form::button('', array('id' => 'hr-em-btn-edit-helper', 'class' => 'hidden')) !!}
{!! Form::button('', array('id' => 'hr-em-btn-delete-helper', 'class' => 'hidden')) !!}
<style></style>

<script type='text/javascript'>
	var hrEmFiltersNames = {!! json_encode($leaveapprover) !!};
	var hrEmFiltersGender = {!! json_encode($genders) !!};
	var hrEmFiltersMaritalStatus = {!! json_encode($mstatus) !!};
	var hrEmFiltersDepartment = {!! json_encode($department) !!};
	var hrEmFiltersPosition = {!! json_encode($position) !!};
	var hrEmFiltersStatus = {!! json_encode($statusl) !!};
	//Falta agregar  codigo para quitar tooltip
	//For grids with multiselect enabled
	function hrEmLaOnSelectRowEvent(id)
	{
		var selRowIds = $('#hr-em-la-grid').jqGrid('getGridParam', 'selarrrow');
		if(selRowIds.length == 0)
		{
			$('#hr-em-btn-group-2').disabledButtonGroup();
			cleanJournals('hr-em-la-');
		}
		else if(selRowIds.length == 1)
		{
			$('#hr-em-btn-group-2').enableButtonGroup();
			$('#hr-em-btn-la').attr('disabled','disabled');
			// cleanJournals('hr-em-la-');
			// getAppJournals('hr-em-la-','firstPage', $('#hr-em-la-grid').getSelectedRowId('hr_em_id'));
		}
		else if(selRowIds.length > 1)
		{
			$('#hr-em-btn-group-2').disabledButtonGroup();
			$('#hr-em-btn-delete').removeAttr('disabled');
			// cleanJournals('hr-em-la-');
		}
	}
	function hrEmShowApprover(status)
	{
		if (status == "B")
		{
			$('#hr-em-la-leave-approver-label').removeAttr('disabled');
			$('#hr-em-la-leave-approver-label-show-all-button').removeAttr('disabled');
		}
		else
		{
			$('#hr-em-la-leave-approver-label').attr('disabled','disabled');
			$('#hr-em-la-leave-approver-label-show-all-button').attr('disabled','disabled');
			$('#hr-em-la-leave-approver-label').val("");
			$('#hr-em-la-leave-approver-id').val("");
		}
	}
	//For grids with multiselect disabled
	function hrEmOnSelectRowEvent()
	{
		var id = $('#hr-em-grid').getSelectedRowId('hr_em_id');

		getAppJournals('hr-em-', 'firstPage', id);
		getElementFiles('hr-em-', id);

		$('#hr-em-btn-group-2').enableButtonGroup();

	}

	$(document).ready(function()
	{
		$('.hr-em-btn-tooltip').tooltip();

		$('#hr-em-form').jqMgVal('addFormFieldsValidations');

		$('#hr-em-form-la').jqMgVal('addFormFieldsValidations');

		$('#hr-em-journals-section').on('hidden.bs.collapse', function ()
		{
			$($(this).attr('data-target-id')).collapse('show');
		});

		$('#hr-em-form-section').on('shown.bs.collapse', function ()
		{
			$('#hr-em-names').focus();
		});

		$('#hr-em-form-section, #hr-em-form-la-section').on('hidden.bs.collapse', function ()
		{
			$('#hr-em-grid-section').collapse('show');

			$('#hr-em-journals-section').collapse('show');
		});

		$('#hr-em-la-status-label').on( 'autocompleteselect', function( event, ui )
		{
			hrEmShowApprover(ui.item.value);
		});

		$('#hr-em-header-image-uploader').click(function()
		{
			// openUploader(prefix, systemReferenceId, parentFolder, allowedFileTypes, minWidth, sizes, maxFileCount, isPublic)
			openUploader('hr-em-', '', $(this).attr('data-folder'), ['image'], 245, true, [245, 100, 50], 1, true);
			$('#hr-em-file-uploader-modal').attr('data-flag', '1');
		});

		$('#hr-em-btn-upload').click(function()
			{
				var rowData, folder = $(this).attr('data-folder');

				if($(this).hasAttr('disabled'))
				{
					return;
				}

				$('.hr-em-btn-tooltip').tooltip('hide');

				if(!$('#hr-em-grid').isRowSelected())
				{
					$('#hr-em-btn-toolbar').showAlertAfterElement('alert-info alert-custom', lang.invalidSelection, 5000);
					return;
				}


				rowData = $('#hr-em-grid').getRowData($('#hr-em-grid').jqGrid('getGridParam', 'selrow'));
				folder = folder + '/' + rowData.hr_em_names + ' ' + rowData.hr_em_surnames;

				$('#hr-em-file-uploader-modal').attr('data-flag', '0');

				console.log(folder);

				// openUploader(prefix, systemReferenceId, parentFolder, allowedFileTypes, minWidth, sizes, maxFileCount, isPublic)
				openUploader('hr-em-', rowData.hr_em_id, folder);
			});


		$('#hr-em-file-uploader-modal').on('hidden.bs.modal', function (e)
		{
		   dataFiles = $.parseJSON($("#hr-em-file-uploader-modal").attr('data-files'));
				// if(("#hr-em-file-uploader-modal").attr('data-flag').val()==0)
				if($('#hr-em-file-uploader-modal').attr('data-flag') == '0')
				{
					if(dataFiles.length > 0)
					{
					  getElementFiles('hr-em-', $('#hr-em-grid').getSelectedRowId('hr_em_id'));
					}
				}
				else
				{
					if(dataFiles.length == 4)
					{
						$('#hr-em-profile-image').attr('src', dataFiles[1]['url']);
				 		$('#hr-em-profile-image-url').val(dataFiles[1]['url']);
						$('#hr-em-profile-image-medium-url').val(dataFiles[2]['url']);
						$('#hr-em-profile-image-small-url').val(dataFiles[3]['url']);
					}
				  else if (dataFiles.length == 3)
				  {
						$('#hr-em-profile-image').attr('src', dataFiles[0]['url']);
						$('#hr-em-profile-image-url').val(dataFiles[0]['url']);
						$('#hr-em-profile-image-medium-url').val(dataFiles[1]['url']);
						$('#hr-em-profile-image-small-url').val(dataFiles[2]['url']);
					}
				}
		});

		$('#hr-em-btn-clear-filter').click(function()
		{
			$('#hr-em-filters-form').find('.tokenfield').find('.close').click()

			$('#hr-em-filters-form').jqMgVal('clearForm');

			$('#hr-em-btn-filter').click();
		});

		$('#hr-em-btn-filter').click(function()
		{
			var filters = [];

			$(this).removeClass('btn-default').addClass('btn-warning');

			if($('#hr-em-filters-body').is(":visible"))
			{
			}

			if(!$('#hr-em-filters-form').jqMgVal('isFormValid'))
			{
				return;
			}

			$('#hr-em-filters-form').jqMgVal('clearContextualClasses');

			if(!$("#hr-em-filters-names").isEmpty())
			{
				filters.push({'field':'e.id', 'op':'in', 'data': $("#hr-em-filters-names").val()});
			}

			if(!$("#hr-em-filters-gender").isEmpty())
			{
				filters.push({'field':'e.gender', 'op':'in', 'data': $("#hr-em-filters-gender").val()});
			}

			if(!$("#hr-em-filters-status").isEmpty())
			{
				filters.push({'field':'e.status', 'op':'in', 'data': $("#hr-em-filters-status").val()});
			}

			if(!$("#hr-em-filters-marital-status").isEmpty())
			{
				filters.push({'field':'e.marital-status', 'op':'in', 'data': $("#hr-em-filters-marital-status").val()});
			}

			if(!$("#hr-em-filters-department").isEmpty())
			{
				filters.push({'field':'e.departament_id', 'op':'in', 'data': $("#hr-em-filters-department").val()});
			}

			if(!$("#hr-em-filters-position").isEmpty())
			{
				filters.push({'field':'e.position_id', 'op':'in', 'data': $("#hr-em-filters-position").val()});
			}
			//
			// if(!$("#hr-em-filters-salary").isEmpty())
			// {
			// 	filters.push({'field':'e.salary', 'op':'in', 'data': $("#hr-em-filters-salary").val()});
			// }

			if($("#hr-em-filters-date-from").val() != '__/__/____' && !$("#hr-em-filters-date-from").isEmpty())
			{
				filters.push({'field':'e.start_date', 'op':'ge', 'data': $.datepicker.formatDate("yy-mm-dd", $("#hr-em-filters-date-from").datepicker("getDate"))});
			}

			if($("#hr-em-filters-date-to").val() != '__/__/____' && !$("#hr-em-filters-date-to").isEmpty())
			{
				filters.push({'field':'e.start_date', 'op':'le', 'data': $.datepicker.formatDate("yy-mm-dd", $("#hr-em-filters-date-to").datepicker("getDate"))});
			}

			if(!$("#hr-em-filters-salary-from").isEmpty())
			{
				filters.push({'field':'e.salary', 'op':'ge', 'data': $("#hr-em-salary-from").val()});
			}

			if(!$("#hr-em-filters-salary-to").isEmpty())
			{
				filters.push({'field':'e.salary', 'op':'le', 'data': $("#hr-em-salary-to").val()});
			}



			if(filters.length == 0)
			{
				$('#hr-em-btn-filter').removeClass('btn-warning').addClass('btn-default');
			}

			$('#hr-em-grid').jqGrid('setGridParam', {'postData':{"filters":"{'groupOp':'AND','rules':" + JSON.stringify(filters) + "}"}}).trigger('reloadGrid');

		});

		$('#hr-em-btn-new').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			$('#hr-em-btn-toolbar').disabledButtonGroup();
			$('#hr-em-btn-group-3').enableButtonGroup();
			$('#hr-em-form-new-title').removeClass('hidden');
			$('#hr-em-journals-section').attr('data-target-id', '#hr-em-form-section');
			$('#hr-em-grid-section').collapse('hide');
			$('#hr-em-journals-section').collapse('hide');
			$('.hr-em-btn-tooltip').tooltip('hide');
		});

		$('#hr-em-btn-refresh').click(function()
		{
		  if($('#hr-em-journals-section').attr('data-target-id') == '' || $('#hr-em-journals-section').attr('data-target-id') == '#hr-em-form-section')
			{
				$('.hr-em-btn-tooltip').tooltip('hide');
				$('#hr-em-grid').trigger('reloadGrid');
				cleanJournals('hr-em-');
		  }
			else if ($('#hr-em-journals-section').attr('data-target-id') == '#hr-em-form-la-section')
			{
				$('#hr-em-la-grid').jqGrid('setGridParam', {'postData':{"filters":"{'groupOp':'AND','rules':[{'field':'l.applicant_id','op':'eq','data':'" + $('#hr-em-la-applicant-id').val() + "'}]}"}}).trigger('reloadGrid');
				$('.hr-em-la-btn-tooltip').tooltip('hide');
	 		  $('#hr-em-la-grid').trigger('reloadGrid');
	 		  cleanJournals('hr-em-la');
			}
		});

		$('#hr-em-btn-export-xls').click(function()
		{
				$('#hr-em-gridXlsButton').click();
		});

		$('#hr-em-btn-export-csv').click(function()
		{
				$('#hr-em-gridCsvButton').click();
		});

		$('#hr-em-btn-edit').click(function()
		{
			var rowData, rowId;

			$('.hr-em-la-btn-tooltip').tooltip('hide');

			if($('#hr-em-journals-section').attr('data-target-id') == '')
			{
				rowId = $('#hr-em-grid').jqGrid('getGridParam', 'selrow');

				if(!$('#hr-em-grid').isRowSelected())
				{
					$('#hr-em-btn-toolbar').showAlertAfterElement('alert-info alert-custom', lang.invalidSelection, 5000);
					return;
				}

				$('#hr-em-btn-toolbar').disabledButtonGroup();
				$('#hr-em-btn-group-3').enableButtonGroup();
				$('#hr-em-form-edit-title').removeClass('hidden');

				rowData = $('#hr-em-grid').getRowData($('#hr-em-grid').jqGrid('getGridParam', 'selrow'));
				populateFormFields(rowData);

				$('#hr-em-user').setAutocompleteLabel(rowData.hr_em_user_id);
				$('#hr-em-country').setAutocompleteLabel(rowData.hr_em_country_id);
				$('#hr-em-status-label').setAutocompleteLabel(rowData.hr_em_status);
				$('#hr-em-bank-label').setAutocompleteLabel(rowData.hr_em_bank);
				$('#hr-em-leave-approver-label').setAutocompleteLabel(rowData.hr_em_leave_approver_id);
				$('#hr-em-grid-section').collapse('hide');
				$('#hr-em-journals-section').attr('data-target-id', '#hr-em-form-section');
				$('#hr-em-journals-section').collapse('hide');
				$('.hr-em-btn-tooltip').tooltip('hide');

				if(!$('#hr-em-profile-image-url').isEmpty())
				{
						$('#hr-em-profile-image').attr('src', rowData.hr_em_profile_image_url);
				}
			}

			else if ($('#hr-em-journals-section').attr('data-target-id') == '#hr-em-form-la-section'){
				rowId = $('#hr-em-la-grid').jqGrid('getGridParam', 'selrow');

				if(!$('#hr-em-la-grid').isRowSelected())
				{
					$('#hr-em-la-btn-toolbar').showAlertAfterElement('alert-info alert-custom', lang.invalidSelection, 5000);
					return;
				}
				$('#hr-em-btn-group-2').disabledButtonGroup();
				$('#hr-em-la-btn-toolbar').disabledButtonGroup();

				rowData = $('#hr-em-la-grid').getRowData($('#hr-em-la-grid').jqGrid('getGridParam', 'selrow'));

				populateFormFields(rowData);
				hrEmShowApprover(rowData.acct_am_parent_account_id);
			}
		});



		$('#hr-em-btn-la').click(function()
		{
			$('#hr-em-btn-toolbar').disabledButtonGroup();
			$('#hr-em-btn-group-1').enableButtonGroup();
			$('#hr-em-btn-group-3').enableButtonGroup();
			$('#hr-em-btn-la').attr('disabled','disabled');
      $('#hr-em-btn-new').attr('disabled','disabled');
			$('.hr-em-btn-tooltip').tooltip('hide');

			var rowData, rowId;

				rowId = $('#hr-em-grid').jqGrid('getGridParam', 'selrow');

				if(rowId == null)
				{
					$('#hr-em-la-btn-toolbar').showAlertAfterElement('alert-info alert-custom', lang.invalidSelection, 5000);
					return;
				}

			rowData = $('#hr-em-grid').getRowData($('#hr-em-grid').jqGrid('getGridParam', 'selrow'));
			$('#hr-em-la-applicant-id').val(rowData.hr_em_id);
			$('#hr-em-la-names').val(rowData.hr_em_names);
			$('#hr-em-la-departament').val(rowData.hr_em_departament);
			$('#hr-em-la-departament-id').val(rowData.hr_em_departament_id);
			$('#hr-em-journals-section').attr('data-target-id', '#hr-em-form-la-section');
			$('#hr-em-btn-refresh').click();
			$('#hr-em-grid-section').collapse('hide');
			$('#hr-em-journals-section').collapse('hide');
		});

		$('#hr-em-btn-delete').click(function()
		{
			var rowData, rowId;

			if($(this).hasAttr('disabled'))
			{
				return;
			}

			if($('#hr-em-journals-section').attr('data-target-id') == '')
			{
				if(!$('#hr-em-grid').isRowSelected())
				{
					$('#hr-em-btn-toolbar').showAlertAfterElement('alert-info alert-custom', lang.invalidSelection, 5000);
					return;
				}
				rowData = $('#hr-em-grid').getRowData($('#hr-em-grid').jqGrid('getGridParam', 'selrow'));
				$('#hr-em-delete-message').html($('#hr-em-delete-message').attr('data-default-label').replace(':names', rowData.hr_em_names + ' ' + rowData.hr_em_surnames));
			}
			else if ($('#hr-em-journals-section').attr('data-target-id') == '#hr-em-form-la-section')
			{
				if(!$('#hr-em-la-grid').isRowSelected())
				{
					$('#hr-em-btn-toolbar').showAlertAfterElement('alert-info alert-custom', lang.invalidSelection, 5000);
					return;
				}

				rowData = $('#hr-em-la-grid').getRowData($('#hr-em-la-grid').jqGrid('getGridParam', 'selrow'));
				$('#hr-em-delete-message').html($('#hr-em-delete-message').attr('data-default-label').replace(':app', rowData.hr_em_names + ' ' + rowData.hr_em_surnames));
			}
			$('.hr-em-btn-tooltip').tooltip('hide');
			$('#hr-em-modal-delete').modal('show');
		});

		$('#hr-em-btn-modal-delete').click(function()
		{
			//For grids with multiselect enabled
			var id, url;

			if($('#hr-em-journals-section').attr('data-target-id') == '')
			{
				url = $('#hr-em-form').attr('action') + '/delete';
				//For grids with multiselect enabled
				// id = $('#hr-em-grid').getSelectedRowsIdCell('hr_em_id');
				//For grids with multiselect disabled
				id = $('#hr-em-grid').getSelectedRowId('hr_em_id');
			}
			else
			{
				console.log('Prueba');
				url = $('#hr-em-form-la').attr('action') + '/delete-leave-application';
 			  //For grids with multiselect enabled
				console.log('Prueba 2');
				id = $('#hr-em-la-grid').getSelectedRowsIdCell('hr_em_la_id');
 			  //For grids with multiselect disabled
 			  // id = $('#hr-em-grid').getSelectedRowId('module_app_id');
			}

			//For grids with multiselect disabled
			// var id = $('#hr-em-grid').getSelectedRowsId('hr_em_id');

			$.ajax(
			{
				type: 'POST',
				data: JSON.stringify({'_token':$('#app-token').val(), 'id':id}),
				dataType : 'json',
				url:  url,
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-em-btn-toolbar', false);
				},
				beforeSend:function()
				{
					$('#app-loader').removeClass('hidden');
					disabledAll();
				},
				success:function(json)
				{
					if(json.success)
					{
						if($('#hr-em-journals-section').attr('data-target-id') == '#hr-em-form-section')
						{
							$('#hr-em-btn-refresh').click();
							$('#hr-em-modal-delete').modal('hide');
							$('#hr-em-btn-toolbar').showAlertAfterElement('alert-success alert-custom',json.success, 5000);
						}
						else
						{
							$('#hr-em-btn-refresh').click();
							$('#hr-em-modal-delete').modal('hide');
							$('#hr-em-btn-toolbar').showAlertAfterElement('alert-success alert-custom',json.success, 5000);

						}
					}

					$('#app-loader').addClass('hidden');
					enableAll();
				}
			});
		});


		$('#hr-em-user-show-all-button').focusout(function()
		{
			$('#hr-em-btn-save').focus();
		});


		$('#hr-em-btn-save').click(function()
		{
			var url = $('#hr-em-form').attr('action'), action = 'new';

			$('.hr-em-btn-tooltip').tooltip('hide');
			if($('#hr-em-journals-section').attr('data-target-id') == '#hr-em-form-section')
			{
				if(!$('#hr-em-form').jqMgVal('isFormValid'))
				{
					return;
				}

				if($('#hr-em-id').isEmpty())
				{
					url = url + '/create';
				}
				else
				{
					url = url + '/update';
					action = 'edit';
				}

				data=$('#hr-em-form').formToObject('hr-em-');
			}


			else if($('#hr-em-journals-section').attr('data-target-id') == '#hr-em-form-la-section')
			{
				if(!$('#hr-em-form-la').jqMgVal('isFormValid'))
				{
					return;
				}
				if($('#hr-em-la-id').isEmpty())
				{
					url = url + '/create-leave-application';
				}
				else
				{
					url = url + '/update-leave-application';
					action = 'edit';
				}
				data=$('#hr-em-form-la').formToObject('hr-em-la-');
			}

			$.ajax(
			{
				type: 'POST',
				data: JSON.stringify(data),
				dataType : 'json',
				url: url,
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-em-form');
				},
				beforeSend:function()
				{
					$('#app-loader').removeClass('hidden');
					disabledAll();
				},
				success:function(json)
				{
					if(json.success)
					{
						if($('#hr-em-journals-section').attr('data-target-id') == '#hr-em-form-section')
						{
							$('#hr-em-btn-close').click();
							hrEmLeaveApproverLabelArrayData = hrEmLaLeaveApproverLabelArrayData = hrTmResponsibleEmployeeLabelArrayData = hrWhmStaEmployeeLabelArrayData = assetAmAssignedEmployeeLabelArrayData = acctJmEmployeeLabel = json.employees;
							$('#hr-em-leave-approver-label, #hr-em-la-leave-approver-label, #hr-tm-responsible-employee-label, #hr-whm-sta-employee-label, #asset-am-assigned-employee-label, #acct-jm-employee-label').autocomplete('option', 'source', json.employees);
						}
						else
						{
							$('#hr-em-form-la').jqMgVal('clearForm');
							$('#hr-em-btn-refresh').click();
						}
					}

					else if(json.info)
					{
						$('#hr-em-form').showAlertAsFirstChild('alert-info', json.info);
					}


					$('#app-loader').addClass('hidden');
					enableAll();
				}
			});
		});

		$('#hr-em-btn-close').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			$('#hr-em-btn-group-1').enableButtonGroup();
			$('#hr-em-btn-group-3').disabledButtonGroup();
			$('.hr-em-btn-tooltip').tooltip('hide');

			if($('#hr-em-journals-section').attr('data-target-id') == '#hr-em-form-section')
			{
				$('#hr-em-form-new-title').addClass('hidden');
				$('#hr-em-form-edit-title').addClass('hidden');
				$('#hr-em-btn-group-2').enableButtonGroup();
				$('#hr-em-form').jqMgVal('clearForm');
				cleanJournals('hr-em-');
				getAppJournals('hr-em-','firstPage', $('#hr-em-grid').getSelectedRowId('hr_em_id'));
				$('#hr-em-btn-refresh').click();
				$('#hr-em-form-section').collapse('hide');
				$('#hr-em-profile-image').attr('src', $('#hr-em-profile-image').attr('data-default-image'));
			}
			else if($('#hr-em-journals-section').attr('data-target-id') == '#hr-em-form-la-section')
			{
				$('#hr-em-form-la').jqMgVal('clearForm');
				// $('#hr-em-la-grid').jqGrid('clearGridData');
				$('#hr-em-btn-refresh').click();
				$('#hr-em-form-la-section').collapse('hide');
				cleanJournals('hr-em-');
				getAppJournals('hr-em-','firstPage', $('#hr-em-grid').getSelectedRowId('hr_em_id'));
				$('#hr-em-btn-group-2').enableButtonGroup();
			}

			$('#hr-em-journals-section').attr('data-target-id', '');
		});

		$('#hr-em-btn-edit-helper').click(function()
	  {
			showButtonHelper('hr-em-btn-close', 'hr-em-btn-group-2', $('#hr-em-edit-action').attr('data-content'));
	  });

		$('#hr-em-btn-delete-helper').click(function()
	  {
			showButtonHelper('hr-em-btn-close', 'hr-em-btn-group-2', $('#hr-em-delete-action').attr('data-content'));
	  });

		if(!$('#hr-em-new-action').isEmpty())
		{
			$('#hr-em-btn-new').click();
		}

		if(!$('#hr-em-edit-action').isEmpty())
		{
			showButtonHelper('hr-em-btn-close', 'hr-em-btn-group-2', $('#hr-em-edit-action').attr('data-content'));
		}

		if(!$('#hr-em-delete-action').isEmpty())
		{
			showButtonHelper('hr-em-btn-close', 'hr-em-btn-group-2', $('#hr-em-delete-action').attr('data-content'));
		}

		setTimeout(function ()
		{
			$('#hr-em-filters-names').tokenfield({
	  		autocomplete: {
			    source: hrEmFiltersNames,
			    delay: 100
			  },
			  showAutocompleteOnFocus: true,
				beautify:false
			});

			$('#hr-em-filters-names').on('tokenfield:createtoken', function (event) {
		    var available_tokens = hrEmFiltersNames;
		    var exists = true;
		    $.each(available_tokens, function(index, token)
				{
		        if (token.value == event.attrs.value)
						{
							exists = false;
						}
		    });
		    if(exists === true)
				{
					event.preventDefault();
				}
			});

			$('#hr-em-filters-gender').tokenfield({
	  		autocomplete: {
			    source: hrEmFiltersGender,
			    delay: 100
			  },
			  showAutocompleteOnFocus: true,
				beautify:false
			});

			$('#hr-em-filters-gender').on('tokenfield:createtoken', function (event) {
		    var available_tokens = hrEmFiltersGender;
		    var exists = true;
		    $.each(available_tokens, function(index, token)
				{
		        if (token.value == event.attrs.value)
						{
							exists = false;
						}
		    });

		    if(exists === true)
				{
					event.preventDefault();
				}
			});

			$('#hr-em-filters-status').tokenfield({
	  		autocomplete: {
			    source: hrEmFiltersStatus,
			    delay: 100
			  },
			  showAutocompleteOnFocus: true,
				beautify:false
			});

			$('#hr-em-filters-status').on('tokenfield:createtoken', function (event) {
		    var available_tokens = hrEmFiltersStatus;
		    var exists = true;
		    $.each(available_tokens, function(index, token)
				{
		        if (token.value == event.attrs.value)
						{
							exists = false;
						}
		    });
		    if(exists === true)
				{
					event.preventDefault();
				}
			});

			$('#hr-em-filters-marital-status').tokenfield({
	  		autocomplete: {
			    source: hrEmFiltersMaritalStatus,
			    delay: 100
			  },
			  showAutocompleteOnFocus: true,
				beautify:false
			});

			$('#hr-em-filters-marital-status').on('tokenfield:createtoken', function (event) {
		    var available_tokens = hrEmFiltersMaritalStatus;
		    var exists = true;
		    $.each(available_tokens, function(index, token)
				{
		        if (token.value == event.attrs.value)
						{
							exists = false;
						}
		    });
		    if(exists === true)
				{
					event.preventDefault();
				}
			});

			$('#hr-em-filters-department').tokenfield({
	  		autocomplete: {
			    source: hrEmFiltersDepartment,
			    delay: 100
			  },
			  showAutocompleteOnFocus: true,
				beautify:false
			});

			$('#hr-em-filters-department').on('tokenfield:createtoken', function (event) {
		    var available_tokens = hrEmFiltersDepartment;
		    var exists = true;
		    $.each(available_tokens, function(index, token)
				{
		        if (token.value == event.attrs.value)
						{
							exists = false;
						}
		    });
		    if(exists === true)
				{
					event.preventDefault();
				}
			});

			$('#hr-em-filters-position').tokenfield({
	  		autocomplete: {
			    source: hrEmFiltersPosition,
			    delay: 100
			  },
			  showAutocompleteOnFocus: true,
				beautify:false
			});

			$('#hr-em-filters-position').on('tokenfield:createtoken', function (event) {
		    var available_tokens = hrEmFiltersPosition;
		    var exists = true;
		    $.each(available_tokens, function(index, token)
				{
		        if (token.value == event.attrs.value)
						{
							exists = false;
						}
		    });
		    if(exists === true)
				{
					event.preventDefault();
				}
			});

			$('#hr-em-filters-position').tokenfield({
	  		autocomplete: {
			    source: hrEmFiltersPosition,
			    delay: 100
			  },
			  showAutocompleteOnFocus: true,
				beautify:false
			});

			$('#hr-em-filters-position').on('tokenfield:createtoken', function (event) {
		    var available_tokens = hrEmFiltersPosition;
		    var exists = true;
		    $.each(available_tokens, function(index, token)
				{
		        if (token.value == event.attrs.value)
						{
							exists = false;
						}
		    });
		    if(exists === true)
				{
					event.preventDefault();
				}
			});

		}, 500);
	});
</script>

<div class="row">
	<div class="col-lg-12 col-md-12">
		{!! Form::open(array('id' => 'hr-em-filters-form', 'url' => URL::to('/'), 'role' => 'form', 'onsubmit' => 'return false;', 'class' => 'form-horizontal')) !!}
		<div id="hr-em-filters" class="panel panel-default">
			<div class="panel-heading custom-panel-heading clearfix">
				<button id="hr-em-filters-salaryter-toggle" type="button" class="btn btn-default btn-sm btn-filter-toggle pull-right" data-toggle="collapse" data-target="#hr-em-filters-body"><i class="fa fa-chevron-down"></i></button>
				<h3 class="panel-title custom-panel-title pull-left">
					{{ Lang::get('form.filtersTitle') }}
				</h3>
				{!! Form::button('<i class="fa fa-filter"></i> ' . Lang::get('form.filterButton'), array('id' => 'hr-em-btn-filter', 'class' => 'btn btn-default btn-sm pull-right btn-filter-left-margin')) !!}
				{!! Form::button('<i class="fa fa-eraser"></i> ' . Lang::get('form.clearFilterButton'), array('id' => 'hr-em-btn-clear-filter', 'class' => 'btn btn-default btn-sm pull-right')) !!}
			</div>
			<div id="hr-em-filters-body" class="panel-body collapse">
				<div class="row">
					<div class="col-lg-6 col-md-12">
						<div class="form-group">
							{!! Form::label('hr-em-filters-names', Lang::get('decima-human-resources::employee-management.gridTitle'), array('class' => 'col-sm-2 control-label')) !!}
							<div class="col-sm-10 mg-hm">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user"></i></span>
									{!! Form::text('hr-em-filters-names', null , array('id' => 'hr-em-filters-names', 'class' => 'form-control')) !!}
								</div>
								<p class="help-block">{{ Lang::get('decima-human-resources::employee-management.namesHelperText') }}</p>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('hr-em-filters-gender', Lang::get('decima-human-resources::employee-management.genderPlural'), array('class' => 'col-sm-2 control-label')) !!}
							<div class="col-sm-10 mg-hm">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-venus-mars"></i></span>
									{!! Form::text('hr-em-filters-gender', null , array('id' => 'hr-em-filters-gender', 'class' => 'form-control')) !!}
								</div>
								<p class="help-block">{{ Lang::get('decima-human-resources::employee-management.gendersHelperText') }}</p>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('hr-em-filters-status', Lang::get('form.statusPlural'), array('class' => 'col-sm-2 control-label')) !!}
							<div class="col-sm-10 mg-hm">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-users"></i></span>
									{!! Form::text('hr-em-filters-status', null , array('id' => 'hr-em-filters-status', 'class' => 'form-control')) !!}
								</div>
								<p class="help-block">{{ Lang::get('decima-human-resources::employee-management.statusHelperText') }}</p>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('hr-em-filters-date-from', Lang::get('decima-human-resources::employee-management.dateRangeShort'), array('class' => 'col-sm-2 control-label')) !!}
							<div class="col-sm-10 mg-hm">
								{!! Form::daterange('hr-em-filters-date-from', 'hr-em-filters-date-to' , array('class' => 'form-control')) !!}
								<p class="help-block">{{ Lang::get('decima-human-resources::employee-management.dateRangeHelperText') }}</p>
							</div>
						</div>
						</div>
						<div class="col-lg-6 col-md-12">
							<div class="form-group">
								{!! Form::label('hr-em-filters-department', Lang::get('decima-human-resources::employee-management.departmentShort'), array('class' => 'col-sm-2 control-label')) !!}
								<div class="col-sm-10 mg-hm">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-sitemap"></i></span>
										{!! Form::text('hr-em-filters-department', null , array('id' => 'hr-em-filters-department', 'class' => 'form-control')) !!}
									</div>
									<p class="help-block">{{ Lang::get('decima-human-resources::employee-management.departmentsHelperText') }}</p>
								</div>
							</div>
							<div class="form-group">
								{!! Form::label('hr-em-filters-position', Lang::get('decima-human-resources::employee-management.positionPlural'), array('class' => 'col-sm-2 control-label')) !!}
								<div class="col-sm-10 mg-hm">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-users"></i></span>
										{!! Form::text('hr-em-filters-position', null , array('id' => 'hr-em-filters-position', 'class' => 'form-control')) !!}
									</div>
									<p class="help-block">{{ Lang::get('decima-human-resources::employee-management.positionsHelperText') }}</p>
								</div>
							</div>
							<div class="form-group">
								{!! Form::label('hr-em-filters-marital-status', Lang::get('decima-human-resources::employee-management.maritalStatusShort'), array('class' => 'col-sm-2 control-label')) !!}
								<div class="col-sm-10 mg-hm">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-heart"></i></span>
										{!! Form::text('hr-em-filters-marital-status', null , array('id' => 'hr-em-filters-marital-status', 'class' => 'form-control')) !!}
									</div>
									<p class="help-block">{{ Lang::get('decima-human-resources::employee-management.maritalStatusHelperText') }}</p>
								</div>
							</div>
							<div class="form-group">
								{!! Form::label('hr-em-filters-salary-from', Lang::get('decima-human-resources::employee-management.salary'), array('class' => 'col-sm-2 control-label')) !!}
								<div class="col-sm-10 mg-hm">
									<div class="input-group">
										<span class="input-group-addon">{{ Lang::get('form.dateRangeFrom') }}</span>
										{!! Form::text('hr-em-filters-salary-from', null , array('id' => 'hr-em-filters-salary-from', 'class' => 'form-control')) !!}
										<span class="input-group-addon">{{ Lang::get('form.dateRangeTo') }}</span>
										{!! Form::text('hr-em-filters-salary-to', null , array('id' => 'hr-em-filters-salary-to', 'class' => 'form-control')) !!}
									</div>
									<p class="help-block">{{ Lang::get('decima-human-resources::employee-management.salaryRangeHelperText') }}</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		{!! Form::close() !!}
		<div id="hr-em-btn-toolbar" class="section-header btn-toolbar" role="toolbar">
			<div id="hr-em-btn-group-1" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-plus"></i> ' . Lang::get('toolbar.new'), array('id' => 'hr-em-btn-new', 'class' => 'btn btn-default hr-em-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('decima-human-resources::employee-management.new'))) !!}
				{!! Form::button('<i class="fa fa-refresh"></i> ' . Lang::get('toolbar.refresh'), array('id' => 'hr-em-btn-refresh', 'class' => 'btn btn-default hr-em-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('toolbar.refreshLongText'))) !!}
				<div class="btn-group">
					{!! Form::button('<i class="fa fa-share-square-o"></i> ' . Lang::get('toolbar.export') . ' <span class="caret"></span>', array('class' => 'btn btn-default dropdown-toggle', 'data-container' => 'body', 'data-toggle' => 'dropdown')) !!}
					<ul class="dropdown-menu">
         		<li><a id='hr-em-btn-export-xls' class="fake-link"><i class="fa fa-file-excel-o"></i> xls</a></li>
         		<li><a id='hr-em-btn-export-csv' class="fake-link"><i class="fa fa-file-text-o"></i> csv</a></li>
       		</ul>
				</div>
			</div>
			<div id="hr-em-btn-group-2" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-upload"></i> ' . Lang::get('toolbar.upload'), array('id' => 'hr-em-btn-upload', 'class' => 'btn btn-default hr-em-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-folder' => Lang::get('decima-human-resources::employee-management.mainFolder'), 'data-original-title' => Lang::get('decima-human-resources::employee-management.upload'))) !!}
				{!! Form::button('<i class="fa fa-edit"></i> ' . Lang::get('toolbar.edit'), array('id' => 'hr-em-btn-edit', 'class' => 'btn btn-default hr-em-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::employee-management.edit'))) !!}
				{!! Form::button('<i class="fa fa-edit"></i> ' . Lang::get('decima-human-resources::employee-management.leaveAplication'), array('id' => 'hr-em-btn-la', 'class' => 'btn btn-default hr-em-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::employee-management.leaveAplicationTooltip'))) !!}
				{!! Form::button('<i class="fa fa-minus"></i> ' . Lang::get('toolbar.delete'), array('id' => 'hr-em-btn-delete', 'class' => 'btn btn-default hr-em-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::employee-management.delete'))) !!}
			</div>
			<div id="hr-em-btn-group-3" class="btn-group btn-group-app-toolbar toolbar-block">
				{!! Form::button('<i class="fa fa-save"></i> ' . Lang::get('toolbar.save'), array('id' => 'hr-em-btn-save', 'class' => 'btn btn-default hr-em-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::employee-management.save'))) !!}
				{!! Form::button('<i class="fa fa-undo"></i> ' . Lang::get('toolbar.close'), array('id' => 'hr-em-btn-close', 'class' => 'btn btn-default hr-em-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('toolbar.closeLongText'))) !!}
			</div>
		</div>
		<div id='hr-em-grid-section' class='app-grid collapse in' data-app-grid-id='hr-em-grid'>
			{!!
			GridRender::setGridId("hr-em-grid")
				->hideXlsExporter()
  			->hideCsvExporter()
	    	->setGridOption('url',URL::to('/human-resources/transactions/employee-management/grid-data'))
	    	->setGridOption('caption', Lang::get('decima-human-resources::employee-management.gridTitle', array('user' => AuthManager::getLoggedUserFirstname())))
	    	->setGridOption('postData',array('_token' => Session::token()))
				->setGridEvent('onSelectRow', 'hrEmOnSelectRowEvent')
				->setGridOption('multiselect', false)
	    	->addColumn(array('index' => 'e.id', 'name' => 'hr_em_id', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.names'), 'index' => 'e.names' ,'name' => 'hr_em_names'))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.surnames'), 'index' => 'e.surnames' ,'name' => 'hr_em_surnames'))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.gender'), 'index' => 'e.gender' ,'name' => 'hr_em_gender_label'))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.maritalStatus'), 'index' => 'e.marital_status' ,'name' => 'hr_em_marital_status_label'))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.children-number'), 'index' => 'e.children_number' ,'name' => 'hr_em_children_number', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.placeBirth'), 'index' => 'e.place_birth' ,'name' => 'hr_em_place_birth', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.dateBirth'), 'index' => 'e.date_birth' ,'name' => 'hr_em_date_birth', 'formatter' => 'date'))
	    	//->addColumn(array('label' => Lang::get('organization/organization-management.country'), 'index' => 'e.country' ,'name' => 'hr_em_country'))
	    	->addColumn(array('index' => 'e.country_id', 'name' => 'hr_em_country_id', 'hidden' => true ))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.passportNumber'), 'index' => 'e.passport_number' ,'name' => 'hr_em_passport_number', 'hidden' => true))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.singleIdentityDocumentNumber'), 'index' => 'e.single_identity_document_number' ,'name' => 'hr_em_single_identity_document_number', 'hidden' => true))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.taxId'), 'index' => 'e.tax_id' ,'name' => 'hr_em_tax_id', 'hidden' => true))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.profileImage'), 'index' => 'e.profile_image_url' ,'name' => 'hr_em_profile_image_url', 'hidden' => true))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.profileImageMedium'), 'index' => 'e.profile_image_medium_url' ,'name' => 'hr_em_profile_image_medium_url', 'hidden' => true))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.profileImageSmall'), 'index' => 'e.profile_image_small_url' ,'name' => 'hr_em_profile_image_small_url', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.personaEmail'), 'index' => 'e.personal_email' ,'name' => 'hr_em_personal_email', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.residencePhone'), 'index' => 'e.residence_phone', 'name' => 'hr_em_residence_phone', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.mobilePhone'), 'index' => 'e.mobile_phone' ,'name' => 'hr_em_mobile_phone', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.emergencyContact'), 'index' => 'e.emergency_contact' ,'name' => 'hr_em_emergency_contact', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.emergencyPhone'), 'index' => 'e.emergency_phone' ,'name' => 'hr_em_emergency_phone', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.bloodType'), 'index' => 'e.blood_type' ,'name' => 'hr_em_blood_type', 'hidden' => true))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.department'), 'index' => 'e.departament' ,'name' => 'hr_em_departament'))
				->addColumn(array('index' => 'e.departament_id', 'name' => 'hr_em_departament_id', 'hidden' => true ))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.status'), 'index' => 'e.status' ,'name' => 'hr_em_status', 'hidden' => true))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.leaveApproverId'), 'index' => 'e.leave_approver_id' ,'name' => 'hr_em_leave_approver_id', 'hidden' => true))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.position'), 'index' => 'e.position' ,'name' => 'hr_em_position'))
				->addColumn(array('index' => 'e.position_id', 'name' => 'hr_em_position_id', 'hidden' => true ))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.salary'), 'index' => 'e.salary' ,'name' => 'hr_em_salary', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.startDate'), 'index' => 'e.start_date' ,'name' => 'hr_em_start_date', 'formatter' => 'date', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.bankId'), 'index' => 'e.bank_id' ,'name' => 'hr_em_bank_id', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.bankId'), 'index' => 'b.name' ,'name' => 'hr_em_bank_label', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.bankAccount'), 'index' => 'e.bank_account_number' ,'name' => 'hr_em_bank_account_number', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.workEmail'), 'index' => 'e.work_email', 'name' => 'hr_em_work_email', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.workPhone'), 'index' => 'e.work_phone' ,'name' => 'hr_em_work_phone', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.workPhoneExtension'), 'index' => 'e.work_phone_extension' ,'name' => 'hr_em_work_phone_extension', 'hidden' => true))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.workMobile'), 'index' => 'e.work_mobile' ,'name' => 'hr_em_work_mobile', 'hidden' => true))
				//->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.user'), 'index' => 'e.user' ,'name' => 'hr_em_user'))
				->addColumn(array('index' => 'e.user_id', 'name' => 'hr_em_user_id', 'hidden' => true ))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.taxIdName'), 'index' => 'e.tax_id_name', 'name' => 'hr_em_tax_id_name', 'hidden' => true))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.singleIdentityDocumentNumberName'), 'index' => 'e.single_identity_document_number_name', 'name' => 'hr_em_single_identity_document_number_name', 'hidden' => true))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.socialSecurityNumber'), 'index' => 'e.social_security_number', 'name' => 'hr_em_social_security_number', 'hidden' => true))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.socialSecurityNumberName'), 'index' => 'e.social_security_number_name', 'name' => 'hr_em_social_security_number_name', 'hidden' => true))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.afp'), 'index' => 'a.name', 'name' => 'hr_em_afp_label', 'hidden' => true))
				->addColumn(array('index' => 'a.afp_id', 'name' => 'hr_em_afp_id', 'hidden' => true ))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.singlePrevisionalNumber'), 'index' => 'e.single_previsional_number', 'name' => 'hr_em_single_previsional_number', 'hidden' => true))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.singlePrevisionalNumberName'), 'index' => 'e.single_previsional_number_name', 'name' => 'hr_em_single_previsional_number_name', 'hidden' => true))
				->addColumn(array('index' => 'e.street1', 'name' => 'hr_em_street1', 'hidden' => true))
				->addColumn(array('index' => 'e.street2', 'name' => 'hr_em_street2', 'hidden' => true))
				->addColumn(array('index' => 'e.city_name', 'name' => 'hr_em_city_name', 'hidden' => true))
				->addColumn(array('index' => 'e.state_name', 'name' => 'hr_em_state_name', 'hidden' => true))
				->addColumn(array('index' => 'e.zip_code', 'name' => 'hr_em_zip_code', 'hidden' => true))
				->renderGrid();
			!!}
		</div>
	</div>
</div>

<div id='hr-em-form-la-section' class="row collapse">
	<div class="col-lg-12 col-md-12">
		<div class="form-container">
			{!! Form::open(array('id' => 'hr-em-form-la', 'url' => URL::to('human-resources/transactions/employee-management'), 'role'  =>  'form', 'onsubmit' => 'return false;')) !!}
				<legend id="hr-em-la-form-new-title" class="hidden" style="border-bottom:none;margin-bottom: 10px;">{{ Lang::get('decima-human-resources::employee-management.formNewTitleLeave') }}</legend>
				<legend id="hr-em-la-form-edit-title" class="hidden" style="border-bottom:none;margin-bottom: 10px;">{{ Lang::get('decima-human-resources::employee-management.formEditTitleLeave') }}</legend>
				<legend style="font-size: 19px;">{{ Lang::get('decima-human-resources::employee-management.personalData') }}</legend>
					<div class="row">
						<div class	="col-lg-6 col-md-6">
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-la-names', Lang::get('decima-human-resources::employee-management.names'), array('class' => 'control-label')) !!}
							 	<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-sitemap"></i>
									</span>
									{!! Form::text('hr-em-la-names', null , array('id' => 'hr-em-la-names', 'class' => 'form-control', 'disabled' => '', 'data-mg-clear-ignored' => '')) !!}
									{!! Form::hidden('hr-em-la-applicant-id', null, array('id' => 'hr-em-la-applicant-id', 'data-mg-clear-ignored' => '')) !!}
								</div>
							</div>
						  <div class="form-group mg-hm">
					    	{!! Form::label('hr-em-la-from-date', Lang::get('decima-human-resources::employee-management.fromDate'), array('class' => 'control-label')) !!}
								{!! Form::date('hr-em-la-from-date', array('class' => 'form-control', 'data-mg-required' => '', 'data-default-value' => $currentDate), $currentDate) !!}
							  {!! Form::hidden('hr-em-la-id', null, array('id' => 'hr-em-la-id')) !!}
				  		</div>
							<div class="form-group mg-hm">
							  {!! Form::label('hr-em-la-to-date', Lang::get('decima-human-resources::employee-management.toDate'), array('class' => 'control-label')) !!}
						    {!! Form::date('hr-em-la-to-date', array('class' => 'form-control', 'data-mg-required' => '', 'data-default-value' => $currentDate), $currentDate) !!}
							</div>
							<div class="form-group mg-hm">
							  {!! Form::label('hr-em-la-total-leave-days', Lang::get('decima-human-resources::employee-management.totalLeaveDays'), array('class' => 'control-label')) !!}
								<div class="input-group">
								 <span class="input-group-addon">
									 <i class="fa fa-sitemap"></i>
								 </span>
					    		{!! Form::text('hr-em-la-total-leave-days', null , array('id' => 'hr-em-la-total-leave-days', 'class' => 'form-control', 'data-mg-required' => '')) !!}
				  			</div>
							</div>
							<div class="form-group mg-hm">
							  {!! Form::label('hr-em-la-is-half-day', Lang::get('decima-human-resources::employee-management.isHalfDay'), array('class' => 'control-label')) !!}
								{!! Form::autocomplete('hr-em-la-is-half-day-label', $halfday, array('class' => 'form-control', 'data-mg-required' => ''), 'hr-em-la-is-half-day-label', 'hr-em-la-is-half-day', null, 'fa-globe') !!}
								{!! Form::hidden('hr-em-la-is-half-day', null, array('id'  =>  'hr-em-la-is-half-day')) !!}
							</div>
						</div>
						<div class	="col-lg-6 col-md-6">
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-la-departament', Lang::get('decima-human-resources::employee-management.department'), array('class' => 'control-label')) !!}
								{!! Form::text('hr-em-la-departament', null , array('id' => 'hr-em-la-departament', 'class' => 'form-control', 'disabled'=>'', 'data-mg-clear-ignored' => '')) !!}
							</div>
					  	<div class="form-group mg-hm">
							  {!! Form::label('hr-em-la-reason', Lang::get('decima-human-resources::employee-management.reason'), array('class' => 'control-label')) !!}
							 	<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-sitemap"></i>
									</span>
							    {!! Form::text('hr-em-la-reason', null , array('id' => 'hr-em-la-reason', 'class' => 'form-control', 'data-mg-required' => '')) !!}
					  	  </div>
							</div>
							<div class="form-group mg-hm">
							  {!! Form::label('hr-em-la-status', Lang::get('decima-human-resources::employee-management.status'), array('class' => 'control-label')) !!}
								{!! Form::autocomplete('hr-em-la-status-label', $status, array('class' => 'form-control', 'data-mg-required' => ''), 'hr-em-la-status-label', 'hr-em-la-status', null, 'fa-globe') !!}
								{!! Form::hidden('hr-em-la-status', null, array('id'  =>  'hr-em-la-status')) !!}
							</div>
							<div class="form-group mg-hm">
						  	{!! Form::label('hr-em-la-leave-type-label', Lang::get('decima-human-resources::employee-management.leaveType'), array('class' => 'control-label')) !!}
								{!! Form::autocomplete('hr-em-la-leave-type-label', $leavetype, array('class' => 'form-control', 'data-mg-required' => ''), 'hr-em-la-leave-type-label', 'hr-em-la-leave-type-id', null, 'fa-globe') !!}
								{!! Form::hidden('hr-em-la-leave-type-id', null, array('id'  =>  'hr-em-la-leave-type-id')) !!}
					    </div>
							<div class="form-group mg-hm">
							  {!! Form::label('hr-em-la-leave-approver-label', Lang::get('decima-human-resources::employee-management.leaveApproverId'), array('class' => 'control-label')) !!}
								{!! Form::autocomplete('hr-em-la-leave-approver-label', $leaveapprover, array('class' => 'form-control', 'disabled' => ''),'hr-em-la-leave-approver-label', 'hr-em-la-leave-approver-id',  null,  'fa-globe') !!}
								{!! Form::hidden('hr-em-la-leave-approver-id', null, array('id'  =>  'hr-em-la-leave-approver-id')) !!}
			  			</div>
						</div>
					</div>
					{!! Form::close() !!}
		</div>
		<div id='hr-em-la-grid-section' class='app-grid collapse in' data-app-grid-id='hr-em-la-grid'>
			{!!
			GridRender::setGridId("hr-em-la-grid")
				->hideXlsExporter()
				->hideCsvExporter()
				->setGridOption('height', 'auto')
				->setGridOption('url',URL::to('/human-resources/transactions/employee-management/grid-data-leave-application'))
				->setGridOption('footerrow',true)
				->setGridOption('postData', array('_token' => Session::token(), 'filters'=>"{'groupOp':'AND','rules':[{'field':'l.applicant_id','op':'eq','data':'-1'}]}"))
				->setGridEvent('onSelectRow', 'hrEmLaOnSelectRowEvent')
				->addColumn(array('index' => 'l.id', 'name' => 'hr_em_la_id', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.fromDate'), 'index' => 'l.from_date' ,'name' => 'hr_em_la_from_date', 'formatter' => 'date'))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.toDate'), 'index' => 'l.to_date' ,'name' => 'hr_em_la_to_date', 'formatter' => 'date'))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.totalLeaveDays'), 'index' => 'l.total_leave_days' ,'name' => 'hr_em_la_total_leave_days'))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.isHalfDay'), 'index' => 'l.is_half_day' ,'name' => 'hr_em_la_is_half_day_label'))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.reason'), 'index' => 'l.reason' ,'name' => 'hr_em_la_reason'))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.status'), 'index' => 'l.status' ,'name' => 'hr_em_la_status_label'))
				->addColumn(array('label' => Lang::get('decima-human-resources::employee-management.leaveType'), 'index' => 'l.leave_type' ,'name' => 'hr_em_la_leave_type'))
				->addColumn(array('index' => 'l.leave_type_id', 'name' => 'hr_em_la_leave_type_id', 'hidden' => true))
				->renderGrid();
			!!}
		</div>
  </div>
</div>
<div id='hr-em-form-section' class="row collapse">
	<div class="col-lg-12 col-md-12">
		<div class="form-container">
			{!! Form::open(array('id' => 'hr-em-form', 'url' => URL::to('human-resources/transactions/employee-management'), 'role'  =>  'form', 'onsubmit' => 'return false;')) !!}
				<legend id="hr-em-form-new-title" class="hidden" style="border-bottom:none;margin-bottom: 10px;">{{ Lang::get('decima-human-resources::employee-management.formNewTitle') }}</legend>
				<legend id="hr-em-form-edit-title" class="hidden" style="border-bottom:none;margin-bottom: 10px;">{{ Lang::get('decima-human-resources::employee-management.formEditTitle') }}</legend>
				<legend style="font-size: 19px;">{{ Lang::get('decima-human-resources::employee-management.personalData') }}</legend>
					<div class="row">
						<div class	="col-lg-6 col-md-6">
							<div class="row">
							  <div class	="col-lg-6 col-md-6">
									<div class="carousel slide" data-ride="carousel">
										 <div class="carousel-inner" style="position:absolute; top:18px;">
												<div class="item active">
													<img id='hr-em-profile-image' class='img-responsive blog-form-header-image' onerror="this.src='{{URL::asset('assets/kwaai/images/perfil.jpg')}}'" data-default-image='{{URL::asset('assets/kwaai/images/perfil.jpg')}}' src='{{URL::asset('assets/kwaai/images/perfil.jpg')}}'>
												  <!-- <img id= "hr-em-profile-image" class="img-responsive blog-form-header-image" onerror="this.src='{{URL::asset('assets/kwaai/images/perfil.jpg')}}'" data-default-image='{{URL::asset('assets/kwaai/images/perfil.jpg')}}' src='{{URL::asset('assets/kwaai/images/perfil.jpg')}}'> -->
													<div class="carousel-caption">
														 <h4><a id='hr-em-header-image-uploader' class="fake-link image-uploader-link" data-folder="{{ Lang::get('decima-human-resources::employee-management.folder') }}"><i class="fa fa-file-image-o" aria-hidden="true"></i> {{ Lang::get('form.uploadImage')}}</a></h4>
													</div>
												</div>
										 </div>
									</div>
							  </div>
								<div class	="col-lg-6 col-md-6">
										<div class="form-group mg-hm">
											{!! Form::label('hr-em-names', Lang::get('decima-human-resources::employee-management.names'), array('class' => 'contsrol-label')) !!}
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-font"></i>
												</span>
												{!! Form::text('hr-em-names', null , array('id' => 'hr-em-names', 'class' => 'form-control', 'data-mg-required' => '')) !!}
												{!! Form::hidden('hr-em-id', null, array('id' => 'hr-em-id')) !!}
											</div>
										</div>
										<div class="form-group mg-hm">
											{!! Form::label('hr-em-surnames', Lang::get('decima-human-resources::employee-management.surnames'), array('class' => 'control-label')) !!}
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-bold"></i>
												</span>
												{!! Form::text('hr-em-surnames', null , array('id' => 'hr-em-surnames', 'class' => 'form-control', 'data-mg-required' => '')) !!}
											</div>
										</div>
										<div class="form-group mg-hm">
											{!! Form::label('hr-em-gender', Lang::get('decima-human-resources::employee-management.gender'), array('class' => 'control-label')) !!}
											{!! Form::autocomplete('hr-em-gender-label', $genders, array('class' => 'form-control', 'data-mg-required' => ''), 'hr-em-gender-label', 'hr-em-gender', null, 'fa-venus-mars') !!}
											{!! Form::hidden('hr-em-gender', null, array('id'  =>  'hr-em-gender')) !!}
										</div>
								  </div>
							 </div>
			  			<div class="form-group mg-hm">
							  {!! Form::label('hr-em-marital-status', Lang::get('decima-human-resources::employee-management.maritalStatus'), array('class' => 'control-label')) !!}
								{!! Form::autocomplete('hr-em-marital-status-label', $mstatus, array('class' => 'form-control', 'data-mg-required' => ''), 'hr-em-marital-status-label', 'hr-em-marital-status', null, 'fa-heart') !!}
								{!! Form::hidden('hr-em-marital-status', null, array('id'  =>  'hr-em-marital-status')) !!}
							</div>
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-date-birth', Lang::get('decima-human-resources::employee-management.dateBirth'), array('class' => 'control-label')) !!}
								{!! Form::date('hr-em-date-birth', array('class' => 'form-control', 'data-mg-required' => '')) !!}
							</div>
		  				<div class="form-group mg-hm">
							  {!! Form::label('hr-em-place-birth', Lang::get('decima-human-resources::employee-management.placeBirth'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-map-marker"></i>
									</span>
									{!! Form::text('hr-em-place-birth', null , array('id' => 'hr-em-place-birth', 'class' => 'form-control', 'data-mg-required' => '')) !!}
				  			</div>
							</div>
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-country', Lang::get('organization/organization-management.country'), array('class' => 'control-label')) !!}
								{!! Form::autocomplete('hr-em-country', $countries, array('class' => 'form-control', 'data-mg-required' => ''), 'hr-em-country', 'hr-em-country-id', null, 'fa-flag') !!}
								{!! Form::hidden('hr-em-country-id', null, array('id'  =>  'hr-em-country-id')) !!}
							</div>
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-single-identity-document-number', Lang::get('decima-purchase::supplier-management.singleIdentityDocumentNumber'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-id-card"></i>
									</span>
									{!! Form::text('hr-em-single-identity-document-number', null , array('id' => 'hr-em-single-identity-document-number', 'class' => 'form-control')) !!}
								</div>
							</div>

							<div class="form-group mg-hm">
								{!! Form::label('hr-em-single-identity-document-number-name', Lang::get('decima-human-resources::employee-management.singleIdentityDocumentNumberName'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-id-card"></i>
									</span>
									{!! Form::text('hr-em-single-identity-document-number-name', null , array('id' => 'hr-em-single-identity-document-number-name', 'class' => 'form-control')) !!}
								</div>
							</div>

							<div class="form-group mg-hm">
								{!! Form::label('hr-em-tax-id', Lang::get('organization/organization-management.taxId'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-id-card"></i>
									</span>
									{!! Form::text('hr-em-tax-id', null , array('id' => 'hr-em-tax-id', 'class' => 'form-control')) !!}
								</div>
							</div>
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-tax-id-name', Lang::get('decima-human-resources::employee-management.taxIdName'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-id-card"></i>
									</span>
									{!! Form::text('hr-em-tax-id-name', null , array('id' => 'hr-em-tax-id-name', 'class' => 'form-control')) !!}
								</div>
							</div>
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-social-security-number', Lang::get('decima-human-resources::employee-management.socialSecurityNumber'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-id-card"></i>
									</span>
									{!! Form::text('hr-em-social-security-number', null , array('id' => 'hr-em-social-security-number', 'class' => 'form-control')) !!}
								</div>
							</div>
							<div class="form-group mg-hm" style="margin-top: 30px">
								{!! Form::label('hr-em-social-security-number-name', Lang::get('decima-human-resources::employee-management.socialSecurityNumberName'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-id-card"></i>
									</span>
									{!! Form::text('hr-em-social-security-number-name', null , array('id' => 'hr-em-social-security-number-name', 'class' => 'form-control')) !!}
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-afp-label', Lang::get('decima-human-resources::employee-management.afpId'), array('class' => 'control-label')) !!}
								{!! Form::autocomplete('hr-em-afp-label', '', array('class' => 'form-control'), 'hr-em-afp-label', 'hr-em-afp-id', null, 'fa-university') !!}
								{!! Form::hidden('hr-em-afp-id', null, array('id'  =>  'hr-em-afp-id')) !!}
								<p class="help-block">&nbsp;</p>
							</div>
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-single-previsional-number', Lang::get('decima-human-resources::employee-management.singlePrevisionalNumber'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-id-card"></i>
									</span>
									{!! Form::text('hr-em-single-previsional-number', null , array('id' => 'hr-em-single-previsional-number', 'class' => 'form-control')) !!}
								</div>
								<p class="help-block">&nbsp;</p>
							</div>
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-single-previsional-number-name', Lang::get('decima-human-resources::employee-management.singlePrevisionalNumberName'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-id-card"></i>
									</span>
									{!! Form::text('hr-em-single-previsional-number-name', null , array('id' => 'hr-em-single-previsional-number-name', 'class' => 'form-control')) !!}
								</div>
								<p class="help-block">&nbsp;</p>
							</div>
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-passport-number', Lang::get('decima-human-resources::employee-management.passportNumber'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-id-card"></i>
									</span>
									{!! Form::text('hr-em-passport-number', null , array('id' => 'hr-em-passport-number', 'class' => 'form-control')) !!}
								</div>
								<p class="help-block">&nbsp;</p>
							</div>
							<div class="form-group mg-hm">
							  {!! Form::label('hr-em-personal-email', Lang::get('decima-human-resources::employee-management.personalEmail'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-at"></i>
									</span>
									{{-- Form::text('hr-em-personal-email', null , array('id' => 'hr-em-personal-email', 'class' => 'form-control', 'data-mg-regex-help-message'=>'Please enter a hex number, example: 12AACC', 'data-mg-regex'=>'^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$')) --}}
									{!! Form::text('hr-em-personal-email', null , array('id' => 'hr-em-personal-email', 'class' => 'form-control')) !!}
			  			  </div>
								<p class="help-block">&nbsp;</p>
							</div>
	  					<div class="form-group mg-hm">
							  {!! Form::label('hr-em-residence-phone', Lang::get('decima-human-resources::employee-management.residencePhone'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-phone"></i>
									</span>
									{!! Form::text('hr-em-residence-phone', null , array('id' => 'hr-em-residence-phone', 'class' => 'form-control')) !!}
			  			  </div>
								<p class="help-block">&nbsp;</p>
							</div>
	  					<div class="form-group mg-hm">
						  	{!! Form::label('hr-em-mobile-phone', Lang::get('decima-human-resources::employee-management.mobilePhone'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-mobile"></i>
									</span>
									{!! Form::text('hr-em-mobile-phone', null , array('id' => 'hr-em-mobile-phone', 'class' => 'form-control')) !!}
			  			 	</div>
							 <p class="help-block">&nbsp;</p>
							</div>
	  					<div class="form-group mg-hm">
						  	{!! Form::label('hr-em-emergency-contact', Lang::get('decima-human-resources::employee-management.emergencyContact'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-ambulance"></i>
									</span>
									{!! Form::text('hr-em-emergency-contact', null , array('id' => 'hr-em-emergency-contact', 'class' => 'form-control')) !!}
			  			  </div>
							</div>
	  					<div class="form-group mg-hm">
							  {!! Form::label('hr-em-emergency-phone', Lang::get('decima-human-resources::employee-management.emergencyPhone'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-phone"></i>
									</span>
									{!! Form::text('hr-em-emergency-phone', null , array('id' => 'hr-em-emergency-phone', 'class' => 'form-control')) !!}
			  			  </div>
							</div>
	  					<div class="form-group mg-hm">
						   	{!! Form::label('hr-em-blood-type', Lang::get('decima-human-resources::employee-management.bloodType'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-heartbeat"></i>
									</span>
									{!! Form::text('hr-em-blood-type', null , array('id' => 'hr-em-blood-type', 'class' => 'form-control')) !!}
			  				</div>
							</div>
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-children-number', Lang::get('decima-human-resources::employee-management.childrenNumber'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-child"></i>
									</span>
									{!! Form::text('hr-em-children-number', null , array('id' => 'hr-em-children-number', 'class' => 'form-control')) !!}
								</div>
							</div>
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-street1', Lang::get('organization/organization-management.address'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-road"></i>
									</span>
									{!! Form::text('hr-em-street1', null , array('id' => 'hr-em-street1', 'class' => 'form-control', 'placeholder'  =>  Lang::get('organization/organization-management.street1PlaceHolder'))) !!}
								</div>
								<div class="input-group stacked-form-element">
								<span class="input-group-addon">
									<i class="fa fa-road"></i>
								</span>
									{!! Form::text('hr-em-street2', null , array('id' => 'hr-em-street2', 'class' => 'form-control', 'placeholder'  =>  Lang::get('organization/organization-management.street2PlaceHolder'))) !!}
								</div>
								<div class="input-group stacked-form-element">
									<span class="input-group-addon"></span>
									{!! Form::text('hr-em-city-name', null , array('id' => 'hr-em-city-name', 'class' => 'form-control', 'placeholder'  =>  Lang::get('organization/organization-management.cityNamePlaceHolder'))) !!}
									<span class="input-group-addon"></span>
									{!! Form::text('hr-em-state-name', null , array('id' => 'hr-em-state-name', 'class' => 'form-control', 'placeholder'  =>  Lang::get('organization/organization-management.stateNamePlaceHolder'))) !!}
									<span class="input-group-addon"></span>
									{!! Form::text('hr-em-zip-code', null , array('id' => 'hr-em-zip-code', 'class' => 'form-control', 'placeholder'  =>  Lang::get('organization/organization-management.zipCodePlaceHolder'))) !!}
								</div>
							</div>
						</div>
					</div>
				<legend style="font-size: 19px;">{{ Lang::get('decima-human-resources::employee-management.professionalData') }}</legend>
					<div class="row">
						<div class="col-lg-6 col-md-6">
							 	<div class="form-group mg-hm">
								  {!! Form::label('hr-em-status',Lang::get('decima-human-resources::employee-management.status'), array('class' => 'control-label')) !!}
								  {!! Form::autocomplete('hr-em-status-label', $statusl, array('class' => 'form-control'), 'hr-em-status-label', 'hr-em-status-label', null, 'fa-certificate') !!}
								  {!! Form::hidden('hr-em-status', null, array('id' => 'hr-em-status')) !!}
							 	</div>
							 	<div class="form-group mg-hm">
									{!! Form::label('hr-em-departament', Lang::get('decima-human-resources::employee-management.department'), array('class' => 'control-label')) !!}
									{!! Form::autocomplete('hr-em-departament', $department, array('class' => 'form-control'), 'hr-em-departament', 'hr-em-departament-id', null, 'fa-building') !!}
									{!! Form::hidden('hr-em-departament-id', null, array('id' => 'hr-em-departament-id')) !!}
							 	</div>
				  			<div class="form-group mg-hm">
									{!! Form::label('hr-em-position', Lang::get('decima-human-resources::employee-management.position'), array('class' => 'control-label')) !!}
									{!! Form::autocomplete('hr-em-position', $position, array('class' => 'form-control'), 'hr-em-position', 'hr-em-position-id', null, 'fa-id-badge') !!}
									{!! Form::hidden('hr-em-position-id', null, array('id'  =>  'hr-em-position-id')) !!}
								</div>
								<div class="form-group mg-hm">
								 {!! Form::label('hr-em-salary', Lang::get('decima-human-resources::employee-management.salary'), array('class' => 'control-label')) !!}
								 {!! Form::money('hr-em-salary', array('class' => 'form-control', 'defaultvalue' => Lang::get('form.defaultNumericValue')), Lang::get('form.defaultNumericValue')) !!}
				  		  </div>
								<div class="form-group mg-hm">
								 {!! Form::label('hr-em-start-date', Lang::get('decima-human-resources::employee-management.startDate'), array('class' => 'control-label')) !!}
								 {!! Form::date('hr-em-start-date', array('class' => 'form-control')) !!}
								</div>
								<div class="form-group mg-hm">
									{!! Form::label('hr-em-bank-label', Lang::get('decima-human-resources::employee-management.bankId'), array('class' => 'control-label')) !!}
									{!! Form::autocomplete('hr-em-bank-label', $banks, array('class' => 'form-control'), 'hr-em-bank-label', 'hr-em-bank-id', null, 'fa fa-cube') !!}
									{!! Form::hidden('hr-em-bank-id', null, array('id'  =>  'hr-em-bank-id')) !!}
								</div>
								<div class="form-group mg-hm">
									{!! Form::label('hr-em-bank-account-number', Lang::get('decima-human-resources::employee-management.bankAccountNumber'), array('class' => 'control-label')) !!}
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-hashtag"></i>
										</span>
										{!! Form::text('hr-em-bank-account-number', null , array('id' => 'hr-em-bank-account-number', 'class' => 'form-control')) !!}
								  </div>
								</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-leave-approver-label', Lang::get('decima-human-resources::employee-management.leaveApproverId'), array('class' => 'control-label')) !!}
								{!! Form::autocomplete('hr-em-leave-approver-label', $leaveapprover, array('class' => 'form-control'),'hr-em-leave-approver-label', 'hr-em-leave-approver-id',  null,  'fa-user-secret') !!}
								{!! Form::hidden('hr-em-leave-approver-id', null, array('id'  =>  'hr-em-leave-approver-id')) !!}
							</div>
							<div class="form-group mg-hm">
							  {!! Form::label('hr-em-work-email', Lang::get('decima-human-resources::employee-management.workEmail'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-at"></i>
									</span>
									{{-- Form::text('hr-em-work-email', null , array('id' => 'hr-em-work-email', 'class' => 'form-control', 'data-mg-regex-help-message'=>'Ingresa un correo valido', 'data-mg-regex-help-message'=>'Please enter a hex number, example: 12AACC', 'data-mg-regex'=>'^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$'))--}}
									{!! Form::text('hr-em-work-email', null , array('id' => 'hr-em-work-email', 'class' => 'form-control', 'data-mg-regex-help-message'=>'', 'data-mg-regex-help-message'=>'', 'data-mg-regex'=>''))!!}
								</div>
						  </div>
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-work-phone', Lang::get('decima-human-resources::employee-management.workPhone'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-tty"></i>
									</span>
									{!! Form::text('hr-em-work-phone', null , array('id' => 'hr-em-work-phone', 'class' => 'form-control')) !!}
								</div>
							</div>
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-work-phone-extension', Lang::get('decima-human-resources::employee-management.workPhoneExtension'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-list-alt"></i>
									</span>
									{!! Form::text('hr-em-work-phone-extension', null , array('id' => 'hr-em-work-phone-extension', 'class' => 'form-control')) !!}
								</div>
							</div>
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-work-mobile', Lang::get('decima-human-resources::employee-management.workMobile'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-mobile"></i>
									</span>
									{!! Form::text('hr-em-work-mobile', null , array('id' => 'hr-em-work-mobile', 'class' => 'form-control')) !!}
							  </div>
				  		</div>
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-user', Lang::get('decima-human-resources::employee-management.user'), array('class' => 'control-label')) !!}
								{!! Form::autocomplete('hr-em-user', $users, array('class' => 'form-control'), 'hr-em-user', 'hr-em-user-id', null, 'fa-user-circle') !!}
								{!! Form::hidden('hr-em-user-id', null, array('id' => 'hr-em-user-id')) !!}
							</div>
						</div>
					</div>
	  		<legend style="font-size: 19px;">{{ Lang::get('decima-human-resources::employee-management.imageProfile') }}</legend>
					<div class="row">
						<div class="col-lg-6 col-md-6">
								<div class="form-group mg-hm">
									{!! Form::label('hr-em-profile-image-url', Lang::get('decima-human-resources::employee-management.profileImageUrl'), array('class' => 'control-label')) !!}
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-picture-o"></i>
										</span>
										{!! Form::text('hr-em-profile-image-url', null , array('id' => 'hr-em-profile-image-url', 'class' => 'form-control', 'disabled' => '')) !!}
									</div>
								</div>
								<div class="form-group mg-hm">
									{!! Form::label('hr-em-profile-image-medium-url', Lang::get('decima-human-resources::employee-management.profileImageMediumUrl'), array('class' => 'control-label')) !!}
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-picture-o"></i>
										</span>
										{!! Form::text('hr-em-profile-image-medium-url', null , array('id' => 'hr-em-profile-image-medium-url', 'class' => 'form-control', 'disabled' => '')) !!}
									</div>
								</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="form-group mg-hm">
								{!! Form::label('hr-em-profile-image-small-url', Lang::get('decima-human-resources::employee-management.profileImageSmallUrl'), array('class' => 'control-label')) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-picture-o"></i>
									</span>
									{!! Form::text('hr-em-profile-image-small-url', null , array('id' => 'hr-em-profile-image-small-url', 'class' => 'form-control', 'disabled' => '')) !!}
								</div>
							</div>
						</div>
					</div>
				{!! Form::close() !!}
			</div>
	</div>
</div>
@include('decima-file::file-viewer')
@include('decima-file::file-uploader')
<div id='hr-em-journals-section' class="row collapse in section-block" data-target-id="">
	{!! Form::journals('hr-em-', $appInfo['id']) !!}
</div>
<div id='hr-em-modal-delete' class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm hr-em-btn-delete">
    <div class="modal-content">
			<div class="modal-body" style="padding: 20px 20px 0px 20px;">
				<p id="hr-em-delete-message" data-default-label="{{ Lang::get('decima-human-resources::employee-management.deleteMessageConfirmation') }}"></p>
      </div>
			<div class="modal-footer" style="text-align:center;">
				<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get('form.no') }}</button>
				<button id="hr-em-btn-modal-delete" type="button" class="btn btn-primary">{{ Lang::get('form.yes') }}</button>
			</div>
    </div>
  </div>
</div>
@parent
@stop
