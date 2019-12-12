<?php include (public_path()."/storage/includes/lang1.en.php" ); ?>
{!! Html::script('js/parsley.min.js') !!}    

               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Task</h4>
               </div>
               <div class="modal-body">
                  {!! Form::open(array('url'=> array('workflows_activity_save'), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'workflows_activity_save', 'id'=> 'workflows_activity_save','data-parsley-validate'=> '')) !!}  
                 <input type="hidden" name="act_workflow_id" id="act_workflow_id" value="{{ $workflow_id }}">

                 <input type="hidden" name="act_process_id" id="act_process_id" value="{{ $process_id }}">

                 <input type="hidden" name="act_stage_id" id="act_stage_id" value="{{ $stage_id }}">
                 <input type="hidden" name="act_activity_id" id="act_activity_id" value="{{ $activity_id }}">

                  <div class="form-group">
                     <div class="col-sm-12 alert_modal">
                       
                     </div>
                  </div>
               

                  @php $random = str_random(5); @endphp
                  
                  <div class="form-group">
                     <label for="inputEmail3" class="col-sm-3 control-label">Task Name: <span class="compulsary">*</span></label>
                     <div class="col-sm-8">
                        <select class="form-control" id="act_activity_name"  name="act_activity_name" required="" data-parsley-required-message="Task name is required" data-parsley-trigger="change focusout">
                           <option value="">Select a Task</option>
                           @foreach($activities as $row)
                           <option value="{{$row->activity_id}}" @if($row->activity_id == $activity_name) selected="selected" @endif>{{$row->activity_name}}</option>
                           @endforeach  
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="act_due_date" class="col-sm-3 control-label">{{ trans('language.activity_due_date') }}: </label>
                     <div class="col-sm-8">
                        <div class="input-group">
                           <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                           </div>
                           <input type="text" class="form-control" id="act_due_date" name="act_due_date" placeholder="YYYY-MM-DD" value="{{$due_date}}">
                        </div>
                     </div>
                  </div>
                
                  <div class="form-group">
                     <label for="new_object_id" class="col-sm-3 control-label">{{ trans('language.responsible_user') }}: </label>
                     <div class="col-sm-8">
                        <select class="form-control" id="act_assigned_to"  name="act_assigned_to" >
                           <option value="">{{ trans('language.select_user') }}</option>
                           @foreach($user as $row)
                           <option value="{{$row->id}}" @if($row->id == $assigned_to) selected="selected" @endif>{{$row->user_full_name}}</option>
                           @endforeach  
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="activity_note" class="col-sm-3 control-label">Task Note: </label>
                     <div class="col-sm-8">
                        <textarea class="form-control" name="act_activity_note" id="act_activity_note" rows="3">{{$activity_note}}</textarea> 
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="col-sm-3"></div>
                     <div class="col-sm-8">
                        <button type="submit" class="btn btn-primary">{{ trans('language.save') }}</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('language.cancel') }}</button>

                        <div  style="margin-top: 5px; display: inline-block;">
                <i class="fa fa-spinner fa-pulse fa-fw fa-lg pre_loader"></i>
                <span class="sr-only">Loading...</span>
            </div>
                     </div>
                  </div>
                  </form>
               </div>
               
      <script>
      $(function ($) {
$('.alert_modal').html('');
$('.pre_loader').hide(); 
$("#workflows_activity_save").submit(function(e) {

   $('.alert_modal').html('');
   if($("#workflows_activity_save").parsley().validate())
   { 
   var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
   var url = "@php echo url('workflows_activity_save'); @endphp";
   var activity_name_label = $("#act_activity_name option:selected").text();
   var postdata = {_token:CSRF_TOKEN,"workflow_id":$("#act_workflow_id").val(),"activity_id":$("#act_activity_id").val(),"process_id":$("#act_process_id").val(),"stage_id":$("#act_stage_id").val(),"activity_name":$("#act_activity_name").val(),"activity_name_label":activity_name_label,"activity_due_date":$("#act_due_date").val(),"assigned_to":$("#act_assigned_to").val(),"activity_note":$("#act_activity_note").val()}
   
   $('.pre_loader').show();
   $.ajax({
          type: "POST",
          url: url,
          dataType:'json',
          data: postdata, /*serializes the form's elements.*/
          success: function(response)
          {
              $('.pre_loader').hide(); 
              if(response.status == 1)
              {
               //show_work_flow(response);

               $('#add_to_wf_activity_model').modal('hide');
               if(response.url != '')
               {
                window.location.href= response.url;
               }
              }
              else if(response.status == 0)
              {
                //$('.alert_modal').html(response.message);
              }
              
          }
        });
   }
   e.preventDefault(); /*avoid to execute the actual submit of the form.*/
   });

         $('#act_due_date').daterangepicker({
           singleDatePicker: true,
           "drops": "down",
            format: 'YYYY-MM-DD',
           showDropdowns: true
       });

         

   });     
      </script>