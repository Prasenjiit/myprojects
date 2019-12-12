<?php
include (public_path()."/storage/includes/lang1.en.php" );
$user_permission=Auth::user()->user_permission;   
$annotation_status = 1;
?>
@extends('layouts.app')
@section('main_content')
 
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!}
<!-- annotation -->
@if(Request::segment(1) == 'documentManagementView')  
    <!--Anotation-->
    {!!Html::style('css/annotation.css')!!}
    <!--For image rotation-->
    {!!Html::style('css/rotate-zoom/jquery.iviewer.css')!!}
    {!!Html::style('css/rotate-zoom/custom.css')!!}
    <!--//For image rotation-->
@endif

{!!Html::style('build/mediaelementplayer.css')!!}

{!! Html::script('lib/html5/ControlUtils.js') !!}<!--For load pdf files-->
{!! Html::script('lib/WebViewer.js') !!}
{!! Html::script('colorbox/jquery.colorbox-min.js') !!} 


<!--Anotation-->
@if(Request::segment(1) == 'documentManagementView')
  {!!Html::script('js/jquery-ui.min.js')!!}
  {!!Html::script('js/jquery.annotate.js')!!}
@endif
<!-- Player -->
<!-- <link rel="stylesheet" href="https://cdn.plyr.io/3.3.7/plyr.css">
<script src="https://cdn.plyr.io/3.3.7/plyr.js"></script> -->
<!-- end Player -->

<script>

$( document ).ready(function() {
    // Add class for minimize side bar 
    $('.sidebar-mini').addClass('sidebar-collapse');
});
</script>


<style>


/*<--For image rotate and zoom-->*/
video{
    width: 100%;height:450px;min-width: 217px;
}
#shw{
    min-height: 520px;
}
.viewer
    {
        width: auto;
        height: 642px;
        position: relative;
    }
.tabs{
        position: relative;
        top: 1%;
        background-color: #edf0f5;
        border: 2px solid #d2d6de;
        z-index: 0;
    }
.resizewrapperiframe{
        overflow-y: hidden;
        overflow-x: scroll;
        width:100%;
        height: 100%;
        min-height: 590px;
    }
    .resizable {
        resize: both;
        overflow-y: hidden;
        overflow-x: scroll;
        border: 1px solid black;
        min-height: 590px;
    }
.set_width{
    width:100%;
}
/*<--// For image rotate and zoom-->*/

/*<--Note-->*/
#newnotetxt{
    width: 99% !important;
    margin-left: 4px !important;
}

ol, ul {
    margin-left: -29px;
}

.col-md-1 {
    width: 3.333%;
}

.col-md-11{
    width: 96.667%;
}

#mini-documents {
    margin-left: 20px;
}

.mandatory{
    padding-left: 5px;
    font-size: 12px;
    color: #FF0000;
}

.rotate {
  font-family: verdana, sans-serif;
  -webkit-transform: rotate(270deg);
  -moz-transform: rotate(270deg);
  -ms-transform: rotate(270deg);
  -o-transform: rotate(270deg);
  transform: rotate(270deg);
  cursor: pointer;
  border: 1px solid #a5a5a5;
}

.vertical_menu ul {
    list-style-type: none;
    padding: 0;
}

.vertical_menu li a {
    display: block;
    height: 22px;
    font-size: 9px;
    width: 100px;
    color: #000;
    padding: 4px;
    text-decoration: none;
    text-align: center;
    margin-top: 78px;
}
.first {
    display: block;
    height: 22px;
    font-size: 9px;
    width: 100px;
    color: #000;
    padding: 4px;
    text-decoration: none;
    text-align: center;
    margin-top: 40px !important;
}

.vertical_menu li a.active {
    background-color: #4CAF50;
    color: white !important;
}

.vertical_menu li a:hover:not(.active) {
    background-color: #555;
    color: white !important;
}
.dispoff{
    display: none;
}
.evncol{
    height: 40px;
    background: #FFFFFF;
}
.odd{
    padding-top: 8px;
    background-color: #FFF;
}
.sidbox{
    width: 100%;
}
.rowstyllft{
    padding-left: 5px;
    width:  150px;
    border-right: 1px solid #EEEEEE;
}
.rowstylmdl{
    padding-left: 5px;
    border-right: 1px solid #EEEEEE;
}
.rowstylrgt{
    padding-right: 5px;
    padding-left: 5px;
}
table a{
    float: right;
    padding-right: 10px;
    color: #FFFFFF;
    cursor: pointer;
}
/*.boxwidth{
    width: 65%;
}*/

table a:hover,a:focus,a:active{
    color: #FFFFFF;
}
canvas {
      max-width: 100%;
      }

.success-word{
    color: green;
    float: right;
}

/*Mobile view*/
/*Managing in each screen*/

@media(max-width:986px){
    .preview_menu{
        padding-left: 6% !important;
    }
}
@media(max-width:800px){
    .preview_menu{
        padding-left: 10% !important;
    }
}
@media(max-width:391px){
    .preview_menu{
        padding-left: 14% !important;
    }
}
@media(max-width:360px){
    .preview_menu{
        padding-left: 13% !important;
    }
}
@media(max-width:320px){
    .preview_menu{
        padding-left: 17% !important;
    }
}

/*To hide breadcrump in mobile view*/
@media(max-width:991px){
    .breadcrumb {
        display:none;
    }
}

@media(max-width:1011px){
    #mini-documents{
        width:63% !important;
    }
}

@media(max-width:400px){
    #mini-documents{
        width: 82% !important;
    }
}

/*<--For image annotation-->*/
<?php if(Input::get('annotation') == 'yes'):?>
/*For chrome*/
.box {
    width: max-content;
}
/*For firefox*/
.box {
    width: -moz-max-content;
}
/*For Safari and a lot of the Chromium forks*/
.box {
    width: -webkit-max-content;
}
/*For IE10*/
.box {
    width: -ms-max-content;
}
/*For Opera*/
.box {
    width: -o-max-content;
}
<?php endif;?>
</style>
    
