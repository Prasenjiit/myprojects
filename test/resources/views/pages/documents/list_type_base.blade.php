<?php include (public_path()."/storage/includes/lang1.en.php" );?>
<table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
<thead>
    <tr>
        <th nowrap="nowrap" width="10%"><input type="checkbox" id="ckbCheckAll" />&nbsp;&nbsp;{{$language['actions']}}</th>
        <th nowrap="nowrap" width="10%" id="docno">{{$language['document type']}}</th>
        <th nowrap="nowrap" width="10%" id="docname">{{$settings_document_no}}</th>
        <th nowrap="nowrap" width="10%">{{$settings_document_name}}</th>
        <th nowrap="nowrap" width="10%">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{$language['departments']}} @endif</th>
        <th nowrap="nowrap" width="10%">{{$language['stack']}}</th>
        <!--For Document column name and values-->
        @if(@$col_names)
            <?php
            $i=0;
            foreach($col_names as $val){?>
            <th nowrap="nowrap" width="10%">{{$val->document_type_column_name}}</th>
            <?php 
            $i++;
            }
            $count_sub=$i; 
            ?>
        @endif
        <!--For Document column name and values-->
        <th nowrap="nowrap" width="10%">{{$language['ownership']}}</th>
        <th nowrap="nowrap" width="10%">{{$language['path']}}</th>
        <th nowrap="nowrap" width="10%">{{$language['created date']}}</th>
        <th nowrap="nowrap" width="10%">{{$language['last updated']}}</th>
        <th nowrap="nowrap" width="10%">{{$language['expir_date']}}</th>
        <th nowrap="nowrap" width="10%">{{$language['status']}}</th>                                
    </tr>
</thead>

<tfoot>
    <tr>
        <td></td>
        <td><input type="text" id="doctype" placeholder="Search Doc Type" style="width:130px"></td>
        <td><input type="text" id="docno" placeholder="Search Doc No" style="width:130px"></td>
        <td><input type="text" id="docname" placeholder="Search Doc Name" style="width:130px"></td>
        <td></td>
        <td><input type="text" id="stacks" placeholder="Search Stacks" style="width:130px"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>                                
        <td></td>
    </tr>
</tfoot>    

