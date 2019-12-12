<?php include (public_path()."/storage/includes/lang1.en.php" ); ?>
@extends('layouts.app')
@section('main_content')
{!! Html::script('js/parsley.min.js') !!}    
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables/dataTables.bootstrap.css') !!}

<style>
.pagination {
    display: inline-block;
    padding-left: 0;
    margin: 0px !important;
    border-radius: 4px;
}
.tooltip-item {
  position: relative;
  cursor: pointer;
}

.tooltip-item:after {
  content: attr(data-message);
  position: absolute;
  white-space: nowrap;
  display: none;
  font-size: 1.2rem;
  background-color: #232323;
  padding-left: 10px;
    padding-right: 10px;
    padding-top: 5px;
    padding-bottom: 5px;
    border-radius: 3px;
  color: #ffffff;
  font-family: Tahoma, Verdana, Segoe, sans-serif;
  font-weight: normal;

}

.progress{
    color: #29bcba;
}

.connectedSortable_doc
{
min-height:75px;
min-width:300px;
/*max-width:400px;*/
border-bottom: 2px solid #f4f4f4;
}
.connectedSortable_activity
{
/*min-height:75px;*/
min-width:300px;

}   

.connectedSortable_doc > li
{
      border-bottom: 2px solid;
      margin-bottom: 3px !important;
}

 .connectedSortable_doc > li:last-child
{
      border-bottom: none;
      margin-bottom: 0px !important;
}   

.li_activity {
/*width:300px !important;*/
}
.li_activity .tools {
display: none;
color: #97a0b3;
}

.nopadding_class
{
padding: 0 !important;
margin: 0 !important;
}

.tool_activity a{    color: #97a0b3 !important; }

            .card {
  min-width: 100%;
  display: flex;
  overflow-x: auto;
            }
             .panner {
    /*border:1px dotted #00c0ef;*/
    display:block;
    top:50%;
    padding: 5px 2px !important;
    margin: 0 !important;
    color: #d2d6de;
    cursor: pointer;
    background-color: rgba(0,0,0,0.7);   
    color: #fff;    
}
.card::-webkit-scrollbar {
  display: none;
}
#panLeft {
    float: left;
    
}
#panRight {
    right:0px;
    float: right;
}
.wf_header {
   /* position: fixed;
    z-index:2;*/
}

.panner:hover { 
    background-color: #00c0ef;
    color: #fff; 
    /*border:1px dotted #d2d6de;*/
}

</style>

<?php 
    $user_id = Auth::user()->id;
    $dept_id = Auth::user()->department_id;
    $user_role = Auth::user()->user_role;
    $wf_permission = Auth::user()->user_workflow_permission;
?>

<section class="content-header">
    <div class="row">
        <div class="col-sm-4">
            <span style="float:left;">
                <strong>
                    {{trans('language.workflows')}}
                </strong> &nbsp;
            </span>
               
        </div>
      <div class="col-sm-4" style="font-size:12px;">    
        <!-- {{ $language['color_legend'] }} &nbsp;
        <button type="button" style="margin-left: 10px; cursor: context-menu; padding: 1px 12px !important; " class="btn btn-danger"></button>&nbsp;{{$language['no_action_taken']}} -->
      </div>
      <div class="col-sm-4">
         <!-- <ol class="breadcrumb">
            <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
            <li class="active">{{trans('language.workflows')}}</li>
         </ol> -->
      </div>
   </div>
</section>
<div id="wfpdeleteDiv">
</div>

