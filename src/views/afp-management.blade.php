@extends('layouts.base')

@section('container')
{!! Form::hidden('hr-afp-new-action', null, array('id' => 'hr-afp-new-action')) !!}
{!! Form::hidden('hr-afp-edit-action', null, array('id' => 'hr-afp-edit-action', 'data-content' => Lang::get('module::app.editHelpText'))) !!}
{!! Form::hidden('hr-afp-remove-action', null, array('id' => 'hr-afp-remove-action', 'data-content' => Lang::get('module::app.editHelpText'))) !!}
{!! Form::button('', array('id' => 'hr-afp-btn-edit-helper', 'class' => 'hidden')) !!}
{!! Form::button('', array('id' => 'hr-afp-btn-delete-helper', 'class' => 'hidden')) !!}
<style></style>

<script type='text/javascript'>
	//Falta agregar  codigo para quitar tooltip
	//For grids with multiselect enabled
	function hrAfpOnSelectRowEvent(id)
	{
		var selRowIds = $('#hr-afp-grid').jqGrid('getGridParam', 'selarrrow');

		if(selRowIds.length == 0)
		{
			$('#hr-afp-btn-group-2').disabledButtonGroup();
			cleanJournals('hr-afp-');
		}
		else if(selRowIds.length == 1)
		{
			$('#hr-afp-btn-group-2').enableButtonGroup();
			cleanJournals('hr-afp-');
			getAppJournals('hr-afp-','firstPage', $('#hr-afp-grid').getSelectedRowId('hr_afp_id'));
		}
		else if(selRowIds.length > 1)
		{
			$('#hr-afp-btn-group-2').disabledButtonGroup();
			$('#hr-afp-btn-delete').removeAttr('disabled');
			cleanJournals('hr-afp-');
		}
	}

	/*
	//For grids with multiselect disabled
	function hrAfpOnSelectRowEvent()
	{
		var id = $('#hr-afp-grid').getSelectedRowId('hr_afp_id');

		getAppJournals('hr-afp-', 'firstPage', id);

		$('#hr-afp-btn-group-2').enableButtonGroup();
	}
	*/

	$(document).ready(function()
	{
		$('.hr-afp-btn-tooltip').tooltip();

		$('#hr-afp-form').jqMgVal('addFormFieldsValidations');

		$('#hr-afp-journals-section').on('hidden.bs.collapse', function ()
		{
			$($(this).attr('data-target-id')).collapse('show');
		});

		$('#hr-afp-form-section').on('shown.bs.collapse', function ()
		{
			$('#hr-afp-name').focus();
		});

		$('#hr-afp-form-section').on('hidden.bs.collapse', function ()
		{
			$('#hr-afp-grid-section').collapse('show');

			$('#hr-afp-journals-section').collapse('show');
		});

		$('#hr-afp-btn-new').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			$('#hr-afp-btn-toolbar').disabledButtonGroup();
			$('#hr-afp-btn-group-3').enableButtonGroup();
			$('#hr-afp-form-new-title').removeClass('hidden');
			$('#hr-afp-grid-section').collapse('hide');
			$('#hr-afp-journals-section').attr('data-target-id', '#hr-afp-form-section');
			$('#hr-afp-journals-section').collapse('hide');
			$('.hr-afp-btn-tooltip').tooltip('hide');
		});

		$('#hr-afp-btn-refresh').click(function()
		{
			$('.hr-afp-btn-tooltip').tooltip('hide');
			$('#hr-afp-btn-toolbar').disabledButtonGroup();
			$('#hr-afp-btn-group-1').enableButtonGroup();

			if($('#hr-afp-journals-section').attr('data-target-id') == '' || $('#hr-afp-journals-section').attr('data-target-id') == '#hr-afp-form-section')
			{
				$('#hr-afp-grid').trigger('reloadGrid');
				cleanJournals('hr-afp-');
			}
		});

		$('#hr-afp-btn-export-xls').click(function()
		{
				$('#hr-afp-gridXlsButton').click();
		});

		$('#hr-afp-btn-export-csv').click(function()
		{
				$('#hr-afp-gridCsvButton').click();
		});

		$('#hr-afp-btn-edit').click(function()
		{
			var rowData;

			$('.hr-afp-btn-tooltip').tooltip('hide');
			$('#hr-afp-btn-toolbar').disabledButtonGroup();
			$('#hr-afp-btn-group-3').enableButtonGroup();

			if($('#hr-afp-journals-section').attr('data-target-id') == '')
			{
				if(!$('#hr-afp-grid').isRowSelected())
				{
					$('#hr-afp-btn-toolbar').showAlertAfterElement('alert-info alert-custom', lang.invalidSelection, 5000);
					return;
				}

				$('#hr-afp-form-edit-title').removeClass('hidden');

				rowData = $('#hr-afp-grid').getRowData($('#hr-afp-grid').jqGrid('getGridParam', 'selrow'));

				populateFormFields(rowData);

				$('#hr-afp-grid-section').collapse('hide');
				$('#hr-afp-journals-section').attr('data-target-id', '#hr-afp-form-section');
				$('#hr-afp-journals-section').collapse('hide');
			}
			else
			{

			}
		});

		$('#hr-afp-btn-delete').click(function()
		{
			var rowData;

			if($(this).hasAttr('disabled'))
			{
				return;
			}

			if($('#hr-afp-journals-section').attr('data-target-id') == '')
			{
				if(!$('#hr-afp-grid').isRowSelected())
				{
					$('#hr-afp-btn-toolbar').showAlertAfterElement('alert-info alert-custom', lang.invalidSelection, 5000);
					return;
				}

				rowData = $('#hr-afp-grid').getRowData($('#hr-afp-grid').jqGrid('getGridParam', 'selrow'));

				$('#hr-afp-delete-message').html($('#hr-afp-delete-message').attr('data-default-label').replace(':description', rowData.hr_afp_description));
			}
			else
			{

			}

			$('.hr-afp-btn-tooltip').tooltip('hide');
			$('#hr-afp-modal-delete').modal('show');
		});

		$('#hr-afp-btn-modal-delete').click(function()
		{
			//For grids with multiselect enabled
			var id = $('#hr-afp-grid').getSelectedRowsIdCell('hr_afp_id');

			if(id.length == 0)
			{
				return;
			}

			//For grids with multiselect disabled
			// var id = $('#hr-afp-grid').getSelectedRowId('hr_afp_id');

			if($('#hr-afp-journals-section').attr('data-target-id') == '#hr-afp-form-section')
			{
				var id = $('#hr-afp-grid').getSelectedRowsIdCell('hr_afp_id');
			}
			else
			{

			}

			$.ajax(
			{
				type: 'POST',
				data: JSON.stringify({'_token':$('#app-token').val(), 'id':id}),
				dataType : 'json',
				url:  $('#hr-afp-form').attr('action') + '/delete',
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-afp-btn-toolbar', false);
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
						$('#hr-afp-btn-refresh').click();
						$('#hr-afp-modal-delete').modal('hide');
						$('#hr-afp-btn-toolbar').showAlertAfterElement('alert-success alert-custom',json.success, 5000);
					}

					$('#app-loader').addClass('hidden');
					enableAll();
				}
			});
		});

		$('#hr-afp-btn-save').click(function()
		{
			var url = $('#hr-afp-form').attr('action'), action = 'new';

			$('.hr-afp-btn-tooltip').tooltip('hide');

			if($('#hr-afp-journals-section').attr('data-target-id') == '#hr-afp-form-section')
			{
				if(!$('#hr-afp-form').jqMgVal('isFormValid'))
				{
					return;
				}

				if($('#hr-afp-id').isEmpty())
				{
					url = url + '/create';
				}
				else
				{
					url = url + '/update';
					action = 'edit';
				}

				data = $('#hr-afp-form').formToObject('hr-afp-');
			}
			else
			{

			}

			$.ajax(
			{
				type: 'POST',
				data: JSON.stringify(data),
				dataType : 'json',
				url: url,
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-afp-form');
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
						if($('#hr-afp-journals-section').attr('data-target-id') == '#hr-afp-form-section')
						{
							$('#hr-afp-btn-close').click();
							$('#hr-afp-btn-toolbar').showAlertAfterElement('alert-success alert-custom',json.success, 6000);
						}
						else
						{
							// $('#hr-afp-form').showAlertAsFirstChild('alert-success', json.info);
						}
					}
					else if(json.info)
					{
						if($('#hr-afp-journals-section').attr('data-target-id') == '#hr-afp-form-section')
						{
							$('#hr-afp-form').showAlertAsFirstChild('alert-info', json.info);
						}
						else
						{
							// $('#hr-afp-form').showAlertAsFirstChild('alert-info', json.info);
						}
					}

					$('#app-loader').addClass('hidden');
					enableAll();
				}
			});
		});

		$('#hr-afp-btn-close').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			// hr-afp-form-section
			if($('#hr-afp-journals-section').attr('data-target-id') == '#hr-afp-form-section')
			{
				$('#hr-afp-form-new-title').addClass('hidden');
				$('#hr-afp-form-edit-title').addClass('hidden');
				$('#hr-afp-btn-refresh').click();
				$('#hr-afp-form').jqMgVal('clearForm');
				$('#hr-afp-form-section').collapse('hide');
			}
			else
			{

			}

			$('#hr-afp-btn-group-1').enableButtonGroup();
			$('#hr-afp-btn-group-3').disabledButtonGroup();
			$('.hr-afp-btn-tooltip').tooltip('hide');
			$('#hr-afp-journals-section').attr('data-target-id', '')
		});

		$('#hr-afp-btn-edit-helper').click(function()
	  {
			showButtonHelper('hr-afp-btn-close', 'hr-afp-btn-group-2', $('#hr-afp-edit-action').attr('data-content'));
	  });

		$('#hr-afp-btn-delete-helper').click(function()
	  {
			showButtonHelper('hr-afp-btn-close', 'hr-afp-btn-group-2', $('#hr-afp-delete-action').attr('data-content'));
	  });

		if(!$('#hr-afp-new-action').isEmpty())
		{
			$('#hr-afp-btn-new').click();
		}

		if(!$('#hr-afp-edit-action').isEmpty())
		{
			showButtonHelper('hr-afp-btn-close', 'hr-afp-btn-group-2', $('#hr-afp-edit-action').attr('data-content'));
		}

		if(!$('#hr-afp-delete-action').isEmpty())
		{
			showButtonHelper('hr-afp-btn-close', 'hr-afp-btn-group-2', $('#hr-afp-delete-action').attr('data-content'));
		}
	});
