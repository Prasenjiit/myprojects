<?php $incr = 1; ?>
<?php if(count($noteList)>0){ ?>
@foreach ($noteList as $key => $val)
    @if(($incr % 2) == 1)
        <tr class="oddcol">
    @else
        <tr class="evncol">
    @endif
        <td class="rowstyllft col-md-4"><div style="max-height:200px; overflow-y:auto;">{{ $val->document_note }}</div></td>
        <td class="rowstylmdl col-md-4">{{ $val->created_at }}</td>
        <td class="rowstylrgt col-md-4">{{ $val->document_note_created_by }}</td>
    </tr>                                    
    <?php $incr++; ?>                     
@endforeach
<?php }else{ ?>
<table class="sidbox">
    <tbody> 
    <tr class="oddcol" style="width:100%;">
        <td class="rowstyllft col-md-4"></td>
        <td class="rowstylmdl col-md-4" style="text-align:center;">No note found</td>
        <td class="rowstylrgt col-md-4"></td>
    </tr>
     </tbody>
</table>
<?php } ?>

