<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables/dataTables.bootstrap.css') !!}

<!-- Content Wrapper. Contains page content -->
<section class="content" id="shw">
    <section class="content-header">
        <h1>
            {{ trans('language.user') }} {{trans('language.history')}}
            <small>- {{ $userName }}</small>
        </h1>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
            <li class="active">{{trans('language.user')}} {{trans('language.history')}}</li>
        </ol>  -->           
    </section>
    @if(Session::has('data'))
    <section class="content content-sty" id="spl-wrn">        
        <div class="alert alert-sty {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('data') }}</div>        
    </section>
    @endif
    <!-- Main content -->
    <section class="content">    
      
            <div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <span class="pull-right">
                    <a href="{{url('/users')}}" title="Back"><button class="btn btn-block btn-info btn-flat">{{trans('language.back')}} </button></a>
                </span>
            </div>
            <div class="box-body">
                <table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                    <thead>
                        <tr>
                            <th>{{ trans('language.username') }}</th>
                            <th>{{ trans('language.item') }}</th>
                            <th>{{ trans('language.description') }}</th>
                            <th>{{ trans('language.action') }}</th>
                            <th>{{ trans('language.created date') }}</th>                                
                        </tr>
                    </thead>                       
                    <tfoot>
                        <tr>
                            <td><input type="text" id="usrname" placeholder="{{trans('language.search')}} {{trans('language.username')}}"></td>
                            <td><input type="text" id="item" placeholder="{{trans('language.search')}} {{trans('language.item')}}"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>     
                        <tbody>
                            @foreach ($usersList as $key => $val)
                                <tr class="even" role="row">                                
                                <td>{{ $val->audit_user_name }}</td>
                                <td>{{ $val->audit_owner }}</td>
                                <td>{{ $val->audit_action_type }}</td>
                                <td>{{ $val->audit_action_desc }}</td>
                                <td>{{ $val->created_at }}</td>
                                </tr>
                            @endforeach                                                 
                        </tbody>                    
                    </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- /.col -->
</div><!-- /.row -->    

    </section>
</section><!-- /.content --> 
<style type="text/css">
    tfoot {
         display: table-header-group;
    }
</style>
{!! Html::script('plugins/datatables/jquery.dataTables.min.js') !!}
{!! Html::script('plugins/datatables/dataTables.bootstrap.min.js') !!}
<script>
    $(function () {

        var table = $('#documentTypeDT').DataTable({            
            "paging": true,
            "searching": true,
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