<tbody>
        <?php
            $user_permission=Auth::user()->user_permission; 
            $oneDimensionalArray=Session::get('child');
            if(@$level){
            foreach ($level as $key => $value) {
                $levels[]= ($value[0]->lvl+1);
            }
            foreach ($levels as $key => $value) {   
                $target=array_splice($oneDimensionalArray,0, $value);
                $path[]=array_reverse($target);               
            }
            $u=1;
            foreach($path as $val):
                $path[$u] = implode('/',$val);
                $u++;
            endforeach;
            }        
            $j = 1; ?>
        @foreach ($dglist as $key => $val)
            <?php  $stckarr = explode(',',$val->stack_id);
            $deptarr = explode(',',$val->department_id);
            $dtyparr = explode(',',$val->document_type_id);

            $valuearr= @$val->document_type_columns; 
            $stackval = "";   
            $doctypeval = "";    
            $deptval = "";                                   
            for($i=0;$i<count($stckarr);$i++){ ?>
                @foreach ($stacks as $key => $stval)
                    @if($stckarr[$i] == $stval->stack_id)
                        <?php if($i==count($stckarr)-1){
                            $stackval = $stackval.$stval->stack_name;
                        }else{
                            $stackval = $stackval.$stval->stack_name.', ';
                        } ?>
                    @endif
                @endforeach   
            <?php }

            for($i=0;$i<count($deptarr);$i++){ ?>
                @foreach ($depts as $key => $dtval)
                    @if($deptarr[$i] == $dtval->department_id)
                        <?php if($i==count($deptarr)-1){
                            $deptval = $deptval.$dtval->department_name;
                        }else{
                            $deptval = $deptval.$dtval->department_name.', ';
                        } ?>
                    @endif
                @endforeach   
            <?php }

            for($i=0;$i<count($dtyparr);$i++){ ?>
                @foreach ($docType as $key => $stval)
                    @if($dtyparr[$i] == $stval->document_type_id)
                        <?php if($i==count($dtyparr)-1){
                            $doctypeval = $doctypeval.$stval->document_type_name;
                        }else{
                            $doctypeval = $doctypeval.$stval->document_type_name.', ';
                        } ?>
                    @endif
                @endforeach   
            <?php }

            ?>    

        <tr class="even" role="row">      
            <td>
               @if($val->document_status!='Checkout')
                    <input name="checkbox[]" type="checkbox" value="{{$val->document_id}}" id="chk{{$val->document_id}}" class="checkBoxClass">
                @else
                    <input name="checkbox_disabled[]" type="checkbox" onclick="return swal('{{$language['no_permission']}}')" value="{{$val->document_id}}" disabled=true>
                @endif
                &nbsp;
                <a title="{{$language['open document']}}" href="{{ url('documentManagementView') }}?dcno={{ $val->document_id }}&page=list">
                <?php 
                $ext = pathinfo($val->document_file_name, PATHINFO_EXTENSION);
                if($ext=='pdf'){?>
                    <li class="fa fa-file-pdf-o"></li>
                <?php
                }
                elseif ($ext=='png'||$ext=='jpg'||$ext=='jpeg'||$ext=='tiff'||$ext=='TIF') {?>
                    <li class="fa fa-file-image-o"></li>
                <?php
                }
                elseif ($ext=='docx'||$ext=='doc') {?>
                    <li class="fa fa-file-word-o"></li>
                <?php
                }
                elseif ($ext=='txt') {?>
                    <li class="fa fa-file-text-o"></li>
                <?php
                }
                elseif ($ext=='zip'||$ext=='rar') {?>
                    <li class="fa fa-file-archive-o"></li>
                <?php
                }
                elseif ($ext=='xls'||$ext=='xlsx') {?>
                    <li class="fa fa-file-excel-o"></li>
                <?php
                }
                else{?>
                    <li class="fa fa-file-o"></li>
                <?php
                }
                ?>
                </a>
                &nbsp;
                <a title="{{$language['history']}}" href="{{ url('documentHistory', $val->document_id ) }}"><li class="fa fa-history" ></li></a>
                &nbsp;
                <!-- Check Out Document -->
                @if(stristr($user_permission,"checkout"))
                @if($val->document_status!='Checkout')
                <a href="javascript:void(0);"  title="{{$language['check out']}}" onclick="return myFunction('{{$val->document_file_name}}','{{$val->document_id}}','{{$i}}','23232')"><i class="fa fa-share"></i></a>
                @else
                <a href="javascript:void(0);" title="{{$language['check out']}}" onclick="return swal('\'{{$val->document_name}}\' is curently Checked Out by \'{{$val->document_modified_by}}\'. It must be Checked In first before you can perform this operation.')"><i class="fa fa-share" ></i></a>
                @endif
                @endif
                &nbsp;
                @if(stristr($user_permission,"edit"))
                        <a title="{{$language['edit_document']}}" href="{{route('editAllDocument', array('id'=>$val->document_id))}}&page=documentsList&status={{$val->document_status}}">
                        <i class="fa fa-pencil"></i></a>
                @endif
                &nbsp;
                @if(stristr($user_permission,"delete"))
                    @if($val->document_status!='Checkout')
                        <li title="{{$language['delete']}}" class="fa fa-close" onclick="del({{ $val->document_id}},'{{$val->document_name }}')" style="color: red; cursor:pointer;"></li>
                    @else
                        <li title="{{$language['delete']}}" class="fa fa-close" onclick="return swal('\'{{$val->document_name}}\' is curently Checked Out by \'{{$val->document_modified_by}}\'. It must be Checked In first before you can perform this operation.')" style="color: red; cursor:pointer;"></li>
                    @endif
                @endif
                &nbsp;
                <a count="<?php echo $j;?>" id="moredet" data-toggle="modal"  style="cursor:pointer; padding-left:2px; padding-right:2px;" data-target="#viewmoreModal" title="More Details" ><i class="fa fa-ellipsis-v" aria-hidden="true"></i></a>
                &nbsp;
                <a title="{{$language['related documents']}}" href="{{ url('relatedsearch', array($val->document_id,$val->document_no,$val->document_name)) }}"><li class="fa fa-files-o" ></li></a>
            </td>                          
            <td>{{ ucfirst($doctypeval) }}</td>
            <td>{{ $val->document_no }}</td>
            <td>{{ ucfirst($val->document_name) }}<br/>Ver {{ $val->document_version_no }}</td>
            <td>{{ ucfirst($deptval) }}</td>
            <td>{{ ucfirst($stackval)}}</td>
            <!--Document column name and values-->
            @if(@$col_names)
            <?php
                foreach ($valuearr as $key => $value) {
                ?>
                <td>{{ucfirst($value->document_column_value)}}</td>
                <?php
                }
                ?>
            @endif
            <!--Document column name and values-->
            <td>{{ ucfirst($val->document_ownership) }}</td>
            <td>{{ ucfirst($path[$j]) }}</td>

            <!-- date sorting issue in datatable, format changed -->

            <?php /*<td><?php echo date('d-m-Y h:i:s a', strtotime($val->created_at));?></td>
            <td><?php echo date('d-m-Y h:i:s a', strtotime($val->updated_at));?></td>*/?>
            
            <td><?php echo $val->created_at;?></td>
            <td><?php echo $val->updated_at;?></td>
            <td>{{ $val->document_expiry_date }}</td>   
            <td>{{ ucfirst($val->document_status) }}</td>      
            <input type="hidden" id="doc_id<?php echo $j;?>" value="<?php echo $val->document_id; ?>">                              
        </tr>
        <?php $j++; ?> 
        @endforeach                           