<section class="content">
   <div class="row">
    @if(Session::get('enbval4')==Session::get('tval'))    
    @if(stristr($wf_permission,"view"))
   
      <div class="col-lg-3">
         <div class="form-group">
            <label for="inputEmail3" class="control-label">{{trans('workflows.select_workflow')}}</label>
            <select name="workflow_id" id="workflow_id" class="form-control workflow_load">
               <option value="0">{{ trans('workflows.select_workflow') }}</option>
               @foreach ($workflows as $wf_row)  
               <option value="{{$wf_row->workflow_id}}" @if($wf_row->workflow_id == $workflow_id) selected="selected" @endif>{{$wf_row->workflow_name}}</option>
               @endforeach
            </select>
         </div>
      </div>     
          
      <div class="col-lg-12">
        <div class="box box-info">
         <div class="row card" id="nav_stages">
         </div>
         <?php if($total_pages >1){?>
         <div align="center">
          <ul class='pagination text-center' id="pagination">
          <?php if(!empty($total_pages)):for($i=1; $i<=$total_pages; $i++):  
           if($i == 1):?>
                      <li class='active'  id="<?php echo $i;?>"><a style="cursor: pointer;"><?php echo $i;?></a></li> 
           <?php else:?>
           <li id="<?php echo $i;?>"><a style="cursor: pointer;"><?php echo $i;?></a></li>
           <?php endif;?> 
          <?php endfor;endif;?>  
          </ul>
          </div>
          <?php } ?>
        </div>
      </div>
      
      <div class="modal fade" id="activity_modal">
         <div class="modal-dialog">
            <div class="modal-content form_response_remote">
               
            <div class="modal-footer"></div>
            </div>
            <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
      </div>

     


      <div class="modal fade" id="add_to_workflow_modal_new">
         <div class="modal-dialog">
            <div class="modal-content add_workflow_remote">
               
            <div class="modal-footer"></div>
            </div>
            <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
    
<div class="modal-container-child"></div> 
      <div class="modal-container"></div>
      @else
    <section class="content content-sty">        
        <div class="alert alert-sty alert-error">{{trans('language.dont_hav_permission')}}</div>        
    </section>
@endif
@elseif(Session::get('enbval4')==Session::get('fval'))
                    <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{trans('language.purchase_now')}}</div></section>
                @else
                    <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{trans('language.module_expired')}}</div></section>
                @endif

   </div>
</section>



