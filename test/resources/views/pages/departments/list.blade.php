<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header">
                <span class="pull-right">
                    <?php
                    $user_permission=Auth::user()->user_permission;
                    include (public_path()."/storage/includes/lang1.en.php" );                         
                    ?>                        
                </span>
            </div>
            <div class="box-body">
                <table border="1" id="departmentDT" class="table table-bordered table-striped dataTable hover">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.departments')}} @endif</th>
                            <th>{{trans('language.description')}}</th>                         
                            <th>{{trans('language.created date')}}</th>                       
                            <th width="18%" nowrap="nowrap">{{trans('language.actions')}}</th>
                            <th>ID</th>
                        </tr>
                    </thead>  
                    <tfoot>
                        <tr>
                            <td></td>
                            <td><input type="text" id="dept" placeholder="Search Department"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>                  
                    <tbody>                    
                        @foreach ($dglist as $key => $val)

                        <!--Show documents data model-->
                        <div class="modal fade show_model" id="dTShowModal<?php echo $val->department_id;?>" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content"></div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->

                         <!--Show users data model-->
                        <div class="modal fade show_model" id="dTShowUserModal<?php echo $val->department_id;?>" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content"></div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->

                        <tr class="even" role="row">
                            <td>{{ $val->department_order }} <div class="grippy1 pull-right"></div></td>                                
                            <td>
                                @if(stristr($user_permission,"edit"))
                                <a href="{{ url('departmentEdit', $val->department_id ) }}">
                                    @endif
                                    {{ $val->department_name }}
                                    @if(stristr($user_permission,"edit"))
                                </a>
                                @endif
                            </td>
                            <td>{{ $val->department_description }}</td>
                            <!-- <td>{{ date('d-m-Y h:i:s a', strtotime($val->created_at)) }}</td> -->
                            <td>{{ $val->created_at->format('d-m-Y H:i:s A') }}</td>
                            <td width="10%">
                                @if(stristr($user_permission,"view"))

                                <!-- old popup view listing documents -->

                                <!-- <a href="{{URL::route('showDocuments', array($val->department_id,$val->department_name))}}" departmentID = {{$val->department_id}}  class="doc-department" data-toggle="modal"  data-target="#dTShowModal<?php //echo $val->department_id;?>" title="Show Documents">
                                    <i class="fa fa-folder-open" aria-hidden="true"></i>
                                </a> -->

                                <a href="listview?view=department&id={{$val->department_id}}" title="Show Documents" class="doc-department">
                                    <i class="fa fa-folder-open" aria-hidden="true"></i>
                                </a>

                                @endif
                                &nbsp;
                                @if(stristr($user_permission,"view"))
                                <a href="{{URL::route('showUser', array($val->department_id,$val->department_name))}}" data-toggle="modal" data-target="#dTShowUserModal<?php echo $val->department_id;?>" title="Show Users">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                </a>
                                @endif
                                &nbsp;
                                @if(stristr($user_permission,"edit"))
                                <a href="{{ url('departmentEdit', $val->department_id ) }}" title="Edit">
                                    <i class="fa fa-pencil" style="cursor:pointer;"></i>
                                </a>
                                @endif
                                &nbsp;
                                @if(stristr($user_permission,"delete"))
                                <i class="fa fa-trash" onclick="del({{ $val->department_id }}, '{{ $val->department_name }}')" style="color: red; cursor:pointer;" title="Delete"></i>
                                @endif
                            </td>
                            <td>{{$val->department_id}}</td>
                        </tr>
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
        var table = $('#departmentDT').DataTable({  
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
            url: '{{URL('rowReorderDept')}}',
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
    });
        //for highlight the row
        $("#departmentDT tbody tr").on('click',function(event) {
            $("#departmentDT tbody tr").removeClass('row_selected');        
            $(this).addClass('row_selected');
        });
        $('#dept').on('keyup', function(){
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