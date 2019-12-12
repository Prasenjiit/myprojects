<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')

<style type="text/css">
    @media(max-width: 517px){
        .box{
            overflow-x:scroll;
        }
    }
    @media(max-width: 388px){
        div#documentGroupDTsub_wrapper {
            overflow-x: scroll;
        }
    }
    @media(max-width: 991px){
        .content-header {
            padding: 22px 15px 0 !important;
            height: 48px;
        }
        .content-header>h1 {
            margin-top: -19px;
        }
    }

    @media (max-width: 767px){
        .content-header {
            padding: 15px 0px 0 !important;
            height: 20px;
        }
    }

    /*<--mobile view table-->*/
    @media(max-width:500px){
        .box{
            overflow-x: auto;
        }
    }
</style>

<section class="content-header">
    <div class="col-sm-8">
        <span style="float:left;">
            <strong>
                {{trans('language.dimension_combitaion_details')}}
            </strong> &nbsp;
        </span>
        <span style="float:left;">
            <?php
                $user_permission=Auth::user()->user_permission;
            ?>
           
          <a class="btn btn-primary btn-sm" href="{{ route('dimentioncombinations.create') }}">{{trans('language.add new')}}</a>            
        </span>
    </div>
</section>



<section class="content" id="shw">
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
                <table border="1" id="stackDT" class="table table-bordered table-striped dataTable hover">
                    <thead>
                        <tr>
                             <th>{{trans('language.lblmsg_No')}}</th>
            <th>{{trans('language.lblmsg_LedgerName') }}</th>
            <th>{{trans('language.lblmsg_CostCentre') }}</th>
            <th>{{trans('language.lblmsg_Department') }}</th>
            <th>{{trans('language.lblmsg_Purpose') }}</th>
            <th>{{trans('language.lblmsg_ActiveComb') }}</th>
            <th width="280px">{{trans('language.lblmsg_Action') }}</th>
                        </tr>
                    </thead>      
                    <tbody>                    
                        @foreach ($dimentioncombinations as $dimentioncombination)

                        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $dimentioncombination->LedgerCode }}</td>
            <td>{{ $dimentioncombination->CostCentre }}</td>
             <td>{{ $dimentioncombination->Department }}</td>
             <td>{{ $dimentioncombination->Purpose }}</td>
             <td>
                <input type="checkbox" onclick="return false" @if($dimentioncombination->Active_Comb == 1){{ "checked"}}@endif name="Active_Comb">            
             
             </td>
             <td>
                <form action="{{ route('dimentioncombinations.destroy',$dimentioncombination->id) }}" method="POST">
                
                    <a class="btn btn-info btn-sm" href="{{ route('dimentioncombinations.show',$dimentioncombination->id) }}">{{trans('language.view') }}</a>
                    
                    <a class="btn btn-primary btn-sm" href="{{route('dimentioncombinations.edit',$dimentioncombination->id)}}">{{trans('language.edit') }}</a>
                   
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="dimentioncombinationid" value="{{ $dimentioncombination->id }}">
                    <button type="submit" onclick="deleteData({{ $dimentioncombination->id }},'attribute','{{ trans('language.deleteMsg') }}')" class="btn btn-danger btn-sm">{{trans('language.delete') }}</button>                  
                
            </td>
        </tr>
                        @endforeach
                    </tbody>                                
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- /.col -->
</div><!-- /.row -->
</section>

<!-- User edit form end -->
  
  <script>

    $(document).ready(function() {
        $('#stackDT').DataTable( {
            "pagingType": "full_numbers"
        } );
    } );

</script>  

{!! Html::script('js/parsley.min.js') !!}  
{!! Html::script('js/jquery.form.js') !!}   
@endsection


