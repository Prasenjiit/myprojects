<?php include (public_path()."/storage/includes/lang1.en.php" ); ?>
{!! Html::script('js/parsley.min.js') !!}    

               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">{{$language["add_new_obj"]}}</h4>
               </div>
               <div class="modal-body">
                  {!! Form::open(array('url'=> array('add_to_workflow'), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'add_to_workflow_form_modal', 'id'=> 'add_to_workflow_form_modal','data-parsley-validate'=> '')) !!}  
                 
                  <div class="form-group">
                     <div class="col-sm-12 alert_modal">
                       
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="form_workflow_id" class="col-sm-3 control-label">{{$language['workflow']}}: <span class="compulsary">*</span></label>
                     <div class="col-sm-8">
                        <select name="form_workflow_id" id="form_workflow_id" class="form-control">
                  <option value="0">{{ $language['select_workflow'] }}</option>
                  @foreach ($workflows as $wf_row)  
                  <option value="{{$wf_row->workflow_id}}" @if($wf_row->workflow_id == $workflow_id) selected="selected" @endif>{{$wf_row->workflow_name}}</option>
                  @endforeach
               </select>
                     </div>
                  </div>

                  <div class="form-group">
                     <label for="inputEmail3" class="col-sm-3 control-label">{{ $language['workflow_type'] }}: <span class="compulsary">*</span></label>
                     <div class="col-sm-8">
                        <select class="form-control" id="form_object_type"  name="form_object_type" required="" data-parsley-required-message="Types is required" data-parsley-trigger="change focusout">
                           <option value="document" @if($object_type == 'document') selected="selected" @endif>{{$language['document']}}</option>
                           <option value="form" @if($object_type == 'form') selected="selected" @endif>{{$language['form']}}</option>
                        </select>
                     </div>
                  </div>

                  <div class="form-group">
                  <input type="hidden" class="form_object_id" autocomplete="off" id="form_object_id" name="form_object_id" value="" >
                     <label for="new_object_id" class="col-sm-3 control-label form_object_type_label"></label>
                     <div class="col-sm-8">
                     <div class="input-group input-group-sm">
                <input type="text" class="form-control form_object_name" autocomplete="off" id="form_object_name" name="form_object_name" value="" readonly="readonly" required="" data-parsley-errors-container=".parsly_error_object">

                  @php $random = str_random(5); @endphp
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-info btn-flat search_object{{$random}}"><i class="fa fa-fw fa-search-plus"></i></button>
                    </span>
              </div>
              <label class="parsly_error_object"></label>
                     </div>
                  </div>
                 
                  <div class="form-group">
                     <label for="inputEmail3" class="col-sm-3 control-label">{{ $language['workflow_stage'] }}: <span class="compulsary">*</span></label>
                     <div class="col-sm-8">
                        <select class="form-control workflow_stages_list" id="doc_stage_id"  name="doc_stage_id" required="" data-parsley-required-message="Workflow Stage is required" data-parsley-trigger="change focusout">
                        <option value="">{{ $language['select_workflow_stage'] }}</option>
                           @foreach($wf_stages as $row)
                           <option value="{{$row->workflow_stage_id}}" class="wf_all wf_option_{{$row->workflow_id}}" style="display: none;">{{$row->workflow_stage_name}}</option>
                           @endforeach  
                           </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="inputEmail3" class="col-sm-3 control-label">{{ $language['activity_name'] }}: <span class="compulsary">*</span></label>
                     <div class="col-sm-8">
                        <select class="form-control" id="doc_activity_name"  name="doc_activity_name" required="" data-parsley-required-message="{{$language['activity_name_required']}}" data-parsley-trigger="change focusout">
                           <option value="">{{ $language['select_activity'] }}</option>
                           @foreach($activities as $row)
                           <option value="{{$row->activity_id}}">{{$row->activity_name}}</option>
                           @endforeach  
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="activity_due_date" class="col-sm-3 control-label">{{ $language['activity_due_date'] }}:</label>
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
                     <label for="new_object_id" class="col-sm-3 control-label">{{ $language['responsible_user'] }}:</label>
                     <div class="col-sm-8">
                        <select class="form-control" id="doc_assigned_to"  name="doc_assigned_to" >
                           <option value="">{{ $language['select_user'] }}</option>
                           @foreach($user as $row)
                           <option value="{{$row->username}}">{{$row->user_full_name}}</option>
                           @endforeach  
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="activity_note" class="col-sm-3 control-label">{{ $language['activity_note'] }}:</label>
                     <div class="col-sm-8">
                        <textarea class="form-control" name="doc_activity_note" id="doc_activity_note" rows="3"></textarea> 
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="col-sm-3"></div>
                     <div class="col-sm-8">
                        <button type="submit" class="btn btn-primary">{{ $language['save'] }}</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ $language['cancel'] }}</button>
                     </div>
                  </div>
                  </form>
               </div>
               
      <script>
      $(function ($) {
$('.alert_modal').html('');
var change_form_label = function() {
    var form_object_type = $('#form_object_type').val();
    if(form_object_type == 'form')
    {
      var form_object_type_label = "{{ $language['form'] }}: ";
    }
    else
    {
      var form_object_type_label = "{{ $language['document'] }}: ";
    }
   $('.form_object_type_label').html(form_object_type_label+'<span class="compulsary">*</span>');
};


var change_stage_options = function() {
    var workflow_id = $('#form_workflow_id').val(); 
    $('#doc_stage_id').val('');

    $('.wf_all').each(function (index, value) { 
         $(this).hide(); 
   });
    $('.wf_option_'+workflow_id).each(function (index, value) { 
         $(this).show(); 
   });
    
};

         $(document).on("change","#form_object_type",function(e) 
         {
           console.log('hiss');
           change_form_label();
           });  
        var random = "@php echo $random; @endphp"; 
       $(document).on("click",".search_object"+random,function(e) {
   $('.modal-container-child').empty();     
   var workflow_id=encodeURI($('#workflow_id').val());
   var object_type=encodeURI($('#form_object_type').val());
   $('.modal-container-child').load("@php echo url('search_object_to_workflow_modal') @endphp?workflow_id="+workflow_id+"&object_type="+object_type,function(result){
    console.log("search_object_model open");
         $('#search_object_model').modal({
                     show: 'show',
                     backdrop: false
               }); 
         });
   });
       change_form_label();

       $('#search_object_model').on('hidden.bs.modal', function () {
         $('.modal-container-child').empty(); 
      
        });

        $(document).on("click",".close_modal",function(e) 
         {
           var modalid= $(this).attr('data-modalid');
           $(modalid).modal('hide');
           console.log("Ok"+modalid);
           }); 

        $("#add_to_workflow_form_modal").submit(function(e) {

      $('#workflow_doc_id').val("");  
    $('.workflow_doc_id').each(function (index, value) { 
         $(this).val(''); 
   });
   $('.alert_modal').html('');
   var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
   var url = "@php echo url('add_to_workflow'); @endphp";
   var postdata = {_token:CSRF_TOKEN,"wf_id":$("#form_workflow_id").val(),"stage_id":$("#doc_stage_id").val(),"object_id":$("#form_object_id").val(),"object_type":$("#form_object_type").val(),"activity_name":$("#doc_activity_name").val(),"activity_due_date":$("#doc_activity_due_date").val(),"assigned_to":$("#doc_assigned_to").val(),"activity_note":$("#doc_activity_note").val()}
   $.ajax({
          type: "POST",
          url: url,
          dataType:'json',
          data: postdata, /*serializes the form's elements.*/
          success: function(response)
          {
               /*show response from the php script.*/
   //alert_space").html(response.message); 
              if(response.status == 1)
              {
               //show_work_flow(response);

               $('#add_to_workflow_modal_new').modal('hide');
              }
              else if(response.status == 0)
              {
                $('.alert_modal').html(response.message);
              }
              
          }
        });
   
   e.preventDefault(); /*avoid to execute the actual submit of the form.*/
   });

         $('#doc_activity_due_date').daterangepicker({
           singleDatePicker: true,
           "drops": "down",
            format: 'YYYY-MM-DD',
           showDropdowns: true
       });

         change_stage_options();
         $(document).on("change","#form_workflow_id",function(e) 
         {
           change_stage_options();
           });
   });     
      </script>