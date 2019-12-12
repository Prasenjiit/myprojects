@extends('layouts.app')
<?php
    include (public_path()."/storage/includes/lang1.en.php" );
?>
@section('main_content')
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}
{!! Html::style('plugins/datatables_new/colReorder.dataTables.min.css') !!}
{!! Html::style('plugins/EasyAutocomplete-1.3.5/easy-autocomplete.min.css') !!} 
{!! Html::style('css/dropzone.min.css') !!}
<style type="text/css">
.help_text{
    /*padding-left: 15px;*/
    font-size: 12px;
    color: #999;
}
.dataTables_scroll
{
    /*overflow:auto;*/
}
.dataTables_processing 
{
    /*top: 64px !important;*/
    z-index: 1000 !important;
}
table.dataTable
{
    border-collapse:collapse;
}
</style>
<section class="content-header">
  <div class="row">
    <div class="col-sm-8">
      <span style="float:left;">
        <strong>{{ trans('apps.app') }} -  <small>{{$app}}</small> </strong> &nbsp;
      </span>
      <span style="float:left;">
        &nbsp;
        <a href="{{url('apps')}}">
                            <button class="btn btn-info btn-flat newbtn" style="line-height:13px !important;">{{ trans('language.back') }}</button>
                        </a> 

        <?php
        $user_permission=Auth::user()->user_permission; 
        $sort_order =  Session::get('app_serach_order');
        $sort_direct =  Session::get('app_serach_direct');                       
        ?> &nbsp;
        <a href="{{ url('viewapp') }}?action=add&appid={{$app_id}}" class="btn  btn-info btn-flat newbtn">{{ trans('apps.add_new_records') }}  &nbsp;<i class="fa fa-plus"></i>
            </a>
@php if(!$search){ @endphp
            &nbsp;
 <a href="{{ url('appslistview/'.$app_id.'/'.$app) }}?search=search" class="btn  btn-info btn-flat newbtn IndexSearch">{{ trans('apps.search_records') }}  &nbsp;<i class="fa fa-search"></i>
            </a>
            @php } @endphp &nbsp;
            <a href="{{ url('importRecords')}}" class="btn  btn-info btn-flat newbtn" title='{{trans("apps.import_tool_tip")}}'>{{trans("apps.import_records")}} &nbsp; <i class="fa fa-download"></i></a>
      </span>
    </div>
    <div class="col-sm-4">
      <ol class="breadcrumb">
          <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{ trans('language.home') }}</a></li>
          <li class="active">{{ trans('apps.apps') }}</li>
      </ol>
    </div>
  </div>
  </section>
<section class="content" id="shw">
<div class="row">
    @if(Session::has('flash_message_success'))
    <section class="content content-sty" id="spl-wrn">        
        <div class="alert alert-sty {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('flash_message_success') }}</div>        
    </section>
    @endif
    @php
    $module_permission=Auth::user()->user_permission;
    @endphp
    
    <div class="col-xs-12">
        
        <div class="box box-info">
            
            <div class="box-body" id="link_row">
             
            </div>
             <div class="box-footer search_footer" style="display: none;">
                 <input class="btn btn-primary" id="search_index" type="submit" value="{{trans('language.search')}}" > 
                <a href="{{url('apps')}}" class="btn btn-danger">{{trans('language.cancel')}}</a>  
                
            </div>
            <div class="box-body" style="overflow-x: auto; overflow-y: auto;">
            <input type="hidden" name="hidd_count" id="hidd_count" value="0">
             <table border="1" id="documentTypeDT" class="table table-bordered table-striped dataTable hover" width="100%">
                    <thead>
                        <tr>
                            <th class="no-sort" nowrap="nowrap"><label class="checkbox-inline"><input type="checkbox" id="ckbCheckAll">&nbsp;{{$language['actions']}}</label></th>
                            @foreach($heads as $value)
                            <th nowrap="nowrap">{{ucfirst(@$value->document_type_column_name)}}</th>
                            @endforeach
                            <th nowrap="nowrap">Updated By</th>
                            <th nowrap="nowrap">Created</th>
                            <th nowrap="nowrap">Updated</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                        </tr>
                    </tfoot>
                    <tbody>
                     
                    </tbody>                    
                </table>   
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
    <div class="form-group">
        <label class="col-sm-5 control-label"></label>
        <div class="col-sm-7">
            @if(stristr($user_permission,'delete')) 
            <span id="deletetag" value="Delete" name="delete" class="btn btn-danger">{{$language['delete']}}</span> &nbsp;&nbsp;
            @endif
            <a href="{{url('apps')}}" class="btn btn-danger">{{$language['cancel']}}</a>
        </div>
    </div>
</div>
</section>
<div class="modal fade" id="view_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div id="content_form"></div>
    </div>
</div>


