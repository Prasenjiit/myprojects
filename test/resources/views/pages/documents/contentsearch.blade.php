<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')
{!! Html::script('plugins/daterangepicker/daterangepicker.js') !!}
{!! Html::script('js/jquery.mark.min.js') !!}   
<section class="content-header">
    <div class="col-sm-8">
        <span style="float:left;">
            <strong>
                {{$language['content_search']}}
            </strong>
        </span>
    </div>
    <div class="col-sm-4">
        <!-- <ol class="breadcrumb">
                <li><a href="<?php echo  url('/home');?>"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
                <li><a href="<?php echo  url('/documents');?>">{{$language['documents']}}</a></li>
                <li class="active">{{$language['content_search']}}</li>
        </ol> -->   
    </div>
</section> 
<!-- Main content -->
<section class="content">
	<div class="box box-info">
		<form class="" id="documentAdvSearchForm">
			<div class="box-body">

				<div class="col-sm-6">
					<!-- content_search -->
		            <div class="col-sm-12">
		            	{!! Form::label($language['content_search'].':', '', array('class'=> 'control-label'))!!}
		                <input class="form-control" type="text" id="content_srchtxt" name="content_srchtxt" placeholder="{{$language['place_holder_content_search']}}">
		                
		            </div>
		            <!-- content_search -->
					<!-- type: document/form -->
	            	<div class="col-sm-6">
		                {!! Form::label($language['search_in'].':', '', array('class'=> 'control-label'))!!}
		                <select class="selbox form-control" id="section" name="section">
		                    <option value="documents" selected>{{$language['documents']}}</option>
		                    <option value="forms">{{$language['forms']}}</option>
		                </select>               
		            </div>
		            <!-- type -->
		            <!-- options: or/exact/and -->
		            <div class="col-sm-6">
		                {!! Form::label($language['options'].':', '', array('class'=> 'control-label'))!!}
		                <select class="selbox form-control" id="andor" name="searchformat">
		                    <option value="or" selected>{{$language['OR']}}</option>
		                    <option value="and">{{$language['AND']}}</option>
		                    <option value="ex">{{$language['EXACT']}}</option>
		                </select>               
		            </div>
	            	<!-- options -->
					<!-- Note section -->
		            <div class='col-sm-12 instructions' style="color: green;">
		                <p><b>*{{$language['OR']}}</b> - {{$language['OR_content_note']}}</p>
		                <p><b>*{{$language['AND']}}</b> - {{$language['AND_content_note']}}</p>
		                <p><b>*{{$language['EXACT']}}</b> - {{$language['EXACT_content_note']}}</p>
		            </div> 
		            <!-- Note section -->
		        </div>
		        <div class="col-sm-6">
		        <div id="doc_section">
		        	<!-- department -->
	            	<div class="col-sm-12"> 
	                {!! Form::label($language['department'].':', '', array('class'=> 'control-label'))!!}
		                <select name="department" id="department" class="multipleSelect form-control">
		                <option value="0">Select a Department</option>
		                    <?php
		                    foreach ($depts as $key => $row) {
		                        ?>
		                        <option id="department_<?php echo $row->department_id;?>" value="<?php echo $row->department_id;?>" <?php if(Session::get('departments')):if(in_array($row->department_id,Session::get('departments'))):echo 'selected';endif;endif;?> ><?php echo $row->department_name;?></option>
		                        <?php
		                    }
		                    ?>
		                </select>   
	            	</div>   
	            	<!-- department -->
	            	<!-- document types -->
		            <div class="col-sm-12">
		            
		                {!! Form::label($language['document types'].':', '', array('class'=> 'control-label'))!!}
		                <select name="doctypeid" id="doctypeid" class="multipleSelect form-control">
		                    <option value="0">Select a Document Type</option>
		                    <?php
		                    foreach ($docType as $key => $row) {
		                        ?>
		                        <option value="<?php echo $row->document_type_id; ?>" <?php if(Session::get('doctypeids') == $row->document_type_id):echo 'selected';endif;?>><?php echo $row->document_type_name;?></option>
		                        <?php
		                    }
		                    ?>
		                </select>          
		            </div>
	            	<!-- document types -->
		            <!-- stack -->
		            <div class="col-sm-12">
		                {!! Form::label($language['stacks'].':', '', array('class'=> 'control-label'))!!}
		                <select name="stacks" id="stacks" class="multipleSelect form-control" >
		                <option value="0">Select a Stack</option>
		                    <?php
		                    foreach ($stacks as $key => $row) {
		                        ?>
		                        <option value="<?php echo $row->stack_id; ?>" <?php if(Session::get('stackids')):if(in_array($row->stack_id,Session::get('stackids'))):echo 'selected';endif;endif;?>><?php echo $row->stack_name;?></option>
		                        <?php
		                    }
		                    ?>
		                </select>
	            	</div>
	            	<!-- stack -->
	            	<!-- ownership -->
		            @if(Auth::user()->user_role != Session::get('user_role_private_user'))
		            <div class="col-sm-12">
		                {!! Form::label($language['ownership'].':', '', array('class'=> 'control-label'))!!}
		                <select name="ownership" id="ownership" class="multipleSelect form-control">
		                <option value="0">Select a User</option>
		                    <?php
		                    foreach ($users as $key => $row) {
		                        ?>
		                        <option value="<?php echo $row->username; ?>" <?php if(Session::get('owner_ids')):if(in_array($row->id,Session::get('owner_ids'))):echo 'selected';endif;endif;?> ><?php echo $row->username;?></option>

		                        <?php
		                    }
		                    ?>
		                </select> 
		            </div>
		            @endif
		            <!-- ownership -->
		           </div>
		           <!-- date section -->
		            <div class="col-md-12">

			           <a href="javascript:void(0)" class="btn btn-primary show_hide_handle btn-xs pull-right" style="margin-top:5px;">More <i class="fa fa-angle-down"></i></a>

			        </div>

		            <div class="col-sm-12 collapse_group1">
		                {!! Form::label($language['created date - from'].':', '', array('class'=> 'control-label'))!!}
		                <div class="input-group">
		                    <div class="input-group-addon">
		                        <i class="fa fa-calendar"></i>
		                    </div>
		                    <input type="text" class="form-control active" id="created_date_from" name="created_date_from" placeholder="YYYY-MM-DD" title="Created Date - From" data-toggle="tooltip" data-original-title="">
		                </div>
		            </div>

		            <div class="col-sm-12 collapse_group1">
		                {!! Form::label($language['created date - to'].':', '', array('class'=> 'control-label'))!!}
		                <div class="input-group">
		                    <div class="input-group-addon">
		                        <i class="fa fa-calendar"></i>
		                    </div>
		                    <input type="text" class="form-control active" id="created_date_to" name="created_date_to" placeholder="YYYY-MM-DD" title="Created Date - To" data-toggle="tooltip" data-original-title="">
		                </div>
		            </div>

		            <div class="col-sm-12 collapse_group1">
		                {!! Form::label($language['last modified - from'].':', '', array('class'=> 'control-label'))!!}
		                <div class="input-group">
		                    <div class="input-group-addon">
		                        <i class="fa fa-calendar"></i>
		                    </div>
		                    <input type="text" class="form-control active" id="last_modified_from" name="last_modified_from" placeholder="YYYY-MM-DD" title="Last Modified Date- From" data-toggle="tooltip" data-original-title="">
		                </div>
		            </div>

		            <div class="col-sm-12 collapse_group1">
		                {!! Form::label($language['last modified - to'].':', '', array('class'=> 'control-label'))!!}
		                <div class="input-group">
		                    <div class="input-group-addon">
		                        <i class="fa fa-calendar"></i>
		                    </div>
		                    <input type="text" class="form-control active" id="last_modified_to" name="last_modified_to" placeholder="YYYY-MM-DD" title="Last Modified Date - To" data-toggle="tooltip" data-original-title="">
		                </div>
		            </div>
		            <!-- button section -->
		            <div class="col-sm-12" style="text-align:right; margin-top: 20px;">
		            	<button type="button" id="contentSearch" class="btn btn-primary">{{$language['search']}}</button> &nbsp;&nbsp;
		            
		            	<a href={{URL::route('contentSearch')}} class="btn btn-primary" title="{{$language['reset']}}">{{$language['reset']}}</a> &nbsp;&nbsp;
		            
		                <a href="{{URL::route('documentAdvanceSearch')}}" class = "btn btn-primary btn-danger">{{$language['cancel']}}</a>
		            </div>
		            <!-- button section -->
	            </div> 
    		</div>
    	</form>
    </div>
    
	<div class="box box-info" id="section2" style="display: none;">
		<div class="box-header with-border">
		</div>
		<form class="" id="contentform">
			<div class="box-body">
				<div class="col-sm-4">
					<div class="form-group" style="margin-top: -25px;" id ='search_term'>
			            <label for="searchTerm">{{$language['search_term']}}:</label>
						<input type="text" class="form-control" value="" name="keyword" id="keyword" placeholder="{{$language['search_content']}}">
						<p style="font-size:12px; color:#999;">The keyword you want to search inside the following search results</p>
					</div>
				</div>
				<div class="col-sm-2">
		        	<div class="form-group" style="margin-top: -25px;" id ='accurate'>
			            <label for="accuracy">{{$language['Accuracy']}}:</label>
			            <select class="form-control" name="accuracy" id="accuracy">
				            <option value="exactly">{{$language['Exactly']}}</option>
				            <option value="partially" selected>{{$language['Partially']}}</option>
				            <option value="complementary">{{$language['Complementary']}}</option>
			             </select>
		          	</div>
		        </div>
		        <div class="col-sm-4">
		        	<label for="separateWordSearch" class="noTransform"><input type="checkbox" value="true" name="separateWordSearch" id="separateWordSearch" checked> {{$language['seperate_word_search']}}</label>
		        	<label for="diacritics" class="noTransform"><input type="checkbox" value="true" name="diacritics" id="diacritics" checked> {{$language['Diacritics']}}</label>
		        </div>
				<div class="col-sm-2">
		        	<button type="button" class="btn btn-default" name="perform" id="perform">{{$language['search']}}</button> &nbsp;&nbsp;
		        	<a href="{{url('documentAdvanceSearch',@$section)}}" class = "btn btn-default">{{$language['back']}}</a> 
		        </div>
	        </div>
	    </form>
	</div>
	<!-- results -->
	<div id="results" style="max-height: 380px; overflow-y: auto;"></div>
	<!-- preloader -->

    <!-- <div class="preloader" style="text-align: center; margin-top: 5px; display: none;" >
      <i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i>
      <span class="sr-only">Loading...</span>
  	</div> -->

  	<!-- preloader -->
  	<div class="progress_section" style="display: none;margin-top: 10px;">
  	<div class="progress active">
	    <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="1" aria-valuemax="100" style="width: 1%" id="status_bar">
	      
	    </div>
  	</div>
  	</div>
