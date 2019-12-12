/**
 * Simple Jquery Form Builder (SJFB)
 * Copyright (c) 2015 Brandon Hoover, Hoover Web Development LLC (http://bhoover.com)
 * http://bhoover.com/simple-jquery-form-builder/
 * SJFB may be freely distributed under the included MIT license (license.txt).
 */

/*generates the form HTML*/
var generateForm = function(form_array) {
    var formID = (typeof form_array.formID != 'undefined') ? form_array.formID:0;
    var url = (typeof form_array.url != 'undefined') ? form_array.url:'';
    var action = (typeof form_array.action != 'undefined') ? form_array.action:'add';
    var col = (typeof form_array.col != 'undefined') ? form_array.col:'col_1';
    var doc_id = (typeof form_array.doc_id != 'undefined') ? form_array.doc_id:0;
    var url_delete = (typeof form_array.url_delete != 'undefined') ? form_array.url_delete:'';
    var url_form_attach = (typeof form_array.url_form_attach != 'undefined') ? form_array.url_form_attach:'';
    var reference_id = (typeof form_array.reference_id != 'undefined') ? form_array.reference_id:0;

    $.getJSON(url+'?form_id=' + formID+'&doc_id='+doc_id+'&action='+action+'&reference_id='+reference_id, function(data) {
        if (data.inputs) {
            
            var row_start='<div class="row">';
            var row_end='</div>';
            var unique_countr =0;
            var loop_countr =0;
            var col_countr  =0;
            /*go through each saved field object and render the form HTML*/
            var data_length = data.inputs.length;
            $.each( data.inputs, function( k, v ) {
                loop_countr++;
                col_countr++;
                var fieldType = (typeof v['type'] != 'undefined') ? v['type']:'';
                var inputId = (typeof v['input_id'] != 'undefined') ? v['input_id']:0;
                var doc_col_id = (typeof v['col_id'] != 'undefined') ? v['col_id']:0;
                var typeId = (typeof v['type_id'] != 'undefined') ? v['type_id']:0;
                var mult = (typeof v['multiple'] != 'undefined') ? v['multiple']:0;
                var files = (typeof v['files'] != 'undefined') ? v['files']:'';
                var filesizes = (typeof v['filesizes'] != 'undefined') ? v['filesizes']:'';
                if(col == 'col_3')
                {
                    var col_class = (typeof v['col_3'] != 'undefined') ? v['col_3']:'';
                }
                else if(col == 'col_2')
                {
                    var col_class = (typeof v['col_2'] != 'undefined') ? v['col_2']:'';
                }
                else
                {
                    var col_class = (typeof v['col_1'] != 'undefined') ? v['col_1']:'';
                }
                
                var selected_value = (typeof v['selected'] != 'undefined') ? v['selected'].split(','):'';
                if(selected_value)
                {
                    var default_value = selected_value;
                }
                else
                {
                    var default_value = (typeof v['defaults'] != 'undefined') ? v['defaults'].split(','):'';
                }
                

                var link_to_app = (typeof v['link_to_app'] != 'undefined') ? v['link_to_app']:0;
                var link_to_app_column = (typeof v['link_to_app_column'] != 'undefined') ? v['link_to_app_column']:0;
                var auto_complete_url = (typeof v['auto_complete_url'] != 'undefined') ? v['auto_complete_url']:'';
                var data_array = {'fieldType':fieldType,'uniqueID':inputId,'typeId':typeId,'link_to_app':link_to_app,'link_to_app_column':link_to_app_column,'auto_complete_url':auto_complete_url,'default_value':default_value,'doc_col_id':doc_col_id,'files':files,'filesizes':filesizes,'url_delete':url_delete,'action':action};
                
                var html='';
                if(col_countr == 1)
                {
                    unique_countr++;
                    html +='<div class="row" id="frow_'+unique_countr+'">'; /*START ROW*/
                }
              
                console.log("col="+col);
                console.log("col_countr="+col_countr);
                 if(col_countr == 1)    
                {
                     html +='</div>';  /*END ROW*/
                     $('#dynamic_from').append(html);
                }
                if((col == 'col_1' && col_countr == 1) || (col == 'col_2' && col_countr == 2) || (col == 'col_3' && col_countr == 3) || (loop_countr == data_length))    
                {
                     html +='</div>';  /*END ROW*/
                     col_countr=0;
                     
                }

                var html='';
                html +='<div class="'+col_class+'">';
                html +=addFieldHTML(data_array);
                html +='<input type="hidden" name="doc_col_id[]" value="'+doc_col_id+'">';
                html +='</div>';
                console.log(html);
                console.log("col_countr="+col_countr);
                console.log("loop_countr = "+loop_countr+",data_length="+data_length);
                $('#frow_'+unique_countr).append(html);
                var $currentField = $('#frow_'+unique_countr+' .sjfb-field').last();

                //Add the label
                $currentField.find('label').text(v['label']+':');

                //Any choices?
                if (v['choices']) {

                    //var uniqueID = Math.floor(Math.random()*999999)+1;
                    var uniqueID =  v['input_id'];
                    $.each( v['choices'], function( k1, v1 ) {

                        if (fieldType == 'select') {
                            
                            var selected = ($.inArray(v1,default_value) != -1) ? ' selected' : '';
                            var choiceHTML = '<option' + selected + '>' + v1 + '</option>';
                            $currentField.find(".choices").append(choiceHTML);
                        }

                        else if (fieldType == 'radio') {
                            var selected = ($.inArray(v1,default_value) != -1) ? ' checked' : '';/*
                            var choiceHTML = '<div class="radio"><label><input type="radio" name="' + uniqueID + '[]"' + selected + ' value="' + v1 + '">' + v1 + '</label></div>';*/
                            var choiceHTML = '<label class="radio-inline"><input type="radio" name="' + uniqueID + '[]"' + selected + ' value="' + v1 + '">' + v1 + '</label>';
                            $currentField.find(".choices").append(choiceHTML);
                        }

                        else if (fieldType == 'checkbox') {
                            var selected = ($.inArray(v1,default_value) != -1) ? ' checked' : '';
                            var choiceHTML = '<label class="checkbox-inline"><input type="checkbox" name="' + uniqueID + '[]"' + selected + ' value="' + v1 + '">' + v1 + '</label>';
                            $currentField.find(".choices").append(choiceHTML);
                        }

                    });
                }

                //Is it required?
                if (v['req']) {
                    if (fieldType == 'text') { $currentField.find("input").prop('required',true).addClass('required-choice') }
                    else if (fieldType == 'email') { $currentField.find("input").prop('required',true).addClass('required-choice') }
                    else if (fieldType == 'file') { $currentField.find(".hiddendrop").last().attr('data-req',1) }
                    else if (fieldType == 'number') { $currentField.find("input").prop('required',true).addClass('required-choice') }
                    else if (fieldType == 'date') { $currentField.find("input").prop('required',true).addClass('required-choice') }
                    else if (fieldType == 'textarea') { $currentField.find("textarea").prop('required',true).addClass('required-choice') }
                    else if (fieldType == 'select') { $currentField.find("select").prop('required',true).addClass('required-choice') }
                    else if (fieldType == 'radio') { $currentField.find("input").prop('required',true).addClass('required-choice') }
                    $currentField.addClass('required-field');
                }
                //Is file multiple upload?
                if(v['multiple']==1){
                    if (fieldType == 'file') { $currentField.find(".dropzone").last().attr('data-multile',1) }
                }
            });
            $('.auto_complete').each(function() {
                var auto_complete_url = $(this).attr('data-auto_complete_url');
                var link_to_app = $(this).attr('data-link_to_app');
                var link_to_app_column = $(this).attr('data-link_to_app_column');
                $(this).easyAutocomplete({
                    url: function(query) {
                        return auto_complete_url+"?link_to_app="+link_to_app+"&link_to_app_column="+link_to_app_column+"&search=" + query;
                      },
                    getValue: "name"
                });
            });
//fileupload
        var max_file_size = "{{$language['max_upload_size']}}";
        max_file_size = max_file_size.slice(0, -2); //remove MB from string
        Dropzone.autoDiscover = false;
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        //multiple dropzone id get using "dropzone class"
        $('.dropzone').each(function() {
            var unique_id = $(this).attr('data');
            var multiple = $(this).attr('data-multile');
            var required = $(this).attr('data-req');
            var file_label = $('#filelabel-'+unique_id).text();
            var myDropzone = new Dropzone("div#"+this.id, {
                type:'post',
                params: {_token:CSRF_TOKEN,element_id:this.id,form_input_type_name:'File',unique:unique_id,label:file_label},
                url: url_form_attach,
                paramName: 'file',
                addRemoveLinks: true,
                maxFilesize: max_file_size,
                
                removedfile: function(file) {
                  
                if(file.xhr){
                    var item_delete = file.xhr.response;
                    var item_json = $.parseJSON(item_delete); 
                    var element_delete = item_json.element_id;
                    var random_name = item_json.random_name;
                    $('.'+item_json.rand).remove();
                        $.ajax({
                            type: 'POST',
                            url: url_delete,
                            data: {_token:CSRF_TOKEN,name:random_name},
                            sucess: function(data){
                                console.log('success: ' + data);
                               }
                        });
                }
                    var _ref;
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
         
                },

                success:function(file,response,data){
                    var json = $.parseJSON(response);
                    console.log(json); //
                    var name     = json.original_name;
                    var randName = json.random_name;
                    var elementId = json.element_id;
                    var rand = json.rand;
                    var element_unique_id = json.unique_id;
                    var element_label = json.label;
                    var size = json.size;
                    $('#hiddfile-'+unique_id).val(name);
                    var htmls = '<input class="'+rand+'" name="name'+element_unique_id+'[]" type="hidden"  value="'+name+'">';
                    htmls += '<input class="'+rand+'" type="hidden" name="randname'+element_unique_id+'[]"  value="'+randName+'">';
                    htmls += '<input class="'+rand+'" name="elementuniqueid'+element_unique_id+'[]" type="hidden"  value="'+element_unique_id+'">';
                    htmls += '<input class="'+rand+'" name="elementlabel'+element_unique_id+'[]" type="hidden"  value="'+element_label+'">';
                    htmls += '<input class="'+rand+'" name="size'+element_unique_id+'[]" type="hidden"  value="'+size+'">';
                    $('#dynamic_from').append(htmls);
                },
            }); 
            if(multiple != 1)
            {
                myDropzone.options.maxFiles = 1;  
            } 


        });

        $('.datetime').daterangepicker({
            singleDatePicker: true,
            "drops": "bottom",
            showDropdowns: true
        });


    }
        //HTML templates for rendering frontend form fields
        function addFieldHTML(data_array) {

            //var uniqueID = Math.floor(Math.random()*999999)+1;
            var fieldType = (typeof data_array.fieldType != 'undefined') ? data_array.fieldType:'';
            var uniqueID = (typeof data_array.uniqueID != 'undefined') ? data_array.uniqueID:'';
            var typeId = (typeof data_array.typeId != 'undefined') ? data_array.typeId:'';
            var link_to_app = (typeof data_array.link_to_app != 'undefined') ? data_array.link_to_app:0;
            var link_to_app_column = (typeof data_array.link_to_app_column != 'undefined') ? data_array.link_to_app_column:0;
            var auto_complete_url = (typeof data_array.auto_complete_url != 'undefined') ? data_array.auto_complete_url:0;
            var default_value = (typeof data_array.default_value != 'undefined') ? data_array.default_value:'';
            var colId = (typeof data_array.doc_col_id != 'undefined') ? data_array.doc_col_id:0;
            var del_url = (typeof data_array.url_delete != 'undefined') ? data_array.url_delete:'';
            var action = (typeof data_array.action != 'undefined') ? data_array.action:'';    
            var auto_complete = (link_to_app) ? 'auto_complete':'';
            if(fieldType == 'checkbox' || fieldType == 'radio' || fieldType == 'select')
            {
                var auto_complete = (link_to_app) ? 'auto_select':'';
            }
            var files = (typeof data_array.files != 'undefined') ? data_array.files:'';
            var filesizes = (typeof data_array.filesizes != 'undefined') ? data_array.filesizes:'';

            var common_data='data-auto_complete_url="'+auto_complete_url+'" data-link_to_app="'+link_to_app+'" data-link_to_app_column="'+link_to_app_column+'" data-type="'+fieldType+'"';
            switch (fieldType) {

                case 'text':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="text-' + uniqueID + '"></label>' +
                        '<input type="text" id="text-' + uniqueID + '" name="' + uniqueID + '" class="form-control ' + auto_complete + '" ' + common_data + ' value="'+default_value+'">' +
                        '</div>';

                case 'email':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="email-' + uniqueID + '"></label>' +
                        '<input type="email" id="email-' + uniqueID + '" name="' + uniqueID + '" class="form-control ' + auto_complete + '" placeholder="someone@example.com" onblur="checkEmail('+uniqueID+')" value="'+default_value+'" ' + common_data + '>' +
                        '</div><div id="wrong"></div>';

                case 'number':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="number-' + uniqueID + '"></label>' +
                        '<input type="number" id="number-' + uniqueID + '" name="' + uniqueID + '" class="form-control ' + auto_complete + '" placeholder="Number" value="'+default_value+'" ' + common_data + '>' +
                        '</div>';

                case 'date':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="date-' + uniqueID + '"></label>' +
                        '<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="form-control datetime" id="date-' + uniqueID + '" name="' + uniqueID + '" value="'+default_value+'" placeholder="YYYY-MM-DD"></div>'+
                        '</div>';

                case 'time':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="time-' + uniqueID + '"></label>' +
                        '<input type="time" id="time-' + uniqueID + '" name="' + uniqueID + '" class="form-control" value="'+default_value+'">' +
                        '</div>';

                case 'textarea':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-textarea form-group">' +
                        '<label for="textarea-' + uniqueID + '"></label>' +
                        '<textarea id="textarea-' + uniqueID + '" name="' + uniqueID + '" class="form-control ' + auto_complete + '">'+default_value+'</textarea>' +
                        '</div>';

                case 'select':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-select form-group">' +
                        '<label for="select-' + uniqueID + '"></label>' +
                        '<select id="select-' + uniqueID + '" name="' + uniqueID + '[]" class="choices choices-select form-control"></select>' +
                        '</div>';

                case 'radio':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-radio form-group">' +
                        '<label></label>' +
                        '<div class="choices choices-radio" class="form-control"></div>' +
                        '</div>';

                case 'checkbox':
                    return '' +
                        '<div id="sjfb-checkbox-' + uniqueID + '" class="sjfb-field sjfb-checkbox form-group">' +
                        '<label class="sjfb-label"></label>' +
                        '<div class="choices choices-checkbox" class="form-control"></div>' +
                        '</div>';
                    
                  
                    
                case 'agree':
                    return '' +
                        // '<div id="sjfb-agree-' + uniqueID + '" class="sjfb-field sjfb-agree required-field form-group">' +
                        // '<input type="checkbox" name="'+uniqueID+'" required>' +
                        // '<label></label>' +
                        // '</div>'
                        '<div id="sjfb-checkbox-' + uniqueID + '" class="sjfb-field">' +
                        '<div class="checkbox"><input type="checkbox" name="'+uniqueID+'" required style="margin-left: 0px;"><label class="sjfb-label"></label></div>'+
                        '</div>';
                //Normal file upload(without dropzone)

                // case 'file':
                //     return '' +
                //         '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-file form-group">' +
                //         '<label for="number-' + uniqueID + '"></label>' +
                //         '<input type="file" id="file-' + uniqueID + '" name="' + uniqueID + '[]" onchange="updateList('+uniqueID+');" >' +
                //         '<br/>Selected files:<div id="fileList-' + uniqueID + '"></div>'+
                //         '</div>';

                case 'file':
                console.log("default_value : "+default_value);
                if(default_value.length > 0)
                {
                    var array_file_original = default_value.toString().split(",");
                }
                else
                {
                    var array_file_original = [];
                }
                if(files.length > 0)
                {
                    var array_file_renamed = files.toString().split(",");
                }
                else
                {
                    var array_file_renamed = [];
                }
                if(filesizes.length > 0)
                {
                    var array_sizes = filesizes.toString().split(",");
                }
                else
                {
                    var array_sizes = [];
                }
                  /*  return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-file form-group">' +
                        '<label for="filelabel-' + uniqueID + '" id="filelabel-' + uniqueID + '" data="' + uniqueID + '"></label>' +
                        '<div id="fileInput-' + uniqueID + '" class="dropzone" data="' + uniqueID + '">'+
                        '</div>'+
                        '<input type="hidden" id="hiddfile-' + uniqueID + '" class="hiddendrop" value="" data="' + uniqueID + '">'+
                        '<p id="span-'+uniqueID+'" style="color:red;"></p>';*/
                    var html =  '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-file form-group">' +
                        '<label for="filelabel-' + uniqueID + '" id="filelabel-' + uniqueID + '" data="' + uniqueID + '"></label>';
                        if(action == 'edit')
                        {
                            html +='<div id="fileExist-' + uniqueID + '" class="delete-attach" data="' + uniqueID + '"><p style="font-weight: 600;"><u>Previously uploaded attachments:</u></p><ul>';

                        
                        for(var i=0;i<array_file_original.length;i++)
                        {
                        if(array_file_renamed[i])
                        {
                         html +='<li id="del_li'+colId+'" col="'+colId+'" style="text-transform: capitalize;">'+array_file_original[i]+'&nbsp;&nbsp;&nbsp;';
                         html +='<a><i org_name="'+array_file_original[i]+'" re_name="'+array_file_renamed[i]+'" colId='+colId+' del_url="'+del_url+'" file="'+array_file_renamed[i]+'"  id="del'+colId+'" class="fa fa-trash deleteattachtag" style="color: red;cursor: pointer;" title="Delete attached document"></i></a>';
                         html +='&nbsp;&nbsp;';
                         var cnt = i+1;
                         var data_attr = 'data-id="'+cnt+'" data-dcno="'+colId+'" data-dcname="'+array_file_renamed[i]+'" data-doc_file_name="'+array_file_renamed[i]+'" data-exprstatus="0" data-toplabel="" data-type="attachment"';
                         html +='<a class="view_attachment" href="'+base_url+'/documentManagementView?dcno=' + colId + '&file=' + array_file_renamed[i] + '&id=' + colId + '&size=' + array_sizes[i] + '&page=apps" target="_blank" '+data_attr+'><i file="'+array_file_renamed[i]+'" id="view'+colId+'" class="fa fa-eye" style="color: #3c8dbc;cursor: pointer;" title="View attached document"></i></a></li>';
                         html +='<input type="hidden" id="attachment-' + uniqueID + '" class="hiddendrop" value="" data="' + uniqueID + '">';       
                        }
                        }
                        html +='</ul></div>';
                        }
                        html +='<div id="fileInput-' + uniqueID + '" class="dropzone" data="' + uniqueID + '">'+
                        '</div>';
                        html +='<p style="font-size:12px; color:#999;">Existing files will be removed when new files are added</p>';

                        html +='<input type="hidden" id="hiddfile-' + uniqueID + '" class="hiddendrop" value="" data="' + uniqueID + '">'+
                        '<p id="span-'+uniqueID+'" style="color:red;"></p>';
                        return html;

            }
        }
    });
}

//Delete single document
$(document).on("click",".deleteattachtag",function(e)
{   
    var docname = $(this).attr('org_name');
    var filename = $(this).attr('re_name');
    var colId = $(this).attr('colId');
    var del_url = $(this).attr('del_url');
    if(docname=="")
    {
        docname="Document";
    }
    swal({
          title: "Do you want to delete '" + docname + "' ?",
          text: "Do you want to continue!",
          type: "warning",
          showCancelButton: true
        }).then(function (result) {
        if(result)
        {
            // Success
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'post',
                url: del_url,
                data: {_token:CSRF_TOKEN,name:filename,original_name:docname,colId:colId},
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
                        title: "Document '"+docname+"' deleted successfully",
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ok'
                        }).then(function (result) {
                            if(result){
                                // Success
                                $('#del'+colId).hide();
                                $('#del_li'+colId).hide();
                            }
                        });
                    }
                    else
                    {
                        // data=0
                        swal("File not existing");
                    }
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
