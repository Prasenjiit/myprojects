<?php
  include (public_path()."/storage/includes/lang1.en.php" );
  $user_permission=Auth::user()->user_permission;
?>
@extends('layouts.app')
@section('main_content')
{!! Html::script('js/parsley.min.js') !!}  
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables/dataTables.bootstrap.css') !!}

<style type="text/css">
/*<--Style for work flowhistory-->*/
.stages{
    position: absolute;
    left: 14px;
}
.tab-pane {
    background-color: #fbfbfb;
}
span.wk-title {
    color: white;
}

</style>

<section class="content-header">
   
   <div class="col-sm-8" style="padding-bottom: 7px;">
        <span style="float:left;">
            <strong>
                {{$language['workfow_history']}}
                
            </strong> &nbsp;
        </span>
        <span style="float:left;">
            <small>{{@$document[0]->document_name}}</small>
        </span>
    </div>
    <div class="col-sm-4">
        <!-- <ol class="breadcrumb">
            <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
            <li class="active"><a href="#" onclick="goBack()">{{$language['back']}}</a></li>
       </ol> -->
    </div>

</section>

<section class="content">

      <!-- row -->
      <div class="row">
        <div class="col-md-12">
         @if(@$data)
          <ul class="timeline">
            <!-- loop start -->
            <?php $count=0;?>
            @foreach($data as $val)
            <li class="time-label">
                  <span class="" style="background-color:{{@$val->workflow_color}}">
                    <span class="wk-title">{{@$val->workflow_name}}</span> 
                  </span>
                  
            </li>
            <!-- /.timeline-label -->
            <!-- Stage repeat here -->
            <?php $stg=0;?>
            @foreach($val->stages as $stage)
            <li>
                <span class="stages">
                    <b>{{$stage->workflow_stage_name}}</b>
                </span></br>
                 

                <div class="timeline-item">
                    <div class="timeline-body">
                       <!---->
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <?php $i=1;?>
                                @foreach($stage->maindate as $min)
                                <li  class="<?php if(1%$i == 0): echo "active";?> <?php endif;?>"><a href="#tab_{{$count}}{{$stg}}{{$i}}" data-toggle="tab" aria-expanded="true">{{$min->main_date}}</a></li>
                                <?php $i++;?>
                                @endforeach
                            </ul>
                        
                            <div class="tab-content">
                            <!--Tab starts-->
                            <?php $j=1;?>
                            @foreach($stage->maindate as $tab)
                                <div class="tab-pane <?php if(1%$j == 0): echo "active";?> <?php endif;?>" id="tab_{{$count}}{{$stg}}{{$j}}">
                                    <!-- Post -->
                                    @foreach($tab->activityDdtails as $det)
                                    <div class="post">
                                        <div class="block">
                                              {{Lang::get('language.activty')}}: {{$det->activity_name}}
                                        </div>
                                        <!-- /.content -->
                                        <div class="form-horizontal">
                                            <div class="form-group"> 
                                            <div class="row"> 
                                                <div class="col-sm-12"> 
                                                        <div class="col-sm-1 hidden-sm"></div>
                                                        <div class="col-sm-3">{{Lang::get('language.assigned_to')}}:
                                                        <i class="fa fa-fw fa-user"></i> {{$det->responsible_user}}</div>

                                                        <div class="col-sm-3">{{Lang::get('language.due_date')}}:
                                                        <i class="fa fa-clock-o"></i> {{$det->document_workflow_activity_due_date}}</div>

                                                        <div class="col-sm-2">{{Lang::get('language.assigned_by')}}:
                                                        <i class="fa fa-fw fa-user"></i> {{$det->activity_by_user}}</div> 

                                                        <div class="col-sm-3">{{Lang::get('language.Date')}}:
                                                        <i class="fa fa-clock-o"></i> {{$det->document_workflow_activity_date}}</div>
                                                        <!-- <div class="col-sm-1 hidden-sm"></div> -->
                                                </div> 
                                            </div> 
                                            </div>
                                                
                                            <div class="direct-chat-msg">
                                                <span class="direct-chat-name pull-left"><i class="fa fa-sticky-note" aria-hidden="true"></i> {{Lang::get('language.note')}}</span>
                                        
                                                <div class="direct-chat-text">
                                                @if($det->document_workflow_activity_notes)
                                                    {{$det->document_workflow_activity_notes}}
                                                @else
                                                    {{$language['no_note_found']}}
                                                @endif
                                                </div>
                                                <!-- /.direct-chat-text -->
                                            </div>

                                            @php if($det->action_activity_name){ @endphp    
                                            <div class="form-group"> 
                                            <div class="row"> 
                                                <div class="col-sm-12"> 
                                                        <div class="col-sm-1 hidden-sm"></div>
                                                        <div class="col-sm-3">{{Lang::get('language.action_status')}}:
                                                        <i class="fa fa-fw fa-user"></i> {{$det->action_activity_name}}</div>

                                                        <div class="col-sm-3">{{Lang::get('language.action_activity_date')}}:
                                                        <i class="fa fa-clock-o"></i> @php echo date('Y-m-d',strtotime($det->action_activity_date)); @endphp</div>

                                                        <div class="col-sm-4">{{Lang::get('language.action_activity_note')}}:
                                                        <i class="fa fa-fw fa-user"></i> {{$det->action_activity_note}}</div> 

                                                        
                                                        <!-- <div class="col-sm-1 hidden-sm"></div> -->
                                                </div> 
                                            </div> 
                                            </div>
@php } @endphp
                                        </div>
                                        <!--content closed-->
                                    </div>
                                    @endforeach
                                    <!-- /.post -->
                                    <?php $j++;?>
                                  </div>
                                @endforeach
                                <!--End of tab loop -->
                            </div>
                                <!-- /.tab-content -->
                              </div>
                              <!-- /.nav-tabs-custom -->
                       <!---->
                    </div>
                    
                </div>
            </li>
            <?php $stg++;?>
            @endforeach
            <?php $count++;?>
            @endforeach
            <!-- END of loop --> 
          </ul>
          @else
            <div class="callout callout-info">
                <h4 style="text-align: center;">{{$language['no_history']}}</h4>
            </div>
          @endif
        </div>
      </div>
      <!-- /.row -->
</section>
<script type="text/javascript">
    function goBack() {
    window.history.back();
}
</script>
@endsection

