/**
 * Simple Jquery Form Builder (SJFB)
 * Copyright (c) 2015 Brandon Hoover, Hoover Web Development LLC (http://bhoover.com)
 * http://bhoover.com/simple-jquery-form-builder/
 * SJFB may be freely distributed under the included MIT license (license.txt).
 */

//generates the form HTML
function generateForm(formID,url,action) {
    //empty out the preview area
    //$("#sjfb-fields").empty();
    //console.log(url);
    $.getJSON(url+'?form_id=' + formID+'&action='+action, function(data) {
        if (data.inputs) {
		var user_id = String(data.user_id);
		var user_role = parseInt(data.user_role);
		console.log(data);
            //go through each saved field object and render the form HTML
            $.each( data.inputs, function( k, v ) {
console.log("DFV:"+v['default_value']);
                var fieldType = v['type'];
                var inputId = v['input_id'];
                var typeId = v['type_id'];
                var mult = v['multiple'];
                var is_default_value = v['is_default_value'];
                var is_input_type = v['is_input_type'];
                var default_value = (v['default_value'])?v['default_value']:'';
		var edit_permissions = v['edit_per'];
		console.log("user_role"+user_role);
		console.log("label"+v['label']);
		console.log("innerarray"+$.inArray(user_id,edit_permissions));
		console.log("edit"+edit_permissions);
                //console.log(v);
               
		
		if(($.inArray(user_id,edit_permissions) != -1) || (user_role == 1))
		{
		//Add the field
        if(fieldType !='file')
        {
		  var myArray = {"fieldType": fieldType,"inputId": inputId,"typeId": typeId,"textlabel": v['label'],"default_value": default_value}
          $('#sjfb-fields').append(addFieldHTML(myArray));
        
		var $currentField = $('#sjfb-fields .sjfb-field').last();

		//Add the label
		/*$currentField.find('label').text(v['label']+':');*/
		} 
                //Any choices?
                if (v['choices']) {

                    //var uniqueID = Math.floor(Math.random()*999999)+1;
                    var uniqueID =  v['input_id'];
                    $.each( v['choices'], function( k, v ) {

                        if (fieldType == 'select') {
                            var selected = (v['sel'] == 1) ? ' selected' : '';
                            var choiceHTML = '<option' + selected + '>' + v['label'] + '</option>';
                            $currentField.find(".choices").append(choiceHTML);
                        }

                        else if (fieldType == 'radio') {
                            var selected = (v['sel'] == 1) ? ' checked' : '';
                            var choiceHTML = '<div class="radio"><label><input type="radio" name="' + uniqueID + '[]"' + selected + ' value="' + v['label'] + '">' + v['label'] + '</label></div>';
                            $currentField.find(".choices").append(choiceHTML);
                        }

                        else if (fieldType == 'checkbox') {
                            var selected = (v['sel'] == 1) ? ' checked' : '';
                            var choiceHTML = '<div class="checkbox"><label><input type="checkbox" name="' + uniqueID + '[]"' + selected + ' value="' + v['label'] + '">' + v['label'] + '</label></div>';
                            $currentField.find(".choices").append(choiceHTML);
                        }

                    });
                }

                //Is it required?
                if (v['req']) {
                    if (fieldType == 'text') { $currentField.find("input").prop('required',true).addClass('required-choice') }
                    else if (fieldType == 'email') { $currentField.find("input").prop('required',true).addClass('required-choice') }
                    else if (fieldType == 'file') { /*$currentField.find(".hiddendrop").last().attr('data-req',1)*/ }
                    else if (fieldType == 'number') { $currentField.find("input").prop('required',true).addClass('required-choice') }
                    else if (fieldType == 'date') { $currentField.find("input").prop('required',true).addClass('required-choice') }
                    else if (fieldType == 'textarea') { $currentField.find("textarea").prop('required',true).addClass('required-choice') }
                    else if (fieldType == 'select') { $currentField.find("select").prop('required',true).addClass('required-choice') }
                    else if (fieldType == 'radio') { $currentField.find("input").prop('required',true).addClass('required-choice') }
                    $currentField.addClass('required-field');
                }
                //Is file multiple upload?
                if(v['multiple']==1){
                    if (fieldType == 'file') { /*$currentField.find(".dropzone").last().attr('data-multile',1)*/ }
                }
}
            });
            
//fileupload
        /*var max_file_size = "{{$language['max_upload_size']}}";
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
                url: "formAttachments",
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
                            url: 'deleteAttachments',
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
        });*/

        $('.datetime').daterangepicker({
            singleDatePicker: true,
            "drops": "bottom",
            showDropdowns: true
        });
    }
        //HTML templates for rendering frontend form fields
        function addFieldHTML(myArray) {

            //var uniqueID = Math.floor(Math.random()*999999)+1;
            var fieldType = (typeof myArray.fieldType !== 'undefined')?myArray.fieldType:'';    
            var uniqueID = (typeof myArray.inputId !== 'undefined')?myArray.inputId:'';  
            var typeId = (typeof myArray.typeId !== 'undefined')?myArray.typeId:'';  
            var is_default_value = (typeof myArray.is_default_value !== 'undefined')?myArray.is_default_value:'';  
            var is_input_type = (typeof myArray.is_input_type !== 'undefined')?myArray.is_input_type:''; 
            var textlabel = (typeof myArray.textlabel !== 'undefined')?myArray.textlabel:'';   
            var default_value = (typeof myArray.default_value !== 'undefined')?myArray.default_value:'';   
            switch (fieldType) {

                case 'text':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="text-' + uniqueID + '">'+textlabel+':</label>' +
                        '<input type="text" id="text-' + uniqueID + '" name="' + uniqueID + '" placeholder="'+textlabel+'" class="form-control" value="'+default_value+'">' +
                        '</div>';

                case 'email':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="email-' + uniqueID + '">'+textlabel+':</label>' +
                        '<input type="email" id="email-' + uniqueID + '" name="' + uniqueID + '" class="form-control" placeholder="someone@example.com" onblur="checkEmail('+uniqueID+')" value="'+default_value+'">' +
                        '</div><div id="wrong"></div>';

                case 'number':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="number-' + uniqueID + '">'+textlabel+':</label>' +
                        '<input type="number" id="number-' + uniqueID + '" name="' + uniqueID + '" class="form-control" placeholder="Number">' +
                        '</div>';

                case 'date':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="date-' + uniqueID + '">'+textlabel+':</label>' +
                        '<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="form-control datetime" id="date-' + uniqueID + '" name="' + uniqueID + '" placeholder="YYYY-MM-DD"></div>'+
                        '</div>';

                case 'time':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="time-' + uniqueID + '">'+textlabel+':</label>' +
                        '<input type="time" id="time-' + uniqueID + '" name="' + uniqueID + '" class="form-control">' +
                        '</div>';

                case 'textarea':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-textarea form-group">' +
                        '<label for="textarea-' + uniqueID + '">'+textlabel+':</label>' +
                        '<textarea id="textarea-' + uniqueID + '" name="' + uniqueID + '" class="form-control">'+default_value+'</textarea>' +
                        '</div>';

                case 'select':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-select form-group">' +
                        '<label for="select-' + uniqueID + '">'+textlabel+':</label>' +
                        '<select id="select-' + uniqueID + '" name="' + uniqueID + '[]" class="choices choices-select form-control"></select>' +
                        '</div>';

                case 'radio':
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-radio form-group">' +
                        '<label>'+textlabel+':</label>' +
                        '<div class="choices choices-radio" class="form-control"></div>' +
                        '</div>';

                case 'checkbox':
                    return '' +
                        '<div id="sjfb-checkbox-' + uniqueID + '" class="sjfb-field sjfb-checkbox form-group">' +
                        '<label class="sjfb-label">'+textlabel+':</label>' +
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
                    return '';
                    return '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-file form-group">' +
                        '<label for="filelabel-' + uniqueID + '" id="filelabel-' + uniqueID + '" data="' + uniqueID + '">'+textlabel+':</label>' +
                        '<div id="fileInput-' + uniqueID + '" class="dropzone" data="' + uniqueID + '">'+
                        '</div>'+
                        '<input type="hidden" id="hiddfile-' + uniqueID + '" class="hiddendrop" value="" data="' + uniqueID + '">'+
                        '<p id="span-'+uniqueID+'" style="color:red;"></p>';

                 case 'section':
                    var htm= '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">';
                    htm +='<hr style="border: 1px solid #d2d6de;"/>';
                    htm +=    '</div>';    
                    return htm;    
                    break;    
                case 'heading':
                    var htm= '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">';
                    htm +='<h3 class="text-center" id="filelabel-' + uniqueID + '">'+textlabel+'</h3>';
                    htm +=    '</div>';    
                    return htm;
                    break; 

                 case 'subheading':
                    var htm= '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">';
                    htm +='<h4  class="text-center" id="filelabel-' + uniqueID + '">'+textlabel+'</h4>';
                    htm +=    '</div>';    
                    return htm;
                    break;          
                case 'label':
                    var htm= '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">';
                    htm +='<label for="filelabel-' + uniqueID + '" class="form_label" id="filelabel-' + uniqueID + '">'+textlabel+'</label>';
                    htm +=    '</div>';    
                    return htm;
                    break;  
            }
        }
    });

}
