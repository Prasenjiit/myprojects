<?php include (public_path()."/storage/includes/lang1.en.php" );?>
{!! Form::open(array('url'=> array('documentsEditAll'), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'uploadFileEditForm', 'id'=> 'uploadFileEditForm')) !!}
<table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable hover" role="grid" aria-describedby="example1_info">                   
<thead>
    <tr>
        <th nowrap="nowrap" width="10%">
        <label class="checkbox-inline"><input type="checkbox" id="ckbCheckAll">{{$language['actions']}}</label>
        </th>
        <th nowrap="nowrap" width="10%">{{$language['document type']}}</th>
        <th nowrap="nowrap" width="10%">{{$settings_document_no}}</th>
        <th nowrap="nowrap" width="10%">{{$settings_document_name}}</th>
        <th nowrap="nowrap" width="10%">{{$language['department']}}</th>
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
        <th nowrap="nowrap" width="10%">{{$language['created date']}}</th>
        <th nowrap="nowrap" width="10%">{{$language['last updated']}}</th>
        <th nowrap="nowrap" width="10%">{{$language['expir_date']}}</th>
        <th nowrap="nowrap" width="10%">{{$language['status']}}</th>
    </tr>
</thead>  
<tbody>
        <?php
            $user_permission=Auth::user()->user_permission;     
        ?>
        <?php $j = 1; ?>
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
            if(($val->document_expiry_date > date('Y-m-d'))){
                $todaydate = date('Y-m-d'); // or your date as well
                $docexpdate = $val->document_expiry_date;
                $datediff = abs(strtotime($docexpdate) - strtotime($todaydate));
                $noofdays = round($datediff / (60 * 60 * 24));                
            }else{
                $noofdays = 0;
            } 
            ?>    

        <tr  role="row" <?php if(($val->document_expiry_date != null) && ($val->document_expiry_date <= date('Y-m-d'))){?> class="even expired"<?php }else if(($noofdays!=0)&&($noofdays<Session::get('settings_document_expiry'))){ ?>class="even expiresoon"<?php }else{ ?>class="even noexpire" <?php } ?>>     
            <td>
               @if($val->document_status!='Checkout')
                    <input name="checkbox[]" type="checkbox" value="{{$val->document_id}}" id="chk{{$val->document_id}}" class="checkBoxClass">
                @else
                    <input name="checkbox_disabled[]" type="checkbox" onclick="return swal('{{$language['no_permission']}}')" value="{{$val->document_id}}" disabled=true>
                @endif
                &nbsp;
                <div class="btn-group">
                  
                  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" title="Actions">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu" style="min-width: 125px;">
                &nbsp;
                <a title="{{$language['open document']}}" href="{{ url('documentManagementView') }}?dcno={{ $val->document_id }}&page=list">
                <?php 
                    $ext = pathinfo($val->document_file_name, PATHINFO_EXTENSION);
                    if($ext=='pdf'){?>
                        <li class="fa fa-file-pdf-o"></li>
                    <?php
                    }
                    elseif ($ext=='png'||$ext=='jpg'||$ext=='jpeg'||$ext=='tiff'||$ext=='tif'||$ext=='TIFF'||$ext=='TIF') {?>
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
                    elseif ($ext=='wav'||$ext=='mp3'||$ext=='ogg') {?>
                        <i class="icon fa fa-file-sound-o"></i>
                    <?php
                    }
                    elseif ($ext=='flv'||$ext=='mp4'||$ext=='ogv'||$ext=='webm') {?>
                        <i class="icon fa fa-file-video-o"></i>
                    <?php
                    }
                    else{?>
                        <li class="fa fa-file-o"></li>
                    <?php
                    }
                    ?>
                    </a>
                &nbsp;
                <a title="{{$language['history']}}" href="{{ url('documentHistory', $val->document_id ) }}"><i class="fa fa-history" ></i></a>
                &nbsp;
                <!-- Check Out Document -->
                @if(stristr($user_permission,"checkout"))
                @if($val->document_status == 'Published')
                <a href="javascript:void(0);"  title="{{$language['check out']}}" onclick="return myFunction('{{$val->document_file_name}}','{{$val->document_id}}','{{$i}}')"><i class="fa fa-share"></i></a>
                @elseif($val->document_status=='Checkout')
                <a href="javascript:void(0);" title="{{$language['check out']}}" onclick="return swal('\'{{$val->document_name}}\' is curently Checked Out by \'{{$val->document_modified_by}}\'. It must be Checked In first before you can perform this operation.')"><i class="fa fa-share" ></i></a>
                @elseif($val->document_status=='Review')
                <a href="javascript:void(0);" title="{{$language['check out']}}" onclick="return swal('\'{{$val->document_name}}\' is curently under the review')"><i class="fa fa-share" ></i></a>
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
                        <i title="{{$language['delete']}}" class="fa fa-close" onclick="del({{ $val->document_id}},'{{$val->document_name }}')" style="color: red; cursor:pointer;"></i>
                    @else
                        <i title="{{$language['delete']}}" class="fa fa-close" onclick="return swal('\'{{$val->document_name}}\' is curently Checked Out by \'{{$val->document_modified_by}}\'. It must be Checked In first before you can perform this operation.')" style="color: red; cursor:pointer;"></i>
                    @endif
                @endif
                &nbsp;
                <a count="<?php echo $j;?>" id="moredet" data-toggle="modal"  style="cursor:pointer; padding-left:2px; padding-right:2px;" data-target="#viewmoreModal" title="More Details" ><i class="fa fa-ellipsis-v" aria-hidden="true"></i></a>
                &nbsp;
                </ul>
                </div>
            </td>                          
            <td>{{ ucfirst($doctypeval) }}</td>
            <td>{{ $val->document_no }}</td>
            <td>{{ ucfirst($val->document_name) }}<br/>Ver {{ $val->document_version_no }}</td>
            <td>{{ ucfirst($deptval) }}</td>
            <td nowrap="nowrap">{{ ucfirst($stackval)}}</td>
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
            <td nowrap="nowrap">{{ $val->created_at }}</td>
            <td nowrap="nowrap">{{ $val->updated_at }}</td>
            <td nowrap="nowrap">{{ $val->document_expiry_date }}</td>
            <td>{{ ucfirst($val->document_status) }}</td>      
            <input type="hidden" id="doc_id<?php echo $j;?>" value="<?php echo $val->document_id; ?>">
            <input type="hidden" id="doc_version<?php echo $j;?>" value="<?php echo $val->document_version_no; ?>">                                 
        </tr>
        <?php $j++; ?> 
        @endforeach                           
</tbody>                    
</table>

<input type="hidden" name="hidd_count" id="hidd_count" value="0">
<input type="hidden" name="hidd_status" value="published">
{!!Form::close()!!}
<script type="text/javascript">
    var i='<?php echo @$count_sub;?>';
    if(i){
        var def=8;
        var change=(+def + +i);
        var table = $('#documentTypeDT').DataTable({            
        "paging": true,
        "ordering": true,
        "searching": true,
        "ordering": true,
        "info": true,
        bJQueryUI: true,
        "autoWidth": false,
        "pageLength": 25,
        order: [[(change),"desc"]],
        language: {
                searchPlaceholder: "{{$language['data_tbl_search_placeholder']}}"
            },
        columnDefs: [ { orderable: false, targets: [0] } ]
        });
        //for highlight the row
    $("#documentTypeDT tbody tr").on('click',function(event) {
        $("#documentTypeDT tbody tr").removeClass('row_selected');        
        $(this).addClass('row_selected');
    });
    }else{
        var table = $('#documentTypeDT').DataTable({            
                "paging": true,
                "searching": true,
                "ordering": true,
                "ordering": true,
                "info": true,
                bJQueryUI: true,
                "autoWidth": false,
                "pageLength": 25,
                order: [[8,"desc"]],
                language: {
                searchPlaceholder: "{{$language['data_tbl_search_placeholder']}}"
                },
                columnDefs: [ { orderable: false, targets: [0] } ]
            });
        //for highlight the row
    $("#documentTypeDT tbody tr").on('click',function(event) {
        $("#documentTypeDT tbody tr").removeClass('row_selected');        
        $(this).addClass('row_selected');
    });
    }

$("#ckbCheckAll").click(function () {
    $(".checkBoxClass").prop('checked', $(this).prop('checked'));
});
    var countChecked = function() {
    var n = $( ".checkBoxClass:checked" ).length;
    document.getElementById('hidd_count').value=n;

    };
    countChecked();
    $( "input[type=checkbox]" ).on( "click", countChecked );

        $("#editall").click(function(){
            var val = $("#hidd_count").val();
            if(val == 0)
                {
                    swal("{{$language['not select']}}");
                    return false;
                }
            else
            {
                $("#uploadFileEditForm").submit();
            }
        });

$("#deletetag").click(function(){
    if(document.getElementById('hidd_count').value==0)
        {
            swal("{{$language['not select']}}");
            return false;
        }
    else{   

             swal({
                  title: "{{$language['confirm_delete_single']}} the documents?",
                  text: "{{$language['Swal_not_revert']}}",
                  type: "{{$language['Swal_warning']}}",
                  showCancelButton: true
                }).then(function (result) {
                    if(result){
                        // Success
                        var arr = $('input:checkbox.checkBoxClass').filter(':checked').map(function () {
                        return this.value;
                        }).get();
                        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                            $.ajax({
                                type: 'post',
                                url: '{{URL('deletePublished')}}',
                               
                                data: {_token: CSRF_TOKEN,selected:arr},
                                timeout: 50000,
                                beforeSend: function() {
                                    $("#bs").show();
                                },
                                success: function(data, status){
                                    
                                    //swal(data);
                                    window.location.reload();
                                },
                                error: function(jqXHR, textStatus, errorThrown){
                                    console.log(jqXHR);    
                                    console.log(textStatus);    
                                    console.log(errorThrown);    
                                },
                                complete: function() {
                                    $("#bs").hide();
                                }
                            });
                    
                    }
                });
        }   
    });    
</script>