<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
<table class="sidbox">
    <th class="head" colspan="3">{{$language['notes']}}

    <a class="closebox" title="Close"><i class="fa fa-close"></i></a>
    <a id="editnote" title="Add New"><i class="fa fa-plus"></i></a>
    <!--@if($view=='add')
    <a id="editnote" title="Add New"><i class="fa fa-plus"></i></a>
    @endif
        @foreach($dglist as $key => $dval)
            <?php //if(@$dval->document_status=="Checkout"){ ?>
                <a //id="editnote" title="Add New"><i class="fa fa-plus"></i></a>
            <?php //} ?>
        @endforeach // This query does't work for some pages.So It has been hidden by Abhi(31/10/2017) -->
    </th>
    <tr class="evncol">
        <td class="rowstyllft">{{$language['note']}}</td>
        <td class="rowstylmdl">{{$language['date']}}</td>
        <td class="rowstylrgt">{{$language['by']}}</td>
    </tr>
    <tr id="newnote" style="display:none;">
        <td colspan="3">
            <textarea style="width:99%;margin-left: 5px;" placeholder="Add new note" name="newnotetxt" id="newnotetxt"></textarea>
            <div id="msgbox" class="mandatory"></div>
            
            <!--Btns-->
            <div class="box-footer">
                <input type="button" id="notesave" class="btn btn-info btn-flat"  value="{{$language['save']}}">
                <input type="button" id="editnote" class="btn btn-primary btn-danger cancel-btn" value="{{$language['cancel']}}">
            </div>

        </td>
    </tr>
    <tr id="msgrow">
        <td colspan="3">
            <div id="msgbox" style="color:#FF0000">Success! Note has been saved successfully.</div>
        </td>
    </tr>
    <?php $incr = 1; ?>
    @foreach ($noteList as $key => $val)
        @if(($incr % 2) == 1)
            <tr class="oddcol">
        @else
            <tr class="evncol">
        @endif
            <td class="rowstyllft"><div style="max-height:200px; overflow-y:auto;">{{ $val->document_note }}</div></td>
            <td class="rowstylmdl" style="width:100px;">{{ $val->created_at }}</td>
            <td  class="rowstylrgt" style="width:100px;">{{ $val->document_note_created_by }}</td>
        </tr>                                    
        <?php $incr++; ?>                     
    @endforeach

</table>
<script type="text/javascript">
$(".closebox").click(function(){
    $("#mini-doc-col").hide();
    $("#msgbox").html("");
});
</script>