</section>
<script type="text/javascript">
//More button
var show_date_text='More <i class="fa fa-angle-down"></i>';
var hide_date_text='Less <i class="fa fa-angle-up"></i>';
var date_open = true;
//Hide and show date serch fileds
var hide_show_date_search = function ()  
{  
    
    var created_date_from = $("#created_date_from").val().length;
    var created_date_to = $("#created_date_to").val().length;
    var last_modified_from = $("#last_modified_from").val().length;
    var last_modified_to = $("#last_modified_to").val().length;
    if(created_date_from == 0 && created_date_to == 0 && last_modified_from == 0 && last_modified_to == 0)
    {
        date_open=false;
        $(".collapse_group1").slideUp();
        $(".show_hide_handle").html(show_date_text);
        
    }
    else
    {
        $(".show_hide_handle").html(hide_date_text);
    }          

} 
hide_show_date_search();
$('.show_hide_handle').click(function(e)
{
    if(date_open == false)
    {
        date_open=true;
        $(".collapse_group1").slideDown();  
        $(".show_hide_handle").html(hide_date_text);   
    }
    else if(date_open == true)
    {
        date_open=false;
        $(".collapse_group1").slideUp();  
        $(".show_hide_handle").html(show_date_text);   
    }            

});
//change type
$(document).ready(function(){
    $('#section').on('change', function() {
      if ( this.value == 'documents')
      
      {
        $("#doc_section").show();
      }
      else
      {
        $("#doc_section").hide();
      }
    });
});
//content search
	var exportedRecords = 0;
	var chunkSize = 1000; // as per query performance
	var total_results = 0;
	var percentage_incr_last = 0;
	var total_ajax_call = 0;
	var req_count = 0;
