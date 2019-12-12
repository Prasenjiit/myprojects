<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
{!! Html::style('css/fastselect.min.css') !!} 
{!! Html::style('plugins/datatables_new/fixedHeader.dataTables.min.css') !!}
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}


<?php
    $user_id = Auth::user()->id;
    $dept_id = Auth::user()->department_id;
    $user_role = Auth::user()->user_role;
    $form_permission = Auth::user()->user_form_permission;
?>
<style type="text/css">
    .fstElement {
    height: AUTO !important;
}

</style>
<section class="content-header">
  <div class="col-sm-12">
    <span >
      <strong>{{trans('forms.form_permission')}}</strong> - {{ $form_data->form_name }}
    </span>
   <span>
      <a href="{{URL::route('forms')}}">
          <button class="btn btn-info btn-flat newbtn" style="line-height:13px !important;">{{trans('language.back')}}</button>
      </a>
    </span>
  </div>
  
</section>
<section class="content" id="shw">
<div class="row">
@if(Session::has('flash_message_success'))
<section class="content content-sty" id="spl-wrn">        
    <div class="alert alert-success {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('flash_message_success') }}</div>        
</section>
@endif
@if($user_role == 1)
<div class="col-xs-12">
<div class="box box-info">
             {!! Form::open(array('url'=> array('form_permissionSave/'.$form_id), 'method'=> 'post', 'name'=> 'addForm', 'id'=> 'form_permissionSave','data-parsley-validate'=> '')) !!}
            <div class="box-body" >
                <table border="1" id="" class="table table-bordered table-striped dataTable hover">
                    <thead>
                        <tr>                   
                                    <th>Form Field</th> 
                                    <th>View Permission</th>
                                    <th>Edit Permission</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach($form_fields as $r)
                        <tr>

                            <td>@php if($r->form_input_title){ echo $r->form_input_title; } else if($r->form_input_type_name){ echo $r->form_input_type_name; } @endphp</td>
                            <td>
                                @php 
                                $view_permission_users = ($r->view_permission_users)?explode(',',$r->view_permission_users):array();

                                 $edit_permission_users = ($r->edit_permission_users)?explode(',',$r->edit_permission_users):array();

                                @endphp
                               <select name="view_permission_users_{{$r->form_input_id}}[]" id="" class="multipleSelect form-control" multiple >
                                    <?php
                                    foreach ($users as $key => $user) {
                                    $selected = (in_array($user->id,$view_permission_users))?"selected":'';    
                                    ?>
                                    <option value="<?php echo $user->id; ?>" {{ $selected }}><?php echo $user->user_full_name;?></option>
                                    <?php
                                    }
                                    ?>
                                </select>  
                            </td>
                           
                            <td>
                               <select name="edit_permission_users_{{$r->form_input_id}}[]" id="" class="multipleSelect form-control" multiple >
                                    <?php
                                    foreach ($users as $key => $user) {
                                    $selected = (in_array($user->id,$edit_permission_users))?"selected":'';    
                                    ?>
                                    <option value="<?php echo $user->id; ?>" {{ $selected }}><?php echo $user->user_full_name;?></option>
                                    <?php
                                    }
                                    ?>
                                </select>    
                            </td>
                        </tr>
                        @endforeach
                    </tbody>                    
                </table>
            </div><!-- /.box-body -->

            <div class="box-footer">
                <button type="submit" id="save_form" class="btn btn-primary save_dy_form" value="save" style="float: left; margin-left: 5px;">Save</button>
                <a href="{{url('forms')}}" name="btn_cancel" id="btn_cancel" class="btn btn-danger btn_cancel" style="float: left; margin-left: 5px;">Cancel</a>
                <div class="preloader" style="float: left; margin-top: 5px; display: none;" >
                    <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>
                    <span class="sr-only">Loading...</span>
                </div>

              </div>
        </div><!-- /.box -->
         {!! Form::close() !!}
    </div>
     @else
        <section class="content content-sty">        
            <div class="alert alert-sty alert-error">{{trans('language.dont_hav_permission')}}</div>        
        </section>
    @endif
</div>
</section>
    <div class="modal fade" id="viewmoreModal" data-backdrop="true" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div id="more"></div>
    </div>



      <div class="modal fade" id="form_response_modal">
         <div class="modal-dialog">
            <div class="modal-content form_response_remote">
               
            <div class="modal-footer"></div>
            </div>
            <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->


      <div class="modal fade" id="modal-default">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Default Modal</h4>
              </div>
              <div class="modal-body">
                <p>One fine body&hellip;</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        {!! Html::script('js/fastselect.standalone.js') !!} 
<script type="text/javascript">
$(document).ready(function() {    
$('.multipleSelect').fastselect();
setTimeout(function() {
    $('#spl-wrn').fadeOut('fast');
}, 5000); // <-- time in milliseconds
 }); 

</script>
@endsection