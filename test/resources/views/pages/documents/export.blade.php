<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')

{!! Html::script('js/parsley.min.js') !!}  
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
<!--{!! Html::script('js/build.min.js') !!} 
{!! Html::style('css/build.min.css') !!} -->
{!! Html::style('css/fastselect.min.css') !!} 
{!! Html::script('js/fastselect.standalone.js') !!}  
{!! Html::script('plugins/daterangepicker/daterangepicker.js') !!}
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}

<!-- Content Wrapper. Contains page content -->
<style type="text/css">
    .content-wrapper{
        min-height: 570px;
    }
    .fstElement{
        width: 100%;
    }
    .fstControls{
        width: 100% !important;
    }
    a {
        color: #3c8dbc;
    }
    .content-header {
    padding: 7px 15px 0px !important;
}
</style>

<!-- Content Wrapper. Contains page content -->
<section class="content-header">
    <div class="col-sm-8">
        <strong>
            {{$language['documents']}}
            <small>- {{$language['export']}}</small>
        </strong>
    </div>
    <div class="col-sm-4">
       <!--  <ol class="breadcrumb">
            <li><a href="<?php echo  url('/home');?>"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
            <li><a href="<?php echo  url('/documents');?>">{{$language['documents']}}</a></li>
            <li class="active">{{$language['export']}}</li>
        </ol>   --> 
    </div>
</section>
<section class="content content-sty">    
    @if(session('status'))
        <div class="alert alert-warning" id="hide-div">
            <p style="text-align: center;"><b><?php echo Session::get('status');?></b></p>
        </div>
    @endif             
