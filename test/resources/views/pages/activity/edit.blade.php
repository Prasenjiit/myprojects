<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
{!! Html::script('js/parsley.min.js') !!} 
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables/dataTables.bootstrap.css') !!}
<div class="modal-header">
        <strong>{{trans('activities.activities')}}</strong>
        <small>- {{trans('language.edit')}} {{trans('activities.activty')}}: {{ $activity[0]->activity_name }}</small>    
</div>
<div class="modal-body"> 
    <!-- form start -->
            {!! Form::open(array('url'=> array('activitySave'), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'addForm', 'id'=> 'addForm','data-parsley-validate'=> '')) !!}
            <!--Hidden fields--> 
            <input type="hidden" value="{{ $activity[0]->activity_id }}" name="activity_id"><!--To update activity-->  
            <div class="form-group">
                <label class="col-sm-2 control-label">{{Lang::get('activities.activty')}}: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <input class="form-control" id="name" placeholder="{{Lang::get('activities.activty')}}" required="" data-parsley-required-message="{{Lang::get('activities.activty')}} {{trans('language.is_required')}}" autofocus="autofocus" name="activity_name" value="{{ $activity[0]->activity_name }}"  title="{{Lang::get('activities.activty')}}">    
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-2"></div>
                <!-- <label class="col-sm-2 control-label">{{Lang::get('language.activty_module')}}: <span class="compulsary"></span></label> -->
                <div class="col-sm-8">
                @php 
                $activity_modules = ($activity[0]->activity_modules)?explode('-',$activity[0]->activity_modules):array();
                @endphp
                <!-- <label class="checkbox-inline"><input type="checkbox" name="activity_module[]" value="workflows" @php if(in_array('workflows',$activity_modules)){ echo 'checked'; } @endphp>Workflows</label> -->
                <label class="checkbox-inline"><input type="checkbox" name="activity_module[]" value="form_action" @php if(in_array('form_action',$activity_modules)){ echo 'checked'; } @endphp>Action Response</label>
                <!-- <label class="checkbox-inline"><input type="checkbox" name="activity_module[]" value="workflow_action" @php if(in_array('workflow_action',$activity_modules)){ echo 'checked'; } @endphp>Workflow Action</label>   --> 
                <p class="settingHelp">{{trans('activities.action_response_help_text')}}</p>
                </div>
            </div>
            <!-- <div class="form-group">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                <label class="checkbox-inline"><input type="checkbox" name="last_activity" value="1" <?php if($activity[0]->last_activity == 1){echo 'checked';} ?>>This is the last activity of a stage</label>
                <p class="settingHelp">{{$language['last_activity_help_text']}}</p>
                </div>
            </div> -->

            <div class="form-group">
                <div class="col-sm-2">
                 <label class="control-label">Workflow Action: </label>   

                </div>
                <div class="col-sm-8">
                   
                <label class="checkbox-inline"><input type="checkbox" name="activity_constant" class="cflags" id="capprove" value="approve" @php if($activity[0]->activity_constant == 'approve'){ echo 'checked'; } @endphp>Approve</label>

                <label class="checkbox-inline"><input type="checkbox" name="activity_constant" class="cflags" id="creject" value="reject" @php if($activity[0]->activity_constant == 'reject'){ echo 'checked'; } @endphp>Reject</label>

                <label class="checkbox-inline"><input type="checkbox" name="activity_constant" class="cflags" id="con-hold" value="on-hold" @php if($activity[0]->activity_constant == 'on-hold'){ echo 'checked'; } @endphp>On-Hold</label>
                <p class="settingHelp">Check this if you would like this activity to be added as Approve, Reject or On-Hold actions of the Workflow </p>
                </div>
            </div>
          <div>
            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                <div class="col-sm-8" style="text-align:right;">
                    <input class="btn btn-primary" id="save" type="submit" value="{{trans('language.save')}}" onclick="myFunction()"> &nbsp;&nbsp;
                    <a href="{{url("/$actionUrl")}}">{!!Form::button(trans('language.cancel'), array('class' => 'btn btn-primary btn-danger', 'id' => 'cn', 'data-dismiss'=> 'modal', 'aria-hidden'=> 'true')) !!}</a>
                </div>
            </div><!-- /.col -->
            {!! Form::close() !!}
        </div>
</div><!-- /.modal-dialog -->
<style type="text/css">
    [draggable] {
      -khtml-user-drag: element;
      -webkit-user-drag: element;
    }
    div.grippy {
      content: '....';
      width: 10px;
      
      display: inline-block;
      line-height: 5px;
      padding: 3px 4px;
      cursor: move;
      vertical-align: middle;
      margin-top: -.7em;
      margin-right: .3em;
      font-size: 14px;
      font-family: sans-serif;
      letter-spacing: 2px;
      color: black;
      text-shadow: 1px 0 1px black;
    }
    div.grippy::after {
      content: '.. .. .. ..';
    }
    .slide-placeholder {
        background: #ecf0f5;
        position: relative;
    }
    .slide-placeholder:after {
        content: " ";
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 0;
        background-color: #FFF;
    }
</style>
<script type="text/javascript">

$(document).ready(function(){
   
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