<!--To make url from diff pages. Because javascript:history.go(-1) not working when you go to image annotation page-->
<?php 
    $page = Input::get('page');
    $dcno = Input::get('dcno');
    $doc_url = config('app.doc_url');
    // if(Input::get('dsd_id')):
    //     Session::put('dsd_id',Input::get('dsd_id'));
    //     endif;
    //print_r(Session::get('dsd_id'));
    
    switch($page){
        case "document":
                    $url = URL('documents').'?saved_search=1';// Back url
                    $pge = 'document';
                    break;
        case "list":
                    $url = URL('listview').'?view=list&saved_search=1';
                    $pge = 'documentsList';
                    break;
        case "import":
                    $url = URL('listview').'?view=import&saved_search=1';
                    $pge = 'import';
                    $doc_url = config('app.temp_document_url');
                    break;
        case "checkout":
                    $url = URL('listview').'?view=checkout&saved_search=1';
                    $pge = 'checkout';
                    break;
        case "stack": 
                    $url = URL('listview').'?view=stack&id='.Session::get('dsd_id').'&saved_search=1';
                    $pge = 'stack';
                    break; 
        case "documentType":
                    $url = URL('listview').'?view=documentType&id='.Session::get('dsd_id').'&saved_search=1';
                    $pge = 'documentType';
                    break;
        case "department":
                    $url = URL('listview').'?view=department&id='.Session::get('dsd_id').'&saved_search=1';
                    $pge = 'departments';
                    break;
        case "content"   :
                    $url = URL('documentAdvanceSearch').'?page=documents'; 
                    $pge = 'content';
                    break;
        case "forms"   :
                    $url = URL('forms'); 
                    $pge = 'forms';
                    break;
        case "history"   :
                    $url = URL('relatedsearch/'.$dcno); 
                    $pge = 'history';
                    $doc_url = config('app.doc_backup_url');
                    break;
    }
    // For annotation
    if(Input::get('annotation')):
            $url = URL('documentManagementView').'?dcno='.$dcno.'&page='.$page.'';
    endif;
    // If page from advance search
    if(@Input::get('frm') == 'advsrch'):
            $url = URL('documentAdvanceSearch').'/edit'; 
            $pge = 'advsrch';
    endif;
