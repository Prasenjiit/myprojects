<?php
include (public_path()."/storage/includes/lang1.en.php" );
$user_permission=Auth::user()->user_permission;
?>
@extends('layouts.app')
@section('main_content')

{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('css/jquery-ui.css') !!} 
{!! Html::script('js/jquery-ui.min.js') !!}  
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}
{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
<!-- Content Wrapper. Contains page content -->
<style type="text/css">
    .fstElement{
        width: 100%;
    }
    .modal-body {
        min-height: 470px !important;
    }
    .fstControls{
        width: 100% !important;
    }
    a {
        color: #3c8dbc;
    }
    .sublabel{
        padding-top: 7px;
        
    }
    #sub-label{
        text-align: center;
        font-weight: 400;
    }
    .box-header{
        padding: 0px !important;
    }
    .box{
        border-top: 0px !important;
    }
    #typeselect1{
        margin-top: -8px;
        margin-bottom: 5px;
        font-size: 12px;
    }
    @media(max-width:991px){
        .content-header {
            height: 86px;
        }
    }
    table.dataTable{
    border-collapse:collapse;
    }
    .expiresoon{
        border-left: 3px solid #9900ff;
    }
    .expired{
        border-left: 3px solid #cc0066;
    }
    .noexpire{
        border-left: 3px solid #996600;
    }
    #labeldoc{
        margin-left: 20px;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<section class="content-header">
     <div class="col-sm-3">    
        <!-- heading of page --> 
        <strong>{{$language['documents']}}</strong>
        <?php foreach ($doc_type_id as $key => $value) {
           $document_type_id=@$value->document_type_id;
        }
        foreach ($doc_expiry as $key => $value) {
           $document_expiry=$value->document_expiry_date;
        }
        ?>
        <small>- {{$language['related documents']}}</small>
    </div>
    <div class="col-sm-6" style="font-size:12px;">    
        {{ $language['color_legend'] }} <button type="button" style="margin-left: 10px; cursor: context-menu; padding: 1px 12px !important; " class="btn btn-warning-btn"></button>&nbsp;{{$language['soon_tobe_expired']}} <?php echo Session::get('settings_document_expiry')." days"; ?>
        <button type="button" style="margin-left: 10px; cursor: context-menu; padding: 1px 12px !important; " class="btn btn-danger-btn"></button>&nbsp;{{$language['expired_docs']}}
        <button type="button" style="margin-left: 10px; padding: 1px 12px !important; cursor: context-menu;" class="btn btn-success-btn"></button>&nbsp;{{$language['all_other_docs']}}
    </div>
    <div class="col-sm-3" style="font-size:12px;">
   <!--  <ol class="breadcrumb">
            <li><a href="<?php echo  url('/home');?>"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
            <li class="active"><a href="<?php echo  url('/documents');?>">{{$language['documents']}}</a></li>
        </ol> -->   
    </div>
    @if(session('status'))
        <div class="alert alert-warning" id="hide-div">
            <p style="text-align: center;"><strong>Warning!</strong> {{ session('status') }}</p>
        </div>
    @endif
             
</section>

<!--Checking view permission-->
@if(stristr($user_permission,'view'))
<div class="modal fade" id="userAddModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dGAddModal" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body" style="min-height: 170px !important;">
                <div class="form-group">
                    {{$language['confirm_checkout_single']}}<sapn class="share-doc-name"></sapn>
                </div>
                <div class="form-group">
                    <label for="Comments" class="col-sm-12 control-label">{{$language['comments']}}:<span style="color:red">*</span></label>
                    <div class="col-sm-12">
                        {!! Form:: 
                        text('comments','', 
                        array( 
                        'class'=> 'form-control', 
                        'id'=> 'comments', 
                        'title'=> "'".$language['full name']."'".$language['length_others'], 
                        'placeholder'=> $language['comments'],
                        'autofocus',                
                        )
                        ) 
                        !!}
                        <input type="hidden" id="hidd-doc-id" name="hidd_doc_id">
                        <input type="hidden" id="hidd-doc-name" name="hidd_doc_name">
                        <input type="hidden" id="hidd-doc-count" name="hidd_doc_count">    
                        <span class="dms_error">{{$errors->first('name')}}</span>       
                        <span class="null_error" style="color:red"></span>        
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-5 control-label"></label>
                    <div class="col-sm-7" id="button-section" style="padding-top: 10px;">
                        {!!Form::submit($language['save'], array('class' => 'btn btn-primary sav_btn','id'=>'comment_save')) !!} &nbsp;&nbsp;
                            {!!Form::button($language['cancel'], array('class' => 'btn btn-primary btn-danger', 'id' => 'cn', 'data-dismiss' => 'modal')) !!}
                        </div>
                </div><!-- /.col -->
                {!!Form::close()!!}
            </div>
        </div>
    </div>