$('#contentSearch').click(function(e){
    //e.preventDefault();
    
    $("#results").empty();
    var content_word = $('#documentAdvSearchForm').find('input[name="content_srchtxt"]').val();
    if(!content_word)
    {
    	$(".progress_section").css("display", "none");
        swal("Please enter the keyword for content search");
        return false;
    }
    $("#keyword").val(content_word);
    var andOR=$("#andor").val();//and,or,exact
    var section=$("#section").val();//type form or doc
    var department = $("#department").val();
    var stacks = $("#stacks").val();
    var doctypeid = $("#doctypeid").val();
    var ownership = $("#ownership").val();
    var created_date_from = $("#created_date_from").val();
    var created_date_to = $("#created_date_to").val();
    var last_modified_from = $("#last_modified_from").val();
    var last_modified_to = $("#last_modified_to").val();
    var CSRF_TOKEN =$('meta[name="csrf-token"]').attr('content');
    $(".progress_section").css("display", "block");
    $.ajax({
        type:'post',
        url: '{{URL('RecordsCountContentSearch')}}',
        dataType: "json",
        data: {_token:CSRF_TOKEN,section:section,department:department,stacks:stacks,doctypeid:doctypeid,ownership:ownership,created_date_from:created_date_from,created_date_to:created_date_to,last_modified_from:last_modified_from,last_modified_to:last_modified_to},
        success:function(response){
           console.log(response);
           var totalRecords = response;
            total_ajax_call = Math.round(totalRecords/chunkSize);
            console.log("total_ajax_call="+total_ajax_call);
           var percentage_incr = (100/total_ajax_call);
            req_count = 0;
           if(totalRecords == 0) 
           {
            $(".progress_section").css("display", "none");
            swal("No documents found");
            return false;
           }
           for( start=0; start < totalRecords; start += chunkSize)
           {
                if(start < totalRecords)
                {
                	$(".preloader").css("display", "block");
                	chunkContentSearch(start, chunkSize,totalRecords,content_word,andOR,section,percentage_incr);
            	}
            	else
            	{
            		$(".progress_section").css("display", "none");
            	}
            	if(chunkSize > totalRecords)
            	{
            		$(".progress_section").css("display", "none");
            	}
            }
        }
    });
});

