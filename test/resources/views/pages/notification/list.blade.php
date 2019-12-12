<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}
{!! Html::style('plugins/datatables_new/colReorder.dataTables.min.css') !!}

<style type="text/css">
.read{
    font-weight: 300;
    background-color: #EBEDEF !important;
    color: black;
}
.not_read{
    font-weight: 500;
    color: black;
}
.workflow_type{
    border-left: 6px solid #9900ff;
}
.form_type{
    border-left: 6px solid #cc0066;
}
.general_type{
    border-left: 6px solid #996600 !important;
}
.test{
    margin-top: 23px;
    position: absolute;
}
@media (max-width: 1023px){
    .test{
    margin-top: 0px;
    position: unset;
}
}
.dropdown-menu-form {
    padding: 5px 10px 0;
    max-height: 270px;
    min-width: 136px !important;
}
table.dataTable{
    border-collapse:collapse;
}

.dataTable{
    width: 100% !important;
}
.dataTables_scrollHeadInner{
    width:100% !important;
}
.content {
        min-height: 0px !important;     
    }
    @media(max-width:517px){
       
    }
    @media(max-width: 991px){
        .content-header {
            padding: 22px 15px 0 !important;
            height: 48px;
        }
        .content-header>h1 {
            margin-top: -19px;
        }
    }

    @media (max-width: 767px){
        .content-header {
            padding: 15px 0px 0 !important;
            height: 20px!important;
        }
    }

</style>
<section class="content-header">
  <div class="col-sm-2">
    <span style="float:left;">
      <strong>{{$language['notifications']}}</strong> &nbsp;
    </span>
  </div>
  <div class="col-sm-7" style="font-size:12px;">    
        Color legend: <button type="button" style="margin-left: 10px; cursor: context-menu; padding: 1px 12px !important; " class="btn btn-warning-btn"></button>
        &nbsp;Workflow Notifications
        <button type="button" style="margin-left: 10px; padding: 1px 12px !important; cursor: context-menu;" class="btn btn-danger-btn"></button>
        &nbsp;Form Notifications
        <button type="button" style="margin-left: 10px; padding: 1px 12px !important; cursor: context-menu;" class="btn btn-success-btn"></button>
        &nbsp;General Notifications
    </div>
  <div class="col-sm-3">
    <!-- <ol class="breadcrumb">
        <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
        <li class="active">{{$language['notifications']}}</li>
    </ol> -->
  </div>
