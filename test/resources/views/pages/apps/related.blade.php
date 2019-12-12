<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
{!! Html::style('css/jquery-ui.css') !!} 

{!! Html::script('js/parsley.min.js') !!} 
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables_new/jquery.dataTables.min.css') !!}
{!! Html::style('plugins/datatables_new/colReorder.dataTables.min.css') !!}

<style type="text/css">

 
</style>
<!-- Satrt Section -->
<section class="content-header">
  <div class="row">
    <div class="col-sm-8">
      <span style="float:left;">
        <strong>{{ trans('apps.app') }} -  <small class="app_name"></small> </strong> &nbsp;
      </span>
      <span style="float:left;">
        <?php
        $user_permission=Auth::user()->user_permission;                       
        ?> &nbsp;
         <a href="{{url('appslistview')}}/{{$app_id}}?app_saved_search=1">
                            <button class="btn btn-info btn-flat newbtn" style="line-height:13px !important;">{{ trans('language.back') }}</button>
                        </a> 
                         &nbsp;
       
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
<section class="content">


        
<div class="row">
  @if(Session::has('data'))
<div class="col-sm-12 alert_space" id="spl-wrn">
    <div class="alert alert-sty {{ Session::get('alert-class', 'alert-success') }} ">{{ Session::get('data') }}</div>        
</div>
@endif

 <div class="col-xs-12">
        
        <div class="box box-info">
            
            <div class="box-body" style="overflow-x: auto; overflow-y: auto;">
             <table border="1" id="app_info" class="table table-bordered table-striped dataTable hover" width="100%" style="border-collapse: collapse;">
                     <thead></thead>
                    <tbody></tbody>                     
                </table>   
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>


     <div class="col-xs-12">
        
        <div class="box box-info">
            <!--- START TABS -->
            <div class="box-body" style="overflow-x: auto; overflow-y: auto;">
            <div id="tabs">
            <ul>
              <!-- <li><a href="#tabs-1" style="color: #333 !important;">Related Documents</a></li> -->
              <li><a href="#tabs-2" style="color: #333 !important;">Previous Version</a></li>
            </ul>
            <!-- <div id="tabs-1">
              <p>Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</p>
            </div> -->
            <div id="tabs-2">
              
              <div class="row">
                <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;overflow-x: auto; overflow-y: auto;">
              <table border="1" id="previous_versions" class="table table-bordered table-striped dataTable hover" width="100%" style="border-collapse: collapse;">
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
                    <tbody></tbody>                     
                </table>   
              </div>
            </div>

            </div>
           
          </div>
          <!--- END TABS -->

            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
 
    
</div>
<!-- /.row -->  
    </section>
    <!-- END Section -->


{!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}
{!! Html::script('plugins/datatables_new/dataTables.colReorder.min.js') !!}
{!! Html::script('plugins/datatables_new/jquery.columntoggle.js') !!}
{!! Html::script('plugins/datatables_new/dataTables.fixedHeader.min.js') !!}
{!! Html::script('js/jquery-ui.min.js') !!}  
<script type="text/javascript">
$(document).ready(function(){
  $( "#tabs" ).tabs();
  $('#spl-wrn').delay(10000).slideUp('slow');
  var doctype_options=[];
  var formID = "{{@$app_id}}";
  var loadformurl = "{{ url('load_app')}}";
  var action = 'edit';

  var load_app_doc = function() 
      {
      var url = "@php echo url('load_app?form_id='.$app_id.'&doc_id='.$doc_id.'&action=view'); @endphp";
      var html = '';
      $.ajax({
            type: "GET",
            url: url,
            dataType:'JSON',
            success: function(response)
            {
                console.log(response);
                $('#dc_types tbody').html('');
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

                /*ß*/
                
            }
        });
    }
   load_app_doc();

   var processing_spinner='<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>';
    var id = '{{$doc_id}}';
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

    var datatable =   $('#previous_versions').DataTable( { 
    "fnInitComplete": function() {
            $('#previous_versions tbody tr').each(function(){
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
           "url": "@php echo url('previous_versions_filter'); @endphp",
           "type": "POST",
            "headers": { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            "data": {'id': id},
            "data":function(d){
                //d.typeselect = typeselect();
                d.app_id = '{{$app_id}}';;
                d.doc_id = '{{$doc_id}}';;
             }
            },
        "iDisplayLength": 25,
        "columns":tcolumn,
        "columnDefs": [
          { targets: 'no-sort', orderable: false }
        ],  
        
        "language": { processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> ',"searchPlaceholder": "{{trans('language.data_tbl_search_placeholder')}}"  },                       
                               
        } );

 // delete
    $(document).on('click', '.delrow1', function (event)
    {   
    
    var id=$(this).attr('data-id');
    var reference_id=$(this).attr('data-duplicate_ref_id');
    var app_id=$(this).attr('app_id');
    var docname="app record";
console.log("reference_id"+reference_id);
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
                data: {_token: CSRF_TOKEN,id:id,app_id:app_id,reference_id:reference_id},
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

 });   
</script>
@endsection