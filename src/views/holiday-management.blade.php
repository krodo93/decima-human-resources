@extends('layouts.base')

@section('container')
{!! Form::hidden('hr-hm-new-action', null, array('id' => 'hr-hm-new-action')) !!}
{!! Form::hidden('hr-hm-edit-action', null, array('id' => 'hr-hm-edit-action', 'data-content' => Lang::get('module::app.editHelpText'))) !!}
{!! Form::hidden('hr-hm-remove-action', null, array('id' => 'hr-hm-remove-action', 'data-content' => Lang::get('module::app.editHelpText'))) !!}
{!! Form::button('', array('id' => 'hr-hm-btn-edit-helper', 'class' => 'hidden')) !!}
{!! Form::button('', array('id' => 'hr-hm-btn-delete-helper', 'class' => 'hidden')) !!}
<style></style>

<script type='text/javascript'>
	//Falta agregar  codigo para quitar tooltip
	//For grids with multiselect enabled
	function hrHmOnSelectRowEvent(id)
	{
		var selRowIds = $('#hr-hm-grid').jqGrid('getGridParam', 'selarrrow');

		if(selRowIds.length == 0)
		{
			$('#hr-hm-btn-group-2').disabledButtonGroup();
			cleanJournals('hr-hm-');
		}
		else if(selRowIds.length == 1)
		{
			$('#hr-hm-btn-group-2').enableButtonGroup();
			cleanJournals('hr-hm-');
			getAppJournals('hr-hm-','firstPage', $('#hr-hm-grid').getSelectedRowId('hr_hm_id'));
		}
		else if(selRowIds.length > 1)
		{
			$('#hr-hm-btn-group-2').disabledButtonGroup();
			$('#hr-hm-btn-delete').removeAttr('disabled');
			cleanJournals('hr-hm-');
		}
	}

	/*
	//For grids with multiselect disabled
	function hrHmOnSelectRowEvent()
	{
		var id = $('#hr-hm-grid').getSelectedRowId('hr_hm_id');

		getAppJournals('hr-hm-', 'firstPage', id);

		$('#hr-hm-btn-group-2').enableButtonGroup();
	}
	*/

	$(document).ready(function()
	{
		$('.hr-hm-btn-tooltip').tooltip();

		$('#hr-hm-form').jqMgVal('addFormFieldsValidations');

		$('#hr-hm-journals-section').on('hidden.bs.collapse', function ()
		{
			$($(this).attr('data-target-id')).collapse('show');
		});

		$('#hr-hm-form-section').on('shown.bs.collapse', function ()
		{
			$('#hr-hm-name').focus();
		});

		$('#hr-hm-form-section').on('hidden.bs.collapse', function ()
		{
			$('#hr-hm-grid-section').collapse('show');

			$('#hr-hm-journals-section').collapse('show');
		});

		$('#hr-hm-btn-new').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			$('#hr-hm-btn-toolbar').disabledButtonGroup();
			$('#hr-hm-btn-group-3').enableButtonGroup();
			$('#hr-hm-form-new-title').removeClass('hidden');
			$('#hr-hm-grid-section').collapse('hide');
			$('#hr-hm-journals-section').attr('data-target-id', '#hr-hm-form-section');
			$('#hr-hm-journals-section').collapse('hide');
			$('.hr-hm-btn-tooltip').tooltip('hide');
		});

		$('#hr-hm-btn-refresh').click(function()
		{
			$('.hr-hm-btn-tooltip').tooltip('hide');
			$('#hr-hm-btn-toolbar').disabledButtonGroup();
			$('#hr-hm-btn-group-1').enableButtonGroup();

			if($('#hr-hm-journals-section').attr('data-target-id') == '' || $('#hr-hm-journals-section').attr('data-target-id') == '#hr-hm-form-section')
			{
				$('#hr-hm-grid').trigger('reloadGrid');
				cleanJournals('hr-hm-');
			}
		});

		$('#hr-hm-btn-export-xls').click(function()
		{
				$('#hr-hm-gridXlsButton').click();
		});

		$('#hr-hm-btn-export-csv').click(function()
		{
				$('#hr-hm-gridCsvButton').click();
		});

		$('#hr-hm-btn-edit').click(function()
		{
			var rowData;

			$('.hr-hm-btn-tooltip').tooltip('hide');
			$('#hr-hm-btn-toolbar').disabledButtonGroup();
			$('#hr-hm-btn-group-3').enableButtonGroup();

			if($('#hr-hm-journals-section').attr('data-target-id') == '')
			{
				if(!$('#hr-hm-grid').isRowSelected())
				{
					$('#hr-hm-btn-toolbar').showAlertAfterElement('alert-info alert-custom', lang.invalidSelection, 5000);
					return;
				}

				$('#hr-hm-form-edit-title').removeClass('hidden');

				rowData = $('#hr-hm-grid').getRowData($('#hr-hm-grid').jqGrid('getGridParam', 'selrow'));

				populateFormFields(rowData);

				$('#hr-hm-grid-section').collapse('hide');
				$('#hr-hm-journals-section').attr('data-target-id', '#hr-hm-form-section');
				$('#hr-hm-journals-section').collapse('hide');
			}
			else
			{

			}
		});

		$('#hr-hm-btn-delete').click(function()
		{
			var rowData;

			if($(this).hasAttr('disabled'))
			{
				return;
			}

			if($('#hr-hm-journals-section').attr('data-target-id') == '')
			{
				if(!$('#hr-hm-grid').isRowSelected())
				{
					$('#hr-hm-btn-toolbar').showAlertAfterElement('alert-info alert-custom', lang.invalidSelection, 5000);
					return;
				}

				rowData = $('#hr-hm-grid').getRowData($('#hr-hm-grid').jqGrid('getGridParam', 'selrow'));

				$('#hr-hm-delete-message').html($('#hr-hm-delete-message').attr('data-default-label').replace(':name', rowData.hr_hm_date));
			}
			else
			{

			}

			$('.hr-hm-btn-tooltip').tooltip('hide');
			$('#hr-hm-modal-delete').modal('show');
		});

		$('#hr-hm-btn-modal-delete').click(function()
		{
			//For grids with multiselect enabled
			var id = $('#hr-hm-grid').getSelectedRowsIdCell('hr_hm_id');

			if(id.length == 0)
			{
				return;
			}

			//For grids with multiselect disabled
			// var id = $('#hr-hm-grid').getSelectedRowId('hr_hm_id');

			if($('#hr-hm-journals-section').attr('data-target-id') == '#hr-hm-form-section')
			{
				var id = $('#hr-hm-grid').getSelectedRowsIdCell('hr_hm_id');
			}
			else
			{

			}

			$.ajax(
			{
				type: 'POST',
				data: JSON.stringify({'_token':$('#app-token').val(), 'id':id}),
				dataType : 'json',
				url:  $('#hr-hm-form').attr('action') + '/delete',
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-hm-btn-toolbar', false);
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
						$('#hr-hm-btn-refresh').click();
						$('#hr-hm-modal-delete').modal('hide');
						$('#hr-hm-btn-toolbar').showAlertAfterElement('alert-success alert-custom',json.success, 5000);
					}

					$('#app-loader').addClass('hidden');
					enableAll();
				}
			});
		});

		$('#hr-hm-btn-save').click(function()
		{
			var url = $('#hr-hm-form').attr('action'), action = 'new';

			$('.hr-hm-btn-tooltip').tooltip('hide');

			if($('#hr-hm-journals-section').attr('data-target-id') == '#hr-hm-form-section')
			{
				if(!$('#hr-hm-form').jqMgVal('isFormValid'))
				{
					return;
				}

				if($('#hr-hm-id').isEmpty())
				{
					url = url + '/create';
				}
				else
				{
					url = url + '/update';
					action = 'edit';
				}

				data = $('#hr-hm-form').formToObject('hr-hm-');
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
					handleServerExceptions(jqXHR, 'hr-hm-form');
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
						if($('#hr-hm-journals-section').attr('data-target-id') == '#hr-hm-form-section')
						{
							$('#hr-hm-btn-close').click();
							$('#hr-hm-btn-toolbar').showAlertAfterElement('alert-success alert-custom',json.success, 6000);
						}
						else
						{
							// $('#hr-hm-form').showAlertAsFirstChild('alert-success', json.info);
						}
					}
					else if(json.info)
					{
						if($('#hr-hm-journals-section').attr('data-target-id') == '#hr-hm-form-section')
						{
							$('#hr-hm-form').showAlertAsFirstChild('alert-info', json.info);
						}
						else
						{
							// $('#hr-hm-form').showAlertAsFirstChild('alert-info', json.info);
						}
					}

					$('#app-loader').addClass('hidden');
					enableAll();
				}
			});
		});

		$('#hr-hm-btn-close').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			// hr-hm-form-section
			if($('#hr-hm-journals-section').attr('data-target-id') == '#hr-hm-form-section')
			{
				$('#hr-hm-form-new-title').addClass('hidden');
				$('#hr-hm-form-edit-title').addClass('hidden');
				$('#hr-hm-btn-refresh').click();
				$('#hr-hm-form').jqMgVal('clearForm');
				$('#hr-hm-form-section').collapse('hide');
			}
			else
			{

			}

			$('#hr-hm-btn-group-1').enableButtonGroup();
			$('#hr-hm-btn-group-3').disabledButtonGroup();
			$('.hr-hm-btn-tooltip').tooltip('hide');
			$('#hr-hm-journals-section').attr('data-target-id', '')
		});

		$('#hr-hm-btn-edit-helper').click(function()
	  {
			showButtonHelper('hr-hm-btn-close', 'hr-hm-btn-group-2', $('#hr-hm-edit-action').attr('data-content'));
	  });

		$('#hr-hm-btn-delete-helper').click(function()
	  {
			showButtonHelper('hr-hm-btn-close', 'hr-hm-btn-group-2', $('#hr-hm-delete-action').attr('data-content'));
	  });

		if(!$('#hr-hm-new-action').isEmpty())
		{
			$('#hr-hm-btn-new').click();
		}

		if(!$('#hr-hm-edit-action').isEmpty())
		{
			showButtonHelper('hr-hm-btn-close', 'hr-hm-btn-group-2', $('#hr-hm-edit-action').attr('data-content'));
		}

		if(!$('#hr-hm-delete-action').isEmpty())
		{
			showButtonHelper('hr-hm-btn-close', 'hr-hm-btn-group-2', $('#hr-hm-delete-action').attr('data-content'));
		}
	});
