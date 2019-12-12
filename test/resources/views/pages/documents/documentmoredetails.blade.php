<?php 
include (public_path()."/storage/includes/lang1.en.php" );
?>

<div class="modal-dialog modal-lg">               
    <div class="modal-content">

        <div class="modal-header" style="border-bottom-color: deepskyblue;">
            <h4 class="modal-title">
                {{$language['all documents']}}
                <small>- View All Data</small>
            </h4>
        </div>
        <div class="modal-body">  

            <div class="row">
                <div class="col-sm-2">
                    <label for="Document No :" class="control-label">{{$language['document no']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="docNo">{{$moreDetails[0]->document_no}}</p>   
                </div>

                <div class="col-sm-2">
                  <label for="Document Name :" class="control-label">{{$language['document name']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="docName">{{$moreDetails[0]->document_name}}</p>
                </div>         
            </div>

            <div class="row">
                <div class="col-sm-2">
                    <label for="Document Type :" class="control-label">{{$language['document type']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="docType">{{$moreDetails[0]->document_type_names[0]->document_type_names}}</p>
                </div>

                <div class="col-sm-2">
                    <label for="Document Path :" class="control-label">{{$language['document path']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="document_path">{{$moreDetails[0]->document_path}}</p>
                </div>

            </div>

            <div class="row">
                <div class="col-sm-2">
                  <label for="Version No :" class="control-label">{{$language['version no']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="version_no">{{$moreDetails[0]->document_version_no}}</p>
                </div> 

                <div class="col-sm-2">
                    <label for="Status :" class="control-label">{{$language['status']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="status">{{$moreDetails[0]->document_status}}</p>
                </div>       
            </div>

            <div class="row">

                <div class="col-sm-2">
                  <label for="Document File Name :" class="control-label">{{$language['file Name']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="document_file_name">{{$moreDetails[0]->document_file_name}}</p>
                </div>

                <div class="col-sm-2">
                  <label for="Last Updated :" class="control-label">{{$language['department']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="department">{{$moreDetails[0]->departments[0]->department_names}}</p>
                </div>  

            </div>

            <div class="row">

                <div class="col-sm-2">
                  <label for="Stacks :" class="control-label">{{$language['stacks']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="stacks">{{$moreDetails[0]->stacks[0]->stack_names}}</p>
                </div>

                <div class="col-sm-2">
                    <label for="tagwords :" class="control-label">{{$language['tag words']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="tagwords">{{$moreDetails[0]->tagwords[0]->tagwords_titles}}</p>
                </div>

            </div>

            <div class="row">
                <div class="col-sm-2">
                  <label for="Document Checkout Date :" class="control-label">{{$language['last check out date']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="document_checkout_date">{{$moreDetails[0]->document_checkout_date}}</p>
                </div>

                <div class="col-sm-2">
                    <label for="Document Checkin Date :" class="control-label">{{$language['last check in date']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="document_checkin_date">{{$moreDetails[0]->document_checkin_date}}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-2">
                    <label for="Document Created By :" class="control-label">{{$language['created by']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="document_created_by">{{$moreDetails[0]->document_created_by}}</p>
                </div>

                <div class="col-sm-2">
                  <label for="Document Modified By :" class="control-label">{{$language['last modified by']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="document_modified_by">{{$moreDetails[0]->document_modified_by}}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-2">
                    <label for="Created At :" class="control-label">{{$language['created at']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="created_at">{{$moreDetails[0]->created_at}}</p>
                </div>

                <div class="col-sm-2">
                   <label for="Updated At :" class="control-label">{{$language['last updated at']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="updated_at">{{$moreDetails[0]->updated_at}}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-2">
                    <label for="Ownership :" class="control-label">{{$language['ownership']}}: </label>
                </div>
                <div class="col-sm-3">
                    <p style="border: yellowgreen;" id="ownership">{{$moreDetails[0]->document_ownership}}</p>
                </div>
            </div>

            @foreach($xtraDetails as $docColms)
                <div class="row">
                    <div class="col-sm-2">
                        <label class="control-label">{{$docColms->document_column_name}}: </label>
                    </div>
                    <div class="col-sm-3">
                        <p style="border: yellowgreen;">{{$docColms->document_column_value}}</p>
                    </div>
                </div>
            @endforeach

            <a href="#">
                <button class="btn btn-primary btn-danger" id="cn" data-dismiss="modal" type="button">{{$language['close']}}</button>
            </a>

        </div><!-- /.modal-dialog -->
    </div>
</div>
    
       
