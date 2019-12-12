/**
 * Simple Jquery Form Builder (SJFB)
 * Copyright (c) 2015 Brandon Hoover, Hoover Web Development LLC (http://bhoover.com)
 * http://bhoover.com/simple-jquery-form-builder/
 * SJFB may be freely distributed under the included MIT license (license.txt).
 */

//generates the form HTML
function generateFormEdit(formID,url,action,unique,del_url,attch_url,activity_note_flag) 
{
    //empty out the preview area
    //$("#sjfb-fields").empty();
    $('#sjfb-fields').html();
    //console.log(url);
    $.getJSON(url+'?form_id=' + formID+'&action='+action+'&uq_id='+unique, function(data) 
    {
        if (data.inputs) 
        {
		var user_id = String(data.user_id);
		var user_role = parseInt(data.user_role);
        /*console.log(user_role);*/
		/*console.log(data);*/
		//console.log(data.inputs);
        //go through each saved field object and render the form HTML
        $.each( data.inputs, function( k, v ) 
        {

                var fieldType = v['type'];
                var inputId = v['input_id'];
                var typeId = v['type_id'];
                var mult = v['multiple'];
                var val = v['values'];
                var is_input_type = v['is_input_type'];
                if(val == null)
                {
                 val ='';
                }
                
                var edit_permissions = v['edit_per'];
                var view_permissions = v['view_per'];
                var files = v['files'];
                var res_id = v['res_id'];
                var selected_item = v['selected_item'];
                console.log("label"+v['label']);
                console.log("selected_item SSS="+selected_item);
                /*console.log("user_role"+user_role);*/
        		
        		/*console.log("innerarray"+$.inArray(user_id,edit_permissions));*/
        		/*console.log("edit"+edit_permissions);
                console.log("view"+view_permissions);*/
		
		
	    
		if(($.inArray(user_id,edit_permissions) != -1) || ($.inArray(user_id,view_permissions) != -1) || (user_role == 1))
		{
            var read_only = 1;
            var read_only_text = 'disabled';
            if(($.inArray(user_id,edit_permissions) != -1 && activity_note_flag == 1) || (user_role == 1 && activity_note_flag == 1))
            {
                var read_only = 0;
                read_only_text = '';
            }
            console.log("read_only"+read_only);
    		//add fields
           var myArray = {"fieldType": fieldType,"inputId": inputId,"typeId": typeId,"textlabel": v['label'],"is_input_type": is_input_type,"val": val,"files": files,"res_id": res_id,"read_only": read_only}
	$('#sjfb-fields').append('<input type="hidden" name="action" value="'+action+'">');
           $('#sjfb-fields').append(addFieldHTML(myArray));
    		var $currentField = $('#sjfb-fields .sjfb-field').last();

    		//Add the label
    		//$currentField.find('label').text(v['label']+':');
			
            //Any choices?
            if (v['choices']) {

                //var uniqueID = Math.floor(Math.random()*999999)+1;
                var uniqueID =  v['input_id'];
                $.each( v['choices'], function( k1, v1 ) {
                    console.log("label"+v1['label']);
                    console.log("selected_item"+selected_item);
                    console.log("Inarray option ="+$.inArray(v1['label'],selected_item));
                    if (fieldType == 'select') {
                        var selected = '';

                        if($.inArray(v1['label'],selected_item) != -1)
                        {
                                selected = ' selected';
                        }
                        var choiceHTML = '<option' + selected + '>' + v1['label'] + '</option>';
                        $currentField.find(".choices").append(choiceHTML);
                    }

                    else if (fieldType == 'radio') {
                        /*var selected = (v1['sel'] == 1) ? ' checked' : '';*/
                        var selected = '';
                        if($.inArray(v1['label'],selected_item) != -1)
                        {
                                selected = ' checked';
                        }
                        var choiceHTML = '<div class="radio"><label><input type="radio" name="' + uniqueID + '[]"' + selected + ' value="' + v1['label'] + '" '+read_only_text+'>' + v1['label'] + '</label></div>';
                        $currentField.find(".choices").append(choiceHTML);
                    }

                    else if (fieldType == 'checkbox') {
                       /* var selected = (v1['sel'] == 1) ? ' checked' : '';*/
                        var selected = '';
                        if($.inArray(v1['label'],selected_item) != -1)
                        {
                                selected = ' checked';
                        }
                        var choiceHTML = '<div class="checkbox"><label><input type="checkbox" name="' + uniqueID + '[]"' + selected + ' value="' + v1['label'] + '" '+read_only_text+'>' + v1['label'] + '</label></div>';
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
        }
                
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
                console.log(unique_id);
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
                        /*console.log(json); */
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
        function addFieldHTML(myArray) 
        {
             //fieldType,inputId,typeId,val,files,res_id,read_only   
            //var uniqueID = Math.floor(Math.random()*999999)+1;
            var fieldType = (typeof myArray.fieldType !== 'undefined')?myArray.fieldType:'';    
            var uniqueID = (typeof myArray.inputId !== 'undefined')?myArray.inputId:'';  
            var typeId = (typeof myArray.typeId !== 'undefined')?myArray.typeId:'';  
            var val = (typeof myArray.val !== 'undefined')?myArray.val:'';  
            var files = (typeof myArray.files !== 'undefined')?myArray.files:[]; 
            var textlabel = (typeof myArray.textlabel !== 'undefined')?myArray.textlabel:'';   
            var res_id = (typeof myArray.res_id !== 'undefined')?myArray.res_id:'';  
            var read_only = (typeof myArray.res_id !== 'undefined')?myArray.read_only:'';  
            var read_only_text = '';
            if(read_only ==1)
            {
                read_only_text = 'disabled';
            }
            switch (fieldType) {

                case 'text':
                    
                        var a = '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="text-' + uniqueID + '">'+textlabel+':</label>' +
                        '<input type="text" id="text-' + uniqueID + '" name="' + uniqueID + '" class="form-control" value="'+ val +'" '+read_only_text+'>';
                        if(read_only !=1)
                        {
                            a+='<input type="hidden" name="resp_id[]" value="'+res_id+'">';
                        }
                        a+='</div>';

                        return a;

                case 'email':
                    
                        var a = '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="email-' + uniqueID + '">'+textlabel+':</label>' +
                        '<input type="email" id="email-' + uniqueID + '" name="' + uniqueID + '" class="form-control" placeholder="someone@example.com" onblur="checkEmail('+uniqueID+')" value="'+ val +'" '+read_only_text+'>';
                        if(read_only !=1)
                        {
                            a+='<input type="hidden" name="resp_id[]" value="'+res_id+'">';
                        }
                        a+='</div><div id="wrong"></div>';
                        return a;
                case 'number':
                    
                        var a = '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="number-' + uniqueID + '">'+textlabel+':</label>' +
                        '<input type="number" id="number-' + uniqueID + '" name="' + uniqueID + '" class="form-control" placeholder="Number" value="'+ val +'" '+read_only_text+'>';
                        if(read_only !=1)
                        {
                            a+='<input type="hidden" name="resp_id[]" value="'+res_id+'">';
                        }
                        a+='</div>';
                        return a;
                case 'date':
                    
                        var a = '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="date-' + uniqueID + '">'+textlabel+':</label>' +
                        '<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="form-control datetime" id="date-' + uniqueID + '" name="' + uniqueID + '" placeholder="YYYY-MM-DD" value="'+ val +'" '+read_only_text+'></div>';
                        if(read_only !=1)
                        {
                            a+= '<input type="hidden" name="resp_id[]" value="'+res_id+'">';
                        }
                        a+= '</div>';
                        return a;
                case 'time':
                    
                        var a = '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">' +
                        '<label for="time-' + uniqueID + '">'+textlabel+':</label>' +
                        '<input type="time" id="time-' + uniqueID + '" name="' + uniqueID + '" class="form-control" value="'+ val +'" '+read_only_text+'>';
                        if(read_only !=1)
                        {
                            a+= '<input type="hidden" name="resp_id[]" value="'+res_id+'">';
                        }
                        a+= '</div>';
                        return a;
                case 'textarea':
                    
                        var a = '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-textarea form-group">' +
                        '<label for="textarea-' + uniqueID + '">'+textlabel+':</label>' +
                        '<textarea id="textarea-' + uniqueID + '" name="' + uniqueID + '" class="form-control" '+read_only_text+'>'+val+'</textarea>';
                        if(read_only !=1)
                        {
                            a+= '<input type="hidden" name="resp_id[]" value="'+res_id+'">';
                        }
                        a+= '</div>';
                        return a;
                case 'select':
                    
                        var a = '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-select form-group">' +
                        '<label for="select-' + uniqueID + '">'+textlabel+':</label>' +
                        '<select id="select-' + uniqueID + '" name="' + uniqueID + '[]" class="choices choices-select form-control" '+read_only_text+'></select>';
                        if(read_only !=1)
                        {
                            a+= '<input type="hidden" name="resp_id[]" value="'+res_id+'">';
                        }
                        a+= '</div>';
                        return a;
                case 'radio':
                    
                        var a = '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-radio form-group">' +
                        '<label>'+textlabel+':</label>' +
                        '<div class="choices choices-radio" class="form-control"></div>' +
                        '</div>';
                         if(read_only !=1)
                        {
                            a += '<input type="hidden" name="resp_id[]" value="'+res_id+'">';
                        }
                        return a;
                case 'checkbox':
                    
                        var a = '<div id="sjfb-checkbox-' + uniqueID + '" class="sjfb-field sjfb-checkbox form-group">' +
                        '<label class="sjfb-label">'+textlabel+':</label>' +
                        '<div class="choices choices-checkbox" class="form-control"></div>' +
                        '</div>';
                         if(read_only !=1)
                        {
                            a += '<input type="hidden" name="resp_id[]" value="'+res_id+'">';
                        }
                        return a;
                  
                    
                case 'agree':
                    
                        // '<div id="sjfb-agree-' + uniqueID + '" class="sjfb-field sjfb-agree required-field form-group">' +
                        // '<input type="checkbox" name="'+uniqueID+'" required>' +
                        // '<label></label>' +
                        // '</div>'
                        var a = '<div id="sjfb-checkbox-' + uniqueID + '" class="sjfb-field">' +
                        '<div class="checkbox"><input type="checkbox" name="'+uniqueID+'" required style="margin-left: 0px;" '+read_only_text+'>';
                        if(read_only !=1)
                        {
                            a+= '<input type="hidden" name="resp_id[]" value="'+res_id+'">';
                        }
                        a+= '<label class="sjfb-label"></label></div>'+
                        '</div>';
                        return a;
                 case 'section':
                    var htm= '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">';
                    htm +='<hr style="border: 1px solid #d2d6de;"/>';
                    if(read_only !=1)
                        {
                            htm+='<input type="hidden" name="resp_id[]" value="'+res_id+'">';
                        }
                    htm +=    '</div>';    
                    return htm;    
                    break;    
                case 'heading':
                    var htm= '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">';
                    htm +='<h3 class="text-center" id="filelabel-' + uniqueID + '">'+textlabel+'</h3>';
                    if(read_only !=1)
                        {
                            htm+='<input type="hidden" name="resp_id[]" value="'+res_id+'">';
                        }
                    htm +=    '</div>';    
                    return htm;
                    break; 

                 case 'subheading':
                    var htm= '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">';
                    htm +='<h4  class="text-center" id="filelabel-' + uniqueID + '">'+textlabel+'</h4>';
                    if(read_only !=1)
                        {
                            htm+='<input type="hidden" name="resp_id[]" value="'+res_id+'">';
                        }
                    htm +=    '</div>';    
                    return htm;
                    break;          
                case 'label':
                    var htm= '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">';
                    htm +='<label for="filelabel-' + uniqueID + '" class="form_label" id="filelabel-' + uniqueID + '">'+textlabel+'</label>';
                    if(read_only !=1)
                        {
                            htm+='<input type="hidden" name="resp_id[]" value="'+res_id+'">';
                        }
                    htm +=    '</div>';    
                    return htm;
                    break;         
                //Normal file upload(without dropzone)

                // case 'file':
                //     return '' +
                //         '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-file form-group">' +
                //         '<label for="number-' + uniqueID + '"></label>' +
                //         '<input type="file" id="file-' + uniqueID + '" name="' + uniqueID + '[]" onchange="updateList('+uniqueID+');" >' +
                //         '<br/>Selected files:<div id="fileList-' + uniqueID + '"></div>'+
                //         '</div>';

                case 'file':
                if(read_only ==1)
                {
                    read_only_text = 'style="display:none;"';
                }
                //var array_file_original = val.split(",");
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

                var nil=0;    
                if(read_only ==1)
                {
                    var html= '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-text form-group">';
                    html +='<label for="filelabel-' + uniqueID + '" class="form_label" id="filelabel-' + uniqueID + '">'+textlabel+': ';
                    var file_length = array_file_original.length;
                    for(var i=0;i<file_length;i++){
                        if((array_file_renamed[i] =="") || (array_file_renamed[i] == null))
                        {
                            html +='';
                        }
                        else
                        {
                         
                          nil=1;
                          html +='<sapn style="cursor:pointer;" data-file="'+array_file_renamed[i]+'" dddd="/dms/documentManagementView?dcno='+array_file_renamed[i]+'&file='+array_file_original[i]+'&id='+res_id+'&page=forms" class="view_doc">'+array_file_original[i]+'<i class="fa fa-fw fa-eye" title="View Attached File" ></i></span>';
                          if(i<(file_length-1))
                          {

                          html +=', ';
                          }
                        }
                        } 
                        if(nil == 0)  
                        {
                                html +='---';
                        } 
                    html+='</label>';
                    
                    html +=    '</div>';    
                    return html;
                }
                else
                {
                    var html =  '' +
                        '<div id="sjfb-' + uniqueID + '" class="sjfb-field sjfb-file form-group">' +
                        '<label for="filelabel-' + uniqueID + '" id="filelabel-' + uniqueID + '" data="' + uniqueID + '">'+textlabel+': </label>' +
                        '<div id="fileExist-' + uniqueID + '" class="delete-attach" data="' + uniqueID + '"><p style="font-weight: 600;"><u>Previously uploaded attachments:</u></p><ul>';
                        for(var i=0;i<array_file_original.length;i++){
                        if((array_file_renamed[i] =="") || (array_file_renamed[i] == null)){
                            html +='';
                        }
                        else
                        {
                         
                         html +='<li id="del_li'+uniqueID+'" style="text-transform: capitalize;">'+array_file_original[i]+'&nbsp;&nbsp;';
                          
                          html +='<span style="cursor:pointer;" data-file="'+array_file_renamed[i]+'" href="/apps/public/documentManagementView?dcno='+array_file_renamed[i]+'&file='+array_file_original[i]+'&id='+res_id+'&page=forms" class="view_doc"><i class="fa fa-fw fa-eye" title="View Attached File" ></i></span>';
                          html +='&nbsp;&nbsp;<a><i onclick=del('+res_id+',"'+array_file_original[i]+'","'+array_file_renamed[i]+'",'+uniqueID+',"'+del_url+'"); file="'+array_file_renamed[i]+'" res_id="'+res_id+'" id="del'+uniqueID+'" class="fa fa-trash" style="color: red;cursor: pointer;" title="Delete attached document"></i></a></li>';
                        }
                        }
                        html +='</ul></div>'+
                        '<div id="fileInput-' + uniqueID + '" class="dropzone" data="' + uniqueID + '" '+read_only_text+'>'+
                        '</div>'+
                        '<p style="font-size:12px; color:#999;">Existing files will be removed when new files are added</p>';
                        '<input type="hidden" id="hiddfile-' + uniqueID + '" class="hiddendrop" value="" data="' + uniqueID + '">';
                        if(read_only !=1)
                        {
                            html+='<input type="hidden" name="resp_id[]" value="'+res_id+'">';
                        }
                        html+='<p id="span-'+uniqueID+'" style="color:red;"></p>';
                        return html;
                    }
                        return html;
                
            }
        }
    });
}
//Delete single document
function del(id,docname,filename,unique_id,del_url)
{   
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
                data: {_token:CSRF_TOKEN,name:filename,id:id,original_name:docname},
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
                                $('#del'+unique_id).hide();
                                $('#del_li'+unique_id).hide();
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
}
$(function(){

});