?>

        <!-- Content Header (Page header) -->
        <?php
        if(Input::get('page') == 'forms')
        {
        	Session::put('sess_file_to_annotate',Input::get('dcno'));
        }
        $file_annotated = Session::get('sess_file_to_annotate');
        $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_annotated);
        $extension = pathinfo($file_annotated, PATHINFO_EXTENSION);
        $extension = strtolower($extension); 
        $user_annotated = Auth::user()->username;
        ?>
        @if($page != 'forms')
        <section class="content-header">
            <div class="col-sm-8">
                <span style="float:left;">
                    <strong>
                        {{$language['documents']}}
                        
                    </strong>&nbsp;
                </span>
                <span style="float:left;">
                    <?php 
                    foreach($dglist as $dval):
                        $data=@$dval->document_file_name;
                        $status = @$dval->document_status;
                        $ext = pathinfo($data, PATHINFO_EXTENSION);
                        $ext = strtolower($ext);
                        ?>
                        <!--back button-->
                        <a href="<?php echo $url; ?>">
                            <button class="btn btn-block btn-info btn-flat newbtn" style="line-height:13px !important;">{{$language['back']}}</button>
                        </a> 

                        </span>&nbsp;

                        <!-- Edit Document -->
                        <?php if($pge!="history"){ ?>
                        @if(stristr($user_permission,'edit'))
                            @if(Input::get('page') != 'import')
                                @php
                                    $docno = Session::get('open_doc_no');
                                @endphp
                                <span style="float:left;margin-left: 6px;">         
                                    <a href="{{route('editAllDocument', array('id'=>@$dval->document_id))}}&dcno={{@$docno}}&page={{@$pge}}&status={{@$dval->document_status}}<?php if($page == 'checkout'):?>&pge=cOut <?php endif;?> ">
                                        
                                        <button class="btn btn-block btn-info btn-flat newbtn" style="line-height:13px !important;">{{$language['edit_index_firelds']}} </button>
                                    </a>        
                                </span>&nbsp;
                            @endif
                        @endif 
                    <?php } 
                    endforeach; ?> 
                    <!-- if document status = checkout disabel add/view workflow -->
                    
                    @if(@$status != 'Checkout')
                        @if(Session::get('enbval4')==Session::get('tval'))
                             <!-- <a class="tn btn-block btn-info btn-flat newbtn" id="workflow" data-toggle="modal" data-target="#addview_workflow" style="line-height:15px !important; margin-left: 6px; border-radius:4px; cursor: pointer; color:#ffffff !important; float: left; width: auto !important;">{{$language['add/view workflow']}}</a> -->
                        @endif
                    @endif
                    @if(@$status == 'Checkout')
                    <p class="help-block">{{@$language['button_missing_checkout']}}</p>
                    @endif
                <!--For add image annotation-->
                <?php 
                    foreach($dglist as $key => $dval):
                        $data=@$dval->document_file_name;    
                        $status = @$dval->document_status;  
                        $ext = pathinfo($data, PATHINFO_EXTENSION);
                        $ext = strtolower($ext);

                        if(@$status!="Checkout"){
                            if($ext=='gif' || $ext=='jpg'|| $ext=='jpeg'|| $ext=='png'){?>
                                <span style="float:left;">
                                @if(Input::get('annotation') != 'yes')
                                    @if (Session::get('module_activation_key2')==1)
                                        @if(date("Y-m-d") > Session::get('module_expiry_date2')) 
                                        @else
                                            <button id="add-img-annotation" documentNo="{{Input::get('dcno')}}" iid="{{Input::get('id')}}" page="{{Input::get('page')}}" class="btn btn-block btn-info btn-flat newbtn" style="margin-left: 4%;line-height:13px !important; ">{{$language['add_view_annotation']}}</button>
                                        @endif
                                    @endif
                                </span>
                                @endif
                            <?php }
                        }                        
                    endforeach; ?> 
                @if (Session::get('module_activation_key2')==1)  
                    @if($extension == 'pdf')
                    <!-- if document status = checkout disable clear all, save buttons -->
                    @if(@$status != 'Checkout')
                        <span style="float:left;margin-left:5px;">
                            <a href="javascript:void(0)" class="btn btn-block btn-info btn-flat newbtn" style="line-height:13px !important; " title="Save all changes.">Save</a>
                        </span>
                        <span style="float:left; margin-left:5px;">
                            <button class="btn btn-block btn-info btn-flat newbtn" style="line-height:13px !important; " title="{{$language['clear_all_changes']}}" id="clear_all">{{$language['clear_all_btn']}}</button>
                        </span>
                    @endif
                    @endif
                @endif
            </div>
            <div class="col-sm-4">
               <!--  <ol class="breadcrumb">
                    <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
                    <li class="active">{{$language['documents']}}</li>
                </ol> -->
            </div>
        </section>
        @endif
    <div class="content">
        <!-- Main content -->
        <section class="content" id ="shw">
            <div class="row">
            <div class="col-sm-12">
            <div class="user-block">
            

                    <?php 
                    $ext = pathinfo($dval->document_file_name, PATHINFO_EXTENSION);
                    $ext = strtolower($ext);
                    if($ext=='pdf'){?>
                        <img class="img-circle img-bordered-sm" src="{{ URL::to('/') }}/images/icons/large/pdf.png" alt="pdf Image">
                    <?php
                    }
                    elseif ($ext=='png'||$ext=='jpg'||$ext=='jpeg'||$ext=='tiff'||$ext=='TIF'||$ext=='TIFF' || $ext=='gif') {?>
                        <img class="img-circle img-bordered-sm" src="{{ URL::to('/') }}/images/icons/large/image.svg" alt="Image">
                    <?php
                    }
                    elseif ($ext=='docx'||$ext=='doc') {?>
                        <img class="img-circle img-bordered-sm" src="{{ URL::to('/') }}/images/icons/large/word.png" alt="word Image">
                    <?php
                    }
                    elseif ($ext=='txt') {?>
                        <img class="img-circle img-bordered-sm" src="{{ URL::to('/') }}/images/icons/large/text.png" alt="text Image">
                    <?php
                    }
                    elseif ($ext=='zip'||$ext=='rar') {?>
                        <img class="img-circle img-bordered-sm" src="{{ URL::to('/') }}/images/icons/large/zip.png" alt="zip Image">
                    <?php
                    }
                    elseif ($ext=='xls'||$ext=='xlsx') {?>
                        <img class="img-circle img-bordered-sm" src="{{ URL::to('/') }}/images/icons/large/excel.png" alt="excel Image">
                    <?php
                    }
                    elseif ($ext=='mp3' || $ext=='wav' || $ext=='ogg') {?>
                        <img class="img-circle img-bordered-sm" src="{{ URL::to('/') }}/images/icons/large/music.png" alt="music Image">
                    <?php
                    }
                    elseif ($ext=='webm' || $ext=='ogv' || $ext=='flv' || $ext=='mp4') {?>
                        <img class="img-circle img-bordered-sm" src="{{ URL::to('/') }}/images/icons/large/webm.png" alt="video Image">
                    <?php
                    }
                    elseif ($ext=='dwg') {?>
                        <img class="img-circle img-bordered-sm" src="{{ URL::to('/') }}/images/icons/large/3d.png" alt="Cad Image">
                    <?php
                    }
                    else{?>
                        <img class="img-circle img-bordered-sm" src="{{ URL::to('/') }}/images/icons/large/file.svg" alt="file Image">
                    <?php
                    } ?>
                
                <span class="username">
                  <a>{{ ucfirst(@$dval->document_file_name) }}</a>
                  
                </span>
            <span class="description">Modified On : {{ dtFormat(@$dval->updated_at) }}</span>
            </div>
                <div class="vertical-menu" style="position: absolute;">
                    <div id="vmenu" class="vertical_menu">
                        <ul>
                        @if($page == 'forms')
                            <li><a class="active rotate first" id="info_form">{{$language['information']}}</a></li>
                        @else
                          <li><a class="active rotate first" id="info">{{$language['information']}}</a></li>
                          
                          <li><a class="rotate" id="notes">{{$language['notes']}}</a></li>
                          <!-- <li><a class="rotate" id="pages">Pages</a></li> -->
                          <li><a class="rotate" id="prever">{{$language['previous version']}}</a></li>
                          <li><a class="rotate" id="elog">{{$language['event log']}}</a></li>
                        @endif
                        </ul>    
                    </div>  
                </div>
                <div class="preview_menu" style="padding-left: 4%;">
                    <!-- Profile Image -->
                    <div class="box box-primary">
                        <div class="box-body box-profile">
                        <div id="pdfrender">    
                        </div>
                        @foreach($dglist as $key => $dval)
                        <?php $data=@$dval->document_file_name;   
                            //extension of file check 
                            $ext = pathinfo($data, PATHINFO_EXTENSION);
                            $ext = strtolower($ext);
                            //check the existance of file
                            if($page == 'history')
                            {
                                $check_file_exist = file_exists(config('app.backup_path').@$dval->document_file_name);
                            }
                            else if($page == 'import')
                            {
                                $check_file_exist = file_exists(config('app.temp_document_path').@$dval->document_file_name);
                            }
                            else
                            {
                                $check_file_exist = file_exists(config('app.base_path').@$dval->document_file_name);
                            }
                            
                            
                            if(($check_file_exist == 1))
                            {
                        ?>                       
                    <?php 
                    if($ext=='tiff'||$ext=='tif'||$ext=='TIFF'||$ext=='TIF'){?>
                            <script type="text/javascript">
                                var path='<?php echo $data;?>';
                                var page = '<?php echo $page;?>';
                                $('#pdfrender').html('<div id="output"></div>');

                                /*Tiff.initialize({TOTAL_MEMORY: 16777216 * 10});
                                var xhr = new XMLHttpRequest();
                                xhr.responseType = 'arraybuffer';
                                //xhr.open('GET', base_url_path+'/storage/documents/'+path);
                                if(page == 'history'){
                                    //console.log(base_url_path+'/storage/documents/documents_backup/'+path);
                                    xhr.open('GET',base_url_path+'/storage/documents/documents_backup/'+path);
                                }else{
                                    //console.log(base_url_path+'/storage/documents/'+path);
                                    xhr.open('GET',base_url_path+'/storage/documents/'+path);
                                }
                                xhr.onload = function (e) {
                                    var buffer = xhr.response;
                                    var tiff = new Tiff({buffer: buffer});
                                    for (var i = 0, len = tiff.countDirectory(); i < len; ++i) {
                                        tiff.setDirectory(i);
                                        var canvas = tiff.toCanvas();
                                        $('#output').append(canvas);
                                    }
                                };
                                xhr.send();*/

                                var xhr = new XMLHttpRequest();
                                xhr.responseType = 'arraybuffer';
                                xhr.open('GET',"{{ $doc_url }}"+path); 
                                xhr.onload = function (e) {
                                    var tiff = new Tiff({buffer: xhr.response});
                                    var canvas = tiff.toCanvas();
                                    if (canvas) {
                                      $('#output').empty().append(canvas);
                                    }
                                };
                                xhr.send();
                            </script>
                    <?php }
                    else if($ext=='xls' || $ext=='xlsx' || $ext=='doc' || $ext=='docx'){
                            $connected = @fsockopen("www.google.com", 80); 
                            if ($connected){
                                $is_conn = true; //action when connected
                                ?>
                                    <iframe enable-annotation src="https://docs.google.com/gview?url={{ $doc_url }}{{ @$dval->document_file_name }}&embedded=true" width="100%"  height="500px;" class="set_width"></iframe>
                                <?php
                                fclose($connected);
                            }else{
                                $is_conn = false; //action in connection failure
                                ?>
                                    <div>{{$language['no_internet']}}</div>  
                                <?php
                            }
                            ?>
                    <?php }
                    else if($ext =='dwg'){
                            ?>
                                    <iframe <?php if($page == 'history'){ ?> src="https://sharecad.org/cadframe/load?url={{$doc_url}}{{ @$dval->document_file_name }}" <?php }else{ ?> src="https://sharecad.org/cadframe/load?url={{$doc_url}}{{ @$dval->document_file_name }}" width="100%" <?php } ?> height="500px;" class="set_width" scrolling="no" width="100%" height="500px"></iframe>
                    <?php }
                    else if($ext=='gif'||$ext=='jpg'||$ext=='jpeg'||$ext=='png'){ ?>
                            
                                <div class="img-div" >
                                <input type="hidden" value="yes" id="isImage"> 
                                        <!--Image preview-->
                                        <div id="viewer" class="viewer"></div>
                                    
                                </div>
                            
                    <?php }
                    else if($ext == 'pdf'){
                        $encoded_document_file_name = $dval->document_file_name;
                        $timestamp = time();
                        //$encoded_document_file_name = $dval->document_file_name;
                        ?> 
                            
                            <!-- pdf thumbnail normal view -->
                            @if(Session::get('enbval2')!=Session::get('tval'))                          
                                <!-- <div class="resizable" style="width:100%; height:100vh;"><iframe enable-annotation src="webviewer?file={{ $doc_url }}{{ $encoded_document_file_name }}&time={{$timestamp}}&dcno={{$dcno}}" width="auto" height="auto;" class="resizewrapperiframe"></iframe></div> -->


                                <div class="resizable" style="width:100%; height:100vh;"><iframe enable-annotation src="annotation/{{$dcno}}?file={{ $doc_url }}{{ $encoded_document_file_name }}&time={{$timestamp}}&dcno={{$dcno}}" width="auto" height="auto;" class="resizewrapperiframe"></iframe></div>

                            <!-- annoation view -->
                            @else   
                                <div id="viewerContainer" class="page" style="width:100%; height:100vh;"></div>
                                <div id="userSelectionContainer" class="page active"></div>
                            @endif 
                    
                    <?php }
                    else if($ext == 'mp4' || $ext == 'flv' || $ext == 'webm' || $ext == 'ogv'){
                            if($ext=='mp4'){ ?>
                                <video id="player1" style="width: 100%;height:450px;min-width: 217px;" poster="{{ $doc_url }}{{ @$dval->document_file_name }}" preload="none" controls playsinline webkit-playsinline>
                                    <source src="{{ $doc_url }}{{ @$dval->document_file_name }}" type="video/mp4">
                                </video>
                            <?php }
                            else if($ext=='flv'){ ?>
                            <!-- flash player use for flv format -->
                                {!! Html::script('js/flashobject.js') !!}
                                <div id="player_8168" style="display:inline-block;">
                                    <a href="http://get.adobe.com/flashplayer/">You need to install the Flash plugin</a> - <a href="http://www.webestools.com/">http://www.webestools.com/</a>
                                </div>
                                <script type="text/javascript">
                                    var flashvars_8168 = {};
                                    var params_8168 = {
                                        quality: "high",
                                        wmode: "transparent",
                                        bgcolor: "#000000",
                                        allowScriptAccess: "always",
                                        allowFullScreen: "true",
                                        flashvars: "fichier={{ $doc_url }}{{ @$dval->document_file_name }}&apercu={{ config('app.url') }}/public/images/icons/large/download.png"
                                    };
                                    var attributes_8168 = {};
                                    flashObject("http://flash.webestools.com/flv_player/v1_27.swf", "player_8168", "920", "450", "8", false, flashvars_8168, params_8168, attributes_8168);
                                </script>
                            <?php }
                            else if($ext=='webm'){ ?>
                                <video id="player1" style="width: 100%;height:450px;min-width: 217px;" poster="{{ $doc_url }}{{ @$dval->document_file_name }}" preload="none" controls playsinline webkit-playsinline>
                                    <source src="{{ $doc_url }}{{ @$dval->document_file_name }}" type="video/webm">
                                </video>
                            <?php }
                            else if($ext=='ogv'){ ?>
                                <video id="player1" style="width: 100%;height:450px;min-width: 217px;" poster="{{ $doc_url }}{{ @$dval->document_file_name }}" preload="none" controls playsinline webkit-playsinline>
                                    <source src="{{ $doc_url }}{{ @$dval->document_file_name }}" type=video/ogg>
                                </video>
                            <?php } ?>
                    <?php } 
                    else if($ext=='mp3'||$ext=="wav"||$ext=='ogg'){ 
                        if($ext=='mp3'){ ?>
                               <audio id="player2" preload="none" controls style="width:100%;"><source src="{{ $doc_url }}{{ @$dval->document_file_name }}" type="audio/mp3"></audio>
                        <?php }
                        else if($ext=='wav'){ ?>
                                <audio id="player2" preload="none" controls style="width:100%;"><source src="{{ $doc_url }}{{ @$dval->document_file_name }}" type="audio/wav"></audio>
                        <?php }
                        else if($ext=='ogg'){ ?>
                                <audio id="player2" preload="none" controls style="width:100%;"><source src="{{ $doc_url }}{{ @$dval->document_file_name }}" type=application/ogg></audio>
                        <?php }       
                    }
                    else if($ext =='zip' || $ext == 'rar'){ ?>
                            <div><div class="info-box"><span class="info-box-icon bg-aqua"><i class="icon fa fa-file-archive-o"></i></span><div class="info-box-content"><span class="info-box-text" style="text-align: left !important;">This is a compressed document.</span><span style="display: block;font-size: 15px;margin-top: 18px;text-align: left !important;"><a href="{{ $doc_url }}{{ @$dval->document_file_name }}" download>Click here to download the document</a></span></div></div></div>
                    <?php }
                    //other extensions view here 
                    else{ ?>
                            <iframe enable-annotation src="{{ $doc_url }}{{ @$dval->document_file_name }}" width="100%" height="500px;" class="set_width"></iframe>
                        <?php 
                            } 
                            
                            }
                            else
                            {
                                echo '<span><b>'.@$dval->document_file_name."</b></span><span style='color:red'> file does not exist".'</span>';
                            }
                            ?>                        
                       @endforeach
                        
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div><!-- /.tab-content -->
            </div>
            </div>
            <div class="row" id="mini-documents" style="min-width: 40% !important;">
            @if($page == 'forms')
                <div id="div-append" style="display:none;">
                    <div class="box box-primary" id="box" style="height:235px !important;">
                        <div id="infobox">
                            <table class="sidbox">
                            <div class="head col-sm-12" style="font-weight:bold;padding-top:10px;">Information<a id="form_close" style="float:right; color:#ffffff; cursor:pointer;"><i class="fa fa-close"></i></a>
                            </div>
                            <div class="col-sm-12">
                                <div class="row odd">
                                    <div class="col-xs-5">
                                        <label for="" class="control-label">File Name: </label>
                                    </div>
                                    <div class="col-xs-7">
                                        <p style="border: yellowgreen;" id="show_name"></p>
                                    </div>
                                </div>
                                <div class="row evenclr">
                                    <div class="col-xs-5">
                                        <label for="" class="control-label">File Size: </label>
                                    </div>
                                    <div class="col-xs-7">
                                        <p style="border: yellowgreen;" id="show_size"></p>
                                    </div>
                                </div>
                                <div class="row odd">
                                    <div class="col-xs-5">
                                        <label for="" class="control-label">Form Name: </label>
                                    </div>
                                    <div class="col-xs-7">
                                        <p style="border: yellowgreen;" id="form_name"></p>
                                    </div>
                                </div>
                                <div class="row evenclr">
                                    <div class="col-xs-5">
                                        <label for="" class="control-label">Form Description: </label>
                                    </div>
                                    <div class="col-xs-7">
                                        <p style="border: yellowgreen;" id="form_description"></p>
                                    </div>
                                </div>
                                <div class="row odd">
                                    <div class="col-xs-5">
                                        <label for="" class="control-label">Created At: </label>
                                    </div>
                                    <div class="col-xs-7">
                                        <p style="border: yellowgreen;" id="created"></p>
                                    </div>
                                </div>
                            </div>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div id="mini-doc-col" style="display:none;">
                    <div class="box box-primary" id="box">
                        <div id="infobox">      
                                <table class="sidbox">
                                    <div class="head col-sm-12" style="font-weight:bold;padding-top:10px;">{{$language['information']}}
                                    <a class="closebox" style="float:right; color:#ffffff; cursor:pointer;"><i class="fa fa-close"></i></a></div>
                                    @foreach($dglist as $key => $val) 
                                    <?php 
                                    if(@$val->documentTypes[0]->document_type_column_no){
                                        $field1 = @$val->documentTypes[0]->document_type_column_no;
                                    }else{
                                        $field1 = ucfirst($settings_document_no);
                                    } 
                                    if(@$val->documentTypes[0]->document_type_column_name){
                                        $field2 = @$val->documentTypes[0]->document_type_column_name;
                                    }else{
                                        $field2 = ucfirst($settings_document_name);
                                    } ?>

                                        <input type="hidden" id="docid" value="{{ @$dval->document_id }}">

                                        <div class="col-sm-6">
                                            <div class="row evenclr">
                                                <div class="col-xs-5">
                                                    <label for="{{$settings_document_no}} :" class="control-label"><?php echo $field1;?>: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;"><?php echo @$val->document_no; ?></p>   
                                                </div>
                                            </div>
                                            <div class="row odd">
                                                <div class="col-xs-5">
                                                    <label for="{{$settings_document_name}} :" class="control-label"><?php echo $field2;?>: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;" id="docName"><?php echo @$val->document_name; ?></p>
                                                </div>    
                                            </div>

                                            <div class="row evenclr">
                                                <div class="col-xs-5">
                                                    <label for="Document Type :" class="control-label" ><?php echo $language['document type'];?>: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;" id="docType"><?php if(@$val->documentTypes_name[0]->document_type_names==''){ echo"-"; }else{ echo @$val->documentTypes_name[0]->document_type_names; }?></p>
                                                </div>
                                            </div>

                                            <?php $k = 0; ?>
                                            <?php 
                                            if(@$val->document_type_columns)
                                            {
                                            foreach($val->document_type_columns as $dtc ):?>
                                                <?php if($k%2 == 0){ ?>
                                                <div class="row odd">
                                            <?php }else{ ?>
                                                <div class="row evenclr">
                                            <?php } ?>  

                                                <div class="col-xs-5">
                                                    <label class="control-label"><?php echo @$dtc->document_column_name; ?>: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;">
                                                     <?php if(@$dtc->document_column_type == 'Date'){ $document_column_value = custom_date_Format($dtc->document_column_value);  }else{ $document_column_value = $dtc->document_column_value; } echo $document_column_value; ?>   
                                                                                   
                                                    </p>
                                                </div>
                                            </div>
                                            <?php $k++; ?>
                                            <?php endforeach;
                                        }
                                            ?>
                                            <?php $k++; ?>
                                            <?php if($k%2==0){ ?>
                                                <div class="row evenclr">
                                            <?php }else{ ?>
                                                <div class="row odd">
                                            <?php } ?>           
                                                    <div class="col-xs-5">
                                                        <label for="Last Updated :" class="control-label" ><?php echo $language['department'];?>: </label>
                                                    </div>
                                                    <div class="col-xs-7">
                                                        <p style="border: yellowgreen;" id="department"><?php if(@$val->department[0]->department_name==''){ echo"-"; }else{ echo @$val->department[0]->department_name; }?></p>
                                                    </div>  
                                                </div>
                                         
                                           
                                            <?php if($k%2==0){ ?>
                                                <div class="row odd">
                                            <?php }else{ ?>
                                                <div class="row evenclr">
                                            <?php } ?>   
                                                <div class="col-xs-5">
                                                    <label for="Stacks :" class="control-label" ><?php echo $language['stacks'];?>: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;" id="stacks"><?php if(@$val->stacks[0]->stack_name==''){ echo"-"; }else{ echo @$val->stacks[0]->stack_name; }?></p>
                                                </div>
                                            </div>

                                            <?php if($k%2==0){ ?>
                                                <div class="row evenclr">
                                            <?php }else{ ?>
                                                <div class="odd row">
                                            <?php } ?>
                                                <div class="col-xs-5">
                                                    <label for="tagwords :" class="control-label" ><?php echo $language['tag words'];?>: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;" id="tagwords"><?php if(@$val->tagwords[0]->tagwords_title==''){ echo"-"; }else{ echo @$val->tagwords[0]->tagwords_title;}?></p>
                                                </div>
                                            </div>
                                        </div>


                                        
                                        <div class="col-sm-6">
                                            <div class="row evenclr">
                                                <div class="col-xs-5">
                                                  <label for="Document File Name :" class="control-label" ><?php echo $language['file Name'];?>: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;" id="document_file_name"><?php echo @$val->document_file_name; ?></p>
                                                </div>
                                            </div>
                                            <div class="row odd">
                                                <div class="col-xs-5">
                                                    <label for="Status :" class="control-label"><?php echo $language['status'];?>: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;" id="status"><?php echo @$val->document_status; ?></p>
                                                </div> 
                                            </div>    
                                            <div class="row evenclr">
                                                <div class="col-xs-5">
                                                    <label for="Document Path :" class="control-label" ><?php echo $language['document path'];?>: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;" id="document_path"><?php echo @$val->document_path; ?></p>
                                                </div>
                                            </div>

                                            <div class="row odd">
                                                <div class="col-xs-5">
                                                    <label for="Ownership :" class="control-label" ><?php echo $language['ownership'];?>: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;" id="ownership"><?php echo @$val->document_ownership; ?></p>
                                                </div>
                                            </div>

                                            <div class="row evenclr">
                                                <div class="col-xs-5">
                                                    <label for="Checkout :" class="control-label" ><?php echo $language['last check out date'];?>: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;" id="document_checkout_date">
                                                        <?php $document_checkout_date = dtFormat(@$val->document_checkout_date); if($document_checkout_date ==''){ echo"-"; }else{ echo $document_checkout_date; }?></p>
                                                </div>
                                            </div>


                                            <div class="row odd">
                                                <div class="col-xs-5">
                                                    <label for="Document Checkin Date :" class="control-label" ><?php echo $language['last check in date'];?>: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;" id="document_checkin_date">
                                                         <?php $document_checkin_date = dtFormat(@$val->document_checkin_date); if($document_checkin_date ==''){ echo"-"; }else{ echo $document_checkin_date; }?>    
                                                        </p>
                                                </div>
                                            </div>

                                            <div class="row evenclr">
                                                <div class="col-xs-5">
                                                    <label for="Created At :" class="control-label" ><?php echo $language['created at'];?>: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;" id="created_at">
                                                        <?php echo dtFormat(@$val->created_at);?></p>
                                                </div>
                                            </div>

                                            <div class="row odd">
                                                <div class="col-xs-5">
                                                    <label for="Document Created By :" class="control-label" ><?php echo $language['created by'];?>: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;" id="document_created_by"><?php echo @$val->document_created_by;?></p>
                                                </div>
                                            </div>

                                            <div class="row evenclr">
                                                <div class="col-xs-5">
                                                   <label for="Updated At :" class="control-label" ><?php echo $language['last updated at'];?>: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;" id="updated_at"><?php if(@$val->updated_at==''){ echo"-"; }else{ echo dtFormat(@$val->updated_at); }?></p>
                                                </div>
                                            </div>

                                             <div class="row odd">
                                                <div class="col-xs-5">
                                                    <label for="Document Modified By :" class="control-label" ><?php echo $language['last modified by'];?>: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;" id="document_modified_by"><?php if(@$val->document_modified_by==''){ echo "-"; }else{ echo @$val->document_modified_by;}?></p>
                                                </div>
                                            </div>

                                            <div class="row evenclr">
                                                <div class="col-xs-5">
                                                   <label for="Size :" class="control-label" >Size: </label>
                                                </div>
                                                <div class="col-xs-7">
                                                    <p style="border: yellowgreen;" id="size">
                                                    <?php $bytes= @$val->size;
                                                        if ($bytes >= 1000000000)
                                                        {
                                                            $bytes = number_format($bytes / 1000000000, 2) . ' GB';
                                                        }
                                                        elseif ($bytes >= 1000000)
                                                        {
                                                            $bytes = number_format($bytes / 1000000, 2) . ' MB';
                                                        }
                                                        elseif ($bytes >= 1000)
                                                        {
                                                            $bytes = number_format($bytes / 1000, 2) . ' KB';
                                                        }
                                                        elseif ($bytes > 1)
                                                        {
                                                            $bytes = $bytes . ' bytes';
                                                        }
                                                        elseif ($bytes == 1)
                                                        {
                                                            $bytes = $bytes . ' byte';
                                                        }
                                                        else
                                                        {
                                                            $bytes = '0 bytes';
                                                        }

                                                        echo @$bytes;
                                                    ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>  
                                      
                                    @endforeach  
                            </table>
                        </div>
                        <div id="notesbox"  class="dispoff">
                        @if(count($noteList)>0)
                        <div id="notes-view">
                            <table class="sidbox">
                                <th class="head" colspan="3">{{$language['notes']}}
                                <a class="closebox" title="Close"><i class="fa fa-close"></i></a>
                                    <!-- @foreach($dglist as $key => $dval)
                                        <?php if(@$dval->document_status=="Checkout"){ ?>
                                            <a id="editnote" title="Add New"><i class="fa fa-plus"></i></a>
                                        <?php } ?>
                                    @endforeach -->
                                    <?php if($pge!="history"){ ?>
                                        <a id="editnote" title="Add New"><i class="fa fa-plus"></i></a>
                                    <?php } ?>
                                </th>
                                <tr class="evncol">
                                    <td class="rowstyllft">{{$language['note']}}</td>
                                    <td class="rowstylmdl">{{$language['date']}}</td>
                                    <td class="rowstylrgt">{{$language['by']}}</td>
                                </tr>
                                <tr id="newnote" style="display:none;">
                                    <td colspan="3">
                                        <textarea class="new-text" style="width:99%" placeholder="{{$language['add_new_note']}}" name="newnotetxt" id="newnotetxt"></textarea>
                                        <div id="msgbox" class="mandatory"></div>
                                        
                                        <!--Btns-->
                                        <div class="box-footer">
                                            <input type="button" id="notesave" class="btn btn-info btn-flat"  value="{{$language['save']}}">
                                            <input type="button" id="editnote" class="btn btn-primary btn-danger cancel-btn" value="{{$language['cancel']}}">
                                        </div>

                                    </td>
                                </tr>
                                
                                <?php $incr = 1; ?>
                                @foreach ($noteList as $key => $val)
                                    @if(($incr % 2) == 1)
                                        <tr class="oddcol">
                                    @else
                                        <tr class="evncol">
                                    @endif
                                        <td class="rowstyllft"><div style="max-height:200px; overflow-y:auto;">{{ $val->document_note }}</div></td>
                                        <td class="rowstylmdl" style="width:100px;"><?php echo dtFormat(@$val->created_at);?></td>
                                        <td  class="rowstylrgt" style="width:100px;">{{ $val->document_note_created_by }}</td>
                                    </tr>                                    
                                    <?php $incr++; ?>                     
                                @endforeach
                    
                            </table>
                        </div>
                        @else
                            <table class="sidbox" id="notes-view">
                                <th class="head">{{$language['notes']}}<a class="closebox"><i class="fa fa-close"></i></a>
                                    <?php /*@foreach($dglist as $key => $dval)
                                        <?php if(@$dval->document_status=="Checkout"){ ?>
                                            <a id="editnote"><i class="fa fa-plus"></i></a>
                                        <?php } ?>
                                    @endforeach*/?>
                                    <a id="editnote" title="Add New"><i class="fa fa-plus"></i></a>
                                </th>
                                <tr id="msgrow">
                                    <td colspan="3">
                                        <div id="msgbox" style="color:#FF0000"></div>
                                    </td>
                                </tr>
                                <tr id="newnote" style="display:none;">
                                    <td colspan="3">
                                        <textarea class="new-text" style="width:99%" placeholder="{{$language['add_new_note']}}" name="newnotetxt" id="newnotetxt"></textarea>

                                        <!--Btns-->
                                        <div class="box-footer">
                                            <input type="button" id="notesave" class="btn btn-info btn-flat"  value="{{$language['save']}}">
                                            <input type="button" id="editnote" class="btn btn-primary btn-danger cancel-btn" value="{{$language['cancel']}}">
                                        </div>

                                    </td>
                                </tr>
                                <tr class="oddcol">
                                    <td class="rowstyllft">{{$language['no notes']}}</td>
                                </tr>
                            </table>
                        @endif
                        </div>
                        <div id="preverbox" class="dispoff">
                            @if(count($preVer)>0)
                                <table class="sidbox">
                                    <th class="head" colspan="2">{{$language['previous version']}}<a class="closebox"><i class="fa fa-close"></i></a></th>
                                    <tr class="evncol">
                                        <td class="rowstyllft">{{$language['version no']}}</td>
                                        <td class="rowstylrgt">{{$language['created on']}}</td>
                                    </tr>
                                    <?php $incr = 1; ?>
                                    @foreach ($preVer as $key => $val)
                                        @if(($incr % 2) == 1)
                                            <tr class="oddcol">
                                        @else
                                            <tr class="evncol">
                                        @endif
                                            <td class="rowstyllft">{{ $val->document_version_no }}</td>
                                            <td class="rowstylrgt"><?php echo dtFormat(@$val->created_at);?></td>
                                        </tr> 
                                        <?php $incr++; ?>                                    
                                    @endforeach
                                </table>
                            @else
                                <table class="sidbox">
                                    <th class="head" colspan="2">{{$language['previous version']}}<a class="closebox"><i class="fa fa-close"></i></a></th>
                                    <tr class="evncol">
                                        <td class="rowstyllft">{{$language['version no']}}</td>
                                        <td class="rowstylrgt">{{$language['created on']}}</td>
                                    </tr>
                                    <tr class="oddcol">
                                        <td class="rowstyllft" colspan="2">{{$language['no previous']}}</td>
                                    </tr> 
                                </table>        
                            @endif
                        </div>
                        <div id="pagesbox" class="dispoff">
                            
                        </div>
                        <div id="elogbox" class="dispoff">
                            @if(count($evntLog)>0)
                                <table class="sidbox">
                                    <th class="head" colspan="3">{{$language['event log']}}<a class="closebox"><i class="fa fa-close"></i></a></th>
                                    <tr class="evncol">
                                        <td class="rowstyllft">{{$language['action']}}</td>
                                        <td class="rowstyllft">{{$language['username']}}</td>
                                        <td class="rowstyllft">{{$language['date']}}</td>
                                    </tr>
                                    <?php $incr = 1; ?>
                                    @foreach ($evntLog as $key => $val)
                                        @if(($incr % 2) == 1)
                                            <tr class="oddcol">
                                        @else
                                            <tr class="evncol">
                                        @endif
                                            <td class="rowstyllft">{{ $val->audit_action_desc }}</td>
                                            <td class="rowstyllft">{{ $val->audit_user_name }}</td>
                                            <td class="rowstyllft"><?php echo dtFormat(@$val->created_at);?></td>
                                        </tr> 
                                        <?php $incr++; ?>                                    
                                    @endforeach
                                </table>
                            @else
                                <table class="sidbox">
                                    <th class="head" colspan="2">{{$language['event log']}}<a class="closebox"><i class="fa fa-close"></i></a></th>
                                    <tr class="evncol">
                                        <td class="rowstyllft">{{$language['name']}}</td>
                                        <td class="rowstylrgt">{{$language['date']}}</td>
                                    </tr>
                                    <tr class="oddcol">
                                        <td class="rowstyllft" colspan="2">{{$language['no event']}}</td>
                                    </tr> 
                                </table>        
                            @endif
                        </div>

                    </div>
                </div>
            @endif
                <div class="modal fade" id="addview_workflow" data-backdrop="true" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div id="content_wf"></div>
                </div>    
            </div><!-- /.nav-tabs-custom -->
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->   

    <!--For image rotation-->
    @if(Request::segment(1) == 'documentManagementView' && Input::get('annotation') != 'yes')
        <!--For image rotation-->
        {!!Html::script('js/rotate-zoom/jqueryui.js')!!}
        {!!Html::script('js/rotate-zoom/jquery.browser.js')!!}
        {!!Html::script('js/rotate-zoom/jquery.mousewheel.min.js')!!}
        {!!Html::script('js/rotate-zoom/jquery.iviewer.js')!!}
    @endif

    {!!Html::script('build/mediaelement-and-player.js')!!}
    <!-- {!!Html::script('build/demo.js')!!} -->
    
    <script type="text/javascript">
        
        $(document).ready(function(){

            $('video, audio').mediaelementplayer();
            // Define variables
            var urlPage = "{{Input::get('page')}}";

            // Go to annotation page
            $('#add-img-annotation').on('click',function(){
                var documentno = $(this).attr('documentno');
                var page       = $(this).attr('page');
                var id         = $(this).attr('iid');
                var url        = 'documentManagementView?dcno='+documentno+'&id='+id+'&page='+page+'&annotation=yes';
                // Reload
                window.location.assign(url)  
            });

            var isAnnotationExists = "<?php echo Input::get('annotation');?>";
            if(isAnnotationExists){
                /*<--Image annotation-->*/
                //Fetching saved annotations
                var did = '{{Input::get('dcno')}}';
                $.ajax({
                    type: 'GET',
                    url : baseurl+'/getImgAnnotations?dId='+did+'',
                    dataType:'json',
                    success:function(response){
                        console.log(response);
                        $(".toAnnotate").annotateImage({
                            editable: true,
                            useAjax: false,

                            notes: response

                        });
                    }
                }); 

            }else{
                /*<!--Zoomin zoomout-->*/
                var isFileExists = "{{file_exists(public_path('images/test/'.@$dval->document_file_name))}}";
                var fileName     = "{{@$dval->document_file_name}}";
                var doc_url       = "{{$doc_url}}";
                var allRrl       = "{{config('app.doc_url')}}";
                var backupPath   = "{{config('app.doc_backup_url')}}";
                //var imgUrl = 'images/test/'+fileName;
                var imgUrl = doc_url+fileName;
                if(isFileExists)
                {
                    //<!--If file exists,show resized image-->
                    var imgUrl = 'images/test/'+fileName;
                }
                

                // Initilize here
                var iv = $("#viewer").iviewer({
                    src: imgUrl
                });
                /*<!--Zoomin zoomout-->*/
            }

            /*<!--To disable right click on image after load it-->*/ 
            $('body').bind('contextmenu', function(e) {
                return false;
            });

            // Reinitilize for rotate
            $('body').on('click','#update_again',function(){
                var documentId = "{{Input::get('dcno')}}";

                swal({
              title: '{{$language['Swal_are_you_sure']}}',
              text: "{{$language['reset_message']}}",
              type: "{{$language['Swal_warning']}}",
              showCancelButton: true,
            }).then(function (result) {
                if(result){
                    // Success
                    $.ajax({
                    type:'GET',
                    url : '{{URL('saveImgRotations')}}?update=yes',
                    data: 'document_is_image_values_saved='+0+'&documentId='+documentId,
                    success:function(result){
                        location.reload();
                    }
                });
                }
            });
               
            });
            
            $("#mini-doc-col").hide();
            $('body').on('click','#editnote',function(){ 
                $("#newnote").toggle();
                $("#msgbox").html("");
                $("#newnotetxt").val('');

            });

            $("body").on('click','#notesave',function(){
                var noteval = $("#newnotetxt").val();
                var docid  = $("#docid").val(); 
                if(noteval.length<1){
                    $("#msgbox").html("{{$language['note_required']}}");
                }else{
                    $.ajax({
                        type: 'get',
                        url: '{{URL('documentsNoteSave')}}',
                        data: {docmntid:docid,noteval:noteval },
                        timeout: 50000,
                        beforeSend: function() {
                            
                        },
                        success: function(data){

                            $('#notes-view').html(data);
                            //swal(data);
                            /*if(data==1){
                                $("#newnote").toggle();
                                $("#msgbox").html("Success! Note has been saved successfully. Please refresh to view.");
                            }else{
                                $("#msgbox").html("Failed! Please try again.");
                            }*/
                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            console.log(jqXHR);    
                            console.log(textStatus);    
                            console.log(errorThrown);    
                        },
                        complete: function() {
                            
                        }
                    });     
                }

                setTimeout(function(){
                    $("#msgbox").html("");
                },10000);

            });

        $("#info_form").click(function(){
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var url_string = window.location.href;
            var url = new URL(url_string);
            var c = url.searchParams.get("page");
            if(c == 'forms')
            {
                var a = url.searchParams.get("dcno");
                var b = url.searchParams.get("file");
                var id = url.searchParams.get("id");
                var size = url.searchParams.get("size");
                $('#show_name').text(b);
                $('#show_size').text(formatBytes(size));
                $('#div-append').css('display','block');
                // if more details required
                $.ajax({
                        type: 'post',
                        url: '{{URL('getattachDetails')}}',
                        data: {_token:CSRF_TOKEN,id:id },
                        success: function(response){
                            var json = $.parseJSON(response);
                            console.log(json); 
                            $('#form_name').text(json.form_name);
                            $('#form_description').text(json.form_description);
                            $('#created').text(json.created_at);
                        }
                });
            }

        });
        function formatBytes(bytes,decimals) {
                   if(bytes == 0) return '0 Bytes';
                   var k = 1024,
                       dm = decimals || 2,
                       sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
                       i = Math.floor(Math.log(bytes) / Math.log(k));
                   return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
                }
        $(".closebox").click(function(){
                $("#mini-doc-col").hide();
            });
        $(document).on('click', '#form_close', function (event) {
        
            $("#div-append").css("display","none");
            });
        $("#info").click(function(){
                //$("#mini-doc-col").show();
                $(this).addClass("active");
                $("#notes").removeClass("active");
                $("#pages").removeClass("active");
                $("#prever").removeClass("active");
                $("#elog").removeClass("active");
                $("#box").addClass("boxwidth");
                $("#infobox").show();
                $("#notesbox").hide();
                $("#pagesbox").hide();
                $("#preverbox").hide();
                $("#elogbox").hide();
                $('#mini-doc-col').toggle();
            });
        $("#notes").click(function(){
                //$("#mini-doc-col").show();
                $(this).addClass("active");
                $("#info").removeClass("active");
                $("#pages").removeClass("active");
                $("#prever").removeClass("active");
                $("#elog").removeClass("active");
                $("#box").removeClass("boxwidth");
                $("#notesbox").show();
                $("#infobox").hide();
                $("#pagesbox").hide();
                $("#preverbox").hide();
                $("#elogbox").hide();
                $('#mini-doc-col').toggle();
            });
           
        $("#prever").click(function(){
                //$("#mini-doc-col").show();
                $(this).addClass("active");
                $("#notes").removeClass("active");
                $("#info").removeClass("active");
                $("#pages").removeClass("active");
                $("#elog").removeClass("active");
                $("#box").removeClass("boxwidth");
                $("#preverbox").show();
                $("#notesbox").hide();
                $("#infobox").hide();
                $("#pagesbox").hide();
                $("#elogbox").hide();
                $('#mini-doc-col').toggle();
            });
        $("#elog").click(function(){
                //$("#mini-doc-col").show();
                $(this).addClass("active");
                $("#notes").removeClass("active");
                $("#info").removeClass("active");
                $("#pages").removeClass("active");
                $("#prever").removeClass("active");
                $("#box").removeClass("boxwidth");
                $("#elogbox").show();
                $("#infobox").hide();
                $("#preverbox").hide();
                $("#pagesbox").hide();
                $("#notesbox").hide();
                $('#mini-doc-col').toggle();
            });

        });

    $(function ($) {
        //add/view workflow
        $('body').on('click','#workflow',function(){
            var docid      = '{{$id}}';
            var view = "{{Session::get('view')}}";
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'get',
                url: 'addviewWorkflow',
                dataType: 'html',
                data: {_token: CSRF_TOKEN,docid:docid,view:view},
                timeout: 50000,
                success: function(data, status){     
                    $("#content_wf").html(data);
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
    <script src="js/tiff.min.js"></script> 
    <script>
    $(function() {
        var page = '<?php echo $page;?>';
        var base_url = '{{ config("app.url") }}';
        var user_noted = '<?php echo $user_annotated ?>';
        var username = user_noted.toUpperCase();
        var isAdmin = (username === 'Moderator');
        $('#userSelectionContainer').toggleClass("active");
        var $viewerContainer = $('#viewerContainer');
        $viewerContainer.toggleClass("active");

        var queryParams = window.ControlUtils.getQueryStringMap(false);
        var docType = queryParams.getString('doctype', 'pdf');
        var file_noted = '<?php echo $withoutExt ?>';
        if(page == 'history'){
            var myWebViewer = new PDFTron.WebViewer({
            	origin: ["*"],
            	responseHeader: ["*"],
            	method: ["*"],
                path: 'lib',
                type: "html5",
                l: "demo:vishnu.edachali@toptechinfo.com:733633bd0147909916a0e9c10bff0f41570c3f3762405aa205",
                initialDoc: '{{ $doc_url }}'+ file_noted+'.' + docType,
                documentType: docType,
                documentId: file_noted,
                enableAnnotations: true,
                annotationUser: username,
                annotationAdmin: isAdmin,
                enableReadOnlyMode: username ? false : true
            }, $viewerContainer[0]);
        }else{
            
            var myWebViewer = new PDFTron.WebViewer({
            	origin: ["*"],
            	responseHeader: ["*"],
            	method: ["*"],
                path: 'lib',
                type: "html5",
                l: "demo:vishnu.edachali@toptechinfo.com:733633bd0147909916a0e9c10bff0f41570c3f3762405aa205",
                initialDoc: '{{ $doc_url }}'+ file_noted+'.' + docType,
                documentType: docType,
                documentId: file_noted,
                enableAnnotations: true,
                annotationUser: username,
                annotationAdmin: isAdmin,
                enableReadOnlyMode: username ? false : true
            }, $viewerContainer[0]);
        }
        $viewerContainer.on('ready', function() {
            var welcomeMessage = '';
            var user;
            $("#legend a").click(function() {
                myWebViewer.getInstance().saveAnnotations().then(function() {
                    //window.location.reload();
                });
            });
            $("#legend span").text(welcomeMessage);
            $("#legend").fadeIn();
        });
    });
    $("#clear_all").click(function(){
        swal({
            title: '{{$language['Swal_are_you_sure']}}',
            text: "{{$language['not_able_save']}}",
            type: "{{$language['Swal_warning']}}",
            showCancelButton: true
        }).then(function (result) {
            if(result){
                // Success
                window.location.reload();
            }
        });
    });

    /*<--It shows the alert when leave the page if it is pdf-->*/
    // var documentPicture = $('#document_picture').attr('src');
    // var isImage         = $('#isImage').val();
    // if(documentPicture == undefined && isImage != 'yes')
    // { 
    //     window.onbeforeunload = function() {
    //         return "{{$language['sure_navigate']}}";
    //     };
    // }

</script> 
<style type="text/css">
    span .lt{
    display: none !important;
}
</style>
@endsection
