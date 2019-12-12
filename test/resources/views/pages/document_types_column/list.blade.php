<div class="row">
        <div class="col-xs-12">

            <div class="box">
                <div class="box-header">
                    <span class="pull-right"><a href="" data-toggle="modal" data-target="#dTAddModal"><button class="btn-info pull-right">Add</button></a></span>
                </div>
                <div class="box-body">
                    <table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                        <thead>
                            <tr>
                                <th width="20%">Document Type</th>
                                <th width="30%">Column Name</th>
                                <th width="45%">Column Type</th>
                                <th width="5%" class="no-sort" style="content:none;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>     
                                                                  
                                @foreach ($dtcollist as $key => $val)
                                <tr class="even" role="row">                                
                                    <td width="20%">{{ $val->docType->document_types_name }}</td>
                                    <td width="30%">
                                        <a href="{{URL::route('documentTypeColumnEdit', $val->document_types_column_id )}}" data-toggle="modal" data-target="#dTEditModal">
                                            {{ $val->document_types_column_name }}
                                        </a>
                                    </td>
                                    <td width="45%">{{ $val->document_types_column_type }}</td>
                                    <td width="5%">
                                        <!-- <li class="fa fa-pencil" onclick="fun({{ $val->id }})" style="cursor:pointer;"></li> -->
                                        <a href="{{URL::route('documentTypeColumnEdit', $val->document_types_column_id )}}" data-toggle="modal" data-target="#dTEditModal">
                                            <li class="fa fa-pencil" style="cursor:pointer;"></li>
                                        </a>
                                        &nbsp;
                                        <li class="fa fa-close" onclick="del({{ $val->document_types_column_id }}, '{{ $val->document_types_column_name }}')" style="color: red; cursor:pointer;"></li>
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
    {!! Html::script('plugins/datatables/jquery.dataTables.min.js') !!}
{!! Html::script('plugins/datatables/dataTables.bootstrap.min.js') !!}
<script>
    $(function () {
        $('#documentTypeDT').DataTable({            
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            bJQueryUI: true,
            "autoWidth": false,
            "pageLength": 10,
            order: [],
            columnDefs: [ { orderable: false, targets: [2] } ]
        });
    });
</script>  
