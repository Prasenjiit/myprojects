<?php $user_permission=Auth::user()->user_permission; ?>
<?php if(count($dglist)>0){ ?>
<script type="text/javascript">
$('#countshow-div').show();
</script>
<style type="text/css">
.icon{
    color: #97a0b3 !important;
    cursor:pointer;
}
.icondiv{
    display: inline-block;
    width: 19px;
    height: 20px;
}
@media (max-width: 320px) and (min-width: 0px){
    .icondiv {
        width: 24px;
    }
}
@media (max-width: 360px) and (min-width: 321px){
    .icondiv {
        width: 29px;
    }
}
@media (max-width: 375px) and (min-width: 361px){
    .icondiv {
        width: 31px;
    }
}
@media (max-width: 414px) and (min-width: 376px){
    .icondiv {
        width: 31px;
    }
}
@media (max-width: 768px) and (min-width: 415px){
    .icondiv {
        width: 48px;
    }
}
</style>
<input type="hidden" id="doccnt" value="<?php echo @$countdocs ?>" />
<input type="hidden" id="seldocid" value="" />
<input type="hidden" id="selid" value="" />
<div id="searchres"> 
    <?php $i = 1; ?>
    <!-- <?php echo e(Session::get('settings_document_expiry')); ?> -->
    <?php $__currentLoopData = $dglist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div id="box<?php echo $i; ?>" class="box-grid">
        <?php if(($val->document_expiry_date > date('Y-m-d'))){
            $todaydate = date('Y-m-d'); // or your date as well
            $docexpdate = $val->document_expiry_date;
            $datediff = abs(strtotime($docexpdate) - strtotime($todaydate));
            $noofdays = round($datediff / (60 * 60 * 24));                
        }else{
            $noofdays = 0;
        }
        if($val->document_encrypt_status ==1){?>
            <div class="box box-encrypt-box">
        <?php } 
        else if(($val->document_expiry_date != null) && ($val->document_expiry_date <= date('Y-m-d'))){ 
            @$expirestatus = 1; ?> 
            <div class="box box-danger-box">
        <?php }
        else if(($noofdays!=0)&&($noofdays<Session::get('settings_document_expiry'))){
            @$expirestatus  = 2; ?>
            <div class="box box-warning-box">
        <?php }
        else if($val->document_status == 'Checkout'){ 
            ?> 
            <div class="box box-checkout-box">
        <?php }
        else{
            @$expirestatus  = 0;  ?>
            <div class="box box-success-box">
        <?php } ?>
            
            <!-- <div class="box box-warning"> -->
                <div class="box-modify box-profile">

                    <div style="display:none" id="documentTypeColumns<?php echo $i;?>">
                                        
                        <?php $__currentLoopData = $val->document_type_columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $docColms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="row">
                            <div class="col-sm-2">
                                <label class="control-label"><?php echo e($docColms->document_column_name); ?>: </label>
                            </div>
                            <div class="col-sm-3">
                                <p style="border: yellowgreen;"><?php echo e($docColms->document_column_value); ?></p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>      
                    </div>
                    <?php 
                    if(@$val->documentTypes[0]->document_type_column_no){
                        $field1 = @$val->documentTypes[0]->document_type_column_no;
                    }else{
                        $field1 = ucfirst(@$settings_document_no);
                    } 
                    if(@$val->documentTypes[0]->document_type_column_name){
                        $field2 = @$val->documentTypes[0]->document_type_column_name;
                    }else{
                        $field2 = ucfirst(@$settings_document_name);
                    } 
                    ?>
                    <input type="hidden" name="toplbl" id="toplbl" value="<?php echo $field1; ?>">
                    
                    <!--hidden fields for view more-->
                    <input type="hidden" id="docType<?php echo $i;?>" value="<?php echo @$val->documentTypes[0]->document_type_names;?>">
                    <input type="hidden" id="stacks<?php echo $i;?>" value="<?php echo @$val->stacks[0]->stack_name;?>">
                    <input type="hidden" id="tagwords<?php echo $i;?>" value="<?php echo @$val->tagwords[0]->tagwords_title;?>">
                    <input type="hidden" id="document_file_name<?php echo $i;?>" value="<?php echo $val->document_file_name;?>" >
                    <input type="hidden" id="document_checkin_date<?php echo $i;?>" value="<?php echo $val->document_checkin_date;?>" >
                    <input type="hidden" id="document_checkout_date<?php echo $i;?>" value="<?php echo $val->document_checkout_date;?>" >
                    <input type="hidden" id="document_created_by<?php echo $i;?>" value="<?php echo $val->document_created_by;?>" >
                    <input type="hidden" id="document_modified_by<?php echo $i;?>" value="<?php echo $val->document_modified_by;?>" >
                    <input type="hidden" id="created_at<?php echo $i;?>" value="<?php echo $val->created_at;?>" >
                    <input type="hidden" id="updated_at<?php echo $i;?>" value="<?php echo $val->updated_at;?>" >

                    <div class="input-group col-sm-12">
                        <div class="col-xs-12"><div class="col-xs-5" style="padding-left:0px !important;"><?php echo $field1; ?></div><div class="col-xs-1" style="padding-left:0px !important;">:</div><div id="doc_no<?php echo $i;?>" class="col-xs-6" style="padding-left:0px !important;"><?php echo e($val->document_no); ?></div></div>
                        <div class="col-xs-12"><div class="col-xs-5" style="padding-left:0px !important;"><?php echo $field2; ?></div><div class="col-xs-1" style="padding-left:0px !important;">:</div><div id="doc_name<?php echo $i;?>" class="col-xs-6" style="padding-left:0px !important;"><?php echo e(ucfirst($val->document_name)); ?></div></div>
                        <div class="col-xs-12"><div class="col-xs-5" style="padding-left:1px !important;">Ownership</div><div class="col-xs-1" style="padding-left:0px !important;">:</div><div id="ownership<?php echo $i;?>" class="col-xs-6" style="padding-left:0px !important;"><?php echo e(ucfirst($val->document_ownership)); ?></div></div>
                        <div class="col-xs-12"><div class="col-xs-5" style="padding-left:1px !important;">Version No</div><div class="col-xs-1" style="padding-left:0px !important;">:</div><div id="versionNo<?php echo $i;?>" class="col-xs-6" style="padding-left:0px !important;"><?php echo e($val->document_version_no); ?></div></div>
                        <div class="col-xs-12"><div class="col-xs-5" style="padding-left:1px !important;">Status</div><div class="col-xs-1" style="padding-left:0px !important;">:</div><div id="status<?php echo $i;?>" class="col-xs-6" style="padding-left:0px !important;"><?php echo e(ucfirst($val->document_status)); ?></div></div>
                        <div class="col-xs-12"><div class="col-xs-5" style="padding-left:0px !important;">Last Updated</div><div class="col-xs-1" style="padding-left:0px !important;">:</div><div id="lastUpdate<?php echo $i;?>" class="col-xs-6" style="padding-left:0px !important;">
                        <?php if($val->updated_at != NULL): ?>
                            <?php echo date('d-m-Y h:i:s a', strtotime($val->updated_at));?>
                            
                        <?php else: ?>
                            <?php echo date('d-m-Y h:i:s a', strtotime($val->created_at));?>
                            
                        <?php endif; ?>
                        </div>
                        </div> 
                    </div> 
                    
                    <div class="row">
                    <div class="docdisp" style="margin-left: 18px;margin-bottom: -30px;margin-top: 0px; text-align: center; margin-right: 15px;">
                        <!-- View Document -->
                        <span class="icondiv">
                            <a  title="View Document" href="javascript:void(0);" onclick="viewfile('<?php echo $i; ?>','<?php echo e($val-> document_id); ?>','<?php echo e($val->document_name); ?>','<?php echo e($val->document_file_name); ?>','<?php echo @$expirestatus; ?>','<?php echo $field1; ?>');">
                                
                                <i class="icon fa fa-eye fa-inverse icpad"></i>      
                            </a>
                        </span>
                        <!-- Open Document -->
                        <span class="icondiv">
                            <a href="<?php echo e(url('documentManagementView')); ?>?dcno=<?php echo e($val->document_id); ?>&id=<?php echo $i;?>&page=document"  count="<?php echo $i;?>" id="showmore" title="Open Document">
                                <?php 
                                $ext = pathinfo($val->document_file_name, PATHINFO_EXTENSION);
                                $ext = strtolower($ext);
                                if($ext=='pdf'){?>
                                    <i class="icon fa fa-file-pdf-o"></i>
                                <?php
                                }
                                elseif ($ext=='png'||$ext=='jpg'||$ext=='jpeg'||$ext=='tiff'||$ext=='tif'||$ext=='TIFF'||$ext=='TIF' ||$ext=='gif') {?>
                                    <i class="icon fa fa-file-image-o"></i>
                                <?php
                                }
                                elseif ($ext=='docx'||$ext=='doc') {?>
                                    <i class="icon fa fa-file-word-o"></i>
                                <?php
                                }
                                elseif ($ext=='txt') {?>
                                    <i class="icon fa fa-file-text-o"></i>
                                <?php
                                }
                                elseif ($ext=='zip'||$ext=='rar') {?>
                                    <i class="icon fa fa-file-archive-o"></i>
                                <?php
                                }
                                elseif ($ext=='xls'||$ext=='xlsx') {?>
                                    <i class="icon fa fa-file-excel-o"></i>
                                <?php
                                }
                                elseif ($ext=='wav'||$ext=='mp3'||$ext=='ogg') {?>
                                    <i class="icon fa fa-file-sound-o"></i>
                                <?php
                                }
                                elseif ($ext=='flv'||$ext=='mp4'||$ext=='ogv'||$ext=='webm') {?>
                                    <i class="icon fa fa-file-video-o"></i>
                                <?php
                                }
                                elseif ($ext=='dwg') {?>
                                    <i class="icon fa fa-clipboard"></i>
                                <?php
                                }
                                else{?>
                                    <i class="icon fa fa-file-o"></i>
                                <?php
                                }
                                ?>
                            </a>
                        </span>
                        <!-- History -->
                        <span class="icondiv">
                            <a title="History" href="<?php echo e(url('documentHistory', $val->document_id )); ?>"><i class="icon fa fa-history" ></i></a>
                        </span>
                        <!-- Check Out Document -->
                        <!-- Checking Check Out permission -->
                        <?php if(stristr($user_permission,'checkout')): ?>
                        <?php if($val->document_status =='Published' || $val->document_status =='Rejected'): ?>
                        <?php if($val->document_encrypt_status ==1): ?>
                        <span class="icondiv"> 
                            <a  title="Check Out" onclick="return swal('\'<?php echo e($val->document_name); ?>\' is curently encrypted by \'<?php echo e($val->document_encrypted_by); ?>\'. It must be decrypted first before you can perform this operation.')">
                                
                                <i class="icon fa fa-share fa-inverse icpad"></i>
                            </a>
                        </span>
                        <?php else: ?>
                        <span class="icondiv"> 
                            <a  title="Check Out" onclick="return myFunction('<?php echo e($val->document_name); ?>','<?php echo e($val->document_id); ?>','<?php echo e($i); ?>')">
                                
                                <i class="icon fa fa-share fa-inverse icpad"></i>
                            </a>
                        </span>
                        <?php endif; ?>
                        <?php elseif($val->document_status =='Checkout'): ?>
                        <span class="icondiv"> 
                            <a  title="Check Out" onclick="return swal('\'<?php echo e($val->document_name); ?>\' is curently Checked Out by \'<?php echo e($val->document_modified_by); ?>\'. It must be Checked In first before you can perform this operation.')">
                                
                                <i class="icon fa fa-share fa-inverse icpad"></i>
                            </a>
                        </span>
                        
                        <?php elseif($val->document_status =='Review'): ?>
                        <span class="icondiv"> 
                            <a  title="Check Out" onclick="return swal('\'<?php echo e($val->document_name); ?>\' is curently under review')">
                                
                                <i class="icon fa fa-share fa-inverse icpad"></i>
                            </a>
                        </span>
                        <?php endif; ?>
                        <?php endif; ?>
                        <!-- Edit Document -->
                        <?php if(stristr($user_permission,'edit')): ?>
                        <!--  <span class="icondiv">         
                            <a  title="Edit Document" href="<?php echo e(route('editAllDocument', array('id'=>$val->document_id))); ?>&page=document&status=<?php echo e($val->document_status); ?>">
                                
                                <i class="icon fa fa-edit fa-inverse icpad"></i>
                            </a>                                               
                        </span> --> <!-- It hidden and added in fileviewre -->
                        <?php endif; ?>                                     
                        <!-- Delete Document -->
                        <!-- Checking delete permission -->
                        <?php if(stristr($user_permission,'delete')): ?>
                        <?php if($val->document_status!='Checkout'): ?>
                        <span class="icondiv"> 
                            <a  title="Delete" onclick="del(<?php echo e($val->document_id); ?>,'<?php echo e($val->document_name); ?>')">
                                
                                <i class="icon fa fa-trash fa-inverse icpad"></i>
                            </a>
                        </span>
                        <?php else: ?>
                        <span class="icondiv">
                            <a  title="Delete" onclick="return swal('\'<?php echo e($val->document_name); ?>\' is curently Checked Out by \'<?php echo e($val->document_modified_by); ?>\'. It must be Checked In first before you can perform this operation.')">
                                
                                <i class="icon fa fa-trash fa-inverse icpad"></i>
                            </a>
                        </span>
                        <?php endif; ?>
                        <?php endif; ?>

                        <span class="icondiv"> 
                            <a  title="Related Documents" count="<?php echo $i;?>" href="<?php echo e(url('relatedsearch', array($val->document_id))); ?>">
                                
                                <i class="icon fa fa-files-o fa-inverse icpad"></i>
                            </a>
                        </span>
                        
                        
                        <?php if(Session::get('enbval6')==Session::get('tval')): ?>
	                        <!-- encrypt/decrypt -->
	                        <?php if($val->document_encrypt_status == 0 && $val->document_status !='Checkout'): ?>
	                        <span class="icondiv"> 
	                            <a count="<?php echo e($val->document_id); ?>" doc_name="<?php echo e($val->document_name); ?>" doc_file_name="<?php echo e($val->document_file_name); ?>" title="Encrypt File" id="encrypt_doc"><i class="icon fa fa-lock" ></i>
	                            </a>
	                        </span>

	                        <?php elseif($val->document_status =='Checkout' && $val->document_encrypt_status == 0): ?>
	                        <span class="icondiv"> 
	                            <a title="Encrypt File" onclick="return swal('\'<?php echo e($val->document_name); ?>\' is curently Checked Out by \'<?php echo e($val->document_modified_by); ?>\'. It must be Checked In first before you can perform this operation.')"><i class="icon fa fa-lock" ></i>
	                            </a>
	                        </span>
	                        <?php else: ?>
	                        <span class="icondiv"> 
	                            <a count="<?php echo e($val->document_id); ?>" doc_name="<?php echo e($val->document_name); ?>" doc_file_name="<?php echo e($val->document_file_name); ?>" title="Decrypt File" id="decrypt_doc_data"><i class="icon fa fa-unlock" ></i>
	                            </a>
	                        </span>
	                        <?php endif; ?>
                        <?php endif; ?>
                        <!-- More Details -->
                        <!-- &nbsp; -->
                        <span class="icondiv"> 
                            <a  title="More Details" count="<?php echo $i;?>" id="moredet" data-toggle="modal" data-target="#viewmoreModal">
                                
                                <i class="icon fa fa-ellipsis-v fa-inverse icpad"></i>
                            </a>
                        </span>
                    </div> 
                    </div>
                        <?php if($val->document_status!='Checkout'): ?>
                            <input type="hidden" value="0" id="docprmsn<?php echo $i; ?>">
                       
                    <?php endif; ?>
                    <input type="hidden" name="filepath<?php echo $i; ?>" id="path<?php echo $i; ?>" value="<?php echo e($val->document_file_name); ?>" encrypt_status = "<?php echo e($val->document_encrypt_status); ?>" count="<?php echo e($i); ?>" dcno="<?php echo e($val-> document_id); ?>" exprstatus="<?php echo @$expirestatus; ?>" toplabel="<?php echo $field1; ?>" dcname="<?php echo e($val->document_name); ?>" doc_file_name="<?php echo e($val->document_file_name); ?>">


                    <input type="hidden" name="urlpath<?php echo $i; ?>" id="url<?php echo $i; ?>" value="<?php echo e($val->document_id); ?>">
                    <input type="hidden" name="dcno<?php echo $i; ?>" id="dcno<?php echo $i; ?>" value="<?php echo e($val->document_no); ?>">
                    <input type="hidden" name="dcname<?php echo $i; ?>" id="dcname<?php echo $i; ?>" value="<?php echo e($val->document_name); ?>">
                    <input type="hidden" name="doc_id<?php echo $i; ?>" id="doc_id<?php echo $i; ?>" value="<?php echo e($val->document_id); ?>">
                    <!-- /input-group -->
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
    <?php $i++; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php }else{ ?>

<?php } ?>
<center> 
<?php echo e($dglist->links()); ?>

</center><?php /**PATH F:\xampp\htdocs\nspl5.8\resources\views/pages/documents/datas.blade.php ENDPATH**/ ?>