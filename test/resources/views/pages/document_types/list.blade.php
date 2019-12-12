<div class="row">
    <div class="col-xs-12">
        <?php
            include (public_path()."/storage/includes/lang1.en.php" );
            $user_permission=Auth::user()->user_permission;
        ?>
        <div class="box box-info">
            
            <div class="box-body" style="overflow-x: auto; overflow-y: auto;">
                <table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable hover">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>{{trans('document_types.document types')}}</th>
                            <th>{{trans('language.description')}}</th>                       
                            <th>{{trans('language.created date')}}</th>                     
                            <th class="no-sort">{{trans('language.actions')}}</th>
                            <th>ID</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td><input type="text" id="doctype" placeholder="Search Doc Types"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                    <tbody>
                    <?php $i=1;?>
                        @foreach ($dglist as $key => $val)
                        <!--Show documents data model-->
                        <div class="modal fade show_model" id="dTShowModal<?php echo $val->document_type_id;?>" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content"></div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                        <tr class="even" role="row">   
                            <td>{{$val->document_type_order}}<div class="grippy1 pull-right"></div></td>
                                              
                            <td>
                                @if(stristr($user_permission,"edit"))
                                <a href="{{URL::route('documentTypeEdit', $val->document_type_id )}}">
                                    @endif
                                    {{ $val->document_type_name }}
                                    @if(stristr($user_permission,"edit"))
                                </a>
                                @endif
                            </td>
                            <td>{{ $val->document_type_description }}</td>
                             <!-- <td>{{ date('d-m-Y h:i:s a', strtotime($val->created_at)) }}</td> -->
                             <td>{{ $val->created_at }}</td>
                            <td>
                                @if(stristr($user_permission,"view"))

                                <!-- old popup view listing documents -->

                                <!-- <a href="{{URL::route('showDocument', array($val->document_type_id,$val->document_type_name))}}" title="Show Documents" data-toggle="modal" data-target="#dTShowModal<?php //echo $val->document_type_id;?>">
                                    <i class="fa fa-folder-open" aria-hidden="true"></i>
                                </a> -->

                                <a href="listview?view=documentType&id={{$val->document_type_id}}" title="Show Documents">
                                    <i class="fa fa-folder-open" aria-hidden="true"></i>
                                </a> 

                                @endif
                                &nbsp;
                                @if(stristr($user_permission,"edit"))
                                <a href="{{URL::route('documentTypeEdit', $val->document_type_id )}}" title="Edit" id="document-type-edit" documentTypeId="{{$val->document_type_id}}">
                                    <i class="fa fa-pencil" style="cursor:pointer;"></i>
                                </a>                            
                                &nbsp;
                                @endif
                                @if(stristr($user_permission,"delete"))
                                <i class="fa fa-trash" onclick="del({{ $val->document_type_id }}, '{{ $val->document_type_name }}')" title="Delete" style="color: red; cursor:pointer;"></i>&nbsp;
                                @endif
                            </td>
                            <td>{{$val->document_type_id}}</td>     
                        </tr>
                        <?php $i++; ?>
                        @endforeach
                    </tbody>                    
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- /.col -->
</div><!-- /.row -->

<script>
    $(function () {

        var rows_per_page = '{{Session('settings_rows_per_page')}}';
        var lengthMenu = getLengthMenu();//Function in app.blade.php
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var table = $('#documentTypeDT').DataTable({            
            rowReorder: true,
            "lengthMenu": lengthMenu,
            "pageLength":rows_per_page,
            language: {
                searchPlaceholder: "{{trans('language.data_tbl_search_placeholder')}}"
            },
            "columnDefs": [
            {
                "targets": [ 5 ],
                "visible": false,
                "searchable": false
            },
            {targets: 'no-sort', orderable: false}]
        });
        table.on( 'row-reorder', function ( e, diff, edit ) {
        var result = 'Reorder started on row: '+edit.triggerRow.data()[1]+'<br>';
        var id = [];
        
        var newdata = [];
        var olddata = [];
        for ( var i=0, ien=diff.length ; i<ien ; i++ ) 
        {
            var rowData = table.row( diff[i].node ).data();
 
            result += rowData[1]+' updated to be in position '+
                diff[i].newData+' (was '+diff[i].oldData+')<br>';
                
                id.push(rowData[5]);
                newdata.push(diff[i].newData);
                olddata.push(diff[i].oldData);
        }
        $.ajax({
            type: 'post',
            url: '{{URL('rowReorder')}}',
            dataType: 'json',
            data: {_token: CSRF_TOKEN, id:id, newval:newdata, oldval:olddata},
            timeout: 50000,
            beforeSend: function() {
    
            },
            success: function(response){
                console.log(response);
            }
        });
        console.log(result);
    } );
		//for highlight the row
        $("#documentTypeDT tbody tr").on('click',function(event) {
            $("#documentTypeDT tbody tr").removeClass('row_selected');        
            $(this).addClass('row_selected');
        });
		$('#doctype').on('keyup', function(){
            table
            .column(1)
            .search(this.value)
            .draw();
        }); 

    });

</script>  
<style type="text/css">
    tfoot {
         display: table-header-group;
    }
    div.grippy1 {
      content: '....';
      width: 10px;
      
      display: inline-block;
      line-height: 6px;
      padding: 3px 4px;
      cursor: move;
      vertical-align: middle;
      margin-top: -.7em;
      margin-right: .3em;
      font-size: 14px;
      font-family: sans-serif;
      letter-spacing: 2px;
      color: black;
      text-shadow: 1px 0 1px black;
    }
    div.grippy1::after {
      content: '.. .. .. ..';
    }
</style>