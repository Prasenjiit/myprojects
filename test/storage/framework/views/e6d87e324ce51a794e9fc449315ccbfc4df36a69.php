<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?>

<?php $__env->startSection('main_content'); ?>
<?php echo Html::style('plugins/datatables_new/fixedHeader.dataTables.min.css'); ?>

<?php echo Html::style('plugins/datatables_new/jquery.dataTables.min.css'); ?>



<?php
    $user_id = Auth::user()->id;
    $dept_id = Auth::user()->department_id;
    $user_role = Auth::user()->user_role;
    $form_permission = Auth::user()->user_form_permission;
?>

<section class="content-header">
  <div class="col-sm-8">
    <span >
      <strong><?php echo e(trans('forms.forms')); ?></strong> &nbsp;
    </span>
   <?php if(Session::get('enbval4')==Session::get('tval')): ?>   
    <!-- super admin can only add forms -->
        <span>
            <a href="<?php echo e(URL::route('forms')); ?>">
                <button class="btn btn-info btn-flat newbtn" style="line-height:13px !important;"><?php echo e(trans('language.back')); ?></button>
            </a>
        </span>
        <?php if(stristr($form_permission,"add")): ?>
        <span >
          <a href="<?php echo e(URL::route('form')); ?>">
              <button class="btn  btn-info btn-flat newbtn"><?php echo e(trans('language.add new')); ?>  <i class="fa fa-plus"></i></button>
          </a>
        </span>
        <?php endif; ?>    
        <span >
            <a id="viewform" class="viewform" form_id='<?php echo e($form_id); ?>' data-toggle="modal" data-target="#view_form" title="Submit Form">
            <button class="btn  btn-info btn-flat newbtn">Submit This Form <i class="fa fa-newspaper-o"></i></button>
            </a>
        </span>
    <?php endif; ?>
  </div>
  <div class="col-sm-4">
    <!-- <ol class="breadcrumb">
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
  <!--Checking module permission-->
<?php if(Session::get('enbval4')==Session::get('tval')): ?>   
<?php if(stristr($form_permission,"view")): ?>
<div class="col-xs-12">
<div class="box box-info">
            
            <div class="box-body" >
                <table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable hover">
                    <thead>
                        <tr>
                         <th>Submitted By</th>                        
                                    <th>Submitted Date</th> 
                                    <th >Workflow</th>
                                    <th class="no-sort">Stage</th>
                                    <th class="no-sort">Status</th>   
                                    <th class="no-sort"><?php echo e(trans('language.actions')); ?></th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        
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
<?php elseif(Session::get('enbval4')==Session::get('fval')): ?>
    <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty"><?php echo e(trans('language.purchase_now')); ?></div></section>
<?php else: ?>
    <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty"><?php echo e(trans('language.module_expired')); ?></div></section>
<?php endif; ?>
</div>
</section>
    <div class="modal fade" id="viewmoreModal" data-backdrop="true" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div id="more"></div>
    </div>



      <div class="modal fade" id="form_response_modal">
         <div class="modal-dialog">
            <div class="modal-content form_response_remote">
               
            <div class="modal-footer"></div>
            </div>
            <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->


      <div class="modal fade" id="modal-default">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Default Modal</h4>
              </div>
              <div class="modal-body">
                <p>One fine body&hellip;</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
<?php echo Html::script('plugins/datatables_new/jquery.dataTables.min.js'); ?>

<?php echo Html::script('plugins/datatables_new/dataTables.fixedHeader.min.js'); ?>

<?php echo Html::script('plugins/simple-jquery-form-builder/js/sjfb-html-generator.js'); ?>

<?php echo Html::script('plugins/simple-jquery-form-builder/js/sjfb-html-generator-edit.js'); ?>

