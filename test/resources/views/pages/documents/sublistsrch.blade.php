{!! Html::script('plugins/daterangepicker/daterangepicker.js') !!}
<?php
$i = 1;
$cal = 0;
foreach ($documentTypeData as $key => $dType) { 
        $arr=explode(",",$dType->document_type_options);
        $wcr=array_filter($arr);
        ?>
   
        <div class="col-sm-12">
            <label for="DocTypes" class="control-label">
                <?php echo $dType->document_type_column_name.':'; ?> 
            </label>
            <?php $doccoltype = $dType->document_type_column_type; ?>
            <input type="hidden" name="docid<?php echo $i; ?>" value="{{ $dType->document_type_column_id }}">
            <input type="hidden" name="doctype<?php echo $i;?>" value="{{$dType->document_type_column_type}}">
            <input type="hidden" name="docmandatory<?php echo $i;?>" value="{{$dType->document_type_column_mandatory}}">
            <input type="hidden" name="doclabl<?php echo $i; ?>" value="{{ $dType->document_type_column_name }}">
            <div class="fstControls">
                <?php if($doccoltype=="Yes/No"){?>
                    <p><input name='doccol<?php echo $i; ?>' id='newsletter' type='radio' value='yes'> Yes &nbsp;&nbsp;&nbsp;
                    <input name='doccol<?php echo $i; ?>' id='newsletter' type='radio' value='no'> No
                    </p> 
                <?php }
                else if($doccoltype=="Piclist"){?>
                    <select id="List" name='doccol<?php echo $i; ?>' class="form-control">
                        <?php
                        foreach($wcr as $key => $value):
                            echo '<option value="'.$value.'">'.$value.'</option>';
                        endforeach;
                        ?>
                    </select>
                <?php }
                else{ ?>
                    <input type="text" name="doccol<?php echo $i; ?>" class="form-control" <?php if($doccoltype=="number"){ ?> data-parsley-type="number" <?php }else if($doccoltype=="Date"){ $cal++;?> id="exp_date<?php echo $cal; ?>" placeholder="{{ placeholder_date_format() }}" <?php  } ?>>
                <?php } ?>
            </div>
        </div>
      
    <?php
    $i++;
    }
?>
<input type="hidden" id="calval" value="<?php if($cal=="undefined"){ echo 0; }else{ echo $cal; }?>">
<input type="hidden" name="coltypecnt" value="<?php echo $i-1; ?>">
<script type="text/javascript">
$(function ($) {
        var d           = new Date();
        var currentYear = d.getFullYear();
        var newDate     = currentYear+10;
        var date        = '12/31/'+newDate;
        
	var cnt = $("#calval").val();
    for (var m=1;m<=cnt;m++){
        $('#exp_date'+m).daterangepicker({
            format: '{{ js_date_format() }}',
            singleDatePicker: true,
            "drops": "up"
        });
    }
    
});
</script>