</tbody>                    
</table>
<script type="text/javascript">
    var i='<?php echo @$count_sub;?>';

    var rows_per_page = '{{Session('settings_rows_per_page')}}';
    var lengthMenu = getLengthMenu();//Function in app.blade.php

    if(i){
        var def=9;
        var change=(+def + +i);
        var table = $('#documentTypeDT').DataTable({
            "lengthMenu": lengthMenu,
            "pageLength":rows_per_page,            
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            bJQueryUI: true,
            "autoWidth": false,
            "scrollY": false,
            "scrollX": true,
            order: [[(change),"desc"]],
            columnDefs: [ { orderable: false, targets: [0] } ]
        });
    }else{
        var table = $('#documentTypeDT').DataTable({   
            "lengthMenu": lengthMenu,
            "pageLength":rows_per_page,         
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            bJQueryUI: true,
            "autoWidth": false,
            "scrollY": false,
            "scrollX": true,
            order: [[9,"desc"]],
            columnDefs: [ { orderable: false, targets: [0] } ]
        });
    }
$('#doctype').on('keyup', function(){
    table
    .column(1)
    .search(this.value)
    .draw();
});       
$('#docno').on('keyup', function(){
    table
    .column(2)
    .search(this.value)
    .draw();
});       
$('#docname').on('keyup', function(){
    table
    .column(3)
    .search(this.value)
    .draw();
});       
$('#stacks').on('keyup', function(){
    table
    .column(5)
    .search(this.value)
    .draw();
});
$("#ckbCheckAll").click(function () {
        $(".checkBoxClass").prop('checked', $(this).prop('checked'));
    });
    var countChecked = function() {
    var n = $( ".checkBoxClass:checked" ).length;
    document.getElementById('hidd_count').value=n;
    };
    countChecked();
    $( "input[type=checkbox]" ).on( "click", countChecked );

        $("form").submit(function(){
            if(document.getElementById('hidd_count').value==0)
                {
                    swal("{{$language['not select']}}");
                    return false;
                }
        });     
</script>