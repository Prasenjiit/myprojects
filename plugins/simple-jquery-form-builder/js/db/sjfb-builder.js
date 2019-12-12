/**
 * Simple Jquery Form Builder (SJFB)
 * Copyright (c) 2015 Brandon Hoover, Hoover Web Development LLC (http://bhoover.com)
 * http://bhoover.com/simple-jquery-form-builder/
 * SJFB may be freely distributed under the included MIT license (license.txt).
 */

$(function(){

    //If loading a saved form from your database, put the ID here. Example id is "1".
    var formID = $('#form_id').val();
    var loadformurl = $('#loadformurl').val();

    //Adds new field with animation
    $(".add-field").click(function() {
        event.preventDefault();
        
        $(".alert_from").remove();
        var myArray = {type: $(this).data('type'), type_name: $(this).data('typename'), is_required: $(this).data('is_required'), is_options: $(this).data('is_options'), type_common: $(this).data('type_common')};
        $(addField(myArray)).appendTo('#form-fields').hide().slideDown('fast');
        $('#form-fields').sortable();
    });


   

    //Removes fields and choices with animation
    $("#sjfb").on("click", ".delete_row", function() {
        var field = $(this);
        swal({
              title: "Do you want to delete this field?",
              text: "Do you want to continue!",
              type: "warning",
              showCancelButton: true
            }).then(function (result) {
                if(result){
                    field.parent().slideUp( "fast", function() {
                        /*$this.parent().remove();*/
                        field.closest(".form_row").remove();
                    });
                }
            });
    });

    //Makes fields required
    $("#sjfb").on("click", ".toggle-required", function() {
        requiredField($(this));
    });

    //Makes choices selected
    $("#sjfb").on("click", ".toggle-selected", function() {
        selectedChoice($(this));
    });

    //Makes file multiple upload
    $("#sjfb").on("click", ".toggle-multiple", function() {
        multipleField($(this));
    });
    //Adds new choice to field with animation
    $("#sjfb").on("click", ".add-choice", function() {
        $(addChoice()).appendTo($(this).prev()).hide().slideDown('fast');
        /*$(addChoice()).appendTo($(this).closest("ul")).hide().slideDown('fast');*/
        $('.choices ul').sortable();
    });

    //Saving form
    $('.save_dy_form').click(function(){  
        $(".preloader").css("display", "block");
        $(".save_dy_form").attr("disabled", true);  
        var return_form = $(this).attr('id');
        console.log(return_form);  
        $('.alert_space').html(''); 
        var validate = $('#sjfb').parsley().validate();
        var numItems = $('.field-label').length;
        console.log("numItems"+numItems);
        if(validate)
        {    
        //Loop through fields and save field data to array
        if(numItems == 0)
        {
         var alertmsg='<p class="text-danger text-center alert_from">Please select at least one form component here</p>'
         $('#form-fields').html(alertmsg); 
         return true; 
        }
        var fields = [];

        $('.field').each(function() {

            var $this = $(this);

            //field type
            var fieldType = $this.data('type');

            //field label
            var fieldLabel = $this.find('.field-label').val();

            //field required
            var fieldReq = $this.hasClass('required') ? 1 : 0;

            //field file multiple upload
            var fieldMultiple = $this.hasClass('multiple') ? 1 : 0;

            var choices = [];    
            //check if this field has choices
            if($this.find('.choices li').length >= 1) {

                

                $this.find('.choices li').each(function() {

                    var $thisChoice = $(this);

                    //choice label
                    var choiceLabel = $thisChoice.find('.choice-label').val();

                    //choice selected
                    var choiceSel = $thisChoice.hasClass('selected') ? 1 : 0;

                    choices.push({
                        label: choiceLabel,
                        sel: choiceSel
                    });

                });
            }

            fields.push({
                type: fieldType,
                label: fieldLabel,
                req: fieldReq,
                multiple: fieldMultiple,
                choices: choices
            });

        });

        var frontEndFormHTML = '';
        //Save form to database
        //Demo doesn't actually save. Download project files for save
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_url = $('#form_url').val();
        var return_form_url = $('#return_form_url').val();

        //multiple select values
        var view_departmentid = $("#view_departmentid").val();
        

        var view_userid = $("#view_userid").val();
        

        var edit_departmentid = $("#edit_departmentid").val();
        

        var edit_userid = $("#edit_userid").val();
        

        var delete_departmentid = $("#delete_departmentid").val();
        

        var delete_userid = $("#delete_userid").val();
        

        var add_departmentid = $("#add_departmentid").val();
        

        var add_userid = $("#add_userid").val();
        

        /*var data = JSON.stringify([{_token:CSRF_TOKEN},{"name":"formID","value":formID},{"name":"formFields","value":fields}]);*/
        var data = {_token: CSRF_TOKEN,
            "form_id":formID,
            "form_name":$('#form_name').val(),
            "form_description":$('#form_description').val(),
            "assigned_to":$('#assigned_to').val(),
            "workflow_id":$('#workflow_id').val(),
            "activity_id":$('#activity_id').val(),
            "view_departmentid":view_departmentid,
            "view_userid":view_userid,
            "view_authentication":$('#view_authentication').prop('checked'),
            "edit_departmentid":edit_departmentid,
            "edit_userid":edit_userid,
            "edit_authentication":$('#edit_authentication').prop('checked'),
            "delete_departmentid":delete_departmentid,
            "delete_userid":delete_userid,
            "delete_authentication":$('#delete_authentication').prop('checked'),
            "add_departmentid":add_departmentid,
            "add_userid":add_userid,
            "add_authentication":$('#add_authentication').prop('checked'),
            
            "formFields":fields};
        console.log(data);
        
        $.ajax({
            method: "POST",
            url: form_url,
            data: data,
            dataType: 'json',
            success: function (msg) {
                console.log(msg);
                $(".preloader").css("display", "none");
                $(".save_dy_form").attr("disabled", false);  
                $('.alert_space').html(msg.message);
                if(msg.status == 1)
                {
                        formID = msg.form_id;
                        $('#sjfb').parsley().reset();
                        if(return_form == 'save_close')
                        {
                          window.location.href = return_form_url;      
                        }

                }
                
                $("html, body").animate({ scrollTop: 0 }, "fast");

                //Demo only
                //$('.alert textarea').val(JSON.stringify(fields));
            }
        });
    }
    });

    //load saved form
    loadForm(formID);

});

