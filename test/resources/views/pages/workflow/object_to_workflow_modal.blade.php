<style>

#search_object_model{
    z-index:1500 !important;
}
</style>
<?php include (public_path()."/storage/includes/lang1.en.php" ); ?>
<div class="modal fade" id="search_object_model">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">
                    @if($object_type == 'document'){{$language["search_document"]}} @endif
                  @if($object_type == 'form'){{$language["search_form"]}} @endif
                  </h4>
               </div>
               <div class="modal-body">
                  {!! Form::open(array('url'=> '', 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'add_to_workflow_form', 'id'=> 'add_to_workflow_form','data-parsley-validate'=> '')) !!}  
                   @if($object_type == 'document')
                   <div class="form-group">

                     <div class="col-sm-4">
                     <label for="new_object_id" class="control-label">{{$language["document type"]}}: </label>
                   <select name="modal_document_type" id="modal_document_type" class="form-control">
                  <option value="">-----</option>
                  @foreach ($docType as $dt_row)  
                  <option value="{{$dt_row->document_type_id}}">{{$dt_row->document_type_name}}</option>
                  @endforeach
               </select></div>

                     <div class="col-sm-3">
                     <label for="new_object_id" class="control-label">{{$language["doc no"]}}: </label>
                     <input type="text" class="form-control" name="modal_document_no" id="modal_document_no" value="" autocomplete="off">
                     </div>
                      <div class="col-sm-5">
                     <label for="new_object_id" class="control-label">{{$language["document name"]}}: </label>
                <input type="text" name="modal_object_name" id="modal_object_name" class="form-control" value="{{$object_name}}" autocomplete="off">
                     </div>

                     
                  </div>
                  @endif

                  @if($object_type == 'form')
                   <div class="form-group">
<div class="col-sm-4">
                     <label for="new_object_id" class="control-label">{{$language["form"]}}: </label>
                   <select name="modal_form_type" id="modal_form_type" class="form-control">
                  <option value="">-----</option>
                  @foreach ($formType as $df_row)  
                  <option value="{{$df_row->form_id}}">{{$df_row->form_name}}</option>
                  @endforeach
               </select></div>
               <div class="col-sm-4">
                     <label for="new_object_id" class="control-label">{{ Lang::get('language.submitedby') }}: </label>
                   <select name="modal_form_user" id="modal_form_user" class="form-control">
                  <option value="">-----</option>
                  @foreach ($user as $df_row)  
                  <option value="{{$df_row->id}}">{{$df_row->user_full_name}}</option>
                  @endforeach
               </select></div>
                  <div class="col-sm-4">
                     <label for="new_object_id" class="control-label">{{$language["date"]}}: </label>
                <div class="input-group">
                           <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                           </div>
                           <input type="text" class="form-control" id="modal_form_date" name="modal_form_date" placeholder="YYYY-MM-DD">
                        </div>
                     </div>

                     
                  </div>
                  @endif

                  @php $random = str_random(5); @endphp
                  <div class="form-group">
                  <div class="col-sm-12">
                  <span class="help-text">Please use the above filters for better search results.</span>
                  <span class="pull-right">
                  <button type="button" class="btn btn-primary btn-xs search_object_form{{$random}}">{{ $language['search'] }}</button>

                  <button type="button" class="btn btn-danger btn-xs close_modal" data-modalid="#search_object_model">{{ $language['cancel'] }}</button>                    </span>
                   
              </div>
                     </div>

                  </form>

                  <div class="row response_doc" style="display: none;">
                    
                  </div>
                  </div>
               <div class="modal-footer">
               </div>
            </div>
            <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
      <script>
      $(function ($) {
        
    var serch_doc = function(doc_url) 
    {
       var src = "{{ URL::asset('images/loading/loading.gif') }}";
       var loading ='<div  style="text-align: center;"><img src="'+src+'"></div>';
       $(".response_doc").html(loading);
      var object_type=encodeURI("@php echo $object_type; @endphp");
      
      /*var str = $.param( params );*/
       $.ajax({
           type: 'GET',
           dataType:'json',
           url: doc_url,
           success:function(data)
           {
            
             $(".response_doc").html(data.html);
             $(".response_doc").slideDown();

             
           },
           error: function(jqXHR, textStatus, errorThrown) 
           {
               console.log(jqXHR.status);
           }
       });
   
    };

   /* var object_type=encodeURI("@php echo $object_type; @endphp");
    var object_name=encodeURI("@php echo $object_name; @endphp");
    var doc_url = "{{URL('search_object_data')}}?page=1&object_type="+object_type+"&object_name="+object_name
    if(object_type == 'document')
      {
        var document_type=encodeURI($("#modal_document_type").val());  
        doc_url +="&document_type="+document_type;
        var document_no=encodeURI($("#modal_document_no").val());  
        doc_url +="&document_no="+document_no; 
      }

      serch_doc(doc_url);*/
    var random = "@php echo $random; @endphp";
    $(document).on("click",".search_object_form"+random,function(e) {
      var object_type=encodeURI("@php echo $object_type; @endphp");
      console.log(object_type);
      var doc_url = "{{URL('search_object_data')}}?page=1&object_type="+object_type;
      if(object_type == 'document')
      {
        var object_name=encodeURI($("#modal_object_name").val());
        doc_url +="&object_name="+object_name;
        var document_type=encodeURI($("#modal_document_type").val());  
        doc_url +="&document_type="+document_type;
        var document_no=encodeURI($("#modal_document_no").val());  
        doc_url +="&document_no="+document_no; 
      }
      if(object_type == 'form')
      {
        var form_type=encodeURI($("#modal_form_type").val());  
        var form_submit_date=encodeURI($("#modal_form_date").val());
        var form_user=encodeURI($("#modal_form_user").val());  
        doc_url +="&form_type="+form_type+"&form_submit_date="+form_submit_date+"&form_user="+form_user;
      } 

      serch_doc(doc_url);
   });

    $(document).on("click",".pagination-link",function(e) {
      e.preventDefault();
      var doc_url = $(this).attr('href');
      serch_doc(doc_url);
   });

  $(document).on("click",".select_object_item",function(e) {
      
      var obj_name = $(this).attr('data-obj_name');
      var obj_id = $(this).attr('data-obj_id');
      console.log(obj_name);
      $(".form_object_name").val(obj_name);
      $(".form_object_id").val(obj_id);
      $('#search_object_model').modal('hide');
   });

        @php if($object_type == 'document'){ @endphp

          
        

        @php }if($object_type == 'form'){ @endphp

          $('#modal_form_date').daterangepicker({
           singleDatePicker: true,
           "drops": "down",
            format: 'YYYY-MM-DD',
           showDropdowns: true
       });

        @php } @endphp
      

   });     
      </script>