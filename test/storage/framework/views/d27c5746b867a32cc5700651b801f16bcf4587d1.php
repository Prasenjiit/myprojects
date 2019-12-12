<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?>

<?php $__env->startSection('main_content'); ?>
<?php echo Html::style('plugins/datatables_new/fixedHeader.dataTables.min.css'); ?>

<?php echo Html::style('plugins/datatables_new/jquery.dataTables.min.css'); ?>

<?php echo Html::style('plugins/datatables_new/colReorder.dataTables.min.css'); ?>

<?php echo Html::script('js/parsley.min.js'); ?>


<?php
    $user_id = Auth::user()->id;
    $dept_id = Auth::user()->department_id;
    $user_role = Auth::user()->user_role;
    $form_permission = Auth::user()->user_form_permission;
?>
<section class="content-header">
  <div class="col-xs-8">
    <span style="float:left;">
      <strong><?php echo e(trans('forms.forms')); ?></strong> &nbsp;
    </span>
    <!-- super admin can only add forms -->
    <?php if(stristr($form_permission,"add")): ?>
    <span style="float:left;">
      <a href="<?php echo e(URL::route('form')); ?>">
          <button class="btn btn-block btn-info btn-flat newbtn"><?php echo e(trans('language.add new')); ?>  <i class="fa fa-plus"></i></button>
      </a>
    </span>
    <?php endif; ?>
    <!-- end super admin can only add forms -->
  </div>
  <div class="col-xs-4">
   <!--  <ol class="breadcrumb">
        <li><a href="<?php echo e(url('/home')); ?>"><i class="fa fa-dashboard"></i> <?php echo e(trans('language.home')); ?></a></li>
        <li class="active"><?php echo e(trans('forms.forms')); ?></li>
    </ol> -->
  </div>
</section>
<section class="content" id="shw">
<div class="row">
    <?php if(Session::has('flash_message_success')): ?>
    <section class="content content-sty" id="spl-wrn">        
        <div class="alert alert-sty <?php echo e(Session::get('alert-class', 'alert-info')); ?> "><?php echo e(Session::get('flash_message_success')); ?></div>        
    </section>
    <?php endif; ?>
    <?php if(Session::has('flash_message_warning')): ?>
    <section class="content content-sty" id="spl_warn">        
        <div class="alert alert-warning alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-warning"></i> Alert!</h4>
        <?php echo e(Session::get('flash_message_warning')); ?>

        <?php echo e(Session::forget('flash_message_warning')); ?>

      </div>
    </section>
    <?php endif; ?>
    <!--Checking module permission-->
<?php if(Session::get('enbval4')==Session::get('tval')): ?>     
        <?php if(stristr($form_permission,"view")): ?>
        <div class="col-xs-12">
            <div class="box box-info">
                <div class="box-body" style="overflow-x: auto; overflow-y: auto;">
                    <table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable hover" width="100%">
                        <thead>
                            <tr>
                                <th><?php echo e(trans('forms.form_name')); ?></th>
                                <th><?php echo e(trans('language.description')); ?></th> 
                                <th><?php echo e(trans('language.created by')); ?></th>                           
                                <th><?php echo e(trans('language.created date')); ?></th>                         
                                <th><?php echo e(trans('language.actions')); ?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td><input type="text" id="form_column" placeholder="Search Forms"></td>
                                <td></td>
                                <!-- <td></td> -->
                                <!-- <td></td> -->
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                        <tbody>

                            <?php $__currentLoopData = $dglist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="even" role="row">                                
                                <td>
                                    <a <?php if(Auth::user()->user_role == 1): ?> href="<?php echo e(URL::route('form',$val->form_id)); ?>" style="cursor: pointer;" <?php endif; ?> id="editform">
                                        <?php echo e($val->form_name); ?>

                                    </a>
                                </td>
                                <td><?php echo e($val->form_description); ?></td>
                                <td><?php $__currentLoopData = $val->created_by; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo e(@ucfirst($user->user_full_name)); ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>
                                <td nowrap="nowrap"><?php echo e($val->created_at); ?></td>
                                <td nowrap="nowrap">
                                <a title="View Submited Forms" id="details" href="<?php echo e(URL::route('form_details',$val->form_id)); ?>">
                                        <i class="fa  fa-list-ol" style="cursor:pointer;"></i>
                                </a> 
                                &nbsp;
                                    <?php 
                                        if($user_role==1 || $user_role==2){ ?>
                                            <a id="viewform" class="viewform" form_id='<?php echo e($val->form_id); ?>' style="cursor: pointer;" data-toggle="modal" data-target="#view_form" title="Submit this Form">
                                                <i class="fa fa-newspaper-o" style="cursor:pointer;"></i>
                                            </a>
                                        <?php }else{ 
                                            if(@$val->form_privileges[3]->privilege_key=="add"){
                                                $uservalue = @$val->form_privileges[3]->privilege_value_user; 
                                                $deptvalue = @$val->form_privileges[3]->privilege_value_department;
                                                $key_user_value = explode(',',$uservalue);
                                                $key_dept_value = explode(',',$deptvalue); 
                                                $depart         = explode(',',$dept_id); 
                                                $intersection   = array_intersect($depart,$key_dept_value);                                     
                                                if(in_array($user_id, $key_user_value) || count($intersection)>0) { ?>
                                                    <a id="viewform" class="viewform" form_id='<?php echo e($val->form_id); ?>' style="cursor: pointer;" data-toggle="modal" data-target="#view_form" title="Submit this Form">
                                                        <i class="fa fa-newspaper-o" style="cursor:pointer;"></i>
                                                    </a>
                                                <?php }                                            
                                            }
                                        } 
                                    ?>
                                    &nbsp;
                                     <?php if(Auth::user()->user_role == 1): ?> 
                                     <a title="permission" id="details" href="<?php echo e(URL::route('form_permission',$val->form_id)); ?>">
                                    <i class="fa  fa-wrench" style="cursor:pointer;"></i>
                                </a> 
                                
                                <?php endif; ?>
                                    
                                <!-- super admin can only edit,delete forms -->
                                <?php if(stristr($form_permission,"edit")): ?>
                                    &nbsp;
                                    <a title="Edit this Form" id="document-type-edit" href="<?php echo e(URL::route('form',$val->form_id)); ?>">
                                        <i class="fa fa-pencil" style="cursor:pointer;"></i>
                                    </a> 
                                <?php endif; ?>
                                <?php if(stristr($form_permission,"delete")): ?>
                                    &nbsp;
                                    <i class="fa fa-trash" onclick="del(<?php echo e($val->form_id); ?>, '<?php echo e($val->form_name); ?>')" title="Delete this Form" style="color: red; cursor:pointer;"></i>                           
                                <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>                    
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
        <?php else: ?>
            <section class="content content-sty">        
                <div class="alert alert-sty alert-error"><?php echo e(trans('language.dont_hav_permission')); ?></div>        
            </section>
        <?php endif; ?>
    <?php elseif(Session::get('enbva4')==Session::get('fval')): ?>
        <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty"><?php echo e(trans('language.purchase_now')); ?></div></section>
    <?php else: ?>
        <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty"><?php echo e(trans('language.module_expired')); ?></div></section>
    <?php endif; ?>
