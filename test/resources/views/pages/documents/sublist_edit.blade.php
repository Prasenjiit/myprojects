{!! Html::script('plugins/daterangepicker/daterangepicker.js') !!}
<?php
$i = 1;
$show = 1;
foreach ($fetch_doc_col as $key => $dCol) {
        $arr=explode(",",$dCol->document_type_options);
        $wcr=array_filter($arr);
    if($dCol->document_type_column_name=="Level 2"){
        if((Auth::user()->user_role != 1)&&(Auth::user()->user_role != 2)){
            $show = 0;
        }
    }else if($dCol->document_type_column_name=="Level 3"){
        if(Auth::user()->user_role != 1){ //check if the user is not a super admin 
            $show = 0;
        }
    }
    if($show==1){ ?>
    <div class="form-group">
        <?php if($dCol->document_type_column_mandatory==1){ ?>
        <label for="DocTypes" class="col-sm-12 control-label"><?php echo $dCol->document_column_name.': '; ?><span class="compulsary"> *</span> </label>
        <?php }else{ ?>
        <label for="DocTypes" class="col-sm-12 control-label"><?php echo $dCol->document_column_name.': '; ?></label>
        <?php } ?>
        <?php $doccoltype = $dCol->document_type_column_type; ?>
     
        <div class="col-sm-12">
        
        <?php if($doccoltype=="Piclist"){?>
        <p>
        <select id="List" name='doccol<?php echo $i; ?>' class="form-control" <?php if($dCol->document_type_column_mandatory==1){ ?> required="true" <?php } ?> >
        <?php 
        foreach ($wcr as $key => $value) {
        ?>
        <option value="<?php echo $value; ?>"<?php if($value==$dCol->document_column_value){ echo "selected";}?>><?php echo $value;?>
        </option>
        <?php }
        ?>
        </select>
        </p>     
        <?php }
        else if($doccoltype=='Yes/No'){
        ?>
            <p>
                <input name='doccol<?php echo $i; ?>' id='newsletter' type='radio' value='yes' <?php if($dCol->document_type_column_mandatory==1){ ?> required="true" <?php } ?> <?php if($dCol->document_column_value=='yes'){ ?> checked="checked" <?php } ?> > Yes 
                <input name='doccol<?php echo $i; ?>' id='newsletter' type='radio' value='no' <?php if($dCol->document_column_value=='no'){ ?> checked="checked" <?php } ?> > No
            </p>
        <?php 
            }else{ 
                if($doccoltype=="Date")
                    {
                        $document_column_value = custom_date_Format($dCol->document_column_value);    
                    }
                    else
                    {
                            $document_column_value = $dCol->document_column_value;  
                    }
                    ?>
           <input type="text" title="Must be less than 30 characters" data-parsley-maxlength = "30" value="{{$document_column_value}}" name="doccol<?php echo $i; ?>" class="form-control <?php if($doccoltype=="Date") echo 'exp_date';?>" <?php if($dCol->document_type_column_mandatory==1){ ?> required="true" <?php } ?> <?php if($doccoltype=="Number"){ ?> data-parsley-type="number" <?php }else if($doccoltype=="Date"){ ?>  placeholder="{{ placeholder_date_format() }}" <?php } ?> >
        <?php } ?>
        </div>
    </div>  
    <?php
} ?>
   <input type="hidden" name="docid<?php echo $i; ?>" value="{{ $dCol->document_type_column_id }}">
        <input type="hidden" name="doclabl<?php echo $i; ?>" value="{{ $dCol->document_column_name }}">
        <input type="hidden" name="doctype<?php echo $i;?>" value="{{$dCol->document_type_column_type}}">
        <input type="hidden" name="docmandatory<?php echo $i;?>" value="{{$dCol->document_column_mandatory}}">
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
        singleDatePicker: true,
        format: '{{ js_date_format() }}',
        "drops": "up",
        showDropdowns: true
    });
  });
</script>