</script>

<div class="row">
	<div class="col-lg-12 col-md-12">
		<div id="hr-afp-btn-toolbar" class="section-header btn-toolbar" role="toolbar">
			<div id="hr-afp-btn-group-1" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-plus"></i> ' . Lang::get('toolbar.new'), array('id' => 'hr-afp-btn-new', 'class' => 'btn btn-default hr-afp-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('decima-human-resources::afp-management.new'))) !!}
				{!! Form::button('<i class="fa fa-refresh"></i> ' . Lang::get('toolbar.refresh'), array('id' => 'hr-afp-btn-refresh', 'class' => 'btn btn-default hr-afp-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('toolbar.refreshLongText'))) !!}
				<div class="btn-group">
					{!! Form::button('<i class="fa fa-share-square-o"></i> ' . Lang::get('toolbar.export') . ' <span class="caret"></span>', array('class' => 'btn btn-default dropdown-toggle', 'data-container' => 'body', 'data-toggle' => 'dropdown')) !!}
					<ul class="dropdown-menu">
         		<li><a id='hr-afp-btn-export-xls' class="fake-link"><i class="fa fa-file-excel-o"></i> xls</a></li>
         		<li><a id='hr-afp-btn-export-csv' class="fake-link"><i class="fa fa-file-text-o"></i> csv</a></li>
       		</ul>
				</div>
			</div>
			<div id="hr-afp-btn-group-2" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-edit"></i> ' . Lang::get('toolbar.edit'), array('id' => 'hr-afp-btn-edit', 'class' => 'btn btn-default hr-afp-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::afp-management.edit'))) !!}
				{!! Form::button('<i class="fa fa-minus"></i> ' . Lang::get('toolbar.delete'), array('id' => 'hr-afp-btn-delete', 'class' => 'btn btn-default hr-afp-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::afp-management.delete'))) !!}
			</div>
			<div id="hr-afp-btn-group-3" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-save"></i> ' . Lang::get('toolbar.save'), array('id' => 'hr-afp-btn-save', 'class' => 'btn btn-default hr-afp-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::afp-management.save'))) !!}
				{!! Form::button('<i class="fa fa-undo"></i> ' . Lang::get('toolbar.close'), array('id' => 'hr-afp-btn-close', 'class' => 'btn btn-default hr-afp-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('toolbar.closeLongText'))) !!}
			</div>
		</div>
		<div id='hr-afp-grid-section' class='app-grid collapse in' data-app-grid-id='hr-afp-grid'>
			{!!
			GridRender::setGridId("hr-afp-grid")
				->enablefilterToolbar(false, false)
				->hideXlsExporter()
  			->hideCsvExporter()
	    	->setGridOption('url',URL::to('human-resources/setup/afp-management/grid-data'))
	    	->setGridOption('caption', Lang::get('decima-human-resources::afp-management.gridTitle', array('user' => AuthManager::getLoggedUserFirstname())))
	    	->setGridOption('postData',array('_token' => Session::token()))
				->setGridEvent('onSelectRow', 'hrAfpOnSelectRowEvent')
	    	->addColumn(array('index' => 't1.id', 'name' => 'hr_afp_id', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::afp-management.name'), 'index' => 't1.name' ,'name' => 'hr_afp_name', 'align' => 'center'))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::afp-management.commission'), 'index' => 't1.commission' ,'name' => 'hr_afp_commission', 'align' => 'center'))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::afp-management.employerContribution'), 'index' => 't1.employer_contribution' ,'name' => 'hr_afp_employer_contribution', 'align' => 'center'))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::afp-management.employeeContribution'), 'index' => 't1.employee_contribution' ,'name' => 'hr_afp_employee_contribution', 'align' => 'center'))
				->renderGrid();
			!!}
		</div>
	</div>
</div>
<div id='hr-afp-journals-section' class="row collapse in section-block" data-target-id="">
	{!! Form::journals('hr-afp-', $appInfo['id']) !!}
</div>
<div id='hr-afp-form-section' class="row collapse">
	<div class="col-lg-12 col-md-12">
		<div class="form-container">
			{!! Form::open(array('id' => 'hr-afp-form', 'url' => URL::to('human-resources/setup/afp-management'), 'role'  =>  'form', 'onsubmit' => 'return false;')) !!}
				<legend id="hr-afp-form-new-title" class="hidden">{{ Lang::get('decima-human-resources::afp-management.formNewTitle') }}</legend>
				<legend id="hr-afp-form-edit-title" class="hidden">{{ Lang::get('decima-human-resources::afp-management.formEditTitle') }}</legend>
				<div class="row">
					<div class="col-lg-6 col-md-6">
						<div class="form-group mg-hm">
							{!! Form::label('hr-afp-name', Lang::get('decima-human-resources::afp-management.name'), array('class' => 'control-label')) !!}
					    {!! Form::text('hr-afp-name', null , array('id' => 'hr-afp-name', 'class' => 'form-control', 'data-mg-required' => '')) !!}
					    {!! Form::hidden('hr-afp-id', null, array('id' => 'hr-afp-id')) !!}
			  		</div>
						<div class="form-group mg-hm">
							{!! Form::label('hr-afp-commission', Lang::get('decima-human-resources::afp-management.commission'), array('class' => 'control-label')) !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-percent"></i>
								</span>
								{!! Form::text('hr-afp-commission', null , array('id' => 'hr-afp-commission', 'class' => 'form-control', 'data-mg-validator' => 'money')) !!}
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-6">
						<div class="form-group mg-hm">
							{!! Form::label('hr-afp-employer-contribution', Lang::get('decima-human-resources::afp-management.employerContribution'), array('class' => 'control-label')) !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-percent"></i>
								</span>
								{!! Form::text('hr-afp-employer-contribution', null , array('id' => 'hr-afp-employer-contribution', 'class' => 'form-control', 'data-mg-validator' => 'money')) !!}
							</div>
							<p class="help-block">&nbsp;</p>
						</div>
						<div class="form-group mg-hm">
							{!! Form::label('hr-afp-employee-contribution', Lang::get('decima-human-resources::afp-management.employeeContribution'), array('class' => 'control-label')) !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-percent"></i>
								</span>
								{!! Form::text('hr-afp-employee-contribution', null , array('id' => 'hr-afp-employee-contribution', 'class' => 'form-control', 'data-mg-validator' => 'money')) !!}
							</div>
							<p class="help-block">&nbsp;</p>
						</div>
					</div>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
<div id='hr-afp-modal-delete' class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm hr-afp-btn-delete">
    <div class="modal-content">
			<div class="modal-body" style="padding: 20px 20px 0px 20px;">
				<p id="hr-afp-delete-message" data-default-label="{{ Lang::get('decima-human-resources::afp-management.deleteMessageConfirmation') }}"></p>
      </div>
			<div class="modal-footer" style="text-align:center;">
				<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get('form.no') }}</button>
				<button id="hr-afp-btn-modal-delete" type="button" class="btn btn-primary">{{ Lang::get('form.yes') }}</button>
			</div>
    </div>
  </div>
</div>
@parent
@stop
