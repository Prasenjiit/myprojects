<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
{!! Html::style('plugins/datatables_new/fixedHeader.dataTables.min.css') !!}
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}
{!! Html::style('plugins/datatables_new/colReorder.dataTables.min.css') !!}
{!! Html::script('js/parsley.min.js') !!}

<?php
    $user_id = Auth::user()->id;
    $dept_id = Auth::user()->department_id;
    $user_role = Auth::user()->user_role;
    $form_permission = Auth::user()->user_form_permission;
?>
<section class="content-header">
  <div class="col-xs-8">
    <span style="float:left;">
      <strong>{{trans('forms.forms')}}</strong> &nbsp;
    </span>
    <!-- super admin can only add forms -->
    @if(stristr($form_permission,"add"))
    <span style="float:left;">
      <a href="{{URL::route('form')}}">
          <button class="btn btn-block btn-info btn-flat newbtn">{{trans('language.add new')}}  <i class="fa fa-plus"></i></button>
      </a>
    </span>
    @endif
    <!-- end super admin can only add forms -->
  </div>
  <div class="col-xs-4">
   <!--  <ol class="breadcrumb">
        <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
        <li class="active">{{trans('forms.forms')}}</li>
    </ol> -->
  </div>
</section>
<section class="content" id="shw">
<div class="row">
    @if(Session::has('flash_message_success'))
    <section class="content content-sty" id="spl-wrn">        
        <div class="alert alert-sty {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('flash_message_success') }}</div>        
    </section>
    @endif
    @if(Session::has('flash_message_warning'))
    <section class="content content-sty" id="spl_warn">        
        <div class="alert alert-warning alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-warning"></i> Alert!</h4>
        {{Session::get('flash_message_warning')}}
        {{Session::forget('flash_message_warning')}}
      </div>
    </section>
    @endif
    <!--Checking module permission-->
@if(Session::get('enbval4')==Session::get('tval'))     
        @if(stristr($form_permission,"view"))
        <div class="col-xs-12">
            <div class="box box-info">
                <div class="box-body" style="overflow-x: auto; overflow-y: auto;">
                    <table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable hover" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('forms.form_name')}}</th>
                                <th>{{trans('language.description')}}</th> 
                                <th>{{trans('language.created by')}}</th>                           
                                <th>{{trans('language.created date')}}</th>                         
                                <th>{{trans('language.actions')}}</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td><input type="text" id="form_column" placeholder="Search Forms"></td>
                                <td></td>
                                <!-- <td></td> -->
                                <!-- <td></td> -->
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                        <tbody>

                            @foreach ($dglist as $key => $val)
                            <tr class="even" role="row">                                
                                <td>
                                    <a @if(Auth::user()->user_role == 1) href="{{URL::route('form',$val->form_id)}}" style="cursor: pointer;" @endif id="editform">
                                        {{ $val->form_name }}
                                    </a>
                                </td>
                                <td>{{ $val->form_description }}</td>
                                <td>@foreach($val->created_by as $user)
                                {{@ucfirst($user->user_full_name)}}
                                @endforeach
                                </td>
                                <td nowrap="nowrap">{{ $val->created_at }}</td>
                                <td nowrap="nowrap">
                                <a title="View Submited Forms" id="details" href="{{URL::route('form_details',$val->form_id)}}">
                                        <i class="fa  fa-list-ol" style="cursor:pointer;"></i>
                                </a> 
                                &nbsp;
                                    <?php 
                                        if($user_role==1 || $user_role==2){ ?>
                                            <a id="viewform" class="viewform" form_id='{{$val->form_id}}' style="cursor: pointer;" data-toggle="modal" data-target="#view_form" title="Submit this Form">
                                                <i class="fa fa-newspaper-o" style="cursor:pointer;"></i>
                                            </a>
                                        <?php }else{ 
                                            if(@$val->form_privileges[3]->privilege_key=="add"){
                                                $uservalue = @$val->form_privileges[3]->privilege_value_user; 
                                                $deptvalue = @$val->form_privileges[3]->privilege_value_department;
                                                $key_user_value = explode(',',$uservalue);
                                                $key_dept_value = explode(',',$deptvalue); 
                                                $depart         = explode(',',$dept_id); 
                                                $intersection   = array_intersect($depart,$key_dept_value);                                     
                                                if(in_array($user_id, $key_user_value) || count($intersection)>0) { ?>
                                                    <a id="viewform" class="viewform" form_id='{{$val->form_id}}' style="cursor: pointer;" data-toggle="modal" data-target="#view_form" title="Submit this Form">
                                                        <i class="fa fa-newspaper-o" style="cursor:pointer;"></i>
                                                    </a>
                                                <?php }                                            
                                            }
                                        } 
                                    ?>
                                    &nbsp;
                                     @if(Auth::user()->user_role == 1) 
                                     <a title="permission" id="details" href="{{URL::route('form_permission',$val->form_id)}}">
                                    <i class="fa  fa-wrench" style="cursor:pointer;"></i>
                                </a> 
                                
                                @endif
                                    
                                <!-- super admin can only edit,delete forms -->
                                @if(stristr($form_permission,"edit"))
                                    &nbsp;
                                    <a title="Edit this Form" id="document-type-edit" href="{{URL::route('form',$val->form_id)}}">
                                        <i class="fa fa-pencil" style="cursor:pointer;"></i>
                                    </a> 
                                @endif
                                @if(stristr($form_permission,"delete"))
                                    &nbsp;
                                    <i class="fa fa-trash" onclick="del({{ $val->form_id }}, '{{ $val->form_name }}')" title="Delete this Form" style="color: red; cursor:pointer;"></i>                           
                                @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>                    
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
        @else
            <section class="content content-sty">        
                <div class="alert alert-sty alert-error">{{trans('language.dont_hav_permission')}}</div>        
            </section>
        @endif
    @elseif(Session::get('enbva4')==Session::get('fval'))
        <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{trans('language.purchase_now')}}</div></section>
    @else
        <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{trans('language.module_expired')}}</div></section>
    @endif
