<style type="text/css">
    .form_view_tr,.form_edit_tr
    {
       /*display: none;*/
    }
    #sjfb-sample .required-field > label:after {
  content: " *";
  color: red;
  font-size: 90%;
}
</style>
{!! Html::style('css/dropzone.min.css') !!}
{!! Html::script('js/dropzone.js') !!}  
<div class="modal-header" id="form_values_add_header">
  
  @php
   $from_user_full_name= ($form_from_user)?$form_from_user->user_full_name:'';
        $to_user_full_name= ($form_to_user)?$form_to_user->user_full_name:'';
        $action_user_full_name= ($form_action_user)?$form_action_user->user_full_name:'';

        @endphp
  <h4 class="modal-title">{{ucfirst(@$form->form_name)}} - {{@$from_user_full_name}} - {{@$form_details[0]->created_at}}</h4>
</div>


<div class="modal-body form_values_show" id="">
  {!! Form::open(array('url'=> array(''), 'method'=> 'post', 'class'=> '', 'name'=> 'form_response_action_form', 'id'=> 'form_response_action_form','data-parsley-validate'=> '')) !!} 
  <div class="row">
  <div class="col-sm-12 alert_form"></div>
  <div class="col-sm-12">

    <table border="1" id="documentGroupDT" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
        <thead>
            <tr>
                <th colspan="2">Form Information
                <?php 
                $edit = true;
                if(Auth::user()->user_role==1 || Auth::user()->user_role==2){
                
                 if(($reject_id) && $reject_id == $response_activity_id) 
                      { 
                          $edit=true;
                      }

                 ?>
                
                <?php }
                else{ 
                  if(@$form_privilages->privilege_key=="edit")
                  {
                    
                    if(in_array(Auth::user()->id, $form_owner) && ($reject_id) && $reject_id == $response_activity_id) 
                      { 
                          $edit=true;
                      }                                         
                  }
              }
              
              

              if($edit)
              {
              ?>
              <!-- edit function disabled for resubmit  -->

              <!-- <a id="editform1" class="editform pull-right" form_id='{{@$form_response_form_id}}' form_unique_id = "{{@$form_response_unique_id}}" style="cursor: pointer;color: white;" title="Edit form values" onclick="myFunction1('{{$form_response_form_id}}','{{$form_response_unique_id}}','edit')"><i class="fa fa-pencil" style="cursor:pointer;"></i>
              </a> -->

              <!-- resubmit form -->
              
              <a id="resubmitform" class="editform pull-right" form_id='{{@$form_response_form_id}}' form_unique_id = "{{@$form_response_unique_id}}" style="cursor: pointer;color: white;display: none;" title="Resubmit Form" onclick="myFunction1('{{$form_response_form_id}}','{{$form_response_unique_id}}','resubmit')"><i class="fa fa-newspaper-o" style="cursor:pointer;"></i>
              </a>
                <?php } ?>
                </th>
            </tr>
        </thead>
        <tbody>
        <tr>
            <td width="25%" nowrap="nowrap">Form:</td>  
            <td>{{ucfirst(@$form->form_name)}}</td> 
            </tr>
            <tr>
            <td>Form Description:</td>  
            <td>{{ucfirst(@$form->form_description)}}</td>  
            </tr>

        @foreach($form_details as $dtc)            
            <tr>
                <td>{{ ucfirst($dtc->form_input_title) }}:</td>
                <td>
                  @php 
                  $array_files = array();
                  $array_rand_files = array();
                  $array_size_files = array();
                        if($dtc->is_options)
                        {
                          $form_response_value = '';
                          $form_response_value_array = ($dtc->form_response_value)?unserialize($dtc->form_response_value):array();
                          foreach ($form_response_value_array as $key => $value) {
                            if($value['sel'])
                            {
                              $form_response_value = $form_response_value.''.$value['label'].', ';
                            }
                          }
                          echo trim(ucfirst($form_response_value),", ");//last comma trim
                        }
                        else{

                          $form_response_value = $dtc->form_response_value;
                          $form_response_file = $dtc->document_file_name;
                          $form_response_size = $dtc->form_response_file_size;
                          if($form_response_value == 'on')
                          {
                            echo '<i class="fa fa-fw fa-check-square-o"></i>';
                          }
    
                          if($dtc->form_input_type_name == 'File')
                          {
                          
                            $array_files = ($form_response_value)?explode(',',$form_response_value):array();
                            foreach($array_files as $k => $v)
                            {
                              $original_file = $v;
                              $rand_file = (isset($array_rand_files[$k]))?$array_rand_files[$k]:'';
                              $size_file = (isset($array_size_files[$k]))?$array_size_files[$k]:'';
                              @endphp
                              <span style="cursor:pointer;" data-file="{{ $rand_file }}" class="view_doc">{{ $original_file }}</span>
                              @php
                            }
                            
                          }
                          else
                          {
                            echo ucfirst($form_response_value);
                          }
                        }
                       @endphp


                </td>
            </tr>
        @endforeach
        </tbody>
        <thead>
            <tr>
                <th colspan="2">Additional Details</th>
            </tr>
        </thead>
        <tbody> 
        @php
       
        $from_user_full_name= ($form_from_user)?$form_from_user->user_full_name:'';
        $to_user_full_name= ($form_to_user)?$form_to_user->user_full_name:'';
        $action_user_full_name= ($form_action_user)?$form_action_user->user_full_name:'';
        @endphp
            <tr>
            <td width="25%" nowrap="nowrap">Submitted By:</td>  
            <td>{{ $from_user_full_name }}</td> 
            </tr>
            <!-- <tr>
            <td>Assigned To:</td> 
            <td>{{ $to_user_full_name }}</td> 
            </tr> -->
            <tr>
            <td>Submitted At:</td>  
            <td>{{@$dtc->created_at}}</td>

            </tr>
            
            
            </tbody> 
   @php
        if(isset($dtc))
        {
        $response_activity_id= $dtc->response_activity_id;
        $response_activity_note= $dtc->response_activity_note;
        $action= (in_array($dtc->form_assigned_to,$form_assigned_to) && !$dtc->response_activity_id)?true:false;
        $owner= (in_array($dtc->form_assigned_to,$form_assigned_to))?true:false;
        $response_activity_date= ($dtc->response_activity_date)?$dtc->response_activity_date:'';
        
        @endphp
       
        
        <tbody> <tr class="form_view_tr">
            <td width="25%" nowrap="nowrap">Status:</td>  
            <td>
              @php 
              $activity_name = $dtc->response_activity_name;
              foreach($form_activity as $fa)  
                  if($fa->activity_id == $dtc->response_activity_id)
                  {
                    $activity_name = $fa->activity_name;
                  }
                  echo ($activity_name)?$activity_name:'';
                @endphp
            </td> 
            </tr>
            <tr class="form_view_tr">
            <td>Remark:</td>  
            <td>
              @php 
              $response_activity_note = $dtc->response_activity_note;
              
                echo ($response_activity_note)?$response_activity_note:'';
                @endphp
            </td> 
            </tr>

            <tr class="form_view_tr">
            <td>Action By:</td>  
            <td>
               @php 
               
               
                echo ($action_user_full_name)?$action_user_full_name:'';
                @endphp
            </td> 
            </tr>
            <tr class="form_view_tr">
            <td>Date:</td>  
            <td>
               @php 
               
               
                echo ($response_activity_date)?$response_activity_date:'';
                @endphp
            </td> 
            </tr>
            
            </tbody> 
            @php } @endphp
    </table>
   </div>
   </div>
   {!! Form::close() !!}
