<?php
  include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
{!! Html::script('js/parsley.min.js') !!} 
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables/dataTables.bootstrap.css') !!}
{!! Html::style('plugins/EasyAutocomplete-1.3.5/easy-autocomplete.min.css') !!} 
<style type="text/css">   
.help_text{
  font-size:12px; color:#999;
}    

.no_padding{
margin-bottom: 2px;
}
</style>
<section class="content-header">
  <div class="row">
    <div class="col-sm-8">
      <span style="float:left;">
        <strong>{{ Lang::get('apps.apps') }}</strong> &nbsp;
      </span>
      <span style="float:left;">
        <?php
        $user_permission=Auth::user()->user_permission;                       
        ?>     
        @if(stristr($user_permission,"add") && Auth::user()->user_role != Session::get('user_role_private_user'))
            <a href="{{ url('appsEdit/0')}}" class="btn  btn-info btn-flat newbtn">{{ Lang::get('apps.add_new_app') }} &nbsp; <i class="fa fa-plus"></i>
            </a>
        @endif
      </span>
    </div>
    <div class="col-sm-4">
      <ol class="breadcrumb">
          <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{ Lang::get('language.home') }}</a></li>
          <li class="active">{{ Lang::get('apps.apps') }}</li>
      </ol>
    </div>
  </div>
  </section>
<section class="content">
      <div class="row">
<div class="col-sm-12 alert_space"></div>        
<div class="col-md-12">
 {!! Form::open(array('url'=> array('appsSave','0'), 'method'=> 'post', 'class'=> '', 'name'=> 'appsSaveForm', 'id'=> 'appsSaveForm','data-parsley-validate'=> '')) !!}                  
<input type="hidden" name="app_id" id="app_id" value="{{ $app_id }}">   
            <input type="hidden" name="action" id="action" value="edit">     
          <div class="box box-primary">
            <div class="box-header with-border">
              <strong>Link <span class="app_name"></span> App to Document Types</strong>
              <p class="help_text">You can link this App to other document types. By linking other document