</div>
</section>
<div class="modal fade" id="view_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div id="content_form"></div>
    </div>
</div>

{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
{!! Html::script('plugins/datatables_new/dataTbles.fixedHeader.min.js') !!}
<script type="text/javascript">
$('#spl-wrn').delay(5000).slideUp('slow');
//$('#spl_warn').delay(8000).slideUp('slow');
$(document).ready(function() {
    var rows_per_page = '{{Session('settings_rows_per_page')}}';
    var lengthMenu = getLengthMenu();//Function in app.blade.php
    var table = $('#documentTypeDT').DataTable({  
            "lengthMenu": lengthMenu,
            "pageLength":rows_per_page,            
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "fixedHeader": true,
            bJQueryUI: true,
            "autoWidth": false,
            "order": [[ 3, "desc" ]],
            language: {
                searchPlaceholder: "{{trans('language.data_tbl_search_placeholder')}}"
            },
            columnDefs: [ { orderable: false, targets: [4] } ]
        });
        //for highlight the row
    $('#documentTypeDT tbody').on('click', 'tr', function () {
        $("#documentTypeDT tbody tr").removeClass('row_selected');        
        $(this).addClass('row_selected');
    });
        $('#form_column').on('keyup', function(){
            table
            .column(0)
            .search(this.value)
            .draw();
        });
} );
// view form
    $('.viewform').click(function(){
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var formID = $(this).attr('form_id');
        $.ajax({
            type: 'post',
            url: '{{URL('viewform')}}',
            data: {_token: CSRF_TOKEN,formid:formID},
            timeout: 50000,
            beforeSend: function() {
              $("#bs").show();
            },
            success: function(data)
            {
                $("#content_form").html(data);
            }
        });

    });
//form delete
    function del(formID,name)
    {   
    swal({
        title: "{{trans('language.confirm_delete_single')}}'" + name + "' ?",
        text: "{{trans('language.Swal_not_revert')}}",
        type: "{{trans('language.Swal_warning')}}",
        showCancelButton: true
      }).then(function (result) {
          if(result){
              var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
              $.ajax({
                  type: 'post',
                  url: '{{URL('deleteform')}}',
                  data: {_token: CSRF_TOKEN,formid:formID,form_name:name},
                  beforeSend: function() {
                      $("#bs").show();
                  },
                  success: function(data, status){ 
                    swal({
                      title: data,
                      showCancelButton: false
                    }).then(function (result) {
                        window.location.reload();
                  });
                  },
                  complete: function(data) {
                      $("#bs").hide();
                  }
              });

        }
    });
  }
</script>
@endsection