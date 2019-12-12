<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
{!! Html::script('js/parsley.min.js') !!}  
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}


<section class="content-header">
    <div class="col-sm-8">
        <span style="float:left;">
            <strong>{{trans('activities.activities')}}</strong> &nbsp;
        </span>
        @if(Auth::user()->user_role == 1)
        <span style="float:left;">
            <?php 
                $user_permission=Auth::user()->user_permission;
            ?>     
            @if(Auth::user()->user_role == 1)
                <a href="" data-toggle="modal" data-target="#dTAddModal">
                    <button class="btn btn-block btn-info btn-flat newbtn">{{trans('language.add new')}} <i class="fa fa-plus"></i></button>
                </a> 
            @endif
        </span>
        @endif
    </div>
    <div class="col-sm-4">
        <!-- <ol class="breadcrumb">
          <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
          <li class="active">{{trans('activities.activities')}}</li>
        </ol> -->
    </div>
</section>

<section class="content" id="shw">

    <!--Show message after insert and update-->
    @if (session('status'))
    <div class="alert alert-success" id="hide-div">
        <p style="text-align: center;"><strong>{{trans('language.success')}}!</strong> {{ session('status') }}</p>
    </div>
    @endif

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-info">                
                <?php
                include (public_path()."/storage/includes/lang1.en.php" );
                ?>
                <div class="box-body">

                    <table border="1" id="tableRecord" class="table table-bordered table-striped dataTable responsive hover" width="100%" role="grid" aria-describedby="example1_info">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th style="width: 20% !important;">{{trans('language.activity')}}</th>
                                <th class="no-sort" style="width: 11% !important;">{{trans('language.action_response')}}</th>
                                <th style="width: 20% !important;">{{trans('language.created date')}}</th>     
                                <th style="width: 20% !important;">{{trans('language.updated date')}}</th>
                                @if(Auth::user()->user_role == 1)     
                                <th class="no-sort" style="width: 5% !important;">{{trans('language.actions')}}</th> 
                                @endif     
                            </tr>
                        </thead>               
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
  </div><!-- /.row -->
</section>
<!-- User add Form -->
<div class="modal fade" id="dTAddModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dGAddModal" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                 {{trans('activities.activities')}}
                 <small>- {{trans('language.add new')}} {{trans('activities.activty')}}</small>
             </h4>
         </div>
         <div class="modal-body">
            <!-- form start -->
            {!! Form::open(array('url'=> array('activitySave'), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'addForm', 'id'=> 'addForm','data-parsley-validate'=> '')) !!}
                        
            <div class="form-group">
                <label class="col-sm-2 control-label">{{trans('activities.activty')}}: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <input class="form-control" id="name" placeholder="{{Lang::get('activities.activty')}}" required="" data-parsley-required-message="{{Lang::get('activities.activty')}} {{trans('language.is_required')}}" autofocus="autofocus" name="activity_name" value=""  title="{{Lang::get('activities.activty')}}">    
                </div>
            </div>   
            <div class="form-group">
                <div class="col-sm-2"></div>
                <!-- <label class="col-sm-2 control-label">{{Lang::get('language.activty_module')}}: <span class="compulsary"></span></label> -->
                <div class="col-sm-8">
                    <!-- <label class="checkbox-inline"><input type="checkbox" name="activity_module[]" value="workflows" >Workflows</label> -->
                <label class="checkbox-inline"><input type="checkbox" name="activity_module[]" value="form_action">Action Response</label>
                <p class="settingHelp">{{trans('activities.action_response_help_text')}}</p>
                </div>
            </div>   

           <!--  <div class="form-group">
                <div class="col-sm-2"></div>
                
                <div class="col-sm-8">
                <label class="checkbox-inline"><input type="checkbox" name="last_activity" value="1">This is the last activity of a stage</label>
                <p class="settingHelp">{{$language['last_activity_help_text']}}</p>
                </div>
            </div>-->


            <div class="form-group">
                <div class="col-sm-2">
                 <label class="control-label">Flag: </label>   

                </div>
                <div class="col-sm-8">
                   
                <label class="checkbox-inline"><input type="checkbox" name="activity_constant" class="cflags" id="capprove" value="approve">Approve</label>

                <label class="checkbox-inline"><input type="checkbox" name="activity_constant" class="cflags" id="creject" value="reject">Reject</label>

                <label class="checkbox-inline"><input type="checkbox" name="activity_constant" class="cflags" id="con-hold" value="on-hold">On Hold</label>
                <p class="settingHelp"><!--{{$language['last_activity_help_text']}}--></p>
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"></label>
                    <div class="col-sm-8">
                        <input class="btn btn-primary" id="save" type="submit" value="{{trans('language.save')}}" onclick="myFunction()"> &nbsp;&nbsp;
                        {!!Form::button(trans('language.close'), array('class' => 'btn btn-primary btn-danger', 'id' => 'cn', 'data-dismiss'=> 'modal', 'aria-hidden'=> 'true')) !!}
                    </div>
                </div><!-- /.col -->
                {!! Form::close() !!}
         </div>
    </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
    
