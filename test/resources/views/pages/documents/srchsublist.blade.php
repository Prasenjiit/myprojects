{!! Html::script('plugins/daterangepicker/daterangepicker.js') !!}
<?php
$i = 1;
foreach ($documentTypeData as $key => $dType) {
    ?>
    <div class="input-group" style="width:100%">
        <?php $doccoltype = $dType->document_type_column_type; ?>
        <input type="hidden" name="doclabl<?php echo $i; ?>" value="{{ $dType->document_type_column_name }}">
        <input type="text" style="width:100%;" name="doccol<?php echo $i; ?>" class="form-control scndpad" placeholder="Search by <?php echo $dType->document_type_column_name; ?>" <?php if($doccoltype=="Integer"){ ?> data-parsley-type="integer" <?php }else if($doccoltype=="Date"){ ?> id="exp_date" placeholder="YYYY-MM-DD" <?php } ?> >   
    </div>
    <?php
    $i++;
}
?>
<input type="hidden" name="coltypecnt" id="coltypecnt" value="<?php echo $i-1; ?>">
<script type="text/javascript">
$(function ($) {

        var d           = new Date();
        var currentYear = d.getFullYear();
        var newDate     = currentYear+10;
        var date        = '12/31/'+newDate;

    $('#exp_date').daterangepicker({
        /*maxDate: moment(date),*/
        format: '{{ js_date_format() }}',
        singleDatePicker: true,
        "drops": "up",
        showDropdowns: true
    });
});
</script>