<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')

{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}
{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}

<section class="content-header">
    <div class="col-sm-8">
        <span style="float:left;">
            <strong>
                Duplicate Files
            </strong> &nbsp;
        </span>
        
    </div>
    <div class="col-sm-4">
       
    </div>
</section>
<section class="content" id="shw">
<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header">
                <span class="pull-right">
                    <?php
                    $user_permission=Auth::user()->user_permission;
                    include (public_path()."/storage/includes/lang1.en.php" );                         
                    ?>                        
                </span>
            </div>
            <div class="box-body">
                <table border="1" id="departmentDT" class="table table-bordered table-striped dataTable hover">
                    <thead>
                        <tr>
                            <th class="no-sort"><input type='checkbox' id='ckbCheckAll' data-orderable='false'></input>Actions</th>
                            <th nowrap>File Name</th>
                            <th nowrap>Document No.</th>
                            <th nowrap>Document Name</th>
                            <!-- agm <th>Pensioner Name</th> 
                            <th>Department</th>   -->                      
                            <th>Stack</th>                       
                            <th nowrap>Created On</th>
                            <th nowrap>Created By</th>
                            <th nowrap>Modified By</th>
                            <th>Size</th>
                            
                        </tr>
                    </thead>  
                                   
                    <tbody>                    
                        @foreach ($dglist as $key => $val)

                        <tr class="even" role="row">

                            <td class="no-sort">
                                <input name="checkbox[]" type="checkbox" value="{{$val->document_id}}" id="chk{{$val->document_id}}" class="checkBoxClass"> &nbsp;&nbsp;
                                @if(stristr($user_permission,"delete"))
                                <i title="Delete" class="fa fa-trash delDoc"  style="color: red; cursor:pointer;" data-docid="{{$val->document_id}}" data-docname="{{$val->document_file_name}}" data-filename="{{$val->document_file_name}}"></i>
                                @endif
                            </td>
                            <td>{{ $val->document_file_name }}</td>  
                            <td>{{ $val->name }}</td>
                            <td>{{ $val->department_name }}</td>
                            <td>{{ $val->stack_name }}</td>
                            <td>{{ $val->created_at }}</td>
                            <td>{{ $val->document_created_by }}</td>
                            <td>{{ $val->document_modified_by }}</td>  
                            <td>{{ $val->document_size }}</td>                        
                            
        
                        </tr>
                        @endforeach
                    </tbody>                                
                </table>
            </div><!-- /.box-body -->

        </div><!-- /.box -->
        <div class="form-group">
               <label class="col-sm-5 control-label processing_spinner text-right"></label>
               <div class="col-sm-7">
                @if(stristr($user_permission,"delete"))
                    <span id="deletetag" value="Delete" name="delete" class="btn btn-primary btn-danger">{{trans('language.delete')}}</span> &nbsp;&nbsp;
                @endif
                    <a href="{{URL('documentAdvanceSearch')}}" id="cancel_window" value="Cancel" name="Cancel" class="btn btn-primary btn-danger">{{trans('language.cancel')}}</a>
               </div>
               <input type="hidden" name="hidd_count" id="hidd_count" value="0">
           </div>
    </div><!-- /.col -->
</div><!-- /.row -->
</section>
<script>

    $(function () {
        var processing_spinner='<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>';
        var rows_per_page = '{{Session('settings_rows_per_page')}}';
        var lengthMenu = getLengthMenu();//Function in app.blade.php
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var table = $('#departmentDT').DataTable({  
            "order": [[ 1, "asc" ]],
            "visible": false,
            "searchable": false,
            "scrollX": true,
            "scrollY": 350,
            "responsive": true,
            "destroy": true,
            "lengthMenu": lengthMenu,
            "pageLength":rows_per_page,
            language: {
                searchPlaceholder: "{{trans('language.data_tbl_search_placeholder')}}"
            },
            "columnDefs": [
            {
                
            },
            {targets: 'no-sort', orderable: false}]
        });
        
        //for highlight the row
        $("#departmentDT tbody tr").on('click',function(event) {
            $("#departmentDT tbody tr").removeClass('row_selected');        
            $(this).addClass('row_selected');
        });

        $(document).on('click', '#ckbCheckAll', function ()  
        {
            $(".checkBoxClass").prop('checked', $(this).prop('checked'));
            countChecked();
        });
            var countChecked = function() 
        {
            var n = $( ".checkBoxClass:checked" ).length;
            document.getElementById('hidd_count').value=n;
        };
        //select each checkbox
        $(document).on('click', '.checkBoxClass', function () 
        {
            countChecked();
        });
        /*Delete single document*/
        $(document).on('click', '.delDoc', function ()
        {   
        var id = $(this).attr('data-docid');
        var docname = $(this).attr('data-docname');
        var filename = $(this).attr('data-filename');
        if(docname=="")
        {
            docname="{{trans('documents.document')}}";
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
                var view = "{{Session::get('view')}}";
                $.ajax({
                    type: 'post',
                    url: 'documentDelete',
                    data: {_token: CSRF_TOKEN, id:id,docname:filename,view:view},
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
                            title: "{{trans('documents.document')}} '"+docname+"' {{trans('language.success_delete')}}",
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ok'
                            }).then(function (result) {
                                if(result){
                                    // Success
                                    window.location.reload();
                                    //datatable.ajax.reload( null,false);    
                                }
                            });
                        }
                        else
                        {
                            // data=0
                            swal("{{trans('documents.contact_admin')}}");
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
        });



        //Delete selected
        $("#deletetag").click(function()
        {
            if(document.getElementById('hidd_count').value==0)
            {
                swal("{{trans('documents.not select')}}");
                return false;
            }
            else
            {   
                swal({
                    title:"{{trans('documents.confirm_delete_multiple')}}",
                    text: "{{trans('language.Swal_not_revert')}}",
                    type: "{{trans('language.Swal_warning')}}",
                    showCancelButton: true
                }).then(function (result) {
                if(result)
                {
                    $(".processing_spinner").html(processing_spinner);
                    // Success
                    var arr = $('input:checkbox.checkBoxClass').filter(':checked').map(function () {
                    return this.value;
                    }).get();
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    var view = "{{trans('documents.list_view')}}";
                    $.ajax({
                        type: 'post',
                        url: 'deleteSelected',
                        data: {_token: CSRF_TOKEN,selected:arr,view:view},
                        timeout: 50000,
                        beforeSend: function() {
                            $("#bs").show();
                            
                        },
                        success: function(data, status){
                            $("#ckbCheckAll").prop('checked', false);
                            window.location.reload();
                            //datatable.ajax.reload( null,false);
                            $(".processing_spinner").html("");
                            swal(data)
                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            $(".processing_spinner").html("");
                            console.log(jqXHR);    
                            console.log(textStatus);    
                            console.log(errorThrown);    

                        },
                        complete: function() {
                            $("#bs").hide();
                            
                        }
                    });
                }
                $('#hidd_count').val(0);
                });
            }   
        });
       
    });
</script>  
@endsection