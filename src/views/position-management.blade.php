@extends('layouts.base')

@section('container')
{!! Form::hidden('hr-position-new-action', null, array('id' => 'hr-position-new-action')) !!}
{!! Form::hidden('hr-position-edit-action', null, array('id' => 'hr-position-edit-action', 'data-content' => Lang::get('module::app.editHelpText'))) !!}
{!! Form::hidden('hr-position-remove-action', null, array('id' => 'hr-position-remove-action', 'data-content' => Lang::get('module::app.editHelpText'))) !!}
{!! Form::button('', array('id' => 'hr-position-btn-edit-helper', 'class' => 'hidden')) !!}
{!! Form::button('', array('id' => 'hr-position-btn-delete-helper', 'class' => 'hidden')) !!}
<style></style>

<script type='text/javascript'>
	//Falta agregar  codigo para quitar tooltip
	//For grids with multiselect enabled
	function hrEmOnSelectRowEvent(id)
	{
		var selRowIds = $('#hr-position-grid').jqGrid('getGridParam', 'selarrrow');

		if(selRowIds.length == 0)
		{
			$('#hr-position-btn-group-2').disabledButtonGroup();
			cleanJournals('hr-position-');
		}
		else if(selRowIds.length == 1)
		{
			$('#hr-position-btn-group-2').enableButtonGroup();
			cleanJournals('hr-position-');
			getAppJournals('hr-position-','firstPage', $('#hr-position-grid').getSelectedRowId('hr_position_id'));
		}
		else if(selRowIds.length > 1)
		{
			$('#hr-position-btn-group-2').disabledButtonGroup();
			$('#hr-position-btn-delete').removeAttr('disabled');
			cleanJournals('hr-position-');
		}
	}

	/*
	//For grids with multiselect disabled
	function hrEmOnSelectRowEvent()
	{
		var id = $('#hr-position-grid').getSelectedRowId('module_app_id');

		getAppJournals('hr-position-', 'firstPage', id);

		$('#hr-position-btn-group-2').enableButtonGroup();
	}
	*/

	$(document).ready(function()
	{
		$('.hr-position-btn-tooltip').tooltip();

		$('#hr-position-form').jqMgVal('addFormFieldsValidations');

		$('#hr-position-grid-section').on('shown.bs.collapse', function ()
		{
			$('#hr-position-btn-refresh').click();
		});

		$('#hr-position-journals-section').on('hidden.bs.collapse', function ()
		{
			$('#hr-position-form-section').collapse('show');
		});

		$('#hr-position-form-section').on('shown.bs.collapse', function ()
		{
			$('#hr-position-name').focus();
		});

		$('#hr-position-form-section').on('hidden.bs.collapse', function ()
		{
			$('#hr-position-grid-section').collapse('show');

			$('#hr-position-journals-section').collapse('show');
		});

		$('#hr-position-btn-new').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			$('#hr-position-btn-toolbar').disabledButtonGroup();
			$('#hr-position-btn-group-3').enableButtonGroup();
			$('#hr-position-form-new-title').removeClass('hidden');
			$('#hr-position-grid-section').collapse('hide');
			$('#hr-position-journals-section').collapse('hide');
			$('.hr-position-btn-tooltip').tooltip('hide');
		});

		$('#hr-position-btn-refresh').click(function()
		{
			$('.hr-position-btn-tooltip').tooltip('hide');
			$('#hr-position-grid').trigger('reloadGrid');
			cleanJournals('hr-position-');
		});

		$('#hr-position-btn-export-xls').click(function()
		{
				$('#hr-position-gridXlsButton').click();
		});

		$('#hr-position-btn-export-csv').click(function()
		{
				$('#hr-position-gridCsvButton').click();
		});

		$('#hr-position-btn-edit').click(function()
		{
			var rowData;

			$('#hr-position-btn-toolbar').disabledButtonGroup();
			$('#hr-position-btn-group-3').enableButtonGroup();
			$('#hr-position-form-edit-title').removeClass('hidden');

			rowData = $('#hr-position-grid').getRowData($('#hr-position-grid').jqGrid('getGridParam', 'selrow'));

			populateFormFields(rowData);

			$('#hr-position-grid-section').collapse('hide');
			$('#hr-position-journals-section').collapse('hide');
			$('.hr-position-btn-tooltip').tooltip('hide');
		});

		$('#hr-position-btn-delete').click(function()
		{
			var rowData;

			if($(this).hasAttr('disabled'))
			{
				return;
			}

			rowData = $('#hr-position-grid').getRowData($('#hr-position-grid').jqGrid('getGridParam', 'selrow'));

			$('#hr-position-delete-message').html($('#hr-position-delete-message').attr('data-default-label').replace(':name', rowData.hr_position_name));

			$('.hr-position-btn-tooltip').tooltip('hide');

			$('#hr-position-modal-delete').modal('show');
		});

		$('#hr-position-btn-modal-delete').click(function()
		{
			//For grids with multiselect enabled
			var id = $('#hr-position-grid').getSelectedRowsIdCell('hr_position_id');

			if(id.length == 0)
			{
				return;
			}

			//For grids with multiselect disabled
			// var id = $('#hr-position-grid').getSelectedRowId('module_app_id');

			$.ajax(
			{
				type: 'POST',
				data: JSON.stringify({'_token':$('#app-token').val(), 'id':id}),
				dataType : 'json',
				url:  $('#hr-position-form').attr('action') + '/delete',
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-position-btn-toolbar', false);
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
						$('#hr-position-btn-refresh').click();
						$('#hr-position-modal-delete').modal('hide');
						$('#hr-position-btn-toolbar').showAlertAfterElement('alert-success alert-custom',json.success, 5000);
					}

					$('#app-loader').addClass('hidden');
					enableAll();
				}
			});
		});

		$('#hr-position-btn-save').click(function()
		{
			var url = $('#hr-position-form').attr('action'), action = 'new';

			$('.hr-position-btn-tooltip').tooltip('hide');

			if(!$('#hr-position-form').jqMgVal('isFormValid'))
			{
				return;
			}

			if($('#hr-position-id').isEmpty())
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
				data: JSON.stringify($('#hr-position-form').formToObject('hr-position-')),
				dataType : 'json',
				url: url,
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-position-form');
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
						$('#hr-position-btn-close').click();
						hrEmPositionArrayData = json.positions;
						$('#hr-em-position').autocomplete('option', 'source', json.positions);
					}
					else if(json.info)
					{
						$('#hr-position-form').showAlertAsFirstChild('alert-info', json.info);
					}

					$('#app-loader').addClass('hidden');
					enableAll();
				}
			});
		});

		$('#hr-position-btn-close').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			$('#hr-position-btn-group-1').enableButtonGroup();
			$('#hr-position-btn-group-3').disabledButtonGroup();
			$('#hr-position-form-new-title').addClass('hidden');
			$('#hr-position-form-edit-title').addClass('hidden');
			$('#hr-position-grid').jqGrid('clearGridData');
			$('#hr-position-form').jqMgVal('clearForm');
			$('.hr-position-btn-tooltip').tooltip('hide');
			$('#hr-position-form-section').collapse('hide');
		});
	});

	$('#hr-position-btn-edit-helper').click(function()
  {
		showButtonHelper('hr-position-btn-close', 'hr-position-btn-group-2', $('#hr-position-edit-action').attr('data-content'));
  });

	$('#hr-position-btn-delete-helper').click(function()
  {
		showButtonHelper('hr-position-btn-close', 'hr-position-btn-group-2', $('#hr-position-delete-action').attr('data-content'));
  });

	if(!$('#hr-position-new-action').isEmpty())
	{
		$('#hr-position-btn-new').click();
	}

	if(!$('#hr-position-edit-action').isEmpty())
	{
		showButtonHelper('hr-position-btn-close', 'hr-position-btn-group-2', $('#hr-position-edit-action').attr('data-content'));
	}

	if(!$('#hr-position-delete-action').isEmpty())
	{
		showButtonHelper('hr-position-btn-close', 'hr-position-btn-group-2', $('#hr-position-delete-action').attr('data-content'));
	}
