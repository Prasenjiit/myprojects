<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
{!! Html::style('plugins/datatables_new/fixedHeader.dataTables.min.css') !!}
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}
<style type="text/css">
#labeldoc2
{
  margin-left: 15px;
  margin-top: 4px;
}
.box-body {
  padding-top: 0px !important;
  }
.box-header {
  padding-bottom: 0px !important;
}
</style>

<?php
    $user_id = Auth::user()->id;
    $dept_id = Auth::user()->department_id;
    $user_role = Auth::user()->user_role;
    $form_permission = Auth::user()->user_form_permission;
?>

<section class="content-header">
  <div class="col-sm-2">
    <span style="float:left;">
      <strong>{{trans('forms.forms')}}</strong> &nbsp;
    </span>
    <!-- super admin can only add forms -->
    @if(Auth::user()->user_role == 1)
    @if (Session::get('module_activation_key4')==1)   
    @if(date("Y-m-d") > Session::get('module_expiry_date4')) 
    @else 
    <span style="float:left;">
      <a href="{{URL::route('form')}}">
          <button class="btn btn-block btn-info btn-flat newbtn">{{trans('language.add new')}}  <i class="fa fa-plus"></i></button>
      </a>
    </span>
    @endif
    @endif
    @endif
    </div>
    <div class="col-sm-6">
    <span style="color: #737373;">All the forms submitted by me and submitted to me listed here</span>
  </div>
  <div class="col-sm-4">
    <!-- <ol class="breadcrumb">
        <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
        <li class="active">{{trans('forms.forms')}}</li>
    </ol> -->
  </div>
</section>
<section class="content" id="shw">
<div class="row">
          <!--Checking module permission-->
@if(Session::get('enbval4')==Session::get('tval'))   
@if(Session::has('flash_message_success'))
<section class="content content-sty" id="spl-wrn">        
    <div class="alert alert-sty {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('flash_message_success') }}</div>        
