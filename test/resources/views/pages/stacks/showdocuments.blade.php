<div class="modal-header">
    <h4 class="modal-title">
        Documents in <b>{{$name}}</b> Types 
    </h4>
</div>
<div class="modal-body">
    <table border="1" id="documentGroupDT" class="table table-bordered table-striped dataTable hover" role="grid" aria-describedby="example1_info">
        <thead>
            <tr>
                <th>{{$settings_document_no}}</th><th>{{$settings_document_name}}</th>
            </tr>
        </thead>
        <tbody>
        @foreach($results as $val)            
            <tr>
                <td>{{ $val->document_no}}</td>
                <td>{{ $val->document_name}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="form-group">
        <label class="col-sm-6 control-label"></label>
        <div class="col-sm-6" style="text-align:right;">
            {!!Form::button('Close', array('class' => 'btn btn-primary btn-danger', 'id' => 'cnEdi','data-dismiss'=>'modal')) !!}
        </div>
    </div><!-- /.col -->
</div>
{!! Html::script('plugins/jQuery/jQuery-2.1.4.min.js') !!}
  