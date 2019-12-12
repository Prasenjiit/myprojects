<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?> 
@extends('layouts.app')
@section('main_content')
 <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/css/bootstrap-colorpicker.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/js/bootstrap-colorpicker.min.js"></script> 

{!! Html::style('plugins/vis-4.21.0/dist/vis-network.min.css') !!} 
{!! Html::script('plugins/vis-4.21.0/dist/vis.min.js') !!}  
{!! Html::script('js/parsley.min.js') !!}   

<style type="text/css">
    #mynetwork {
      /*width: 400px;
      height: 400px;*/
      border: 1px solid lightgray;
    }

    #stages_table thead tr {
    background-color: #fff !important;
    color: #333 !important;
}

#edges_table thead tr {
    background-color: #fff !important;
    color: #333 !important;
}
  </style>
<section class="content">
       <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Closed Workflow </h3>
              <!-- <p class="help-block">{{$language['form_help1']}}</p> -->
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" id="closed_workflow_name" name="closed_workflow_name" data-parsley-validate="data-parsley-validate">
              <input type="hidden" name="workflow_id" id="workflow_id" class="workflow_id" value="{{$workflow_id}}">
              <div class="box-body">
                <div class="col-md-12 alert_space">
                  </div>
                <div class="col-md-6">
                  
                  <div class="form-group">
                  <label>{{$language['workflow name']}}:<span class="compulsary">*</span></label>
                   <input type="text" class="form-control" id="workflow_name" name="workflow_name" required="" data-parsley-required-message="Workflow name is required" data-parsley-trigger="change focusout">
                </div>

                <div class="form-group">
                  <label>{{$language['color']}}:<span class="compulsary">*</span></label>
                  <div id="cp2" class="input-group colorpicker-component" title="Using input value">
                    <input type="text" name="color" id="workflow_color" class="form-control" value="#C0C0C0" readonly="" />
                    <span class="input-group-addon"><i></i></span>
                  </div>
                </div>

                <div class="form-group">
                  <label>{{$language['stage']}}:</label>
                  <table class="table table-bordered" id="stages_table">
                <thead><tr>
                  <td style="width: 5%;" nowrap="nowrap">#</td>
                  <td style="width: 50%;">{{$language['stage']}}:</td>
                  <td style="width: 35%;">Actions</td>
                </tr>
              </thead>
                <tbody>
                
              </tbody>
<tfoot><tr>
                  
                  <td colspan="3"><button type="button" class="btn btn-block btn-primary btn-xs addNewState" ><i class="fa fa-plus"></i> Add New Stage</button></td>
                </tr>
              </tfoot>
            </table>
                </div>

                <div class="form-group">
                  <label>Transitions:</label>
                  <table class="table table-bordered" id="edges_table">
                <thead><tr>
                  <td style="width: 5%;" nowrap="nowrap">#</td>
                  <td style="width: 25%;">Name:</td>
                  <td style="width: 25%;">From:</td>
                  <td style="width: 25%;">TO:</td>
                  <td style="width: 20%;">Actions</td>
                </tr>
              </thead>
                <tbody>
                
              </tbody>

            </table>
                </div>
				
				<div class="form-group">
                 <button type="button" class="btn btn-primary save_workflow">Save</button>
                 <button type="button" class="btn btn-danger" >Cancel</button>
                </div>
                </div>
                <!-- LEFT COL -->
                <div class="col-md-6">
                  <div id="mynetwork"></div>
                </div>
                <!-- LEFT COL END-->
               </div> 
            </form>
          </div>
{!! Form::open(array('url'=> '', 'method'=> 'post', 'class'=> '', 'name'=> 'add_transition', 'id'=> 'add_transition','data-parsley-validate'=> '')) !!} 
      <div class="modal fade" id="activity_modal">
        <input type="hidden" name="edge_id" id="edge_id" value="">
          <div class="modal-dialog modal-xs">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Transition</h4>
              </div>
              <div class="modal-body">
                <!--- START !-->
                <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Transition Name:<span class="compulsary">*</span></label>
                <input type="text" name="transition_name" id="transition_name" class="form-control" value="" required="" data-parsley-required-message="Transition name is required" data-parsley-trigger="change focusout"/>
              </div>
              <!-- /.form-group -->
            </div>
          </div>

             <div class="row">     
            <div class="col-md-6">
              <div class="form-group">
                <label>From Stage:<span class="compulsary">*</span></label>
                <select name="from_satge" id="from_satge" class="form-control select2 stages_option" required="" data-parsley-min="1" data-parsley-required-message="From stage is required" data-parsley-trigger="change focusout">
                  
                </select>
              </div>
              <!-- /.form-group -->
            </div>
            <!-- /.col -->
            <div class="col-md-6">
              <div class="form-group">
                <label>To Stage:<span class="compulsary">*</span></label>
                <select name="to_satge" id="to_satge" class="form-control select2 stages_option" required="" data-parsley-min="1" data-parsley-required-message="To stage is required" data-parsley-trigger="change focusout">
                 
                </select>
              </div>
             
            </div>
            <!-- /.col -->
          </div>
                <!--- END !-->
              </div>
              <div class="modal-footer">
               
                <button type="button" class="btn btn-primary save_transition">Save</button>
                 <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>        
