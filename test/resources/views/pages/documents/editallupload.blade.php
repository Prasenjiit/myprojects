<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
  
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables/dataTables.bootstrap.css') !!}

<!--{!! Html::script('js/build.min.js') !!} 
{!! Html::style('css/build.min.css') !!} -->
{!! Html::style('css/fastselect.min.css') !!} 
{!! Html::script('js/fastselect.standalone.js') !!}   

<style>
    .file-node{
        color:#337ab7;
    }
    .fstElement { font-size: 1.2em; }
    .fstToggleBtn { min-width: 16.5em; }

    .submitBtn { display: none; }

    .fstMultipleMode { display: block; }
    .fstMultipleMode .fstControls { width: 100%; }
    .modal-body {
        min-height: 470px !important;
    }
    .fstElement{
        width: 100%;
    }
    .fstControls{
        width: 100% !important;
    }

</style>

<!-- Content Wrapper. Contains page content -->
<section class="content" id="shw">
    <section class="content-header">
        <h1>
            {{$language['documents']}}
            <small>- {{$language['edit']}} {{$language['all documents']}}</small>
            <?php
            $arr=Input::get('checkbox');
            $string= implode(',',$arr);
            $status=Input::get('hidd_status');
            ?>
        </h1>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
            <li class="active">{{$language['edit']}} {{$language['all documents']}}</li>
        </ol>   -->          
    </section>
    @if(Session::has('data'))
    <section class="content content-sty" id="spl-wrn">        
        <div class="alert alert-sty {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('data') }}</div>        
    </section>
    @endif
    <!-- Main content -->
    <section class="content">    
        <div class="modal-body">    
            <!-- form start -->
            {!! Form::open(array('url'=> array('documentsSaveAll',$status,$string), 'files'=>true, 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'documentManagementAddForm', 'id'=> 'documentManagementAddForm','data-parsley-validate'=> '')) !!}            
                <div class="form-group">
                    <label for="Doc Types" class="col-sm-2 control-label">{{$language['document types']}}: </label>
                    <div class="col-sm-8">
                        <select name="doctypeid" id="doctypeid" class="multipleSelect form-control">
                        <option value="">Select a Document Type</option>
                            <?php
                            foreach ($docType as $key => $dType) {
                                ?>
                                <option value="<?php echo $dType->document_type_id; ?>"><?php echo $dType->document_type_name;?></option>
                                <?php
                            }
                            ?>
                        </select>   
              
                    </div>
                </div>
                <div class="form-group">
                <label for="department" class="col-sm-2 control-label">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{$language['department']}} @endif: </label>
                
                <div class="col-sm-8">
                    <select name="departments[]" id="departments" class="multipleSelect" multiple>
                        <?php
                        foreach ($deptApp as $key => $val) {
                            ?>
                            <option value="<?php echo $val->department_id; ?>"><?php echo $val->department_name;?></option>
                            <?php
                        }
                        ?>
                    </select>                      
                    <span class="dms_error">{{$errors->first('docname')}}</span>
                </div>
            </div> 
           
            <div class="form-group">
                {!! Form::label($language['stack'].':', '', array('class'=> 'col-sm-2 control-label'))!!}
                <div class="col-sm-8">
                    <select name="stack[]" id="stack" class="multipleSelect" multiple>
                        <?php
                        foreach ($stack as $key => $val) {
                            ?>
                            <option value="<?php echo $val->stack_id; ?>"><?php echo $val->stack_name;?></option>
                            <?php
                        }
                        ?>
                    </select>                      
                    <span class="dms_error">{{$errors->first('docname')}}</span>
                </div>
            </div>   
            <div class="form-group">
                {!! Form::label($language['tag category'].':', '', array('class'=> 'col-sm-2 control-label'))!!}
                <div class="col-sm-8">
                    <select name="tagscate[]" id="tagwrdcat" class="multipleSelect" multiple>
                        <?php
                        foreach ($tagsCateg as $key => $tagcat) {
                            ?>
                            <option value="<?php echo $tagcat->tagwords_category_id; ?>"><?php echo $tagcat->tagwords_category_name;?></option>
                            <?php
                        }
                        ?>
                    </select>   
                   
                    
                    <span class="dms_error">{{$errors->first('docname')}}</span>
                </div>
            </div>   

            <div class="form-group">
                {!! Form::label($language['tag words'].':', '', array('class'=> 'col-sm-2 control-label'))!!}
                <div class="col-sm-8">
                    <div id="keywd">
                        <select name="keywords" id="keywords" class="form-control">
                            <option value="">{{$language['pls_select']}}</option>
                        </select>
                    </div>
                    <div id="reskeywrds"></div>
                    
                    <span class="dms_error">{{$errors->first('docname')}}</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">{{$language['expir_date']}}: </label>
                <div class="col-sm-4">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control" id="doc_exp_date" name="document_expiry_date" placeholder="YYYY-MM-DD">
                    </div><!-- /.input group -->
                </div>
                <div class="col-sm-6">
                {!! Form::checkbox('doc_exp_chk',1, true, ['id'=> 'doc_exp_chk']) !!} {{$language['no expiry']}}
                </div>
            </div>

            <div class="form-group">
                <label for="Folde path" class="col-sm-2 control-label">{{$language['upload folder']}}: </label>
                <div class="col-sm-8">
                    <div>
                        <input type="text" id="up_folder" name="up_folder" class="form-control" readonly placeholder="Choose a Document folder">
                        <input type="hidden" name="hidd_folder_id" id="hidd_folder_id" value="{{Session::get('SESS_parentIdd')}}">
                    </div>
                    <div class="box">
            
                    <!-- <span id='change_Folder' style="cursor:pointer;color: #3c8dbc;">Change Upload Folder</span> -->
                    <div id="tree-container" style="padding:5px 0px;overflow: auto;max-height: 100px;"></div>
                    
                    </div>              
                </div>
            </div>   

            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                <div class="col-sm-8">
                    @if($status=='unpublished')
                    <input type="submit" value="{{$language['save']}}" name="save" class="btn btn-primary">
                    <a href="{{url('listview')}}?view=import" value="Cancel" name="cancel" class="btn btn-primary btn-danger" id = "cn">{{$language['cancel']}}</a>
                    @endif
                    @if($status=='published')
                    <input type="submit" value="{{$language['save']}}" name="save" class="btn btn-primary">
                    <a href="{{url('listview?view=list')}}" value="Cancel" name="cancel" class="btn btn-primary btn-danger" id = "cn">{{$language['cancel']}}</a>
                    @endif
                </div>
            </div><!-- /.col -->
            {!! Form::close() !!}        
        </div>
    </section>
