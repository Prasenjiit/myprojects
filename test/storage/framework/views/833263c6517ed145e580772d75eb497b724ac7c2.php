<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
<?php echo Html::style('css/dropzone.min.css'); ?>

<style type="text/css">
.description
{
  color: #777;
  font-size: 15px;
  display: inline-block;
  padding-left: 4px;
  font-weight: 300;
}
#sjfb-sample .required-field > label:after {
  content: " *";
  color: red;
  font-size: 90%;
}
.assign{
  padding-top: 10px;
  font-size: 15px;
  font-weight: 500;
}
#wrong{
  color: red;
  margin-top: -5px;
}
label {
  text-transform: capitalize;
}
</style>

<div class="modal-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">×</span></button>
    <h4 class="modal-title"><?php echo e(ucfirst(@$form_name)); ?> <span class="description"> - <?php echo e(ucfirst(@$form_description)); ?></span></h4>
  </div>
  <form id="sjfb-sample" method="POST" action="<?php echo e(url('saveFormValuesAdd')); ?>" enctype="multipart/form-data" data-parsley-validate=''>
  <div class="modal-body" style="max-height: 500px !important; overflow-y: auto;">
    <div id="sjfb-wrap">
        
            <?php echo csrf_field(); ?>

            <input type="hidden" name="action" id="form_id" value="<?php echo e($action); ?>">
            <input type="hidden" name="form_id" id="form_id" value="<?php echo e($form_id); ?>">
            <input type="hidden" name="form_name" id="form_name" value="<?php echo e($form_name); ?>">
            <input type="hidden" name="form_description" id="form_description" value="<?php echo e($form_description); ?>">
            <input type="hidden" name="loadformurl" id="loadformurl" value="<?php echo e(url('load_form')); ?>">
            <div id="sjfb-fields">
            </div>
         <!-- <?php echo "<pre>"; print_r($inputs); echo "</pre>"; ?>  -->
        <div id='div-append'>
        </div>

    </div>

        <div class="row">
          <?php foreach($inputs as $key => $value)
          { 
          $column = (isset($value['column']) && $value['column'])?$value['column']:'col-xs-12 col-sm-12 col-md-12';  
          $is_input_type =  (isset($value['is_input_type']))?$value['is_input_type']:0;
          $input_label =  (isset($value['input_label']))?$value['input_label']:'';  
          $placeholder =  (isset($value['input_label']))?$value['input_label']:''; 
          $input_type =  (isset($value['input_type']))?$value['input_type']:''; 
          $is_required =  (isset($value['is_required']))?$value['is_required']:0; 
          $input_id =  (isset($value['input_id']))?(string)$value['input_id']:''; 
          $is_options =  (isset($value['is_options']))?$value['is_options']:''; 
          $multiple_files =  (isset($value['multiple_files']))?$value['multiple_files']:0; 
          
          $input_choices = (isset($value['input_choices']))?$value['input_choices']:array();
          $input_value = (isset($value['input_values']))?$value['input_values']:''; 
          $form_edit_permision =  (isset($value['form_edit_permision']))?$value['form_edit_permision']:0; 
          $form_view_permission =  (isset($value['form_view_permission']))?$value['form_view_permission']:0; 
          $iptype = 'text';
          if($input_type == 'number')
          {
            $iptype = 'number';
          }
          else if($input_type == 'time')
          {
            $iptype = 'time';
            $placeholder ='';
          }
          ?>
          <?php if($form_edit_permision == '0' && $form_view_permission == '1'){ ?> 
          
          <div class="<?php echo e($column); ?>">
            <?php if($is_input_type == '1'){ ?> 
             <div class="form-group" id="parsley_error_<?php echo e($input_id); ?>">
            <label for="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>" id="filelabel-<?php echo e($input_id); ?>"><?php echo e($input_label); ?> :</label>
          </div>
            <?php } ?>
            <?php if($input_type == 'heading'){ ?> 
            <div class="form-group" id="parsley_error_<?php echo e($input_id); ?>">
              <h3 class="text-center"><?php echo e($input_label); ?></h3>
            </div>
            <?php } ?>

             <?php if($input_type == 'subheading'){ ?> 
            <div class="form-group" id="parsley_error_<?php echo e($input_id); ?>">
              <h4 class="text-center"><?php echo e($input_label); ?></h4>
            </div>
            <?php } ?>
          </div>

          <?php } else if($form_edit_permision == '1'){ ?>
          <div class="<?php echo e($column); ?>">
            <!-- SECTION START -->
            <?php if(($input_type == 'text' || $input_type == 'email' || $input_type == 'number' || $input_type == 'time') && ($form_edit_permision)){ ?>
            <div class="form-group">
              <label for="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>" id="filelabel-<?php echo e($input_id); ?>"><?php echo e($input_label); ?> :</label>
              <input type="<?php echo e($iptype); ?>" name="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>" id="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>" class="form-control" <?php if($is_required){ ?> data-parsley-required="true" data-parsley-required-message="<?php echo e($input_label); ?> <?php echo e(Lang::get('language.is_required')); ?>" <?php } ?>  placeholder="<?php echo e($placeholder); ?>" value="<?php echo e($input_value); ?>">
            </div>
            <?php } ?> 
            <!-- SECTION END -->

            <!-- SECTION START -->
            <?php if(($input_type == 'date') && ($form_edit_permision)){ ?>
            <div class="form-group" id="parsley_error_<?php echo e($input_id); ?>">
              <label for="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>" id="filelabel-<?php echo e($input_id); ?>"><?php echo e($input_label); ?> :</label>
              
              <div class="input-group" >
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="<?php echo e($iptype); ?>" name="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>" id="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>" class="form-control datetime" <?php if($is_required){ ?> data-parsley-required="true" data-parsley-required-message="<?php echo e($input_label); ?> <?php echo e(Lang::get('language.is_required')); ?>" <?php } ?>  placeholder="<?php echo e(placeholder_date_format()); ?>" data-parsley-errors-container="#parsley_error_<?php echo e($input_id); ?>" value="<?php echo e($input_value); ?>">
              </div>
            </div>
            <?php } ?> 
            <!-- SECTION END -->

             <!-- SECTION START -->
            <?php if(($input_type == 'textarea') && ($form_edit_permision)){ ?>
            <div class="form-group" >
              <label for="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>" id="filelabel-<?php echo e($input_id); ?>"><?php echo e($input_label); ?> :</label>
                <textarea name="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>" id="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>" class="form-control" <?php if($is_required){ ?> data-parsley-required="true" data-parsley-required-message="<?php echo e($input_label); ?> <?php echo e(Lang::get('language.is_required')); ?>" <?php } ?>  placeholder="<?php echo e($placeholder); ?>" ><?php echo e($input_value); ?></textarea>
            </div>
            <?php } ?> 
            <!-- SECTION END -->

            <!-- SECTION START -->
            <?php if(($input_type == 'file') && ($form_edit_permision)){ ?>
            <div class="form-group" id="parsley_error_<?php echo e($input_id); ?>">
              <label for="exampleInputEmail1"><?php echo e($input_label); ?> :</label>
              <input type="hidden" name="hidden-<?php echo e($input_id); ?>"  id="hidden-<?php echo e($input_id); ?>"  <?php if($is_required){ ?> data-parsley-required="true" data-parsley-required-message="<?php echo e($input_label); ?> <?php echo e(Lang::get('language.is_required')); ?>" <?php } ?> data-parsley-errors-container="#parsley_error_<?php echo e($input_id); ?>" value="">
              <div class="dropzone" id="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>" data-id="<?php echo e($input_id); ?>" data-multiple="<?php echo e($multiple_files); ?>"></div>
              <div id="form_field-<?php echo e($input_id); ?>"></div>  
            </div>
            <?php } ?> 
            <!-- SECTION END -->


            <!-- SECTION START -->
            <?php if(($input_type == 'select') && ($form_edit_permision)){ ?>
            <div class="form-group" id="parsley_error_<?php echo e($input_id); ?>">
              <label for="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>" id="filelabel-<?php echo e($input_id); ?>"><?php echo e($input_label); ?> :</label>
              
              <select  name="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>[]" id="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>" class="form-control"  <?php if($is_required){ ?> data-parsley-required="true" data-parsley-required-message="<?php echo e($input_label); ?> <?php echo e(Lang::get('language.is_required')); ?>" <?php } ?> data-parsley-errors-container="#parsley_error_<?php echo e($input_id); ?>" >
              <?php 
              foreach($input_choices as $k => $v){ 
                $option_label = (isset($v['label']))?$v['label']:'';
                $option_select = (isset($v['sel']) && $v['sel'])?"selected":'';
              ?>
                <option value="<?php echo e($option_label); ?>" <?php echo e($option_select); ?>><?php echo e($option_label); ?></option>
              <?php } ?>  

              </select>
            </div>
            <?php } ?> 
            <!-- SECTION END -->

             <!-- SECTION START -->
            <?php if(($input_type == 'radio' || $input_type == 'checkbox') && ($form_edit_permision)){ ?>
            <div class="form-group" id="parsley_error_<?php echo e($input_id); ?>">
              <p for="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>" id="filelabel-<?php echo e($input_id); ?>"><?php echo e($input_label); ?> : </p>
              <?php 
              foreach($input_choices as $k => $v){ 
                $option_label = (isset($v['label']))?$v['label']:'';
                $option_select = (isset($v['sel']) && $v['sel'])?"checked":'';
              ?>
                <label class="<?php echo e($input_type); ?>-inline">
                  <input type="<?php echo e($input_type); ?>" name="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>[]" value="<?php echo e($option_label); ?>" <?php if($is_required){ ?> data-parsley-required="true" data-parsley-required-message="<?php echo e($input_label); ?> <?php echo e(Lang::get('language.is_required')); ?>" <?php } ?> data-parsley-errors-container="#parsley_error_<?php echo e($input_id); ?>" <?php echo e($option_select); ?>><?php echo e($option_label); ?></label>
              <?php } ?>  
            </div>
            <?php } ?> 
            <!-- SECTION END -->

            <!-- SECTION START -->
            <?php if(($input_type == 'agree') && ($form_edit_permision)){ ?>
            <div class="form-group" id="parsley_error_<?php echo e($input_id); ?>">
              
              <input type="checkbox" name="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>" value="1"  data-parsley-required="true" data-parsley-required-message="<?php echo e($input_label); ?> <?php echo e(Lang::get('language.is_required')); ?>"  data-parsley-errors-container="#parsley_error_<?php echo e($input_id); ?>" > <?php echo e($input_label); ?></label>
            </div>
            <?php } ?> 
            <!-- SECTION END -->

             

            <!-- SECTION START -->
            <?php if(($input_type == 'heading') && ($form_edit_permision)){ ?>
            <div class="form-group" id="parsley_error_<?php echo e($input_id); ?>">
              <h3 class="text-center"><?php echo e($input_label); ?></h3>
            </div>
            <?php } ?> 
            <!-- SECTION END -->

             <!-- SECTION START -->
            <?php if(($input_type == 'subheading') && ($form_edit_permision)){ ?>
            <div class="form-group" id="parsley_error_<?php echo e($input_id); ?>">
              <h3 class="text-center"><?php echo e($input_label); ?></h3>
            </div>
            <?php } ?> 
            <!-- SECTION END -->

             <!-- SECTION START -->
            <?php if(($input_type == 'label') && ($form_edit_permision)){ ?>
            <div class="form-group" id="parsley_error_<?php echo e($input_id); ?>">
              <label for="<?php echo e($input_type); ?>-<?php echo e($input_id); ?>" id="filelabel-<?php echo e($input_id); ?>"><?php echo e($input_label); ?></label>
            </div>
            <?php } ?> 
            <!-- SECTION END -->

            <!-- SECTION START -->
            <?php if(($input_type == 'section') && ($form_edit_permision)){ ?>
            <div class="form-group" id="parsley_error_<?php echo e($input_id); ?>">
              <hr style="border: 1px solid #d2d6de;"/>
            </div>
            <?php } ?> 
            <!-- SECTION END -->

          </div>
          
          <?php } ?> <!-- end permission -->

          <?php } ?>
        </div>
  </div>
  <div class="modal-footer">
  <div class="col-sm-8">
    <input type="submit" class="btn btn-primary" id="save_form" value="Submit">
      &nbsp;
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
  </div>
  <div class="preloader" style="float: left; margin-top: 5px; display: none;" >
      <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>
      <span class="sr-only">Loading...</span>
  </div>
  </div>
  </form>
