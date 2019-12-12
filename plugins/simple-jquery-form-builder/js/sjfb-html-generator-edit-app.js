/**
 * Simple Jquery Form Builder (SJFB)
 * Copyright (c) 2015 Brandon Hoover, Hoover Web Development LLC (http://bhoover.com)
 * http://bhoover.com/simple-jquery-form-builder/
 * SJFB may be freely distributed under the included MIT license (license.txt).
 */

//generates the form HTML
function generateFormEdit(formID,url,action,doc,del_url,attch_url) 
{
    //empty out the preview area
    //$("#sjfb-fields").empty();
    //console.log(url);
    $.getJSON(url+'?form_id=' + formID+'&action='+action+'&doc='+doc, function(data) 
    {
        if (data.inputs) 
        {
            //go through each saved field object and render the form HTML
            $.each( data.inputs, function( k, v ) 
            {

                var fieldType = v['type'];
                var inputId = v['input_id'];
                var colId = v['col_id'];
                var typeId = v['type_id'];
                var mult = v['multiple'];
                var val = v['values'];
                selected_value = v['selected'].split(',');
                var filesizes = v['sizes'];
                var files = v['files'];
                var res_id = v['res_id'];
                console.log(files);
                //console.log(v);
                console.log("inputId = "+inputId);
                var link_to_app = (typeof v['link_to_app'] != 'undefined') ? v['link_to_app']:0;
                var link_to_app_column = (typeof v['link_to_app_column'] != 'undefined') ? v['link_to_app_column']:0;
                var auto_complete_url = (typeof v['auto_complete_url'] != 'undefined') ? v['auto_complete_url']:'';
                var data_array = {'fieldType':fieldType,'uniqueID':inputId,'typeId':typeId,'link_to_app':link_to_app,'link_to_app_column':link_to_app_column,'auto_complete_url':auto_complete_url,'val':val,'files':files,'res_id':res_id,'colId':colId,'filesizes':filesizes};
                
                $('#sjfb-fields').append(addFieldHTML(data_array));
                var $currentField = $('#sjfb-fields .sjfb-field').last();

                //Add the label
                $currentField.find('label').text(v['label']+':');

                //Any choices?
                if (v['choices']) {

                    //var uniqueID = Math.floor(Math.random()*999999)+1;
                    var uniqueID =  v['input_id'];
                    $.each( v['choices'], function( k1, v1 ) {

                        if (fieldType == 'select') {
                            var selected = ($.inArray(v1,selected_value) != -1) ? ' selected' : '';
                            var choiceHTML = '<option' + selected + '>' + v1 + '</option>';
                            $currentField.find(".choices").append(choiceHTML);
                        }

                        else if (fieldType == 'radio') {
                            var selected = ($.inArray(v1,selected_value) != -1) ? ' checked' : '';
                            var choiceHTML = '<div class="radio"><label><input type="radio" name="' + uniqueID + '[]"' + selected + ' value="' + v1 + '">' + v1 + '</label></div>';
                            $currentField.find(".choices").append(choiceHTML);
                        }

                        else if (fieldType == 'checkbox') {
                            var selected = ($.inArray(v1,selected_value) != -1) ? ' checked' : '';
                            var choiceHTML = '<div class="checkbox"><label><input type="checkbox" name="' + uniqueID + '[]"' + selected + ' value="' + v1 + '">' + v1 + '</label></div>';
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
            
            $('.dropzone').each(function() 
            {
                var dropzone_id = $(this).attr('id');
                var unique_id = $(this).attr('data');
                var multiple = $(this).attr('data-multile');
                var required = $(this).attr('data-req');
                var file_label = $('#filelabel-'+unique_id).text();
                
                var myDropzone = new Dropzone("#"+this.id, {
                    type:'post',
                    params: {_token:CSRF_TOKEN,element_id:this.id,form_input_type_name:'File',unique:unique_id,label:file_label},
                    url: attch_url,
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
                                url: '/deleteAttachments',
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
                        var html = '<input class="'+rand+'" name="name'+element_unique_id+'[]" type="hidden"  value="'+name+'"><input class="'+rand+'" type="hidden" name="randname'+element_unique_id+'[]"  value="'+randName+'"><input class="'+rand+'" name="elementuniqueid'+element_unique_id+'[]" type="hidden"  value="'+element_unique_id+'"><input class="'+rand+'" name="elementlabel'+element_unique_id+'[]" type="hidden"  value="'+element_label+'"><input class="'+rand+'" name="size'+element_unique_id+'[]" type="hidden"  value="'+size+'">';
                        $('#div-append').append(html);
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
        function addFieldHTML(data_array) 
        {

            //var uniqueID = Math.floor(Math.random()*999999)+1;
            var fieldType = (typeof data_array.fieldType != 'undefined') ? data_array.fieldType:'';
            var uniqueID = (typeof data_array.uniqueID != 'undefined') ? data_array.uniqueID:'';
            var typeId = (typeof data_array.typeId != 'undefined') ? data_array.typeId:'';
            var val = (typeof data_array.val != 'undefined') ? data_array.val:'';
            var files = (typeof data_array.files != 'undefined') ? data_array.files:'';
            var res_id = (typeof data_array.res_id != 'undefined') ? data_array.res_id:'';
            var colId = (typeof data_array.colId != 'undefined') ? data_array.colId:'';
            var filesizes = (typeof data_array.filesizes != 'undefined') ? data_array.filesizes:'';

            var link_to_app = (typeof data_array.link_to_app != 'undefined') ? data_array.link_to_app:0;
            var link_to_app_column = (typeof data_array.link_to_app_column != 'undefined') ? data_array.link_to_app_column:0;
            var auto_complete_url = (typeof data_array.auto_complete_url != 'undefined') ? data_array.auto_complete_url:0;
            
            var auto_complete = (link_to_app) ? 'auto_complete':'';
            var common_data='data-auto_complete_url="'+auto_complete_url+'" data-link_to_app="'+link_to_app+'" data-link_to_app_column="'+link_to_app_column+'"';
            switch (fieldType) {

                case 'text':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="text-' + uniqueID + '"></label>' +
                        '<input type="text" id="text-' + uniqueID + '" name="' + uniqueID + '" class="form-control ' + auto_complete + '" ' + common_data + ' value="'+ val +'">' +
                        '<input type="hidden" name="resp_id[]" value="'+res_id+'">'+
                        '</div>';

                case 'email':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="email-' + uniqueID + '"></label>' +
                        '<input type="email" id="email-' + uniqueID + '" name="' + uniqueID + '" class="form-control ' + auto_complete + '" ' + common_data + ' placeholder="someone@example.com" onblur="checkEmail('+uniqueID+')" value="'+ val +'">' +
                        '<input type="hidden" name="resp_id[]" value="'+res_id+'">'+
                        '</div><div id="wrong"></div>';

                case 'number':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="number-' + uniqueID + '"></label>' +
                        '<input type="number" id="number-' + uniqueID + '" name="' + uniqueID + '" class="form-control ' + auto_complete + '" ' + common_data + ' placeholder="Number" value="'+ val +'">' +
                        '<input type="hidden" name="resp_id[]" value="'+res_id+'">'+
                        '</div>';

                case 'date':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="date-' + uniqueID + '"></label>' +
                        '<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="form-control datetime" id="date-' + uniqueID + '" name="' + uniqueID + '" placeholder="YYYY-MM-DD" value="'+ val +'"></div>'+
                        '<input type="hidden" name="resp_id[]" value="'+res_id+'">'+
                        '</div>';

                case 'time':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="time-' + uniqueID + '"></label>' +
                        '<input type="time" id="time-' + uniqueID + '" name="' + uniqueID + '" class="form-control" value="'+ val +'">' +
                        '<input type="hidden" name="resp_id[]" value="'+res_id+'">'+
                        '</div>';

                case 'textarea':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-textarea form-group">' +
                        '<label for="textarea-' + uniqueID + '"></label>' +
                        '<textarea id="textarea-' + uniqueID + '" name="' + uniqueID + '" class="form-control">'+val+'</textarea>' +
                        '<input type="hidden" name="resp_id[]" value="'+res_id+'">'+
                        '</div>';

                case 'select':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-select form-group">' +
                        '<label for="select-' + uniqueID + '"></label>' +
                        '<select id="select-' + uniqueID + '" name="' + uniqueID + '[]" class="choices choices-select form-control"></select>' +
                        '<input type="hidden" name="resp_id[]" value="'+res_id+'">'+
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
                        '<div class="checkbox"><input type="checkbox" name="'+uniqueID+'" required style="margin-left: 0px;">'+
                        '<input type="hidden" name="resp_id[]" value="'+res_id+'">'+
                        '<label class="sjfb-label"></label></div>'+
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
                if(val != null)
                {
                    var array_file_original = val.split(",");
                }
                else
                {
                    var array_file_original = [''];
                }
                if(files != null)
                {
                    var array_file_renamed = files.split(",");
                }
                else
                {
                    var array_file_renamed = [''];
                }
                if(filesizes != null)
                {
                    var array_sizes = filesizes.split(",");
                }
                else
                {
                    var array_sizes = [''];
                }
                    var html =  '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-file form-group">' +
                        '<label for="filelabel-' + uniqueID + '" id="filelabel-' + uniqueID + '" data="' + uniqueID + '"></label>' +
                        '<div id="fileExist-' + uniqueID + '" class="delete-attach" data="' + uniqueID + '"><p style="font-weight: 600;"><u>Previously uploaded attachments:</u></p><ul>';
                        for(var i=0;i<array_file_original.length;i++){
                        if((array_file_renamed[i] =="") || (array_file_renamed[i] == null)){
                            html +='';
                        }
                        else
                        {
                         html +='<li id="del_li'+colId+'" col="'+colId+'" style="text-transform: capitalize;">'+array_file_original[i]+'&nbsp;&nbsp;&nbsp;';
                         html +='<a><i org_name="'+array_file_original[i]+'" re_name="'+array_file_renamed[i]+'" colId='+colId+' del_url="'+del_url+'" file="'+array_file_renamed[i]+'" res_id="'+res_id+'" id="del'+colId+'" class="fa fa-trash deleteattachtag" style="color: red;cursor: pointer;" title="Delete attached document"></i></a>';
                         html +='&nbsp;&nbsp;';
                         var cnt = i+1;
                         var data_attr = 'data-id="'+cnt+'" data-dcno="'+colId+'" data-dcname="'+array_file_renamed[i]+'" data-doc_file_name="'+array_file_renamed[i]+'" data-exprstatus="0" data-toplabel="" data-type="attachment"';
                         html +='<a class="view_attachment" href="'+base_url+'/documentManagementView?dcno=' + colId + '&file=' + array_file_renamed[i] + '&id=' + colId + '&size=' + array_sizes[i] + '&page=apps" target="_blank" '+data_attr+'><i file="'+array_file_renamed[i]+'" res_id="'+res_id+'" id="view'+colId+'" class="fa fa-eye" style="color: #3c8dbc;cursor: pointer;" title="View attached document"></i></a></li>';
                        }
                        }
                        html +='</ul></div>'+
                        '<div id="fileInput-' + uniqueID + '" class="dropzone" data="' + uniqueID + '">'+
                        '</div>'+
                        '<p style="font-size:12px; color:#999;">Existing files will be removed when new files are added</p>'+
                        '<input type="hidden" id="hiddfile-' + uniqueID + '" class="hiddendrop" value="" data="' + uniqueID + '">'+
                        '<input type="hidden" name="resp_id[]" value="'+res_id+'">'+
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
