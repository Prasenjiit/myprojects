<?php include (public_path()."/storage/includes/lang1.en.php" ); ?>
  

               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Delegate to another user</h4>
               </div>
               <div class="modal-body">
                  

                  <form action="<?php echo url('delegate_user_save'); ?>" method="post" class="form-horizontal" name="delegate_user_form"
                  id="delegate_user_form">
                  <input type="hidden" name="wf_operation_id" id="wf_operation_id" value="<?php echo $wf_operation_id; ?>">
                  <input type="hidden" name="wf_stage" id="wf_stage" value="<?php echo $wf_stage; ?>">
                  <div class="form-group">
                     <div class="col-sm-12 alert_modal">
                       
                     </div>
                  </div>     
                  <div class="form-group">
                     <label for="new_object_id" class="col-sm-3 control-label">Select user:</label>
                     <div class="col-sm-8">
                        <select class="form-control" id="delegate_user_id"  name="delegate_user_id">
                           <option value="">select user</option>
                           <?php foreach($users as $row) { ?>
                           <option value="<?php echo $row->id; ?>" <?php if(isset($delegated->delegated_user) && ($row->id == $delegated->delegated_user)) { echo "selected='selected'"; } ?> 
                           data-user_full_name="<?php echo $row->user_full_name; ?>"
                           ><?php echo $row->user_full_name.' '.$row->user_role; ?></option>
                           <?php } ?>
                        </select>
                     </div>
                  </div>
                  
                  <div class="form-group" id="delegate_confirm" >
                     <div class="col-sm-3"></div>
                     <div class="col-sm-8">
                        <button type="submit" class="btn btn-primary">Assign</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <div class="delegate_loader" style="display:none;"><i class="fa fa-spinner fa-pulse fa-fw fa-lg"></i></div>
                     </div>
                  </div>
                  </form>
              </div>
               
      <script>
        $(document).ready(function(){
          /*
          $('#delegate_user_id').on('change', function() {
            var user = $(this).find(':selected').data('user_full_name');
            $(".alert_modal").html('');
            if(typeof(user)=='undefined') {
              $(".alert_modal").hide();
              $("#delegate_confirm").hide();
            }
            else{
              $(".alert_modal").html('<div class="alert alert-warning alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4><i class="icon fa fa-warning"></i> Confirm !</h4>Do you want to deletegate your responsibility to <b>'+user+'</b></div>');
              $(".alert_modal").show();
              $("#delegate_confirm").show();
            }
          });
        */
          $("#delegate_user_form").submit(function(e){
              e.preventDefault();
              var wf_operation_id  = $("#wf_operation_id").val();
              var wf_stage         = $("#wf_stage").val();
              var delegate_user_id = $("#delegate_user_id").val();
              $('.delegate_loader').show();
              $.ajax({
                      type: 'get',
                      url: "<?php echo URL('delegate_user_save'); ?>",
                      dataType: 'json',
                      data: {wf_operation_id:wf_operation_id,wf_stage:wf_stage,delegate_user_id:delegate_user_id},
                      timeout: 50000,
                      success: function(data)
                      {
                        $('.delegate_loader').hide();
                          if(data.status==1) {
                            $(".alert_modal").html(data.msg);
                            setTimeout(function() {
                                $("#delegate_user_modal").modal('toggle');
                            }, 2000);
                          }
                          else {
                            $(".alert_modal").html(data.msg);
                            setTimeout(function() {
                                $(".alert_modal").html('');
                            }, 5000);
                          }
                      }
              });
              /*$(".alert_modal").html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4><i class="icon fa fa-warning"></i> Success !</h4>Assigned to user</div>');
              setTimeout(function() {
                  $("#delegate_user_modal").modal('toggle');
              }, 2000);*/
          });
      });
      </script>