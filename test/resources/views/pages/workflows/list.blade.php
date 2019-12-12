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
    $wf_permission = Auth::user()->user_workflow_permission;
?>
<section class="content-header">
  <div class="col-sm-4">
    <span style="float:left;">
      <strong>{{ Lang::get('workflows.workflow_template') }}</strong> &nbsp;
    </span>
    <!-- super admin can only add forms -->
    @if(Session::get('enbval4')==Session::get('tval'))   
        @if(stristr($wf_permission,"add"))
            <span style="float:left;">
              <a href="{{URL::route('closed_workflow')}}">
                  <button class="btn btn-block btn-info btn-flat newbtn">{{ Lang::get('workflows.workflow_new_template') }}  <i class="fa fa-plus"></i></button>
              </a>
            </span>
        @endif
    @endif
    </div>
    <div class="col-sm-4">
   
  </div>
  <div class="col-sm-4">
    <!-- <ol class="breadcrumb">
        <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
        <li class="active">{{trans('forms.forms')}}</li>
    </ol> -->
  </div>
</section>
<section class="content content-sty" id="ajax-msg" style="display:none;"></section>

        @if(stristr($wf_permission,"view"))
        <section class="content" id="shw">
            <div id="bs" style="display:none; text-align:center;">
                <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>

            <div class="row">
                @if(Session::has('flash_message_success'))
                    <section class="content content-sty" id="spl-wrn">        
                        <div class="alert alert-sty {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('flash_message_success') }}</div>        
                    </section>
                @endif
                @if(Session::get('enbval4')==Session::get('tval'))   
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header"> 
                            
                        </div>
                        <div class="box-body">
                            <table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable hover">
                                <thead>
                                    <tr>
                                    <th>Workflow Name</th> 
                                    <th class="no-sort">Stages</th> 
                                    <th class="no-sort">Process</th>                  
                                    <th>Created</th>                    
                                    <th class="no-sort">{{trans('language.actions')}}</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <td><input type="text" id="form_column_name" placeholder="Search Workflow Name"></td>
                                        <td></td>
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
                @elseif(Session::get('enbval4')==Session::get('fval'))
                    <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{trans('language.purchase_now')}}</div></section>
                @else
                    <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{trans('language.module_expired')}}</div></section>
                @endif
            </div>
        </section>
        @else
            <section class="content content-sty">        
                <div class="alert alert-sty alert-error">{{trans('language.dont_hav_permission')}}</div>        
            </section>
        @endif
    
     <div class="modal fade" id="add_to_workflow_modal_new">
         <div class="modal-dialog">
            <div class="modal-content add_workflow_remote">
               
            <div class="modal-footer"></div>
            </div>
            <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->

      <div class="modal-container-child"></div> 
      <div class="modal-container"></div>
      <!-- /.modal -->
{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
{!! Html::script('plugins/datatables_new/dataTables.fixedHeader.min.js') !!}
<script type="text/javascript">



// function load(){
//     var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
//     $.ajax({
//         type: 'get',
//         url: '{{URL('allworkflow')}}',
//         dataType: 'html',
//         data: {_token: CSRF_TOKEN},
//         timeout: 50000,
//         beforeSend: function() {
//             $("#bs").show();
//         },
//         success: function(data, status){
//             $("#shw").html(data);               
//         },
//         error: function(jqXHR, textStatus, errorThrown){
//             console.log(jqXHR);    
//             console.log(textStatus);    
//             console.log(errorThrown);    
//         },
//         complete: function() {
//             $("#bs").hide();
//         }
//     });
// }

// ajax data table
var selected = [];
// var typeselect =   function()
//     {   
//      var obj = $('#typeselect').val();
//     return obj;
//     };
var filter =   function()
    {   
     var obj = '';
    return obj;
    };
//datatable
var rows_per_page = '{{Session('settings_rows_per_page')}}';
var lengthMenu = getLengthMenu();//Function in app.blade.php
var datatable =   $('#documentTypeDT').DataTable( { 
              "fnInitComplete": function() {
        $('#documentTypeDT tbody tr').each(function(){
                $(this).find('td:eq(3)').attr('nowrap', 'nowrap');
                $(this).find('td:eq(5)').attr('nowrap', 'nowrap');
        });
    },
                            "lengthMenu": lengthMenu,
                            "pageLength":rows_per_page,       
                            "processing": true, 
                            "serverSide": true,
                            
                            "scrollY": 350,
                            "fixedHeader": true,
                            "order": [[3,"desc"]],
                            "ajax": {
                               "url": "@php echo url('ajax_workflow_list'); @endphp",
                               "type": "POST",
                                "headers": { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                               "data":function(d){
                                        //d.typeselect = typeselect();
                                     }
                                },
                            "iDisplayLength": 25,
                            "columns": [
                                        {
                                            "class":"0",
                                            "data":"workflow_name",
                                        },
                                        {
                                            "class":"1",
                                            "data":"stages_count",
                                        },
                                        {
                                            "class":"1",
                                            "data":"Process_count",
                                        },
                                        {
                                            "class":"1",
                                            "data":"created",
                                        },
                                        {
                                            "class":"2",
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
$('#form_column_to').on('keyup', function(){
    datatable
    .column(2)
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


  $(document).on("click",".start_process",function(e) {
   e.preventDefault();
   var src = "{{ URL::asset('images/loading/loading.gif') }}";
   var loading ='<div  style="text-align: center;"><img src="'+src+'"></div>';
   $('.add_workflow_remote').html(loading);
   $('#add_to_workflow_modal_new').modal({
                     show: 'show',
                     backdrop: false
               }); 
   var workflow_id=$(this).attr('data-workflow_id');
   var object_type='document';  
   $('.add_workflow_remote').load("@php echo url('add_to_workflows_modal') @endphp?workflow_id="+workflow_id+"&object_type="+object_type,function(result){
         /*$('#loading_model').modal('hide');*/
         /*$('#add_to_workflow_modal_new').modal({
                     show: 'show',
                     backdrop: false
               }); */
         
         });
   });
   
} );

function wfdel(id,name)
{     
swal({
    title: "{{trans('language.confirm_delete_single')}}'" + name + "' ?",
    text: "{{trans('language.Swal_not_revert')}}",
    type: "{{trans('language.Swal_warning')}}",
    showCancelButton: true
}).then(function (result) {
    if(result){
        // Success
        $('#spl-wrn').html('');//hide unwanted message box
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'post',
            url: '{{URL('wrkflowDelete')}}',
            dataType: 'json',
            data: {_token: CSRF_TOKEN, id:id, name:name},
            timeout: 50000,
            beforeSend: function() {
                $("#bs").show();
            },
            success: function(data, status){ 
                //alert(data);
                if(data == 'false'){
                    //error message if data exists
                    //load();
                    ///window.location.reload();
                    $('#ajax-msg').html('<section class="content content-sty" id="spl-wrn"><div class="alert alert-error alert-sty">Sorry! Can \'t delete, '+name+' is availabe in child table</div></section>');
                    setTimeout(function () {
                        $("#ajax-msg").show();
                    }, 200);
                    setTimeout(function () {
                       $('#ajax-msg').hide();
                    }, 6000);
                } else{   
                //     load();
                //window.location.reload();
                    // setTimeout(function () {
                    //     $('#ajax-msg').html('<section class="content content-sty" id="spl-wrn"><div class="alert alert-success alert-sty">The workflow '+name+' deleted successfully.</div></section>');
                    //     $("#ajax-msg").show();
                    //     datatable.ajax.reload();
                    // }, 200);
                    // setTimeout(function () {
                    //     $('#ajax-msg').hide();
                    // }, 5000);
                    var text = 'The workflow '+name+' deleted successfully.';
                    swal({title: "Deleted", text: text, type: 
                    "success"}).then(function(){ 
                       location.reload();
                       }
                    );    
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