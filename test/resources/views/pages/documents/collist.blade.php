{!! Html::script('plugins/daterangepicker/daterangepicker.js') !!}
<?php
$i = 1;
foreach ($documentTypeData as $key => $dType) {
    ?>
    
        <div class="col-sm-6">
        {!! Form::label( $dType->document_type_column_name, '', array('class'=> 'control-label'))!!}
        
            <?php $doccoltype = $dType->document_type_column_type; ?>
            <input type="hidden" name="doclabl<?php echo $i; ?>" value="{{ $dType->document_type_column_name }}">
            <input type="hidden" name="count_columns" value="<?php echo $i; ?>">
            <input type="text" name="doccol<?php echo $i; ?>" class="form-control" <?php if($doccoltype=="Integer"){ ?> data-parsley-type="integer" <?php }else if($doccoltype=="Date"){ ?> id="exp_date" placeholder="YYYY-MM-DD" <?php } ?> >   
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
        singleDatePicker: true,
        "drops": "up",
        maxDate: moment(date),
        showDropdowns: true
    });
  });
</script>