<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')

{!! Html::script('js/parsley.min.js') !!}  
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables_new/fixedHeader.dataTables.min.css') !!}
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}
<style type="text/css">
    .content {
        min-height: 0px !important;     
    }
    @media(max-width:517px){
        .box{
            overflow-x: scroll;
        }
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

<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="col-sm-8">
        <strong>{{trans('audits.audits')}}</strong>                
    </div>
    <div class="col-sm-4">
        <!-- <ol class="breadcrumb">
            <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
            <li class="active">{{trans('audits.audits')}}</li>
        </ol> -->
    </div>
</section>
<!-- Main content -->
<div class="content">
    <div class="row">
        <div class="col-xs-12">
            <!--Checking module permission-->
                @if(Session::get('enbval5')==Session::get('tval'))
                    <div class="box box-info">
                        <div class="box-header">
                             <!--search list-->
                             @if(Session::get('is_search_exists') == 'yes')
                                <div class="box-body no-padding col-sm-10">

                                    <h3 class="box-title">Filtered By: </h3>

                                    <?php if(!empty(Session::get('username'))) { ?>
                                    <span style="font-style: italic;font-weight: bold;"><?php echo trans('language.sign in name');?></span>: <?php echo Session::get('username');?> &nbsp
                                    <?php } if(!empty(Session::get('actions'))) { ?>
                                    <span style="font-style: italic;font-weight: bold;"><?php echo trans('language.actions');?></span>: <?php echo Session::get('actions'); ?> &nbsp
                                    <?php } if(!empty(Session::get('items'))) { ?>
                                    <span style="font-style: italic;font-weight: bold;"><?php echo trans('language.items');?></span>: <?php echo Session::get('items'); ?> &nbsp
                                    <?php } if(!empty(Session::get('docno'))) { ?>
                                    <span style="font-style: italic;font-weight: bold;"><?php echo trans('language.document no'); ?></span>: <?php echo Session::get('docno'); ?> &nbsp
                                    <?php }  if(!empty(Session::get('docname'))) { ?>
                                    <span style="font-style: italic;font-weight: bold;"><?php echo trans('language.document name');?></span>: <?php echo Session::get('docname'); ?> &nbsp
                                    <?php } if(!empty(Session::get('stack_names'))) { ?>
                                    <span style="font-style: italic;font-weight: bold;"><?php echo trans('stack.stacks');?></span>: <?php echo Session::get('stack_names'); ?> &nbsp
                                    <?php } if(!empty(Session::get('departmntNames'))) { ?>
                                    <span style="font-style: italic;font-weight: bold;">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.departments')}} @endif</span>: <?php echo Session::get('departmntNames'); ?> &nbsp
                                    <?php } if(!empty(Session::get('document_type_names'))) { ?>
                                    <span style="font-style: italic;font-weight: bold;"><?php echo trans('language.document type');?></span>: <?php echo Session::get('document_type_names'); ?> &nbsp
                                    <?php } if(!empty(Session::get('date_from'))) { ?>
                                    <span style="font-style: italic;font-weight: bold;"><?php echo trans('language.date_from');?></span>: <?php echo Session::get('date_from'); ?> &nbsp
                                    <?php } if(!empty(Session::get('date_to'))) { ?>
                                    <span style="font-style: italic;font-weight: bold;"><?php echo trans('language.date_to');?></span>: <?php echo Session::get('date_to'); ?> &nbsp
                                    <?php } ?>

                                    <div>
                                        <a href="{{URL::route('audits')}}?is_back=change_criteria"><button class="btn btn-primary">{{trans('language.change_criteria')}} <i class="fa fa-exchange" aria-hidden="true"></i></button></a>    
                                    </div>
                                    
                                </div>
                            @endif

                            <span class="pull-right">
                                <?php
                                $user_permission=Auth::user()->user_permission;                         
                                ?>     
                                @if(stristr($user_permission,"add"))
                                <a href="{{URL::route('audits')}}"><button class="btn btn-block btn-info btn-flat">Search <i class="fa fa-search"></i></button></a>
                                @endif
                            </span>
                        </div>
                        <div class="box-body">

                            <!--Loading-->
                            <div id="bs" style="display:none; text-align:center;">
                                <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
                                <span class="sr-only">Loading...</span>
                            </div>

                            <table border="1" id="departmentDT" class="table table-bordered table-striped dataTable hover" width="100%" role="grid" aria-describedby="example1_info">
                                <thead>
                                    <tr>
                                        <th nowrap="nowrap">{{trans('language.sign in name')}}</th>
                                        <th>{{trans('language.item')}}</th>
                                        <th>{{trans('language.description')}}</th>     
                                        <th>{{trans('language.actions')}}</th>     
                                        <th>{{trans('language.created date')}}</th>     
                                            
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>                 
                            </table>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                @elseif(Session::get('enbval5')==Session::get('fval'))
                    <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{trans('language.purchase_now')}}</div></section>
                @else
                    <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{trans('language.module_expired')}}</div></section>
                @endif
        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.content-wrapper -->   
{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
{!! Html::script('plugins/datatables_new/dataTables.fixedHeader.min.js') !!}
    <script>
    $(function () {

        var rows_per_page = '{{Session('settings_rows_per_page')}}';
        var lengthMenu = getLengthMenu();//Function in app.blade.php

        var searchedValues         = '<?php print_r(json_encode(@$values));?>';
        if(searchedValues != 'null'){
            var controller = '<?php echo URL('auditsSearchRecords');?>?searchedValues='+searchedValues+'';
        }else{
            var controller = '<?php echo URL('getAllAudits');?>';
        }
        //console.log(controller);
        var dataTable = $('#departmentDT').DataTable( {
            "lengthMenu": lengthMenu,
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "order": [[ 4, "desc" ]],/*default order*/
            "columnDefs": [ { orderable: false, targets: [2]}],
            "scrollX": true,
            "scrollY": 350,
            "language": { processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> ',"searchPlaceholder": "{{trans('language.data_tbl_search_placeholder')}}"  },
            "pageLength": rows_per_page,         
            "ajax":{
                url :controller, // json datasource
                type: "get",  // method  , by default get
                error: function(){  // error handling        
                    //swal'No data found');
                    //$("#departmentDT_processing").css("display","none");
                    $(".departmentDT-error").html("");
                    $("#departmentDT").append('<tbody class="departmentDT-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#departmentDT_processing").css("display","none");
                }
            }
        } );
            //for highlight the row
    $('#departmentDT tbody').on('click', 'tr', function () {
        $("#departmentDT tbody tr").removeClass('row_selected');        
        $(this).addClass('row_selected');
    });
    });

</script>  

@endsection