<script type="text/javascript">
$(document).ready(function() {    
// ajax data table
var selected = [];
var filter =   function()
{   
    var obj = $('#filter').val();
    return obj;
};

var rows_per_page = '<?php echo e(Session('settings_rows_per_page')); ?>';
var lengthMenu = getLengthMenu();//Function in app.blade.php
var datatable =   $('#documentTypeDT').DataTable({ 
              "fnInitComplete": function() {
        $('#documentTypeDT tbody tr').each(function(){
                // $(this).find('td:eq(2)').attr('nowrap', 'nowrap');
                // $(this).find('td:eq(5)').attr('nowrap', 'nowrap');
        });
    },
                            "lengthMenu": lengthMenu,
                            "pageLength":rows_per_page,       
                            "processing": true, 
                            "serverSide": true,
                            "fixedHeader": true,
                            "order": [[1,"desc"]],
                            "ajax": {
                               "url": "<?php echo url('single_forms_filter_ajax'); ?>",
                               "type": "POST",
                                "headers": { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                               "data":function(d){
                                        //d.typeselect = typeselect();
                                        d.filter = filter();
                                        d.form_id = "<?php echo $form_id; ?>";
                                     }
                                },
                            "iDisplayLength": 25,
                            "columns": [
                                        {
                                            "class":"0",
                                            "data":"sender_user",
                                        },
                                        {
                                            "class":"1",
                                            "data":"created",
                                        },
                                        {
                                            "class":"2",
                                            "data":"wf",
                                        },
                                        {
                                            "class":"3",
                                            "data":"state",
                                        },
                                        {
                                            "class":"4",
                                            "data":"status",
                                        },
                                        {
                                            "class":"6",
                                            "data":"actions",
                                        }
                                    ],
                              "columnDefs": [
                                  { targets: 'no-sort', orderable: false }
                                ],          
                             "language": { processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> ',"searchPlaceholder": "<?php echo e(trans('language.data_tbl_search_placeholder')); ?>"  },                       
                           
    } );
//for highlight the row
    $('#documentTypeDT tbody').on('click', 'tr', function () {
        $("#documentTypeDT tbody tr").removeClass('row_selected');        
        $(this).addClass('row_selected');
    });
$('#filter').change(function() {
    var filter= this.value;
    var selected_type=$("#typeselect").val(); 
    datatable.ajax.reload();
 });
$('#form_column_name').on('keyup', function(){
    datatable
    .column(0)
    .search(this.value)
    .draw();
});
$('#form_column_from').on('keyup', function(){
    datatable
    .column(1)
    .search(this.value)
    .draw();
});
$('#form_state').on('keyup', function(){
    datatable
    .column(4)
    .search(this.value)
    .draw();
});
$('#form_wf').on('keyup', function(){
    datatable
    .column(3)
    .search(this.value)
    .draw();
});

$('#spl-wrn').delay(5000).slideUp('slow');


$(document).on("click",".delete_single",function(e)
{
   var docname = $(this).attr('docname');
   var form_id = $(this).attr('form_id');
   var unique_id = $(this).attr('form_response_unique_id');

   console.log("docname"+docname);
   console.log("form_id"+form_id);
   console.log("unique_id"+unique_id);

   swal({
          title: "<?php echo e(trans('language.confirm_delete_single')); ?>'" + docname + "' ?",
          text: "<?php echo e(trans('language.Swal_not_revert')); ?>",
          type: "<?php echo e(trans('language.Swal_warning')); ?>",
          showCancelButton: true,
        }).then(function (result) {
        if(result)
        {
            // Success
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var form_url = "<?php echo e(url('deleteSingleSubmittedform')); ?>";
            var postdata ={_token: CSRF_TOKEN,form_id:form_id,form_name:docname,form_response_unique_id:unique_id}
             $.ajax({
                method: "POST",
                url: form_url,
                data: postdata,
                dataType: 'json',
                success: function (msg) 
                {
                    datatable.ajax.reload();
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);    
                    console.log(textStatus);    
                    console.log(errorThrown);    
                }
            });

        }   
    });
});

// view form more details
    var loading_src = "<?php echo e(URL::asset('images/loading/loading.gif')); ?>";
    var loading_text ='<div  style="text-align: center;"><img src="'+loading_src+'"></div>';
    var show_FORM_details = function(response) 
    {
        $('.form_response_remote').html(loading_text);
        $('#form_response_modal').modal({
            show: 'show',
            backdrop: false
        }); 
        var formID = response.formid;
        var form_response_unique_id = response.form_response_unique_id;

        $.ajax({
            type: 'get',
            url: "<?php echo e(URL('formMoreDetails')); ?>",
            dataType: 'json',
            data: {formid:formID,form_response_unique_id:form_response_unique_id},
            timeout: 50000,
            success: function(data)
            {
                $('.form_response_remote').html(data.html);
            }
        });
    };

    <?php
    if(isset($response) && $response)
    {
    ?>    
        var obj = {formid: "<?php echo $form_id; ?>",form_response_unique_id: "<?php echo $response; ?>" };
        show_FORM_details(obj);
    <?php
    }
    ?>

$(document).on("click",".view_form_response_details",function(e) {
        e.preventDefault();
        var obj = {formid: $(this).attr('form_id'),form_response_unique_id: $(this).attr('form_response_unique_id')};
        show_FORM_details(obj);
    });


$(document).on("click",".viewform",function(e) {
        e.preventDefault();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var formID = $(this).attr('form_id');

        $('.form_response_remote').html(loading_text);
        $('#form_response_modal').modal({
            show: 'show',
            backdrop: false
        }); 
        
        $.ajax({
            type: 'POST',
            url: '<?php echo e(URL('viewform')); ?>',
            data: {_token: CSRF_TOKEN,formid:formID},
            timeout: 50000,
            success: function(data)
            {
                console.log(data);
                $('.form_response_remote').html(data);
            }
        });
    });




 }); 



</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\nspl5.8\resources\views/pages/forms/details.blade.php ENDPATH**/ ?>