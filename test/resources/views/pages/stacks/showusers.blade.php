<div class="modal-header">
    <h4 class="modal-title">
        Users in <b>{{$name}}</b> Department        
    </h4>
</div>
<div class="modal-body">    
    
            <table border="1" id="documentGroupDTsub" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                        <thead>
                            <tr>
                                <th width="30%">Username</th>
                                <th width="30%">Full Name</th>
                                <th width="40%">Email ID</th>
                            </tr>
                            </tr>
                        </thead>
                        <tbody>    
                        @foreach($results as $user)
                            <tr>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->user_full_name }}</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                        @endforeach 
                        </tbody>
            </table>
        <div class="form-group">
        <label class="col-sm-6 control-label"></label>
        <div>
           
            {!!Form::button('Cancel', array('class' => 'btn btn-primary btn-danger', 'id' => 'cnEdi')) !!}

        </div>
    </div><!-- /.col -->
        
</div>



{!! Html::script('plugins/jQuery/jQuery-2.1.4.min.js') !!}
    {!! Html::script('plugins/datatables/jquery.dataTables.min.js') !!}
{!! Html::script('plugins/datatables/dataTables.bootstrap.min.js') !!}
<script>
    $(function ($) {        
        $("#cnEdi").click(function() {
            window.location.reload();
        });
    });

    $(function () {
        $('#documentGroupDTsub').DataTable({            
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            bJQueryUI: true,
            "autoWidth": false,
            "pageLength": 10,
            order: [],
            columnDefs: [ { orderable: false, targets: [2,2] } ]
        });
    });
</script>    