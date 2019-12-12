<div class="row">
    <div class="col-xs-12">
        <?php
            include (public_path()."/storage/includes/lang1.en.php" );
            $user_permission=Auth::user()->user_permission;
        ?>
        <div class="box box-info">
            
            <div class="box-body">
                <table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable hover">
                    <thead>
                        <tr>
                            <th>{{$language['document types']}}</th>
                            <th>{{$language['description']}}</th>                            
                            <th>{{$language['created date']}}</th>                            
                            <th>{{$language['actions']}}</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td><input type="text" id="doctype" placeholder="Search Doc Types"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach ($dglist as $key => $val)
                        <!--Show documents data model-->
                        <div class="modal fade show_model" id="dTShowModal<?php echo $val->document_type_id;?>" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content"></div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                        <tr class="even" role="row">                                
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
                                <i class="fa fa-close" onclick="del({{ $val->document_type_id }}, '{{ $val->document_type_name }}')" title="Delete" style="color: red; cursor:pointer;"></i>&nbsp;
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>                    
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- /.col -->
</div><!-- /.row -->
{!! Html::script('plugins/jQuery/jQuery-2.1.4.min.js') !!}
{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
<script>
    $(function () {

        var rows_per_page = '{{Session('settings_rows_per_page')}}';
        var lengthMenu = getLengthMenu();//Function in app.blade.php

        var table = $('#documentTypeDT').DataTable({            
            "paging": true,
            "lengthMenu": lengthMenu,
            "pageLength":rows_per_page,
            "searching": true,
            "ordering": true,
            "info": true,
            bJQueryUI: true,
            "autoWidth": false,
            "order": [[ 2, "desc" ]],
            language: {
                searchPlaceholder: "{{$language['data_tbl_search_placeholder']}}"
            },
            columnDefs: [ { orderable: false, targets: [3] } ]
        });
		//for highlight the row
        $("#documentTypeDT tbody tr").on('click',function(event) {
            $("#documentTypeDT tbody tr").removeClass('row_selected');        
            $(this).addClass('row_selected');
        });
		$('#doctype').on('keyup', function(){
            table
            .column(0)
            .search(this.value)
            .draw();
        });  
		
    });
</script>  
<style type="text/css">
    tfoot {
         display: table-header-group;
    }
</style>