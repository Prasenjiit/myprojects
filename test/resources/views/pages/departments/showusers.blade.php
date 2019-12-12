<div class="modal-header">
    <h4 class="modal-title">
        Users in <b>{{$name}}</b> @if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.departments')}} @endif     
    </h4>
</div>
<section class="content">   
        <table border="1" id="documentGroupDTsub" class="table table-bordered table-striped dataTable hover">
            <thead>
                <tr>
                    <th width="30%">Login Name</th>
                    <th width="30%">Full Name</th>
                    <th width="40%">Email ID</th>
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
            <div class="col-sm-6" style="text-align:right; padding-right:0px;">
                {!!Form::button('Close', array('class' => 'btn btn-primary btn-danger', 'id' => 'cnEdi','data-dismiss'=>'modal')) !!}
            </div>
        </div><!-- /.col -->

</section>
{!! Html::script('plugins/jQuery/jQuery-2.1.4.min.js') !!}
