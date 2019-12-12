<?php 
include (public_path()."/storage/includes/lang1.en.php" );
?>
{!! Html::script('js/parsley.min.js') !!}
<div class="modal-dialog">
<div class="modal-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">×</span></button>
    <h4 class="modal-title">@if($exists == 0){{$language['new doc']}} '{{$document}}' {{$language['existing_workflow']}} @else {{$language['list_workflows']}} '{{$document}}' @endif</h4>
  </div>
  <div class="modal-body" style="min-height: 150px !important;">
  @if($exists == 0)
    <!-- form start -->
          {!! Form::open(array('url'=> array('WorkflowSelectSave',@$docid), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'workflowAddForm', 'id'=> 'workflowAddForm','data-parsley-validate'=> '')) !!}            
             <div class="form-group">
                <label for="Workflow" class="col-sm-3 control-label">{{$language['workflow name']}}: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <select class="form-control" name="workflow_select" id="workflow_select" required="" data-parsley-required-message="Workflow name is required" data-parsley-trigger="change focusout">
                     <option value="">{{$language['select_workflow']}}</option>
                     @foreach(@$workflows as $wf)
                    <option value="{{$wf->workflow_id}}">{{@$wf->workflow_name}}</option>
                    @endforeach
                  	</select>
                </div>
            </div>
  			<div id="stages"></div><!-- Stages are list here -->
		  <div class="form-group">
             <label for="inputEmail3" class="col-sm-3 control-label">{{$language['activity_name']}}: <span class="compulsary">*</span></label>
             <div class="col-sm-8">
                <select class="form-control" id="doc_activity_name"  name="doc_activity_name" required="" data-parsley-required-message="Activity name is required" data-parsley-trigger="change focusout">
                   <option value="">{{$language['select_activity']}}</option>
                   @foreach(@$activities as $row)
                   <option value="{{$row->activity_id}}">{{@$row->activity_name}}</option>
                   @endforeach  
                </select>
             </div>
          </div>
          <div class="form-group">
             <label for="activity_due_date" class="col-sm-3 control-label">{{$language['activity_due_date']}}:</label>
             <div class="col-sm-8">
                <div class="input-group">
                   <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                   </div>
                   <input type="text" class="form-control" id="doc_activity_due_date" name="doc_activity_due_date" placeholder="YYYY-MM-DD">
                </div>
             </div>
          </div>
          <!-- <div class="form-group">
             <label for="assigned_to" class="col-sm-3 control-label">Assigned To</label>
             
             <div class="col-sm-8">
               <input type="text" class="form-control" name="doc_assigned_to" id="doc_assigned_to">
             </div>
             </div> -->
          <div class="form-group">
             <label for="new_object_id" class="col-sm-3 control-label">{{$language['responsible_user']}}:</label>
             <div class="col-sm-8">
                <select class="form-control" id="doc_assigned_to"  name="doc_assigned_to" >
                   <option value="">{{$language['select_user']}}</option>
                   @foreach(@$user as $row)
                   <option value="{{$row->username}}">{{@$row->user_full_name}}</option>
                   @endforeach  
                </select>
             </div>
          </div>
          <div class="form-group">
             <label for="activity_note" class="col-sm-3 control-label">{{$language['activity_note']}}:</label>
             <div class="col-sm-8">
                <textarea class="form-control" name="doc_activity_note" id="doc_activity_note" rows="3"></textarea> 
             </div>
          </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"></label>
            <div class="col-sm-8">
                <input class="btn btn-primary" id="save" type="submit" value="{{$language['save']}}" > &nbsp;&nbsp;
                {!!Form::button($language['close'], array('class' => 'btn btn-primary btn-danger', 'data-dismiss'=> 'modal', 'aria-hidden'=> 'true')) !!}&nbsp;
            </div>
        </div><!-- /.col -->
        {!! Form::close() !!}
      
      @else
              <table class="table table-condensed" style="margin-bottom: 0px !important;">
                <tbody><tr>
                  <th>{{$language['no']}}</th>
                  <th>{{$language['workflow name']}}</th>
                  <th>{{$language['color']}}</th>
                  <th style="text-align: center;" nowrap>{{$language['no_stages']}}</th>
                </tr>
                @php $i=1 @endphp
                @foreach(@$workflows as $wf)
                <tr>
                  <td>{{$i}}.</td>
                  <td><a href="viewworkflow/{{@$wf->workflow_id}}?object_id={{@$docid}}&type=document" style="cursor: pointer;text-align: left !important;float: left;color: #3c8dbc !important;">{{ucfirst(@$wf->workflow_name)}}</a></td>
                  <td>
                    <div class="progress progress-xs">
                      <div class="progress-bar" style="width: 100%;background-color: {{@$wf->workflow_color}};"></div>
                    </div>
                  </td>
                  <td style="text-align: center;"><span class="badge" style="background-color: {{@$wf->workflow_color}};">{{@$wf->stage_count}}</span></td>
                </tr>
                @php $i++ @endphp
                @endforeach
              </tbody>
            </table>
      <div class="form-group">
        <label class="col-sm-5 control-label"></label>
        <div class="col-sm-7">
          <button type="button" class="btn btn-danger" data-dismiss="modal">{{$language['close']}}</button>
        </div>
      </div>
      @endif
  </div>
  
</div>
<!-- /.modal-content -->
</div>
<script type="text/javascript">
//$(':input[type="submit"]').prop('disabled', true);
$(function(){
  // clear modal
  $('#addview_workflow').on('hidden.bs.modal', function () { 
    $('#content_wf').html('');
    console.log('clear');
  });

    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $('#workflow_select').change(function(){
      var data = $(this).val();
      if(data == "" || data == null)
      {
      	//swal("Please select a workflow");
      	$('#stages').hide();
      	// $(':input[type="submit"]').prop('disabled', true);
      	// return false;
      }
      $.ajax({
      	type: 'get',
        url: 'selectStages',
        dataType: 'JSON',
        data: {_token: CSRF_TOKEN,wfid:data},
        timeout: 50000,
        success: function(response){  
        	console.log(response);
            show_work_flow_stages(response);   
        },
        error: function(jqXHR, textStatus, errorThrown){
          console.log(jqXHR);    
          console.log(textStatus);    
          console.log(errorThrown);    
        }
      });            
    });

    var show_work_flow_stages = function(response)
    {
    	var html ='<div class="form-group"><label class="col-sm-3 control-label">Stage: <span class="compulsary">*</span></label><div class="col-sm-8"><select class="form-control" name="select_stage" required="" data-parsley-required-message="Stage is required" data-parsley-trigger="change focusout">';
    	$.each(response.wf_stages, function( key, value ) {
                 html +='<option value="'+value['workflow_stage_id']+'">'+value['workflow_stage_name']+'</option>';
             });
    	html +='</select></div></div>';
    	$('#stages').html(html);
    	$('#stages').show();
    	//$(':input[type="submit"]').prop('disabled', false);
    };
    $('#doc_activity_due_date').daterangepicker({
           singleDatePicker: true,
           "drops": "down",
           format: 'YYYY-MM-DD',
           showDropdowns: true
       });
});
</script>