</script>

<div class="row">
	<div class="col-lg-12 col-md-12">
		<div id="hr-position-btn-toolbar" class="section-header btn-toolbar" role="toolbar">
			<div id="hr-position-btn-group-1" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-plus"></i> ' . Lang::get('toolbar.new'), array('id' => 'hr-position-btn-new', 'class' => 'btn btn-default hr-position-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('decima-human-resources::position-management.new'))) !!}
				{!! Form::button('<i class="fa fa-refresh"></i> ' . Lang::get('toolbar.refresh'), array('id' => 'hr-position-btn-refresh', 'class' => 'btn btn-default hr-position-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('toolbar.refreshLongText'))) !!}
				<div class="btn-group">
					{!! Form::button('<i class="fa fa-share-square-o"></i> ' . Lang::get('toolbar.export') . ' <span class="caret"></span>', array('class' => 'btn btn-default dropdown-toggle', 'data-container' => 'body', 'data-toggle' => 'dropdown')) !!}
					<ul class="dropdown-menu">
         		<li><a id='hr-position-btn-export-xls' class="fake-link"><i class="fa fa-file-excel-o"></i> xls</a></li>
         		<li><a id='hr-position-btn-export-csv' class="fake-link"><i class="fa fa-file-text-o"></i> csv</a></li>
       		</ul>
				</div>
			</div>
			<div id="hr-position-btn-group-2" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-edit"></i> ' . Lang::get('toolbar.edit'), array('id' => 'hr-position-btn-edit', 'class' => 'btn btn-default hr-position-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::position-management.edit'))) !!}
				{!! Form::button('<i class="fa fa-minus"></i> ' . Lang::get('toolbar.delete'), array('id' => 'hr-position-btn-delete', 'class' => 'btn btn-default hr-position-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::position-management.delete'))) !!}
			</div>
			<div id="hr-position-btn-group-3" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-save"></i> ' . Lang::get('toolbar.save'), array('id' => 'hr-position-btn-save', 'class' => 'btn btn-default hr-position-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::position-management.save'))) !!}
				{!! Form::button('<i class="fa fa-undo"></i> ' . Lang::get('toolbar.close'), array('id' => 'hr-position-btn-close', 'class' => 'btn btn-default hr-position-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('toolbar.closeLongText'))) !!}
			</div>
		</div>
		<div id='hr-position-grid-section' class='app-grid collapse in' data-app-grid-id='hr-position-grid'>
			{!!
			GridRender::setGridId("hr-position-grid")
				->enablefilterToolbar(false, false)
				->hideXlsExporter()
  			->hideCsvExporter()
	    	->setGridOption('url',URL::to('human-resources/setup/position-management/grid-data'))
	    	->setGridOption('caption', Lang::get('decima-human-resources::position-management.gridTitle', array('user' => AuthManager::getLoggedUserFirstname())))
	    	->setGridOption('postData',array('_token' => Session::token()))
				->setGridEvent('onSelectRow', 'hrEmOnSelectRowEvent')
	    	->addColumn(array('index' => 't1.id', 'name' => 'hr_position_id', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::position-management.name'), 'index' => 't1.name' ,'name' => 'hr_position_name'))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::position-management.salary'), 'index' => 't1.salary' ,'name' => 'hr_position_salary' , 'formatter' => 'currency'))
	    	->renderGrid();
			!!}
		</div>
	</div>
</div>
<div id='hr-position-journals-section' class="row collapse in section-block">
	{!! Form::journals('hr-position-', $appInfo['id']) !!}
</div>
<div id='hr-position-form-section' class="row collapse">
	<div class="col-lg-12 col-md-12">
		<div class="form-container">
			{!! Form::open(array('id' => 'hr-position-form', 'url' => URL::to('human-resources/setup/position-management'), 'role'  =>  'form', 'onsubmit' => 'return false;')) !!}
				<legend id="hr-position-form-new-title" class="hidden">{{ Lang::get('decima-human-resources::position-management.formNewPosition') }}</legend>
				<legend id="hr-position-form-edit-title" class="hidden">{{ Lang::get('decima-human-resources::position-management.formEditPosition') }}</legend>
				<div class="row">
					<div class="col-lg-6 col-md-6">
						<div class="form-group mg-hm">
							{!! Form::label('hr-position-name', Lang::get('decima-human-resources::position-management.name'), array('class' => 'control-label')) !!}
				    	{!! Form::text('hr-position-name', null , array('id' => 'hr-position-name', 'class' => 'form-control', 'data-mg-required' => '')) !!}
				    	{!! Form::hidden('hr-position-id', null, array('id' => 'hr-position-id')) !!}
		  			</div>
					</div>
					<div class="col-lg-6 col-md-6">
						<div class="form-group mg-hm">
							{!! Form::label('hr-position-salary', Lang::get('decima-human-resources::position-management.salary'), array('class' => 'control-label')) !!}
							{!! Form::money('hr-position-salary' , array('id' => 'hr-position-salary' , 'class' => 'form-control', 'data-mg-required' => '', 'defaultvalue' => Lang::get('form.defaultNumericValue')), Lang::get('form.defaultNumericValue')) !!}
						</div>
					</div>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
<div id='hr-position-modal-delete' class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm hr-position-btn-delete">
    <div class="modal-content">
			<div class="modal-body" style="padding: 20px 20px 0px 20px;">
				<p id="hr-position-delete-message" data-default-label="{{ Lang::get('decima-human-resources::position-management.deleteMessageConfirmation') }}"></p>
      </div>
			<div class="modal-footer" style="text-align:center;">
				<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get('form.no') }}</button>
				<button id="hr-position-btn-modal-delete" type="button" class="btn btn-primary">{{ Lang::get('form.yes') }}</button>
			</div>
    </div>
  </div>
</div>
@parent
@stop