</div>
</section>
<div class="modal fade" id="view_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div id="content_form"></div>
    </div>
</div>

<?php echo Html::script('plugins/datatables_new/jquery.dataTables.min.js'); ?>

<?php echo Html::script('plugins/datatables_new/dataTbles.fixedHeader.min.js'); ?>

<script type="text/javascript">
$('#spl-wrn').delay(5000).slideUp('slow');
//$('#spl_warn').delay(8000).slideUp('slow');
$(document).ready(function() {
    var rows_per_page = '<?php echo e(Session('settings_rows_per_page')); ?>';
    var lengthMenu = getLengthMenu();//Function in app.blade.php
    var table = $('#documentTypeDT').DataTable({  
            "lengthMenu": lengthMenu,
            "pageLength":rows_per_page,            
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "fixedHeader": true,
            bJQueryUI: true,
            "autoWidth": false,
            "order": [[ 3, "desc" ]],
            language: {
                searchPlaceholder: "<?php echo e(trans('language.data_tbl_search_placeholder')); ?>"
            },
            columnDefs: [ { orderable: false, targets: [4] } ]
        });
        //for highlight the row
    $('#documentTypeDT tbody').on('click', 'tr', function () {
        $("#documentTypeDT tbody tr").removeClass('row_selected');        
        $(this).addClass('row_selected');
    });
        $('#form_column').on('keyup', function(){
            table
            .column(0)
            .search(this.value)
            .draw();
        });
} );
// view form
    $('.viewform').click(function(){
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var formID = $(this).attr('form_id');
        $.ajax({
            type: 'post',
            url: '<?php echo e(URL('viewform')); ?>',
            data: {_token: CSRF_TOKEN,formid:formID},
            timeout: 50000,
            beforeSend: function() {
              $("#bs").show();
            },
            success: function(data)
            {
                $("#content_form").html(data);
            }
        });

    });
//form delete
    function del(formID,name)
    {   
    swal({
        title: "<?php echo e(trans('language.confirm_delete_single')); ?>'" + name + "' ?",
        text: "<?php echo e(trans('language.Swal_not_revert')); ?>",
        type: "<?php echo e(trans('language.Swal_warning')); ?>",
        showCancelButton: true
      }).then(function (result) {
          if(result){
              var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
              $.ajax({
                  type: 'post',
                  url: '<?php echo e(URL('deleteform')); ?>',
                  data: {_token: CSRF_TOKEN,formid:formID,form_name:name},
                  beforeSend: function() {
                      $("#bs").show();
                  },
                  success: function(data, status){ 
                    swal({
                      title: data,
                      showCancelButton: false
                    }).then(function (result) {
                        window.location.reload();
                  });
                  },
                  complete: function(data) {
                      $("#bs").hide();
                  }
              });

        }
    });
  }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\nspl5.8\resources\views/pages/forms/list.blade.php ENDPATH**/ ?>