//Add field to builder
function addField(myArray) {
    var fieldType = myArray.type;
    var fieldTypeCommon = myArray.type_common;
    var typeName = myArray.type_name;
    var hasRequired = myArray.is_required;
    var hasChoices = myArray.is_options;
    var includeRequiredHTML = '';
    var includeChoicesHTML = '';
    var returnformITEM = '';
    //multile file upload option
    if(fieldType == 'file')
    {
        includeChoicesHTML = '' +
            '<div class="col-xs-12 choices">' +
            '<ul></ul>' +
            '<label>' +
            '<input class="toggle-multiple" type="checkbox"> Multiple' +
            '</label>' +
            '</div>';
    }
    if (hasRequired) {
        includeRequiredHTML = '' +
            '<label>' +
            '<input class="toggle-required" type="checkbox"> Required' +
            '</label>'
    }

    if (hasChoices) {
        includeChoicesHTML = '' +
            '<div class="col-xs-12 choices">' +
            '<ul></ul>' +
            '<button type="button" class="btn test_button btn-xs add-choice">Add Choice <i class="fa fa-plus"></i></button>' +
            '</div>';
    }

   /* return '' +
        '<div class="field" data-type="' + fieldType + '">' +
        '<button type="button"  class="delete">Delete Field</button>' +
        '<h3>' + fieldType + '</h3>' +
        '<label>Label:' +
        '<input type="text" class="field-label">' +
        '</label>' +
        includeRequiredHTML +
        includeChoicesHTML +
        '</div>'*/


        returnformITEM +='<div class="row form_row field" data-type="' + fieldType + '">';
        returnformITEM +='<div class="form-group">';
        returnformITEM +='<label for="" class="col-sm-2 control-label">' + typeName + ': </label>';
        returnformITEM +='<div class="col-sm-5">';
         returnformITEM +='<input type="text" class="form-control field-label"  value="" placeholder="Field Name">';
        returnformITEM +='</div>';
        returnformITEM +='<div class="col-xs-5">';
        returnformITEM +='<span>';
        returnformITEM +=includeRequiredHTML;
        returnformITEM +='</span>'; 
        returnformITEM +='<span>';
        returnformITEM +='<i class="fa fa-fw fa-trash delete_row" title="Remove Field"></i>';
        returnformITEM +='</span> '; 
        returnformITEM +='<span title="Drag & Drop">';
        returnformITEM +='<div class="grippy"></div>';
        returnformITEM +='</span> ';    
        returnformITEM +='</div>';
        returnformITEM +=includeChoicesHTML;
        returnformITEM +='<div class="col-xs-12"><hr></div>';
        returnformITEM +='</div>';
        returnformITEM +='</div>';
        return returnformITEM;
}