</div>
<!-- Edit Section START-->
<form id="sjfb-sample" method="POST" action="{{url('saveFormValues')}}">
      {!! csrf_field() !!}
<div class="modal-body form_values_edit" id="">
  <div id="sjfb-wrap">
    
        
        <input type="hidden" name="form_response_unique_id" id="form_response_unique_id" value="{{ $form_details[0]->form_response_unique_id }}">
        <input type="hidden" name="form_id" id="form_id" value="{{$form_response_form_id}}">
        <input type="hidden" name="form_name" id="form_name" value="{{@$form->form_name}}">
        <input type="hidden" name="form_reject_status" id="form_reject_status" value="{{@$status}}">
        <div id="sjfb-fields">
        </div>
      <div id='div-append'>
      </div>
    
  </div>
</div>
<!-- Edit Section -->

<div class="modal-footer" id="">
  <div class="col-sm-12">
  <input type="hidden" name="form_response_unique_id" id="form_response_unique_id" value="{{$form_response_unique_id}}">
  <input type="hidden" name="form_response_form_id" id="form_response_form_id" value="{{$form_response_form_id}}">

  <input type="submit" value="Save" class="btn btn-primary form_values_edit" id="edit_form">
  <button type="button" class="btn btn-danger" data-dismiss="modal" >Close</button>
  </div>
</div>
</form>
<script type="text/javascript">
var url_delete = "<?php echo URL('deleteAttached');?>";
var url_form_attach = "<?php echo URL('formAttachments');?>";
$('.form_values_edit').hide();
$('.form_values_show').show();
$(document).ready(function(){

});

function myFunction1(formID,form_unique_id,action)
{
  $('#div-append').html('');
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var loadformurl = "{{ url('load_form')}}";
        generateFormEdit(formID,loadformurl,action,form_unique_id,url_delete,url_form_attach);
        $('.form_values_edit').show();
        $('.form_values_show').hide();
}
</script>          