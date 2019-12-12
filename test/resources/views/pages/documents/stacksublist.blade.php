{!! Html::script('plugins/daterangepicker/daterangepicker.js') !!}
<?php
$i = 1;
foreach ($stackData as $key => $dType) { 
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
            <input type="hidden" name="docid<?php echo $i; ?>" value="{{ $dType->stack_column_id }}">
            <input type="hidden" name="doctype<?php echo $i;?>" value="{{$dType->stack_column_type}}">
            <input type="hidden" name="docmandatory<?php echo $i;?>" value="{{$dType->stack_column_mandatory}}">
            <input type="hidden" name="doclabl<?php echo $i; ?>" value="{{ $dType->stack_column_name }}">
            <div class="col-sm-12">
                <?php if($doccoltype=="Yes/No"){?>
                    <p><input name='doccol<?php echo $i; ?>' id='newsletter' type='radio' value='yes' <?php if($dType->stack_column_mandatory==1){ ?> required="true" <?php } ?> > Yes 
                    <input name='doccol<?php echo $i; ?>' id='newsletter' type='radio' value='no'> No
                    </p> 
                <?php }
            else if($doccoltype=="Piclist"){?>
            <select id="List" name='doccol<?php echo $i; ?>' class="form-control" <?php if($dType->stack_column_mandatory==1){ ?> required="true" <?php } ?> >
            <?php
            foreach($wcr as $key => $value):
            echo '<option value="'.$value.'">'.$value.'</option>';
            endforeach;
            ?>
            </select>
            <?php }
            else{ ?>
            <input type="text" title="Must be less than 30 characters" data-parsley-maxlength = "30" name="doccol<?php echo $i; ?>" value="" class="form-control" <?php if($dType->stack_column_mandatory==1){ ?> required="true" <?php } ?> <?php if($doccoltype=="Number"){ ?> data-parsley-type="number" <?php }else if($doccoltype=="Date"){ ?> id="exp_date" placeholder="YYYY-MM-DD" <?php } ?> 
            >
            <?php } ?>
            </div>
    </div>        
    <?php
    $i++;
    }
?>
<input type="hidden" name="coltypecnt" value="<?php echo $i-1; ?>">
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
});
</script>