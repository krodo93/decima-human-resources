@extends('layouts.base')

@section('container')
{!! Form::hidden('hr-tm-new-action', null, array('id' => 'hr-tm-new-action')) !!}
{!! Form::hidden('hr-tm-edit-action', null, array('id' => 'hr-tm-edit-action', 'data-content' => Lang::get('module::app.editHelpText'))) !!}
{!! Form::hidden('hr-tm-remove-action', null, array('id' => 'hr-tm-remove-action', 'data-content' => Lang::get('module::app.editHelpText'))) !!}
{!! Form::button('', array('id' => 'hr-tm-btn-edit-helper', 'class' => 'hidden')) !!}
{!! Form::button('', array('id' => 'hr-tm-btn-delete-helper', 'class' => 'hidden')) !!}
<style></style>

<style>
.media
 {
		 /*box-shadow:0px 0px 4px -2px #000;*/
		 margin: 15px 0;
		 padding:10px;
 }
 .dp
 {
		 border:10px solid #eee;
		 transition: all 0.2s ease-in-out;
 }
 .dp:hover
 {
		 border:2px solid #eee;
		 transform:rotate(360deg);
		 -ms-transform:rotate(360deg);
		 -webkit-transform:rotate(360deg);
		 /*-webkit-font-smoothing:antialiased;*/
 }
