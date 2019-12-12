<section class="content-header">
    <div class="col-sm-8">
        <span style="float:left;">
            <strong>{{trans('language.modules')}}</strong>
        </span>
    </div>
</section><br/>
@if (isset($success))
<section class="content content-sty" id="spl-wrn"> 
    <div class="alert alert-success" id="hide-div">
        <p style="text-align: center;"><strong>Success!</strong> <?php echo $success; ?></p>
    </div>
</section>
@endif

<!--Checking view permission-->
<!-- Main content -->
    <section class="content">
        <div class="row">
            {!! Form::open(array('url'=> array('ModulesSave'), 'files'=>true, 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'ModulesAddForm', 'id'=> 'ModulesAddForm','data-parsley-validate'=> '')) !!} 
            <div class="col-md-6">
                <!-- company form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('language.imp_exp')}}</h3>
                    </div><!-- /.box-header -->  
                    <!--SA and GA-->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="checkbox" name="ie_status" <?php if($data_ie_status){ echo "checked"; } ?> value="<?php if($data_ie_status){ echo 1; }else{ echo 0; } ?>">&nbsp;&nbsp;{{trans('language.status')}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.activ_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="ie_activ_date" value="<?php if($data_ie_activ_date){ echo $data_ie_activ_date; } ?>" placeholder="yyyy-mm-dd">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.count')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="ie_cnt" value="<?php //if($data_ie_cnt){ echo $data_ie_cnt; }else{ echo 0; } ?>">
                                    </div>
                                </div> -->
                                
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.exp_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="ie_exp_date" value="<?php if($data_ie_exp_date){ echo $data_ie_exp_date; } ?>" placeholder="yyyy-mm-dd / No Expiry">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="ie_module_id" value="1">
                        </div>
                    </div>
                </div>

            </div><!--// General form end-->
             <div class="col-md-6">
                <!-- company form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('language.audits')}}</h3>
                    </div><!-- /.box-header -->  
                    <!--SA and GA-->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="checkbox" name="audt_status" <?php if($data_audt_status){ echo "checked"; } ?> value="<?php if($data_audt_status){ echo 1; }else{ echo 0; } ?>">&nbsp;&nbsp;{{trans('language.status')}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.activ_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="audt_activ_date" value="<?php if($data_audt_activ_date){ echo $data_audt_activ_date; } ?>" placeholder="yyyy-mm-dd">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.count')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="audt_cnt" value="<?php //if($data_audt_cnt){ echo $data_audt_cnt; }else{ echo 0; } ?>">
                                    </div>
                                </div> -->
                                
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.exp_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="audt_exp_date" value="<?php if($data_audt_exp_date){ echo $data_audt_exp_date; } ?>" placeholder="yyyy-mm-dd / No Expiry">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="audt_module_id" value="5">
                        </div>
                    </div>
                </div>

            </div><!--// General form end-->

             <div class="col-md-6">
                <!-- company form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('language.forms_wf')}}</h3>
                    </div><!-- /.box-header -->  
                    <!--SA and GA-->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="checkbox" name="fw_status" <?php if($data_fw_status){ echo "checked"; } ?> value="<?php if($data_fw_status){ echo 1; }else{ echo 0; } ?>">&nbsp;&nbsp;{{trans('language.status')}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.activ_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="fw_activ_date" value="<?php if($data_fw_activ_date){ echo $data_fw_activ_date; } ?>" placeholder="yyyy-mm-dd">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.count')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="fw_cnt" value="<?php //if($data_fw_cnt){ echo $data_fw_cnt; }else{ echo 0; } ?>">
                                    </div>
                                </div> -->
                                
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.exp_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="fw_exp_date" value="<?php if($data_fw_exp_date){ echo $data_fw_exp_date; } ?>" placeholder="yyyy-mm-dd / No Expiry">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="fw_module_id" value="4">
                        </div>
                    </div>
                </div>

            </div><!--// General form end-->

             <div class="col-md-6">
                <!-- company form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('language.doc_anno')}}</h3>
                    </div><!-- /.box-header -->  
                    <!--SA and GA-->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="checkbox" name="docanno_status" <?php if($data_docanno_status){ echo "checked"; } ?> value="<?php if($data_docanno_status){ echo 1; }else{ echo 0; } ?>">&nbsp;&nbsp;{{trans('language.status')}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.activ_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="docanno_activ_date" value="<?php if($data_docanno_activ_date){ echo $data_docanno_activ_date; }?>" placeholder="yyyy-mm-dd">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.count')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="docanno_cnt" value="<?php //if($data_docanno_cnt){ echo $data_docanno_cnt; }else{ echo 0; } ?>">
                                    </div>
                                </div> -->
                                
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.exp_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="docanno_exp_date" value="<?php if($data_docanno_exp_date){ echo $data_docanno_exp_date; }?>" placeholder="yyyy-mm-dd / No Expiry">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="docanno_module_id" value="2">
                        </div>
                    </div>
                </div>

            </div><!--// General form end-->

            <div class="col-md-6">
                <!-- company form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('language.encrypt_decrypt')}}</h3>
                    </div><!-- /.box-header -->  
                    <!--SA and GA-->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="checkbox" name="endec_status" <?php if($data_endec_status){ echo "checked"; } ?> value="<?php if($data_endec_status){ echo 1; }else{ echo 0; } ?>">&nbsp;&nbsp;{{trans('language.status')}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.activ_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="endec_activ_date" value="<?php if($data_endec_activ_date){ echo $data_endec_activ_date; } ?>" placeholder="yyyy-mm-dd">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.count')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="endec_cnt" value="<?php //if($data_endec_cnt){ echo $data_endec_cnt; }else{ echo 0; } ?>">
                                    </div>
                                </div> -->
                                
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.exp_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="endec_exp_date" value="<?php if($data_endec_exp_date){ echo $data_endec_exp_date; } ?>" placeholder="yyyy-mm-dd / No Expiry">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="endec_module_id" value="6">
                        </div>
                    </div>
                </div>

            </div><!--// General form end-->

             <div class="col-md-6">
                <!-- company form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('language.apps')}}</h3>
                    </div><!-- /.box-header -->  
                    <!--SA and GA-->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="checkbox" name="app_status" <?php if($data_app_status){ echo "checked"; } ?> value="<?php if($data_app_status){ echo 1; }else{ echo 0; } ?>">&nbsp;&nbsp;{{trans('language.status')}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.activ_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="app_activ_date" value="<?php if($data_app_activ_date){ echo $data_app_activ_date; } ?>" placeholder="yyyy-mm-dd">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.count')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="app_cnt" value="<?php if($data_app_cnt){ echo $data_app_cnt; }else{ echo 0; } ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.exp_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="app_exp_date" value="<?php if($data_app_exp_date){ echo $data_app_exp_date; } ?>" placeholder="yyyy-mm-dd / No Expiry">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="app_module_id" value="7">
                        </div>
                    </div>
                </div>

            </div><!--// General form end-->

             <div class="col-md-6">
                <!-- company form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('language.email_doc')}}</h3>
                    </div><!-- /.box-header -->  
                    <!--SA and GA-->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="checkbox" name="emaildoc_status" <?php if($data_emaildoc_status){ echo "checked"; } ?> value="<?php if($data_emaildoc_status){ echo 1; }else{ echo 0; } ?>">&nbsp;&nbsp;{{trans('language.status')}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.activ_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="emaildoc_activ_date" value="<?php if($data_emaildoc_activ_date){ echo $data_emaildoc_activ_date; } ?>" placeholder="yyyy-mm-dd">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.count')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="emaildoc_cnt" value="<?php //if($data_emaildoc_cnt){ echo $data_emaildoc_cnt; }else{ echo 0; } ?>">
                                    </div>
                                </div> -->
                                
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.exp_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="emaildoc_exp_date" value="<?php if($data_emaildoc_exp_date){ echo $data_emaildoc_exp_date; } ?>" placeholder="yyyy-mm-dd / No Expiry">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="emaildoc_module_id" value="8">
                        </div>
                    </div>
                </div>

            </div><!--// General form end-->

             <div class="col-md-6">
                <!-- company form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('language.web_publish')}}</h3>
                    </div><!-- /.box-header -->  
                    <!--SA and GA-->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="checkbox" name="webpub_status" <?php if($data_webpub_status){ echo "checked"; } ?> value="<?php if($data_webpub_status){ echo 1; }else{ echo 0; } ?>">&nbsp;&nbsp;{{trans('language.status')}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.activ_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="webpub_activ_date" value="<?php if($data_webpub_activ_date){ echo $data_webpub_activ_date; } ?>" placeholder="yyyy-mm-dd">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.count')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="webpub_cnt" value="<?php //if($data_webpub_cnt){ echo $data_webpub_cnt; }else{ echo 0; } ?>">
                                    </div>
                                </div> -->
                                
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.exp_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="webpub_exp_date" value="<?php if($data_webpub_exp_date){ echo $data_webpub_exp_date; } ?>" placeholder="yyyy-mm-dd / No Expiry">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="webpub_module_id" value="9">
                        </div>
                    </div>
                </div>

            </div><!--// General form end-->

             <div class="col-md-6">
                <!-- company form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('language.task_manager')}}</h3>
                    </div><!-- /.box-header -->  
                    <!--SA and GA-->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="checkbox" name="taskman_status" <?php if($data_taskman_status){ echo "checked"; } ?> value="<?php if($data_taskman_status){ echo 1; }else{ echo 0; } ?>">&nbsp;&nbsp;{{trans('language.status')}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.activ_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="taskman_activ_date" value="<?php if($data_taskman_activ_date){ echo $data_taskman_activ_date; } ?>" placeholder="yyyy-mm-dd">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.count')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="taskman_cnt" value="<?php //if($data_taskman_cnt){ echo $data_taskman_cnt; }else{ echo 0; } ?>">
                                    </div>
                                </div> -->
                                
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.exp_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="taskman_exp_date" value="<?php if($data_taskman_exp_date){ echo $data_taskman_exp_date; } ?>" placeholder="yyyy-mm-dd / No Expiry">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="taskman_module_id" value="10">
                        </div>
                    </div>
                </div>

            </div><!--// General form end-->
            

             <div class="col-md-6">
                <!-- company form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('language.scanner')}}</h3>
                    </div><!-- /.box-header -->  
                    <!--SA and GA-->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="checkbox" name="scanr_status" <?php if($data_scanr_status){ echo "checked"; } ?> value="<?php if($data_scanr_status){ echo 1; }else{ echo 0; } ?>">&nbsp;&nbsp;{{trans('language.status')}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.activ_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="scanr_activ_date" value="<?php if($data_scanr_activ_date){ echo $data_scanr_activ_date; } ?>" placeholder="yyyy-mm-dd">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.count')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="scanr_cnt" value="<?php //if($data_scanr_cnt){ echo $data_scanr_cnt; }else{ echo 0; } ?>">
                                    </div>
                                </div> -->
                                
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.exp_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="scanr_exp_date" value="<?php if($data_scanr_exp_date){ echo $data_scanr_exp_date; } ?>" placeholder="yyyy-mm-dd / No Expiry">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="scanr_module_id" value="11">
                        </div>
                    </div>
                </div>

            </div><!--// General form end-->
            

             <div class="col-md-6">
                <!-- company form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('language.ocr')}}</h3>
                    </div><!-- /.box-header -->  
                    <!--SA and GA-->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="checkbox" name="ocr_status" <?php if($data_ocr_status){ echo "checked"; } ?> value="<?php if($data_ocr_status){ echo 1; }else{ echo 0; } ?>">&nbsp;&nbsp;{{trans('language.status')}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.activ_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="ocr_activ_date" value="<?php if($data_ocr_activ_date){ echo $data_ocr_activ_date; } ?>" placeholder="yyyy-mm-dd">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.count')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="ocr_cnt" value="<?php //if($data_ocr_cnt){ echo $data_ocr_cnt; }else{ echo 0; } ?>">
                                    </div>
                                </div> -->
                                
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.exp_date')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="ocr_exp_date" value="<?php if($data_ocr_exp_date){ echo $data_ocr_exp_date; } ?>" placeholder="dd-mm-yyyy / No Expiry">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="ocr_module_id" value="3">
                        </div>
                    </div>
                </div>

            </div><!--// General form end-->

<br/><br/><br/>

            <section class="content-header">
                <div class="col-sm-8">
                    <span style="float:left;">
                        <strong>{{trans('language.lic_details')}}</strong>
                    </span>
                </div>
            </section>
            <br/>

            <div class="col-md-6">
                <!-- company form elements -->
                <div class="box box-primary">
                    <!--SA and GA-->
                    <div class="box-footer" style="">
                        <div class="modal-body">    
                            <!-- form start -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.usr_lic')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="user_lic" value="<?php if($settings_no_of_users){ echo $settings_no_of_users; } ?>" placeholder="1,2,3 / Unlimited">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Email" class="col-sm-4 control-label">{{trans('language.lic_exp')}}: </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="user_lic_exp" value="<?php if($settings_expiry_date){ echo $settings_expiry_date; } ?>" placeholder="yyyy-mm-dd / No Expiry">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!--// General form end-->

            <section class="content">
            <div class="form-group">
                <div class="col-sm-12" style="text-align:right;">
                    <input type="submit" value="Save" name="save" class="btn btn-primary">
                </div>
            </div><!-- /.col -->

            </section>
        {!! Form::close() !!}     

    </div>  

</section>
