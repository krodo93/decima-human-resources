@extends('layouts.base')

@section('container')
{!! Form::hidden('hr-department-new-action', null, array('id' => 'hr-department-new-action')) !!}
{!! Form::hidden('hr-department-edit-action', null, array('id' => 'hr-department-edit-action', 'data-content' => Lang::get('decima-human-resources::department-management.editHelpText'))) !!}
{!! Form::hidden('hr-department-remove-action', null, array('id' => 'hr-department-remove-action', 'data-content' => Lang::get('decima-human-resources::department-management.editHelpText'))) !!}
{!! Form::button('', array('id' => 'hr-department-btn-edit-helper', 'class' => 'hidden')) !!}
{!! Form::button('', array('id' => 'hr-department-btn-delete-helper', 'class' => 'hidden')) !!}
<style></style>

<script type='text/javascript'>
	//Falta agregar  codigo para quitar tooltip

	//For grids with multiselect enabled
	function hrEmOnSelectRowEvent(id)
	{
		var selRowIds = $('#hr-department-grid').jqGrid('getGridParam', 'selarrrow');

		if(selRowIds.length == 0)
		{
			$('#hr-department-btn-group-2').disabledButtonGroup();
			cleanJournals('hr-department-');
		}
		else if(selRowIds.length == 1)
		{
			$('#hr-department-btn-group-2').enableButtonGroup();
			cleanJournals('hr-department-');
			getAppJournals('hr-department-','firstPage', $('#hr-department-grid').getSelectedRowId('hr_department_id'));
		}
		else if(selRowIds.length > 1)
		{
			$('#hr-department-btn-group-2').disabledButtonGroup();
			$('#hr-department-btn-delete').removeAttr('disabled');
			cleanJournals('hr-department-');
		}
	}

	/*
	//For grids with multiselect disabled
	function hrEmOnSelectRowEvent()
	{
		var id = $('#hr-department-grid').getSelectedRowId('module_app_id');

		getAppJournals('hr-department-', 'firstPage', id);

		$('#hr-department-btn-group-2').enableButtonGroup();
	}
	*/

	$(document).ready(function()
	{
		$('.hr-department-btn-tooltip').tooltip();

		$('#hr-department-form').jqMgVal('addFormFieldsValidations');

		$('#hr-department-grid-section').on('shown.bs.collapse', function ()
		{
			$('#hr-department-btn-refresh').click();
		});

		$('#hr-department-journals-section').on('hidden.bs.collapse', function ()
		{
			$('#hr-department-form-section').collapse('show');
		});

		$('#hr-department-form-section').on('shown.bs.collapse', function ()
		{
			$('#hr-department-name').focus();
		});

		$('#hr-department-form-section').on('hidden.bs.collapse', function ()
		{
			$('#hr-department-grid-section').collapse('show');

			$('#hr-department-journals-section').collapse('show');
		});

		$('#hr-department-btn-new').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			$('#hr-department-btn-toolbar').disabledButtonGroup();
			$('#hr-department-btn-group-3').enableButtonGroup();
			$('#hr-department-form-new-title').removeClass('hidden');
			$('#hr-department-grid-section').collapse('hide');
			$('#hr-department-journals-section').collapse('hide');
			$('.hr-department-btn-tooltip').tooltip('hide');
		});

		$('#hr-department-btn-refresh').click(function()
		{
			$('.hr-department-btn-tooltip').tooltip('hide');
			$('#hr-department-grid').trigger('reloadGrid');
			cleanJournals('hr-department-');
		});

		$('#hr-department-btn-export-xls').click(function()
		{
				$('#hr-department-gridXlsButton').click();
		});

		$('#hr-department-btn-export-csv').click(function()
		{
				$('#hr-department-gridCsvButton').click();
		});

		$('#hr-department-btn-edit').click(function()
		{
			var rowData;

			if(!$('#hr-department-grid').isRowSelected())
			{
				$('#hr-department-btn-toolbar').showAlertAfterElement('alert-info alert-custom', lang.invalidSelection, 5000);
				return;
			}

			$('#hr-department-btn-toolbar').disabledButtonGroup();
			$('#hr-department-btn-group-3').enableButtonGroup();
			$('#hr-department-form-edit-title').removeClass('hidden');

			rowData = $('#hr-department-grid').getRowData($('#hr-department-grid').jqGrid('getGridParam', 'selrow'));

			populateFormFields(rowData);

			$('#hr-department-grid-section').collapse('hide');
			$('#hr-department-journals-section').collapse('hide');
			$('.hr-department-btn-tooltip').tooltip('hide');
		});

		$('#hr-department-btn-delete').click(function()
		{
			var rowData;

			if($(this).hasAttr('disabled'))
			{
				return;
			}

			rowData = $('#hr-department-grid').getRowData($('#hr-department-grid').jqGrid('getGridParam', 'selrow'));

			$('#hr-department-delete-message').html($('#hr-department-delete-message').attr('data-default-label').replace(':name', rowData.hr_department_name));

			$('.hr-department-btn-tooltip').tooltip('hide');

			$('#hr-department-modal-delete').modal('show');
		});

		$('#hr-department-btn-modal-delete').click(function()
		{
			//For grids with multiselect enabled
			var id = $('#hr-department-grid').getSelectedRowsIdCell('hr_department_id');

			if(id.length == 0)
			{
				return;
			}

			//For grids with multiselect disabled
			// var id = $('#hr-department-grid').getSelectedRowId('module_app_id');

			$.ajax(
			{
				type: 'POST',
				data: JSON.stringify({'_token':$('#app-token').val(), 'id':id}),
				dataType : 'json',
				url:  $('#hr-department-form').attr('action') + '/delete',
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-department-btn-toolbar', false);
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
						$('#hr-department-btn-refresh').click();
						$('#hr-department-modal-delete').modal('hide');
						$('#hr-department-btn-toolbar').showAlertAfterElement('alert-success alert-custom',json.success, 5000);
					}

					$('#app-loader').addClass('hidden');
					enableAll();
				}
			});
		});

		$('#hr-department-btn-save').click(function()
		{
			var url = $('#hr-department-form').attr('action'), action = 'new';

			$('.hr-department-btn-tooltip').tooltip('hide');

			if(!$('#hr-department-form').jqMgVal('isFormValid'))
			{
				return;
			}

			if($('#hr-department-id').isEmpty())
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
				data: JSON.stringify($('#hr-department-form').formToObject('hr-department-')),
				dataType : 'json',
				url: url,
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-department-form');
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
						$('#hr-department-btn-close').click();
						hrEmDepartmentArrayData = json.department;
						$('#hr-em-department, #asset-mm-me-department-destination-id-label, #asset-am-assigned-department-label').autocomplete('option', 'source', json.department);
					}
					else if(json.info)
					{
						$('#hr-department-form').showAlertAsFirstChild('alert-info', json.info);
					}

					$('#app-loader').addClass('hidden');
					enableAll();
				}
			});
		});

		$('#hr-department-btn-close').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			$('#hr-department-btn-group-1').enableButtonGroup();
			$('#hr-department-btn-group-3').disabledButtonGroup();
			$('#hr-department-form-new-title').addClass('hidden');
			$('#hr-department-form-edit-title').addClass('hidden');
			$('#hr-department-grid').jqGrid('clearGridData');
			$('#hr-department-form').jqMgVal('clearForm');
			$('.hr-department-btn-tooltip').tooltip('hide');
			$('#hr-department-form-section').collapse('hide');
		});
	});

	$('#hr-department-btn-edit-helper').click(function()
  {
		showButtonHelper('hr-department-btn-close', 'hr-department-btn-group-2', $('#hr-department-edit-action').attr('data-content'));
  });

	$('#hr-department-btn-delete-helper').click(function()
  {
		showButtonHelper('hr-department-btn-close', 'hr-department-btn-group-2', $('#hr-department-delete-action').attr('data-content'));
  });

	if(!$('#hr-department-new-action').isEmpty())
	{
		$('#hr-department-btn-new').click();
	}

	if(!$('#hr-department-edit-action').isEmpty())
	{
		showButtonHelper('hr-department-btn-close', 'hr-department-btn-group-2', $('#hr-department-edit-action').attr('data-content'));
	}

	if(!$('#hr-department-delete-action').isEmpty())
	{
		showButtonHelper('hr-department-btn-close', 'hr-department-btn-group-2', $('#hr-department-delete-action').attr('data-content'));
	}

	setTimeout(function ()
	{
		$('#hr-department-raw-materials-warehouse-label').setAutocompleteLabel('raw_materials_warehouse_id');
		$('#hr-department-consumable-warehouse-label').setAutocompleteLabel('consumable_warehouse_id');
		$('#hr-department-finished-goods-warehouse-label').setAutocompleteLabel('finished_goods_warehouse_id');
	}, 500);
