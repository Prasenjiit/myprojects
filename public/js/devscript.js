    
    // Checking Password Complexity:
    $('body').on('keyup','.password1,.password2' ,function(){  
    	var password = $(this).val();
    	var count = $(this).attr('this_val');
    	var auth  = $(this).attr('is_auth');
    	if(auth){
    		var url = 'getSettingsAuth';// If user is not logged in,go to this function( like reset password link etc )
    	}else{
    		var url = 'getSettings';// If logged in, go to this function(in users module)
    	}
    	
        // If password has space show error
        if(/\s/g.test(password) == true){ 
            $('#space_error'+count).html('The password should not contain any space.');
            $('#password_length_from_error_'+count).html('');
            $('#password_length_to_error_'+count).html('');
            $('#complexity_error_'+count+'alphabets').html('');
            $('#complexity_error_'+count+'numerics').html('');
            $('#complexity_error_'+count+'special_characters').html('');
            $('#complexity_error_'+count+'capital_and_small').html('');
            $('.sav_btn').attr('disabled', true);
            return false;
        }else{
            $('#space_error'+count).html('');
        	$.ajax({
        		type : 'GET',
        		url  : base_url+'/'+url,
        		cache : false,
        		dataType : "json",
        		success:function(response){ // ajax success start
        			
                    var settings_alphabets = response.settings_alphabets;
        			var settings_numerics  = response.settings_numerics;
        			var settings_special_characters = response.settings_special_characters;
        			var settings_capital_and_small  = response.settings_capital_and_small;
                    var settings_password_length_from = response.settings_password_length_from;
                    var settings_password_length_to   = response.settings_password_length_to;

                    // Update password input field with maximum and minimum length
                    $('.password1').attr('data-parsley-minlength',+settings_password_length_from);
                    $('.password1').attr('data-parsley-maxlength',+settings_password_length_to);
                    $('.password2').attr('data-parsley-minlength',+settings_password_length_from);
                    $('.password2').attr('data-parsley-maxlength',+settings_password_length_to);

                    // Checking password length from
                    if(settings_password_length_from){

                        if(password.length < settings_password_length_from){
                           // error
                           $('#password_length_from_error_'+count).html('The password must have '+settings_password_length_from+' to '+settings_password_length_to+' characters');
                        }else{
                            // success
                            $('#password_length_from_error_'+count).html('');
                        }
                        
                    }
                    // Checking password length to
                    if(settings_password_length_to){

                        if(password.length > settings_password_length_to){
                           // error
                           $('#password_length_to_error_'+count).html('The password must have '+settings_password_length_from+' to '+settings_password_length_to+' characters');
                        }else{
                            // success
                            $('#password_length_to_error_'+count).html('');
                        }                 
                    }
                    //<!--Checking password length end--> 

                    //<!--Checking complexity conditions--> 
                    if(settings_alphabets == '1'){
                        // Checking alphabets
                        if( /[a-z]/.test(password) == true || /[A-Z]/.test(password)){
                            // success
                            $('#complexity_error_'+count+'alphabets').html('');
                            $('.password'+count).removeClass('parsley-error');
                            $('.sav_btn').attr('disabled', false);
                        }else{
                            // error
                            $('#complexity_error_'+count+'alphabets').html('The password must contain at least one alphabet');
                            $('.password'+count).addClass('parsley-error');
                            $('.sav_btn').attr('disabled', true);
                        }
                    } else if(settings_alphabets == '0'){
                        $('#complexity_error_'+count+'alphabets').html('');
                        $('.password'+count).removeClass('parsley-error');
                        $('.sav_btn').attr('disabled', false);
                    }

                    if(settings_numerics == '1'){
                        // Checking Numerics
                        var digit = /\d/g;
                        if(password.match(digit)){//success
                            $('#complexity_error_'+count+'numerics').html('');
                            $('.password'+count).removeClass('parsley-error');
                            $('.sav_btn').attr('disabled', false);
                        }else{ // error
                            $('#complexity_error_'+count+'numerics').html('The password must contain at least one number');
                            $('.password'+count).addClass('parsley-error');
                            $('.sav_btn').attr('disabled', true);
                        }
                    } else if(settings_numerics == '0'){
                        $('#complexity_error_'+count+'numerics').html('');
                        $('.password'+count).removeClass('parsley-error');
                        $('.sav_btn').attr('disabled', false);
                    }

                    if(settings_special_characters == '1'){
                        var digit = /\d/g;   
                        if(password.match(/\!/g) || password.match(/\@/g ) || password.match(/\#/g ) || password.match(/\$/g ) || password.match(/\^/g ) || password.match(/\&/g ) || password.match(/\*/g ) || password.match(/\(/g ) || password.match(/\)/g ) || password.match(/\[/g ) || password.match(/\]/g ) ){
                            //success
                            $('#complexity_error_'+count+'special_characters').html('');
                            $('.password'+count).removeClass('parsley-error');
                            $('.sav_btn').attr('disabled', false);
                        }else{
                            //error
                            $('#complexity_error_'+count+'special_characters').html('The password must contain at least one special character');
                            $('.password'+count).addClass('parsley-error');
                            $('.sav_btn').attr('disabled', true);
                        }
                    } else if(settings_special_characters == '0'){
                        $('#complexity_error_'+count+'special_characters').html('');
                        $('.password'+count).removeClass('parsley-error');
                        $('.sav_btn').attr('disabled', false);
                    }

                    if(settings_capital_and_small == '1'){
                        // Has atlist one capital and smoll letter
                        if( /[a-z]/.test(password) == true && /[A-Z]/.test(password)){
                            //success
                            $('#complexity_error_'+count+'capital_and_small').html('');
                            $('.password'+count).removeClass('parsley-error');
                            $('.sav_btn').attr('disabled', false);
                        }else{
                            // error
                            $('#complexity_error_'+count+'capital_and_small').html('The password must contain at least one capital and small letter');
                            $('.password'+count).addClass('parsley-error');
                            $('.sav_btn').attr('disabled', true);
                        }
                    } else if(settings_capital_and_small == '0'){
                        $('#complexity_error_'+count+'capital_and_small').html('');
                        $('.password'+count).removeClass('parsley-error');
                        $('.sav_btn').attr('disabled', false);
                    }
        			//<!--Checking complexity conditions end--> 

        		}// success end
        	});// Ajax end
       
       }
        	
    });

    /*<1--For clear password error duplicate message-->*/
    $('.dms_form').submit(function(e){
        $('.password_length_error').html('');
    });

    // Checking duplicate entry of email
    $('body').on('keyup change mouseleave','#email',function(){ 
        var username   = $('#username').val();
        var eId        = $(this).val();
        // alert(username);
        // alert(eId);
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        
        $.ajax({
            type:'POST',
            url :base_url+'/emailDuplication',
            data:{_token: CSRF_TOKEN,emialId:eId,username:username},
            success:function(result){
                //alert(result);
                if(result){
                    $('#email').val('');
                    $('#show-err-msg').html(eId+' is already in our database');
                }else{
                    $('#show-err-msg').html('');
                }
            }
        });
        
    });