types to an App you can avoid duplicating information in other document types</p>
            </div>
            <div class="box-body" >
             <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-4">
              <div class="form-group">
              <label class="no_padding" for="" class="">{{ Lang::get('apps.column_from_apps') }}: <span class="compulsary">*</span></label>
              <p class="help_text no_padding">Select the linking column name of the App</p>
              <select class="form-control" name="link_column_type" id="link_column_type" data-parsley-required="true" data-parsley-required-message="{{ Lang::get('apps.this_field_required') }}" >
                
              </select>
              
              </div>
              </div> 
              <div class="col-xs-12 col-sm-12 col-md-8" id="link_row"></div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                 <input class="btn btn-primary" id="save" type="submit" value="{{Lang::get('language.save')}}" > 
                <a href="{{url('apps')}}" class="btn btn-danger">{{Lang::get('language.cancel')}}</a>  
                {!!Form::button(Lang::get('apps.link_to_document_type'),array('class'=>'btn btn-primary addDoctype','id'=>'addDoctype','value'=>'addDoctype'))!!}
              </div>
          </div>
           <!-- /.box -->
{!! Form::close() !!}    
        </div> <!-- /.COL -->
        </div> <!-- /.ROW -->
      </section>


 {!! Html::script('js/jquery-ui.min.js') !!}
{!! Html::script('plugins/EasyAutocomplete-1.3.5/jquery.easy-autocomplete.min.js') !!} 
<script type="text/javascript">

    $(document).ready(function(){
    
      var link_ctr=0;
      

      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
      var index_options = '<option value="">Please select a column</option>';
      var selected_option=0;
      var doc_type_options = '<option value="">Please select a document type</option>';
      var doc_type_index_options = '<option value="">Please select a column</option>';
      var app_name='';
      /*load apps*/
      
 
      /*load apps*/
      var load_app_form = function() 
      {
      var url = "@php echo url('load_app_form?app_id='.$app_id.'&action=edit'); @endphp";
      var html = '';
      $.ajax({
            type: "GET",
            url: url,
            dataType:'JSON',
            success: function(response)
            {
                
                if(response.status == 1)
                {
                  app_name = response.app_name;
                  $.each(response.document_type_column, function(i, item) {

                    if(item.document_type_column_name !='')
                    {
                        index_options +='<option value="'+item.document_type_column_id+'">'+item.document_type_column_name+'</option>';
                    }
                  });
                  $("#link_column_type").html(index_options); 
                   $.each(response.document_types, function(i, item) {
                      doc_type_options +='<option value="'+item.id+'">'+item.name+'</option>';
                      $.each(item.childrens, function(i1, item1) {
                       doc_type_index_options +='<option value="'+item1.id+'"" class="doc_type_options doc_type_'+item.id+'" style="display:none;">'+item1.name+'</option>';   
                      });
                   });

                   $.each(response.app_links, function(i, item) {

                    if(item.app_column_id !='')
                    {
                    $("#link_column_type").val(item.app_column_id).trigger("change");  
                    link_ctr++;
                    var data_array = {'link_ctr':link_ctr,'app_link_id':item.app_link_id,'app_column_id':item.app_column_id,'document_type_id':item.document_type_id,'document_column_id':item.document_column_id};
                    add_links(data_array);
                    }
                  });

                  if(response.app_links.length == 0)
                  {
                      console.log("No links");
                      $(".addDoctype").trigger("click");  
                  } 
                 
                }
                else
                {
                }
                $("#app_section").html(html); 
                $(".app_name").html(app_name); 
            }
        });
    }
    //call function for load apps
    load_app_form();



$("#appsSaveForm").submit(function(e) 
  {
    if($("#appsSaveForm").parsley().validate())
     { 
       $('.alert_space').html('');
       var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
       var url = "@php echo url('save_app_links'); @endphp";
       var postdata = {_token:CSRF_TOKEN}
       
       /*$('.pre_loader').show();*/
       $.ajax({
              type: "POST",
              url: url,
              dataType:'json',
              data: $('#appsSaveForm').serialize(), /*serializes the form's elements.*/
              success: function(response)
              {
                  if(response.status == 1)
                  {
                    //call function for load apps
                    
                  }
                  $('.alert_space').html(response.message);
              }
            });
     }
     else
     {
        console.log("validation Failed");
     }
   e.preventDefault(); /*avoid to execute the actual submit of the form.*/
   });
/* Add New App End*/    

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



$(document).on('click', '.addDoctype', function () {
    link_ctr++;
    var data_array = {'link_ctr':link_ctr};
    add_links(data_array);
    
});
var add_links = function(data_array) 
{
    var l_ctr = data_array.link_ctr;
    var app_link_id = (typeof data_array.app_link_id != 'undefined') ? data_array.app_link_id:0;
    var app_column_id = (typeof data_array.app_column_id != 'undefined') ? data_array.app_column_id:'';
    var document_type_id = (typeof data_array.document_type_id != 'undefined') ? data_array.document_type_id:'';
    var document_column_id = (typeof data_array.document_column_id != 'undefined') ? data_array.document_column_id:'';

    var common_attr = 'data-l_ctr="'+l_ctr+'"';

    var label_enabled = (l_ctr ==1)?'':'display:none;';
    var hint_enabled = (l_ctr ==1)?'':'display:none;';
    var style_delete = (l_ctr ==1)?'margin-top:30px;':'';
   
    var newTr = '<div class="row link_tr_'+l_ctr+'">';
    if(l_ctr == -1) /*disabled this section*/
    {
    newTr += '<div class="col-xs-3">';
    newTr += '<div class="form-group">';
    newTr += '<label class="no_padding" for="link_column_type_'+l_ctr+'">{{ Lang::get('apps.column_from_apps') }}: <span class="compulsary">*</span></label>';
    newTr += '<p class="help_text no_padding">Select the linking column name of the App</p>';
    newTr += '<select class="form-control link_column_types" name="link_column_type_'+l_ctr+'" id="link_column_type_'+l_ctr+'" ' + common_attr + ' data-parsley-required="true" data-parsley-required-message="{{ Lang::get('apps.this_field_required') }}" >' + index_options + '</select>';
    
    newTr += '</div>'; /*End Form group*/
    newTr += '</div>'; /*End Col*/
    }
    newTr += '<div class="col-xs-12 col-sm-12 col-md-5">';
    newTr += '<div class="form-group">';
    newTr += '<label class="no_padding" for="link_column_type_'+l_ctr+'" style="'+label_enabled+'" >{{ Lang::get('apps.document_type') }}: <span class="compulsary">*</span></label>';
    newTr += '<p class="help_text no_padding" style="'+hint_enabled+'">Select the document type to which the app will be linked</p>';
    newTr += '<select class="form-control document_type document_type'+l_ctr+'" name="document_type_'+l_ctr+'" id="document_type_'+l_ctr+'" ' + common_attr + ' data-parsley-required="true" data-parsley-required-message="{{ Lang::get('apps.this_field_required') }}" >' + doc_type_options + '</select>';
    newTr += '<input type="hidden" name="link_id['+l_ctr+']" id="link_id'+l_ctr+'" value="'+app_link_id+'"  />';
    
      newTr += '</div>'; /*End Form group*/
    newTr += '</div>'; /*End Col*/

    newTr += '<div class="col-xs-10 col-sm-11 col-md-5">';
    newTr += '<div class="form-group">';
    newTr += '<label class="no_padding" for="link_column_type_'+l_ctr+'" style="'+label_enabled+'">{{ Lang::get('apps.column_from_doctype') }}: <span class="compulsary">*</span></label>';
    newTr += '<p class="help_text no_padding" style="'+hint_enabled+'">Select the linking column name of the document type</p>';
    newTr += '<select class="form-control document_type_option_'+l_ctr+'" name="column_type_option_'+l_ctr+'" id="column_type_option_'+l_ctr+'" ' + common_attr + ' data-parsley-required="true" data-parsley-required-message="{{ Lang::get('apps.this_field_required') }}" >' + doc_type_index_options + '</select>';
    
    newTr += '</div>'; /*End Form group*/
    newTr += '</div>'; /*End Col*/

    newTr += '<div class="col-xs-2 col-sm-1 col-md-1">';
    newTr += '<div class="form-group">';
    newTr += '<div style="'+style_delete+'">';
    newTr += '<i class="fa fa-fw fa-trash deleteCol" title="Remove Field" ' + common_attr + '></i><div>';
    newTr += '</div>'; /*End Form group*/
    newTr += '</div>'; /*End Col*/

    newTr += '</div>'; /*End Row*/
    $('#link_row').append(newTr);

    /*$("#link_column_type_"+l_ctr).val(app_column_id).trigger("change");  */
    $("#document_type_"+l_ctr).val(document_type_id).trigger("change");
    $("#column_type_option_"+l_ctr).val(document_column_id).trigger("change");
    
};
    

   

    $(document).on("change",".document_type",function(e) {
        var l_ctr =$(this).attr('data-l_ctr');
        var doc_type =$(this).val();
        $("#column_type_option_"+l_ctr).val('');
        $(".document_type_option_"+l_ctr).children('.doc_type_options').hide();
        $(".document_type_option_"+l_ctr).children(".doc_type_"+doc_type).show();
     });

    });
    </script>
@endsection