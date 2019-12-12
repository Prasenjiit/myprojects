// This js is form tags words master module
$(document).ready(function(){

	// add cat
	$('body').on('click','#add-cat',function(){
		var catName = $('#tagwords_category_name').val();

		// checking delete permission for RU
		var delete_permission = true;
		var deleteIcon        = '<i class="fa fa-close" id="cat-fa-close"></i>';
		var deletePermission = $('#delete-premission').val();
		if(deletePermission == ''){
			var delete_permission = 'disabled';
			var deleteIcon        = '';
		}

		// checking edit permission for RU
		var edit_permission = true;
		var updateIcon      = '<i class="fa fa-plus"></i>';
		var editPermission = $('#edit-premission').val();
		if(editPermission == ''){
			var edit_permission = 'disabled';
			var updateIcon      = '';
		}

		if(catName){
			$.ajax({
				type:'GET',
				url:base_url+'/saveCategory',
				data:'catName='+catName,
				success:function(response){// response is cat id
					// false
					if(response == 'false'){				
						// warning message			
						$('#ajax-msg').css('display','block');
						$('#ajax-msg').html('<div class="alert alert-danger alert-sty" id="ajax-msg"><strong id="strong-msg">'+catName+' already exits.</strong></div>');
						slideshow();// slide slow process
					} else {//true

						// get ajax select box list
						$.ajax({
							type:'GET',
							url:base_url+'/getCatgory',
							success:function(category){
								// update select box in each add
								$('#twm-update-select-box').html(category);
							}
						});
						// success meg
						$('#ajax-msg').css('display','block');
						$('#ajax-msg').html('<div class="alert alert-success alert-sty" id="twm-msg"><strong id="strong-msg">Category added successfully.</strong></div>');
						slideshow();// slide slow property
						// update and delete button
						$('#twm-add-update-cat').html('<a href="" id="update-cat" cat_id='+response+'><button class="btn btn-block btn-info btn-flat" '+edit_permission+'>Update  '+updateIcon+'</button></a>');
						$('#twm-delete-cat').html('<a href="" id="remove" cat_id='+response+'><button class="btn btn-block btn-info btn-flat" id="btn-delete" '+delete_permission+'>Delete '+deleteIcon+'</button></a>');
						
						// add tag from
						var addTagFrom = '<div class="box-body" id="delete_formgroup"><label for="Tag:" class="col-sm-2 control-label">Tag: <span style="color:red">*</span></label><div class="col-sm-4"><input class="form-control global_s_msg" id="tagwords_title" required="" placeholder="Tag Name" name="tagwords_title" type="text" value=""> <p id="tag-val-msg" style="color:red"></p></div><div class="col-sm-2" id="twm-add-tag"><a href="" data-toggle="modal" id="add-tag" catID=""><button class="btn btn-block btn-info btn-flat">Add Tag  <i class="fa fa-plus"></i></button></a></div><div class="col-sm-2" id="twm-update-tag" style="display:none"><a href="" class="update_tag" data-toggle="modal" id="update-tag" tagId=""><button class="btn btn-block btn-info btn-flat" '+edit_permission+'>Update  '+updateIcon+'</button></a></div><div class="col-sm-2" id="twm-delete-tag" style="display:none"><a href="" class="delete_tag" data-toggle="modal" id="delete-tag" tagId=""><button class="btn btn-block btn-info btn-flat" '+delete_permission+'>Delete  '+deleteIcon+'</button></a></div></div>';
						$('#twm-add-tags').append(addTagFrom);
						// adding catId for add,update and delete tag purposes
						$('#add-tag').attr('catID',+response);

					}
			
				}
			});
		} else {
			// show validation message for category
			$('#show-val-msg').html('Please enter category name.');
			$('#tagwords_category_name').css('border-color','red');
		}
		
	});

	// update category
	$('body').on('click','#update-cat',function(e){
		e.preventDefault();
		var catId   = $(this).attr('cat_id');
		var catName = $('#tagwords_category_name').val();
		if(catName){
			$.ajax({
				type:'GET',
				url:base_url+'/updateCategory',
				data:'catId='+catId+'&catName='+catName,
				success:function(response){
					if(response == 'false'){
						// warning message			
						$('#ajax-msg').css('display','block');
						$('#ajax-msg').html('<div class="alert alert-danger alert-sty" id="ajax-msg"><strong id="strong-msg">'+catName+' already exits.</strong></div>');
						slideshow();// slide slow property
					} else {
						// success meg
						$('#ajax-msg').css('display','block');
						$('#ajax-msg').html('<div class="alert alert-success alert-sty" id="twm-msg"><strong id="strong-msg">Category updated successfully.</strong></div>');
						slideshow();// slide slow property
						// get ajax select box list
						$.ajax({
							type:'GET',
							url:base_url+'/getCatgory',
							success:function(category){
								// update select box in each add
								$('#twm-update-select-box').html(category);
							}
						});
					}
				}
			});
		} else {
			// show validation message for category
			$('#show-val-msg').html('Please enter category name.');
			$('#tagwords_category_name').css('border-color','red');
		}
	});

	// delete cat
	$('body').on('click','#remove',function(){
		var catId   = $('#remove').attr('cat_id');
		var catName = $('#tagwords_category_name').val();

		swal({
              title: ''+confirm_delete_single+''+catName+' ?',
              text: swalNotRevert,
              type: warning,
              showCancelButton: true
            }).then(function (result) {
                if(result){
                    // Success
                    $.ajax({
					type:'GET',
					url :base_url+'/deleteCat',
					data:'catId='+catId,
					success:function(result){
						// success message
						$('#ajax-msg').css('display','block');
							$('#ajax-msg').html('<div class="alert alert-success alert-sty" id="twm-msg"><strong id="strong-msg">Category deleted successfully.</strong></div>');
						slideshow();// slide slow property
						// new form
						$('#tagwords_category_name').val('');
						$('#twm-add-update-cat').html('<a href="" data-toggle="modal" id="add-cat"><button class="btn btn-block btn-info btn-flat">Add Category  <i class="fa fa-plus"></i></button></a>');
						$('#twm-delete-cat').html('');
						$('#twm-add-tags').html('');

						// get ajax select box list
						$.ajax({
							type:'GET',
							url:base_url+'/getCatgory',
							success:function(category){
								// update select box in each add
								$('#twm-update-select-box').html(category);
							}
						});
					} 
				});
              }
          });

		return false;
	});

	// add tag
	$('body').on('click','#add-tag',function(e){
		e.preventDefault();
		var tagName = $('#tagwords_title').val();
		var catId   = $(this).attr('catID');
		
		// checking delete permission for RU
		var delete_permission = true;
		var deletePermission  = $('#delete-premission').val();
		var deleteIcon        = '<i class="fa fa-close"></i>';
		if(deletePermission == ''){
			var delete_permission = 'disabled';
			var deleteIcon        = '';
		}

		// checking edit permission for RU
		var edit_permission = true;
		var updateIcon      = '<i class="fa fa-plus"></i>';
		var editPermission = $('#edit-premission').val();
		if(editPermission == ''){
			var edit_permission = 'disabled';
			var updateIcon      = '';
		}

		$.ajax({
			type:'GET',
			url : base_url+'/addTag',
			data:'catId='+catId+'&tagName='+tagName,
			success:function(response){// response=tagid

				if(response == 'null'){
					// show validation message
					$('#tag-val-msg').html('Please enter tag name.');
					$('#tagwords_title').css('border-color','red');
				}else{// if success
					// change old ids with new one
					$("#add-tag").attr('id','add-tag_'+response);
					$('#tagwords_title').attr('id','tags_word_title_'+response);
					$('#tag-val-msg').attr('id','tag-val-msg_'+response);
			
					// success message
					$('#ajax-msg').css('display','block');
					$('#ajax-msg').html('<div class="alert alert-success alert-sty" id="twm-msg"><strong id="strong-msg">Tag added successfully.</strong></div>');
					slideshow();// slide slow property

					// saving tag ids and enable update and delete button then disable add button
					$('#delete-tag').attr('id','delete-tag_'+response);
					$('#update-tag').attr('id','update-tag_'+response);
					$('#twm-add-tag').attr('id','twm-add-tag_'+response);
					$('#twm-update-tag').attr('id','twm-update-tag_'+response);
					$('#twm-delete-tag').attr('id','twm-delete-tag_'+response);
					$('#delete_formgroup').attr('id','delete_formgroup_'+response);

					$('#delete-tag_'+response).attr('tagId',+response);
					$('#update-tag_'+response).attr('tagId',+response);
					$('#twm-add-tag_'+response).css('display','none');
					$('#twm-update-tag_'+response).css('display','block');
					$('#twm-delete-tag_'+response).css('display','block');

					// add more tag after done 
					var addTagFrom = '<div class="box-body" id="delete_formgroup"><label for="Tag:" class="col-sm-2 control-label">Tag: <span style="color:red">*</span></label><div class="col-sm-4"><input class="form-control global_s_msg" id="tagwords_title" required="" placeholder="Tag Name" name="tagwords_title" type="text" value=""> <p id="tag-val-msg" style="color:red"></p></div><div class="col-sm-2" id="twm-add-tag"><a href="" data-toggle="modal" id="add-tag" catId='+catId+'><button class="btn btn-block btn-info btn-flat">Add Tag  <i class="fa fa-plus"></i></button></a></div><div class="col-sm-2" id="twm-update-tag" style="display:none"><a href="" class="update_tag" data-toggle="modal" id="update-tag" tagId=""><button class="btn btn-block btn-info btn-flat" '+edit_permission+'>Update  '+updateIcon+'</button></a></div><div class="col-sm-2" id="twm-delete-tag" style="display:none"><a href="" class="delete_tag" data-toggle="modal" id="delete-tag" tagId=""><button class="btn btn-block btn-info btn-flat" '+delete_permission+'>Delete  '+deleteIcon+'</button></a></div></div>';
					$('#twm-add-tags').append(addTagFrom);

					// disable delete button
					$('#btn-delete').prop('disabled', true);
					$('#btn-delete').attr('title','Sorry! Can\'t delete, tagwords is availabe in child table');
					$('#cat-fa-close').attr('class','');


				}
			}
		});
	});

	// update tag
	$('body').on('click','.update_tag',function(){
		var tagId = $(this).attr('tagid');
		var name  = $('#tags_word_title_'+tagId).val(); 
			$.ajax({
				type:'GET',
				url:base_url+'/updateTag',
				data:'tagId='+tagId+'&name='+name,
				success:function(result){
					if(result == 'true'){
					// success message
					$('#ajax-msg').css('display','block');
					$('#ajax-msg').html('<div class="alert alert-success alert-sty" id="twm-msg"><strong id="strong-msg">Tag updated successfully.</strong></div>');
					slideshow();// slide slow property
					}else{
						swal('Please enter tag name');
					}			
				}
			});
	});

	// delete tag
	$('body').on('click','.delete_tag',function(){
		var tagId = $(this).attr('tagid');
		var catId = $('#update-cat').attr('cat_id');
		var tagNam = $('#tags_word_title_'+tagId).val();

		swal({
              title: ''+confirm_delete_single+''+tagNam+' ?',
              text: swalNotRevert,
              type: warning,
              showCancelButton: true
            }).then(function (result) {
                if(result){
                    // Success
                    $.ajax({
						type:'GET',
						url:base_url+'/deleteTag',
						data:'tagId='+tagId+'&catId='+catId+'&tagNam='+tagNam,
						cache:false,
						dataType:"json",
						success:function(result){
				
							if(result.isTagExists == 'true'){
								// deleted successfully

								if(result.catCount == '1'){
									// enable delete button
									$('#btn-delete').prop('disabled', false);
									$('#btn-delete').attr('title','Delete');
									$('#cat-fa-close').attr('class','fa fa-close');
								}

								$('#ajax-msg').css('display','block');
								$('#ajax-msg').html('<div class="alert alert-success alert-sty" id="twm-msg"><strong id="strong-msg">Tag deleted successfully.</strong></div>');
								slideshow();// slide slow property

								$('#delete_formgroup_'+tagId).remove();
							}else{
								$('#ajax-msg').css('display','block');
								$('#ajax-msg').html('<div class="alert alert-danger alert-sty" id="twm-msg"><strong id="strong-msg">Sorry! Can\'t delete, This tagword is availabe in child table</strong></div>');
								slideshow();// slide slow property
							}
						}
					});
              }
          });
	});

	// clear error in cat
	$('body').on('keyup','#tagwords_category_name',function(){
		$('#show-val-msg').html('');
		$('#tagwords_category_name').css('border-color','');
	});
	// clear error in tag
	$('body').on('keyup','#tagwords_title',function(){
		$('#tag-val-msg').html('');
		$('#tagwords_title').css('border-color','');
	});

	// on change select box category
	$('body').on('change','#twm-update-select-box',function(){

		// clear valmessage  of cat
		$('#show-val-msg').html('');
		$('#tagwords_category_name').css('border-color','#d2d6de');

		var catId = $(this).val();
		var catName = $("#twm-update-select-box option:selected").text();

		// checking add permission for RU
		var add_permission = true;
		var addIcon        = '<i class="fa fa-plus"></i>';
		var addPermission = $('#add-premission').val();
		if(addPermission == ''){
			var add_permission = 'disabled';
			var addIcon       = '';
		}
		// checking edit permission for RU
		var edit_permission = true;
		var updateIcon      = '<i class="fa fa-plus"></i>';
		var editPermission = $('#edit-premission').val();
		if(editPermission == ''){
			var edit_permission = 'disabled';
			var updateIcon      = '';
		}
		// checking delete permission for RU
		var delete_permission = true;
		var deleteIcon        = '<i class="fa fa-close"></i>';
		var deletePermission = $('#delete-premission').val();
		if(deletePermission == ''){
			var delete_permission = 'disabled';
			var deleteIcon        = '';
		}

		if(catId){
			$.ajax({
				type:'GET',
				url :base_url+'/getTags',
				data:'catId='+catId,
				success:function(result){// result = tag form
						// cat form
						$('#tagwords_category_name').val(catName);
						// update and delete button
						$('#twm-add-update-cat').html('<a href="" id="update-cat" cat_id='+catId+'><button class="btn btn-block btn-info btn-flat" '+edit_permission+'>Update  '+updateIcon+'</button></a>');
						$('#twm-delete-cat').html('<a href="" id="remove" cat_id='+catId+'><button class="btn btn-block btn-info btn-flat" id="btn-delete" disabled title="Sorry! Can\'t delete, tagwords is availabe in child table">Delete <i class="" id="cat-fa-close"></i></button></a>');
						// tag form	
						$('#twm-add-tags').html(result);

						if(result == 0){
							
							if(deletePermission == ''){
								$('#btn-delete').prop('disabled', true);
								$('#cat-fa-close').remove();
							}else{
								// enable delete button
								$('#btn-delete').prop('disabled', false);
							}
							
							$('#cat-fa-close').attr('class','fa fa-close');
							$('#btn-delete').attr('title','Delete');

							// tag form
							$('#twm-add-tags').html('<div class="box-body" id="delete_formgroup"><label for="Tag:" class="col-sm-2 control-label">Tag: <span style="color:red">*</span></label><div class="col-sm-4"><input class="form-control global_s_msg" id="tagwords_title" required="" placeholder="Tag Name" name="tagwords_title" type="text" value=""> <p id="tag-val-msg" style="color:red"></p></div><div class="col-sm-2" id="twm-add-tag"><a href="" data-toggle="modal" id="add-tag" catID='+catId+'><button class="btn btn-block btn-info btn-flat" '+add_permission+'>Add Tag  '+addIcon+'</button></a></div><div class="col-sm-2" id="twm-update-tag" style="display:none"><a href="" class="update_tag" data-toggle="modal" id="update-tag" tagid=""><button class="btn btn-block btn-info btn-flat" '+edit_permission+'>Update  '+updateIcon+'</button></a></div><div class="col-sm-2" id="twm-delete-tag" style="display:none"><a href="" class="delete_tag" data-toggle="modal" id="delete-tag" tagid=""><button class="btn btn-block btn-info btn-flat" '+delete_permission+'>Delete  '+deleteIcon+'</button></a></div></div>');
						}

				}
			});
		}else{
			//add new
			$('#tagwords_category_name').val('');
			$('#twm-add-update-cat').html('<a href="" data-toggle="modal" id="add-cat"><button class="btn btn-block btn-info btn-flat" '+add_permission+'>Add '+addIcon+'</button></a>');
			$('#twm-delete-cat').html('');	
			$('#twm-add-tags').html('');			
		}
	});

	// common function for slide up and down property.
	function slideshow(){
		setTimeout(function () {
            $("#ajax-msg").slideDown(1000);
	        }, 200);
	        setTimeout(function () {
	            $('#ajax-msg').slideUp("slow");
	        }, 4000);
	}

});
