<select name="keywords[]" id="keywords" class="multipleSelect keyword" multiple>
<?php
$arr_tag_id=explode(',', $key);
print_r($arr_tag_id);
foreach ($tagWords as $key => $tagval) { ?>
    <option value="<?php echo $tagval->tagwords_id; ?>"<?php if(in_array($tagval->tagwords_id,$arr_tag_id)){ echo "selected";}?>><?php echo $tagval->tagwords_title;?></option>
 <?php
}
?></select>
<script>
    $('.multipleSelect').fastselect();
</script>