<!-- User edit form end -->
<!-- {!! Html::script('plugins/jQueryUI/jquery-ui.min.js') !!} -->
{!! Html::script('js/jquery-ui.min.js') !!}
{!! Html::script('plugins/select2/select2.full.min.js') !!}
<script>
   $(function ($) {
    var pageNum = 1;
   //$('[data-toggle="tooltip"]').tooltip({placement: "mouse"})

    $('#panRight').click(function(event) {
      event.preventDefault();
      $('#nav_stages').animate({
        scrollLeft: "+=200px"
      }, "slow");
   });
   
     $('#panLeft').click(function(event) {
      event.preventDefault();
      $('#nav_stages').animate({
        scrollLeft: "-=200px"
      }, "slow");
   });
   //pagination
   $("#pagination li").click(function(e){
    $("#pagination li").removeClass('active');
    $(this).addClass('active');
       pageNum = this.id;
        get_workflow_stages();
    });
   var today = "@php echo date('d-m-Y'); @endphp";  
   
   var show_work_flow = function(response) 
   {
   var rhtml = ''; 
   if(response.status == 1)
             {
             var rhtml =   response.html;
           }
           $("#nav_stages").html(rhtml);
   };  
   
   var get_workflow_stages = function() {
    console.log($('#workflow_id').val());
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
         $.ajax({
           data:{_token:CSRF_TOKEN,page:pageNum,workflow_id:$('#workflow_id').val(),workflow_doc_type:$('#workflow_doc_type').val(),object_type:$('#workflw_object_type').val(),object_id:$('#workflow_doc_id').val()},
           type: 'POST',
           dataType:'json',
           url: '{{URL('workflows_stages')}}',
           success:function(response)
           {

             //$('[data-toggle="tooltip"]').tooltip({placement: "mouse"});  // Call function again for AJAX loaded content
             console.log(response);
             show_work_flow(response);             
           },
           error: function(jqXHR, textStatus, errorThrown) 
           {
               console.log(jqXHR.status);
           }
       });
   }

   var get_workflow_docs = function() {
    
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
         $.ajax({
           data:{_token:CSRF_TOKEN,workflow_id:$('#workflow_id').val(),object_type:$('#workflw_object_type').val()},
           type: 'POST',
           dataType:'json',
           url: '{{URL("get_workflow_docs")}}',
           success:function(response)
           {
             console.log(response);
              if(response.wf_docs != null || response.wf_forms != null)
               {
               
               var wf_docs_option = "<option value=''>{{trans('language.select_document')}}</option>";
               $.each(response.wf_docs, function( key, value ) 
               {
                  wf_docs_option +='<option value="'+value.document_id+'">'+value.document_name+'</option>';
                }); 

                $.each(response.wf_forms, function( key, value ) 
               {
                  wf_docs_option +='<option value="'+value.id+'">'+value.name+'-'+value.user_full_name+'-'+value.date+'</option>';
                });    
               $("#workflow_doc_id").html(wf_docs_option);
               }
             
           },
           error: function(jqXHR, textStatus, errorThrown) 
           {
               console.log(jqXHR.status);
           }
       });
   }
    
   get_workflow_stages();

    $('.workflow_load').on('change', function() {
    
   get_workflow_stages();
   });
   
   $('.workflow_doc_change').on('change', function() {

      $('.workflow_doc_id').each(function (index, value) { 
         $(this).val($('#workflow_doc_id').val()); 
   });
   get_workflow_stages();

   });
  
   var loading_src = "{{ URL::asset('images/loading/loading.gif') }}";
   var loading_text ='<div  style="text-align: center;"><img src="'+loading_src+'"></div>';
   $(document).on("click",".add_activity",function(e){
    e.preventDefault();
    var activity = $(this).attr("data-activity");
    var objectid = $(this).attr("data-objectid");
    var objecttype = $(this).attr("data-objecttype");
    var stageid = $(this).attr("data-stageid");
    var action = $(this).attr("data-action");
    var workflow_id= $("#workflow_id").val();
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var post_data = {_token: CSRF_TOKEN,"activity":activity,"objectid":objectid,"objecttype":objecttype,"workflow_id":workflow_id,"stageid":stageid,"action":action};
     show_Activity_details(post_data);
   });  

   var show_Activity_details = function(post_data) 
   {
    $('#activity_modal .form_response_remote').html(loading_text);
    $('#activity_modal').modal({
                     show: 'show',
                     backdrop: false
               }); 
    
    $.ajax({
            type: 'POST',
            url: "{{URL('load_activity_form')}}",
            dataType: 'json',
            data: post_data,
            timeout: 50000,
            success: function(data)
            {
                $('.form_response_remote').html(data.html);

            }
        });
   };
@php
    if(isset($activity_view) && $activity_view)
    {
    @endphp
      var workflow_id= $("#workflow_id").val();    
      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var obj = {_token: CSRF_TOKEN,activity: "@php echo $activity_view; @endphp","workflow_id":workflow_id,"action":"view"};
        show_Activity_details(obj);
    @php
    }
@endphp 

$(document).on("click",".save_action_workflows",function(e) {
   e.preventDefault();
   var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
   var workflow_id = $('#wf_action_id').val();
   var activity= $('#wf_action_activity_id').val();
   var activity_id= $('#action_activity_id').val();
   var activity_name= $('#action_activity_id').find('option:selected').text();
   var activity_note= $('#action_activity_note').val();
   var last_activity_check = $('#action_activity_id').find('option:selected').attr('last_activity');
   var stage_order = $('#hidd_stage_order').val();
   var stage_count = $('#stage_count').val();
   if($("#workflow_action_form").parsley().validate())
   {
    if(last_activity_check == 1)
    {
      swal({
        title:"{{trans('language.last_activity_wf_next_stage')}}",
        showCancelButton: true
        }).then((result) => {
        if(result){
          var url = "@php echo url('change_workflow_stage'); @endphp";
            $.ajax({
              type: "POST",
              url: url,
              dataType:'json',
              data: {_token: CSRF_TOKEN,
                "wf_id":$("#hidd_wf_id").val(),
                "wf_stage_id":$("#hidd_wf_stage_id").val(),
                "wf_object_id":$("#hidd_wf_object_id").val(),
                "wf_object_type":$("#hidd_wf_object_type").val(),
                "current_stage_order":stage_order
              },
              success: function(response)
              {
                  if(response.status == 1)
                  {
                   show_work_flow(response);
                   $('#change_stage_modal').modal('hide');
                  }
                  
              }
            });
        }
        else
        {
          //stay in same stage
        }
     });
    }
    if((stage_order == stage_count) && (last_activity_check == 1))
    {
      var exit = "@php echo url('workflow_exit'); @endphp?wf="+$("#hidd_wf_id").val()+"&objecttype="+$("#hidd_wf_object_type").val()+"&objectid="+$("#hidd_wf_object_id").val();
      swal({
        title:"{{trans('language.last_activity_wf_exit')}}",
        showCancelButton: true
        }).then((result) => {
                           if(result){
                            window.location.href= exit;
                           }
                           else{
                             //stay in workflow last stage
                           }
                         });
    }

   $.ajax({
            type: 'POST',
            url: "{{URL('save_action_workflow')}}",
            data: {_token:CSRF_TOKEN,workflow_id: workflow_id,activity_id: activity_id,activity_name: activity_name,activity_note: activity_note,activity: activity},
            dataType: 'json',
            timeout: 50000,
            success: function(data)
            {
                if(data.html !='')
                {
                  $('.form_response_remote').html(data.html);
                }
                if(data.message !='')
                {
                   $('.alert_form').html(data.message);
                }
                get_workflow_stages();
            }
        });
   }
   });
  
  
    $(document).on("click",".change_stage",function() {
        $("#change_stage_id").val('');
        $(".wf_stage_id").val($(this).attr("data-stageid"));
        $(".wf_object_id").val($(this).attr("data-objectid"));
        $(".wf_object_type").val($(this).attr("data-objecttype"));
        $('#change_stage_form').parsley().reset();
        $('#change_stage_modal').modal({
            show: 'show',
            backdrop: false
        }); 
    });

    $(document).on("click",".reset_stage",function() {
        $(".connectedSortable_doc").sortable('cancel');
        $('#change_stage_modal').modal('hide');
    });
   
    $(document).on("click",".complete_work_flow",function() {
        var complete = "@php echo url('workflow_exit'); @endphp?wf="+$(this).attr("data-workflow")+"&objecttype="+$(this).attr("data-objecttype")+"&objectid="+$(this).attr("data-objectid");
        var Stage_name = swal({
                         title: "{{ trans('workflows.workflow_complete_note')}}", 
                         showCancelButton: true
                       }).then((result) => {
                           if(result){
                             
                             window.location.href= complete;
                           }
                           else{
                             
                           }
                         });
    
   });
 
   

   
   
   $(document).on("click",".delete_activity",function() {
   var deleteurl = "@php echo url('workflow_activity_delete'); @endphp?wf="+$(this).attr("data-workflow")+"&activity="+$(this).attr("data-activity");
     var Stage_name = swal({
                         title: "{{trans('language.activity_delete_note')}}", 
                         showCancelButton: true
                       }).then((result) => {
                           if(result){
                             console.log(deleteurl);
                               window.location.href = deleteurl;
   
                           }
                           else{
                             
                           }
                         });
    
   });
   
   
   //console.log("tos"+today);
  
  
   $(document).on("click",".save_activity",function(e) {
   e.preventDefault();
   var last_activity_check = $('#activity_name').find('option:selected').attr('add_last_activity');
   var stage_order = $('#add_hidd_stage_order').val();
   var stage_count = $('#stage_count').val();
   if($("#workflow_activity_form").parsley().validate())
   {   
      if(last_activity_check == 1)
      {
        var url = "@php echo url('workflow_activity_save'); @endphp";
        swal({
        title:"{{trans('workflows.last_activity_wf_next_stage')}}",
        showCancelButton: true
        }).then(function(result) {
         $.ajax({
              type: "POST",
              url: url,
              dataType:'json',
              data: $("#workflow_activity_form").serialize() + "&last_activity_flag="+last_activity_check, /*serializes the form's elements.*/
              success: function(response)
              {
                var url = "@php echo url('change_workflow_stage'); @endphp";
              
                  $.ajax({
                  type: "POST",
                  url: url,
                  dataType:'json',
                  data: $("#workflow_activity_form").serialize() + "&last_activity_flag="+last_activity_check, /*serializes the form's elements.*/
                  success: function(response)
                  {
                    if(response.status == 1)
                    {
                     show_work_flow(response);
                     $('#activity_modal').modal('hide');
                    }
                  }
                  });
              }
            });
        }, 
        function(dismiss) {
         var url = "@php echo url('workflow_activity_save'); @endphp";
              $.ajax({
                type: "POST",
                url: url,
                dataType:'json',
                data: $("#workflow_activity_form").serialize(), /*serializes the form's elements.*/
                success: function(response)
                {
                     /*show response from the php script.*/
                    $(".alert_space").html(response.message); 
                    if(response.status == 1)
                    {
                     show_work_flow(response);
                     //$('#activity_modal').modal('hide');
                    }
                    
                }
              });
       });
      }
      else
      {
        var url = "@php echo url('workflow_activity_save'); @endphp";
        $.ajax({
          type: "POST",
          url: url,
          dataType:'json',
          data: $("#workflow_activity_form").serialize(), /*serializes the form's elements.*/
          success: function(response)
          {
               /*show response from the php script.*/
              $(".alert_space").html(response.message); 
              if(response.status == 1)
              {
               show_work_flow(response);
               $('#activity_modal').modal('hide');
              }
              
          }
        });
      }
     
//check last order and last stage?
      if((stage_order == stage_count) && (last_activity_check == 1))
      {
        var exit = "@php echo url('workflow_exit'); @endphp?wf="+$("#add_wf_id").val()+"&objecttype="+$("#add_wf_object_type").val()+"&objectid="+$("#add_wf_object_id").val();
        swal({
          title:"{{trans('language.last_activity_wf_exit')}}",
          showCancelButton: true
          }).then((result) => {
                             if(result){
                              window.location.href= exit;
                             }
                             else{
                               //stay in workflow last stage
                             }
                           });
      }
    }
  });
   
   $("#change_stage_form").submit(function(e) {
   
   
   var url = "@php echo url('change_workflow_stage'); @endphp";
   
   $.ajax({
          type: "POST",
          url: url,
          dataType:'json',
          data: $("#change_stage_form").serialize(), /*serializes the form's elements.*/
          success: function(response)
          {
               /*show response from the php script.*/
   //alert_space").html(response.message); 
              if(response.status == 1)
              {
               show_work_flow(response);
               $('#change_stage_modal').modal('hide');
              }
              
          }
        });
   
   e.preventDefault(); /*avoid to execute the actual submit of the form.*/
   });
   
  
   
   
   //refine code

   $(document).on("click",".add_to_workflows",function(e) {
   e.preventDefault();
   var src = "{{ URL::asset('images/loading/loading.gif') }}";
   var loading ='<div  style="text-align: center;"><img src="'+src+'"></div>';
   $('.add_workflow_remote').html(loading);
   $('#add_to_workflow_modal_new').modal({
                     show: 'show',
                     backdrop: false
               }); 
   var workflow_id=$('#workflow_id').val();
   var object_type='document';  
   $('.add_workflow_remote').load("@php echo url('add_to_workflows_modal') @endphp?workflow_id="+workflow_id+"&object_type="+object_type,function(result){
         /*$('#loading_model').modal('hide');*/
         /*$('#add_to_workflow_modal_new').modal({
                     show: 'show',
                     backdrop: false
               }); */
         
         });
   });

  $(document).on('hidden.bs.modal','#add_to_workflow_modal_new', function () {
         get_workflow_stages();
      
   });

  $(document).on("click",".show_more_activity",function(e) {
   e.preventDefault();
   
   var kloop = $(this).attr("data-loop");
   console.log("HI"+kloop);
   if ($("#show_li_"+kloop).css('display') == 'none') 
   {
    $("#show_li_"+kloop).slideDown();
   }
   else
   {
    $("#show_li_"+kloop).slideUp();
   }
   
   });
   
   });
  function wfpdel(id,name)
  {     
      swal({
          title: "{{trans('language.confirm_delete_single')}}'" + name + "' ?",
          text: "{{trans('language.Swal_not_revert')}}",
          type: "{{trans('language.Swal_warning')}}",
          showCancelButton: true
      }).then(function (result) {
             if(result) {
              var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
              $.ajax({
                      type: 'post',
                      url: '{{URL('wfProcessDelete')}}',
                      dataType: 'json',
                      data: {_token: CSRF_TOKEN, id:id},
                      timeout: 50000,
                      success: function(data, status){ 
                        if(status){
                          if(data.response.success==true){
                            $("#WFprocess"+id).remove();
                            $("#wfpdeleteDiv").html('<div class="alert alert-success text-center">Workflow process Deleted Successfully</div>');
                            setTimeout(function() {
                                $("#wfpdeleteDiv").html('')
                            }, 5000);
                          }
                        }
                      },
                      error: function(jqXHR, textStatus, errorThrown){
                             console.log(jqXHR);    
                             console.log(textStatus);    
                             console.log(errorThrown);    
                      }
                    });
             }
      });

  }

   
</script>    
@endsection