<!--Load data table-->
{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}

<script type="text/javascript">

$(document).ready(function(){
    //<!--Server side script-->

    var rows_per_page = '{{Session('settings_rows_per_page')}}';
    var lengthMenu = getLengthMenu();//Function in app.blade.php

    // Initilize data table
    var dataTable = $('#tableRecord').DataTable({
                        "lengthMenu": lengthMenu,
                        "pageLength":rows_per_page,
                        "destroy": true,
                        "processing": true,
                        "serverSide": true,
                        "scrollX": true,
                        "scrollY": 350,
                        "order": [[3,"desc"]],
                        "ajax": base_url+"/activityList",
                        "language": {
                              "emptyTable": "{{trans('language.no_data_available')}}"
                            },
                        "columnDefs": [
                                  { targets: 'no-sort', orderable: false }
                                ],  
                        "language": { processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> ',"searchPlaceholder": "{{trans('language.data_tbl_search_placeholder')}}"  }
                    });
    //for highlight the row
    $('#tableRecord tbody').on('click', 'tr', function () {
        $("#tableRecord tbody tr").removeClass('row_selected');        
        $(this).addClass('row_selected');
    });
    // Hide column id [id we used because need id to edit and deleted.Its important]
    dataTable.column(0).visible(false);
    //<!--Server side script ends-->
    // Delete
    $('body').on('click','.activity-delete',function(){
        var activityId = $(this).attr('activityId');    
        var title      = $(this).attr('name');   
        // Delete
        swal({
              title:"{{trans('language.confirm_delete_single')}}'" + title + "' ?",
              text: "{{trans('language.Swal_not_revert')}}",
              type: "{{trans('language.Swal_warning')}}",
              showCancelButton: true,
            }).then(function (result) {
                if(result){
                    // Success
                   $.ajax({
                        type:'GET',
                        url:base_url+'/deleteActivity',
                        data:'activityId='+activityId+'&title='+title,
                        success:function(result){
                            console.log('success');
                        }
                    });
                   // reload the same page
                   //location.reload();
                }
                // success msg
                swal({
                  title: "{{trans('language.Swal_deleted')}}",
                  type: "{{trans('language.Swal_warning')}}",
                  showCancelButton: false,
                }).then(function (result) {
                    if(result){
                        // Success
                        // reload the same page
                        location.reload();
                    }
                });

            });
    });


$(document).on('click','.cflags', function(){  

      var cflags = $(this).val();  
      var ckb = $(this).is(':checked');
      console.log($(this).val());   
     $('.cflags').prop('checked', false);

        if(ckb == true) 
        {
            $("#c"+cflags).prop('checked', true);
        }
      
});

});  
</script>

@endsection