</section>
@endif
@if(stristr($form_permission,"view"))
<div class="col-xs-12">
<div class="box box-info">
            <div class="box-header"> 
                <div class="row">   
                <div class="col-md-3"></div>     
                    <div class="col-md-1">
                    <label class="control-label" id="labeldoc2">{{trans('language.filter')}} :</label>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" id="filter" name="filter">
                            <option value="0" selected="selected">
                                {{trans('forms.show_all_forms')}}
                            </option>
                            <option value="1"> 
                                {{trans('forms.form_submitted_by_me')}}    
                            </option> 
                            <option value="2"> 
                                {{trans('forms.form_submitted_to_me')}}
                            </option> 
                        </select>
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>
            </div>
            <div class="box-body" style="overflow-x: auto; overflow-y: auto;">
                <table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable hover" width="100%">
                    <thead>
                        <tr>
                        <th>Form Name</th> 
                        <th>Submitted By</th>                    
                        <th>Submitted</th> 
                        <th>Workflow</th>
                        <th>Stage</th>
                        <th>Status</th>  
                        <th>Updated</th>                      
                        <th class="no-sort">{{trans('language.actions')}}</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td><input type="text" id="form_column_name" placeholder="Search Form Name"></td>
                            <td><input type="text" id="form_column_from" placeholder="Search Submitted By"></td>
                            <td></td>
                            <td><input type="text" id="form_wf" placeholder="Search Workflow"></td>
                            <td><input type="text" id="form_state" placeholder="Search Stage"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                    <tbody>
                        
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

     @elseif(Session::get('enbval4')==Session::get('fval'))
        <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{trans('language.purchase_now')}}</div></section>
    @else
        <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{trans('language.module_expired')}}</div></section>
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
{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
{!! Html::script('plugins/datatables_new/dataTables.fixedHeader.min.js') !!}
{!! Html::script('plugins/simple-jquery-form-builder/js/sjfb-html-generator-edit.js') !!}
<script type="text/javascript">
// ajax data table
var selected = [];
// var typeselect =   function()
//     {   
//      var obj = $('#typeselect').val();
//     return obj;
//     };
var filter =   function()
    {   
     var obj = $('#filter').val();
    return obj;
    };
//datatable
var rows_per_page = '{{Session('settings_rows_per_page')}}';
var lengthMenu = getLengthMenu();//Function in app.blade.php
var datatable =   $('#documentTypeDT').DataTable( { 
              "fnInitComplete": function() {
        $('#documentTypeDT tbody tr').each(function(){
                // $(this).find('td:eq(2)').attr('nowrap', 'nowrap');
                // $(this).find('td:eq(5)').attr('nowrap', 'nowrap');
        });
    },
                            "lengthMenu": lengthMenu,
                            "pageLength":rows_per_page,       
                            "processing": true, 
                            "serverSide": true,
                            "fixedHeader": true,
                            "order": [[6,"desc"]],
                            "scrollX": true,
                            "scrollY": 350,
                            "ajax": {
                               "url": "@php echo url('my_forms_filter'); @endphp",
                               "type": "POST",
                                "headers": { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                               "data":function(d){
                                        //d.typeselect = typeselect();
                                        d.filter = filter();
                                     }
                                },
                            "iDisplayLength": 25,
                            "columns": [
                                        {
                                            "class":"0",
                                            "data":"form_name",
                                        },
                                        {
                                            "class":"1",
                                            "data":"from",
                                        },
                                        {
                                            "class":"2",
                                            "data":"created",
                                        },
                                        {
                                            "class":"3",
                                            "data":"wf",
                                        },
                                        {
                                            "class":"4",
                                            "data":"state",
                                        },
                                        {
                                            "class":"5",
                                            "data":"status",
                                        },
                                        {
                                            "class":"6",
                                            "data":"updated",
                                        },
                                        {
                                            "class":"7",
                                            "data":"actions",
                                        }
                                    ],
                              "columnDefs": [
                                  { targets: 'no-sort', orderable: false }
                                ],          
                             "language": { processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> ',"searchPlaceholder": "{{trans('language.data_tbl_search_placeholder')}}"  },                       
                           
    } );
//for highlight the row
    $('#documentTypeDT tbody').on('click', 'tr', function () {
        $("#documentTypeDT tbody tr").removeClass('row_selected');        
        $(this).addClass('row_selected');
    });
$('#filter').change(function() {
    var filter= this.value;
    var selected_type=$("#typeselect").val(); 
    datatable.ajax.reload();
 });
$('#form_column_name').on('keyup', function(){
    datatable
    .column(0)
    .search(this.value)
    .draw();
});
$('#form_column_from').on('keyup', function(){
    datatable
    .column(1)
    .search(this.value)
    .draw();
});
$('#form_state').on('keyup', function(){
    datatable
    .column(4)
    .search(this.value)
    .draw();
});
$('#form_wf').on('keyup', function(){
    datatable
    .column(3)
    .search(this.value)
    .draw();
});
// $('#typeselect').change(function() {
//     var selected_type= this.value;
//     var filter=$("#filter").val(); 
//     datatable.ajax.reload();
//  });
$('#spl-wrn').delay(5000).slideUp('slow');
$(document).ready(function() {

    // view form more details
    var loading_src = "{{ URL::asset('images/loading/loading.gif') }}";
   var loading_text ='<div  style="text-align: center;"><img src="'+loading_src+'"></div>';
   var show_FORM_details = function(response) 
   {
    $('.form_response_remote').html(loading_text);
    $('#form_response_modal').modal({
                     show: 'show',
                     backdrop: false
               }); 
    var formID = response.formid;
    var form_response_unique_id = response.form_response_unique_id;

    $.ajax({
            type: 'get',
            url: "{{URL('formMoreDetails')}}",
            dataType: 'json',
            data: {formid:formID,form_response_unique_id:form_response_unique_id},
            timeout: 50000,
            success: function(data)
            {
                $('.form_response_remote').html(data.html);
            }
        });
   };

    $(document).on("click",".view_form_response_details",function(e) {
   e.preventDefault();
   var obj = {formid: $(this).attr('form_id'),form_response_unique_id: $(this).attr('form_response_unique_id')};
   show_FORM_details(obj);
   
   });

    @php
    if(isset($response) && $response)
    {
    @endphp    
        var obj = {formid: "@php echo $form_id; @endphp",form_response_unique_id: "@php echo $response; @endphp" };
        show_FORM_details(obj);
    @php
    }

    @endphp

$(document).on("click",".save_action_form",function(e) {
   e.preventDefault();
   var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
   var formid = $('#form_response_form_id').val();
   var form_response_unique_id= $('#form_response_unique_id').val();
   var activity_id= $('#form_activity_id').val();
   var activity_name= $('#form_activity_id').find('option:selected').text();
   var activity_note= $('#form_response_remark').val();
if($("#form_response_action_form").parsley().validate())
   {
   $.ajax({
            type: 'POST',
            url: "{{URL('save_action_form')}}",
            data: {_token:CSRF_TOKEN,formid: formid,form_response_unique_id: form_response_unique_id,activity_id: activity_id,activity_name: activity_name,activity_note: activity_note},
            dataType: 'json',
            timeout: 50000,
            success: function(data)
            {
                if(data.html !='')
                {
                  $('.form_response_remote').html(data.html);
                }
                if(data.message !='')
                {
                   $('.alert_form').html(data.message);
                }
               
            }
        });
   }
   });


} );
//Delete single form
function delete_single(form_id,unique_id,docname)
{   
    if(docname=="")
    {
        docname="Submitted Form";
    }
    swal({
          title: "{{trans('language.confirm_delete_single')}}'" + docname + "' ?",
          text: "{{trans('language.Swal_not_revert')}}",
          type: "{{trans('language.Swal_warning')}}",
          showCancelButton: true
        }).then(function (result) {
        if(result)
        {
            // Success
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: "{{url('deleteSingleSubmittedform')}}",
                data: {_token: CSRF_TOKEN, form_id:form_id,form_name:docname,form_response_unique_id:unique_id},
                timeout: 50000,
                beforeSend: function() {
                    $("#bs").show();
                },
                success: function(data, status)
                {
                    // success
                    if(data==1)
                    {
                        swal({
                        title: "Sumbmitted Values of Form '"+docname+"' {{trans('language.success_delete')}}",
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ok'
                        }).then(function (result) {
                            if(result){
                                // Success
                                window.location.reload();
                            }
                        });
                    }
                    else
                    {
                        // data=0
                        swal("Submitted form not found");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(jqXHR);    
                    console.log(textStatus);    
                    console.log(errorThrown);    
                },
                complete: function() {
                    $("#bs").hide();
                }
            });
        }   
    });
}
</script>
@endsection