//Make builder field required
function requiredField($this) {
    if (!$this.parents('.field').hasClass('required')) {
        //Field required
        $this.parents('.field').addClass('required');
        $this.attr('checked','checked');
    } else {
        //Field not required
        $this.parents('.field').removeClass('required');
        $this.removeAttr('checked');
    }
}

//Make builder field multiple
function multipleField($this) {
    if (!$this.parents('.field').hasClass('multiple')) {
        //Field required
        $this.parents('.field').addClass('multiple');
        $this.attr('checked','checked');
    } else {
        //Field not required
        $this.parents('.field').removeClass('multiple');
        $this.removeAttr('checked');
    }
}

function selectedChoice($this) {
    if (! $this.parents('li').hasClass('selected')) {

        //Only checkboxes can have more than one item selected at a time
        //If this is not a checkbox group, unselect the choices before selecting
        if ($this.parents('.field').data('type') != 'checkbox') {
            $this.parents('.choices').find('li').removeClass('selected');
            $this.parents('.choices').find('.toggle-selected').not($this).removeAttr('checked');
        }

        //Make selected
        $this.parents('li').addClass('selected');
        $this.attr('checked','checked');

    } else {

        //Unselect
        $this.parents('li').removeClass('selected');
        $this.removeAttr('checked');

    }
}

//Builder HTML for select, radio, and checkbox choices
function addChoice() {
    
    
    returnChoice ='<li>';
    returnChoice +='<div class="row form_row">';
    returnChoice +='<div class="col-sm-2"><label for="" class="pull-right">Choice: </label></div>';
    returnChoice +='<div class="col-sm-4"><input type="text" class="form-control choice-label" value="" placeholder="choice"></div>';
    returnChoice +='<div class="col-sm-4"><span>';
    returnChoice +='<input class="toggle-selected" type="checkbox"> Selected';
    returnChoice +='</span><span>';
    returnChoice +='<i class="fa fa-fw fa-trash delete_row" title="Remove Choice"></i>';
    returnChoice +='</span><span title="Drag & Drop">';
    returnChoice +='<div class="grippysmall"></div>';
    returnChoice +='</span>';
    returnChoice +='</li>';
    return returnChoice;
}

