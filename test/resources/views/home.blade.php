@extends('layouts.app')
@section('main_content')
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
<style type="text/css">
/*@media(max-width: 991px){
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
        padding: 45px 0px 0 !important;
        height: 50px;
    }
}*/
/*<--mobile view table-->*/
/*@media(max-width:500px){
    .box{
        overflow-x: auto;
    }
}*/
</style>
<?php
    $user_permission=Auth::user()->user_permission;
    include (public_path()."/storage/includes/lang1.en.php" );
?>
<section class="content-header mobile-view-custom">
    <h1>
        {{$language['dashboard']}}
        <small>{{$language['version']}}</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
        <li class="active">{{$language['dashboard']}}</li>
    </ol> -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Info boxes -->
    <div class="row">
        <!-- DOCUMENTS  COUNT -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="{{URL::to('listview?view=list')}}">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-file-text-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Published Docs</span>
                        <span class="info-box-number">{{ @$docsCnt }}</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </a>
        </div><!-- /.col -->
        <!-- DOCUMENTS  COUNT -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="{{URL::to('listview?view=import')}}">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-file"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Unpublished Docs</span>
                        <span class="info-box-number">{{ @$un_docsCnt }}</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </a>
        </div><!-- /.col -->
        <!-- DOCUMENTS  COUNT -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="{{URL::to('listview?view=checkout')}}">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa  fa-file-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Check Out Docs</span>
                        <span class="info-box-number">{{ @$checkCnt }}</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </a>
        </div><!-- /.col -->
        <!-- DOCUMENTS  COUNT -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="{{URL::route('documents')}}">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-file-zip-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Archives</span>
                        <span class="info-box-number">{{ @$archive_Cnt }}</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </a>
        </div><!-- /.col -->
    </div>
    <div class="row">
        <!-- USERS COUNT -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="{{URL::route('users')}}">
                <div class="info-box">
                    <span class="info-box-icon bg-olive"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{$language['users']}}</span>
                        <span class="info-box-number">{{ @$usrCnt }}</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </a>
        </div><!-- /.col -->
        <!-- DOCUMENT TYPE  COUNT -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="{{URL::route('documentTypes')}}">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-database"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{$language['document types']}}</span>
                        <span class="info-box-number">{{ @$doctypesCnt }}</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </a>
        </div><!-- /.col -->
        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>
        <!-- DEPARTMENT COUNT -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="{{URL::route('departments')}}">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-university"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{$language['departments']}} @endif</span>
                        <span class="info-box-number">{{ @$departmntCnt }}</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </a>
        </div><!-- /.col -->
        <!-- STACKS COUNT -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="{{URL::route('stacks')}}">
                <div class="info-box">
                    <span class="info-box-icon bg-purple"><i class="fa fa-stack-overflow"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{$language['stacks']}}</span>
                        <span class="info-box-number">{{ @$stacksCnt }}</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </a>
        </div><!-- /.col -->
    </div> 

    <div class="row"> 
        
        <!-- TAGS COUNT -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="{{URL::route('tagWords')}}">
                <div class="info-box">
                    <span class="info-box-icon bg-maroon"><i class="fa fa-tags"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{$language['tag words']}}</span>
                        <span class="info-box-number">{{ @$tagsCnt }}</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </a>
        </div><!-- /.col -->
        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>
        @if(Session::get('enbval4')==Session::get('tval'))
        <!-- FORMS COUNT -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="{{URL::route('my_forms')}}">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-newspaper-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{$language['forms']}}</span>
                        <span class="info-box-number">{{ @$formCnt }}</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </a>
        </div><!-- /.col -->
        @endif
        @if(Session::get('enbval4')==Session::get('tval'))
            <!-- WORKFLOW  COUNT -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="{{URL::route('allworkflow')}}">
                    <div class="info-box">
                        <span class="info-box-icon bg-orange"><i class="fa fa-map"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{$language['workflows']}}</span>
                            <span class="info-box-number">{{ @$workflowCnt }}</span>
                        </div><!-- /.info-box-content -->
                    </div><!-- /.info-box -->
                </a>
            </div><!-- /.col -->
        @endif

        @if(Session::get('enbval5')==Session::get('tval'))
            <!-- AUDITS COUNT -->
            @if (Auth::user()->user_role != 3)
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <a href="{{URL::route('audits')}}">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="fa fa-history"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{$language['audits']}}</span>
                            <span class="info-box-number">{{ @$auditCnt }}</span>
                        </div><!-- /.info-box-content -->
                    </div><!-- /.info-box -->
                    </a>
                </div><!-- /.col -->
            @endif
        @endif
    </div>
            
            
    <div class="row">
        @if(stristr($user_permission,"view"))
            <!-- Divide in to 3 colums -->
            @for ($divno = 1; $divno <= 3; $divno++)
                @php 
                $center_div= (isset($widget_postion['center_div'.$divno]))?$widget_postion['center_div'.$divno]:array();
            @endphp
            
            <div id="div_sort{{$divno}}" class="col-md-4 connectedSortable">
                @foreach ($center_div as $div_item)
                    @if($div_item == 'recentdoc')
                    <!-- LIST RECENT 5 DOCUMENTS -->
                        <div class="box box-info widget_item" id="recentdoc">
                            <div class="box-header with-border">
                                <h3 class="box-title">{{$language['recent documents']}}</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <ul class="products-list product-list-in-box">
                                    @foreach ($docs as $key => $val)  
                                        <li class="item">
                                            <div class="product-img">
                                                <?php 
                                                $ext = pathinfo($val->document_file_name, PATHINFO_EXTENSION);
                                                $ext = strtolower($ext);
                                                if($ext=='pdf'){?>
                                                    <img src="{{ URL::to('/') }}/images/icons/large/pdf.png" alt="pdf Image">
                                                <?php
                                                }
                                                elseif ($ext=='png'||$ext=='jpg'||$ext=='jpeg'||$ext=='tiff'||$ext=='TIF'||$ext=='TIFF'||$ext=='gif') {?>
                                                    <img src="{{ URL::to('/') }}/images/icons/large/image.svg" alt="Image">
                                                <?php
                                                }
                                                elseif ($ext=='docx'||$ext=='doc') {?>
                                                    <img src="{{ URL::to('/') }}/images/icons/large/word.png" alt="word Image">
                                                <?php
                                                }
                                                elseif ($ext=='txt') {?>
                                                    <img src="{{ URL::to('/') }}/images/icons/large/text.png" alt="text Image">
                                                <?php
                                                }
                                                elseif ($ext=='zip'||$ext=='rar') {?>
                                                    <img src="{{ URL::to('/') }}/images/icons/large/zip.png" alt="zip Image">
                                                <?php
                                                }
                                                elseif ($ext=='xls'||$ext=='xlsx') {?>
                                                    <img src="{{ URL::to('/') }}/images/icons/large/excel.png" alt="excel Image">
                                                <?php
                                                }
                                                elseif ($ext=='mp3' || $ext=='wav' || $ext=='ogg') {?>
                                                    <img src="{{ URL::to('/') }}/images/icons/large/music.png" alt="music Image">
                                                <?php
                                                }
                                                elseif ($ext=='webm' || $ext=='ogv' || $ext=='flv' || $ext=='mp4') {?>
                                                    <img src="{{ URL::to('/') }}/images/icons/large/webm.png" alt="video Image">
                                                <?php
                                                }
                                                elseif ($ext=='dwg') {?>
                                                    <img src="{{ URL::to('/') }}/images/icons/large/3d.png" alt="Cad Image">
                                                <?php
                                                }
                                                else{?>
                                                    <img src="{{ URL::to('/') }}/images/icons/large/file.svg" alt="file Image">
                                                <?php
                                                } ?>
                                            </div>
                                            <div class="product-info">
                                                <a href="{{URL::route('listview')}}?view=list&docid={{$val->document_id}}" class="product-title">{{$val->document_no}}</a>                                                
                                                @php
                                                    $date = date('Y-m-d', strtotime($val->created_at));
                                                @endphp
                                                <span class="product-description">
                                                    {{$val->document_name}}
                                                </span>
                                                <span class="product-description">Last accessed on : {{$date}}</span>
                                            </div>
                                        </li> <!-- /.item -->
                                    @endforeach
                                    @if(!$docs)
                                        <div style="color:red;">{{$language['No documents found']}}</div>
                                    @endif
                                </ul>
                            </div>
                            <div class="box-footer text-center">
                                <a href="{{URL::to('listview?view=recent-document')}}" class="uppercase">View More</a>
                            </div><!-- /.box-footer -->                
                        </div>
                    @elseif($div_item == 'wi')
                        <div class="box box-info widget_item" id="notaccesseddoc">
                            <div class="box-header with-border">
                                <h3 class="box-title">fffd</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div id="search_section">
                                    <input type="text" name="search_day" id="search_day" placeholder="{{$language['count_of_days']}}" value="{{$language['days_not_accessed_value']}}">
                                    <button class="btn btn-default" style="height: 37px;margin-bottom: 4px;" id="search_button" onclick="search_not_access();">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>
                                </div>
                                <div id="search_body"></div>
                            </div>
                            <div class="box-footer text-center">
                                <a href="{{URL::route('documents')}}" class="uppercase">{{$language['view all documents']}}</a>
                            </div><!-- /.box-footer -->                
                        </div>
                    @elseif($div_item == 'notaccesseddoc')
                        <!-- Not accessed documents -->
                        <!--OLD CODE 
                        <div class="box box-info widget_item" id="notaccesseddoc">
                            <div class="box-header with-border">
                                <h3 class="box-title">{{$language['Not accessed documents']}}</h3>
                            </div>
                            <div class="box-body">
                                <div id="search_section">
                                    <input type="text" name="search_day" id="search_day" placeholder="{{$language['count_of_days']}}" value="{{$language['days_not_accessed_value']}}">
                                    <button class="btn btn-default" style="height: 37px;margin-bottom: 4px;" id="search_button" onclick="search_not_access();">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>
                                </div>
                                <div id="search_body"></div>
                            </div>
                            <div class="box-footer text-center">
                                <a href="{{URL::route('documents')}}" class="uppercase">{{$language['view all documents']}}</a>
                            </div>            
                        </div> -->




                        <div class="box box-info widget_item" id="notaccesseddoc">
                            <div class="box-header with-border">
                                <h3 class="box-title">Documents Not Accessed In The Last </h3>
                                <input type="text" name="search_day" id="search_day" placeholder="" value="{{$language['days_not_accessed_value']}}" style="width:65px">
                                 <h3 class="box-title">&nbsp;Days</h3>
                                <button class="btn btn-default" style="height: 37px;margin-bottom: 4px;" id="search_button" onclick="search_not_access();">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div id="search_section">
                                    
                                    
                                </div>
                                <div id="search_body"></div>
                            </div>
                            <div class="box-footer text-center">
                                <a href="{{URL::route('documents')}}" class="uppercase">{{$language['view all documents']}}</a>
                            </div><!-- /.box-footer -->                
                        </div>
                    @elseif ($div_item == 'docdepartment')
                        <!-- No size of docs by department -->
                        <div class="box box-info widget_item" id="docdepartment">
                            <div class="box-header with-border">
                                <h3 class="box-title">{{$language['no.of docs']}}@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{$language['departments']}} @endif</h3>
                            </div>
                            <div class="box-body">
                                <center>
                                    <div style="position: relative; width:130px; height:130px;">
                                        <canvas id="text_dept" 
                                          style="z-index: 1; 
                                                 position: absolute;
                                                 left: 0px; 
                                                 top: 0px;" 
                                          height="130" 
                                          width="130"></canvas>
                                        <canvas id="mycanvas_dept_size" 
                                          style="z-index: 2; 
                                                 position: absolute;
                                                 left: 0px; 
                                                 top: 0px;" 
                                          height="130" 
                                          width="130"></canvas>
                                    </div>
                                </center>
                                <div id="list_dept"></div>
                            </div>
                            <div class="box-footer text-center">
                                <a href="{{URL::route('listview')}}?view=list" class="uppercase">{{$language['view all documents']}}</a>
                            </div>
                        </div> 
                    @elseif($div_item == 'doctype')
            
                  <div class="box box-info widget_item" id="doctype">
                      <div class="box-header with-border">
                        <h3 class="box-title">{{$language['no.of docs']}}{{$language['document types']}}</h3>
                      </div><!-- /.box-header -->
                      <div class="box-body">
                      <center>
                        <!-- <canvas id="mycanvas_doctype" height="130px" width="130px"></canvas> -->
                        <div style="position: relative; width:130px; height:130px;">
                        <canvas id="text_type" 
                          style="z-index: 1; 
                                 position: absolute;
                                 left: 0px; 
                                 top: 0px;" 
                          height="130" 
                          width="130"></canvas>
                        <canvas id="mycanvas_doctype_size" 
                          style="z-index: 2; 
                                 position: absolute;
                                 left: 0px; 
                                 top: 0px;" 
                          height="130" 
                          width="130"></canvas>
                          </div>
                      </center>
                      <div id="list_type"></div>
                      </div>
                      <div class="box-footer text-center">
                        <a href="{{URL::route('listview')}}?view=list" class="uppercase">{{$language['view all documents']}}</a>
                      </div>
                  <!-- </div> -->
                </div>
             @elseif($div_item == 'docusers')
           
