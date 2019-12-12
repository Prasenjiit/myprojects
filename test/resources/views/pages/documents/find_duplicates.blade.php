<?php
    $str= explode(",",Auth::user()->user_permission);
    include (public_path()."/storage/includes/lang1.en.php" );
    $user_permission=Auth::user()->user_permission;                       
?>
@extends('layouts.app')
@section('main_content')

{!! Html::script('js/parsley.min.js') !!}  
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}

<style type="text/css">
    table.dataTable{
        border-collapse:collapse;
    }
    .expiresoon{
        border-left: 6px solid #9900ff;
    }
    .expired{
        border-left: 6px solid #cc0066;
    }
    .noexpire{
        border-left: 6px solid #996600; 
    }

    .col-md-3{
        padding-left: 0px !important;
    }
    .col-md-6{
        padding-left: 0px !important;
        padding-right: 0px !important;
    }
    .box{
          margin-bottom: 10px !important;
    }
    .btn{
        padding: 4px 12px !important;
    }
    /*.main-header .logo{
       height: 70px;
    }*/
    .topicn{
        z-index:1050; 
        position:relative;
        left:190px;
        top: -66px;
    }

    /*<--model style-->*/
    .modal-body{
        overflow: auto;
        max-height: 550px;
    }
    .ScrollStyle
    {
        max-height: 570px;
        overflow-y: auto;
    }
    .box-modify {
        border-radius: 0px 0px 3px 3px;
        padding-bottom: 40px;
    }
</style>
<section class="content-header">
    <div class="col-sm-2">    
        <!-- heading of page -->    
        <strong>Missing Documents</strong>
    </div>
   
</section>
<section class="content content-sty" id="msg" style="display:none;"></section>
<section class="content content-sty" id="msg_add" style="display:none;"></section>
@if(Session::has('flash_message_edit'))
<section class="content content-sty" id="spl-wrn">        
    <div class="alert alert-sty {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('flash_message_edit') }}</div>        
</section>
@endif

<!-- Error Msg -->
@if(Session::get('dglist') == '0')
<section class="content content-sty">        
    <div class="alert alert-warning" id="hide-div">
        <p style="text-align: center;"><b>There is no data found.</b></p>
    </div>       
</section>
@endif

<!--Checking view permission-->
@if(stristr($user_permission,'view'))   
    <section class="content" id="shw">

      

        <!--View more content-->
        <div class="modal fade" id="viewmoreModal" data-backdrop="true" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header" style="border-bottom-color: deepskyblue;">
                        <h4 class="modal-title" style="float:left; width:90%;">
                            {{$language['all documents']}}
                            <small>- View All Data</small>
                        </h4>

                        <a href="javascript:void(0);">
                            <button class="btn btn-primary btn-danger" id="cn" data-dismiss="modal" type="button">{{@$language['close']}}</button>
                        </a>
                    </div>

                    <div class="modal-body" id="more">  
                       
                    </div><!-- /.modal-dialog -->

                </div><!-- /.modal -->
            </div>
        </div>
            
          
                                
    <table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable hover">   
        <thead>
            <tr>
                
                <th width="10%">#</th>                          
                    <th>Document Name</th>
                    <th>Document No</th>
                    <th>File Name</th>                   
                    <th>Created Date</th>
                    <th>Updated At</th>                                 
            </tr>
        </thead>


        <tbody>
                <?php
                    $user_permission=Auth::user()->user_permission;     
                 $j = 1; 

                 print_r($datalist); ?>
                               
        </tbody>                    
    </table>
                            
                            <!--dataTable loading here-->
                            

                        </div><!-- /.box-body -->
                    </div><!-- /.box -->

                    <div class="form-group">
                        <label class="col-sm-5 control-label"></label>
                        <div class="col-sm-7">
                        
                        <!-- <span id="movetagdraft" value="Save&draft" name="Save&draft" class="btn btn-primary">Download</span> &nbsp;&nbsp; -->
                        <!-- bulk encrypt -->
                        @if (Session::get('module_activation_key6')==Session::get('tval'))
                        <span id="bulkencypt" value="Encrypt" name="bulkencypt" class="btn btn-primary">Encrypt</span> &nbsp;&nbsp;
                        @endif
                        <!-- end bulk encrypt -->
                        @if(stristr($user_permission,'edit')) 
                        {!!Form::submit('Edit', array('class' => 'btn btn-primary','id'=>'Edit')) !!} &nbsp;&nbsp;
                        @endif
                        <input type="hidden" name="hidd_status" value="published">
                        @if(stristr($user_permission,"delete"))
                        <span id="deletetag" value="Delete" name="delete" class="btn btn-primary">{{$language['delete']}}</span> &nbsp;&nbsp;
                        @endif
                        
                        {!!Form::button($language['cancel'], array('class' => 'btn btn-primary btn-danger', 'id' => 'cnEdi','onclick'=> 'window.location="documentAdvanceSearch/edit"')) !!}
                        <input type="hidden" name="hidd_view" id="hidd_view" value="list">
                        <input type="hidden" name="hidd_count" id="hidd_count" value="0">
                        <input type="hidden" name="hidd_type" id="hidd_type" value="published">
                        </div>
                    </div>
                </div><!-- /.col -->
            </div><!-- /.row -->
         <!-- Adv Search -->
        <div class="modal fade" id="dTSearchModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content"></div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </section>
@else
    <section class="content"><div class="alert alert-danger alert-sty">{{$language['dont_hav_permission']}}</div></section>
@endif

<!-- User edit Form -->
<div class="modal fade" id="dTEditModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- User edit form end -->
<!-- addview_workflow -->
<div class="modal fade" id="addview_workflow" data-backdrop="true" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div id="content_wf"></div>
</div>
<!-- addview_workflow end -->
{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}

<script type="text/javascript">
        var table = $('#documentTypeDT').DataTable({            
                "paging": true,
                "lengthMenu": lengthMenu,
                "pageLength":rows_per_page,
                "searching": true,
                "ordering": true,
                "info": true,
                bJQueryUI: true,
                "autoWidth": false,
                "scrollX": true,
                "scrollY": 350,
                language: {
                searchPlaceholder: "{{$language['data_tbl_search_placeholder']}}"
                },
                columnDefs: [ { orderable: false, targets: [0] } ]
            });
       
 
</script>


@endsection