<div class="modal fade" id="link_to_doc_modal" data-backdrop="false" data-keyboard="false">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header" style="border-bottom-color: deepskyblue;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Link this record to other documents</h4>
                <p style="font-size:12px; color:#999;">You can link this record to other documents in the system. Linking will group the related documents together</p>
              </div>          
              <div class="modal-body" id="dupicate_data_div">

                 
                 <div class="row" >
                <div class="col-xs-12" style="overflow-x: auto; overflow-y: auto;">

               <table border="1" id="app_info" class="table table-bordered table-striped dataTable hover" width="100%">
                    <thead></thead>
                    <tbody></tbody>                    
                </table>
                </div>
</div>
                <div class="row" style="margin-top: 8px;margin-bottom: 8px;">
                
                <div class="col-xs-12">
                  <p style="font-size:12px; color:#999;">Select the related documents from the list below and click Save</p>     
                </div>
                <div class="col-xs-4">
                  <select class="form-control" id="typeselect" name="typeselect" style="">
                            @foreach($doctypeApp as $val)
                            <option value="{{$val->document_type_id}}" <?php if((Session::get('view') != 'stack') || (Session::get('view') != 'department')){ if(@$type_id == $val->document_type_id) { echo 'selected';}} ?>>{{$val->document_type_name}}</option>
                            @endforeach
                        </select>  
                </div>
                <div class="col-sm-2" style="padding-right:0px;">  
                        <select class="form-control" id="linked_doc" name="linked_doc" >
                            <option value="show_all" selected="selected"> Show All</option>
                             <option value="linked_doc"> Show only Linked Documents</option>
                      </select> 
                    </div>

                <div class="col-sm-2" style="padding-right:0px;">  
                        <select class="form-control" id="search_column" name="search_column" >
                      </select> 
                    </div>
                <div class="col-sm-3" style="padding-left:1px;">
                     
                    <div class="input-group input-group-sm">
                    
                    <input type="text" class="form-control" id="search_text" name="search_text" value="@php echo (isset($search_text))?$search_text:''; @endphp">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-info btn-flat docsrch" id="docsrch"><i class="fa fa-search"></i> {{trans('language.search')}} </button>
                    </span>
                  </div>
                    
                    </div>
                </div>
                 <div class="row">
                <div class="col-xs-12" style="overflow-x: auto; overflow-y: auto;">
               <table border="1" id="link_to_docDT" class="table table-bordered table-striped dataTable hover" width="100%">
                    <thead></thead>
                    <tbody></tbody>                    
                </table>
                </div>
</div>
              </div>
              <div class="modal-footer">
                <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <label class="control-label processing_spinner_modal"></label> 
                    <input type="hidden" id="link_doc_id" name="link_doc_id">
                   <button class="btn btn-primary save_link" type="button" id="replace">{{Lang::get('language.save')}}</button>
                   <button class="btn btn-danger" data-dismiss="modal" type="button">{{Lang::get('language.close')}}</button>
              </div>     
                </div>
                </div>
               </div> 
               
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->

          <!----- DUplicate Row----->
<div class="modal fade" id="duplicate_doc_modal" data-backdrop="false" data-keyboard="false">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <form role="form" id="sjfb-sample" method="POST" action="{{url('saveAppValues')}}" enctype="multipart/form-data">
    {!! csrf_field() !!}
            <input type="hidden" name="hidd_action" id="hidd_action" value="duplicate">
            <input type="hidden" name="hidd_doc" id="duplicate_doc_id" value="">
            <input type="hidden" name="app_id" id="duplicate_app_id" value="">
           
              
              <div class="modal-header" style="border-bottom-color: deepskyblue;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Duplicate Record</h4>
              </div>          
              <div class="modal-body" id="dynamic_from">

                 
            </div>
              <div class="modal-footer">
                <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <label class="control-label processing_spinner_modal"></label> 
                    
                   <button class="btn btn-primary save_duplicate_row" name="duplicate" type="submit" id="duplicate" value="{{Lang::get('language.save')}}">{{Lang::get('language.save')}}</button>
                   <button class="btn btn-danger" data-dismiss="modal" type="button">{{Lang::get('language.close')}}</button>
              </div>     
                </div>
                </div>
               </div> 
               
            </div>
            <!-- /.modal-content -->
            </form>
          </div>
          <!-- /.modal-dialog -->

          <!-- /.Duplicate row end -->

