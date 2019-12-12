<?php 
include (public_path()."/storage/includes/lang1.en.php" );
foreach ($dglist as $val){ ?>
    <div class="col-sm-6">
        <div class="row evenclr">
            <div class="col-xs-5">
                <label for="{{$settings_document_no}} :" class="control-label"><?php if(@$val->documentTypes[0]->document_type_column_no){ echo @$val->documentTypes[0]->document_type_column_no; }else{ echo $settings_document_no; }?>: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;"><?php echo $val->document_no; ?></p>   
            </div>
        </div>
        <div class="row odd">
            <div class="col-xs-5">
                <label for="{{$settings_document_name}} :" class="control-label"><?php if(@$val->documentTypes[0]->document_type_column_name){ echo @$val->documentTypes[0]->document_type_column_name; }else{ echo $settings_document_name; }?>: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;" id="docName"><?php echo $val->document_name; ?></p>
            </div>    
        </div>

        <div class="row evenclr">
            <div class="col-xs-5">
                <label for="Document Type :" class="control-label" ><?php echo $language['document type'];?>: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;" id="docType"><?php if(@$val->documentTypes_name[0]->document_type_names==''){ echo"-"; }else{ echo @$val->documentTypes_name[0]->document_type_names; }?></p>
            </div>
        </div>

        <?php $k = 0; ?>
        <?php foreach($val->document_type_columns as $dtc ):?>
            <?php if($k%2 == 0){ ?>
            <div class="row odd">
        <?php }else{ ?>
            <div class="row evenclr">
        <?php } ?>  

            <div class="col-xs-5">
                <label class="control-label"><?php echo $dtc->document_column_name; ?>: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;">
                    <?php if($dtc->document_column_type == 'Date'){ $document_column_value = custom_date_Format($dtc->document_column_value);  }else{ $document_column_value = $dtc->document_column_value; } echo $document_column_value; ?>                  
                </p>
            </div>
        </div>
        <?php $k++; ?>
        <?php endforeach;?>
        <?php $k++; ?>
        <?php if($k%2==0){ ?>
            <div class="row evenclr">
        <?php }else{ ?>
            <div class="row odd">
        <?php } ?>           
                <div class="col-xs-5">
                    <label for="Last Updated :" class="control-label"><?php if(Session::get('settings_department_name')){ echo Session::get('settings_department_name'); }else{ echo $language['department']; } ?>: </label>
                </div>
                <div class="col-xs-7">
                    <p style="border: yellowgreen;" id="department"><?php if(@$val->department[0]->department_name==''){ echo"-"; }else{ echo @$val->department[0]->department_name; }?></p>
                </div>  
            </div>
     
       
        <?php if($k%2==0){ ?>
            <div class="row odd">
        <?php }else{ ?>
            <div class="row evenclr">
        <?php } ?>   
            <div class="col-xs-5">
                <label for="Stacks :" class="control-label" ><?php echo $language['stacks'];?>: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;" id="stacks"><?php if(@$val->stacks[0]->stack_name==''){ echo"-"; }else{ echo @$val->stacks[0]->stack_name; }?></p>
            </div>
        </div>

        <?php if($k%2==0){ ?>
            <div class="row evenclr">
        <?php }else{ ?>
            <div class="row odd">
        <?php } ?>
            <div class="col-xs-5">
                <label for="tagwords :" class="control-label" ><?php echo $language['tag words'];?>: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;" id="tagwords"><?php if(@$val->tagwords[0]->tagwords_title==''){ echo"-"; }else{ echo @$val->tagwords[0]->tagwords_title;}?></p>
            </div>
        </div>

        <?php if($k%2==0){ ?>
            <div class="row odd">
        <?php }else{ ?>
            <div class="row evenclr">
        <?php } ?>
            <div class="col-xs-5">
                <label for="tagwords :" class="control-label" ><?php echo $language['version no'];?>: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;" id="tagwords"><?php if(@$val->document_version_no==''){ echo"-"; }else{ echo @$val->document_version_no;}?></p>
            </div>
        </div>
    </div>


    
    <div class="col-sm-6">
        <div class="row evenclr">
            <div class="col-xs-5">
              <label for="Document File Name :" class="control-label" ><?php echo $language['file Name'];?>: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;" id="document_file_name"><?php echo $val->document_file_name; ?></p>
            </div>
        </div>
        <div class="row odd">
            <div class="col-xs-5">
                <label for="Status :" class="control-label"><?php echo $language['status'];?>: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;" id="status"><?php echo $val->document_status; ?></p>
            </div> 
        </div>    
        <div class="row evenclr">
            <div class="col-xs-5">
                <label for="Document Path :" class="control-label" ><?php echo $language['document path'];?>: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;" id="document_path"><?php echo $val->document_path; ?></p>
            </div>
        </div>

        <div class="row odd">
            <div class="col-xs-5">
                <label for="Ownership :" class="control-label" ><?php echo $language['ownership'];?>: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;" id="ownership"><?php echo @$val->document_ownership; ?></p>
            </div>
        </div>

        <div class="row evenclr">
            <div class="col-xs-5">
                <label for="Checkout :" class="control-label" ><?php echo $language['last check out date'];?>: </label>
            </div>
            <div class="col-xs-7">
              <p style="border: yellowgreen;" id="document_checkout_date">
                                                        <?php $document_checkout_date = dtFormat(@$val->document_checkout_date); if($document_checkout_date ==''){ echo"-"; }else{ echo $document_checkout_date; }?></p>
            </div>
        </div>


        <div class="row odd">
            <div class="col-xs-5">
                <label for="Document Checkin Date :" class="control-label" ><?php echo $language['last check in date'];?>: </label>
            </div>
            <div class="col-xs-7">
                  <p style="border: yellowgreen;" id="document_checkin_date">
                                                         <?php $document_checkin_date = dtFormat(@$val->document_checkin_date); if($document_checkin_date ==''){ echo"-"; }else{ echo $document_checkin_date; }?>    
                                                        </p>
            </div>
        </div>

        <div class="row evenclr">
            <div class="col-xs-5">
                <label for="Created At :" class="control-label" ><?php echo $language['created at'];?>: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;" id="created_at"> <?php echo dtFormat(@$val->created_at);?></p>
            </div>
        </div>

        <div class="row odd">
            <div class="col-xs-5">
                <label for="Document Created By :" class="control-label" ><?php echo $language['created by'];?>: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;" id="document_created_by"><?php echo @$val->document_created_by;?></p>
            </div>
        </div>

        <div class="row evenclr">
            <div class="col-xs-5">
               <label for="Updated At :" class="control-label" ><?php echo $language['last updated at'];?>: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;" id="updated_at"><?php if(@$val->updated_at==''){ echo"-"; }else{ echo dtFormat(@$val->updated_at); }?></p>
            </div>
        </div>

         <div class="row odd">
            <div class="col-xs-5">
                <label for="Document Modified By :" class="control-label" ><?php echo $language['last modified by'];?>: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;" id="document_modified_by"><?php if($val->document_modified_by==''){ echo "-"; }else{ echo $val->document_modified_by;}?></p>
            </div>
        </div>
        <div class="row evenclr">
            <div class="col-xs-5">
               <label for="Size :" class="control-label" >Size: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;" id="size">
                <?php $bytes= @$val->size;
                    if ($bytes >= 1000000000)
                    {
                        $bytes = number_format($bytes / 1000000000, 2) . ' GB';
                    }
                    elseif ($bytes >= 1000000)
                    {
                        $bytes = number_format($bytes / 1000000, 2) . ' MB';
                    }
                    elseif ($bytes >= 1000)
                    {
                        $bytes = number_format($bytes / 1000, 2) . ' KB';
                    }
                    elseif ($bytes > 1)
                    {
                        $bytes = $bytes . ' bytes';
                    }
                    elseif ($bytes == 1)
                    {
                        $bytes = $bytes . ' byte';
                    }
                    else
                    {
                        $bytes = '0 bytes';
                    }

                    echo $bytes;
                ?>
                </p>
            </div>
        </div>
        <div class="row odd">
            <div class="col-xs-5">
                <label for="Document Assigned To :" class="control-label" ><?php echo $language['doc_assigned_to'];?>: </label>
            </div>
            <div class="col-xs-7">
                <p style="border: yellowgreen;" id="document_assigned_to"><?php if(@$val->document_assigned_to==''){ echo "-"; }else{ echo ucfirst(@$val->document_assigned_to);}?></p>
            </div>
        </div>
        <!-- encrypted details -->
        <?php if(@$val->document_encrypt_status==1){?>
            <div class="row evenclr">
                <div class="col-xs-5">
                    <label for="Document encrypted on :" class="control-label" ><?php echo $language['doc_encrypted_at'];?>: </label>
                </div>
                <div class="col-xs-7">
                     <p style="border: yellowgreen;" id="document_encrypted_at"><?php if(@$val->document_encrypted_on==''){ echo "-"; }else{ echo dtFormat(@$val->document_encrypted_on);}?></p>
                </div>
            </div>
            <div class="row odd">
                <div class="col-xs-5">
                    <label for="Document encrypted by :" class="control-label" ><?php echo $language['doc_encrypted_by'];?>: </label>
                </div>
                <div class="col-xs-7">
                    <p style="border: yellowgreen;" id="document_encrypted_by"><?php if(@$val->document_encrypted_by==''){ echo "-"; }else{ echo ucfirst(@$val->document_encrypted_by);}?></p>
                </div>
            </div>
            <div class="row evenclr">
                <div class="col-xs-5">
                    <label for="Document decrypted on :" class="control-label" ><?php echo $language['doc_decrypted_at'];?>: </label>
                </div>
                <div class="col-xs-7">
                    <p style="border: yellowgreen;" id="document_decrypted_at"><?php if(@$val->document_decrypted_on==''){ echo "-"; }else{ echo @$val->document_decrypted_on;}?></p>
                </div>
            </div>
            <div class="row odd">
                <div class="col-xs-5">
                    <label for="Document decrypted by :" class="control-label" ><?php echo $language['doc_decrypted_by'];?>: </label>
                </div>
                <div class="col-xs-7">
                    <p style="border: yellowgreen;" id="document_decrypted_by"><?php if(@$val->document_decrypted_by==''){ echo "-"; }else{ echo ucfirst(@$val->document_decrypted_by);}?></p>
                </div>
            </div>
        <?php } ?>
        <!-- encrypted details end -->
    </div>  
<?php } ?>

