@extends('layouts.base')

@section('container')
{!! Form::hidden('hr-ltm-new-action', null, array('id' => 'hr-ltm-new-action')) !!}
{!! Form::hidden('hr-ltm-edit-action', null, array('id' => 'hr-ltm-edit-action', 'data-content' => Lang::get('module::app.editHelpText'))) !!}
{!! Form::hidden('hr-ltm-remove-action', null, array('id' => 'hr-ltm-remove-action', 'data-content' => Lang::get('module::app.editHelpText'))) !!}
{!! Form::button('', array('id' => 'hr-ltm-btn-edit-helper', 'class' => 'hidden')) !!}
{!! Form::button('', array('id' => 'hr-ltm-btn-delete-helper', 'class' => 'hidden')) !!}
<style></style>

<script type='text/javascript'>
	//Falta agregar  codigo para quitar tooltip
	//For grids with multiselect enabled
	function hrEmOnSelectRowEvent(id)
	{
		var selRowIds = $('#hr-ltm-grid').jqGrid('getGridParam', 'selarrrow');

		if(selRowIds.length == 0)
		{
			$('#hr-ltm-btn-group-2').disabledButtonGroup();
			cleanJournals('hr-ltm-');
		}
		else if(selRowIds.length == 1)
		{
			$('#hr-ltm-btn-group-2').enableButtonGroup();
			cleanJournals('hr-ltm-');
			getAppJournals('hr-ltm-','firstPage', $('#hr-ltm-grid').getSelectedRowId());
		}
		else if(selRowIds.length > 1)
		{
			$('#hr-ltm-btn-group-2').disabledButtonGroup();
			$('#hr-ltm-btn-delete').removeAttr('disabled');
			cleanJournals('hr-ltm-');
		}
	}

	/*
	//For grids with multiselect disabled
	function hrEmOnSelectRowEvent()
	{
		var id = $('#hr-ltm-grid').getSelectedRowId('module_app_id');

		getAppJournals('hr-ltm-', 'firstPage', id);

		$('#hr-ltm-btn-group-2').enableButtonGroup();
	}
	*/

	$(document).ready(function()
	{
		$('.hr-ltm-btn-tooltip').tooltip();

		$('#hr-ltm-form').jqMgVal('addFormFieldsValidations');

		$('#hr-ltm-grid-section').on('shown.bs.collapse', function ()
		{
			$('#hr-ltm-btn-refresh').click();
		});

		$('#hr-ltm-journals-section').on('hidden.bs.collapse', function ()
		{
			$('#hr-ltm-form-section').collapse('show');
		});

		$('#hr-ltm-form-section').on('shown.bs.collapse', function ()
		{
			$('#hr-ltm-name').focus();
		});

		$('#hr-ltm-form-section').on('hidden.bs.collapse', function ()
		{
			$('#hr-ltm-grid-section').collapse('show');

			$('#hr-ltm-journals-section').collapse('show');
		});

		$('#hr-ltm-btn-new').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			$('#hr-ltm-btn-toolbar').disabledButtonGroup();
			$('#hr-ltm-btn-group-3').enableButtonGroup();
			$('#hr-ltm-form-new-title').removeClass('hidden');
			$('#hr-ltm-grid-section').collapse('hide');
			$('#hr-ltm-journals-section').collapse('hide');
			$('.hr-ltm-btn-tooltip').tooltip('hide');
		});

		$('#hr-ltm-btn-refresh').click(function()
		{
			$('.hr-ltm-btn-tooltip').tooltip('hide');
			$('#hr-ltm-grid').trigger('reloadGrid');
			cleanJournals('hr-ltm-');
		});

		$('#hr-ltm-btn-export-xls').click(function()
		{
				$('#hr-ltm-gridXlsButton').click();
		});

		$('#hr-ltm-btn-export-csv').click(function()
		{
				$('#hr-ltm-gridCsvButton').click();
		});

		$('#hr-ltm-btn-edit').click(function()
		{
			var rowData;

			$('#hr-ltm-btn-toolbar').disabledButtonGroup();
			$('#hr-ltm-btn-group-3').enableButtonGroup();
			$('#hr-ltm-form-edit-title').removeClass('hidden');

			rowData = $('#hr-ltm-grid').getRowData($('#hr-ltm-grid').jqGrid('getGridParam', 'selrow'));

			populateFormFields(rowData);

			$('#hr-ltm-grid-section').collapse('hide');
			$('#hr-ltm-journals-section').collapse('hide');
			$('.hr-ltm-btn-tooltip').tooltip('hide');
		});

		$('#hr-ltm-btn-delete').click(function()
		{
			var rowData;

			if($(this).hasAttr('disabled'))
			{
				return;
			}

			rowData = $('#hr-ltm-grid').getRowData($('#hr-ltm-grid').jqGrid('getGridParam', 'selrow'));

			$('#hr-ltm-delete-message').html($('#hr-ltm-delete-message').attr('data-default-label').replace(':name', rowData.hr_ltm_name));

			$('.hr-ltm-btn-tooltip').tooltip('hide');

			$('#hr-ltm-modal-delete').modal('show');
		});

		$('#hr-ltm-btn-modal-delete').click(function()
		{
			//For grids with multiselect enabled
			var id = $('#hr-ltm-grid').getSelectedRowsIdCell('hr_ltm_id');

			if(id.length == 0)
			{
				return;
			}

			//For grids with multiselect disabled
			// var id = $('#hr-ltm-grid').getSelectedRowId('module_app_id');

			$.ajax(
			{
				type: 'POST',
				data: JSON.stringify({'_token':$('#app-token').val(), 'id':id}),
				dataType : 'json',
				url:  $('#hr-ltm-form').attr('action') + '/delete',
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-ltm-btn-toolbar', false);
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
						$('#hr-ltm-btn-refresh').click();
						$('#hr-ltm-modal-delete').modal('hide');
						$('#hr-ltm-btn-toolbar').showAlertAfterElement('alert-success alert-custom',json.success, 5000);
					}

					$('#app-loader').addClass('hidden');
					enableAll();
				}
			});
		});

		$('#hr-ltm-btn-save').click(function()
		{
			var url = $('#hr-ltm-form').attr('action'), action = 'new';

			$('.hr-ltm-btn-tooltip').tooltip('hide');

			if(!$('#hr-ltm-form').jqMgVal('isFormValid'))
			{
				return;
			}

			if($('#hr-ltm-id').isEmpty())
			{
				url = url + '/create';
			}
			else
			{
				url = url + '/update';
				action = 'edit';
			}

			$.ajax(
			{
				type: 'POST',
				data: JSON.stringify($('#hr-ltm-form').formToObject('hr-ltm-')),
				dataType : 'json',
				url: url,
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-ltm-form');
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
						$('#hr-ltm-btn-close').click();
					}
					else if(json.info)
					{
						$('#hr-ltm-form').showAlertAsFirstChild('alert-info', json.info);
					}

					$('#app-loader').addClass('hidden');
					enableAll();
				}
			});
		});

		$('#hr-ltm-btn-close').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			$('#hr-ltm-btn-group-1').enableButtonGroup();
			$('#hr-ltm-btn-group-3').disabledButtonGroup();
			$('#hr-ltm-form-new-title').addClass('hidden');
			$('#hr-ltm-form-edit-title').addClass('hidden');
			$('#hr-ltm-grid').jqGrid('clearGridData');
			$('#hr-ltm-form').jqMgVal('clearForm');
			$('.hr-ltm-btn-tooltip').tooltip('hide');
			$('#hr-ltm-form-section').collapse('hide');
		});
	});

	$('#hr-ltm-btn-edit-helper').click(function()
  {
		showButtonHelper('hr-ltm-btn-close', 'hr-ltm-btn-group-2', $('#hr-ltm-edit-action').attr('data-content'));
  });

	$('#hr-ltm-btn-delete-helper').click(function()
  {
		showButtonHelper('hr-ltm-btn-close', 'hr-ltm-btn-group-2', $('#hr-ltm-delete-action').attr('data-content'));
  });

	if(!$('#hr-ltm-new-action').isEmpty())
	{
		$('#hr-ltm-btn-new').click();
	}

	if(!$('#hr-ltm-edit-action').isEmpty())
	{
		showButtonHelper('hr-ltm-btn-close', 'hr-ltm-btn-group-2', $('#hr-ltm-edit-action').attr('data-content'));
	}

	if(!$('#hr-ltm-delete-action').isEmpty())
	{
		showButtonHelper('hr-ltm-btn-close', 'hr-ltm-btn-group-2', $('#hr-ltm-delete-action').attr('data-content'));
	}