function chunkContentSearch(start,chunkSize,totalRecords,content_word,andOR,section,percentage_incr){
    console.log(content_word);
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var url = '{{URL('contentAdvSearch')}}';
    
    //console.log(url);
    jQuery.ajax({
        type : "post",
        dataType : "json",
        url: url,
        data: {_token:CSRF_TOKEN,content_srchtxt:content_word,searchformat:andOR,start:start,chunkSize:chunkSize,section:section},
        success: function(response) {
        	req_count++;
            var today = new Date();
            var count = response.dglist.file.length;
            total_results += count;
            console.log("req_count="+req_count);
            percentage_incr_last = ((req_count/total_ajax_call)*100);
            percentage_incr_last = Math.round(percentage_incr_last);
            if(percentage_incr_last > 100) 
            {
            	percentage_incr_last = 100;
            }
            console.log(percentage_incr_last);
            $(".progress-bar-primary").css("width", percentage_incr_last+'%');
            $('#status_bar').text(percentage_incr_last+'%');
            // $('#doc_count_status').text(percentage_incr_last+'%');
            //if(count)
            //{
	            var i=0;
	            var htmldata = "";
	            for(i=0;i<count;i++)
	            {
	            var ext = response.dglist.file[i].substr(response.dglist.file[i].lastIndexOf('.') + 1);
	            
	            if(response.dglist.expiry[i] != null)
	            {
	            	if((response.dglist.expiry[i]) <= today)
	            	{
	            		var class_used = "class='box box-danger box-solid collapsed-box'";
	            	} 
	            	else
	            	{
	            		var class_used = "class='box box-success box-solid collapsed-box'";
	            	}
	            }
	            else
	            { 
	            	var class_used = "class='box box-success box-solid collapsed-box'";
	            }
				htmldata += "<div "+class_used+"><div class='box-header with-border'><h3 class='box-title'><a href='documentManagementView?docno=0&amp;id=0&amp;name="+response.dglist.file[i]+"&page=content'>"+response.dglist.name[i]+'.'+ext+"</a></h3><br><span class='description'>&bull;<li>&nbsp;&nbsp;{{$language['doc no']}}: "+response.dglist.number[i]+"</li>&nbsp;&nbsp;&bull;<li>&nbsp;&nbsp;{{$language['created at']}}: "+response.dglist.created[i]+"</li>&nbsp;&nbsp;&bull;<li>&nbsp;&nbsp;{{$language['last updated at']}}: "+response.dglist.updated[i]+"</li>&nbsp;&nbsp;&bull;<li>&nbsp;&nbsp;{{$language['ownership']}}: "+response.dglist.owner[i]+"</li>&nbsp;&nbsp;&bull;<li>&nbsp;&nbsp;{{$language['expir_date']}}: "+response.dglist.expiry[i]+"</li></span><div class='box-tools pull-right'><button type='button' class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-plus'></i></button></div></div><div class='box-body' style='display: none;overflow-x: auto;max-height: 370px;'><div class='content context'><p>"+response.dglist.content[i]+"</p></div></div></div>";
				}
				if(htmldata != "")
				{
					$('#section2').css("display", "block");
            		$("#results").append(htmldata);
            		highlightText(content_word);
            	}
            	exportedRecords += chunkSize;
            //}
            noRecords(exportedRecords,total_results,totalRecords);
        }
    });
}
	function noRecords(exportedRecords,total_results,totalRecords){
	// console.log(exportedRecords);
	// console.log(totalRecords);
	if(exportedRecords>=totalRecords){ 
		if(total_results == 0)
		{
			$('#section2').css("display", "none");
			$(".progress_section").css("display", "none");
		   	var no_data = "<div class='callout callout-danger'><h4>{{$language['No results found']}}.</h4></div>"; 
		   	$("#results").append(no_data);      
		}
	}
	}