<div class="col-sm-12" style="margin-top:10px;">
    <h4><?php echo $language['notes'];?></h4>
    <div id="notes-view">
        <table class="sidbox" id="notearea" style="width:100%">
            <tbody>  
                 <tr>
                    <th class="rowstyllft  head"><?php echo $language['note'];?></th>
                    <th class="rowstyllft  head"><?php echo $language['date'];?></th> 
                    <th class="rowstyllft  head"><?php echo $language['by'];?></th>
                  </tr>                                           
                <?php $incr = 1; ?>
                <?php if(count($noteList)>0){ ?>
                    <?php foreach ($noteList as $key => $val){ 
                        if(($incr % 2) == 1){ ?>
                            <tr class="oddcol">
                        <?php }else{ ?>
                            <tr class="evncol">
                        <?php } ?>
                            <td class="rowstyllft col-md-4"><div style="max-height:200px; overflow-y:auto;"><?php echo @$val->document_note; ?></div></td>
                            <td class="rowstylmdl col-md-4"><?php echo dtFormat($val->created_at);?></td>
                            <td class="rowstylrgt col-md-4"><?php echo @$val->document_note_created_by;?></td>
                        </tr>                                    
                        <?php $incr++; ?>                     
                    <?php } ?>
                <?php }else{ ?>
                
                    <tr class="oddcol" style="width:100%;">
                        <td class="rowstyllft col-md-4"></td>
                        <td class="rowstylmdl col-md-4" style="text-align:center;">No note found</td>
                        <td class="rowstylrgt col-md-4"></td>
                    </tr>
                
                <?php } ?>
            </tbody>
        </table>
    </div>
 </div>         


<style type="text/css">
    .evenclr{
        background-color: #e6faff;
        padding-top: 8px;
    }
    .odd{
        padding-top: 8px;
    }

    .oddcol{
        height: 40px;
        background: #e6faff;
    }
    .evncol{
        height: 40px;
        background: #FFFFFF;
    }

    .head {
        background: #00c0ef none repeat scroll 0 0;
        color: #ffffff;
        height: 40px;
        padding-left: 10px;
        padding-top: 10px;
    }

</style>