</div>  
<div class="modal-body">
    <div class="box box-info" style="overflow-x: auto;">
        <div class="box-body">
            <table border="1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                 <thead>
                    <tr> 
                        <th nowrap="nowrap" width="10%">{{$language['actions']}}</th>
                        <th nowrap="nowrap" width="10%">{{$language['document type']}}</th>
                        <th nowrap="nowrap" width="10%">{{$settings_document_no}}</th>
                        <th nowrap="nowrap" width="10%">{{$settings_document_name}}</th>
                        <th nowrap="nowrap" width="10%">{{$language['department']}}</th>
                        <th nowrap="nowrap" width="10%">{{$language['stack']}}</th>
                        <th nowrap="nowrap" width="10%">{{$language['expir_date']}}</th>
                        <!--For Document column name and values-->
                        @foreach($doc_columns as $val)
                        <th nowrap="nowrap" width="10%">{{$val->document_column_name}}</th>
                        @endforeach
                        <!--For Document column name and values-->
                                                        
                    </tr>
                </thead>  
                <tbody>
                <?php
                    $user_permission=Auth::user()->user_permission;     
                ?>
                @foreach ($dglist as $key => $val)
                    <?php  $stckarr = explode(',',$val->stack_id);
                    $deptarr = explode(',',$val->department_id);
                    $dtyparr = explode(',',$val->document_type_id);

                    $valuearr= @$val->document_type_columns; 
                    $stackval = "";   
                    $doctypeval = "";    
                    $deptval = "";                                   
                    for($i=0;$i<count($stckarr);$i++){ ?>
                        @foreach ($stacks as $key => $stval)
                            @if($stckarr[$i] == $stval->stack_id)
                                <?php if($i==count($stckarr)-1){
                                    $stackval = $stackval.$stval->stack_name;
                                }else{
                                    $stackval = $stackval.$stval->stack_name.', ';
                                } ?>
                            @endif
                        @endforeach   
                    <?php }

                    for($i=0;$i<count($deptarr);$i++){ ?>
                        @foreach ($depts as $key => $dtval)
                            @if($deptarr[$i] == $dtval->department_id)
                                <?php if($i==count($deptarr)-1){
                                    $deptval = $deptval.$dtval->department_name;
                                }else{
                                    $deptval = $deptval.$dtval->department_name.', ';
                                } ?>
                            @endif
                        @endforeach   
                    <?php }

                    for($i=0;$i<count($dtyparr);$i++){ ?>
                        @foreach ($docType as $key => $stval)
                            @if($dtyparr[$i] == $stval->document_type_id)
                                <?php if($i==count($dtyparr)-1){
                                    $doctypeval = $doctypeval.$stval->document_type_name;
                                }else{
                                    $doctypeval = $doctypeval.$stval->document_type_name.', ';
                                } ?>
                            @endif
                        @endforeach   
                    <?php }
                    if(($val->document_expiry_date > date('Y-m-d'))){
                        $todaydate = date('Y-m-d'); // or your date as well
                        $docexpdate = $val->document_expiry_date;
                        $datediff = abs(strtotime($docexpdate) - strtotime($todaydate));
                        $noofdays = round($datediff / (60 * 60 * 24));                
                    }else{
                        $noofdays = 0;
                    } 
                    ?>    

                <tr  role="row" <?php if(($val->document_expiry_date != null) && ($val->document_expiry_date <= date('Y-m-d'))){?> class="even expired"<?php }else if(($noofdays!=0)&&($noofdays<Session::get('settings_document_expiry'))){ ?>class="even expiresoon"<?php }else{ ?>class="even noexpire" <?php } ?>>    
                    <td>
                    <div class="btn-group">
                  
                      <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" title="Actions">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                        <ul class="dropdown-menu" role="menu" style="min-width: 125px;">
                        &nbsp;
                        <a title="{{$language['open document']}}" href="{{ url('documentManagementView') }}?dcno={{ $val->document_id }}&page=list">
                        <?php 
                    $ext = pathinfo($val->document_file_name, PATHINFO_EXTENSION);
                    if($ext=='pdf'){?>
                        <li class="fa fa-file-pdf-o"></li>
                    <?php
                    }
                    elseif ($ext=='png'||$ext=='jpg'||$ext=='jpeg'||$ext=='tiff'||$ext=='tif'||$ext=='TIFF'||$ext=='TIF') {?>
                        <li class="fa fa-file-image-o"></li>
                    <?php
                    }
                    elseif ($ext=='docx'||$ext=='doc') {?>
                        <li class="fa fa-file-word-o"></li>
                    <?php
                    }
                    elseif ($ext=='txt') {?>
                        <li class="fa fa-file-text-o"></li>
                    <?php
                    }
                    elseif ($ext=='zip'||$ext=='rar') {?>
                        <li class="fa fa-file-archive-o"></li>
                    <?php
                    }
                    elseif ($ext=='xls'||$ext=='xlsx') {?>
                        <li class="fa fa-file-excel-o"></li>
                    <?php
                    }
                    elseif ($ext=='wav'||$ext=='mp3'||$ext=='ogg') {?>
                        <i class="icon fa fa-file-sound-o"></i>
                    <?php
                    }
                    elseif ($ext=='flv'||$ext=='mp4'||$ext=='ogv'||$ext=='webm') {?>
                        <i class="icon fa fa-file-video-o"></i>
                    <?php
                    }
                    else{?>
                        <li class="fa fa-file-o"></li>
                    <?php
                    }
                    ?>
                    </a>
                        &nbsp;
                        <a title="{{$language['history']}}" href="{{ url('documentHistory', $val->document_id ) }}"><i class="fa fa-history" ></i></a>
                        &nbsp;
                        <!-- Check Out Document -->
                        @if(stristr($user_permission,"checkout"))
                        @if($val->document_status == 'Published')
                        <a href="javascript:void(0);"  title="{{$language['check out']}}" onclick="return myFunction('{{$val->document_file_name}}','{{$val->document_id}}','{{$i}}')"><i class="fa fa-share"></i></a>
                        @elseif($val->document_status=='Checkout')
                        <a href="javascript:void(0);" title="{{$language['check out']}}" onclick="return swal('\'{{$val->document_name}}\' is curently Checked Out by \'{{$val->document_modified_by}}\'. It must be Checked In first before you can perform this operation.')"><i class="fa fa-share" ></i></a>
                        @elseif($val->document_status=='Review')
                        <a href="javascript:void(0);" title="{{$language['check out']}}" onclick="return swal('\'{{$val->document_name}}\' is curently under the review')"><i class="fa fa-share" ></i></a>
                        @endif
                        @endif
                        &nbsp;
                        @if(stristr($user_permission,"edit"))
                                <a title="{{$language['edit_document']}}" href="{{route('editAllDocument', array('id'=>$val->document_id))}}&page=documentsList&status={{$val->document_status}}">
                                <i class="fa fa-pencil"></i></a>
                        @endif
                        &nbsp;
                        @if(stristr($user_permission,"delete"))
                            @if($val->document_status!='Checkout')
                                <i title="{{$language['delete']}}" class="fa fa-close" onclick="del({{ $val->document_id}},'{{$val->document_name }}')" style="color: red; cursor:pointer;"></i>
                            @else
                                <i title="{{$language['delete']}}" class="fa fa-close" onclick="return swal('\'{{$val->document_name}}\' is curently Checked Out by \'{{$val->document_modified_by}}\'. It must be Checked In first before you can perform this operation.')" style="color: red; cursor:pointer;"></i>
                            @endif
                        @endif
                        &nbsp;
                        <a count="<?php echo $val->document_id;?>" id="moredet_current" data-toggle="modal"  style="cursor:pointer; padding-left:2px; padding-right:2px;" data-target="#viewmoreModal" title="More Details" ><i class="fa fa-ellipsis-v" aria-hidden="true"></i></a>
                        &nbsp;
                        </ul>
                    </div>
                    </td>                          
                    <td>{{ ucfirst($doctypeval) }}</td>
                    <td>{{ $val->document_no }}</td>
                    <td>{{ ucfirst($val->document_name) }}<br/>Ver {{ $val->document_version_no }}</td>
                    <td>{{ ucfirst($deptval) }}</td>
                    <td nowrap="nowrap">{{ ucfirst($stackval)}}</td>
                    <td nowrap="nowrap">{{ $document_expiry }}</td>
                    <!--Document column name and values-->
                    @foreach($doc_values as $val)
                        <td>{{$val->document_column_value}}</td>
                    @endforeach
                    <!--Document column name and values-->                              
                </tr>
                @endforeach                           
                </tbody>               
                </table>
                <?php
                    foreach($doc_values as $val)
                    {
                    $arr_values[]=$val->document_column_value;
                    }
                ?>
                <div class="col-sm-6">
                <p>
                Choose the tagword from the list below with which the documents are linked:
                </p>
                </div>
                <div class="col-sm-6">
                <p>
                {{$language['related title']}}
                </p>
                </div>
                <div class="col-sm-3">
                    <select class="form-control" id="tagselect" name="tagselect">
                        <option value="0" doc_value='0'>Select a tagword to link</option>
                        
                        <?php
                        
                        foreach($tagwords as $val){
                        ?>
                        <option value="{{$val->tagwords_id}}" doc_value='{{$val->tagwords_id}}'>{{$val->tagwords_title}}</option>
                        <?php
                        
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <input type="button" class="btn btn-primary" id="real-tagword-button" value="{{$language['search']}}" title="{{$language['search']}}" onClick="GetSelectedTag('tagselect');">
                    </div>
                </div>
                <div class="col-sm-3">
                    <select class="form-control" id="typeselect" name="typeselect">
                        <option value="0" doc_value='0'>{{$language['select_index_to_link']}}</option>
                        <option value="{{$settings_document_name}}" doc_value='{{$name}}'>{{$settings_document_name}}</option>
                        <option value="{{$settings_document_no}}" doc_value='{{$no}}'>{{$settings_document_no}}</option>
                        <?php
                        $i=0;
                        foreach($doc_columns as $val){
                        ?>
                        <option value="{{$val->document_column_name}}" doc_value='{{$arr_values[$i]}}'>{{$val->document_column_name}}</option>
                        <?php
                        $i++; 
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <input type="button" class="btn btn-primary" id="real-import-button" value="{{$language['search']}}" title="{{$language['search']}}" onClick="GetSelectedItem('typeselect');">
                        <input type="button" value="{{$language['cancel']}}" name="cancel" class="btn btn-primary btn-danger" id = "cn" onclick="window.history.go(-1); return false;">
                        
                    </div>
                </div>
            </div>
        </div>

        <!-- tabs -->
        <div id="tabs">
          <ul>
            <li><a href="#tabs-1" style="color: #333 !important;">Related Documents</a></li>
            <li><a href="#tabs-2" style="color: #333 !important;">Previous Versions</a></li>
          </ul>
          <div id="tabs-1">
            <div class="box-header">
                <div class="col-sm-3"></div> 
                <div class="col-sm-2">    
                    <label class="control-label" id="labeldoc">{{$language['document type']}} :</label>
                </div>
                <div class="col-sm-3">    
                    <select class="form-control" id="typeselect1" name="typeselect1">
                        <option value="0" selected="">{{($language['all document types'])}}</option>
                        @foreach($doctypeApp as $val)
                        <option value="{{$val->document_type_id}}">{{$val->document_type_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4"></div> 
            </div>
            <div class="box" id="result_div" style="display: none;">
            <div class="box-body" id="table_head" style="overflow: auto;max-height: 250px;">
            <!--loading img-->
                <div id="loading" style="text-align: center;">
                    <img src="{{ URL::to('/') }}/images/loading/loading.gif"> 
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 control-label"></label>
                <div class="col-sm-7">
                @if(stristr($user_permission,'edit')) 
                    {!!Form::submit('Edit', array('class' => 'btn btn-primary','id'=>'editall')) !!} &nbsp;&nbsp;
                @endif
                @if(stristr($user_permission,"delete"))
                    <span id="deletetag" value="Delete" name="delete" class="btn btn-primary">{{$language['delete']}}</span> &nbsp;&nbsp;
                @endif
                
                </div>
            </div>
            </div>
          </div>
          <div id="tabs-2">
            <div class="box" id="result_div_prev" style="display: none;">
            <div class="box-body" id="table_head_prev" style="overflow: auto;max-height: 250px;">
            <!--loading img-->
                <div id="loading" style="text-align: center;">
                    <img src="{{ URL::to('/') }}/images/loading/loading.gif"> 
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 control-label"></label>
                <div class="col-sm-7">
                @if(stristr($user_permission,"delete"))
                <span id="deletetag_previous" value="Delete" name="delete_previous" class="btn btn-primary">{{$language['delete']}}</span> &nbsp;&nbsp;
                @endif
                </div>
            </div>
            </div>
          </div>
        </div>

</div>
</div>
@else
    <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{$language['dont_hav_permission']}}</div></section>
@endif
<!--View more content-->
<div class="modal fade" id="viewmoreModal" data-backdrop="true" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="height:600px; overflow-y:scroll;">
            <div class="modal-header" style="border-bottom-color: deepskyblue;">
                <h4 class="modal-title" style="float:left; width:90%;">
                    {{$language['all documents']}}
                    <small>- View All Data</small>
                </h4>
                <a href="javascript:void(0);">
                    <button class="btn btn-primary btn-danger" id="cn" data-dismiss="modal" type="button">{{@$language['close']}}</button>
                </a>
            </div>
            <div class="modal-body" id="more">   
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var document_id = '<?php echo $id;?>';
   $.ajax({
        type: 'post',
        url: '{{URL('relateResultPrevious')}}',
        data:{_token:CSRF_TOKEN,document_id:document_id},
        success:function(response){
            $('#result_div_prev').show();
            $('#table_head_prev').html(response);
        }
    });
});
$('#typeselect1').change(function(){
    var e = document.getElementById('typeselect');
    var doc_col_name = e.options[e.selectedIndex].value;
    var doc_col_value = e.options[e.selectedIndex].getAttribute('doc_value');
    if(doc_col_value == '0')
    {
        //swal("Please choose a Document type index.");
        return false;
    }
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var doc_type = '<?php echo @$document_type_id;?>';

    var document_id = '<?php echo $id;?>';
    var selected_type=$("#typeselect1").val(); 
    $.ajax({
        url : '{{URL('relateResult')}}',
        data:{_token:CSRF_TOKEN,sel_col:doc_col_name,sel_value:doc_col_value,doctype_id:doc_type,selected_type:selected_type,document_id:document_id},
        type:"post",
        success:function(data,response)
        {
            $('#table_head').html(data);
        }
    });
});
  $( function() {
    $( "#tabs" ).tabs();
    var e = document.getElementById('typeselect');
    var doc_col_name = e.options[e.selectedIndex].value;
    var doc_col_value = e.options[e.selectedIndex].getAttribute('doc_value');
    if(doc_col_value == '0')
    {
        //swal("Please choose a Document type index.");
        return false;
    }
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var doc_type = '<?php echo @$document_type_id;?>';
    var document_id = '<?php echo $id;?>';
    $.ajax({
        type: 'post',
        url: '{{URL('relateResult')}}',
        data:{_token:CSRF_TOKEN,sel_col:doc_col_name,sel_value:doc_col_value,doctype_id:doc_type,document_id:document_id},
        success:function(response){
            $('#result_div').show();
            $('#table_head').html(response);
        }
    });
    $.ajax({
        type: 'post',
        url: '{{URL('relateResultPrevious')}}',
        data:{_token:CSRF_TOKEN,sel_col:doc_col_name,sel_value:doc_col_value,doctype_id:doc_type,document_id:document_id},
        success:function(response){
            $('#result_div_prev').show();
            $('#table_head_prev').html(response);
        }
    });
  });
/*<--check out model-->*/
    function myFunction(docname,docid,count) {
        $('#userAddModal').modal('toggle');
        $('#userAddModal').modal('show');
        $('.share-doc-name').text(docname+' ?');
        $("#hidd-doc-id").val(docid);
        $('#hidd-doc-name').val(docname);
        $('#hidd-doc-count').val(count);
    }
        $('#comment_save').click(function(){
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var docid=$("#hidd-doc-id").val();
        var docname=$('#hidd-doc-name').val();
        var comments=$('#comments').val();
        var count=$('#hidd-doc-count').val();

        if(comments){
            // success
            $('.null_error').html('')
            $.ajax({
                type: 'post',
                url: '{{URL('commentAdd')}}',
                data: {_token:CSRF_TOKEN,hidd_doc_id:docid,hidd_doc_name:docname,comments:comments },
                beforeSend: function() {
                    
                },
                success: function(data){
                    $('#userAddModal').modal('hide');
                    $('#status'+count).text('Checkout');
                    window.location.href = 'download';

                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(jqXHR);    
                    console.log(textStatus);    
                    console.log(errorThrown);    
                },
                complete: function() {
                    
                }
            });
        }else{
            // null
            $('.null_error').html('Please fill Check Out Comments.')
        }     
    });/*<--check out model-->*/
        function GetSelectedItem(el)
        {
            var e = document.getElementById(el);
            var doc_col_name = e.options[e.selectedIndex].value;
            var doc_col_value = e.options[e.selectedIndex].getAttribute('doc_value');
            if(doc_col_value == '0')
            {
                swal("Please choose a Document type index.");
                return false;
            }
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var doc_type = '<?php echo @$document_type_id;?>';
            var document_id = '<?php echo $id;?>';
            $.ajax({
                type: 'post',
                url: '{{URL('relateResult')}}',
                data:{_token:CSRF_TOKEN,sel_col:doc_col_name,sel_value:doc_col_value,doctype_id:doc_type,document_id:document_id},
                success:function(response){
                    $('#typeselect1').val('0');
                    $('#result_div').show();
                    $('#table_head').html(response);
                }
            });
            $.ajax({
                type: 'post',
                url: '{{URL('relateResultPrevious')}}',
                data:{_token:CSRF_TOKEN,sel_col:doc_col_name,sel_value:doc_col_value,doctype_id:doc_type,document_id:document_id},
                success:function(response){
                    $('#result_div_prev').show();
                    $('#table_head_prev').html(response);
                }
            });
        }
        function GetSelectedTag(el)
        {
            var e = document.getElementById(el);
            var document_id = '<?php echo $id;?>';
            var tag = $('#tagselect').find(":selected").val();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: '{{URL('relateResultTag')}}',
                data:{_token:CSRF_TOKEN,document_id:document_id,tag:tag},
                success:function(response){
                    //$('#typeselect1').val('0');
                    $('#result_div').show();
                    $('#table_head').html(response);
                }
            });
            $.ajax({
                type: 'post',
                url: '{{URL('relateResultPrevious')}}',
                data:{_token:CSRF_TOKEN,document_id:document_id},
                success:function(response){
                    $('#result_div_prev').show();
                    $('#table_head_prev').html(response);
                }
            });
        }
        function del(id,docname)
        {   

            // Delete
            swal({
                  title: "{{$language['confirm_delete_single']}}'" + docname + "' ?",
                  text: "{{$language['Swal_not_revert']}}",
                  type: "{{$language['Swal_warning']}}",
                  showCancelButton: true
                }).then(function (result) {
                    if(result){
                        // Success
                       var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                        $.ajax({
                            type: 'post',
                            url: '{{URL('documentDelete')}}',
                            dataType: 'json',
                            data: {_token: CSRF_TOKEN, id:id,docname:docname},
                            timeout: 50000,
                            beforeSend: function() {
                                $("#bs").show();
                            },
                            success: function(data, status){
                                //swal(data);
                                window.location.reload();
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
                    swal(
                    '{{$language['Swal_deleted']}}'
                    )
                });
        }
        function delhistory(id,docname)
        {   

            // Delete
            swal({
                  title: "{{$language['confirm_delete_single']}}'" + docname + "' ?",
                  text: "{{$language['Swal_not_revert']}}",
                  type: "{{$language['Swal_warning']}}",
                  showCancelButton: true
                }).then(function (result) {
                    if(result){
                        // Success
                        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                        $.ajax({
                            type: 'post',
                            url: '{{URL('documentDeleteHistory')}}',
                            dataType: 'json',
                            data: {_token: CSRF_TOKEN, id:id,docname:docname},
                            timeout: 50000,
                            beforeSend: function() {
                                $("#bs").show();
                            },
                            success: function(data, status){
                                //swal(data);
                                window.location.reload();
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
                    swal(
                    '{{$language['Swal_deleted']}}'
                    )
                });

        }

        $(function () {

            $('body').on('click','#moredet',function(){
                var count = $(this).attr('count');
                // Get data
                var docid      = $('#doc_id'+count+'').val();
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'get',
                    url: '{{URL::route('documentsMoreDetails_relate')}}',
                    dataType: 'html',
                    data: {_token: CSRF_TOKEN,docid:docid},
                    timeout: 50000,
                    success: function(data, status){     
                        $("#more").html(data);
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                      console.log(jqXHR);    
                      console.log(textStatus);    
                      console.log(errorThrown);    
                    }
                });    

               
            });

            $('body').on('click','#moredet_previous',function(){
                var count = $(this).attr('count');
                // Get data
                var docid      = $('#document_id_history'+count+'').val();
                var version    = $('#doc_version_history'+count+'').val();
                var doc_history_id = $('#doc_history_id'+count+'').val();
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'get',
                    url: '{{URL::route('documentsMoreDetailsPrevious')}}',
                    dataType: 'html',
                    data: {_token: CSRF_TOKEN,docid:docid,version:version,doc_history_id:doc_history_id},
                    timeout: 50000,
                    success: function(data, status){     
                        $("#more").html(data);
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                      console.log(jqXHR);    
                      console.log(textStatus);    
                      console.log(errorThrown);    
                    }
                });    

               
            });
        
            $('body').on('click','#moredet_current',function(){
                
                // Get data
                var docid      = $(this).attr('count');
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                var view = 'list';
                $.ajax({
                    type: 'get',
                    url: '{{URL::route('documentsMoreDetails')}}',
                    dataType: 'html',
                    data: {_token: CSRF_TOKEN,docid:docid,view:view},
                    timeout: 50000,
                    success: function(data, status){     
                        $("#more").html(data);
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                      console.log(jqXHR);    
                      console.log(textStatus);    
                      console.log(errorThrown);    
                    }
                });    

               
            });
        });
</script>
@endsection