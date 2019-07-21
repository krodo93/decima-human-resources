@extends('layouts.base')

@section('container')
{!! Form::hidden('hr-pm-new-action', null, array('id' => 'hr-pm-new-action')) !!}
{!! Form::hidden('hr-pm-edit-action', null, array('id' => 'hr-pm-edit-action', 'data-content' => Lang::get('module::app.editHelpText'))) !!}
{!! Form::hidden('hr-pm-remove-action', null, array('id' => 'hr-pm-remove-action', 'data-content' => Lang::get('module::app.editHelpText'))) !!}
{!! Form::button('', array('id' => 'hr-pm-btn-edit-helper', 'class' => 'hidden')) !!}
{!! Form::button('', array('id' => 'hr-pm-btn-delete-helper', 'class' => 'hidden')) !!}
<style></style>

<script type='text/javascript'>
	//Falta agregar  codigo para quitar tooltip
	//For grids with multiselect enabled
	function hrPmOnSelectRowEvent(id)
	{
		var selRowIds = $('#hr-pm-grid').jqGrid('getGridParam', 'selarrrow');

		if(selRowIds.length == 0)
		{
			$('#hr-pm-btn-group-2').disabledButtonGroup();
			cleanJournals('hr-pm-');
		}
		else if(selRowIds.length == 1)
		{
			$('#hr-pm-btn-group-2').enableButtonGroup();
			cleanJournals('hr-pm-');
			getAppJournals('hr-pm-','firstPage', $('#hr-pm-grid').getSelectedRowId('hr_pm_id'));
		}
		else if(selRowIds.length > 1)
		{
			$('#hr-pm-btn-group-2').disabledButtonGroup();
			$('#hr-pm-btn-delete').removeAttr('disabled');
			cleanJournals('hr-pm-');
		}
	}

	/*
	//For grids with multiselect disabled
	function hrPmOnSelectRowEvent()
	{
		var id = $('#hr-pm-grid').getSelectedRowId('module_app_id');

		getAppJournals('hr-pm-', 'firstPage', id);

		$('#hr-pm-btn-group-2').enableButtonGroup();
	}
	*/

	$(document).ready(function()
	{
		$('.hr-pm-btn-tooltip').tooltip();

		$('#hr-pm-form').jqMgVal('addFormFieldsValidations');

		$('#hr-pm-journals-section').on('hidden.bs.collapse', function ()
		{
			$($(this).attr('data-target-id')).collapse('show');
		});

		$('#hr-pm-form-section').on('shown.bs.collapse', function ()
		{
			$('#hr-pm-name').focus();
		});

		$('#hr-pm-form-section').on('hidden.bs.collapse', function ()
		{
			$('#hr-pm-grid-section').collapse('show');

			$('#hr-pm-journals-section').collapse('show');
		});

		$('#hr-pm-btn-new').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			$('#hr-pm-btn-toolbar').disabledButtonGroup();
			$('#hr-pm-btn-group-3').enableButtonGroup();
			$('#hr-pm-form-new-title').removeClass('hidden');
			$('#hr-pm-grid-section').collapse('hide');
			$('#hr-pm-journals-section').attr('data-target-id', '#hr-pm-form-section');
			$('#hr-pm-journals-section').collapse('hide');
			$('.hr-pm-btn-tooltip').tooltip('hide');
		});

		$('#hr-pm-btn-refresh').click(function()
		{
			$('.hr-pm-btn-tooltip').tooltip('hide');
			$('#hr-pm-btn-toolbar').disabledButtonGroup();
			$('#hr-pm-btn-group-1').enableButtonGroup();

			if($('#hr-pm-journals-section').attr('data-target-id') == '' ||  $('#hr-pm-journals-section').attr('data-target-id') == '#hr-pm-form-section')
			{
				$('#hr-pm-grid').trigger('reloadGrid');
				cleanJournals('hr-pm-');
			}
		});

		$('#hr-pm-btn-export-xls').click(function()
		{
				$('#hr-pm-gridXlsButton').click();
		});

		$('#hr-pm-btn-export-csv').click(function()
		{
				$('#hr-pm-gridCsvButton').click();
		});

		$('#hr-pm-btn-edit').click(function()
		{
			var rowData;

			$('.hr-pm-btn-tooltip').tooltip('hide');
			$('#hr-pm-btn-toolbar').disabledButtonGroup();
			$('#hr-pm-btn-group-3').enableButtonGroup();

			if($('#hr-pm-journals-section').attr('data-target-id') == '')
			{
				if(!$('#hr-pm-grid').isRowSelected())
				{
					$('#hr-pm-btn-toolbar').showAlertAfterElement('alert-info alert-custom', lang.invalidSelection, 5000);
					return;
				}

				$('#hr-pm-form-edit-title').removeClass('hidden');

				rowData = $('#hr-pm-grid').getRowData($('#hr-pm-grid').jqGrid('getGridParam', 'selrow'));

				populateFormFields(rowData);

				$('#hr-pm-grid-section').collapse('hide');
				$('#hr-pm-journals-section').attr('data-target-id', '#hr-pm-form-section');
				$('#hr-pm-journals-section').collapse('hide');
			}
			else
			{

			}
		});

		$('#hr-pm-btn-delete').click(function()
		{
			var rowData;

			if($(this).hasAttr('disabled'))
			{
				return;
			}

			if($('#hr-pm-journals-section').attr('data-target-id') == '')
			{
				if(!$('#hr-pm-grid').isRowSelected())
				{
					$('#hr-pm-btn-toolbar').showAlertAfterElement('alert-info alert-custom', lang.invalidSelection, 5000);
					return;
				}

				rowData = $('#hr-pm-grid').getRowData($('#hr-pm-grid').jqGrid('getGridParam', 'selrow'));

				$('#hr-pm-delete-message').html($('#hr-pm-delete-message').attr('data-default-label').replace(':name', rowData.hr_pm_name));

			}
			else
			{

			}

			$('.hr-pm-btn-tooltip').tooltip('hide');
			$('#hr-pm-modal-delete').modal('show');
		});

		$('#hr-pm-btn-modal-delete').click(function()
		{
			//For grids with multiselect enabled
			var id = $('#hr-pm-grid').getSelectedRowsIdCell('hr_pm_id');

			if(id.length == 0)
			{
				return;
			}

			//For grids with multiselect disabled
			// var id = $('#hr-pm-grid').getSelectedRowId('module_app_id');

			if($('#hr-pm-journals-section').attr('data-target-id') == '#hr-pm-form-section')
			{
				var id = $('#hr-pm-grid').getSelectedRowsIdCell('hr_pm_id');
			}
			else
			{

			}

			$.ajax(
			{
				type: 'POST',
				data: JSON.stringify({'_token':$('#app-token').val(), 'id':id}),
				dataType : 'json',
				url: $('#hr-pm-form').attr('action') + '/delete',
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-pm-btn-toolbar', false);
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
						$('#hr-pm-btn-refresh').click();
						$('#hr-pm-modal-delete').modal('hide');
						$('#hr-pm-btn-toolbar').showAlertAfterElement('alert-success alert-custom',json.success, 5000);
					}

					$('#app-loader').addClass('hidden');
					enableAll();
				}
			});
		});

		$('#hr-pm-btn-save').click(function()
		{
			var url = $('#hr-pm-form').attr('action'), action = 'new';

			$('.hr-pm-btn-tooltip').tooltip('hide');

			if($('#hr-pm-journals-section').attr('data-target-id') == '#hr-pm-form-section')
			{
				if(!$('#hr-pm-form').jqMgVal('isFormValid'))
				{
					return;
				}

				if($('#hr-pm-id').isEmpty())
				{
					url = url + '/create';
				}
				else
				{
					url = url + '/update';
					action = 'edit';
				}

				data = $('#hr-pm-form').formToObject('hr-pm-');
			}
			else
			{

			}

			$.ajax(
			{
				type: 'POST',
				data: JSON.stringify(data),
				dataType : 'json',
				url:  url,
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-pm-form');
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
						if($('#hr-pm-journals-section').attr('data-target-id') == '#hr-pm-form-section')
						{
							$('#hr-pm-btn-close').click();
							hrTmPhaseLabelArrayData = json.phases;
 +						$('#hr-tm-phase-label').autocomplete('option', 'source', json.phases);
							// $('#hr-pm-btn-toolbar').showAlertAfterElement('alert-success alert-custom',json.success, 6000);
						}
						else if(json.info)
						{
							$('#hr-pm-form').showAlertAsFirstChild('alert-success', json.info);
						}
					}
					else if(json.info)
					{
						if($('#hr-pm-journals-section').attr('data-target-id') == '#hr-pm-form-section')
						{
							$('#hr-pm-form').showAlertAsFirstChild('alert-info', json.info);
						}
						else
						{
							// $('#hr-pm-form').showAlertAsFirstChild('alert-info', json.info);
						}
					}

					$('#app-loader').addClass('hidden');
					enableAll();
				}
			});
		});

		$('#hr-pm-btn-close').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			// hr-pm-form-section
			if($('#hr-pm-journals-section').attr('data-target-id') == '#hr-pm-form-section')
			{
				$('#hr-pm-form-new-title').addClass('hidden');
				$('#hr-pm-form-edit-title').addClass('hidden');
				$('#hr-pm-btn-refresh').click();
				$('#hr-pm-form').jqMgVal('clearForm');
				$('#hr-pm-form-section').collapse('hide');
			}
			else
			{

			}

			$('#hr-pm-btn-group-1').enableButtonGroup();
			$('#hr-pm-btn-group-3').disabledButtonGroup();
			$('.hr-pm-btn-tooltip').tooltip('hide');
			$('#hr-pm-journals-section').attr('data-target-id', '')
		});

		$('#hr-pm-btn-edit-helper').click(function()
	  {
			showButtonHelper('hr-pm-btn-close', 'hr-pm-btn-group-2', $('#hr-pm-edit-action').attr('data-content'));
	  });

		$('#hr-pm-btn-delete-helper').click(function()
	  {
			showButtonHelper('hr-pm-btn-close', 'hr-pm-btn-group-2', $('#hr-pm-delete-action').attr('data-content'));
	  });

		if(!$('#hr-pm-new-action').isEmpty())
		{
			$('#hr-pm-btn-new').click();
		}

		if(!$('#hr-pm-edit-action').isEmpty())
		{
			showButtonHelper('hr-pm-btn-close', 'hr-pm-btn-group-2', $('#hr-pm-edit-action').attr('data-content'));
		}

		if(!$('#hr-pm-delete-action').isEmpty())
		{
			showButtonHelper('hr-pm-btn-close', 'hr-pm-btn-group-2', $('#hr-pm-delete-action').attr('data-content'));
		}
	});