//highlight first search
function highlightText($input) 
{
	var $context = $(".context");
	var $form = $('#contentform');
	var searchTerm = $input;
	var options = {};
	var values = $form.serializeArray();
	values = values.concat(
	$form.find("input[type='checkbox']:not(:checked)").map(
		function() {
		  return {
		    "name": this.name,
		    "value": "false"
		  }
		}).get()
	);
	$.each(values, function(i, opt){
		var key = opt.name;
		var val = opt.value;
		if(key === "keyword" || !val){
		return;
		}
		if(val === "false"){
		val = false;
		} else if(val === "true"){
		val = true;
		}
		options[key] = val;
	});
	$context.unmark();
	$context.mark(searchTerm, options);
};
//on click the search button content search
$("#perform").click(function()
{
	var $context = $(".context");
	var $form = $('#contentform');
	// Determine search term
	var searchTerm = $('#keyword').val();
	// Determine options
	var options = {};
	var values = $form.serializeArray();
	/* Because serializeArray() ignores unset checkboxes */
	values = values.concat(
	$form.find("input[type='checkbox']:not(:checked)").map(
	function() {
	  return {
	    "name": this.name,
	    "value": "false"
	  }
	}).get()
	);
	$.each(values, function(i, opt){
	var key = opt.name;
	var val = opt.value;
	if(key === "keyword" || !val){
	return;
	}
	if(val === "false"){
	val = false;
	} else if(val === "true"){
	val = true;
	}
	options[key] = val;
	});

	$context.unmark();
	$context.mark(searchTerm, options);

	});
	//calender
	// get the html doc type columnname by doctype when reload it for PREVIOUS DATA
        var d           = new Date();
        var currentYear = d.getFullYear();
        var newDate     = currentYear+10;
        var date        = '12/31/'+newDate;
		$('#created_date_from').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
            maxDate: moment(date),
            showDropdowns: true
        });
        $('#created_date_to').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
            maxDate: moment(date),
            showDropdowns: true
        });
        $('#last_modified_from').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
            maxDate: moment(date),
            showDropdowns: true
        });
        $('#last_modified_to').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
             maxDate: moment(date),
            showDropdowns: true
        });
</script>
	<style type="text/css">
	.context mark {
		  background: orange;
		  padding: 0;
		}
	.description li{
		display: inline;
	}
	@media(max-width: 740px){
		#accurate{
			margin-top: 0px !important;
		}
		#search_term{
			margin-top: 0px !important;
		}
	}
	</style>
@endsection