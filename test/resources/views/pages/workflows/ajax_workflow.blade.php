<?php
$user_role = Auth::user()->user_role;
$user_id = Auth::user()->id;
$dept_id = Auth::user()->department_id;
$user_role = Auth::user()->user_role;
$wf_permission = Auth::user()->user_workflow_permission;
?>
<style type="text/css">
    .title a:hover{
        text-decoration:none;
        background-color: none !important;
    }
    #test { font-size:0; }
    #test a { font-size:16px;}
</style>
<div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <label class="">Workflow: @php echo ($wf_details)?$wf_details->workflow_name:'All'; @endphp</h3>
            </label>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="box-group" id="accordion">

              @php  foreach($workflow_process as $r){ @endphp 
               <div class="box-header" style="border:1px solid;margin: 3px 0px 3px 0px; " id="WFprocess{{$r->process_id}}">
                 <div class="row">
               <div class="col-md-8">    

                        Process : {{$r->wf_operation_name}}
                      <!-- <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" class="collapsed">
                        Process : {{$r->wf_operation_name}}
                      </a> -->
                      
                    </div>
                    <div class="col-md-4">
                      <label class="pull-right"><span style="padding-left: 5px;">Last Updated: {{ dtFormat($r->last_updated_at) }}</span></label>

                  <?php if($user_role==1 || $user_role==2){ ?>
                    <label class="pull-right"><i class="fa fa-close" onclick="wfpdel({{$r->process_id}},'{{$r->wf_operation_name}}')" title="Delete" style="color: red; cursor:pointer;"></i></label>
                  <?php }else{
                    if(count($wf_privileges_delete)>0){
                        foreach ($wf_privileges_delete as $val) {                    
                            $wfprevkey_tmp = $val->privilege_key;
                            $wfprevkey = explode(',',$wfprevkey_tmp);
                            if(in_array('delete', $wfprevkey) && $val->privilege_status==1){ 
                                $uservalue = $val->privilege_value_user; 
                                $deptvalue = $val->privilege_value_department;
                                $key_user_value = explode(',',$uservalue);
                                $key_dept_value = explode(',',$deptvalue);                                            
                                if(in_array($user_id, $key_user_value) || in_array($dept_id, $key_dept_value)) { ?>
                                    <label class="pull-right"><i class="fa fa-close" onclick="wfpdel({{$r->process_id}},'{{$r->wf_operation_name}}')" title="Delete" style="color: red; cursor:pointer;"></i></label>
                                <?php }
                            }
                        }
                    }
                  } ?> 
                    
                    </div>
                <div class="col-md-12 title">   
                    <?php if($r->wf_object_type=="document"){ ?>
                        <a href="{{ url('documentManagementView') }}?dcno={{$r->wf_object_id}}&page=document" title="Open Document" target="_blank" style="cursor:pointer;">
                            <i class="{{$r->obj_icon}}" title="{{$r->obj_label}}"></i> {{$r->obj_name}}
                        </a>
                    <?php }else if($r->wf_object_type=="form"){ ?>
                        <a href="{{ url('form_details') }}/{{$r->wf_object_type_id}}?response={{$r->wf_object_id}}" title="Open Form" target="_blank" style="cursor:pointer;">
                            <i class="{{$r->obj_icon}}" title="{{$r->obj_label}}"></i> {{$r->obj_name}}
                        </a> 
                    <?php } ?> 
                      <!-- <label><i class="{{$r->obj_icon}}" title="{{$r->obj_label}}"></i> {{$r->obj_name}}</label> -->
                    </div>    
                 
               <div class="col-md-6" id="test"> 
               @php 
               $tasks = ($r->tasks)?$r->tasks:array();
               $task_count = sizeof($tasks);
               $percentage = ($task_count)?(95/$task_count):0;
               @endphp    
  @php $c=0;foreach($tasks as $t){ 
  $c++;
  $class='progress-bar-warning';
  $style='background-color: #ffffff;';
  if($t->state_completed == 1)
  {
    $class='progress-bar-primary';
    $style='background-color:'.$wf_details->workflow_color.';';
  }
  else if($t->state_completed == 2)
  {
    $class='progress-bar-success';
    $style='background-color:'.$wf_details->workflow_color.';';
  }
  $style .= 'border-right: 1px solid #A9A9A9;border-top: 1px solid #A9A9A9;border-bottom: 1px solid #A9A9A9;';
  if($c == 1)
  {
    $style .= 'border-left: 1px solid #A9A9A9;';
  }

  $compl_activity_name = '';
  if($t->type != 'last' && $t->state_completed)
  { 
      $compl_activity_name = ($t->state_completed_activity_name)?' - Status: '.$t->state_completed_activity_name:'';
  }
  if($t->state_completed == '3')
  { 
      $compl_activity_name = ' - Stage Skipped';
  }
  if($t->type == 'last' && $t->state_completed)
  { 
      $compl_activity_name = ($r->completed_activity_name)? ' - Status: '.$r->completed_activity_name:'';
  }
  @endphp       
  
 <!--  <a href="@php echo URL('view_wf_process/'.$r->process_id).'?stage='.$t->wf_stage; @endphp" class="progress-bar tooltip-item" role="progressbar" style="{{$style}} width:{{$percentage}}%; @if($c != 1)border-left:1px solid white; @endif @if($c != $task_count) border-right:1px solid white; @endif" data-message="@php echo $t->wf_stage_name; @endphp">
  </a> -->
  <a href="@php echo URL('view_wf_process/'.$r->process_id).'?stage='.$t->wf_stage; @endphp" data-message="@php echo $t->wf_stage_name; echo $compl_activity_name; @endphp" class="tooltip-item" style="{{$style}} height:10px;">
 <div style="text-align:center;display:inline-block; width:{{$percentage}}%;">@if($t->state_completed == 3)<i class="fa fa-fw fa-ban"></i>@endif</div></a>
  @php } @endphp 

</div>
<div class="col-md-2">{{$task_count}} stages </div>
</div> <!--row -->
                  </div>
              @php } @endphp  
         
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>

        <script type="text/javascript">
  (function(selector, horizontalOffset, verticalOffset) {
  var items;
  
  selector = selector || '.tooltip';
  horizontalOffset = horizontalOffset || 5;
  verticalOffset = verticalOffset || 5;
  
  items = document.querySelectorAll(selector);
  items = Array.prototype.slice.call(items);
  
  items.forEach(function(item) {
    // Every time the pointer moves over the element the 
    //  CSS-rule in overwritten with new values for 
    //  top and left.
    item.addEventListener('mousemove', function(e) {
      let countCssRules = document.styleSheets[0].cssRules.length;
      let newRule = selector +
        ':hover:after { display: block; ' + 
                        'left: ' + '0px; ' +
                        'top: ' +  (e.offsetY + verticalOffset) + 'px; }';
      
      document.styleSheets[0].insertRule(newRule, countCssRules);
    });
  });
})('.tooltip-item', 0, -40);

</script>