</script>

<div class="row">
	<div class="col-lg-12 col-md-12">
		<div id="hr-pm-btn-toolbar" class="section-header btn-toolbar" role="toolbar">
			<div id="hr-pm-btn-group-1" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-plus"></i> ' . Lang::get('toolbar.new'), array('id' => 'hr-pm-btn-new', 'class' => 'btn btn-default hr-pm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('decima-human-resources::phase-management.new'))) !!}
				{!! Form::button('<i class="fa fa-refresh"></i> ' . Lang::get('toolbar.refresh'), array('id' => 'hr-pm-btn-refresh', 'class' => 'btn btn-default hr-pm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('toolbar.refreshLongText'))) !!}
				<div class="btn-group">
					{!! Form::button('<i class="fa fa-share-square-o"></i> ' . Lang::get('toolbar.export') . ' <span class="caret"></span>', array('class' => 'btn btn-default dropdown-toggle', 'data-container' => 'body', 'data-toggle' => 'dropdown')) !!}
					<ul class="dropdown-menu">
         		<li><a id='hr-pm-btn-export-xls' class="fake-link"><i class="fa fa-file-excel-o"></i> xls</a></li>
         		<li><a id='hr-pm-btn-export-csv' class="fake-link"><i class="fa fa-file-text-o"></i> csv</a></li>
       		</ul>
				</div>
			</div>
			<div id="hr-pm-btn-group-2" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-edit"></i> ' . Lang::get('toolbar.edit'), array('id' => 'hr-pm-btn-edit', 'class' => 'btn btn-default hr-pm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::phase-management.edit'))) !!}
				{!! Form::button('<i class="fa fa-minus"></i> ' . Lang::get('toolbar.delete'), array('id' => 'hr-pm-btn-delete', 'class' => 'btn btn-default hr-pm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::phase-management.delete'))) !!}
			</div>
			<div id="hr-pm-btn-group-3" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-save"></i> ' . Lang::get('toolbar.save'), array('id' => 'hr-pm-btn-save', 'class' => 'btn btn-default hr-pm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::phase-management.save'))) !!}
				{!! Form::button('<i class="fa fa-undo"></i> ' . Lang::get('toolbar.close'), array('id' => 'hr-pm-btn-close', 'class' => 'btn btn-default hr-pm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('toolbar.closeLongText'))) !!}
			</div>
		</div>
		<div id='hr-pm-grid-section' class='app-grid collapse in' data-app-grid-id='hr-pm-grid'>
			{!!
			GridRender::setGridId("hr-pm-grid")
				->enablefilterToolbar(false, false)
				->hideXlsExporter()
  			->hideCsvExporter()
	    	->setGridOption('url',URL::to('human-resources/setup/phase-management/grid-data'))
	    	->setGridOption('caption', Lang::get('decima-human-resources::phase-management.gridTitle', array('user' => AuthManager::getLoggedUserFirstname())))
	    	->setGridOption('postData',array('_token' => Session::token()))
				->setGridEvent('onSelectRow', 'hrPmOnSelectRowEvent')
	    	->addColumn(array('index' => 'p.id', 'name' => 'hr_pm_id', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::phase-management.name'), 'index' => 'p.name' ,'name' => 'hr_pm_name', 'align' => 'center'))
	    	->renderGrid();
			!!}
		</div>
	</div>
</div>
<div id='hr-pm-journals-section' class="row collapse in section-block" data-target-id="">
	{!! Form::journals('hr-pm-', $appInfo['id']) !!}
</div>
<div id='hr-pm-form-section' class="row collapse">
	<div class="col-lg-12 col-md-12">
		<div class="form-container">
			{!! Form::open(array('id' => 'hr-pm-form', 'url' => URL::to('human-resources/setup/phase-management'), 'role'  =>  'form', 'onsubmit' => 'return false;')) !!}
				<legend id="hr-pm-form-new-title" class="hidden">{{ Lang::get('decima-human-resources::phase-management.formNewTitle') }}</legend>
				<legend id="hr-pm-form-edit-title" class="hidden">{{ Lang::get('decima-human-resources::phase-management.formEditTitle') }}</legend>
				<div class="row">
					<div class="col-lg-6 col-md-6">
						<div class="form-group mg-hm">
							{!! Form::label('hr-pm-name', Lang::get('decima-human-resources::phase-management.name'), array('class' => 'control-label')) !!}
					    {!! Form::text('hr-pm-name', null , array('id' => 'hr-pm-name', 'class' => 'form-control', 'data-mg-required' => '')) !!}
					    {!! Form::hidden('hr-pm-id', null, array('id' => 'hr-pm-id')) !!}
			  		</div>
					</div>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
<div id='hr-pm-modal-delete' class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm hr-pm-btn-delete">
    <div class="modal-content">
			<div class="modal-body" style="padding: 20px 20px 0px 20px;">
				<p id="hr-pm-delete-message" data-default-label="{{ Lang::get('decima-human-resources::phase-management.deleteMessageConfirmation') }}"></p>
      </div>
			<div class="modal-footer" style="text-align:center;">
				<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get('form.no') }}</button>
				<button id="hr-pm-btn-modal-delete" type="button" class="btn btn-primary">{{ Lang::get('form.yes') }}</button>
			</div>
    </div>
  </div>
</div>
@parent
@stop
