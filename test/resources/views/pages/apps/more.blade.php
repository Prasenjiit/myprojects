
<div class="modal-header" id="form_values_add_header" style="border-bottom-color: deepskyblue; padding-bottom: 40px;">
  <h4 class="modal-title" style="float:left;">
      {{ucfirst(@$app_details->document_type_name)}}
      <small>- More Details</small>
  </h4>
  
  <button class="btn btn-primary btn-danger pull-right" id="cn" data-dismiss="modal" type="button">Close</button>
  
</div>


<div class="modal-body form_values_show" id="">
  {!! Form::open(array('url'=> array(''), 'method'=> 'post', 'class'=> '', 'name'=> 'form_response_action_form', 'id'=> 'form_response_action_form','data-parsley-validate'=> '')) !!} 
  <div class="row">
  <div class="col-sm-12 alert_form"></div>
  <div class="col-sm-12">

    <table border="1" id="documentGroupDT" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
        <thead>
            <tr>
                <th colspan="2">App Information</th>
            </tr>
        </thead>
        <tbody>
        <tr>
            <td width="25%" nowrap="nowrap">App:</td>  
            <td>{{ucfirst(@$app_details->document_type_name)}}</td> 
            </tr>
            <tr>
            <td>App Description:</td>  
            <td>{{ucfirst(@$app_details->document_type_description)}}</td>  
            </tr>
            <tr>
            <td>No. of Records:</td>  
            <td>{{ucfirst(@$count_records)}}</td>  
            </tr>
        
        
            <tr>
            <td width="25%" nowrap="nowrap">Created By:</td>  
            <td>{{@$app_details->document_type_created_by}}</td> 
            </tr>
            <tr>
            <td>Created At:</td>  
            <td>{{@$app_details->created_at}}</td>
            </tr>
            
            <tr>
            <td width="25%" nowrap="nowrap">Updated By:</td>  
            <td>{{@$app_details->document_type_modified_by}}</td> 
            </tr>
            <tr>
            <td>Updated At:</td>  
            <td>{{@$app_details->updated_at}}</td>
            </tr>

            </tbody> 
        @if($app_links)
        <thead>
            <tr>
                <th width="50%" nowrap="nowrap">Linked Document Types</th>
                <th width="50%" nowrap="nowrap">Linking Columns</th>
            </tr>
        </thead>
          <tbody>
          
          <?php for($i=0;$i<count($app_links);$i++){?>
          <tr>
          <td>{{$app_links[$i]['document_type_name']}}</td>
          <td>{{$app_links[$i]['document_column_name']}}</td>
          </tr>
          <?php } ?>
          </tbody> 
        @endif
    </table>
   </div>
   </div>
   {!! Form::close() !!}
</div>   