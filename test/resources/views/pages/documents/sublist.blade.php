{!! Html::script('plugins/daterangepicker/daterangepicker.js') !!}
<?php
$i = 1;
$show = 1;
foreach ($documentTypeData as $key => $dType) { 
    $arr=explode(",",$dType->document_type_options);
    $wcr=array_filter($arr);
    
    if($dType->document_type_column_name=="Level 2"){
        if((Auth::user()->user_role != 1)&&(Auth::user()->user_role != 2)){
            $show = 0;
        }
    }else if($dType->document_type_column_name=="Level 3"){
        if(Auth::user()->user_role != 1){ //check if the user is not a super admin 
            $show = 0;
        }
    }
    if($show==1){ ?> 
    <div class="form-group">
        <label for="DocTypes" class="col-sm-12 control-label"><?php echo $dType->document_type_column_name.':'; if($dType->document_type_column_mandatory==1){ ?><span class="compulsary"> *</span><?php } ?> </label>
        <?php $doccoltype = $dType->document_type_column_type; ?>
       
        <div class="col-sm-12">
            <?php if($doccoltype=="Yes/No"){?>
                <p><input name='doccol<?php echo $i; ?>' id='newsletter' type='radio' value='yes' <?php if($dType->document_type_column_mandatory==1){ ?> required="true" <?php } ?> > Yes 
                <input name='doccol<?php echo $i; ?>' id='newsletter' type='radio' value='no'> No
                </p> 
            <?php }
        else if($doccoltype=="Piclist"){?>
        <select id="List" name='doccol<?php echo $i; ?>' class="form-control" <?php if($dType->document_type_column_mandatory==1){ ?> required="true" <?php } ?> >
        <?php
        foreach($wcr as $key => $value):
        '<option value="'.$value.'">'.$value.'</option>';
        endforeach;
        ?>
        </select>
        <?php }
        else{ ?>
        <input type="text" title="Must be less than 30 characters" data-parsley-maxlength = "30" name="doccol<?php echo $i; ?>" value="" class="form-control <?php if($doccoltype=="Date") echo 'exp_date';?>" <?php if($dType->document_type_column_mandatory==1){ ?> required="true" <?php } ?> <?php if($doccoltype=="Number"){ ?> data-parsley-type="number" <?php }else if($doccoltype=="Date"){ ?> id="exp_date" placeholder="{{ placeholder_date_format() }}" <?php } ?> 
        >
        <?php } ?>
        </div>
    </div>        
    <?php
    } ?>
     <input type="hidden" name="docid<?php echo $i; ?>" value="{{ $dType->document_type_column_id }}">
        <input type="hidden" name="doctype<?php echo $i;?>" value="{{$dType->document_type_column_type}}">
        <input type="hidden" name="docmandatory<?php echo $i;?>" value="{{$dType->document_type_column_mandatory}}">
        <input type="hidden" name="doclabl<?php echo $i; ?>" value="{{ $dType->document_type_column_name }}">
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

     $('.exp_date').daterangepicker({
        /*maxDate: moment(date),*/
        format: '{{ js_date_format() }}',
        singleDatePicker: true,
        "drops": "up",
        showDropdowns: true
    });
});
</script>