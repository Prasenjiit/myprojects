<?php include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}
<style type="text/css">
@media(max-width:989px){
    .box {
        overflow-x: scroll;
    }
}
tfoot 
    {
    display: table-header-group;
    }
</style>

<?php
    // Fro cancel button
    if(Input::get('frm') == 'documentType' || Input::get('frm') == 'stack' || Input::get('frm') == 'department'):
        $cancelBtnUrl = 'docsList/'.Input::get('fid').'?page='.Input::get('frm').'';
    else:
        $cancelBtnUrl = 'listview?view=list';
    endif;
?>

<!-- Content Wrapper. Contains page content -->
    <section class="content-header">
        <div class="col-sm-9">
            <strong>{{$language['document']}} {{$language['history']}}</strong>
            <small>- {{ $document_name }}</small>
            <!-- <span class="pull-right">
                <a href="{{url($cancelBtnUrl)}}" title="Back"><button class="btn btn-block btn-info btn-flat">Back </button></a>
            </span> -->
        </div>
        <div class="col-sm-3">
            <!-- <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
                <li class="active">{{$language['document']}} {{$language['history']}}</li>
            </ol> -->  
        </div>          
    </section>
    @if(Session::has('data'))
    <section class="content content-sty" id="spl-wrn">        
        <div class="alert alert-sty {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('data') }}</div>        
    </section>
    @endif
    <!-- Main content -->
    <section class="content" id="shw">    
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                            <thead>
                                <tr>
                                    <th>{{$language['username']}}</th>
                                    <th>{{$language['items']}}</th>
                                    <th>{{$language['action']}}</th>
                                    <th>{{$language['description']}}</th>
                                    <th>{{$language['created date']}}</th>                                
                                </tr>
                            </thead>                       
                            <tfoot>
                                <tr>
                                    <td><input type="text" id="usrname" placeholder="Search User Name"></td>
                                    <td><input type="text" id="item" placeholder="Search Item"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>     
                                <tbody>
                                    @foreach ($docsList as $key => $val)
                                        <tr class="even" role="row">                                
                                        <td>{{ $val->audit_user_name }}</td>
                                        <td>{{ $val->audit_owner }}</td>
                                        <td>{{ $val->audit_action_type }}</td>
                                        <td>{{ $val->audit_action_desc }}</td>
                                        <?php /*<td><?php echo date('d-m-Y h:i:s a', strtotime($val->created_at));?></td>*/?>
                                        <td><?php echo dtFormat($val->created_at);?></td>

                                        </tr>
                                    @endforeach                                                 
                                </tbody>                    
                            </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->    
</section><!-- /.content --> 
{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
<script>
    $(function () {

        var table = $('#documentTypeDT').DataTable({      
            "responsive": true,      
            "paging": true,
            "searching": true,
            language: {
            searchPlaceholder: "{{$language['data_tbl_search_placeholder']}}"
            },
            "ordering": true,
            "info": true,
            bJQueryUI: true,
            "autoWidth": false,
            "pageLength": 25,
            "order": [[ 4, "desc" ]],
            columnDefs: [ { orderable: false, targets: [] } ]
        });

        $('#usrname').on('keyup', function(){
            table
            .column(0)
            .search(this.value)
            .draw();
        }); 

        $('#item').on('keyup', function(){
            table
            .column(1)
            .search(this.value)
            .draw();
        }); 

    });
</script>    
@endsection