//Loads a saved form from your database into the builder
function loadForm(formID) {
    var loadformurl = $('#loadformurl').val();
    //console.log("loadformurl"+loadformurl);
    $.getJSON(loadformurl+'?form_id=' + formID, function(data) {

        $("#form_name").val(data.form_name); 
        $("#form_description").val(data.form_description); 

        if (data.assigned_users) {
            //go through each assigned users
            $.each( data.assigned_users, function( k, v ) {
                    $(".assigned_to").val(v.form_user_id);
            });
        }
        if(data.assigned_workflows.length > 0)
        {
           $(".activity_row").slideDown();     
        }   
        if (data.assigned_workflows) {
            //go through each assigned users
            $.each( data.assigned_workflows, function( k, v ) {
                    $(".workflow_id").val(v.form_workflow_id);
                    $(".activity_id").val(v.form_activity_id);
            });
        }
        var a = [];
        var b = [];
        var c = [];
        var d = [];
        var e = [];
        var f = [];
        var g = [];
        var h = [];
        $.each(data.form_privilages, function(i, items)
        {
            if(items.privilege_key == 'add'){
                $.each(items.privilege_department_array, function(i, item)
                {
                    if(item != ''){
                    $('#add_departmentid').data('fastselect').setSelectedOption($('#add_departmentid option[value='+item+']').get(0));
                    a.push(item);}
                    
                });
                $("#add_departmentid").val(a);
                $.each(items.privilege_user_array, function(i, item)
                {
                    if(item != ''){
                    $('#add_userid').data('fastselect').setSelectedOption($('#add_userid option[value='+item+']').get(0));
                     b.push(item);}
                });
                $("#add_userid").val(b);
                if(items.privilege_status == 1)
                {
                    $('#add_authentication').prop('checked', true);
                }
            }
            else if(items.privilege_key == 'edit'){
                $.each(items.privilege_department_array, function(i, item)
                {
                    if(item != ''){
                    $('#edit_departmentid').data('fastselect').setSelectedOption($('#edit_departmentid option[value='+item+']').get(0));
                    c.push(item);}
                });
                $("#edit_departmentid").val(c); 
                $.each(items.privilege_user_array, function(i, item)
                {
                    if(item != ''){
                    $('#edit_userid').data('fastselect').setSelectedOption($('#edit_userid option[value='+item+']').get(0));
                    d.push(item);}
                });
                $("#edit_userid").val(d); 
                if(items.privilege_status == 1)
                {
                    $('#edit_authentication').prop('checked', true);
                }
            }
            else if(items.privilege_key == 'delete'){
                $.each(items.privilege_department_array, function(i, item)
                {
                    if(item != ''){
                    $('#delete_departmentid').data('fastselect').setSelectedOption($('#delete_departmentid option[value='+item+']').get(0));
                    e.push(item);}
                });
                $("#delete_departmentid").val(e); 
                $.each(items.privilege_user_array, function(i, item)
                {
                    if(item != ''){
                    $('#delete_userid').data('fastselect').setSelectedOption($('#delete_userid option[value='+item+']').get(0));
                    f.push(item);}
                });
                $("#delete_userid").val(f); 
                if(items.privilege_status == 1)
                {
                    $('#delete_authentication').prop('checked', true);
                }
            }
            else if(items.privilege_key == 'view'){
                $.each(items.privilege_department_array, function(i, item)
                {
                    if(item != ''){
                    $('#view_departmentid').data('fastselect').setSelectedOption($('#view_departmentid option[value='+item+']').get(0));
                    g.push(item);}
                });
                $("#view_departmentid").val(g); 
                $.each(items.privilege_user_array, function(i, item)
                {
                    if(item != ''){
                    $('#view_userid').data('fastselect').setSelectedOption($('#view_userid option[value='+item+']').get(0));
                     h.push(item);}
                });
                $("#view_userid").val(h);
                if(items.privilege_status == 1)
                {
                    $('#view_authentication').prop('checked', true);
                }
            }
        });
        

        if (data.inputs) {
            //go through each saved field object and render the builder
            $.each( data.inputs, function( k, v ) {
                //Add the field
                var myArray = {type: v['type'], type_name: v['input_type_name'], is_required: v['is_required'], is_options: v['is_options'], type_common: v['type_common']};
                $(addField(myArray)).appendTo('#form-fields').hide().slideDown('fast');
                var $currentField = $('#form-fields .field').last();

                //Add the label
                $currentField.find('.field-label').val(v['label']);

                //Is it required?
                if (v['req']) {
                    requiredField($currentField.find('.toggle-required'));
                }

                //Is it required?
                if (v['multiple']) {
                    multipleField($currentField.find('.toggle-multiple'));
                }
                
                //Any choices?
                if (v['choices']) {
                    $.each( v['choices'], function( k, v ) {
                        //add the choices
                        $currentField.find('.choices ul').append(addChoice());

                        //Add the label
                        $currentField.find('.choice-label').last().val(v['label']);

                        //Is it selected?
                        if (v['sel']==1) {
                            console.log("sel="+v['sel']);
                            selectedChoice($currentField.find('.toggle-selected').last());
                        }
                    });
                }

            });

            $('#form-fields').sortable();
            $('.choices ul').sortable();
        }
    });
}