<!--                 <div class="col-md-6"> -->
                  <div class="box box-info widget_item" id="docusers">
                      <div class="box-header with-border">
                        <h3 class="box-title">{{$language['no.of docs']}}{{$language['users']}}</h3>
                      </div><!-- /.box-header -->
                      <div class="box-body">
                      <center>
                        <!-- <canvas id="mycanvas_user" height="130px" width="130px"></canvas> -->
                        <div style="position: relative; width:130px; height:130px;">
                        <canvas id="text_user" 
                          style="z-index: 1; 
                                 position: absolute;
                                 left: 0px; 
                                 top: 0px;" 
                          height="130" 
                          width="130"></canvas>
                        <canvas id="mycanvas_user_size" 
                          style="z-index: 2; 
                                 position: absolute;
                                 left: 0px; 
                                 top: 0px;" 
                          height="130" 
                          width="130"></canvas>
                          </div>
                      </center>
                      <div id="list_user"></div> 
                      </div>
                      <div class="box-footer text-center">
                        <a href="{{URL::route('listview')}}?view=list" class="uppercase">{{$language['view all documents']}}</a>
                      </div>
                  </div>
                <!-- </div>
                <div class="col-md-6"> -->
                @elseif($div_item == 'docextension')
              
                  <div class="box box-info widget_item" id="docextension">
                      <div class="box-header with-border">
                        <h3 class="box-title">{{$language['no.of docs']}}{{$language['documents extension']}}</h3>
                      </div><!-- /.box-header -->
                      <div class="box-body">
                      <center>
                        <!-- <canvas id="mycanvas_extension" height="130px" width="130px"></canvas> -->
                        <div style="position: relative; width:130px; height:130px;">
                        <canvas id="text_ext" 
                          style="z-index: 1; 
                                 position: absolute;
                                 left: 0px; 
                                 top: 0px;" 
                          height="130" 
                          width="130"></canvas>
                        <canvas id="mycanvas_extension_size" 
                          style="z-index: 2; 
                                 position: absolute;
                                 left: 0px; 
                                 top: 0px;" 
                          height="130" 
                          width="130"></canvas>
                          </div>
                      </center>
                      <div id="list_extension"></div>
                      </div>
                      <div class="box-footer text-center">
                        <a href="{{URL::route('listview')}}?view=list" class="uppercase">{{$language['view all documents']}}</a>
                      </div>
                  <!-- </div> -->
                </div>
                @elseif($div_item == 'docmedia')
                  <div class="box box-info widget_item" id="docmedia">
                      <div class="box-header with-border">
                        <h3 class="box-title">Total Data Usage</h3>
                      </div><!-- /.box-header -->
                      <div class="box-body">
                      <center>
                        <!-- <canvas id="mycanvas_extension" height="130px" width="130px"></canvas> -->
                        <div style="position: relative; width:130px; height:130px;">
                        <canvas id="text_media" 
                          style="z-index: 1; 
                                 position: absolute;
                                 left: 0px; 
                                 top: 0px;" 
                          height="130" 
                          width="130"></canvas>
                        <canvas id="mycanvas_mediafile_size" 
                          style="z-index: 2; 
                                 position: absolute;
                                 left: 0px; 
                                 top: 0px;" 
                          height="130" 
                          width="130"></canvas>
                          </div>
                      </center>
                      <div id="list_media"></div>
                      </div>
                      <div class="box-footer text-center">
                        <a href="{{URL::route('listview')}}?view=list" class="uppercase">{{$language['view all documents']}}</a>
                      </div>
                  <!-- </div> -->
                </div>
                @elseif($div_item == 'diskspace')
                  <div class="box box-info widget_item" id="diskspace">
                      <div class="box-header with-border">
                        <h3 class="box-title">Total Disk Space Usage</h3>
                      </div><!-- /.box-header -->
                      <div class="box-body">
                      <center>
                        <!-- <canvas id="mycanvas_extension" height="130px" width="130px"></canvas> -->
                        <div style="position: relative; width:130px; height:130px;">
                        <canvas id="text_space" 
                          style="z-index: 1; 
                                 position: absolute;
                                 left: 0px; 
                                 top: 0px;" 
                          height="130" 
                          width="130"></canvas>
                        <canvas id="mycanvas_diskspace_size" 
                          style="z-index: 2; 
                                 position: absolute;
                                 left: 0px; 
                                 top: 0px;" 
                          height="130" 
                          width="130"></canvas>
                          </div>
                      </center>
                      <div id="list_space"></div>
                      </div>
                  <!-- </div> -->
                </div>
                @endif
                @endforeach
              </div>
               @endfor
              @else
              <section class="content content-sty" id="spl-wrn">        
                  <div class="alert alert-danger alert-sty">{{$language['dont_hav_permission']}}</div>
              </section>
            @endif
            </div>  
        </section>
        {!! Html::script('plugins/jQueryUI/jquery-ui.min.js') !!}
        <script type="text/javascript">
        var permissions = '<?php echo $user_permission;?>';
        expr = /view/;
        if(permissions.match(expr))
        {
          search_not_access();
        }
        
        
        
            function search_not_access(){
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                var search_val=$("#search_day").val();
                if(search_val=="" || search_val==null){
                    alert("Please enter the value.");
                }
                if(isNaN(search_val)){
                    alert('Please enter the number.');
                }
                else{
                $.ajax({
                    type:'post',
                    url:'searchNotAccess',
                    dataType: "html",
                    data:{_token:CSRF_TOKEN,val:search_val},
                    success:function(data){
                        var result = data;
                        $('#search_body').html(result);   
                    }
                });
            }
        };


