@extends('layouts.base')

@section('container')
{!! Form::hidden('hr-whm-new-action', null, array('id' => 'hr-whm-new-action')) !!}
{!! Form::hidden('hr-whm-edit-action', null, array('id' => 'hr-whm-edit-action', 'data-content' => Lang::get('decima-human-resources::worked-hour-management.editHelpText'))) !!}
{!! Form::hidden('hr-whm-remove-action', null, array('id' => 'hr-whm-remove-action', 'data-content' => Lang::get('decima-human-resources::worked-hour-management.removeHelpText'))) !!}
{!! Form::button('', array('id' => 'hr-whm-btn-edit-helper', 'class' => 'hidden')) !!}
{!! Form::button('', array('id' => 'hr-whm-btn-delete-helper', 'class' => 'hidden')) !!}
<style></style>

<script type='text/javascript'>
	var hrWhmFiltersTask = {!! json_encode($task) !!};
	var hrWhmFiltersResponsibleEmployee = {!! json_encode($employees) !!};
	//Falta agregar  codigo para quitar tooltip
	//For grids with multiselect enabled
	// function hrWhmOnSelectRowEvent(id)
	// {
	// 	var selRowIds = $('#hr-whm-grid').jqGrid('getGridParam', 'selarrrow'), id;
	//
	// 	if(selRowIds.length == 0)
	// 	{
	// 		$('#hr-whm-btn-group-2').disabledButtonGroup();
	// 		cleanJournals('hr-whm-');
	// 		// cleanFiles('hr-whm-')
	// 	}
	// 	else if(selRowIds.length == 1)
	// 	{
	// 		$('#hr-whm-btn-group-2').enableButtonGroup();
	//
	// 		id = $('#hr-whm-grid').getSelectedRowId('hr_whm_id');
	//
	// 		if($('#hr-whm-journals').attr('data-journalized-id') != id)
	// 		{
	// 			cleanJournals('hr-whm-');
	// 			// getElementFiles('hr-whm-', id);
	// 			getAppJournals('hr-whm-','firstPage', id);
	// 		}
	//
	// 	}
	// 	else if(selRowIds.length > 1)
	// 	{
	// 		$('#hr-whm-btn-group-2').disabledButtonGroup();
	// 		$('#hr-whm-btn-delete').removeAttr('disabled');
	// 		cleanJournals('hr-whm-');
	// 		// cleanFiles('hr-whm-')
	// 	}
	// }

	//For grids with multiselect disabled
	function hrWhmOnSelectRowEvent()
	{
		var id = $('#hr-whm-grid').getSelectedRowId('hr_whm_id');

		if($('#hr-whm-journals').attr('data-journalized-id') != id)
		{
			getAppJournals('hr-whm-', 'firstPage', id);
			// getElementFiles('hr-whm-', id);
		}

		$('#hr-whm-btn-group-2').enableButtonGroup();


		rowData = $('#hr-whm-grid').getRowData($('#hr-whm-grid').jqGrid('getGridParam', 'selrow'));

		if(rowData.hr_whm_end_date != ' ')
		{
			$('#hr-whm-btn-end').attr('disabled', 'disabled');
		}
	}

	function hrWhmOnLoadCompleteEvent()
	{
		$(this).jqGrid('footerData','set', {'hr_whm_description': 'Total:', 'hr_whm_worked_hours': $(this).jqGrid('getCol', 'hr_whm_worked_hours', false, 'sum')});
	}

	$(document).ready(function()
	{
		$('.hr-whm-btn-tooltip').tooltip();

		$('#hr-whm-sta-form').jqMgVal('addFormFieldsValidations');
		$('#hr-whm-endf-form').jqMgVal('addFormFieldsValidations');

		$('#hr-whm-journals-section').on('hidden.bs.collapse', function ()
		{
			$($(this).attr('data-target-id')).collapse('show');
		});

		$('#hr-whm-form-section').on('shown.bs.collapse', function ()
		{
			$('#hr-whm-name').focus();
		});

		$('#hr-whm-endf-form-section').on('shown.bs.collapse', function ()
		{
			$('#hr-whm-endf-task-label').focus();
		});

		$('#hr-whm-form-section, #hr-whm-sta-form-section, #hr-whm-endf-form-section').on('hidden.bs.collapse', function ()
		{
			$('#hr-whm-grid-section').collapse('show');

			$('#hr-whm-journals-section').collapse('show');
		});

		//--------------------------------------------------------------------------
		$('#hr-whm-btn-clear-filter').click(function()
		{
			$('#hr-whm-filters-form').find('.tokenfield').find('.close').click()

			$('#hr-whm-filters-form').jqMgVal('clearForm');

			$('#hr-whm-btn-filter').click();
		});

		$('#hr-whm-btn-filter').click(function()
		{
			var filters = [];

			$(this).removeClass('btn-default').addClass('btn-warning');

			if(!$('#hr-whm-filters-form').jqMgVal('isFormValid'))
			{
				return;
			}

      if($('#hr-whm-filters-body').is(":visible"))
			{
				$('#hr-whm-filters-salaryter-toggle').click();
			}

			$('#hr-whm-filters-form').jqMgVal('clearContextualClasses');

			// if(!$("#hr-whm-filters-dates").isEmpty())
			// {
			// 	filters.push({'field':'e.dates_id', 'op':'in', 'data': $("#hr-whm-filters-dates").val()});
			// }

			if($("#hr-whm-filters-date-from").val() != '__/__/____' && !$("#hr-whm-filters-date-from").isEmpty())
			{
				filters.push({'field':'w.start_date', 'op':'ge', 'data': $.datepicker.formatDate("yy-mm-dd", $("#hr-whm-filters-date-from").datepicker("getDate")) + ' 00:00:00'});
			}

			if($("#hr-whm-filters-date-to").val() != '__/__/____' && !$("#hr-whm-filters-date-to").isEmpty())
			{
				filters.push({'field':'w.start_date', 'op':'le', 'data': $.datepicker.formatDate("yy-mm-dd", $("#hr-whm-filters-date-to").datepicker("getDate")) + ' 23:59:59'});
			}

			if($("#hr-whm-filters-single-date").val() != '__/__/____' && !$("#hr-whm-filters-single-date").isEmpty())
			{
				filters.push({'field':'w.start_date', 'op':'ge', 'data': $.datepicker.formatDate("yy-mm-dd", $("#hr-whm-filters-single-date").datepicker("getDate")) + ' 00:00:00'});
				filters.push({'field':'w.start_date', 'op':'le', 'data': $.datepicker.formatDate("yy-mm-dd", $("#hr-whm-filters-single-date").datepicker("getDate")) + ' 23:59:59'});
			}

			if(!$("#hr-whm-filters-task").isEmpty())
			{
				filters.push({'field':'w.task_id', 'op':'in', 'data': $("#hr-whm-filters-task").val()});
			}

			if(!$("#hr-whm-filters-description").isEmpty())
			{
				filters.push({'field':'w.description', 'op':'in', 'data': $("#hr-whm-filters-description").val()});
			}

			if(!$("#hr-whm-filters-responsible-employee").isEmpty())
			{
				filters.push({'field':'w.responsible_employee_id', 'op':'in', 'data': $("#hr-whm-filters-responsible-employee").val()});
			}


			if(filters.length == 0)
			{
				$('#hr-whm-btn-filter').removeClass('btn-warning').addClass('btn-default');
			}

			$('#hr-whm-grid').jqGrid('setGridParam', {'postData':{"filters":"{'groupOp':'AND','rules':" + JSON.stringify(filters) + "}"}}).trigger('reloadGrid');

		});
		//--------------------------------------------------------------------------

		$('#hr-whm-btn-start').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}
			$('#hr-whm-btn-toolbar').disabledButtonGroup();
			$('#hr-whm-btn-group-3').enableButtonGroup();
			$('#hr-whm-grid-section').collapse('hide');
			$('#hr-whm-journals-section').attr('data-target-id', '#hr-whm-sta-form-section');
			$('#hr-whm-journals-section').collapse('hide');
			$('.hr-whm-btn-tooltip').tooltip('hide');
		});


		$('#hr-whm-btn-refresh').click(function()
		{
			$('.hr-whm-btn-tooltip').tooltip('hide');
			$('#hr-whm-btn-toolbar').disabledButtonGroup();
			$('#hr-whm-btn-group-1').enableButtonGroup();

			if($('#hr-whm-journals-section').attr('data-target-id') == '' || $('#hr-whm-journals-section').attr('data-target-id') == '#hr-whm-form-section')
			{
				$('#hr-whm-grid').trigger('reloadGrid');
				cleanJournals('hr-whm-');
				// cleanFiles('hr-whm-')
			}
			else
			{

			}
		});

		$('#hr-whm-btn-export-xls').click(function()
		{
			if($('#hr-whm-journals-section').attr('data-target-id') == '')
			{
				$('#hr-whm-gridXlsButton').click();
			}
			else
			{

			}
		});

		$('#hr-whm-btn-export-csv').click(function()
		{
			if($('#hr-whm-journals-section').attr('data-target-id') == '')
			{
				$('#hr-whm-gridCsvButton').click();
			}
			else
			{

			}
		});

		$('#hr-whm-btn-end').click(function()
		{
			var rowData;
			$('#hr-whm-endf-start-date-calendar-button').attr('disabled', 'disabled');
			$('.hr-whm-btn-tooltip').tooltip('hide');
			$('#hr-whm-btn-toolbar').disabledButtonGroup();
			$('#hr-whm-btn-group-3').enableButtonGroup();

			if($('#hr-whm-journals-section').attr('data-target-id') == '')
			{
				if(!$('#hr-whm-grid').isRowSelected())
				{
					$('#hr-whm-btn-toolbar').showAlertAfterElement('alert-info alert-custom', lang.invalidSelection, 5000);
					return;
				}

				$('#hr-whm-form-edit-title').removeClass('hidden');
				rowData = $('#hr-whm-grid').getRowData($('#hr-whm-grid').jqGrid('getGridParam', 'selrow'));
				$('#hr-whm-endf-id').val(rowData.hr_whm_id);
				$('#hr-whm-endf-start-date').val(rowData.hr_whm_start_date);
				$('#hr-whm-endf-responsible-employee').val(rowData.hr_whm_responsible_employee);
				$('#hr-whm-endf-responsible-employee-id').val(rowData.hr_whm_responsible_employee_id);

				populateFormFields(rowData);

				$('#hr-whm-endf-form-title').removeClass('hidden');
				$('#hr-whm-grid-section').collapse('hide');
				$('#hr-whm-journals-section').attr('data-target-id', '#hr-whm-endf-form-section');
				$('#hr-whm-journals-section').collapse('hide');
			}
			else
			{

			}
		});

		$('#hr-whm-btn-delete').click(function()
		{
			var rowData;

			if($(this).hasAttr('disabled'))
			{
				return;
			}

			if($('#hr-whm-journals-section').attr('data-target-id') == '')
			{
				if(!$('#hr-whm-grid').isRowSelected())
				{
					$('#hr-whm-btn-toolbar').showAlertAfterElement('alert-info alert-custom', lang.invalidSelection, 5000);
					return;
				}

				rowData = $('#hr-whm-grid').getRowData($('#hr-whm-grid').jqGrid('getGridParam', 'selrow'));

				// $('#hr-whm-delete-message').html($('#hr-whm-delete-message').attr('data-default-label').replace(':name', rowData.hr_whm_name));
			}
			else
			{

			}

			$('.hr-whm-btn-tooltip').tooltip('hide');
			$('#hr-whm-modal-delete').modal('show');
		});

		$('#hr-whm-btn-modal-delete').click(function()
		{
			var id, url;

			if($('#hr-whm-journals-section').attr('data-target-id') == '')
			{
				url = $('#hr-whm-sta-form').attr('action') + '/delete';
				//For grids with multiselect enabled
				// id = $('#hr-whm-grid').getSelectedRowsIdCell('hr_whm_id');
				//For grids with multiselect disabled
				id = $('#hr-whm-grid').getSelectedRowId('hr_whm_id');
			}
			else
			{

			}

			$.ajax(
			{
				type: 'POST',
				data: JSON.stringify({'_token':$('#app-token').val(), 'id':id}),
				dataType : 'json',
				url:  url,
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-whm-btn-toolbar', false);
					$('#hr-whm-modal-delete').modal('hide');
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
						$('#hr-whm-btn-refresh').click();
						$('#hr-whm-btn-toolbar').showAlertAfterElement('alert-success alert-custom',json.success, 5000);
					}
					else if(json.info)
					{
						$('#hr-whm-btn-toolbar').showAlertAfterElement('alert-info alert-custom',json.info, 12000);
					}

					$('#hr-whm-modal-delete').modal('hide');
					$('#app-loader').addClass('hidden');
					enableAll();
				}
			});
		});

		$('#hr-whm-btn-save').click(function()
		{
			var url = $('#hr-whm-sta-form').attr('action'), action = 'new';

			$('.hr-whm-btn-tooltip').tooltip('hide');

			if($('#hr-whm-journals-section').attr('data-target-id') == '#hr-whm-sta-form-section')
			{
				if(!$('#hr-whm-sta-form').jqMgVal('isFormValid'))
				{
					return;
				}
				url = url + '/create';

				data = $('#hr-whm-sta-form').formToObject('hr-whm-sta-');
			}
			else
			{
				if(!$('#hr-whm-endf-form').jqMgVal('isFormValid'))
				{
					return;
				}

				if($('#hr-whm-endf-id').isEmpty())
				{
					url = url + '/create';
				}
				else
				{
					url = url + '/update';
					action = 'edit';
				}

				data = $('#hr-whm-endf-form').formToObject('hr-whm-endf-');
			}

			$.ajax(
			{
				type: 'POST',
				data: JSON.stringify(data),
				dataType : 'json',
				url: url,
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-whm-form');
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
						if($('#hr-whm-journals-section').attr('data-target-id') == '#hr-whm-sta-form-section')
						{
							$('#hr-whm-btn-close').click();
							$('#hr-whm-btn-refresh').click();
							$('#hr-whm-btn-toolbar').showAlertAfterElement('alert-success alert-custom',json.success, 6000);
						}
						else if($('#hr-whm-journals-section').attr('data-target-id') == '#hr-whm-endf-form-section')
						{
							$('#hr-whm-btn-close').click();
							$('#hr-whm-btn-refresh').click();
							$('#hr-whm-btn-toolbar').showAlertAfterElement('alert-success alert-custom',json.success, 6000);
						}

					}
					else if(json.info)
					{
						if($('#hr-whm-journals-section').attr('data-target-id') == '#hr-whm-sta-form-section')
						{
							$('#hr-whm-sta-form').showAlertAsFirstChild('alert-info', json.info, 12000);
						}
						else
						{
							$('#hr-whm-endf-form').showAlertAsFirstChild('alert-info', json.info, 12000);
						}
					}

					$('#app-loader').addClass('hidden');
					enableAll();
				}
			});
		});

		$('#hr-whm-btn-close').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			// hr-whm-form-section
			if($('#hr-whm-journals-section').attr('data-target-id') == '#hr-whm-form-section')
			{
				$('#hr-whm-form-new-title').addClass('hidden');
				$('#hr-whm-form-edit-title').addClass('hidden');
				$('#hr-whm-btn-refresh').click();
				$('#hr-whm-form').jqMgVal('clearForm');
				$('#hr-whm-form-section').collapse('hide');
			}
			else if ($('#hr-whm-journals-section').attr('data-target-id') == '#hr-whm-sta-form-section')
			{

				$('#hr-whm-sta-form-title').addClass('hidden');
				$('#hr-whm-btn-refresh').click();
				$('#hr-whm-sta-form').jqMgVal('clearForm');
				$('#hr-whm-sta-form-section').collapse('hide');
			}
			else if ($('#hr-whm-journals-section').attr('data-target-id') == '#hr-whm-endf-form-section')
			{
				$('#hr-whm-endf-form-title').addClass('hidden');
				$('#hr-whm-btn-refresh').click();
				$('#hr-whm-endf-form').jqMgVal('clearForm');
				$('#hr-whm-endf-form-section').collapse('hide');
			}

			$('#hr-whm-btn-group-1').enableButtonGroup();
			$('#hr-whm-btn-group-3').disabledButtonGroup();
			$('.hr-whm-btn-tooltip').tooltip('hide');
			$('#hr-whm-journals-section').attr('data-target-id', '')
		});

		$('#hr-whm-btn-edit-helper').click(function()
	  {
			showButtonHelper('hr-whm-btn-close', 'hr-whm-btn-group-2', $('#hr-whm-edit-action').attr('data-content'));
	  });

		$('#hr-whm-btn-delete-helper').click(function()
	  {
			showButtonHelper('hr-whm-btn-close', 'hr-whm-btn-group-2', $('#hr-whm-delete-action').attr('data-content'));
	  });

		if(!$('#hr-whm-new-action').isEmpty())
		{
			$('#hr-whm-btn-new').click();
		}

		if(!$('#hr-whm-edit-action').isEmpty())
		{
			showButtonHelper('hr-whm-btn-close', 'hr-whm-btn-group-2', $('#hr-whm-edit-action').attr('data-content'));
		}

		if(!$('#hr-whm-delete-action').isEmpty())
		{
			showButtonHelper('hr-whm-btn-close', 'hr-whm-btn-group-2', $('#hr-whm-delete-action').attr('data-content'));
		}

		$('#hr-whm-endf-description').focusout(function()
		{
			$('#hr-whm-btn-save').focus();
		});

		$('#hr-whm-sta-employee-label-show-all-button').focusout(function()
		{
			$('#hr-whm-btn-save').focus();
		});

		//--------------------------------------------------------------------------
	  setTimeout(function ()
	  {
			$('#hr-whm-sta-employee-label').setAutocompleteLabel('{!! !empty($loggedUser)? $loggedUser['id']: '' !!}');

	    $('#hr-whm-filters-task').tokenfield({
	      autocomplete: {
	        source: hrWhmFiltersTask,
	        delay: 100
	      },
	      showAutocompleteOnFocus: true,
	      beautify:false
	    });

	    $('#hr-whm-filters-task').on('tokenfield:createtoken', function (event) {
	      var available_tokens = hrWhmFiltersTask;
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

			$('#hr-whm-filters-responsible-employee').tokenfield({
	      autocomplete: {
	        source: hrWhmFiltersResponsibleEmployee,
	        delay: 100
	      },
	      showAutocompleteOnFocus: true,
	      beautify:false
	    });

	    $('#hr-whm-filters-responsible-employee').on('tokenfield:createtoken', function (event) {
	      var available_tokens = hrWhmFiltersResponsibleEmployee;
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
		{!! Form::open(array('id' => 'hr-whm-filters-form', 'url' => URL::to('/'), 'role' => 'form', 'onsubmit' => 'return false;', 'class' => 'form-horizontal')) !!}
		<div id="hr-whm-filters" class="panel panel-default">
			<div class="panel-heading custom-panel-heading clearfix">
				<button id="hr-whm-filters-salaryter-toggle" type="button" class="btn btn-default btn-sm btn-filter-toggle pull-right" data-toggle="collapse" data-target="#hr-whm-filters-body"><i class="fa fa-chevron-down"></i></button>
				<h3 class="panel-title custom-panel-title pull-left">
					{{ Lang::get('form.filtersTitle') }}
				</h3>
				{!! Form::button('<i class="fa fa-filter"></i> ' . Lang::get('form.filterButton'), array('id' => 'hr-whm-btn-filter', 'class' => 'btn btn-default btn-sm pull-right btn-filter-left-margin')) !!}
				{!! Form::button('<i class="fa fa-eraser"></i> ' . Lang::get('form.clearFilterButton'), array('id' => 'hr-whm-btn-clear-filter', 'class' => 'btn btn-default btn-sm pull-right')) !!}
			</div>
			<div id="hr-whm-filters-body" class="panel-body collapse">
				<div class="row">
					<div class="col-lg-6 col-md-12">
						<div class="form-group">
							{!! Form::label('hr-whm-filters-date-from', Lang::get('decima-human-resources::worked-hour-management.dateRangeShort'), array('class' => 'col-sm-2 control-label')) !!}
							<div class="col-sm-10 mg-hm">
								{!! Form::daterange('hr-whm-filters-date-from', 'hr-whm-filters-date-to' , array('class' => 'form-control')) !!}
								<p class="help-block">{{ Lang::get('decima-human-resources::worked-hour-management.dateRangeHelperText') }}</p>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('hr-whm-filters-single-date', Lang::get('decima-human-resources::worked-hour-management.singleDate'), array('class' => 'col-sm-2 control-label')) !!}
							<div class="col-sm-10 mg-hm">
								{!! Form::date('hr-whm-filters-single-date', array('class' => 'form-control')) !!}
								<!-- {!! Form::text('hr-whm-filters-names', null , array('id' => 'hr-whm-filters-names', 'class' => 'form-control')) !!} -->
								<p class="help-block">{{ Lang::get('decima-human-resources::worked-hour-management.singleDateHelperText') }}</p>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('hr-whm-filters-task', Lang::get('decima-human-resources::worked-hour-management.taskPlural'), array('class' => 'col-sm-2 control-label')) !!}
							<div class="col-sm-10 mg-hm">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-users"></i></span>
									{!! Form::text('hr-whm-filters-task', null , array('id' => 'hr-whm-filters-task', 'class' => 'form-control')) !!}
								</div>
								<p class="help-block">{{ Lang::get('decima-human-resources::worked-hour-management.tasksHelperText') }}</p>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-12">
						<div class="form-group">
							{!! Form::label('hr-whm-filters-description', Lang::get('decima-human-resources::worked-hour-management.description'), array('class' => 'col-sm-2 control-label')) !!}
							<div class="col-sm-10 mg-hm">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-users"></i></span>
									{!! Form::text('hr-whm-filters-description', null , array('id' => 'hr-whm-filters-description', 'class' => 'form-control')) !!}
								</div>
								<p class="help-block">{{ Lang::get('decima-human-resources::worked-hour-management.descriptionHelperText') }}</p>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('hr-whm-filters-responsible-employee', Lang::get('decima-human-resources::worked-hour-management.employee'), array('class' => 'col-sm-2 control-label')) !!}
							<div class="col-sm-10 mg-hm">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-users"></i></span>
									{!! Form::text('hr-whm-filters-responsible-employee', null , array('id' => 'hr-whm-filters-responsible-employee', 'class' => 'form-control')) !!}
								</div>
								<p class="help-block">{{ Lang::get('decima-human-resources::worked-hour-management.responsibleEmployeeHelperText') }}</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		{!! Form::close() !!}
		<div id="hr-whm-btn-toolbar" class="section-header btn-toolbar" role="toolbar">
			<div id="hr-whm-btn-group-1" class="btn-group btn-group-app-toolbar">
				<!-- {!! Form::button('<i class="fa fa-plus"></i> ' . Lang::get('toolbar.new'), array('id' => 'hr-whm-btn-new', 'class' => 'btn btn-default hr-whm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('decima-human-resources::worked-hour-management.new'))) !!} -->
				{!! Form::button('<i class="fa fa-clock-o" style="color:green;"></i> ' . Lang::get('decima-human-resources::menu.checkIn'), array('id' => 'hr-whm-btn-start', 'class' => 'btn btn-default hr-whm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('decima-human-resources::worked-hour-management.startDateLongText'))) !!}
				{!! Form::button('<i class="fa fa-refresh"></i> ' . Lang::get('toolbar.refresh'), array('id' => 'hr-whm-btn-refresh', 'class' => 'btn btn-default hr-whm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('toolbar.refreshLongText'))) !!}
				<div class="btn-group">
					{!! Form::button('<i class="fa fa-share-square-o"></i> ' . Lang::get('toolbar.export') . ' <span class="caret"></span>', array('class' => 'btn btn-default dropdown-toggle', 'data-container' => 'body', 'data-toggle' => 'dropdown')) !!}
					<ul class="dropdown-menu">
         		<li><a id='hr-whm-btn-export-xls' class="fake-link"><i class="fa fa-file-excel-o"></i> xls</a></li>
         		<li><a id='hr-whm-btn-export-csv' class="fake-link"><i class="fa fa-file-text-o"></i> csv</a></li>
       		</ul>
				</div>
			</div>
			<div id="hr-whm-btn-group-2" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-clock-o" style="color:red;"></i> ' . Lang::get('decima-human-resources::menu.checkOut'), array('id' => 'hr-whm-btn-end', 'class' => 'btn btn-default hr-whm-btn-tooltip',  'disabled' => '', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('decima-human-resources::worked-hour-management.endDateLongText'))) !!}
				{!! Form::button('<i class="fa fa-minus"></i> ' . Lang::get('toolbar.delete'), array('id' => 'hr-whm-btn-delete', 'class' => 'btn btn-default hr-whm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::worked-hour-management.delete'))) !!}
			</div>
			<div id="hr-whm-btn-group-3" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-save"></i> ' . Lang::get('toolbar.save'), array('id' => 'hr-whm-btn-save', 'class' => 'btn btn-default hr-whm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::worked-hour-management.save'))) !!}
				{!! Form::button('<i class="fa fa-undo"></i> ' . Lang::get('toolbar.close'), array('id' => 'hr-whm-btn-close', 'class' => 'btn btn-default hr-whm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('toolbar.closeLongText'))) !!}
			</div>
		</div>
		<div id='hr-whm-grid-section' class='app-grid collapse in' data-app-grid-id='hr-whm-grid'>
			{!!
			GridRender::setGridId("hr-whm-grid")
				//->enablefilterToolbar(false, false)
				->hideXlsExporter()
  			->hideCsvExporter()
				->setGridOption('rowList', array(20, 50, 100, 150, 250, 500))
				->setGridOption('rowNum', 20)
	    	->setGridOption('url',URL::to('human-resources/transactions/worked-hour-management/grid-data'))
	    	->setGridOption('caption', Lang::get('decima-human-resources::worked-hour-management.workedHours', array('user' => AuthManager::getLoggedUserFirstname())))
	    	->setGridOption('postData',array('_token' => Session::token()))
				->setGridOption('multiselect', false)
				->setGridOption('footerrow', true)
				->setGridEvent('onSelectRow', 'hrWhmOnSelectRowEvent')
				->setGridEvent('loadComplete', 'hrWhmOnLoadCompleteEvent')
				->addColumn(array('index' => 'w.id', 'name' => 'hr_whm_id', 'hidden' => true))
				->addColumn(array('index' => 'w.responsible_employee_id', 'name' => 'hr_whm_responsible_employee_id', 'hidden' => true))
				->addColumn(array('index' => 'w.task_id', 'name' => 'hr_whm_task_id', 'hidden' => true))
				->addColumn(array('label' => Lang::get('decima-human-resources::worked-hour-management.url'), 'index' => 'w.url' ,'name' => 'hr_whm_url', 'hidden' => true))
				->addColumn(array('label' => Lang::get('decima-human-resources::worked-hour-management.startDate'), 'index' => 'w.start_date', 'name' => 'hr_whm_start_date', 'width' => 100, 'align' => 'center', 'formatter' => 'date', 'formatoptions' => array('srcformat' => 'Y-m-d H:i:s', 'newformat' => Lang::get('form.phpDateTimeFormat'))))
				->addColumn(array('label' => Lang::get('decima-human-resources::worked-hour-management.endDate'), 'index' => 'w.end_date', 'name' => 'hr_whm_end_date', 'width' => 100, 'align' => 'center', 'formatter' => 'date', 'formatoptions' => array('srcformat' => 'Y-m-d H:i:s', 'newformat' => Lang::get('form.phpDateTimeFormat'))))
				->addColumn(array('label' => Lang::get('decima-human-resources::worked-hour-management.task'), 'index' => 'w.task_label' ,'name' => 'hr_whm_task_label'))
				->addColumn(array('label' => Lang::get('decima-human-resources::worked-hour-management.employee'), 'index' => 'w.responsible_employee', 'width' => 75, 'align' => 'center', 'name' => 'hr_whm_responsible_employee'))
				->addColumn(array('label' => Lang::get('decima-human-resources::worked-hour-management.description'), 'index' => 'w.description' ,'name' => 'hr_whm_description'))
				->addColumn(array('label' => '', 'index' => 'w.url' ,'name' => 'hr_whm_url_html', 'width' => '20', 'align' => 'center'))
				->addColumn(array('label' => Lang::get('form.hours'), 'index' => 'w.worked_hours' ,'name' => 'hr_whm_worked_hours', 'width' => 35, 'align' => 'center', 'formatter' => 'currency'))
	    	->renderGrid();
			!!}
		</div>
	</div>
</div>
<div id='hr-whm-sta-form-section' class="row collapse">
	<div class="col-lg-12 col-md-12">
		<div class="form-container">
			{!! Form::open(array('id' => 'hr-whm-sta-form', 'url' => URL::to('human-resources/transactions/worked-hour-management'), 'role'  =>  'form', 'onsubmit' => 'return false;')) !!}
			<legend id="hr-whm-sta-form-title" class="hidden">{{ Lang::get('decima-human-resources::worked-hour-management.formStartTitle') }}</legend>
				<div class="row">
					<div class="col-lg-6 col-md-6">
						<div class="form-group mg-hm">
							{!! Form::label('hr-whm-sta-start-date', Lang::get('decima-human-resources::worked-hour-management.startDate'), array('class' => 'control-label')) !!}
					    {!! Form::datetime('hr-whm-sta-start-date', array('class' => 'form-control', 'data-mg-required' => '', 'data-default-value' => $DateH), $DateH) !!}
					    {!! Form::hidden('hr-whm-sta-id', null, array('id' => 'hr-whm-sta-id')) !!}
			  		</div>
					</div>
					<div class="col-lg-6 col-md-6">
						<div class="form-group mg-hm">
								{!! Form::label('hr-whm-sta-responsible-employee-id', Lang::get('decima-human-resources::worked-hour-management.employee'), array('class' => 'control-label')) !!}
								{!! Form::autocomplete('hr-whm-sta-employee-label', $employees, array('class' => 'form-control', 'data-mg-required' => '', 'data-mg-clear-ignored '=> ''),'hr-whm-sta-employee-label', 'hr-whm-sta-responsible-employee-id',  null,  'fa-globe') !!}
								{!! Form::hidden('hr-whm-sta-responsible-employee-id', null, array('id'  =>  'hr-whm-sta-responsible-employee-id')) !!}
						</div>
					</div>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
<div id='hr-whm-endf-form-section' class="row collapse">
	<div class="col-lg-12 col-md-12">
		<div class="form-container">
			{!! Form::open(array('id' => 'hr-whm-endf-form', 'url' => URL::to('human-resources/transactions/worked-hour-management/'), 'role'  =>  'form', 'onsubmit' => 'return false;')) !!}
			<legend id="hr-whm-endf-form-title" class="hidden">{{ Lang::get('decima-human-resources::worked-hour-management.formEndTitle') }}</legend>
			<div class="row">
				<div class="col-lg-6 col-md-6">
					<div class="row">
						<div class="col-lg-6 col-md-6">
							<div class="form-group mg-hm">
								{!! Form::label('hr-whm-endf-start-date', Lang::get('decima-human-resources::worked-hour-management.startDate'), array('class' => 'control-label')) !!}
								{!! Form::datetime('hr-whm-endf-start-date', array('class' => 'form-control', 'data-mg-required' => '', 'disabled' => '', 'data-default-value' => $DateH), $DateH) !!}
								{!! Form::hidden('hr-whm-endf-id', null, array('id' => 'hr-whm-endf-id')) !!}
							</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="form-group mg-hm">
								{!! Form::label('hr-whm-endf-end-date', Lang::get('decima-human-resources::worked-hour-management.endDate'), array('class' => 'control-label')) !!}
								{!! Form::datetime('hr-whm-endf-end-date', array('class' => 'form-control', 'data-mg-required' => '', 'data-default-value' => $DateH), $DateH) !!}
							</div>
						</div>
					</div>
					<div class="form-group mg-hm">
							{!! Form::label('hr-whm-endf-task-id', Lang::get('decima-human-resources::worked-hour-management.task'), array('class' => 'control-label')) !!}
							{!! Form::autocomplete('hr-whm-endf-task-label', $task, array('class' => 'form-control'),'hr-whm-endf-task-label', 'hr-whm-endf-task-id',  null,  'fa-globe') !!}
							{!! Form::hidden('hr-whm-endf-task-id', null, array('id'  =>  'hr-whm-endf-task-id')) !!}
					</div>
					<div class="form-group">
						<div class="form-group mg-hm" style="padding-top: 5px">
							{!! Form::label('hr-whm-endf-url', Lang::get('decima-human-resources::worked-hour-management.url'), array('class' => 'control-label')) !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-sitemap"></i>
								</span>
								{!! Form::text('hr-whm-endf-url', null , array('id' => 'hr-whm-endf-url', 'class' => 'form-control')) !!}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-md-6">
					<div class="form-group mg-hm">
							{!! Form::label('hr-whm-endf-responsible-employee', Lang::get('decima-human-resources::worked-hour-management.employee'), array('class' => 'control-label')) !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-sitemap"></i>
								</span>
								{!! Form::text('hr-whm-endf-responsible-employee', null , array('id' => 'hr-whm-endf-responsible-employee', 'disabled' => '', 'class' => 'form-control')) !!}
								{!! Form::hidden('hr-whm-endf-responsible-employee-id', null, array('id' => 'hr-whm-endf-responsible-employee-id')) !!}
							</div>
							<p class="help-block">&nbsp;</p>
					</div>
					<div class="form-group">
						<div class="form-group mg-hm">
							{!! Form::label('hr-whm-endf-description', Lang::get('decima-human-resources::worked-hour-management.description'), array('class' => 'control-label')) !!}
							{!! Form::textareacustom('hr-whm-endf-description', 4, 500, array('class' => 'form-control')) !!}
						</div>
					</div>
				</div>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
<div id='hr-whm-journals-section' class="row collapse in section-block" data-target-id="">
	{!! Form::journals('hr-whm-', $appInfo['id']) !!}
</div>
<div id='hr-whm-modal-delete' class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm hr-whm-btn-delete">
    <div class="modal-content">
			<div class="modal-body" style="padding: 20px 20px 0px 20px;">
				<p id="hr-whm-delete-message" data-default-label="{{ Lang::get('form.deleteMessageConfirmation') }}">{{ Lang::get('form.deleteMessageConfirmation') }}</p>
      </div>
			<div class="modal-footer" style="text-align:center;">
				<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get('form.no') }}</button>
				<button id="hr-whm-btn-modal-delete" type="button" class="btn btn-primary">{{ Lang::get('form.yes') }}</button>
			</div>
    </div>
  </div>
</div>
@parent
@stop