</section><!-- /.content --> 

<script>
    $(function ($) {

        //Initilize date picker
        var d           = new Date();
        var currentYear = d.getFullYear();
        var newDate     = currentYear+10;
        var date        = '12/31/'+newDate;

         // disable previous year
        var dd = d.getDate();
        var mm = d.getMonth()+1; 
        if(dd<10) {
            dd = '0'+dd
        } 

        if(mm<10) {
            mm = '0'+mm
        } 
        today = currentYear+'-'+mm+'-'+dd;
        $('#doc_exp_date').daterangepicker({
                singleDatePicker: true,
                "drops": "up",
                minDate: today,
                maxDate: moment(date),
                showDropdowns: true
            });
        //
        //Expire checking
        if($("#doc_exp_chk").prop('checked') == true){
            $("#doc_exp_date").attr("disabled", true);
        }  

        $('#doc_exp_chk').click(function() {
            var ckb = $("#doc_exp_chk").is(':checked');
            if(ckb == false) {
                $("#doc_exp_date").attr("disabled", false);
                $('#doc_exp_date').attr('required','required');
                $('#doc_exp_date').attr('data-parsley-required-message','Expiry date is required');
            } else {
                $("#doc_exp_date").attr("disabled", true);
                $('#doc_exp_date').attr('required',false);
                $('#doc_exp_date').val('');
            }           
        }); //

        $('.multipleSelect').fastselect();
        //Duplicate entry
        $('#spl-wrn').delay(5000).slideUp('slow');
        // Onchange function to get the sublist of the document type
        $("#tagwrdcat").change(function(){
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
                timeout: 50000,
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
    });
$(document).ready(function(){
    $('#tree-container')
                .jstree({
                    'core' : {
                        'data' : {
                            'url' : 'response?operation=get_node',
                            'data' : function (node) {
                                return { 'id' : node.id };
                            }
                        },
                        'check_callback' : true,
                        'themes' : {
                            'responsive' : false
                        }
                    },
                    types: {
                        "child": {
                          "icon" : "glyphicon glyphicon-folder-open file-node"
                        }                        
                    },  
                    'force_text' : true,
                    'plugins' : ['state','dnd','contextmenu','wholerow','types']
                })
                .on('delete_node.jstree', function (e, data)
                {
                    var folder_name=data.node.text.replace(/ *\([^)]*\) */g, "");

                    swal({
                      title: "{{$language['confirm_delete_single']}} folder '" + folder_name + "' ?",
                      type: "{{$language['Swal_warning']}}",
                      showCancelButton: true
                    }).then(function (result) {
                        if(result){
                            // Success
                            $.ajax({
                            type : 'GET',
                            url  : 'response?operation=delete_node',
                            data : {id:data.node.id},
                            complete: function(response) {
                            if(response.responseText=='true')
                            {
                            //swal("{{$language['success_del_folder']}}");
                            window.location.reload();
                            }
                            else if(response.responseText=='null')
                            {
                            //swal("{{$language['folder_check']}}");
                            window.location.reload();
                            }    
                            else if(response.responseText=='root')
                            {
                            //swal("{{$language['root_delete']}}");
                            window.location.reload();
                            }
                            else if(response.responseText=='temp')
                            {
                            //swal("{{$language['temp_folder_check']}}");
                            window.location.reload();
                            }
                            }
                            });
                        }
                        swal(
                        'Success'
                        )
                    },function (dismiss) {
                      // dismiss can be 'cancel', 'overlay',
                      if (dismiss === 'cancel') {
                        window.location.reload();
                      }
                    }); 
                })
                .on('create_node.jstree', function (e, data) {
                    $.get('response?operation=create_node', { 'id' : data.node.parent, 'position' : data.position, 'text' : data.node.text })
                        .done(function (d) {
                            data.instance.set_id(data.node, d.id);
                        })
                        .fail(function () {
                            data.instance.refresh();
                        });
                })
                .on('rename_node.jstree', function (e, data) {
                    $.get('response?operation=rename_node', { 'id' : data.node.id, 'text' : data.text })
                        .fail(function () {
                            data.instance.refresh();
                        });
                })
                .on('move_node.jstree', function (e, data) {
                    $.get('response?operation=move_node', { 'id' : data.node.id, 'parent' : data.parent, 'position' : data.position })
                        .fail(function () {
                            data.instance.refresh();
                        });
                })
                .on('copy_node.jstree', function (e, data) {
                    $.get('response?operation=copy_node', { 'id' : data.original.id, 'parent' : data.parent, 'position' : data.position })
                        .always(function () {
                            data.instance.refresh();
                        });
                })                        
                });
                $('#tree-container').on('ready.jstree', function() {
                $("#tree-container").jstree("open_all");          
                });
                $("#tree-container").on('changed.jstree', function (e, data) {
                    var path = data.instance.get_path(data.node,'/');
                    var dataString='lang=' + path;
                    var n= $("#tree-container").jstree(true).get_selected();
                    $('#hidd_folder_id').val(n);
                    $('#up_folder').val(path);
                });           
</script>
{!! Html::script('dist/jstree.min.js') !!}
{!! Html::style('dist/style.min.css') !!}
@endsection