</script>

<div class="row">
	<div class="col-lg-12 col-md-12">
		<div id="hr-hm-btn-toolbar" class="section-header btn-toolbar" role="toolbar">
			<div id="hr-hm-btn-group-1" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-plus"></i> ' . Lang::get('toolbar.new'), array('id' => 'hr-hm-btn-new', 'class' => 'btn btn-default hr-hm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('decima-human-resources::holiday-management.new'))) !!}
				{!! Form::button('<i class="fa fa-refresh"></i> ' . Lang::get('toolbar.refresh'), array('id' => 'hr-hm-btn-refresh', 'class' => 'btn btn-default hr-hm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('toolbar.refreshLongText'))) !!}
				<div class="btn-group">
					{!! Form::button('<i class="fa fa-share-square-o"></i> ' . Lang::get('toolbar.export') . ' <span class="caret"></span>', array('class' => 'btn btn-default dropdown-toggle', 'data-container' => 'body', 'data-toggle' => 'dropdown')) !!}
					<ul class="dropdown-menu">
         		<li><a id='hr-hm-btn-export-xls' class="fake-link"><i class="fa fa-file-excel-o"></i> xls</a></li>
         		<li><a id='hr-hm-btn-export-csv' class="fake-link"><i class="fa fa-file-text-o"></i> csv</a></li>
       		</ul>
				</div>
			</div>
			<div id="hr-hm-btn-group-2" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-edit"></i> ' . Lang::get('toolbar.edit'), array('id' => 'hr-hm-btn-edit', 'class' => 'btn btn-default hr-hm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::holiday-management.edit'))) !!}
				{!! Form::button('<i class="fa fa-minus"></i> ' . Lang::get('toolbar.delete'), array('id' => 'hr-hm-btn-delete', 'class' => 'btn btn-default hr-hm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::holiday-management.delete'))) !!}
			</div>
			<div id="hr-hm-btn-group-3" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-save"></i> ' . Lang::get('toolbar.save'), array('id' => 'hr-hm-btn-save', 'class' => 'btn btn-default hr-hm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::holiday-management.save'))) !!}
				{!! Form::button('<i class="fa fa-undo"></i> ' . Lang::get('toolbar.close'), array('id' => 'hr-hm-btn-close', 'class' => 'btn btn-default hr-hm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('toolbar.closeLongText'))) !!}
			</div>
		</div>
		<div id='hr-hm-grid-section' class='app-grid collapse in' data-app-grid-id='hr-hm-grid'>
			{!!
			GridRender::setGridId("hr-hm-grid")
				->enablefilterToolbar(false, false)
				->hideXlsExporter()
  			->hideCsvExporter()
	    	->setGridOption('url',URL::to('human-resources/setup/holiday-management/grid-data'))
	    	->setGridOption('caption', Lang::get('decima-human-resources::holiday-management.gridTitle', array('user' => AuthManager::getLoggedUserFirstname())))
	    	->setGridOption('postData',array('_token' => Session::token()))
				->setGridEvent('onSelectRow', 'hrHmOnSelectRowEvent')
	    	->addColumn(array('index' => 't1.id', 'name' => 'hr_hm_id', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::holiday-management.year'), 'index' => 't1.year' ,'name' => 'hr_hm_year', 'align' => 'center'))
				->addColumn(array('label' => Lang::get('decima-human-resources::holiday-management.date'), 'index' => 't1.date' ,'name' => 'hr_hm_date', 'formatter' => 'date', 'align' => 'center'))
				->addColumn(array('label' => Lang::get('decima-human-resources::holiday-management.description'), 'index' => 't1.description' ,'name' => 'hr_hm_description'))
				->renderGrid();
			!!}
		</div>
	</div>
</div>
<div id='hr-hm-journals-section' class="row collapse in section-block" data-target-id="">
	{!! Form::journals('hr-hm-', $appInfo['id']) !!}
</div>
<div id='hr-hm-form-section' class="row collapse">
	<div class="col-lg-12 col-md-12">
		<div class="form-container">
			{!! Form::open(array('id' => 'hr-hm-form', 'url' => URL::to('human-resources/setup/holiday-management'), 'role'  =>  'form', 'onsubmit' => 'return false;')) !!}
				<legend id="hr-hm-form-new-title" class="hidden">{{ Lang::get('decima-human-resources::holiday-management.formNewTitle') }}</legend>
				<legend id="hr-hm-form-edit-title" class="hidden">{{ Lang::get('decima-human-resources::holiday-management.formEditTitle') }}</legend>
				<div class="row">
					<div class="col-lg-6 col-md-6">
						<div class="form-group mg-hm">
							{!! Form::label('hr-hm-year', Lang::get('decima-human-resources::holiday-management.year'), array('class' => 'control-label')) !!}
					    {!! Form::text('hr-hm-year', null , array('id' => 'hr-hm-year', 'class' => 'form-control', 'data-mg-required' => '', 'data-mg-validator' => 'positiveIntegerNoZero')) !!}
					    {!! Form::hidden('hr-hm-id', null, array('id' => 'hr-hm-id')) !!}
			  		</div>
						<div class="form-group mg-hm">
							{!! Form::label('hr-hm-date', Lang::get('decima-human-resources::holiday-management.date'), array('class' => 'control-label')) !!}
							{!! Form::date('hr-hm-date', array('class' => 'form-control', 'data-mg-required' => '')) !!}
			  		</div>
					</div>
					<div class="col-lg-6 col-md-6">
						<div class="form-group mg-hm">
							{!! Form::label('hr-hm-description', Lang::get('decima-human-resources::holiday-management.description'), array('class' => 'control-label')) !!}
							{!! Form::text('hr-hm-description', null , array('id' => 'hr-hm-description', 'class' => 'form-control', 'data-mg-required' => '')) !!}
						</div>
					</div>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
<div id='hr-hm-modal-delete' class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm hr-hm-btn-delete">
    <div class="modal-content">
			<div class="modal-body" style="padding: 20px 20px 0px 20px;">
				<p id="hr-hm-delete-message" data-default-label="{{ Lang::get('decima-human-resources::holiday-management.deleteMessageConfirmation') }}"></p>
      </div>
			<div class="modal-footer" style="text-align:center;">
				<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get('form.no') }}</button>
				<button id="hr-hm-btn-modal-delete" type="button" class="btn btn-primary">{{ Lang::get('form.yes') }}</button>
			</div>
    </div>
  </div>
</div>
@parent
@stop