</script>

<div class="row">
	<div class="col-lg-12 col-md-12">
		<div id="hr-ltm-btn-toolbar" class="section-header btn-toolbar" role="toolbar">
			<div id="hr-ltm-btn-group-1" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-plus"></i> ' . Lang::get('toolbar.new'), array('id' => 'hr-ltm-btn-new', 'class' => 'btn btn-default hr-ltm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('decima-human-resources::leave-type-management.new'))) !!}
				{!! Form::button('<i class="fa fa-refresh"></i> ' . Lang::get('toolbar.refresh'), array('id' => 'hr-ltm-btn-refresh', 'class' => 'btn btn-default hr-ltm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('toolbar.refreshLongText'))) !!}
				<div class="btn-group">
					{!! Form::button('<i class="fa fa-share-square-o"></i> ' . Lang::get('toolbar.export') . ' <span class="caret"></span>', array('class' => 'btn btn-default dropdown-toggle', 'data-container' => 'body', 'data-toggle' => 'dropdown')) !!}
					<ul class="dropdown-menu">
         		<li><a id='hr-ltm-btn-export-xls' class="fake-link"><i class="fa fa-file-excel-o"></i> xls</a></li>
         		<li><a id='hr-ltm-btn-export-csv' class="fake-link"><i class="fa fa-file-text-o"></i> csv</a></li>
       		</ul>
				</div>
			</div>
			<div id="hr-ltm-btn-group-2" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-edit"></i> ' . Lang::get('toolbar.edit'), array('id' => 'hr-ltm-btn-edit', 'class' => 'btn btn-default hr-ltm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::leave-type-management.edit'))) !!}
				{!! Form::button('<i class="fa fa-minus"></i> ' . Lang::get('toolbar.delete'), array('id' => 'hr-ltm-btn-delete', 'class' => 'btn btn-default hr-ltm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::leave-type-management.delete'))) !!}
			</div>
			<div id="hr-ltm-btn-group-3" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-save"></i> ' . Lang::get('toolbar.save'), array('id' => 'hr-ltm-btn-save', 'class' => 'btn btn-default hr-ltm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::leave-type-management.save'))) !!}
				{!! Form::button('<i class="fa fa-undo"></i> ' . Lang::get('toolbar.close'), array('id' => 'hr-ltm-btn-close', 'class' => 'btn btn-default hr-ltm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('toolbar.closeLongText'))) !!}
			</div>
		</div>
		<div id='hr-ltm-grid-section' class='app-grid collapse in' data-app-grid-id='hr-ltm-grid'>
			{!!GridRender::setGridId("hr-ltm-grid")
				->enablefilterToolbar(false, false)
				->hideXlsExporter()
	  			->hideCsvExporter()
		    	->setGridOption('url',URL::to('human-resources/setup/leave-type-management/grid-data'))
		    	->setGridOption('caption', Lang::get('decima-human-resources::leave-type-management.gridTitle', array('user' => AuthManager::getLoggedUserFirstname())))
		    	->setGridOption('postData',array('_token' => Session::token()))
				->setGridEvent('onSelectRow', 'hrEmOnSelectRowEvent')
		    	->addColumn(array('index' => 't1.id', 'name' => 'hr_ltm_id', 'hidden' => true))
		    	->addColumn(array('label' => Lang::get('decima-human-resources::leave-type-management.name'), 'index' => 't1.name' ,'name' => 'hr_ltm_name'))
		    	->addColumn(array('label' => Lang::get('decima-human-resources::leave-type-management.maxDaysLeaveAllowedShort'), 'index' => 't1.max_days_leave_allowed' ,'name' => 'hr_ltm_max_days_leave_allowed' ,  'align' => 'center'))
				->addColumn(array('label' => Lang::get('decima-human-resources::leave-type-management.isLeaveWithoutPayShort'), 'index' => 't1.is_leave_without_pay' ,'name' => 'hr_ltm_is_leave_without_pay' , 'formatter' => 'select', 'editoptions' => array('value' => Lang::get('form.booleanText')), 'align' => 'center' ))
				->addColumn(array('label' => Lang::get('decima-human-resources::leave-type-management.includeHolidaysWithinLeavesAsLeavesShort'), 'index' => 't1.include_holidays_within_leaves_as_leaves' ,'name' => 'hr_ltm_include_holidays_within_leaves_as_leaves' , 'formatter' => 'select', 'editoptions' => array('value' => Lang::get('form.booleanText')), 'align' => 'center'))
	    	->renderGrid();!!}
		</div>
	</div>
</div>
<div id='hr-ltm-journals-section' class="row collapse in section-block">
	{!! Form::journals('hr-ltm-', $appInfo['id']) !!}
</div>
<div id='hr-ltm-form-section' class="row collapse">
	<div class="col-lg-12 col-md-12">
		<div class="form-container">
			{!! Form::open(array('id' => 'hr-ltm-form', 'url' => URL::to('human-resources/setup/leave-type-management'), 'role'  =>  'form', 'onsubmit' => 'return false;')) !!}
				<legend id="hr-ltm-form-new-title" class="hidden">{{ Lang::get('decima-human-resources::leave-type-management.formNewLeave-Type') }}</legend>
				<legend id="hr-ltm-form-edit-title" class="hidden">{{ Lang::get('decima-human-resources::leave-type-management.formEditLeave-Type') }}</legend>
				<div class="row">
					<div class="col-lg-6 col-md-6">
						<div class="form-group mg-hm">
							{!! Form::label('hr-ltm-name', Lang::get('decima-human-resources::leave-type-management.name'), array('class' => 'control-label')) !!}
				    		{!! Form::text('hr-ltm-name', null , array('id' => 'hr-ltm-name', 'class' => 'form-control', 'data-mg-required' => '')) !!}
				    		{!! Form::hidden('hr-ltm-id', null, array('id' => 'hr-ltm-id')) !!}
		  			</div>
						<div class="form-group checkbox">
							<label class="control-label">
								{!! Form::checkbox('hr-ltm-is-leave-without-pay', 'S' , array('id' => 'hr-ltm-is-leave-without-pay')) !!}
								{{ Lang::get('decima-human-resources::leave-type-management.salary') }}
							</label>
						</div>
					</div>
					<div class="col-lg-6 col-md-6">
						<div class="form-group mg-hm">
							{!! Form::label('hr-ltm-max-days-leave-allowed', Lang::get('decima-human-resources::leave-type-management.maxDaysLeaveAllowed'), array('class' => 'control-label')) !!}
				    		{!! Form::text('hr-ltm-max-days-leave-allowed', null , array('id' => 'hr-ltm-max-days-leave-allowed', 'class' => 'form-control', 'data-mg-required' => '')) !!}
		  			</div>
						<div class="form-group checkbox">
							<label class="control-label">
								{!! Form::checkbox('hr-ltm-include-holidays-within-leaves-as-leaves', 'S' , array('id' => 'hr-ltm-include-holidays-within-leaves-as-leaves' ,'name' => 'hr-leave-type-include-holidays-within-leaves-as-leaves')) !!}
								{{ Lang::get('decima-human-resources::leave-type-management.includeHolidaysWithinLeavesAsLeaves') }}
							</label>
						</div>
					</div>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
<div id='hr-ltm-modal-delete' class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm hr-ltm-btn-delete">
    <div class="modal-content">
			<div class="modal-body" style="padding: 20px 20px 0px 20px;">
				<p id="hr-ltm-delete-message" data-default-label="{{ Lang::get('decima-human-resources::leave-type-management.deleteMessageConfirmation') }}"></p>
      </div>
			<div class="modal-footer" style="text-align:center;">
				<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get('form.no') }}</button>
				<button id="hr-ltm-btn-modal-delete" type="button" class="btn btn-primary">{{ Lang::get('form.yes') }}</button>
			</div>
    </div>
  </div>
</div>
@parent
@stop