$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip(); 
 /*sorting Widget */
 $('.connectedSortable').sortable({
    placeholder         : 'sort-highlight',
    connectWith         : '.connectedSortable',
    handle              : '.box-header',
    forcePlaceholderSize: true,
    zIndex              : 999999,
    stop: function (event, ui) {
       var changedList = this.id;
        var data = $(this).sortable('serialize');
        
          var div_sort1 = new Array();
          var div_sort2 = new Array();
          var div_sort3 = new Array();
          $("#div_sort1 > div.widget_item").each(function( index ) {
             div_sort1.push($( this ).attr('id'));

          });
          $("#div_sort2 > div.widget_item").each(function( index ) {
            div_sort2.push($( this ).attr('id'));
          });

          $("#div_sort3 > div.widget_item").each(function( index ) {
             div_sort3.push($( this ).attr('id'));
          });

          var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
          $.ajax({
            data:{_token:CSRF_TOKEN,center_div1:div_sort1,center_div2:div_sort2,center_div3:div_sort3},
            type: 'POST',
            dataType:'json',
            url: '{{URL('saveWidgetPostion')}}',
            success:function(response)
            {
              /*console.log(response);*/
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.status);
                }
        });
    }
  });

  $('.connectedSortable .box-header').css('cursor', 'move');

  $.ajax({
    url:'{{URL('getDeptment')}}',
    type:'get',
    dataType:'json',
    success:function(response)
    {
// Department number chart
if ($("#docdepartment").length ) 
{
      //var ctx_dept = $("#mycanvas_dept").get(0).getContext("2d");
      var data=response.dept;
      if(data == "" || data == null){
        /*$('#docdepartment').hide();*/
      }
      else{
      //var chart_dept = new Chart(ctx_dept).Doughnut(data,{tooltipFontSize: 10});
      var dept_count=data.length;

//Listing dept name and number of docs

      var html_dept = '<ul class="chart-legend clearfix"><div class="col-xs-7"><h5>Department</h5></div><div class="col-xs-2"><h5>No.</h5></div><div class="col-xs-3"><h5>Size</h5></div>';
      for(var i=0 ; i<dept_count ; i++) {
        html_dept+='<li class="list"><div class="col-xs-7"><i class="fa fa-circle-o" style="color: '+ JSON.stringify(data[i].color).replace(/['"]+/g, '') +';"></i>' +' '+JSON.stringify(data[i].label).replace(/['"]+/g, '')+'</div><div class="col-xs-2">'+JSON.stringify(data[i].value).replace(/['"]+/g, '') +'</div><div class="col-xs-3">'+JSON.stringify(data[i].size).replace(/['"]+/g, '')+'MB</div></li>'
      }
      html_dept+= '</ul>';
      document.getElementById("list_dept").innerHTML = html_dept;
    }
   }  

// Document type number chart
if ($("#doctype").length ) 
{
      //var ctx_type = $("#mycanvas_doctype").get(0).getContext("2d");
      var data=response.doctype;
      if(data != 'empty')
      {
      //var chart_type = new Chart(ctx_type).Doughnut(data,{tooltipFontSize: 10});
      var type_count=data.length;

//Listing doctype name and number of docs
      
      var html_type = '<ul class="chart-legend clearfix"><div class="col-xs-7"><h5>Document Type</h5></div><div class="col-xs-2"><h5>No.</h5></div><div class="col-xs-3"><h5>Size</h5></div>';
      for(var i=0 ; i<type_count ; i++) {
        html_type+='<li class="list"><div class="col-xs-7"><i class="fa fa-circle-o" style="color: '+ JSON.stringify(data[i].color).replace(/['"]+/g, '') +';"></i>' +' '+JSON.stringify(data[i].label).replace(/['"]+/g, '')+'</div><div class="col-xs-2">'+JSON.stringify(data[i].document_count).replace(/['"]+/g, '') +'</div><div class="col-xs-3">'+JSON.stringify(data[i].size).replace(/['"]+/g, '')+'MB</div></li>'
      }
      html_type+= '</ul>';
      document.getElementById("list_type").innerHTML = html_type;
    }
    else{
     /* $('#doctype').hide();*/
    }
}
// User number chart
if ($("#docusers").length ) 
{
      //var ctx_user = $("#mycanvas_user").get(0).getContext("2d");
      var data=response.user;
      //var chart_user = new Chart(ctx_user).Doughnut(data,{tooltipFontSize: 10});
      var user_count=data.length;

//Listing user name and number of docs
      
      var html_user = '<ul class="chart-legend clearfix"><div class="col-xs-7"><h5>User Name</h5></div><div class="col-xs-2"><h5>No.</h5></div><div class="col-xs-3"><h5>Size</h5></div>';
      for(var i=0 ; i<user_count ; i++) {
        html_user+='<li class="list"><div class="col-xs-7"><i class="fa fa-circle-o" style="color: '+ JSON.stringify(data[i].color).replace(/['"]+/g, '') +';"></i>' +' '+JSON.stringify(data[i].label).replace(/['"]+/g, '')+'</div><div class="col-xs-2">'+JSON.stringify(data[i].value).replace(/['"]+/g, '') +'</div><div class="col-xs-3">'+JSON.stringify(data[i].size).replace(/['"]+/g, '')+'MB</div></li>'
      }
      html_user+= '</ul>';
      document.getElementById("list_user").innerHTML = html_user;
}
// Extension number chart
if ($("#docextension").length ) 
{
      //var ctx_extension = $("#mycanvas_extension").get(0).getContext("2d");
      var data=response.extension;
      if(data=='empty'){
        /*$('#docextension').hide();*/
      }
      else{
      //var chart_extension = new Chart(ctx_extension).Doughnut(data,{tooltipFontSize: 10});
      var extension_count = data.length;

//Listing extension name and number of docs

      var html_ext = '<div class="col-xs-7"><ul class="chart-legend clearfix"><h5>Extension</h5>';
      for(var i=0 ; i<extension_count ; i++) {
        html_ext+='<li class="list"><i class="fa fa-circle-o" style="color: '+ JSON.stringify(data[i].color).replace(/['"]+/g, '') +';"></i>' +' '+JSON.stringify(data[i].label).replace(/['"]+/g, '') +'</li>'
      }
      html_ext+= '</ul></div>';
      html_ext+='<div class="col-xs-2"><ul class="chart-legend clearfix"><h5>No.</h5>';
      for(var i=0 ; i<extension_count ; i++) {
        html_ext+='<li style="text-align:center;">'+ JSON.stringify(data[i].count).replace(/['"]+/g, '') +'</li>'
      }
      html_ext+= '</ul></div>';

       html_ext+='<div class="col-xs-3"><ul class="chart-legend clearfix"><h5>Size</h5>';
      for(var i=0 ; i<extension_count ; i++) {
        html_ext+='<li>'+ JSON.stringify(data[i].value).replace(/['"]+/g, '')+'MB' +'</li>'
      }
      html_ext+= '</ul></div>';
      document.getElementById("list_extension").innerHTML = html_ext;
    }
}
// Media number chart
if ($("#docmedia").length ) 
{
      
      var data=response.file_size;
      if(data=='empty'){
        /*$('#docmedia').hide();*/
      }
      else{
      //var chart_extension = new Chart(ctx_extension).Doughnut(data,{tooltipFontSize: 10});
      var media_count = data.length;

//Listing media name and size

      var html_media = '<div class="col-xs-7"><ul class="chart-legend clearfix"><h5>File Categories</h5>';
      for(var i=0 ; i<media_count ; i++) {
        html_media+='<li class="list"><i class="fa fa-circle-o" style="color: '+ JSON.stringify(data[i].color).replace(/['"]+/g, '') +';"></i>' +' '+JSON.stringify(data[i].label).replace(/['"]+/g, '') +'</li>'
      }
      
      html_media+= '</ul></div>';
      html_media+='<div class="col-xs-2"><ul class="chart-legend clearfix"><h5>No.</h5>';
      for(var i=0 ; i<media_count ; i++) {
        html_media+='<li style="text-align:center;">'+ JSON.stringify(data[i].length)+'</li>'
      }
      html_media+= '</ul></div>';
       html_media+='<div class="col-xs-3"><ul class="chart-legend clearfix"><h5>Size</h5>';
      for(var i=0 ; i<media_count ; i++) {
        html_media+='<li>'+ JSON.stringify(data[i].size).replace(/['"]+/g, '')+'MB' +'</li>'
      }
      html_media+= '</ul></div>';
      document.getElementById("list_media").innerHTML = html_media;
    }
}
// Space chart
if ($("#diskspace").length ) 
{
      
      var data=response.disk_size;
      if(data=='empty'){
        /*$('#docmedia').hide();*/
      }
      else{
      //var chart_extension = new Chart(ctx_extension).Doughnut(data,{tooltipFontSize: 10});
      var disk_count = data.length;

//Listing space name and size

      var html_disk = '<div class="col-xs-7"><ul class="chart-legend clearfix"><h5>Space</h5>';
      for(var i=0 ; i<disk_count ; i++) {
        html_disk+='<li class="list"><i class="fa fa-circle-o" style="color: '+ JSON.stringify(data[i].color).replace(/['"]+/g, '') +';"></i>' +' '+JSON.stringify(data[i].label).replace(/['"]+/g, '') +'</li>'
      }
      
      html_disk+= '</ul></div>';
      html_disk+='<div class="col-xs-2"></div>';
       html_disk+='<div class="col-xs-3"><ul class="chart-legend clearfix"><h5>Size</h5>';
      for(var i=0 ; i<disk_count ; i++) {
        html_disk+='<li>'+ JSON.stringify(data[i].size).replace(/['"]+/g, '')+'GB' +'</li>'
      }
      html_disk+= '</ul></div>';
      document.getElementById("list_space").innerHTML = html_disk;
    }
}
//dept size chart
if ($("#docdepartment").length ) 
{
    var ctx_dept_size = $("#mycanvas_dept_size").get(0).getContext("2d");
      var data=response.dept_size;
      var chart_dept_size = new Chart(ctx_dept_size).Doughnut(data,{tooltipFontSize: 10,tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>MB"});

//Listing department size

      var sum_dept=0;
        //html_dept+='<div class="col-xs-3"><ul class="chart-legend clearfix"><h5>Size</h5>';
      for(var i=0 ; i<dept_count ; i++) {
        sum_dept += parseFloat(data[i].value);
        //html_dept+='<li>'+ JSON.stringify(data[i].value).replace(/['"]+/g, '')+'MB' +'</li>'
      }
        //html_dept+= '</ul></div>';
      document.getElementById("list_dept").innerHTML = html_dept;
      var textCtx = $("#text_dept").get(0).getContext("2d");
      textCtx.textAlign = "center";
      textCtx.textBaseline = "middle";
      textCtx.font = "12px sans-serif";
      textCtx.fillText(sum_dept.toFixed(2)+'MB', 65, 65);
      
}
//Document type size chart
if ($("#doctype").length ) 
{
      var ctx_type_size = $("#mycanvas_doctype_size").get(0).getContext("2d");
      var data=response.doctype;
      if(data != 'empty')
      {
        var chart_type_size = new Chart(ctx_type_size).Doughnut(data,{tooltipFontSize: 10,tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>MB"});
      }
      else
      {
        /*$('#doctype').hide();*/
      }

//Listing document type size

      var sum_type=0;
        //html_type+='<div class="col-xs-3"><ul class="chart-legend clearfix"><h5>Size</h5>';
      for(var i=0 ; i<type_count ; i++) {
        sum_type += parseFloat(data[i].value);
        //html_type+='<li>'+ JSON.stringify(data[i].value).replace(/['"]+/g, '')+'MB' +'</li>'
      }
        //html_type+= '</ul></div>';
      /*document.getElementById("list_type").innerHTML = html_type;*/
      var textCtx = $("#text_type").get(0).getContext("2d");
      textCtx.textAlign = "center";
      textCtx.textBaseline = "middle";
      textCtx.font = "12px sans-serif";
      textCtx.fillText(sum_type.toFixed(2)+'MB', 65, 65);
  }

// User Size chart
if ($("#docusers").length ) 
{
      var ctx_user_size = $("#mycanvas_user_size").get(0).getContext("2d");
      var data=response.user_size;
      var chart_user_size = new Chart(ctx_user_size).Doughnut(data,{tooltipFontSize: 10,tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>MB"});

//Listing user size

      var sum_user=0;
        //html_user+='<div class="col-xs-3"><ul class="chart-legend clearfix"><h5>Size</h5>';
      for(var i=0 ; i<user_count ; i++) {
        sum_user += parseFloat(data[i].value);
        //html_user+='<li>'+ JSON.stringify(data[i].value).replace(/['"]+/g, '')+'MB' +'</li>'
      }
        //html_user+= '</ul></div>';
      document.getElementById("list_user").innerHTML = html_user;
      var textCtx = $("#text_user").get(0).getContext("2d");
      textCtx.textAlign = "center";
      textCtx.textBaseline = "middle";
      textCtx.font = "12px sans-serif";
      textCtx.fillText(sum_user.toFixed(2)+'MB', 65, 65);
}
// Extension Size chart
if ($("#docextension").length ) 
{
      var ctx_extension_size = $("#mycanvas_extension_size").get(0).getContext("2d");
      var data=response.extension;
      if(data=='empty')
      {
        /*$('#docextension').hide();*/
      }
      else
      {
      var chart_extension_size = new Chart(ctx_extension_size).Doughnut(data,{tooltipFontSize: 10,tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>MB"});
      }

//Listing extension size

      var sum_ext=0;
      for(var i=0 ; i<extension_count ; i++) {
        sum_ext += parseFloat(data[i].value);
      }
      var textCtx = $("#text_ext").get(0).getContext("2d");
      textCtx.textAlign = "center";
      textCtx.textBaseline = "middle";
      textCtx.font = "12px sans-serif";
      textCtx.fillText(sum_ext.toFixed(2)+'MB', 65, 65);
    }
// Media Size chart
if ($("#docmedia").length ) 
{
      var ctx_media_size = $("#mycanvas_mediafile_size").get(0).getContext("2d");
      var data=response.file_size;
      if(data=='empty')
      {
        /*$('#docmedia').hide();*/
      }
      else
      {
      var chart_media_size = new Chart(ctx_media_size).Doughnut(data,{tooltipFontSize: 10,tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>MB"});
      }

//Listing media size

      var sum_media=0;
      for(var i=0 ; i<media_count ; i++) {
        sum_media += parseFloat(data[i].size);
      }
      var textCtx = $("#text_media").get(0).getContext("2d");
      textCtx.textAlign = "center";
      textCtx.textBaseline = "middle";
      textCtx.font = "12px sans-serif";
      textCtx.fillText(sum_media.toFixed(2)+'MB', 65, 65);
    }
// Space Size chart
if ($("#diskspace").length ) 
{
      var ctx_space_size = $("#mycanvas_diskspace_size").get(0).getContext("2d");
      var data=response.disk_size;
      if(data=='empty')
      {
        /*$('#docmedia').hide();*/
      }
      else
      {
      var chart_space_size = new Chart(ctx_space_size).Doughnut(data,{tooltipFontSize: 10,tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>GB"});
      }

//Listing media size

      var sum_space=0;
      for(var i=0 ; i<disk_count ; i++) {
        sum_space += parseFloat(data[i].size);
      }
      var textCtx = $("#text_space").get(0).getContext("2d");
      textCtx.textAlign = "center";
      textCtx.textBaseline = "middle";
      textCtx.font = "12px sans-serif";
      textCtx.fillText(sum_space.toFixed(2)+'GB', 65, 65);
    }
  }
  });
});
</script>
<script type="text/javascript" src="js/Chart.min.js"></script>
<style type="text/css">
ul h5{
  font-weight: 600;
  color: #337AB7;
}
 input[type=text] {
    width: 255px;
    padding: 6px 20px;
    margin: 8px 0;
    box-sizing: border-box;
}
.list {
    float: none !important;
  } 
</style>
@endsection