</form>
      </section>
   

    {!! Html::script('js/jquery-ui.min.js') !!}

    <script>

 $(document).ready(function() {
  var nodes, edges, network,nodecount,edgecount,stage_color;
  var nodelabels = [];
  var stage_option = '<option value="">Select a stage</option>';
  $('#cp2').colorpicker({
            format: 'hex'
        }).on('change',
            function(ev) {
                console.log(ev);
                if(stage_color != ev.target.value)
                {

                }
                /*stage_color = ev.target.value;*/
            });;
    var s = $("#sticker");
    var pos = s.position();                    
    var stage_color = $("#workflow_color").val(); 
    var workflow_id = $('#workflow_id').val();
    var nodecount = 0; var edgecount = 0;

    var nodeset = [];
  var nodes = new vis.DataSet(nodeset);

  var edgeset = [];
  var edges = new vis.DataSet(edgeset);

  // create a network
  var container = document.getElementById('mynetwork');
  var data = {
    nodes: nodes,
    edges: edges
  };
  var options = {
  height: '600px',
  width: '100%'
  }
  var network = new vis.Network(container, data, options);network.fit();

    
 
   var addNewState = function(newstate) 
   {
    nodecount++;
    try {
    var newtrHTML ='';
	var stage_id = nodecount;	
  var data_options ='data-stage="'+stage_id+'" data-edge=""  data-edge_label="Next" data-edge_from="'+stage_id+'" data-edge_to=""';
      newtrHTML += '<tr id="tr-'+stage_id+'"><td>1</td>';
      newtrHTML += '<td><input class="form-control input-sm stage_label" id="stage_label_'+stage_id+'" type="text" value="'+newstate+'" '+data_options+'></td>';
      newtrHTML += '<td><i class="fa fa-plus Addtransition" '+data_options+'></i> &nbsp; &nbsp; <i class="fa fa-trash deleteState" '+data_options+'></i></td></tr>'; 
      $('#stages_table').append(newtrHTML);  

                nodes.add({
                    id: nodecount,
                    dbid:0,
                    label: newstate,
                    shape: 'box',
                    color:stage_color
                });
                return json = {}
            }
            catch (err) {
                alert(err);
            }
    
   }; 

  var DrawTabale = function() 
   {
   // nodeset = [];
   $("#stages_table > tbody").empty();
    var response = nodes.get();
    var trHTML = '';
    $.each(response, function(i, item) {
	  var stage_id = item.id;	
    var data_options ='data-stage="'+stage_id+'" data-edge="" data-edge_label="Next" data-edge_from="'+stage_id+'" data-edge_to=""';
      trHTML += '<tr id="tr-'+stage_id+'"><td>' + item.id + '</td>';
      trHTML += '<td><input class="form-control input-sm stage_label" id="stage_label_'+stage_id+'" type="text" value="' + item.label + '" '+data_options+'></td>';
      trHTML += '<td><i class="fa fa-plus Addtransition" '+data_options+'></i> &nbsp; &nbsp;<i class="fa fa-trash deleteState" '+data_options+'></i> </td></tr>';
       nodelabels[item.id] = item.label;
       });
    //console.log(trHTML);
    if(trHTML == '')
    {
      /*addNewState('Stage 1');
      addNewState('Stage 2');*/
      network.fit();
    }
    else
    {
      $('#stages_table').append(trHTML);
    }

      
     };

    
     
     

    var DrawEdges = function() 
   {
   // nodeset = [];
   $("#edges_table > tbody").empty();
    var response = edges.get();
    var trHTML = '';
    
    $.each(response, function(i, item) {
	  var from_node = nodes.get(item.from);	
	  var to_node = nodes.get(item.to);
	  if(from_node && to_node)
	  {
      var data_options ='data-stage="'+item.from+'" data-edge="'+item.id+'" data-edge_label="'+item.label+'" data-edge_from="'+item.from+'" data-edge_to="'+item.to+'"'; 
      trHTML += '<tr id="tr-'+item.id+'"><td>' + item.id + '</td>';
      trHTML += '<td>'+item.label+'</td>';
      trHTML += '<td>'+from_node.label+'</td>';
      trHTML += '<td>'+to_node.label+'</td>';
      trHTML += '<td><i class="fa fa-edit Addtransition" '+data_options+'></i> &nbsp;&nbsp;<i class="fa fa-trash deleteEdge" '+data_options+'></i> </td></tr>';
	  }
       });
    
      $('#edges_table').append(trHTML);
    

      
     };

    
     
     


     var loadWorkflow = function(wf) 
   {
      var loadformurl = "@php echo URL('load_Workflow_json'); @endphp";
      console.log("loadformurl"+loadformurl);
      $.getJSON(loadformurl+'?workflow_id=' + wf, function(data) {
        nodecount = data.nodecount;
        edgecount = data.edgecount;
        var workflow_name =data.workflow_name;
        stage_color =data.workflow_color;
        var nodeset = data.wf_states;
        var edgeset = data.wf_transitions;
        $("#workflow_name").val(workflow_name); 
        $("#workflow_color").val(stage_color); 
        /*var nodes = new vis.DataSet(nodeset);
        var edges = new vis.DataSet(edgeset);
        var container = document.getElementById('mynetwork');
        var data = { nodes: nodes,edges: edges};
  
        var options = {
                        height: '600px',
                        width: '100%'
        }
        var network = new vis.Network(container, data, options);network.fit();*/

        nodes.add(nodeset);
        edges.add(edgeset);

        DrawTabale();
        DrawEdges();
      });
   };
   loadWorkflow(workflow_id);

     $(document).on("click",".addNewState",function(e) {

       addNewState('Stage');
     });

     $(document).on("click",".deleteState",function(e) {
       var node_id =$(this).attr('data-stage'); 
       deleteState(node_id);
	   DrawEdges();
     });

     $(document).on("click",".deleteEdge",function(e) {
       var edge_id =$(this).attr('data-edge'); 
        try {
                console.log(edge_id);
                edges.remove({id: edge_id});
                DrawEdges();
            }
            catch (err) {
                alert(err);
            }
            
     });

  var deleteState = function(node_id) 
   {
      
      try {
                nodes.remove({id: node_id});
                $("#tr-"+node_id).remove();
            }
            catch (err) {
                alert(err);
            }
    
   }; 

   $(document).on("click",".Addtransition",function(e) {
    console.log(nodes.get());
    console.log(edges.get());
    var satge_id =$(this).attr('data-stage');
    var edge_id =$(this).attr('data-edge'); 
    var edge_from =$(this).attr('data-edge_from');
    var edge_to =$(this).attr('data-edge_to');
    var transition_name = $(this).attr('data-edge_label');
$('#activity_modal').modal({
                     show: 'show',
                     backdrop: false
               });
    var stage_option = '<option value="">Select</option>';
    var response = nodes.get();
    $.each(response, function(i, item) {
      stage_option += '<option value="' + item.id + '">' + item.label + '</option>';
      
       });
      $('.stages_option').html(stage_option);
      $('#transition_name').val(transition_name);
      $('#from_satge').val(edge_from);
      $('#to_satge').val(edge_to);
      $('#edge_id').val(edge_id);
      $('#add_transition').parsley().reset();
     });
    $(document).on("change keyup paste input",".stage_label",function(e) {
	 
	  
	  try {
                var stage_id = $(this).attr('data-stage');	
				console.log("save 122"+stage_id);
			   nodes.update({
                    id: stage_id,
                    label: $('#stage_label_'+stage_id).val()
                });
				
				DrawEdges();
            }
            catch (err) {
                alert(err);
            }
      
     });

   $(document).on("click",".save_workflow",function(e) {
   e.preventDefault();
   
   if($("#closed_workflow_name").parsley().validate())
   {
    console.log("save");
        var fields='';
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_url = "@php echo URL('save_closed_workflow'); @endphp";
        var workflow_id = $('#workflow_id').val();

        /*var data = JSON.stringify([{_token:CSRF_TOKEN},{"name":"formID","value":formID},{"name":"formFields","value":fields}]);*/
        var data = {_token: CSRF_TOKEN,"workflow_id":workflow_id,"workflow_name":$('#workflow_name').val(),"workflow_color":$('#workflow_color').val(),"workflow_stages":nodes.get(),"workflow_edges":edges.get()};
        console.log(data);
        
        $.ajax({
            method: "POST",
            url: form_url,
            data: data,
            dataType: 'json',
            success: function (msg) {
                console.log(msg);
                $('.alert_space').html(msg.message);
                if(msg.status == 1)
                {
                        workflow_id = msg.workflow_id;
                        $('#closed_workflow_name').parsley().reset();
                       /* if(return_form == 'save_close')
                        {
                          window.location.href = return_form_url;      
                        }*/

                }
                
                $("html, body").animate({ scrollTop: 0 }, "fast");

                //Demo only
                //$('.alert textarea').val(JSON.stringify(fields));
            }
        });
    
   }

     });
	 
	 
	  $(document).on("click",".save_transition",function(e) {
   e.preventDefault();
   
   if($("#add_transition").parsley().validate())
   {
    console.log("save");
    try {
                var edge_id = $('#edge_id').val();
                if(edge_id)
                {
                  edges.update({
                      id: edge_id,
                      dbid:0,
                      label:document.getElementById('transition_name').value,
                      from: document.getElementById('from_satge').value,
                      to: document.getElementById('to_satge').value,
                      arrows:'to'
                  });
                }
                else
                {
                  edgecount++;
                  edges.add({
                      id: edgecount,
                      dbid:0,
                      label:document.getElementById('transition_name').value,
                      from: document.getElementById('from_satge').value,
                      to: document.getElementById('to_satge').value,
                      arrows:'to'
                  });
                  $('#edge_id').val(edgecount);
                }
                
                DrawEdges();
            }
            catch (err) {
                alert(err);
            }
   }

     });

  });

  
</script>
   
  @endsection