/* CSS used here will be applied after bootstrap.css */
	.sortable-placeholder {
			margin-left: 0 !important;
			border: 1px solid #ccc;
			-webkit-box-shadow: 0px 0px 10px #888;
			-moz-box-shadow: 0px 0px 10px #888;
			box-shadow: 0px 0px 10px #888;
	}
  .sortable-rows{
    margin-top: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    box-shadow: 1px 1px 5px #888;
    border-radius: 4px;
  }
	.caption-helper {
		padding: 0;
		margin: 0;
		line-height: 13px;
		color: #9eacb4;
		font-size: 13px;
		font-weight: 400;
	}
  .cont {
  	width: 100%;
  	overflow-x: auto;
  	white-space: nowrap;
  }
	.panel-ms-schedule
	{
		margin: 1px 2px 1px 2px;
		background-color: white;
    box-shadow: 1px 1px 3px #999;
	}
	.panel-placeholder {
		opacity: 0.8;
		border-style: dotted;
	}
  .path{
    padding-top: 0px;
    padding-right: 0px;
    padding-bottom: 0px;
    padding-left: 12px;
  }
	.clickable {
			background: rgba(0, 0, 0, 0.15);
			display: inline-block;
			padding: 6px 12px;
			border-radius: 4px;
			cursor: pointer;
	}
  .wrapper {
    position: relative;
  }
  .ribbon-wrapper-green {
    width: 85px;
    height: 88px;
    overflow: hidden;
    position: absolute;
    top: -3px;
    right: -3px;
  }
  .ribbon-B {
    font: bold 12px Sans-Serif;
    color: #333;
    text-align: center;
    text-shadow: rgba(255,255,255,0.5) 0px 1px 0px;
    -webkit-transform: rotate(45deg);
    -moz-transform:    rotate(45deg);
    -ms-transform:     rotate(45deg);
    -o-transform:      rotate(45deg);
    position: relative;
    padding: 7px 0;
    left: -5px;
    top: 0;
    bottom: 0px;
    background-color: #BFDC7A;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#BFDC7A), to(#8EBF45));
    background-image: -webkit-linear-gradient(top, #BFDC7A, #8EBF45);
    background-image:    -moz-linear-gradient(top, #BFDC7A, #8EBF45);
    background-image:     -ms-linear-gradient(top, #BFDC7A, #8EBF45);
    background-image:      -o-linear-gradient(top, #BFDC7A, #8EBF45);
    color: #6a6340;
    -webkit-box-shadow: 0px 0px 3px rgba(0,0,0,0.3);
    -moz-box-shadow:    0px 0px 3px rgba(0,0,0,0.3);
    box-shadow:         0px 0px 3px rgba(0,0,0,0.3);
    padding-top: 22px;
    padding-bottom: 5px;
    margin-bottom: 0px;
    padding-right: 5px;
    width: 150px;
  }
  .ribbon-M{
    font: bold 12px Sans-Serif;
    color: #333;
    text-align: center;
    text-shadow: rgba(255,255,255,0.5) 0px 1px 0px;
    -webkit-transform: rotate(45deg);
    -moz-transform:    rotate(45deg);
    -ms-transform:     rotate(45deg);
    -o-transform:      rotate(45deg);
    position: relative;
    padding: 7px 0;
    left: -5px;
    top: 0;
    bottom: 0px;
    background-color: #FECD42;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#FECD42), to(#ffd766));
    color: #6a6340;
    -webkit-box-shadow: 0px 0px 3px rgba(0,0,0,0.3);
    -moz-box-shadow:    0px 0px 3px rgba(0,0,0,0.3);
    box-shadow:         0px 0px 3px rgba(0,0,0,0.3);
    padding-top: 22px;
    padding-bottom: 5px;
    margin-bottom: 0px;
    padding-right: 5px;
    width: 150px;
  }
  .ribbon-A{
    font: bold 12px Sans-Serif;
    color: #333;
    text-align: center;
    text-shadow: rgba(255,255,255,0.5) 0px 1px 0px;
    -webkit-transform: rotate(45deg);
    -moz-transform:    rotate(45deg);
    -ms-transform:     rotate(45deg);
    -o-transform:      rotate(45deg);
    position: relative;
    padding: 7px 0;
    left: -5px;
    top: 0;
    bottom: 0px;
    background-color: #d63d1a;
    background-color: #d63d1a;background-image: -webkit-gradient(linear, left top, left bottom, from(#d49654), to(#d49654));
    color: #6a6340;
    -webkit-box-shadow: 0px 0px 3px rgba(0,0,0,0.3);
    -moz-box-shadow:    0px 0px 3px rgba(0,0,0,0.3);
    box-shadow:         0px 0px 3px rgba(0,0,0,0.3);
    padding-top: 22px;
    padding-bottom: 5px;
    margin-bottom: 0px;
    padding-right: 5px;
    width: 150px;
    color: #FFF;
  }
  .ribbon-U{
    font: bold 12px Sans-Serif;
    color: #333;
    text-align: center;
    text-shadow: rgba(255,255,255,0.5) 0px 1px 0px;
    -webkit-transform: rotate(45deg);
    -moz-transform:    rotate(45deg);
    -ms-transform:     rotate(45deg);
    -o-transform:      rotate(45deg);
    position: relative;
    padding: 7px 0;
    left: -5px;
    top: 0;
    bottom: 0px;
    background-color: #CC3319;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#CC3319), to(#e85940));
    color: #6a6340;
    -webkit-box-shadow: 0px 0px 3px rgba(0,0,0,0.3);
    -moz-box-shadow:    0px 0px 3px rgba(0,0,0,0.3);
    box-shadow:         0px 0px 3px rgba(0,0,0,0.3);
    padding-top: 22px;
    padding-bottom: 5px;
    top: 0;
    bottom: 0px;
    margin-bottom: 0px;
    padding-right: 5px;
    width: 150px;
    color: #FFF;
  }
</style>

<!-- <script
  src="http://code.jquery.com/ui/1.12.0/jquery-ui.js"
  integrity="sha256-0YPKAwZP7Mp3ALMRVB2i8GXeEndvCq3eSl/WsAl1Ryk="
  crossorigin="anonymous"></script> -->

<script src="{{ URL::asset('assets/jquery-ui-v1.10.3/dev/minified/jquery.ui.sortable.min.js') }}"></script>

<script type='text/javascript'>
  //Instancia del editor de texto

  var hrTmFiltersNames = {!! json_encode($phases) !!}
  var hrTmFiltersPriority = {!! json_encode($priority) !!}
  var hrTmFiltersPhases = {!! json_encode($phases) !!}
  var hrTmFiltersEmployee = {!! json_encode($employees) !!}
  var cmsBmQuill;

  function hrTmEditTask(task)
	{
    $('#hr-tm-btn-toolbar').disabledButtonGroup();
    $('#hr-tm-btn-group-3').enableButtonGroup();
    $('#hr-tm-id').val($(task).attr('hr-tm-id'));
    $('#hr-tm-name').val($(task).attr('hr-tm-name'));
    $('#hr-tm-priority-label').setAutocompleteLabel($(task).attr('hr-tm-priority'));
    $('#hr-tm-responsible-employee-label').setAutocompleteLabel($(task).attr('hr-tm-employee'));
    $('#hr-tm-phase-label').setAutocompleteLabel($(task).attr('hr-tm-phase'));
    $('#hr-tm-completion-percentage-label').setAutocompleteLabel($(task).attr('hr-tm-completion-percentage'));
    $('#hr-tm-planned-initial-hour').val($(task).attr('hr-tm-hour'));
    $('#hr-tm-manual-reference').val($(task).attr('hr-tm-reference'));
    $('#hr-tm-limit-date').val($(task).attr('hr-tm-date'));
    $('.ql-editor').html($('#hr-tm-manual-reference').val());
    $('#hr-tm-grid-section').collapse('hide');
    $('#hr-tm-journals-section').collapse('hide');
    $('.hr-tm-btn-tooltip').tooltip('hide');
	}
  function hrTmDeleteTask(task)
  {
    //alert('Baia Baia' + $(task).attr('task-id'));
    $('#hr-tm-delete-message').html($('#hr-tm-delete-message').attr('data-default-label').replace(':name', $(task).attr('task-id')));
    $('#hr-tm-id').val($(task).attr('task-id'));
    $('.hr-tm-btn-tooltip').tooltip('hide');
    $('#hr-tm-modal-delete').modal('show');
  }

  function hrTmupdateTaskPhaseAndPosition(tasks)
  {
    $.ajax(
    {
      type: 'POST',
      data: JSON.stringify({'_token':$('#app-token').val(),'tasks' : tasks}),
      dataType : 'json',
      url:  $('#hr-tm-form').attr('action') + '/update-task-phase-and-position',
      error: function (jqXHR, textStatus, errorThrown)
      {
        handleServerExceptions(jqXHR, 'hr-tm-btn-toolbar', false);
      },
      success:function(json)
      {
        if(json.success)
        {
          $('#hr-tm-btn-refresh').click();
          //$('#hr-tm-btn-toolbar').showAlertAfterElement('alert-success alert-custom',json.success, 5000);
        }
      }
    });
  }

	$(document).ready(function()
	{
		$('.hr-tm-btn-tooltip').tooltip();
		$('#hr-tm-form').jqMgVal('addFormFieldsValidations');

		$('#hr-tm-grid-section').on('shown.bs.collapse', function ()
		{
			$('#hr-tm-btn-refresh').click();
		});

		$('#hr-tm-journals-section').on('hidden.bs.collapse', function ()
		{
			$('#hr-tm-form-section').collapse('show');
		});

		$('#hr-tm-form-section').on('shown.bs.collapse', function ()
		{
			$('#hr-tm-name').focus();
		});

		$('#hr-tm-form-section').on('hidden.bs.collapse', function ()
		{
			$('#hr-tm-grid-section').collapse('show');
			$('#hr-tm-journals-section').collapse('show');
		});

    //--------------------------------------------------------------------------

    $('#hr-tm-btn-clear-filter').click(function()
		{
			$('#hr-tm-filters-form').find('.tokenfield').find('.close').click()

			$('#hr-tm-filters-form').jqMgVal('clearForm');

			$('#hr-tm-btn-filter').click();
		});

		$('#hr-tm-btn-filter').click(function()
		{
			var filters = [];

			$(this).removeClass('btn-default').addClass('btn-warning');

			if($('#hr-tm-filters-body').is(":visible"))
			{
			}

			if(!$('#hr-tm-filters-form').jqMgVal('isFormValid'))
			{
				return;
			}

      if($('#hr-tm-filters-body').is(":visible"))
			{
				$('#hr-tm-filters-salaryter-toggle').click();
			}

			$('#hr-tm-filters-form').jqMgVal('clearContextualClasses');

			if(!$("#hr-tm-filters-names").isEmpty())
      {
        filters.push({'field':'t1.mname', 'op':'cn', 'data': $("#hr-tm-filters-names").val()});
      }

			if(!$("#hr-tm-filters-phases").isEmpty())
			{
				filters.push({'field':'t1.position', 'op':'in', 'data': $("#hr-tm-filters-phases").val()});
			}

			if(!$("#hr-tm-filters-employee").isEmpty())
			{
				filters.push({'field':'t1.employee', 'op':'in', 'data': $("#hr-tm-filters-employee").val()});
			}

      if($("#hr-tm-filters-limit-date").val() != '__/__/____' && !$("#hr-tm-filters-limit-date").isEmpty())
			{
        filters.push({'field':'t1.limit_date', 'op':'ge', 'data': $.datepicker.formatDate("yy-mm-dd", $("#hr-tm-filters-limit-date").datepicker("getDate")) + ' 00:00:00'});
        filters.push({'field':'t1.limit_date', 'op':'le', 'data': $.datepicker.formatDate("yy-mm-dd", $("#hr-tm-filters-limit-date").datepicker("getDate")) + ' 23:59:59'});
			}

			if(!$("#hr-tm-filters-description").isEmpty())
			{
				filters.push({'field':'t1.manual_reference', 'op':'cn', 'data': $("#hr-tm-filters-description").val()});
			}

			if(filters.length == 0)
			{
				$('#hr-tm-btn-filter').removeClass('btn-warning').addClass('btn-default');
			}

			$('#hr-tm-grid').jqGrid('setGridParam', {'postData':{"filters":"{'groupOp':'AND','rules':" + JSON.stringify(filters) + "}"}}).trigger('reloadGrid');

		});

    //--------------------------------------------------------------------------

		$('#hr-tm-btn-new').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			$('#hr-tm-btn-toolbar').disabledButtonGroup();
			$('#hr-tm-btn-group-3').enableButtonGroup();
			$('#hr-tm-form-new-title').removeClass('hidden');
      $('.hr-tm-btn-tooltip').tooltip('hide');
			$('#hr-tm-grid-section').collapse('hide');
			$('#hr-tm-journals-section').collapse('hide');
		});

		$('#hr-tm-btn-refresh').click(function()
		{
			$('.hr-tm-btn-tooltip').tooltip('hide')
			cleanJournals('hr-tm-');
		});

		$('#hr-tm-btn-export-xls').click(function()
		{
				$('#hr-tm-gridXlsButton').click();
		});

		$('#hr-tm-btn-export-csv').click(function()
		{
				$('#hr-tm-gridCsvButton').click();
		});

		$('#hr-tm-btn-save').click(function()
		{
			var url = $('#hr-tm-form').attr('action'), action = 'new';

      $('.hr-tm-btn-tooltip').tooltip('hide');

      $('#hr-tm-manual-reference').val($('.ql-editor').html());

			if(!$('#hr-tm-form').jqMgVal('isFormValid'))
			{
				return;
			}

			if($('#hr-tm-id').isEmpty())
			{
				url = url + '/create';
			}
			else
			{
        action = 'edit';
				url = url + '/update';
			}
			$.ajax(
			{
				type: 'POST',
				data: JSON.stringify($('#hr-tm-form').formToObject('hr-tm-')),
				dataType : 'json',
				url: url,
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-tm-form');
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
            if(action == "edit")
            //$('#hr-tm-btn-close').click();
            {
              //Name
              $('#task-name-' + $('#hr-tm-id').val()).html($('#hr-tm-name').val());
              $('#task-name-' + $('#hr-tm-id').val()).attr('hr-tm-name', $('#hr-tm-name').val());
              //Ribbbon, nombre y color de el liston
              $('#task-ribbon-' + $('#hr-tm-id').val()).removeClass('ribbon-' + $('#task-name-' + $('#hr-tm-id').val()).attr('hr-tm-priority'));
              $('#task-ribbon-' + $('#hr-tm-id').val()).addClass('ribbon-' + $('#hr-tm-priority').val().toUpperCase());
              $('#task-ribbon-' + $('#hr-tm-id').val()).html($('#hr-tm-priority-label').val().toUpperCase());
              $('#task-name-' + $('#hr-tm-id').val()).attr('hr-tm-priority', $('#hr-tm-priority').val());
              $('#task-ribbon-' + $('#hr-tm-id').val()).attr('hr-tm-priority', $('#hr-tm-priority').val());
              //Etapa
              $('#task-name-' + $('#hr-tm-id').val()).attr('hr-tm-phase', $('#hr-tm-phase-id').val());
              //Responsable
              $('#task-name-' + $('#hr-tm-id').val()).attr('hr-tm-employee', $('#hr-tm-responsible-employee-id').val());
              //Hora
              $('#task-name-' + $('#hr-tm-id').val()).attr('hr-tm-hour', $('#hr-tm-planned-initial-hour').val());
              //Descripcion
              $('#task-name-' + $('hr-tm-id').val()).attr('hr-tm-reference', $('#hr-tm-manual-reference').val());
              //Porcentaje de Avance
              $('#task-name-' + $('hr-tm-id').val()).attr('hr-tm-completion-percentage', $('#hr-tm-completion-percentage').val());
              //Refresca la imagen del responsable
              $('#task-url-' + $('#hr-tm-id').val()).attr('src', json.url);
            }
            else{
              //Lo va a crear
              //alert(json.task.id);
              var priority = '';
              if(json.task.priority == 'B'){
                priority = 'BAJA';
              }else if(json.task.priority == 'M') {
                priority = 'MEDIA';
              }else if(json.task.priority == 'A'){
                priority = 'ALTA';
              }else if(json.task.priority == 'U'){
                priority = 'URGENTE';
              }
              panel = $('<div/>', {'id': 'hr-tm-task-' + json.task.id, 'data-task-id':json.task.id, 'data-current-phase-id':json.task.phase_id, 'class' :'panel panel-default panel-ms-schedule wrapper', 'style' : 'bottom: 10px;top: 10px;padding-bottom: 0px;margin-bottom: 8px;'});
              ribbon = $('<div/>', {class: 'ribbon-wrapper-green'});
              row = $('<div/>', {class: 'row'});
              col_12 = $('<div/>', {class: 'col-md-12'});
              col_6 = $('<div/>', {class: 'col-md-6 path'});
              col_4 = $('<div/>', {class: 'col-md-4'});
              col_2 = $('<div/>', {class: 'col-md-2'});
              media_1 = $('<div/>', {'class' : 'media', 'style' : 'margin-bottom: 0px;padding-bottom: 0px;'});
              media_2 = $('<div/>', {class: 'media path'});
              $('<div/>', {
      				    'class': 'ribbon-' + json.task.priority,
      						'id'   : 'task-ribbon-' + json.task.id,
                  'hr-tm-priority' : json.task.priority,
                  'html' : priority
      				}).appendTo(ribbon);
              panel.append(ribbon);
              panel.append(row);
              row.append(col_12);
              row.append(col_6);
              row.append(col_4);
              row.append(col_2);
              col_12.append(media_1);
              col_6.append(media_2);
              $('<a/>',{
                'html' : json.task.name,
                'class' : 'fake-link task-name',
                'onclick' : 'hrTmEditTask(this)',
                'id' : 'task-name-' + json.task.id,
                'hr-tm-id' : json.task.id,
                'hr-tm-name' : json.task.name,
                'hr-tm-priority' : json.task.priority,
                'hr-tm-employee' : json.task.responsible_employee_id,
                'hr-tm-phase' : json.task.phase_id,
                'hr-tm-date' : json.task.limit_date,
                'hr-tm-reference' : json.task.manual_reference,
                'hr-tm-hour' : json.task.planned_initial_hour,
                'hr-tm-completion-percentage' : json.task.completion_percentage,
                'style' : 'margin-bottom: 0px;padding-bottom: 0px;padding-right: 32px; color:#545759;font-size: 15px;'
              }).appendTo(media_1);
              $('<h5/>',{
                'id' : 'task-date-' + json.task.id,
                'html' : json.task.limit_date,
                'style' : 'color: black;'
              }).appendTo(media_2);
              $('<h5/>',{
                'id' : 'task-hour-' + json.task.id,
                'html' : '0 horas',
                'style' : 'margin-bottom: 0px;'
              }).appendTo(media_2);
               a = $('<a/>', {'class' : 'pull-left', 'style' : 'padding-top: 5px;padding-left: 45px; padding-bottom: 0px;'});
               col_4.append(a);
               $('<img/>', {
                 'id' : 'task-url-' + json.task.id,
                 'class' : 'media-object img-circle',
                 'src' : json.url,
                 'style' : 'width: 50px;height:50px;'
               }).appendTo(a);
               $('<i/>', {
                 'id' : 'hr-tm-btn-delete-' + json.task.id,
                 'task-id' : json.task.id,
                 'onclick' : 'hrTmDeleteTask(this);',
                 'class' : 'glyphicon glyphicon-trash text-danger',
                 'style' : 'padding-top: 45px;border-right-width: 10px;'
               }).appendTo(col_2);
               panel.append(row);
               $('#hr-tm-phase-' + json.task.phase_id).append(panel);
              //$('#hr-tm-task-' + json.task.id)
            }
            $('#hr-tm-btn-close').click();
					}
					else if(json.info)
					{
						$('#hr-tm-form').showAlertAsFirstChild('alert-info', json.info);
					}

					$('#app-loader').addClass('hidden');
					enableAll();
          $('.hr-tm-btn-tooltip').tooltip('hide');
				}
			});
		});

    $('#hr-tm-btn-modal-delete').click(function()
    {
      var id = $('#hr-tm-id').val();

      $('.hr-tm-btn-tooltip').tooltip('hide');

      $.ajax(
			{
				type: 'POST',
				data: JSON.stringify({'_token':$('#app-token').val(), 'id':id}),
        url:  $('#hr-tm-form').attr('action') + '/delete',
				dataType : 'json',
				error: function (jqXHR, textStatus, errorThrown)
				{
					handleServerExceptions(jqXHR, 'hr-tm-btn-toolbar', false);
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
						$('#hr-tm-btn-refresh').click();
						$('#hr-tm-modal-delete').modal('hide');
						$('#hr-tm-btn-toolbar').showAlertAfterElement('alert-success alert-custom',json.success, 5000);
            $('#hr-tm-task-' + json.id).remove();
            $('#hr-tm-id').val("")
					}
					$('#app-loader').addClass('hidden');
					enableAll();
          $('.hr-tm-btn-tooltip').tooltip('hide');
				}
			});
    });

		$('#hr-tm-btn-close').click(function()
		{
			if($(this).hasAttr('disabled'))
			{
				return;
			}

			$('#hr-tm-btn-group-1').enableButtonGroup();
			$('#hr-tm-btn-group-3').disabledButtonGroup();
			$('#hr-tm-form-new-title').addClass('hidden');
			$('#hr-tm-form-edit-title').addClass('hidden');
			$('#hr-tm-form').jqMgVal('clearForm');
      $('.ql-editor').html('');
			$('#hr-tm-form-section').collapse('hide');
      $('.hr-tm-btn-tooltip').tooltip('hide');
		});

		//Sortable para items, los items que se agarra na los rows
		$( ".sortable-rows" ).sortable({
			items       : '.panel-ms-schedule',
			connectWith:  '.sortable-rows',
			placeholder: 'panel-placeholder',
			start: function(event, ui) {
					ui.placeholder.html(ui.item.html());
			},
      // out: function( event, ui ) {
      //   console.log('out tarea: ' + $(ui.item[0]).attr('data-task-id'));
      //   console.log('out fase: ' +$(ui.item[0]).parent().attr('data-phase-id'));
      // },
      update: function( event, ui ) {
        // console.log(ui.position);
        // console.log($(ui.item[0]));
        var tasks = [];
        if($(ui.item[0]).attr('data-current-phase-id') == $(ui.item[0]).parent().attr('data-phase-id') && $(ui.item[0]).attr('data-top') != ui.position.top && $(ui.item[0]).attr('data-left') != ui.position.left)
        {
          // $(ui.item[0]).attr('data-task-id', $(ui.item[0]).attr('data-current-task-id'));
          $(ui.item[0]).attr('data-top', ui.position.top);
          $(ui.item[0]).attr('data-left', ui.position.left);
          // console.log('cambio de posicion');
          // console.log('id tarea: ' + $(ui.item[0]).attr('data-task-id'));
          // console.log('id fase: ' + $(ui.item[0]).parent().attr('data-phase-id'));
          position = 0;
          $('#hr-tm-phase-' + $(ui.item[0]).parent().attr('data-phase-id')).children('.wrapper').each(function()
          {
            position++;
            tasks.push({id:$(this).attr('data-task-id'), phase_id: $(this).attr('data-current-phase-id'), position: position})
          });
          //console.log(tasks);
          hrTmupdateTaskPhaseAndPosition(tasks);
        }
        if($(ui.item[0]).attr('data-current-phase-id') != $(ui.item[0]).parent().attr('data-phase-id') && $(ui.item[0]).attr('data-top') != ui.position.top && $(ui.item[0]).attr('data-left') != ui.position.left)
        {
          position = 0, currentPhaseId = $(ui.item[0]).attr('data-current-phase-id');
          $(ui.item[0]).attr('data-current-phase-id', $(ui.item[0]).parent().attr('data-phase-id'));
          $('#task-name-' + $(ui.item[0]).attr('data-task-id')).attr('hr-tm-phase', $(ui.item[0]).parent().attr('data-phase-id'));
          $(ui.item[0]).attr('data-top', ui.position.top);
          $(ui.item[0]).attr('data-left', ui.position.left);
          // console.log(currentPhaseId);
          // console.log($(ui.item[0]).parent().attr('data-phase-id'));
          // $(ui.item[0]).attr('data-task-id', $(ui.item[0]).attr('data-current-task-id'));
          $('#hr-tm-phase-' + currentPhaseId).children('.wrapper').each(function()
          {
            position++;
            tasks.push({id:$(this).attr('data-task-id'), phase_id: $(this).attr('data-current-phase-id'), position: position})
          });
          position = 0;
          $('#hr-tm-phase-' + $(ui.item[0]).parent().attr('data-phase-id')).children('.wrapper').each(function()
          {
            position++;
            tasks.push({id:$(this).attr('data-task-id'), phase_id: $(this).attr('data-current-phase-id'), position: position})
          });
          // console.log('cambio de fase');
          // console.log('id tarea: ' + $(ui.item[0]).attr('data-task-id'));
          // console.log('id fase: ' + $(ui.item[0]).parent().attr('data-phase-id'));
          hrTmupdateTaskPhaseAndPosition(tasks);
        }
      }
			});

			$( ".sortable-rows" ).disableSelection();

      //Sortable para las columnas, el container
			$(".sortable-columns").sortable({
				items		 : '.sortable-rows',
				connectWith: '.sortable-columns',
				start: function(event, ui){
					ui.placeholder.html(ui.item.html());
				},
        change: function( event, ui ) {
          console.log('Holi');
        }
			});

			$(".sortable-columns").disableSelection();

      cmsBmQuill = new Quill('#hr-tm-editor-container',{ modules:{
  	    	toolbar:
  				[
  					[{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            [ 'bold', 'italic', 'underline', 'strike' ],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'script': 'super' }, { 'script': 'sub' }],
            ['blockquote', 'code-block' ],
            [{ 'list': 'ordered' }, { 'list': 'bullet'}, { 'align': [] }, { 'indent': '-1' }, { 'indent': '+1' }],
  					[{ 'direction': 'rtl' }  , 'link'],
            //[ 'image', 'video'],
            [ 'clean' ]
  	    	]
  	  	},
  	  	placeholder: 'Ingrese su texto...',
  	  	theme: 'snow'
  		});

      $('#hr-tm-btn-edit-helper').click(function()
      {
        showButtonHelper('hr-tm-btn-close', 'hr-tm-btn-group-2', $('#hr-tm-edit-action').attr('data-content'));
      });

      $('#hr-tm-btn-delete-helper').click(function()
      {
        showButtonHelper('hr-tm-btn-close', 'hr-tm-btn-group-2', $('#hr-tm-delete-action').attr('data-content'));
      });

      if(!$('#hr-tm-new-action').isEmpty())
      {
        $('#hr-tm-btn-new').click();
      }

      if(!$('#hr-tm-edit-action').isEmpty())
      {
        showButtonHelper('hr-tm-btn-close', 'hr-tm-btn-group-2', $('#hr-tm-edit-action').attr('data-content'));
      }

      if(!$('#hr-tm-delete-action').isEmpty())
      {
        showButtonHelper('hr-tm-btn-close', 'hr-tm-btn-group-2', $('#hr-tm-delete-action').attr('data-content'));
      }

      $('#hr-tm-completion-percentage-label-show-all-button').focusout(function()
  		{
  			$('.ql-editor').focus();
  		});
      //------------------------------------------------------------------------
      setTimeout(function ()
  		{
        $('#hr-tm-responsible-employee-label').setAutocompleteLabel('{!! !empty($loggedUser)? $loggedUser['id']: '' !!}');

  			$('#hr-tm-filters-phases').tokenfield({
  	  		autocomplete: {
  			    source: hrTmFiltersPhases,
  			    delay: 100
  			  },
  			  showAutocompleteOnFocus: true,
  				beautify:false
  			});

  			$('#hr-tm-filters-phases').on('tokenfield:createtoken', function (event) {
  		    var available_tokens = hrTmFiltersPhases;
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

  			$('#hr-tm-filters-priority').tokenfield({
  	  		autocomplete: {
  			    source: hrTmFiltersPriority,
  			    delay: 100
  			  },
  			  showAutocompleteOnFocus: true,
  				beautify:false
  			});

  			$('#hr-tm-filters-priority').on('tokenfield:createtoken', function (event) {
  		    var available_tokens = hrTmFiltersPriority;
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

  			$('#hr-tm-filters-employee').tokenfield({
  	  		autocomplete: {
  			    source: hrTmFiltersEmployee,
  			    delay: 100
  			  },
  			  showAutocompleteOnFocus: true,
  				beautify:false
  			});

  			$('#hr-tm-filters-employee').on('tokenfield:createtoken', function (event) {
  		    var available_tokens = hrTmFiltersEmployee;
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
    {!! Form::open(array('id' => 'hr-tm-filters-form', 'url' => URL::to('/'), 'role' => 'form', 'onsubmit' => 'return false;', 'class' => 'form-horizontal')) !!}
		<div id="hr-tm-filters" class="panel panel-default">
			<div class="panel-heading custom-panel-heading clearfix">
				<button id="hr-tm-filters-salaryter-toggle" type="button" class="btn btn-default btn-sm btn-filter-toggle pull-right" data-toggle="collapse" data-target="#hr-tm-filters-body"><i class="fa fa-chevron-down"></i></button>
				<h3 class="panel-title custom-panel-title pull-left">
					{{ Lang::get('form.filtersTitle') }}
				</h3>
				{!! Form::button('<i class="fa fa-filter"></i> ' . Lang::get('form.filterButton'), array('id' => 'hr-tm-btn-filter', 'class' => 'btn btn-default btn-sm pull-right btn-filter-left-margin')) !!}
				{!! Form::button('<i class="fa fa-eraser"></i> ' . Lang::get('form.clearFilterButton'), array('id' => 'hr-tm-btn-clear-filter', 'class' => 'btn btn-default btn-sm pull-right')) !!}
			</div>
			<div id="hr-tm-filters-body" class="panel-body collapse">
				<div class="row">
					<div class="col-lg-6 col-md-12">
						<div class="form-group">
							{!! Form::label('hr-tm-filters-names', Lang::get('decima-human-resources::task-management.names'), array('class' => 'col-sm-2 control-label', 'style' => 'text-align: center')) !!}
							<div class="col-sm-10 mg-hm">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-font"></i></span>
									{!! Form::text('hr-tm-filters-names', null , array('id' => 'hr-tm-filters-names', 'class' => 'form-control')) !!}
								</div>
								<p class="help-block">{{ Lang::get('decima-human-resources::task-management.namesHelperText') }}</p>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('hr-tm-filters-phases', Lang::get('decima-human-resources::task-management.phases'), array('class' => 'col-sm-2 control-label', 'style' => 'text-align: center')) !!}
							<div class="col-sm-10 mg-hm">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-list-ol"></i></span>
									{!! Form::text('hr-tm-filters-phases', null , array('id' => 'hr-tm-filters-phases', 'class' => 'form-control')) !!}
								</div>
								<p class="help-block">{{ Lang::get('decima-human-resources::task-management.phasesHelperText') }}</p>
							</div>
						</div>
            <div class="form-group">
							{!! Form::label('hr-tm-filters-priority', Lang::get('decima-human-resources::task-management.priority'), array('class' => 'col-sm-2 control-label', 'style' => 'text-align: center')) !!}
							<div class="col-sm-10 mg-hm">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-exclamation-triangle"></i></span>
									{!! Form::text('hr-tm-filters-priority', null , array('id' => 'hr-tm-filters-priority', 'class' => 'form-control')) !!}
								</div>
								<p class="help-block">{{ Lang::get('decima-human-resources::task-management.priorityHelperText') }}</p>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-12">
            <div class="form-group">
              {!! Form::label('hr-tm-filters-employee', Lang::get('decima-human-resources::task-management.empleado'), array('class' => 'col-sm-2 control-label', 'style' => 'text-align: center')) !!}
              <div class="col-sm-10 mg-hm">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-font"></i></span>
                  {!! Form::text('hr-tm-filters-employee', null , array('id' => 'hr-tm-filters-employee', 'class' => 'form-control')) !!}
                </div>
                <p class="help-block">{{ Lang::get('decima-human-resources::task-management.employeeHelperText') }}</p>
              </div>
            </div>
            <div class="form-group">
							{!! Form::label('hr-tm-filters-limit-date', Lang::get('decima-human-resources::task-management.limitDateShort'), array('class' => 'col-sm-2 control-label', 'style' => 'text-align: center')) !!}
							<div class="col-sm-10 mg-hm">
								{!! Form::date('hr-tm-filters-limit-date', array('class' => 'form-control')) !!}
								<!-- {!! Form::text('hr-whm-filters-names', null , array('id' => 'hr-whm-filters-names', 'class' => 'form-control')) !!} -->
								<p class="help-block">{{ Lang::get('decima-human-resources::task-management.limitDateShortHelperText') }}</p>
							</div>
						</div>
          	<div class="form-group">
							{!! Form::label('hr-tm-filters-description', Lang::get('decima-human-resources::task-management.descriptionShort'), array('class' => 'col-sm-2 control-label', 'style' => 'text-align: center')) !!}
							<div class="col-sm-10 mg-hm">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
									{!! Form::text('hr-tm-filters-description', null , array('id' => 'hr-tm-filters-description', 'class' => 'form-control')) !!}
								</div>
								<p class="help-block">{{ Lang::get('decima-human-resources::task-management.descriptionShortHelperText') }}</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		{!! Form::close() !!}
    <div id="hr-tm-btn-toolbar" class="section-header btn-toolbar" role="toolbar">
			<div id="hr-tm-btn-group-1" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-plus"></i> ' . Lang::get('toolbar.new'), array('id' => 'hr-tm-btn-new', 'class' => 'btn btn-default hr-tm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('decima-human-resources::task-management.new'))) !!}
				<!-- {!! Form::button('<i class="fa fa-refresh"></i> ' . Lang::get('toolbar.refresh'), array('id' => 'hr-tm-btn-refresh', 'class' => 'btn btn-default hr-tm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'data-original-title' => Lang::get('toolbar.refreshLongText'))) !!} -->
			</div>
			<div id="hr-tm-btn-group-3" class="btn-group btn-group-app-toolbar">
				{!! Form::button('<i class="fa fa-save"></i> ' . Lang::get('toolbar.save'), array('id' => 'hr-tm-btn-save', 'class' => 'btn btn-default hr-tm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('decima-human-resources::task-management.save'))) !!}
				{!! Form::button('<i class="fa fa-undo"></i> ' . Lang::get('toolbar.close'), array('id' => 'hr-tm-btn-close', 'class' => 'btn btn-default hr-tm-btn-tooltip', 'data-container' => 'body', 'data-toggle' => 'tooltip', 'disabled' => '', 'data-original-title' => Lang::get('toolbar.closeLongText'))) !!}
			</div>
		</div>
		<div id='hr-tm-grid-section' class="row collapse in">
      <div class='col-md-12 sortable-columns ' style="overflow-x:auto;white-space:nowrap;">
          @foreach ($phasesWithTasks as $phaseWithTasks)
            <div id="hr-tm-phase-{{ $phaseWithTasks['id'] }}" data-phase-id="{{ $phaseWithTasks['id'] }}" class="sortable sortable-rows" style="width:290px;display:inline-block; margin-right: 12px;padding-right: 8px;padding-left:5px;padding-bottom: 25px;box-shadow: 1px 1px 1px #999;;">
              <h3 class="text-center">{{ $phaseWithTasks['name'] }}</h3>
              @foreach($phaseWithTasks['tasks'] as $task)
                <div id= "hr-tm-task-{{ $task['id'] }}" data-task-id="{{ $task['id'] }}" data-current-phase-id="{{ $task['phase_id'] }}" data-top="" data-left="" class="panel panel-default panel-ms-schedule wrapper" style="bottom: 10px;top: 10px;padding-bottom: 0px;margin-bottom: 8px;">
                  @if($task['priority'] == 'B')
                    <div class="ribbon-wrapper-green"><div id='task-ribbon-{{ $task['id'] }}' hr-tm-priority="{{ $task['priority'] }}" class="ribbon-B">BAJA</div></div>
                  @elseif($task['priority'] == 'M')
                    <div class="ribbon-wrapper-green"><div id='task-ribbon-{{ $task['id'] }}' hr-tm-priority="{{ $task['priority'] }}" class="ribbon-M">MEDIA</div></div>
                  @elseif($task['priority'] == 'A')
                    <div class="ribbon-wrapper-green"><div id='task-ribbon-{{ $task['id'] }}' hr-tm-priority="{{ $task['priority'] }}" class="ribbon-A">ALTA</div></div>
                  @elseif($task['priority'] == 'U')
                    <div class="ribbon-wrapper-green"><div id='task-ribbon-{{ $task['id'] }}' hr-tm-priority="{{ $task['priority'] }}" class="ribbon-U">URGENTE</div></div>
                  @endif
                  <div class="row">
                    <div class="col-md-12">
                        <div class="media" style="margin-bottom: 0px;padding-bottom: 0px;">
                        <a id="task-name-{{ $task['id'] }}"
                           hr-tm-id        = "{{ $task['id'] }}"
                           hr-tm-name      = "{{ $task['name'] }}"
                           hr-tm-priority  = "{{ $task['priority'] }}"
                           hr-tm-employee  = "{{ $task['responsible_employee_id'] }}"
                           hr-tm-phase     = "{{ $task['phase_id'] }}"
                           hr-tm-date      = "{{ $task['limit_date'] }}"
                           hr-tm-reference = "{{ $task['manual_reference'] }}"
                           hr-tm-completion-percentage = "{{ $task['completion_percentage'] }}"
                           hr-tm-hour      = "{{ $task['planned_initial_hour'] }}" class="fake-link task-name" title="{{ $task['name'] }}" style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 0px;padding-bottom: 0px;padding-right: 32px; color:#545759;font-size: 15px;" onclick="hrTmEditTask(this);">{{ $task['name'] }}</a>
                      </div>
                    </div>
                    <div class="col-md-6 path">
                      <div class="media path">
                        <h5 id="task-date-{{ $task['id'] }}" style="color: black;">{{ $task['limit_date']}}</h5>
                        @if($task['worked_hours'] > $task['planned_initial_hour'])
                          <h5 id="task-hour-{{ $task['id'] }}" style="color: #CC3319;margin-bottom: 0px;">{{ $task['worked_hours'] }} Horas.</h5>
                        @else
                          <h5 id="task-hour-{{ $task['id'] }}" style="margin-bottom: 0px;">{{ $task['worked_hours'] }} Horas.</h5>
                        @endif
                      </div>
                    </div>
                    <div class="col-md-4">
                     <a class="pull-left" href="#" style="padding-top: 5px;padding-left: 45px; padding-bottom: 0px;">
                        <img id="task-url-{{ $task['id'] }}" title="{{ $task['responsible_employee_id'] }}" class="media-object img-circle"  src="{{ $task['url'] }}" style="width: 50px;height:50px;">
                     </a>
                    </div>
                    <div class="col-md-2">
                        <i task-id= "{{ $task['id'] }}" id="hr-tm-btn-delete-{{ $task['id'] }}"  onclick="hrTmDeleteTask(this);" class="glyphicon glyphicon-trash text-danger" style="padding-top: 45px;border-right-width: 10px;"></i>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endforeach
      </div>
    </div>
  </div>
</div>
<div id='hr-tm-journals-section' class="row collapse in section-block">
	{{-- Form::journals('hr-tm-', $appInfo['id']) --}}
</div>
<div id='hr-tm-form-section' class="row collapse">
	<div class="col-lg-12 col-md-12">
		<div class="form-container">
			{!! Form::open(array('id' => 'hr-tm-form', 'url' => URL::to('human-resources/transactions/task-management'), 'role'  =>  'form', 'onsubmit' => 'return false;')) !!}
				<legend id="hr-tm-form-new-title" class="hidden">{{ Lang::get('decima-human-resources::task-management.formNewTask') }}</legend>
				<legend id="hr-tm-form-edit-title" class="hidden">{{ Lang::get('decima-human-resources::task-management.formEditPosition') }}</legend>
				<div class="row">
					<div class="col-lg-6 col-md-6">
						<div class="form-group mg-hm">
							{!! Form::label('hr-tm-name', Lang::get('decima-human-resources::task-management.name'), array('class' => 'control-label')) !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-font"></i>
                </span>
                {!! Form::text('hr-tm-name', null , array('id' => 'hr-tm-name', 'class' => 'form-control', 'data-mg-required' => '')) !!}
              </div>
				    	{!! Form::hidden('hr-tm-id', null, array('id' => 'hr-tm-id')) !!}
              {!! Form::hidden('hr-tm-manual-reference', null , array('id' => 'hr-tm-manual-reference', 'class' => 'form-control', 'data-mg-required' => '')) !!}
		  			</div>
            <div class="form-group mg-hm">
              {!! Form::label('hr-tm-phase-id', Lang::get('decima-human-resources::task-management.phases'), array('class' => 'control-label')) !!}
              {!! Form::autocomplete('hr-tm-phase-label', $phases, array('class' => 'form-control', 'data-mg-required' => ''), 'hr-tm-phase-label', 'hr-tm-phase-id', null, 'fa-list-ol') !!}
              {!! Form::hidden('hr-tm-phase-id', null, array('id'  =>  'hr-tm-phase-id')) !!}
            </div>
            <div class="form-group mg-hm">
              {!! Form::label('hr-tm-responsible-employee-label', Lang::get('decima-human-resources::task-management.employee'), array('class' => 'control-label')) !!}
              {!! Form::autocomplete('hr-tm-responsible-employee-label', $employees, array('class' => 'form-control', 'data-mg-required' => '', 'data-mg-clear-ignored' => ''), 'hr-tm-responsible-employee-label', 'hr-tm-responsible-employee-id', null, 'fa-user') !!}
              {!! Form::hidden('hr-tm-responsible-employee-id', null, array('id'  =>  'hr-tm-responsible-employee-id')) !!}
            </div>


					</div>
					<div class="col-lg-6 col-md-6">
            <div class="form-group mg-hm">
                {!! Form::label('hr-tm-limit-date', Lang::get('decima-human-resources::task-management.limitDate'), array('class' => 'control-label')) !!}
                {!! Form::date('hr-tm-limit-date', array('class' => 'form-control', 'data-mg-required' => '', 'data-default-value' => $currentDate), $currentDate) !!}
            </div>
            <div class="form-group mg-hm">
              {!! Form::label('hr-tm-priority', Lang::get('decima-human-resources::task-management.priority'), array('class' => 'control-label')) !!}
              {!! Form::autocomplete('hr-tm-priority-label', $priority, array('class' => 'form-control', 'data-mg-required' => ''), 'hr-tm-priority-label', 'hr-tm-priority', null, 'fa-exclamation-triangle') !!}
              {!! Form::hidden('hr-tm-priority', null, array('id'  =>  'hr-tm-priority')) !!}
            </div>
            <div class="row">
  					  <div class="col-lg-6 col-md-6">
                <div class="form-group mg-hm">
                    {!! Form::label('hr-tm-planned-initial-hour', Lang::get('decima-human-resources::task-management.plannedInitialHour'), array('class' => 'control-label')) !!}
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="fa fa-clock-o"></i>
                      </span>
                      {!! Form::text('hr-tm-planned-initial-hour', null , array('id' => 'hr-tm-planned-initial-hour', 'class' => 'form-control', 'data-mg-validator' => 'money', 'data-mg-required' => '')) !!}
                    </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="form-group mg-hm">
                  {!! Form::label('hr-tm-completion-percentage', Lang::get('decima-human-resources::task-management.completitionPercentage'), array('class' => 'control-label')) !!}
                  {!! Form::autocomplete('hr-tm-completion-percentage-label', $completionPercentage, array('class' => 'form-control', 'data-mg-required' => ''), 'hr-tm-completion-percentage-label', 'hr-tm-completion-percentage', null, 'fa-line-chart') !!}
                  {!! Form::hidden('hr-tm-completion-percentage', null, array('id'  =>  'hr-tm-completion-percentage')) !!}
                </div>
              </div>
            </div>
					</div>
          <div class="col-lg-12 col-md-12">
						<div id="hr-tm-editor-container" class="sc-toolbar-container toolbar">
							<!-- Javascript para obtener editor: $('.ql-editor').html() -->
						</div>
					</div>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>

<div id='hr-tm-modal-delete' class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm hr-tm-btn-delete">
    <div class="modal-content">
			<div class="modal-body" style="padding: 20px 20px 0px 20px;">
				<p id="hr-tm-delete-message" data-default-label="{{ Lang::get('decima-human-resources::task-management.deleteMessageConfirmation') }}"></p>
      </div>
			<div class="modal-footer" style="text-align:center;">
				<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get('form.no') }}</button>
				<button id="hr-tm-btn-modal-delete" type="button" class="btn btn-primary">{{ Lang::get('form.yes') }}</button>
			</div>
    </div>
  </div>
</div>
@parent
@stop