</div>


<?php echo Html::script('js/parsley.min.js'); ?> 
<?php echo Html::script('js/dropzone.js'); ?>  
<script type="text/javascript">
$(document).ready(function(){
  window.ParsleyConfig = {
    excluded: 'input[type=button], input[type=submit], input[type=reset]',
    inputs: 'input, textarea, select, input[type=hidden], :hidden',
};


var max_file_size = "<?php echo e($language['max_upload_size']); ?>";
max_file_size = max_file_size.slice(0, -2); //remove MB from string
Dropzone.autoDiscover = false;
var attch_url = "<?php echo URL('formAttachments');?>";
var delete_attachmen = "<?php echo URL('formAttachments');?>";
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
              $('.dropzone').each(function() 
            {
                var dropzone_id = $(this).attr('id');
                var unique_id = $(this).attr('data-id');
                var multiple = $(this).attr('data-multiple');
                var required = $(this).attr('data-req');
                var file_label = $('#filelabel-'+unique_id).text();
                
                var myDropzone = new Dropzone("#"+this.id, {
                    type:'post',
                    params: {_token:CSRF_TOKEN,element_id:this.id,form_input_type_name:'File',unique:unique_id,label:file_label},
                    url: attch_url,
                    paramName: 'file',
                    addRemoveLinks: true,
                    maxFilesize: max_file_size,
                    
                    removedfile: function(file) {
                      
                    if(file.xhr){
                        var item_delete = file.xhr.response;
                        var item_json = $.parseJSON(item_delete); 
                        var element_delete = item_json.element_id;
                        var random_name = item_json.random_name;
                        $('.'+item_json.rand).remove();
                            $.ajax({
                                type: 'POST',
                                url: delete_attachmen,
                                data: {_token:CSRF_TOKEN,name:random_name},
                                dataType:'json',
                                sucess: function(data){
                                    //console.log('success: ' + data);
                                   }
                            });
                    }
                        var _ref;
                        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
             
                    },

                    success:function(file,response,data){
                      //console.log(response); 
                        //var json = $.parseJSON(response);
                        var json =response;
                        console.log(json); //
                        var name     = json.original_name;
                        var randName = json.random_name;
                        var elementId = json.element_id;
                        var rand = json.rand;
                        var element_unique_id = json.unique_id;
                        var element_label = json.label;
                        var size = json.size;
                        $('#hidden-'+element_unique_id).val(1);
                        /*var html = '<input class="'+rand+'" name="name'+element_unique_id+'[]" type="hidden"  value="'+name+'"><input class="'+rand+'" type="hidden" name="randname'+element_unique_id+'[]"  value="'+randName+'"><input class="'+rand+'" name="elementuniqueid'+element_unique_id+'[]" type="hidden"  value="'+element_unique_id+'"><input class="'+rand+'" name="elementlabel'+element_unique_id+'[]" type="hidden"  value="'+element_label+'"><input class="'+rand+'" name="size'+element_unique_id+'[]" type="hidden"  value="'+size+'">';*/

                        var html = '<input class="'+rand+'" name="attachment_name-'+elementId+'[]" type="hidden"  value="'+name+'">';

                        html += '<input class="'+rand+'" name="attachment_new_name-'+elementId+'[]" type="hidden"  value="'+randName+'">';

                        html += '<input class="'+rand+'" name="attachment_size-'+elementId+'[]" type="hidden"  value="'+size+'">';

                        $('#form_field-'+element_unique_id).append(html);
                    },

                }); 
                if(multiple != 1)
                {
                    myDropzone.options.maxFiles = 1;  
                }
                
            });

          $('.datetime').daterangepicker({
            singleDatePicker: true,
            format: '<?php echo e(js_date_format()); ?>',
            "drops": "bottom",
            showDropdowns: true
        });
            /*  $('#view_form').on('hidden.bs.modal', function () { 
    $('#content_form').html('');
    console.log('clear');
  });
var formID = $('#form_id').val();
var loadformurl = $('#loadformurl').val();
var action = 'add';
generateForm(formID,loadformurl,action);*/
             });
</script><?php /**PATH F:\xampp\htdocs\nspl5.8\resources\views/pages/forms/view_form.blade.php ENDPATH**/ ?>