</script>

<div class="row">
	<div class="col-lg-12 col-md-12">
		<div id="hr-department-btn-toolbar" class="section-header btn-toolbar" role="toolbar">
			<div id="hr-department-btn-group-1" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-plus"></i> ' . Lang::get('toolbar.new'), array('id' => 'hr-department-btn-new', 'class' => 'btn btn-default hr-department-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('decima-human-resources::department-management.new'))) !!}
				{!! Form::button('<i class="fa fa-refresh"></i> ' . Lang::get('toolbar.refresh'), array('id' => 'hr-department-btn-refresh', 'class' => 'btn btn-default hr-department-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('toolbar.refreshLongText'))) !!}
				<div class="btn-group">
					{!! Form::button('<i class="fa fa-share-square-o"></i> ' . Lang::get('toolbar.export') . ' <span class="caret"></span>', array('class' => 'btn btn-default dropdown-toggle', 'data-container' => 'body', 'data-toggle' => 'dropdown')) !!}
					<ul class="dropdown-menu">
         		<li><a id='hr-department-btn-export-xls' class="fake-link"><i class="fa fa-file-excel-o"></i> xls</a></li>
         		<li><a id='hr-department-btn-export-csv' class="fake-link"><i class="fa fa-file-text-o"></i> csv</a></li>
       		</ul>
				</div>
			</div>
			<div id="hr-department-btn-group-2" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-edit"></i> ' . Lang::get('toolbar.edit'), array('id' => 'hr-department-btn-edit', 'class' => 'btn btn-default hr-department-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::department-management.edit'))) !!}
				{!! Form::button('<i class="fa fa-minus"></i> ' . Lang::get('toolbar.delete'), array('id' => 'hr-department-btn-delete', 'class' => 'btn btn-default hr-department-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::department-management.delete'))) !!}
			</div>
			<div id="hr-department-btn-group-3" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-save"></i> ' . Lang::get('toolbar.save'), array('id' => 'hr-department-btn-save', 'class' => 'btn btn-default hr-department-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::department-management.save'))) !!}
				{!! Form::button('<i class="fa fa-undo"></i> ' . Lang::get('toolbar.close'), array('id' => 'hr-department-btn-close', 'class' => 'btn btn-default hr-department-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('toolbar.closeLongText'))) !!}
			</div>
		</div>
		<div id='hr-department-grid-section' class='app-grid collapse in' data-app-grid-id='hr-department-grid'>
			{!!
			GridRender::setGridId("hr-department-grid")
				->enablefilterToolbar(false, false)
				->hideXlsExporter()
  			->hideCsvExporter()
	    	->setGridOption('url',URL::to('human-resources/setup/department-management/grid-data'))
	    	->setGridOption('caption', Lang::get('decima-human-resources::department-management.gridTitle', array('user' => AuthManager::getLoggedUserFirstname())))
	    	->setGridOption('postData',array('_token' => Session::token()))
				->setGridEvent('onSelectRow', 'hrEmOnSelectRowEvent')
				->addColumn(array('index' => 'd.id', 'name' => 'hr_department_id', 'hidden' => true))
	    	->addColumn(array('label' => Lang::get('decima-human-resources::department-management.name'), 'index' => 'hr_department_name' ,'name' => 'hr_department_name'))
				->addColumn(array('label' => Lang::get('decima-accounting::journal-management.costCenter'), 'index' => 'hr_cost_center_name', 'name' => 'hr_cost_center_name'))
				->addColumn(array('label' => Lang::get('decima-purchase::initial-purchases-setup.rawMaterialsWarehouseId'), 'index' => 'hr_department_raw_materials_warehouse_label', 'name' => 'hr_department_raw_materials_warehouse_label'))
				->addColumn(array('label' => Lang::get('decima-purchase::initial-purchases-setup.finishedGoodsWarehouseId'), 'index' => 'hr_department_finished_goods_warehouse_label', 'name' => 'hr_department_finished_goods_warehouse_label'))
				->addColumn(array('label' => Lang::get('decima-purchase::initial-purchases-setup.consumableWarehouseId'), 'index' => 'hr_department_consumable_warehouse_label', 'name' => 'hr_department_consumable_warehouse_label'))
				->addColumn(array('index' => 'hr_dep_cost_center_id', 'name' => 'hr_department_cost_center_id', 'hidden' => true ))
				->addColumn(array('index' => 'd.finished_goods_warehouse_id', 'name' => 'hr_department_finished_goods_warehouse_id', 'hidden' => true ))
				->addColumn(array('index' => 'd.raw_materials_warehouse_id', 'name' => 'hr_department_raw_materials_warehouse_id', 'hidden' => true ))
				->addColumn(array('index' => 'd.consumable_warehouse_id', 'name' => 'hr_department_consumable_warehouse_id', 'hidden' => true ))
	    	->renderGrid();
			!!}
		</div>
	</div>
</div>
<div id='hr-department-journals-section' class="row collapse in section-block">
	{!! Form::journals('hr-department-', $appInfo['id']) !!}
</div>
<div id='hr-department-form-section' class="row collapse">
	<div class="col-lg-12 col-md-12">
		<div class="form-container">
			{!! Form::open(array('id' => 'hr-department-form', 'url' => URL::to('human-resources/setup/department-management'), 'role'  =>  'form', 'onsubmit' => 'return false;')) !!}
			<legend id="hr-department-form-new-title" class="hidden">{{ Lang::get('decima-human-resources::department-management.formNewDepartment') }}</legend>
			<legend id="hr-department-form-edit-title" class="hidden">{{ Lang::get('decima-human-resources::department-management.formEditDepartment') }}</legend>
			<div class="row">
				<div class="col-lg-6 col-md-6">
					<div class="form-group mg-hm">
						{!! Form::label('hr-department-name', Lang::get('decima-human-resources::department-management.name'), array('class' => 'control-label')) !!}
			    		{!! Form::text('hr-department-name', null , array('id' => 'hr-department-name', 'class' => 'form-control', 'data-mg-required' => '')) !!}
			    		{!! Form::hidden('hr-department-id', null, array('id' => 'hr-department-id')) !!}
	  			</div>
					<div class="form-group mg-hm">
						{!! Form::label('hr-cos-center', Lang::get('decima-accounting::journal-management.costCenter'), array('class' => 'control-label')) !!}
						{!! Form::autocomplete('hr-cost-center-name', $costCenters['organizationCostCenters'], array('class' => 'form-control'), 'hr-cost-center-name', 'hr-department-cost-center-id', null, 'fa-sitemap') !!}
						{!! Form::hidden('hr-department-cost-center-id', null, array('id'  =>  'hr-department-cost-center-id')) !!}
					</div>
					<div class="form-group mg-hm">
						{!! Form::label('hr-department-finished-goods-warehouse-label', Lang::get('decima-purchase::initial-purchases-setup.finishedGoodsWarehouseId'), array('class' => 'control-label')) !!}
						{!! Form::autocomplete('hr-department-finished-goods-warehouse-label', $warehouses, array('class' => 'form-control'),'hr-department-finished-goods-warehouse-label','hr-department-finished-goods-warehouse-id',  null, 'fa-sitemap') !!}
						{!! Form::hidden('hr-department-finished-goods-warehouse-id', null , array('id'  =>  'hr-department-finished-goods-warehouse-id')) !!}
					</div>
				</div>
				<div class="col-lg-6 col-md-6">
					<div class="form-group mg-hm">
						{!! Form::label('hr-department-consumable-warehouse-label', Lang::get('decima-purchase::initial-purchases-setup.consumableWarehouseId'), array('class' => 'control-label')) !!}
						{!! Form::autocomplete('hr-department-consumable-warehouse-label', $warehouses, array('class' => 'form-control'),'hr-department-consumable-warehouse-label', 'hr-department-consumable-warehouse-id',  null, 'fa-sitemap') !!}
						{!! Form::hidden('hr-department-consumable-warehouse-id', null , array('id'  =>  'hr-department-consumable-warehouse-id')) !!}
						<p class="help-block">&nbsp;</p>
					</div>
					<div class="form-group mg-hm">
						{!! Form::label('hr-department-raw-materials-warehouse-label', Lang::get('decima-purchase::initial-purchases-setup.rawMaterialsWarehouseId'), array('class' => 'control-label')) !!}
						{!! Form::autocomplete('hr-department-raw-materials-warehouse-label', $warehouses, array('class' => 'form-control'),'hr-department-raw-materials-warehouse-label','hr-department-raw-materials-warehouse-id', null, 'fa-sitemap') !!}
						{!! Form::hidden('hr-department-raw-materials-warehouse-id', null, array('id'  => 'hr-department-raw-materials-warehouse-id')) !!}
					</div>
				</div>
			</div>
		</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
<div id='hr-department-modal-delete' class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm hr-department-btn-delete">
    <div class="modal-content">
			<div class="modal-body" style="padding: 20px 20px 0px 20px;">
				<p id="hr-department-delete-message" data-default-label="{{ Lang::get('decima-human-resources::department-management.deleteMessageConfirmation') }}"></p>
      </div>
			<div class="modal-footer" style="text-align:center;">
				<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get('form.no') }}</button>
				<button id="hr-department-btn-modal-delete" type="button" class="btn btn-primary">{{ Lang::get('form.yes') }}</button>
			</div>
    </div>
  </div>
</div>
@parent
@stop