</section>
<!--Checking view permission-->
@if(Session::get('enbval1')==Session::get('tval'))
        <div class="modal-body"> 
            <!-- form start -->
            {!! Form::open(array('files'=>true,'class'=> 'form-horizontal', 'name'=> 'documentAdvSearchForm', 'id'=> 'documentAdvSearchForm','data-parsley-validate'=> '')) !!}   


            <!-- Lift Side -->
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="col-sm-12">
                        {!! Form::label($language['document types'].':', '', array('class'=> 'control-label'))!!}
                        <select name="doctypeid" id="doctypeid" class="form-control">
                            <option value="">Select Document Types</option>
                            <?php
                            foreach ($docType as $key => $row) {
                                ?>
                                <option value="<?php echo $row->document_type_id; ?>" <?php if(Session::get('export_doctypeids') == $row->document_type_id):echo 'selected';endif;?>><?php echo $row->document_type_name;?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <!-- <span style="color: #ff0000;">Note - Multiple document type selection not available.</span> -->          
                    </div>

                    
                    <!--show content or html of doc type column when click on doctype-->
                    <!-- <div id="sublist">
                    @if(Session::get('search_document_type_name'))
                        <input id="doc_type_ids" type="text" value="<?php //echo Session::get('export_doctypeids');?>">
                       
                    @endif
                   </div> -->
                   <div class="col-sm-12"> 
                        <label class="control-label">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{$language['department']}} @endif: </label>
                        <select name="department[]" id="department" class="multipleSelect form-control" multiple>
                            <?php
                            foreach ($results as $key => $row) {
                                ?>
                                <option value="<?php echo $row->department_id;?>" <?php if(Session::get('export_departments')):if(in_array($row->department_id,Session::get('export_departments'))):echo 'selected';endif;endif;?> ><?php echo $row->department_name;?></option>
                                <?php
                            }
                            ?>
                        </select>   
                    </div>
                   <div class="col-sm-12">
                        {!! Form::label($language['stacks'].':', '', array('class'=> 'control-label'))!!}
                        <select name="stacks[]" id="stacks" class="multipleSelect form-control" multiple>
                            <?php
                            foreach ($stacks as $key => $row) {
                                ?>
                                <option value="<?php echo $row->stack_id; ?>" <?php if(Session::get('export_stackids')):if(in_array($row->stack_id,Session::get('export_stackids'))):echo 'selected';endif;endif;?>><?php echo $row->stack_name;?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-12">
                        {!! Form::label($language['ownership'].':', '', array('class'=> 'control-label'))!!}
                        <select name="ownership[]" id="ownership" class="multipleSelect form-control" multiple>
                            <?php
                            foreach ($users as $key => $row) {
                                ?>
                                <option value="<?php echo $row->username; ?>" <?php if(Session::get('export_owner_ids')):if(in_array($row->id,Session::get('export_owner_ids'))):echo 'selected';endif;endif;?> ><?php echo $row->username;?></option>

                                <?php
                            }
                            ?>
                        </select> 
                    </div>
                    
                </div>
            </div> <!-- End Of Lfet Side -->

            <!-- Right Side -->
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="col-sm-12">
                        {!! Form::label($language['created date - from'].':', '', array('class'=> 'control-label'))!!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" value="{{Session::get('export_search_created_date_from')}}" class="form-control active" id="created_date_from" name="created_date_from" placeholder="YYYY-MM-DD" title="Created Date - From" data-toggle="tooltip" data-original-title="">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        {!! Form::label($language['created date - to'].':', '', array('class'=> 'control-label'))!!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" value="{{Session::get('export_search_created_date_to')}}" class="form-control active" id="created_date_to" name="created_date_to" placeholder="YYYY-MM-DD" title="Created Date - To" data-toggle="tooltip" data-original-title="">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        {!! Form::label($language['last modified - from'].':', '', array('class'=> 'control-label'))!!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" value="{{Session::get('export_search_last_modified_from')}}" class="form-control active" id="last_modified_from" name="last_modified_from" placeholder="YYYY-MM-DD" title="Last Modified Date- From" data-toggle="tooltip" data-original-title="">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        {!! Form::label($language['last modified - to'].':', '', array('class'=> 'control-label'))!!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" value="{{Session::get('export_search_last_modified_to')}}" class="form-control active" id="last_modified_to" name="last_modified_to" placeholder="YYYY-MM-DD" title="Last Modified Date - To" data-toggle="tooltip" data-original-title="">
                        </div>
                    </div>
                    

                </div>
            </div><!-- End Of Right Side -->
            <section class="content">
                <div class="form-group">
                    <div class="col-sm-12" style="text-align:right;">
                            
                            <button type="button" id="saveEdi" class="btn btn-primary">{{$language['export_csv_only']}}</button>&nbsp;&nbsp;
                            <input type='button' id='download' value='{{$language['export_datafiles']}}' class="btn btn-primary">
                            &nbsp;&nbsp;                 
                            <a href="javascript:history.go(-1)" class = "btn btn-primary btn-danger">{{$language['cancel']}}</a> &nbsp;&nbsp;
                            <a href="javascript:void(0)" class="btn btn-primary" id="clr" title="Refresh">{{$language['refresh']}}</a>
                    </div>
                </div><!-- /.col -->
            </section>
            {!! Form::close() !!} 
            <div class="preloader" style="text-align: center; margin-top: 5px; display: none;" >
              <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>Exporting documents from database. To cancel export operation <a id="cancel_export" style="cursor: pointer;" title="Cancel Export">Click Here</a>
              <span class="sr-only">Exporting documents from database...</span>
            </div> 
            <div class="progress_section" style="display: none;margin-top: 10px;">
                <div class="progress active">
                    <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="1" aria-valuemax="100" style="width: 1%" id="status_bar">
                      
                    </div>
                </div>
            </div> 
            <!-- preloader --> 
        </div>
 @elseif(Session::get('enbval1')==Session::get('fval'))
        <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{trans('language.purchase_now')}}</div></section>
    @else
        <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{trans('language.module_expired')}}</div></section>
    @endif

<!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog" style="width: 750px !important;">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close modal_close" id="">&times;</button>
          <h4 class="modal-title">Exported Files</h4>
          <p style="font-size:12px; color:#999;">Current exported file is listed on first row of table. Previously exported files also listed here. You can download and delete them. Delete unnecessary files to save disk space</p>
        </div>
        <div class="modal-body">

        <table border="1" id="example" class="table table-bordered table-striped dataTable hover">
            <thead>
                <tr>
                <th class="no-sort" nowrap="nowrap">File Name</th> 
                <th class="no-sort" nowrap="nowrap">Size</th>   
                <th nowrap="nowrap">Created Date</th>                                    
                <th class="no-sort" nowrap="nowrap">{{$language['actions']}}</th>
                </tr>
            </thead>
            <tfoot>
        
            </tfoot>
            <tbody>
                
            </tbody>                    
        </table>
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default modal_close" id="">Close</button>
        </div>
      </div>
      
    </div>
  </div>
{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
<script>
    $( document ).ready(function() {
        //datafiles zip download
            var exportedRecords = 0;
            var chunkSize = 1000; // as per query performance
            var total_results = 0;
            var percentage_incr_last = 0;
            var total_ajax_call = 0;
            var req_count = 0;
            var totalRecords = 0;
            var start =0;
        $('#download').click(function(){
            var texta = document.forms["documentAdvSearchForm"]["doctypeid"].value;
            if(texta == "")
            {
                $(".preloader").css("display", "none");
                $(".progress_section").css("display", "none");
                swal("Document type is required");
                return false;
            }
            else
            {
                var skillsSelect = document.getElementById("doctypeid");
                var today = new Date();
                var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                var time = today.getHours() + "-" + today.getMinutes() + "-" + today.getSeconds();
                var dateTime = date+'_'+time;
                var selectedText = skillsSelect.options[skillsSelect.selectedIndex].text;
                $(".preloader").css("display", "block");
                $(".progress_section").css("display", "block");
                $.ajax({
                     url: '{{URL('RecordscountExportData')}}',
                     type: 'post',
                     dataType : "json",
                     data: $('#documentAdvSearchForm').serialize(),
                     success: function(response){
                        console.log(response);
                        totalRecords = response.count;
                        var encrypt_status = response.encrypt;
                        total_ajax_call = Math.round(totalRecords/chunkSize);
                        console.log("total_ajax_call="+total_ajax_call);
                        console.log("encrypt_status="+encrypt_status);
                        var percentage_incr = (100/total_ajax_call);
                        req_count = 0;
                        if(totalRecords == 0) 
                        {
                            $(".preloader").css("display", "none");
                            $(".progress_section").css("display", "none");
                            swal("No documents found");
                            return false;
                        }
                        if(encrypt_status == 1)
                        {
                            $(".preloader").css("display", "none");
                            $(".progress_section").css("display", "none");
                            swal({
                                    title: "There are some encrypted files. Encrypted files could not export. Do you want to continue?",
                                    type: "{{$language['Swal_warning']}}",
                                    showCancelButton: true
                                }).then(function (result) {
                                if(result)
                                {
                                    $(".preloader").css("display", "block");
                                    $(".progress_section").css("display", "block");
                                    //multiple requests at a time cause to tokenmissmatch authentication error so, for loop avoided and place request first and after the responcse that request send next request using the if case
                                    if(start < totalRecords)
                                    {
                                        //console.log(start);
                                        chunkDataExport(start, chunkSize,totalRecords,selectedText,dateTime,percentage_incr);
                                        start += chunkSize;
                                    }
                                }
                           
                                },function (dismiss) {
                                  return false;
                                });
                        }
                        else
                        {
                            if(start < totalRecords)
                            {
                                //console.log(start);
                                chunkDataExport(start, chunkSize,totalRecords,selectedText,dateTime,percentage_incr);
                                start += chunkSize;
                            }
                        }
                     }
                });
            }
        });

        function chunkDataExport(start,chunkSize,totalRecords,selectedText,dateTime,percentage_incr){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var url = '{{URL('downloadDataZip')}}';
            var section = 'zip';
            //console.log(url);
            var xhr = $.ajax({
                type : "post",
                dataType : "json",
                url: url,
                data: $('#documentAdvSearchForm').serialize()+'&start=' + start + '&chunkSize=' + chunkSize + '&selectedText=' + selectedText + '&dateTime=' + dateTime,
                success: function(response) {
                    req_count++;
                    console.log(response);
                    var count = response.result.length;
                    total_results += count;
                    console.log("req_count="+req_count);
                    percentage_incr_last = ((req_count/total_ajax_call)*100);
                    percentage_incr_last = Math.round(percentage_incr_last);
                    if(percentage_incr_last > 100) 
                    {
                        percentage_incr_last = 100;
                    }
                    console.log(percentage_incr_last);
                    $(".progress-bar-primary").css("width", percentage_incr_last+'%');
                    $('#status_bar').text(percentage_incr_last+'%');
                    var filename = response.filename;
                    exportedRecords += chunkSize;
                    start += chunkSize;
                    if(start < totalRecords)
                        {
                            
                            chunkDataExport(start, chunkSize,totalRecords,selectedText,dateTime,percentage_incr);
                            
                        }
                    else
                        {
                            downloadfile(totalRecords,exportedRecords,selectedText,section,filename,dateTime);
                        }
                }
            });
            $('#cancel_export').click(function(e){ 
                var user_name = "{{Auth::user()->username}}";
                var file = "{{Config::get('constants.export_zip')}}";
                var docname = user_name+file+selectedText.replace(/\s+/g, '')+'_'+dateTime+'.zip';
                e.preventDefault();
                xhr.abort();
                $(".preloader").css("display", "none");
                $(".progress_section").css("display", "none");
                swal({
                        title: "Do you want to cancel this operation?",
                        type: "{{$language['Swal_warning']}}"
                    }).then(function (result) {
                        if(result)
                        {
                            //delete file
                            $.ajax({
                                type: 'post',
                                url: 'DeleteZip',
                                data: {_token: CSRF_TOKEN,filename:docname},
                                success: function(response) {
                                    console.log(response);
                                }
                            });
                            swal({
                                title: "Export canceled successfully"
                            }).then(function (result) {
                                //datatable re_initialise message avoid reload page
                                window.location.reload();
                            });
                        }
                    });
            });
        }
        // Get document sublist if exists when reload
        var doc_type_ids = $('#doc_type_ids').val();
        if(doc_type_ids){        
            $.ajax({
                type:'GET',
                url: base_url+'/getDocumentTypeSublis',
                data:'id='+doc_type_ids,
                success:function(response){
                   $('#sublist').html(response);
                }
            });
        }
        //var exportedRecords = 0;
        //var chunkSize = 4; // as per query performance
        //submit form
        $('#saveEdi').click(function(e){
            //e.preventDefault();
            var texta = document.forms["documentAdvSearchForm"]["doctypeid"].value;
            if(texta == "")
            {
                $(".preloader").css("display", "none");
                $(".progress_section").css("display", "none");
                swal("Document type is required");
                return false;
            }
            else
            {
            var today = new Date();
            var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
            var time = today.getHours() + "-" + today.getMinutes() + "-" + today.getSeconds();
            var dateTime = date+'_'+time;
            var skillsSelect = document.getElementById("doctypeid");
            var selectedText = skillsSelect.options[skillsSelect.selectedIndex].text;
            $(".preloader").css("display", "block");
            $(".progress_section").css("display", "block");
            $.ajax({
                type:'post',
                url: '{{URL('RecordscountExport')}}',
                dataType: "json",
                data: $('#documentAdvSearchForm').serialize(),
                success:function(response){
                    console.log(response);
                    var totalRecords = response;
                    total_ajax_call = Math.round(totalRecords/chunkSize);
                    console.log("total_ajax_call="+total_ajax_call);
                    var percentage_incr = (100/total_ajax_call);
                    req_count = 0;
                   if(totalRecords == 0) 
                   {
                    $(".preloader").css("display", "none");
                    $(".progress_section").css("display", "none");
                    swal("No documents found");
                    return false;
                   }
                   if(start < totalRecords)
                   {
                        //console.log(start);
                        chunkCSVExport(start, chunkSize,totalRecords,selectedText,dateTime,percentage_incr);
                    }
                }
            });
            }
        });

        function chunkCSVExport(start,chunkSize,totalRecords,selectedText,dateTime,percentage_incr){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var url = '{{URL('chunkExport')}}';
            var section = 'csv';
            //console.log(url);
            var xhr = $.ajax({
                type : "post",
                dataType : "json",
                url: url,
                data: $('#documentAdvSearchForm').serialize()+'&start=' + start + '&chunkSize=' + chunkSize + '&selectedText=' + selectedText + '&dateTime=' + dateTime,
                success: function(response) {
                    req_count++;
                    console.log(response);
                    var count = response.result.length;
                    total_results += count;
                    console.log("req_count="+req_count);
                    percentage_incr_last = ((req_count/total_ajax_call)*100);
                    percentage_incr_last = Math.round(percentage_incr_last);
                    if(percentage_incr_last > 100) 
                    {
                        percentage_incr_last = 100;
                    }
                    console.log(percentage_incr_last);
                    $(".progress-bar-primary").css("width", percentage_incr_last+'%');
                    $('#status_bar').text(percentage_incr_last+'%');
                    var filename = response.filename;
                    exportedRecords += chunkSize;
                    start += chunkSize;
                    if(start < totalRecords)
                        {
                            
                            chunkCSVExport(start, chunkSize,totalRecords,selectedText,dateTime,percentage_incr);
                            
                        }
                    else{
                            downloadfile(totalRecords,exportedRecords,selectedText,section,filename,dateTime);
                        }
                }
            });
            $('#cancel_export').click(function(e){ 
                var user_name = "{{Auth::user()->username}}";
                var file = "{{Config::get('constants.export_file')}}";
                var docname = user_name+file+selectedText.replace(/\s+/g, '')+'_'+dateTime+'.csv';
                e.preventDefault();
                xhr.abort();
                $(".preloader").css("display", "none");
                $(".progress_section").css("display", "none");
                swal({
                        title: "Do you want to cancel this operation?",
                        type: "{{$language['Swal_warning']}}"
                    }).then(function (result) {
                        if(result)
                        {
                            //delete file
                            $.ajax({
                                type: 'post',
                                url: 'DeleteZip',
                                data: {_token: CSRF_TOKEN,filename:docname},
                                
                                success: function(response) {
                                    console.log(response);
                                }
                            });
                            swal({
                                title: "Export canceled successfully"
                            }).then(function (result) {
                                //datatable re_initialise message avoid reload page
                                window.location.reload();
                            });
                        }
                    });
            });
        }
        function downloadfile(totalRecords,exportedRecords,selectedText,section,filename,dateTime)
        {   

            if(exportedRecords>=totalRecords){
                $(".preloader").css("display", "none");
                $(".progress_section").css("display", "none");

                    $('#myModal').modal('show');
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    var datatable = $('#example').DataTable( {
                        "processing": true,
                        "serverSide": true,
                        "searching": false,
                        "order": [[2,"desc"]],
                        "responsive": true,
                        "lengthChange":false,
                        "ajax": {
                               "url": "@php echo url('NumberZip'); @endphp",
                               "type": "POST",
                                "headers": { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                },
                        "iDisplayLength": 5,
                        "columns": [{
                                        "class":"0",
                                        "data":"filename",
                                    },
                                    {
                                        "class":"1",
                                        "data":"size",
                                    },
                                    {
                                        "class":"2",
                                        "data":"date",
                                    },
                                    {
                                        "class":"3",
                                        "data":"actions",
                                    }],
                                    "columnDefs": [
                  { targets: 'no-sort', orderable: false }
                ],
                    } );
            }
        }
        $('.modal_close').click(function(){
            $('#myModal').modal('hide');
            window.location.reload();
        })
        $('.multipleSelect').fastselect();
        var sess    =  <?php echo $sess_doctypecol ?>
        function myFunction(){
            if ((sess == "")){
                $("#TextBoxesGroup").hide();
            }
        }  

        $("#cn").click(function(){
            $("#documentAdvSearchForm")[0].reset();
            $('#documentAdvSearchForm').parsley().reset();
        });

        // Onchange function to get the sublist of the document type
        $("#tagwrdcat").change(function(){
            // remove selected items
            $('.selecte_tag').attr('selected',false);

            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
             if($("#tagwrdcat").val()==null){
                var value = 0; 
            }else{
                var value = $("#tagwrdcat").val();
            }
            $.ajax({
                type: 'post',
                url: '{{URL('documentsTagwords')}}',
                data: {_token: CSRF_TOKEN, tagcatid: value },
                
                beforeSend: function() {
                    
                },
                success: function(data, status){
                    if(data){
                        $("#keywd").hide();
                        $("#reskeywrds").html(data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(jqXHR);    
                    console.log(textStatus);    
                    console.log(errorThrown);    
                },
                complete: function() {
                    
                }
            });           
        });



        $("#clr").click(function(){ 
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'get',
                url: '{{URL('exportclear')}}',
                data: {_token: CSRF_TOKEN},
                
                beforeSend: function() {
                    $("#bs").show(); 
                },
                success: function(data, status){
                    // Reload
                    window.location.reload(true);

                    $('.fstControls').val('');
                    $("#TextBoxesGroup").hide();
                },

                complete: function() {

                },

                error: function(jqXHR, textStatus, errorThrown){
                    console.log(jqXHR);    
                    console.log(textStatus);    
                    console.log(errorThrown);    
                }
            });

        });

        var d           = new Date();
        var currentYear = d.getFullYear();
        var newDate     = currentYear+10;
        var date        = '12/31/'+newDate;

        $('#created_date_from').daterangepicker({
            singleDatePicker: true,
            "drops": "bottom",
            maxDate: moment(date),
            showDropdowns: true
        });
        $('#created_date_to').daterangepicker({
            singleDatePicker: true,
            "drops": "bottom",
            maxDate: moment(date),
            showDropdowns: true
        });
        $('#last_modified_from').daterangepicker({
            singleDatePicker: true,
            "drops": "bottom",
            maxDate: moment(date),
            showDropdowns: true
        });
        $('#last_modified_to').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
            maxDate: moment(date),
            showDropdowns: true
        });

        

    });
    //Delete single document
    function del(docname)
    {   
        if(docname=="")
        {
            docname="{{$language['document']}}";
        }
        swal({
              title: "{{$language['confirm_delete_single']}}'" + docname + "' ?",
              type: "{{$language['Swal_warning']}}",
              showCancelButton: true
            }).then(function (result) {
            if(result)
            {
                // Success
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'post',
                    url: 'DeleteZip',
                    data: {_token: CSRF_TOKEN,filename:docname},
                    
                    beforeSend: function() {
                        $("#bs").show();
                    },
                    success: function(data, status)
                    {
                        // success
                        if(data==1)
                        {
                            swal({
                            title: "{{$language['document']}} '"+docname+"' {{$language['success_delete']}}",
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ok'
                            }).then(function (result) {
                                if(result){
                                    // Success
                                    var oTable = $('#example').DataTable();
                                    // to reload
                                    oTable.ajax.reload();
                                }
                            });
                        }
                        else
                        {
                            // data=0
                            swal("File not exists");
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