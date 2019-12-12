<?php
echo Html::script('plugins/daterangepicker/daterangepicker.js') ;
$i = 1;
foreach ($stackTypeData as $key => $dType) { 
        $arr=explode(",",$dType->stack_options);
        $wcr=array_filter($arr);
        ?>
    <div class="form-group">
        <?php if($dType->stack_column_mandatory==1){ ?>
            <label for="DocTypes" class="col-sm-12 control-label"><?php echo $dType->stack_column_name.':'; ?><span class="compulsary"> *</span> </label>
        <?php }else{ ?>
            <label for="DocTypes" class="col-sm-12 control-label"><?php echo $dType->stack_column_name.':'; ?> </label>
        <?php } ?>
            <?php $doccoltype = $dType->stack_column_type; ?>
            
            <input type="hidden" name="stckcolid<?php echo $i; ?>" value="<?php echo $dType->stack_column_id; ?>">
            <input type="hidden" name="stckcolname<?php echo $i;?>" value="<?php echo $dType->stack_column_name; ?>">
            <input type="hidden" name="stckcoltype<?php echo $i;?>" value="<?php echo $dType->stack_column_type; ?>">
            <input type="hidden" name="stckcolmandatory<?php echo $i; ?>" value="<?php echo $dType->stack_column_mandatory; ?>">
            <div class="col-sm-12">
                <?php if($doccoltype=="Yes/No"){?>
                    <p><input name='stckcol<?php echo $i; ?>' id='newsletter' type='radio' value='yes' <?php if($dType->stack_column_mandatory==1){ ?> required="true" <?php } ?> > Yes 
                    <input name='stckcol<?php echo $i; ?>' id='newsletter' type='radio' value='no'> No
                    </p> 
                <?php }
            else if($doccoltype=="Piclist"){?>
            <select id="List" name='stckcol<?php echo $i; ?>' class="form-control" <?php if($dType->stack_column_mandatory==1){ ?> required="true" <?php } ?> >
            <?php
            foreach($wcr as $key => $value):
            echo '<option value="'.$value.'">'.$value.'</option>';
            endforeach;
            ?>
            </select>
            <?php }
            else{ ?>
            <input type="text" title="Must be less than 30 characters" data-parsley-maxlength = "30" name="stckcol<?php echo $i; ?>" value="" class="form-control" <?php if($dType->stack_column_mandatory==1){ ?> required="true" <?php } ?> <?php if($doccoltype=="Number"){ ?> data-parsley-type="number" <?php }else if($doccoltype=="Date"){ ?> id="datefield" placeholder="YYYY-MM-DD" <?php } ?> 
            >
            <?php } ?>

            </div>
    </div>        
    <?php
    $i++;
    }
?>
<input type="hidden" name="stackcolcnt" value="<?php echo $i-1; ?>">
<script type="text/javascript">
$(function ($) {

        var d           = new Date();
        var currentYear = d.getFullYear();
        var newDate     = currentYear+10;
        var date        = '12/31/'+newDate;

    $('#exp_date').daterangepicker({
        maxDate: moment(date),
        singleDatePicker: true,
        "drops": "up",
        showDropdowns: true
    });
    $('#datefield').daterangepicker({
        maxDate: moment(date),
        singleDatePicker: true,
        "drops": "up",
        showDropdowns: true
    });
});
</script>