</section>
<section class="content" id="shw">
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header">       
                    <div class="col-md-2">
                        <div id="grpChkBox" class="test">
                            <div class="button-group">
                                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><span>Show/Hide Columns</span> <span class="caret"></span></button>
                                <ul class="dropdown-menu dropdown-menu-form" role="menu">
                                    <li>
                                    <ul class="menu">
                                    <li>
                                      <input type="checkbox" name="0" /><a> {{$language['notifications']}}</a>
                                    </li>
                                    <li>
                                      <input type="checkbox" name="1" /><a> {{$language['from']}}</a>
                                    </li>
                                    <li> 
                                      <input type="checkbox" name="2" /><a> {{$language['to']}}</a> 
                                    </li>
                                    <li>
                                      <input type="checkbox" name="3" /><a> {{$language['created date']}}</a>
                                    </li>
                                    <li> 
                                      <input type="checkbox" name="4" /><a> {{$language['actions']}}</a> 
                                    </li>
                                    </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label class="control-label" id="labeldoc1">{{$language['notification_type']}} :</label>    
                        <select class="form-control" id="typeselect" name="typeselect">
                        <option value="0">{{($language['all notification types'])}}</option>
                        <!-- <option value="1">{{($language['doc_notifications'])}}</option>
                        <option value="2">{{($language['audit_notifications'])}}</option>
                        <option value="3">{{($language['pwd_notifications'])}}</option> -->
                        <option value="4">{{($language['workflow_notifications'])}}</option>
                        <option value="5">{{($language['form_notifications'])}}</option>
                        <!-- <option value="6">{{($language['activity_notifications'])}}</option> -->
                        <option value="7">{{($language['general_notifications'])}}</option>
                        </select>
                    </div>
                    <!-- radio button section -->
                    <div class="col-md-5">
                        <label class="control-label" id="labeldoc2">{{$language['filter']}} :</label>
                        <select class="form-control" id="filter" name="filter">
                            <option value="0" selected="selected">
                                {{$language['notification_all']}}
                            </option>
                            <!-- <option value="1">
                               {{$language['notification_read']}}
                            </option> -->
                            <option value="2"> 
                                {{$language['notification_not_read']}}    
                            </option> 
                            <!-- <option value="3"> 
                                {{$language['notification_assigned_by_me']}}
                            </option>  -->
                            <option value="4"> 
                                {{$language['notification_assigned_to_me']}}
                            </option> 
                        </select>
                    </div>
                    <!-- end radio buttons -->
                
            </div>
            <div class="box-body">
                <table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable hover">
                    <thead>
                        <tr>
                            <th class="0" title="{{$language['sort_reorder_column']}}">{{$language['notifications']}}</th> 
                            <th class="1" title="{{$language['sort_reorder_column']}}">{{$language['from']}}</th>
                            <th class="2" title="{{$language['sort_reorder_column']}}">{{$language['to']}}</th>                          
                            <th class="3" title="{{$language['sort_reorder_column']}}">{{$language['created date']}}</th>                
                            <th class="4 no-sort" title="{{$language['reorder_column']}}">{{$language['actions']}}</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td class="0"><input type="text" id="form_column" placeholder="Search Notifications"></td>
                            <td class="1"><input type="text" id="form_column_from" placeholder="Search From"></td>
                            <td class="2"><input type="text" id="form_column_to" placeholder="Search To"></td>
                            <td class="3"></td>
                            <td class="4"></td>
                        </tr>
                    </tfoot>
                    <tbody>
                        
                    </tbody>                    
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>
</section>
{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
{!! Html::script('plugins/datatables_new/dataTables.colReorder.min.js') !!}
{!! Html::script('plugins/datatables_new/jquery.columntoggle.js') !!}
{!! Html::script('plugins/datatables_new/dataTables.fixedHeader.min.js') !!}
<script type="text/javascript">
var selected = [];
var processing_data = 'Processing';
var typeselect =   function()
    {   
     var obj = $('#typeselect').val();
    return obj;
    };
var filter =   function()
    {   
     var obj = $('#filter').val();
    return obj;
    };
var lengthMenu = getLengthMenu();//Function in app.blade.php
var rows_per_page = '{{Session('settings_rows_per_page')}}';
var datatable =   $('#documentTypeDT').DataTable( {        
                            "processing": true, 
                            "serverSide": true,
                            "scrollX":true,
                            "scrollY":350,
                            "order": [[3,"desc"]],
                            "stateSave": true,
                            "colReorder": true,
                            "pageLength": rows_per_page,
                            "lengthMenu": lengthMenu,
                            "ajax": {
                               "url": "@php echo url('notificationfilter'); @endphp",
                               "type": "POST",
                                "headers": { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                               "data":function(d){
                                        d.typeselect = typeselect();
                                        d.filter = filter();
                                     }
                                },
                            "rowCallback": function( row, data ) {
                                    if(data['notification_type'] == 'workflow')
                                    {
                                        $(row).addClass('workflow_type');
                                    }
                                    else if(data['notification_type'] == 'form')
                                    {
                                        $(row).addClass('form_type');
                                    }
                                    else
                                    {
                                        $(row).addClass('read');
                                    }
                                    if ( data['viewed'] == 1 ) 
                                    {
                                        $(row).addClass('read');
                                    }
                                    else
                                    {
                                        $(row).addClass('not_read');
                                    }
                                },
                            //"iDisplayLength": 25,
                            "columns": [
                                        {
                                            "class":"0",
                                            "data":"notification",
                                        },
                                        {
                                            "class":"1",
                                            "data":"from",
                                        },
                                        {
                                            "class":"2",
                                            "data":"to",
                                        },
                                        {
                                            "class":"3",
                                            "data":"created",
                                        },
                                        {
                                            "class":"4",
                                            "data":"actions",
                                        }
                                    ],
                              "columnDefs": [
                                  { targets: 'no-sort', orderable: false }
                                ],          
                             "language": { "processing": processing_data,"searchPlaceholder": "{{$language['data_tbl_search_placeholder']}}"  },                       
                           
    });
    //for highlight the row
    $('#documentTypeDT tbody').on('click', 'tr', function () {
        $("#documentTypeDT tbody tr").removeClass('row_selected');        
        $(this).addClass('row_selected');
    });
//selction on rows click on the tr tag
    /*$('#documentTypeDT tbody').on('click', 'tr', function () {
        var id = this.id;
        var index = $.inArray(id, selected);
 
        if ( index === -1 ) {
            selected.push( id );
        } else {
            selected.splice( index, 1 );
        }
 
        $(this).toggleClass('selected');
    } );*/
$('#form_column').on('keyup', function(){
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
//clear data table textboxes values
    datatable
    .search( '' )
    .columns().search( '' )
    .draw();
//end clear
//hide and show column    
$(function () {
    var $chk = $("#grpChkBox input:checkbox");
    var $tbl = $(document);

    $chk.prop('checked', true);

    $chk.click(function () {
        var colToHide = $tbl.find("." + $(this).attr("name"));
        $(colToHide).toggle();
    });

}); 
//dropdown not close on click
$("document").ready(function() {
  $('.dropdown-menu').on('click', function(e) {
      if($(this).hasClass('dropdown-menu-form')) {
          e.stopPropagation();
      }
  });
});  
$('#filter').change(function() {
    var filter= this.value;
    var selected_type=$("#typeselect").val(); 
    datatable.ajax.reload();
 });
$('#typeselect').change(function() {
    var selected_type= this.value;
    var filter=$("#filter").val(); 
    datatable.ajax.reload();
 });
</script>
@endsection