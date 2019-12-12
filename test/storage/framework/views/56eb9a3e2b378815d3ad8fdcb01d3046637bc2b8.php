<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?> 

<?php $__env->startSection('main_content'); ?>
<?php echo Html::script('js/parsley.min.js'); ?>  
<?php echo Html::style('css/fastselect.min.css'); ?> 
<?php echo Html::script('js/fastselect.standalone.js'); ?> 
<style type="text/css">
    .form_row,.form_row_choice{ margin-top: 3px; margin-bottom: 3px; }
    .test_button{background-color: #f4f4f4; color: #444; border-color: #ddd;     text-align: left !important;}
    .nopadding_class{ padding: 0 !important; margin: 0 !important; }
    .form_class{ margin: 1px !important;}
    .stick {
        /*position:fixed;
        top:0px;*/
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
    div.grippysmall {
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
    div.grippysmall::after {
        content: '.. .. ..';
    }
    .activity_row{display: none;}
    .fstElement{
        width: 100%;
    }
    .fstControls{
        width: 100% !important;
    }
    .chart{
        height: 257px;
    }
    .fstResults
    {
        max-height: 124px !important;
        overflow-y: scroll !important;
    }
    .sticky-scroll-box{
        background:#f9f9f9;
        box-sizing:border-box;
        -moz-box-sizing:border-box;
        -webkit-box-sizing:border-box;
    }
    .fixed {
        position:fixed;
        top:0;
        z-index:99999;
    }
    .help{
      font-size:12px; color:#999;
    }
</style>
<section class="content">
<div class="row">
  <!--Checking module permission-->
<?php if(Session::get('enbval4')==Session::get('tval')): ?>   
  <div class="col-sm-12 alert_space" id="spl-wrn">
                </div>
    <!-- left column -->
    <div class="col-md-3">
        <!-- general form elements -->
        <div class="box box-info sticky-scroll-box">
            <div class="box-header with-border">
                <h3 class="box-title">Form components</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form">
                <div class="box-body">
                    <input type="hidden" name="form_id" id="form_id" value="<?php echo e($form_id); ?>">
                    <input type="hidden" name="form_url" id="form_url" value="<?php echo e(url('save_dynamic_form')); ?>">
                    <input type="hidden" name="return_form_url" id="return_form_url" value="<?php echo e(url('forms')); ?>">
                    <input type="hidden" name="loadformurl" id="loadformurl" value="<?php echo e(url('load_form')); ?>">
                    <ul id="add-field">
                        <?php $__currentLoopData = $form_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <button type="button" class="btn btn-block test_button btn-xs add-field form_class" id="add-<?php echo e($row_type->form_input_type_value); ?>" data-type="<?php echo e($row_type->form_input_type_value); ?>" data-typename="<?php echo e($row_type->form_input_type_name); ?>" data-is_required="<?php echo e($row_type->is_required); ?>" data-is_options="<?php echo e($row_type->is_options); ?>" data-type_common="<?php echo e($row_type->form_input_type_common); ?>" data-default_value="<?php echo e($row_type->is_default_value); ?>"><i class="<?php echo e($row_type->form_input_icon); ?>"></i> <?php echo e($row_type->form_input_type_name); ?></button>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>     
                    </ul>
                </div>
                <!-- /.box-body -->
                <div class="box-footer"></div>
            </form>
        </div>
        <!-- /.box -->
        <!-- Form Element sizes -->
        <!-- Input addon -->
    </div>
    <!--/.col (left) -->

         <!-- right column -->
        <div class="col-md-9">
          <!-- general form elements -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Form </h3>
              <p style="font-size:12px; color:#999;"><?php echo e(trans('forms.form_help1')); ?></p>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" id="sjfb" name="sjfb" data-parsley-validate="data-parsley-validate">
              <div class="box-body">
             
                <div class="row form_row">
                <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Form Name: <span class="compulsary">*</span></label></label>

                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="form_name" name="form_name" required="" data-parsley-required-message="Form Name is required" data-parsley-trigger="change focusout">
                  </div>
                </div>
                </div>

                <div class="row form_row">
                <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Form Description: </label>

                  <div class="col-sm-9">
                    <textarea class="form-control" id="form_description" name="form_description"></textarea>
                  </div>
                </div>
                </div>

                <!-- code disabled bug no:653(Assigned to) -->
                <?php
                /*<div class="row form_row">
                <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Assigned To: </label>

                  <div class="col-sm-9">
                     <select class="form-control assigned_to" id="assigned_to"   name="assigned_to">
                        <option value="">{{ $language['select_user'] }}</option>
                           <?php
                            foreach ($users as $key => $user) {
                                ?>
                                <option value="<?php echo $user->id; ?>"><?php echo ucfirst(@$user->user_full_name);?> -{{@$user->user_role}}@if(@$user->departments[0]->department_name != "")- {{ ucfirst(@$user->departments[0]->department_name) }} @endif</option>
                                <?php
                            }
                            ?> 
                        </select>
                        <p class="help">{{$language['users_with_roles']}}</p>
                        <p class="help">{{$language['form_help2']}}</p>
                  </div>
                </div>
                </div>
                

                <hr class="activity_row">
                <div class="row form_row">

                <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Workflow: </label>

                  <div class="col-sm-9">
                     <select name="workflow_id" id="workflow_id" class="form-control workflow_id">
               <option value="0">{{ $language['select_workflow'] }}</option>
               @foreach ($workflows as $wf_row)  
               <option value="{{$wf_row->workflow_id}}">{{$wf_row->workflow_name}}</option>
               @endforeach
            </select>
            <p style="font-size:12px; color:#999;">{{$language['form_help3']}}</p>
                  </div>
                </div>
                </div>*/
              ?>
                <div class="row form_row activity_row" >
                <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Activity: </label>

                  <div class="col-sm-9">
                     <select name="activity_id" id="activity_id" class="form-control activity_id">
                            <option value="0"><?php echo e(trans('forms.select_activity')); ?></option>
                           <?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                           <option value="<?php echo e($row->activity_id); ?>"><?php echo e($row->activity_name); ?></option>
                           <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>  
            </select>
            
                  </div>
                </div>
                </div>
                <hr class="activity_row">
              </div>

              <div class="box-header with-border">
                <h3 class="box-title">Form Permissions</h3>
                <p style="font-size:12px; color:#999;"><?php echo e(trans('forms.form_note')); ?></p>
              </div>

              <div class="box-footer" style="">
                <div class="nav-tabs-custom"> 
                    <!--For set active class if smtp table is empty(truncated)-->
                                                                                                            
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs pull-left ui-sortable-handle">
                      <li class="active"><a href="#add-tab" data-toggle="tab" class="smtp-btn" aria-expanded="true">Add</a></li>
                      <li class=""><a href="#edit-tab" data-toggle="tab" class="smtp-btn" aria-expanded="false">Edit</a></li>
                      <li class=""><a href="#delete-tab" data-toggle="tab" class="smtp-btn" aria-expanded="false">Delete</a></li>
                      <li class=""><a href="#view-tab" data-toggle="tab" class="smtp-btn" aria-expanded="false">View</a></li>
                    </ul>
                    <!--Define email provider-->
                    <div class="tab-content no-padding"> 
                        <!-- view -->
                        <div class="chart tab-pane" id="view-tab">
                          <div class="row form_row">
                            <div class="form-group">
                              <label for="" class="col-sm-2 control-label">Departments: </label>

                              <div class="col-sm-9">
                                 <select name="view_departmentid[]" id="view_departmentid" class="multipleSelect form-control" multiple >
                                    <?php
                                    foreach ($deptApp as $key => $dept) {
                                    ?>
                                    <option value="<?php echo $dept->department_id; ?>"><?php echo $dept->department_name;?></option>
                                    <?php
                                    }
                                    ?>
                                </select> 
                                <p style="font-size:12px; color:#999;">Choose department(s) who can view a form</p>  
                              </div>
                            </div>
                          </div>

                          <div class="row form_row">
                            <div class="form-group">
                              <label for="" class="col-sm-2 control-label">Users: </label>

                              <div class="col-sm-9">
                                 <select class="form-control multipleSelect" id="view_userid"   name="view_userid[]" multiple >
                                                          
                                 <?php
                                  foreach ($users as $key => $user) {
                                      ?>
                                      <option value="<?php echo $user->id; ?>"><?php echo ucfirst(@$user->user_full_name);?> -<?php echo e(@$user->user_role); ?><?php if(@$user->departments[0]->department_name != ""): ?>- <?php echo e(ucfirst(@$user->departments[0]->department_name)); ?> <?php endif; ?></option>
                                      <?php
                                  }
                                  ?>  
                                </select> 
                                <p class="help">Choose user(s) who can view a form</p>
                                <p class="help"><?php echo e(trans('language.users_with_roles')); ?></p>
                                
                              </div>
                            </div>
                          </div>
                          
                          <div class="row form_row">
                            <div class="form-group">
                              <label for="" class="col-sm-2 control-label"></label>
                                  
                                  <div class="col-sm-9"> 
                                      <input type="checkbox" id="view_authentication"  name="view_authentication" data-original-title="" title="" value="view" checked style="display:none;">
                                  </div>
                              </div>
                          </div>
                        </div>
                        <!-- Delete -->
                        <div class="chart tab-pane" id="delete-tab">
                          <div class="row form_row">
                            <div class="form-group">
                              <label for="" class="col-sm-2 control-label">Departments: </label>

                              <div class="col-sm-9">
                                 <select name="delete_departmentid[]" id="delete_departmentid" class="multipleSelect form-control" multiple >
                                    <?php
                                    foreach ($deptApp as $key => $dept) {
                                    ?>
                                    <option value="<?php echo $dept->department_id; ?>"><?php echo $dept->department_name;?></option>
                                    <?php
                                    }
                                    ?>
                                </select> 
                                <p style="font-size:12px; color:#999;">Choose department(s) who can delete a form</p>  
                              </div>
                            </div>
                          </div>

                          <div class="row form_row">
                            <div class="form-group">
                              <label for="" class="col-sm-2 control-label">Users: </label>

                              <div class="col-sm-9">
                                 <select class="form-control multipleSelect" id="delete_userid"   name="delete_userid[]" multiple >
                                                          
                                 <?php
                                  foreach ($users as $key => $user) {
                                      ?>
                                      <option value="<?php echo $user->id; ?>"><?php echo ucfirst(@$user->user_full_name);?> -<?php echo e(@$user->user_role); ?><?php if(@$user->departments[0]->department_name != ""): ?>- <?php echo e(ucfirst(@$user->departments[0]->department_name)); ?> <?php endif; ?></option>
                                      <?php
                                  }
                                  ?>  
                                </select> 
                                <p class="help">Choose user(s) who can delete a form</p>
                                <p class="help"><?php echo e(trans('language.users_with_roles')); ?></p>
                                
                              </div>
                            </div>
                          </div>
                          
                          <div class="row form_row">
                            <div class="form-group">
                              <label for="" class="col-sm-2 control-label"></label>
                                  
                                  <div class="col-sm-9"> 
                                      <input type="checkbox" id="delete_authentication"  name="delete_authentication" data-original-title="" title="" value="delete" checked style="display:none;">
                                  </div>
                              </div>
                          </div>
                        </div>
                        <!-- Edit -->
                        <div class="chart tab-pane" id="edit-tab">
                          <div class="row form_row">
                            <div class="form-group">
                              <label for="" class="col-sm-2 control-label">Departments: </label>

                              <div class="col-sm-9">
                                 <select name="edit_departmentid[]" id="edit_departmentid" class="multipleSelect form-control" multiple >
                                    <?php
                                    foreach ($deptApp as $key => $dept) {
                                    ?>
                                    <option value="<?php echo $dept->department_id; ?>"><?php echo $dept->department_name;?></option>
                                    <?php
                                    }
                                    ?>
                                </select> 
                                <p style="font-size:12px; color:#999;">Choose department(s) who can edit a form</p>  
                              </div>
                            </div>
                          </div>

                          <div class="row form_row">
                            <div class="form-group">
                              <label for="" class="col-sm-2 control-label">Users: </label>

                              <div class="col-sm-9">
                                 <select class="form-control multipleSelect" id="edit_userid"   name="edit_userid[]" multiple >
                                                          
                                 <?php
                                  foreach ($users as $key => $user) {
                                      ?>
                                      <option value="<?php echo $user->id; ?>"><?php echo ucfirst(@$user->user_full_name);?> -<?php echo e(@$user->user_role); ?><?php if(@$user->departments[0]->department_name != ""): ?>- <?php echo e(ucfirst(@$user->departments[0]->department_name)); ?> <?php endif; ?></option>
                                      <?php
                                  }
                                  ?>  
                                </select> 
                                <p class="help">Choose user(s) who can edit a form</p>
                                <p class="help"><?php echo e(trans('language.users_with_roles')); ?></p>
                                
                              </div>
                            </div>
                          </div>
                          
                          <div class="row form_row">
                            <div class="form-group">
                              <label for="" class="col-sm-2 control-label"></label>
                                  
                                  <div class="col-sm-9"> 
                                      <input type="checkbox" id="edit_authentication"  name="edit_authentication" data-original-title="" title="" value="edit" checked style="display:none;">                               </div>
                              </div>
                          </div>
                        </div>
                        <!--Add-->
                        <div class="chart tab-pane  active" id="add-tab">
                          <div class="row form_row">
                            <div class="form-group">
                              <label for="" class="col-sm-2 control-label">Departments: </label>

                              <div class="col-sm-9">
                                 <select name="add_departmentid[]" id="add_departmentid" class="multipleSelect form-control" multiple >
                                    <?php
                                    foreach ($deptApp as $key => $dept) {
                                    ?>
                                    <option value="<?php echo $dept->department_id; ?>"><?php echo $dept->department_name;?></option>
                                    <?php
                                    }
                                    ?>
                                </select> 
                                <p style="font-size:12px; color:#999;">Choose department(s) who can add a form</p>  
                              </div>
                            </div>
                          </div>

                          <div class="row form_row">
                            <div class="form-group">
                              <label for="" class="col-sm-2 control-label">Users: </label>

                              <div class="col-sm-9">
                                 <select class="form-control multipleSelect" id="add_userid"   name="add_userid[]" multiple >
                                                          
                                 <?php
                                  foreach ($users as $key => $user) {
                                      ?>
                                      <option value="<?php echo $user->id; ?>"><?php echo ucfirst(@$user->user_full_name);?> -<?php echo e(@$user->user_role); ?><?php if(@$user->departments[0]->department_name != ""): ?>- <?php echo e(ucfirst(@$user->departments[0]->department_name)); ?> <?php endif; ?></option>
                                      <?php
                                  }
                                  ?>  
                                </select> 
                                <p class="help">Choose user(s) who can add a form</p>
                                <p class="help"><?php echo e(trans('language.users_with_roles')); ?></p>
                                
                              </div>
                            </div>
                          </div>
                          
                          <div class="row form_row">
                            <div class="form-group">
                              <label for="" class="col-sm-2 control-label"></label>
                                  
                                  <div class="col-sm-9"> 
                                      <input type="checkbox" id="add_authentication"  name="add_authentication" data-original-title="" title="" value="add" checked style="display:none;">                               </div>
                              </div>
                          </div>
                        </div>
                       
                    </div><!--//End Tabs within a box -->
                </div>
              </div>

              <div class="box-body" id="form-fields">  
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="button" id="save_form" class="btn btn-primary save_dy_form" value="save" style="float: left; margin-left: 5px;">Save</button>
                <button type="button" id="save_close" class="btn btn-primary save_dy_form" value="save_close" style="float: left; margin-left: 5px;">Save & Close</button>
                <button type="button" name="btn_cancel" id="btn_cancel" class="btn btn-danger btn_cancel" style="float: left; margin-left: 5px;">Cancel</button>
                <div class="preloader" style="float: left; margin-top: 5px; display: none;" >
                    <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>
                    <span class="sr-only">Loading...</span>
                </div>

              </div>
            </form>
          </div>
          <!-- /.box -->

          <!-- Form Element sizes -->
          

       

          <!-- Input addon -->
          

        </div>
        <!--/.col (right) -->
       <?php elseif(Session::get('enbval4')==Session::get('fval')): ?>
        <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty"><?php echo e(trans('language.purchase_now')); ?></div></section>
    <?php else: ?>
        <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty"><?php echo e(trans('language.module_expired')); ?></div></section>
    <?php endif; ?>
      </div>
      <!-- /.row --></section>
   

    <?php echo Html::script('js/jquery-ui.min.js'); ?>

<?php echo Html::script('plugins/simple-jquery-form-builder/js/sjfb-builder.js'); ?>

<script>

$('.multipleSelect').fastselect();
 $(document).ready(function() {
  setInterval(function (){ $('#spl-wrn').html('');}, 30000);
    
    var top = $('.sticky-scroll-box').offset().top;
    $(window).scroll(function (event) {
    var y = $(this).scrollTop();
        if (y >= top) {
          $('.sticky-scroll-box').addClass('fixed');
        } else {
          $('.sticky-scroll-box').removeClass('fixed');
        }
        $('.sticky-scroll-box').width($('.sticky-scroll-box').parent().width());
    });


    var s = $("#sticker");
    var pos = s.position();                    
    $(window).scroll(function() {
        var windowpos = $(window).scrollTop();
    });

    $('.btn_cancel').click(function()
    {
        var return_form_url = $('#return_form_url').val();
        window.location.href = return_form_url;   
      });

    $('.workflow_id').on('change', function() 
    {
     var check=  $(this).val();
     console.log(check);
     if(check !=0)
     {
        $(".activity_row").slideDown();
     }
     else
     {
       $(".activity_row").slideUp();
     }
    
   });
});

</script>  
   
  <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\nspl5.8\resources\views/pages/forms/index.blade.php ENDPATH**/ ?>