{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
{!! Html::script('plugins/datatables_new/dataTables.colReorder.min.js') !!}
{!! Html::script('plugins/datatables_new/jquery.columntoggle.js') !!}
{!! Html::script('plugins/datatables_new/dataTables.fixedHeader.min.js') !!}

{!! Html::script('js/dropzone.js') !!}
{!! Html::script('plugins/EasyAutocomplete-1.3.5/jquery.easy-autocomplete.min.js') !!} 
{!! Html::script('plugins/simple-jquery-form-builder/js/sjfb-html-generator-app.js') !!}
<script>
var opendoc = '<?php echo Session::get("app_selected_doc_list");?>';
$(document).ready(function(){

    // all check checkbox selection
    $("#ckbCheckAll").click(function () 
    {
        $(".checkBoxClass").prop('checked', $(this).prop('checked'));
        countChecked();
    });
        var countChecked = function() 
    {
        var n = $( ".checkBoxClass:checked" ).length;
        document.getElementById('hidd_count').value=n;
    };
    //select each checkbox
    $(document).on('click', '.checkBoxClass', function () 
    {
        countChecked();
    });
    var filter =   function()
    {   
        var obj = [];
        var search_option = $('#search_options_1').val();

        $( ".link_column_types" ).each(function( index ) {
            var from = '';
            var to = '';
            var value = '';
            var name = $(this).val();
            var l_ctr =$(this).attr('data-l_ctr');

            var type =  $('option:selected', this).attr('data-type');
            console.log(type+" <- type");
            if(type == 'time' || type == 'date')
            {
                from = $('#from_'+l_ctr).val();
                to   = $('#to_'+l_ctr).val();
            }
            else
            {
                value = $('#textbox_'+l_ctr).val();
            }
            
            
            if((value) || (from || to))
            {
                var ind = {'name':name,'type':type,'value':value,'from':from,'to':to,'search_option':search_option};
                obj.push(ind);
            }
        });
        
    return obj;
    };
    var load_search = function() 
    {
        var url_search = '{{url('appslistview')}}/'+'{{$id}}'+'/'+'{{$app}}'+'?search=search';
        window.location.replace(url_search);
    } 
    $('#search_records').click(function(){
        load_search();
    });
    var add_records = function() 
    {
        var url_add = '{{url('viewapp')}}?action=add&appid='+'{{$id}}';
        window.location.replace(url_add);
    } 
    $('#add_records').click(function(){
        add_records();
    });
    //datatable
    var id = '{{$id}}';
    var rows_per_page = '{{Session('settings_rows_per_page')}}';
    var lengthMenu = getLengthMenu();//Function in app.blade.php
    var tcolumn=[]
    tcolumn.push({"data":"action"});
    var order_col=0;
    @php foreach($heads as $value){ @endphp
      order_col++;
      tcolumn.push({"data":"{{$value->document_type_column_id}}"});
    @php } @endphp
    order_col += 3;
    tcolumn.push({"data":"updated_by"});
    tcolumn.push({"data":"created_at"});
    tcolumn.push({"data":"updated_at"});
    var datatable =   $('#documentTypeDT').DataTable( { 
    "fnInitComplete": function() {
            $('#documentTypeDT tbody tr').each(function(){
                    //$(this).find('td:eq(1)').attr('nowrap', 'nowrap');
                    //$(this).find('td:eq(2)').attr('nowrap', 'nowrap');
            });
        },
        "lengthMenu": lengthMenu,
        "pageLength":rows_per_page,       
        "processing": true, 
        "serverSide": true,
        "fixedHeader": true,
        "order": [[order_col,"desc"]],
        "responsive": true,
        "lengthChange":true,
        "colReorder": true,
        "scrollX": true,
        "scrollY": 350,
        "bFilter": true,
        "ajax": {
           "url": "@php echo url('my_apps_filter'); @endphp",
           "type": "POST",
            "headers": { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            "data": {'id': id},
            "data":function(d){
                //d.typeselect = typeselect();
                d.filter = filter();
                d.id = id;
             }
            },
        "iDisplayLength": 25,
        "columns":tcolumn,
        "columnDefs": [
          { targets: 'no-sort', orderable: false }
        ],  
        
        "language": { processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> ',"searchPlaceholder": "{{trans('language.data_tbl_search_placeholder')}}"  },                       
                               
        } );
    //for highlight the row
        $('#documentTypeDT tbody').on('click', 'tr', function () {
            $("#documentTypeDT tbody tr").removeClass('row_selected');        
            $(this).addClass('row_selected');
        });
    // delete
    $(document).on('click', '.delrow', function (event)
    {   
    
    var id=$(this).attr('data-id');
    var app_id=$(this).attr('app_id');
    var docname="app record";

    swal({
          title: "{{trans('language.confirm_delete_single')}}'" + docname + "' ?",
          text: "{{trans('language.Swal_not_revert')}}",
          type: "{{trans('language.Swal_warning')}}",
          showCancelButton: true
        }).then(function (result) {
        if(result)
        {
            // Success
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: "{{url('deleteSubmittedIndexvalue')}}",
                data: {_token: CSRF_TOKEN,id:id,app_id:app_id},
                timeout: 50000,
                beforeSend: function() {
                    $("#bs").show();
                },
                success: function(data, status)
                {
                    // success
                    if(data==1)
                    {
                        swal({
                        title: "Sumbmitted records of '"+docname+"' {{trans('language.success_delete')}}",
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ok'
                        }).then(function (result) {
                            if(result){
                                // Success
                                /*window.location.reload();*/
                            }
                        });
                    }
                    else
                    {
                        // data=0
                        swal("Submitted index field not found");
                    }
                    datatable.ajax.reload( null, false ); 
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(jqXHR);    
                    console.log(textStatus);    
                    console.log(errorThrown);    
                },
                complete: function() {
                    $("#bs").hide();
                }
            });
        }   
    });
     });
    var link_ctr=0;
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var index_options = '<option value="all" type="all"  data-type="all" type="all">All index fields</option>';
    var select_type ='';
    @php  foreach ($heads as $key => $value) : @endphp
    index_options +='<option value="{{@$value->document_type_column_name}}" data-type="{{$value->document_type_column_type}}" type="{{$value->document_type_column_type}}" options="{{ @$value->document_type_options}}">{{ucfirst(@$value->document_type_column_name)}}</option>';  
    @php endforeach @endphp
                        
    var selected_option=0;
    var doc_type_options = '<option value="">please select a document type</option>';
    var doc_type_index_options = '<option value="">please select a column</option>';
    var app_name='';
   
   var add_links = function(data_array) 
{
    $(".search_footer").show();  
    var l_ctr = data_array.link_ctr;
    var app_link_id = (typeof data_array.app_link_id != 'undefined') ? data_array.app_link_id:0;
    var app_column_id = (typeof data_array.app_column_id != 'undefined') ? data_array.app_column_id:'';
    var document_type_id = (typeof data_array.document_type_id != 'undefined') ? data_array.document_type_id:'';
    var document_column_id = (typeof data_array.document_column_id != 'undefined') ? data_array.document_column_id:'';

    var common_attr = 'data-l_ctr="'+l_ctr+'"';
   
    var newTr = '<div class="row link_tr_'+l_ctr+'">';
    newTr += '<div class="col-xs-12 col-sm-12 col-md-3">';
    newTr += '<div class="form-group">';
    if(l_ctr == 1)
        {
    newTr += '<label for="link_column_type_'+l_ctr+'">{{trans("apps.column_from_apps")}}</label>';
}
    newTr += '<select class="form-control link_column_types" name="link_column_type_'+l_ctr+'" id="link_column_type_'+l_ctr+'" ' + common_attr + ' data-parsley-required="true" data-parsley-required-message="{{ trans("apps.this_field_required") }}" >' + index_options + '</select>';
    
    if(l_ctr == 1)
        {
            /*newTr += '<p class="help_text">{{trans("apps.column_from_apps_help")}}</p>';*/
        }
    newTr += '</div>'; /*End Form group*/
    newTr += '</div>'; /*End Col*/

    newTr +='<div class="col-xs-12 col-sm-12 col-md-4  dynamicbox_'+l_ctr+' col_6_'+l_ctr+'" style="display:block;">';
    newTr +='1';
    newTr +='</div>';

    newTr +='<div class="col-xs-12 col-sm-12 col-md-2 dynamicbox_'+l_ctr+' col_3_one_'+l_ctr+'" style="display:block;">';
    newTr +='2';
    newTr +='</div>';

    newTr +='<div class="col-xs-12 col-sm-12 col-md-2 dynamicbox_'+l_ctr+' col_3_two_'+l_ctr+'" style="display:block;">';
    newTr +='3';
    newTr +='</div>';

    if(l_ctr == 1)
        {
            newTr += '<div class="col-xs-12 col-sm-6 col-md-3">';
            newTr += '<div class="form-group">';
            newTr += '<label for="link_column_type_'+l_ctr+'">{{trans("language.search_options")}}</label>';
            newTr += '<select class="form-control" name="search_options_'+l_ctr+'" id="search_options_'+l_ctr+'" ' + common_attr + ' data-parsley-required="true" data-parsley-required-message="{{ trans("apps.this_field_required") }}">';
            newTr += '<option value="or">{{trans("language.or")}}</option>';
            newTr += '<option value="and">{{trans("language.and")}}</option>';
            newTr += '</select>';
            newTr += '<p class="help_text">{!! trans("language.search_options_help") !!}</p>';
            newTr += '</div>'; /*End Form group*/
            newTr += '</div>'; /*End Col*/


            newTr += '<div class="col-xs-12 col-sm-6 col-md-2">';
            newTr += '<div class="form-group">';
            newTr += '<div style="margin-top:28px;">';
            newTr += '<input class="btn btn-primary addIndexSearch" id="save" type="submit" value="{{trans("apps.search_more_field")}}" >';
            newTr += '</div>'; /*End Form group*/
            newTr += '</div>'; /*End Col*/
        }
        else
        {
            newTr += '<div class="col-xs-1">';
            newTr += '<div class="form-group">';
            newTr += '<div style="">';
            newTr += '<i class="fa fa-fw fa-trash deleteCol" title="Remove Field" ' + common_attr + '></i><div>';
            newTr += '</div>'; /*End Form group*/
            newTr += '</div>'; /*End Col*/
        }

    

    newTr += '</div>'; /*End Row*/
    $('#link_row').append(newTr);

    $("#link_column_type_"+l_ctr).val(app_column_id).trigger("change");  
   /* $("#document_type_"+l_ctr).val(document_type_id).trigger("change");
    $("#column_type_option_"+l_ctr).val(document_column_id).trigger("change");*/
    
};

var add_more_click = function() 
{
    link_ctr++;
    var data_array = {'link_ctr':link_ctr,'app_column_id':'all'};
    add_links(data_array);
}
$(document).on('click', '.addIndexSearch', function (event) {
    
    add_more_click();
    
});

$(document).on('click', '.IndexSearch', function (event) {
    event.preventDefault();
    if(link_ctr == 0)
    {
        add_more_click();

    }
    
});

//Delete selected
$("#deletetag").click(function()
{
    if(document.getElementById('hidd_count').value==0)
    {
        swal("{{trans("apps.not_select")}}");
        return false;
    }
    else
    {   
        swal({
            title: "{{trans("apps.confirm_delete_multiple")}}",
            type: "{{$language['Swal_warning']}}",
            showCancelButton: true
        }).then(function (result) {
        if(result)
        {
            // Success
            var arr = $('input:checkbox.checkBoxClass').filter(':checked').map(function () {
            return this.value;
            }).get();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: '{{URL("multipleAppDelete")}}',
                data: {_token: CSRF_TOKEN,selected:arr},
                timeout: 50000,
                beforeSend: function() {
                    $("#bs").show();
                },
                success: function(data){
                    swal(data);
                    window.location.reload();
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(jqXHR);    
                    console.log(textStatus);    
                    console.log(errorThrown);    
                },
                complete: function() {
                    $("#bs").hide();
                }
            });
        }
        });
    }   
});

$(document).on('change', '.link_column_types', function (event) {
    
   var data_l_ctr =$(this).attr('data-l_ctr');
   var data_type = $(this).find(':selected').attr('data-type');
   var options = $(this).find(':selected').attr('options');
   var common_attr = 'data-l_ctr="'+data_l_ctr+'"';
   console.log(data_type);
   var html='';
   $('.dynamicbox_'+data_l_ctr).hide();
    switch (data_type) 
    {
        case 'select':
        case 'radio':
        case 'checkbox':
        html +='<div class="form-group">';
        if(data_l_ctr == 1)
        {
            html +='<label for="link_column_type_'+data_l_ctr+'" id="label_keywd_'+data_l_ctr+'">{{trans("language.select")}}</label>';
        }
        
        html += '<select class="input form-control" name="selectbox_'+data_l_ctr+'" id="selectbox_'+data_l_ctr+'" '+common_attr+'>';
        if(options)
        {
            var opt = options.split(',');
            //console.log(opt);
            $.each( opt, function( key, value ) {
              //alert( key + ": " + value );
              html +='<option value="'+value+'">'+value+'</option>';
            });
            
        }
        html += '<select>';
        if(data_l_ctr == 1)
        {
            /*html +='<p class="help_text">{{trans("language.select_for_search")}}</p>';*/
        }
        html +='</div>';
        $('.col_6_'+data_l_ctr).show();
        $('.col_6_'+data_l_ctr).html(html);
        console.log(html);
        break;
        case 'date':
        html +='<div class="form-group">';
        if(data_l_ctr == 1)
        {
            html +='<label for="link_column_type_'+data_l_ctr+'" id="label_keywd_'+data_l_ctr+'">{{trans("language.date_from")}}</label>';
        }
        
        
        html +='<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i></div>';
        html +='<input type="text" class="form-control datetime" name="from_'+data_l_ctr+'" id="from_'+data_l_ctr+'" '+common_attr+' placeholder="{{trans("language.date_from")}}" value="">';
        html +='</div>';
        if(data_l_ctr == 1)
        {
            /*html +='<p class="help_text">{{trans("language.date_from_help")}}</p>';*/
        }
        html +='</div>';

        html2 ='<div class="form-group">';
        if(data_l_ctr == 1)
        {
            html2 +='<label for="link_column_type_'+data_l_ctr+'" id="label_keywd_'+data_l_ctr+'">{{trans("language.date_to")}}</label>';
        }
        
        
        html2 +='<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i></div>';
        html2 +='<input type="text" class="form-control datetime" name="to_'+data_l_ctr+'" id="to_'+data_l_ctr+'" '+common_attr+' placeholder="{{trans("language.date_to")}}" value="">';
        html2 +='</div>';
        if(data_l_ctr == 1)
        {
            /*html2 +='<p class="help_text">{{trans("language.date_to_help")}}</p>';*/
        }
        html2 +='</div>';
        $('.col_3_one_'+data_l_ctr).show();
        $('.col_3_two_'+data_l_ctr).show();

        $('.col_3_one_'+data_l_ctr).html(html);
        $('.col_3_two_'+data_l_ctr).html(html2);

        $('#from_'+data_l_ctr).daterangepicker({
            singleDatePicker: true,
            "drops": "bottom",
            showDropdowns: true
        });


        $('#to_'+data_l_ctr).daterangepicker({
            singleDatePicker: true,
            "drops": "bottom",
            showDropdowns: true
        });

        break;

        case 'time':
        html +='<div class="form-group">';
        if(data_l_ctr == 1)
        {
        html +='<label for="link_column_type_'+data_l_ctr+'" id="label_keywd_'+data_l_ctr+'">{{trans("language.time_from")}}</label>';
        }
        
        html +='<input type="time" class="form-control datetime" name="from_'+data_l_ctr+'" id="from_'+data_l_ctr+'" '+common_attr+' placeholder="{{trans("language.time_from")}}" value="">';
         if(data_l_ctr == 1)
        {
            /*html +='<p class="help_text">{{trans("language.time_from_help")}}</p>';*/
        }
        html +='</div>';

        html2 ='<div class="form-group">';
        if(data_l_ctr == 1)
        {
            html2 +='<label for="link_column_type_'+data_l_ctr+'" id="label_keywd_'+data_l_ctr+'">{{trans("language.time_to")}}</label>';
        }
        
        html2 +='<input type="time" class="form-control datetime" name="to_'+data_l_ctr+'" id="to_'+data_l_ctr+'" '+common_attr+' placeholder="{{trans("language.time_to")}}" value="">';
        if(data_l_ctr == 1)
        {
            /*html2 +='<p class="help_text">{{trans("language.time_to_help")}}</p>';*/
        }
        html2 +='</div>';
        $('.col_3_one_'+data_l_ctr).show();
        $('.col_3_two_'+data_l_ctr).show();

        $('.col_3_one_'+data_l_ctr).html(html);
        $('.col_3_two_'+data_l_ctr).html(html2);

        /*$('#from_'+data_l_ctr).daterangepicker({
            timePicker : true,
            timePicker24Hour : true,
            timePickerIncrement : 1,
            timePickerSeconds : false,
            locale : {
                format : 'HH:mm'
            }
        }).on('show.daterangepicker', function(ev, picker) {
            picker.container.find(".calendar-table").hide();
        });

        $('#to_'+data_l_ctr).daterangepicker({
            timePicker : true,
            DatePicker:false,
            timePicker24Hour : true,
            timePickerIncrement : 1,
            timePickerSeconds : false,
            locale : {
                format : 'HH:mm'
            }
        }).on('show.daterangepicker', function(ev, picker) {
            picker.container.find(".calendar-table").hide();
        });*/
        
        break;
        default:
        if(data_type == 'number')
        {
            var input_type ='number';
        }
        else
        {
            var input_type ='text';
        }
        html +='<div class="form-group">';
        if(data_l_ctr == 1)
        {
            html +='<label for="link_column_type_'+data_l_ctr+'" id="label_keywd_'+data_l_ctr+'">{{trans("apps.keyword")}}</label>';
        }    
        
        html +='<input type="'+input_type+'" class="input form-control" name="textbox_'+data_l_ctr+'" id="textbox_'+data_l_ctr+'" '+common_attr+' placeholder="{{trans("apps.keyword")}}">';
         if(data_l_ctr == 1)
        {
            /*html +='<p class="help_text">Search the keyword</p>';*/
        }
        html +='</div>';
        $('.col_6_'+data_l_ctr).show();
        $('.col_6_'+data_l_ctr).html(html);
        console.log('.col_6_'+data_l_ctr);
        console.log(html);
        break;
    }
    
});
    /* Delete Column */
    $(document).on("click",".deleteCol",function(e) {
       
       var ditem =$(this).attr('data-l_ctr'); 

       swal({
        title:"{{trans('language.confirm_delete')}}",
        showCancelButton: true
        }).then((result) => {
        if(result){
         
          $(".link_tr_"+ditem).remove();
        }
        else
        {
          //stay in same stage
        }
     });
     });
/* END Delete Column */
    
$(document).on("click","#search_index",function(e) {
    datatable.ajax.reload();
 });


@php if($search){ @endphp

add_more_click();
$("#IndexSearch").hide();
@php } @endphp

/* Link to doc Code*/

var load_app_doc = function(app_id,doc_id) 
      {
      var url = "@php echo url('load_app'); @endphp";
      url +="?form_id="+app_id+"&doc_id="+doc_id+"&action=view";
      var html = '';
      $.ajax({
            type: "GET",
            url: url,
            dataType:'JSON',
            success: function(response)
            {
                var newTh   = '<tr>';
                var newTr   = '<tr>';
                $.each(response.inputs, function(i, item) {
                    console.log(item);    
                    newTh   += '<th>'+item.label+'</th>';

                    newTr   += '<td>'+item.selected+'</td>'; 
                  });
                newTh   += '</tr>';

                newTr   += '</tr>';;
                //$("#app_section").html(html); 
                console.log(newTh);
                console.log(newTr);
                
                $("#app_info thead").html(newTh);
                $("#app_info tbody").html(newTr);
            }
        });
    }
    //call function for load apps
    //load_app_doc();


var processing_spinner='<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>';
var tcolumns=[];
var doctype_options=[];
@php foreach($doctypeApp as $key => $row){ 
$index_filed = (isset($index_fileds[$row->document_type_id]))?$index_fileds[$row->document_type_id]:array();
$loop=0;
@endphp
var tcolumn=[];
var doctype_option='';
var loop = 0;
/*col START*/
tcolumn.push({"data":"actions","title":"<input type='checkbox' id='link_ckbCheckAll' data-orderable='false'></input> {{trans('language.actions')}}","class":"{{ $loop }} no-sort"});
doctype_option +="<option value='search_all' data-column_type='' data-dynamic_col='0'>{{trans('language.search_all')}}</option>";    
/*col END*/

/*col START*/
var department_name ="@if(Session::get('settings_department_name')){{ Session::get('settings_department_name') }} @else {{trans('language.department')}} @endif";

tcolumn.push({"data":"department_id","title":department_name,"class":"{{ ++$loop }}"});

doctype_option +="<option value='department' data-column_type='' data-dynamic_col='0'>"+department_name+"</option>";

/*col END*/

/*col START*/
var stack_name = "{{trans('language.Stack')}}";
tcolumn.push({"data":"stack_id","title":stack_name,"class":"{{ ++$loop }}"});
 doctype_option +="<option value='filename' data-column_type='' data-dynamic_col='0'>"+stack_name+"</option>";

/*col END*/

/*col START*/
var document_no_label ="{{$row->document_type_column_no}}";
tcolumn.push({"data":"document_no","title":document_no_label,"class":"{{ ++$loop }}"});
doctype_option +="<option value='document_no' data-column_type='' data-dynamic_col='0'>"+document_no_label+"</option>";

/*col END*/

/*col START*/
var document_name_label ="{{$row->document_type_column_name}}";
tcolumn.push({"data":"document_name","title":document_name_label,"class":"{{ ++$loop }}"});
 doctype_option +="<option value='document_name' data-column_type='' data-dynamic_col='0'>"+document_name_label+"</option>"; 

/*col END*/

/*col START*/
@php 
        if(count($index_filed)){
            foreach($index_filed as $value){ @endphp

tcolumn.push({"data":"{{$value->document_type_column_id}}","title":"{{$value->document_type_column_name}}", "class":"{{ ++$loop }} "}); 
                
doctype_option +="<option value='{{$value->document_type_column_id}}' data-column_type='{{$value->document_type_column_type}}' data-dynamic_col='1'>{{$value->document_type_column_name}}</option>";      
            @php } } 
        @endphp

/*col END*/

/*col START*/

/*col END*/

/*col START*/

/*col END*/

tcolumns["{{$row->document_type_id}}"] = tcolumn;
doctype_options["{{$row->document_type_id}}"] = doctype_option;
var  link_doc_id =0;
@php } /*end foreach */@endphp

  var typeselect =   function()
    {   
        var obj = $('#typeselect').val();
        return obj;
    };

    var linked_doc_only =   function()
    {   
        var obj = $('#linked_doc').val();
        return obj;
    };

    var linkdocid =   function()
    {   
        var val = $('#link_doc_id').val();
        return val;
    };

    var search_text =   function()
    {   
        var obj = $('#search_text').val();
        return obj;
    };

     var search_column_data =   function()
    {   
        var column_type = $('#search_column').find(':selected').attr('data-column_type');
        var search_column_type =  (typeof column_type === "undefined")?"":column_type;
        var dynamic_col = $('#search_column').find(':selected').attr('data-dynamic_col');
        var search_dynamic_col =  (typeof dynamic_col === "undefined")?"0":dynamic_col;
        console.log("search_column_type = "+search_column_type);
        var obj = {'search_column':$('#search_column').val(),'search_column_type':search_column_type,'search_dynamic_col':search_dynamic_col};
        return obj;
    };    

 var search_column_history = "@php echo (isset($search_column))?$search_column:''; @endphp"
        var link_dtshow = function(){
            if ( $.fn.DataTable.isDataTable('#link_to_docDT')) 
            {
              console.log("destroy DataTable");
              $('#link_to_docDT').DataTable().destroy();
            }

            $('#link_to_docDT').empty();
            var value = $("#typeselect").val();
            $('#search_column').html(doctype_options[value]);
             
            var sort_order = "@php echo (isset($sort_order))?$sort_order:1; @endphp";
            var sort_direct = "@php echo (isset($sort_direct))?$sort_direct:'desc'; @endphp";
            
            var res_datatable = $('#link_to_docDT').DataTable( {
                "processing": true,
                "serverSide": true,
                "order": [[sort_order,sort_direct]],
                "lengthMenu": lengthMenu,
                "responsive": true,
                "destroy": true,
                "pageLength": rows_per_page,
                "displayStart": "@php echo (isset($displayStart))?$displayStart:0; @endphp",
                "colReorder": true,
                "scrollX": true,
                "scrollY": 350,
                "bFilter": true,
                /*"search": {"search": "@php echo (isset($search_text))?$search_text:''; @endphp"},*/
                "searching": false,
                "ajax": 
                {
                    "url": "@php echo url('link_to_doc_filter'); @endphp",
                    "type": "POST",
                    "headers": { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    "data":function(d)
                    {
                        //tcolumn.push({"data":"headsocument_status"});
                        d.type = typeselect();
                        d.link_doc_id = $('#link_doc_id').val();
                        d.search_text = search_text();
                        d.search_column_data = search_column_data();
                        d.linked_doc_only = linked_doc_only();
                        /*d.filter = filter();
                        d.view = '{{Session::get('view')}}';
                        d.id = "{{@$sel_item_id}}";
                        d.docid = $("#hidd-doc-id").val();
                        d.search_text = search_text();
                        d.search_column_data = search_column_data();*/
                    }
                },
                "columns": tcolumns[value],            
                "columnDefs": [{ "targets": 0, "orderable": false }],
                "language": { processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span> ',"searchPlaceholder": "{{trans('language.data_tbl_search_placeholder')}}"  },
                "rowCallback": function( row, data ) {
                    
                    if(data['document_id']==opendoc)
                    {
                        $(row).addClass('row_selected');
                    }
                   // console.log(data);
                    

                },

            });
            //alert disable
            $.fn.dataTable.ext.errMode = 'none';

            //disable auto search of datatable
            $("div.dataTables_filter input").unbind();

            return res_datatable;
        };
        var link_datatable = '';    
console.log("--------tcolumns-----------");
console.log(tcolumns);

console.log("--------doctype_options-----------");
console.log(doctype_options);
$(document).on('click', '.link_to_doc', function (e) 
    {
        e.preventDefault();
        link_doc_id = $(this).attr('data-doc_id');
        var app_id = "{{$app_id}}";
        $('#link_doc_id').val(link_doc_id);
        $(".checkBoxClass").prop('checked', false);
        $("#chk"+link_doc_id).prop('checked', true);
         $('#link_to_doc_modal').modal().show();
         load_app_doc(app_id,link_doc_id)
         link_datatable = link_dtshow();
    });

 
    $(document).on('click', '#link_ckbCheckAll',function () 
    {
        $(".link_checkBoxClass").prop('checked', $(this).prop('checked'));
        countChecked();
    });

$('#typeselect').change(function(){
        link_datatable = link_dtshow();
    });
$('#linked_doc').change(function(){
        link_datatable = link_dtshow();
    });

        $(document).on('click', '.save_link', function (e) 
    {
        e.preventDefault();
        
        
        swal({
              title: "{{trans('language.Swal_are_you_sure')}}",
              text: "{{trans('language.Swal_not_revert')}}",
              type: "{{trans('language.Swal_warning')}}",
              showCancelButton: true
            }).then(function (result) {
                if(result){
                    linkdocid = $('#link_doc_id').val();
                    //$(".processing_spinner_modal").html(processing_spinner);
                    var arr = $('input:checkbox.link_checkBoxClass').filter(':checked').map(function () {
                                        return this.value;
                                        }).get();
                            
                    var uncheked_arr = $('input:checkbox.link_checkBoxClass').filter(':not(:checked)').map(function () {
                                        return this.value;
                                        }).get();
                    
                    var published_count = arr.length;
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    
                    $.ajax({
                    type: 'post',
                    dataType:'json',
                    url: "@php echo url('link_to_doc_save'); @endphp",
                    data: {'_token': CSRF_TOKEN,'selected':arr,'link_doc_id':linkdocid,'uncheked_arr':uncheked_arr},
                    timeout: 50000,
                    beforeSend: function() {
                   // show_bar();
                    },
                    success: function(data)
                    {
                       swal(data.message);
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        console.log(jqXHR);    
                        console.log(textStatus);    
                        console.log(errorThrown);    
                    },
                    complete: function() {
                        /*$("#bs").hide();*/
                        //hide_bar();
                    }
                    });
                }
            });
        

    });

    $(document).on('click', '.docsrch', function ()
    {
        var value = $('#search_text').val();
        console.log(value); // <-- the value
        link_datatable.ajax.reload();    
    });  

    $(document).on('click', '.duplicate_to_doc', function (e) 
    {
        e.preventDefault();
        link_doc_id = $(this).attr('data-doc_id');
        var app_id = "{{$app_id}}";
        $('#duplicate_doc_id').val(link_doc_id);
        $('#duplicate_app_id').val(app_id);
        $(".checkBoxClass").prop('checked', false);
        $("#chk"+link_doc_id).prop('checked', true);
        
        $('#dynamic_from').html('');
        $('#duplicate_doc_modal').modal().show();

  var formID = "{{@$app_id}}";
  var loadformurl = "{{ url('load_app')}}";
  var action = 'edit';
  var doc_id = link_doc_id;
  var url_delete = "<?php echo URL('deleteAppAttached');?>";
  var url_delete = "<?php echo URL('deleteAppAttached');?>";
  var url_form_attach = "<?php echo URL('formAttachments');?>";
  /*generateFormEdit(formID,loadformurl,action,doc_id,url_delete,url_form_attach);*/
  var form_array = {'formID':formID,'url':loadformurl,'action':action,'doc_id':doc_id,'url_delete':url_delete,'url_form_attach':url_form_attach,'col':'col_3'};
    generateForm(form_array);
        
    });  
});


</script>  
@endsection