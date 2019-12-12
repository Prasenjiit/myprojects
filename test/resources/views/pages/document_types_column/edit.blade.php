
<div class="modal-header">
    <h4 class="modal-title">
     Document Type Column Management
     <small>- Edit Document Type Column: {{ $docTypeCol->document_types_column_name }}</small>
 </h4>
</div>
<div class="modal-body">

    <!-- form start -->
    {!! Form::open(array('url'=> array('documentTypeColumnSave',$docTypeCol->document_types_column_id), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'documentTypeAddForm', 'id'=> 'documentTypeAddForm','data-parsley-validate'=> '')) !!}            

    <div class="form-group">
        {!! Form::label('Doc Type :', '', array('class'=> 'col-sm-4 control-label'))!!}
        <div class="col-sm-8">
            <select name="doctypeid" id="doctypeid" class="form-control" data-parsley-required-message="Document type name is required" required >
                <option value="">Select a doc type</option>
             <?php
             foreach ($docType as $key => $dType) {
                 ?>
                <option value="<?php echo $dType->document_types_id; ?>" <?php if($docTypeCol->document_types_id == $dType->document_types_id) echo 'selected'; ?>>
                    <?php echo $dType->document_types_name;?>
                </option>
                 <?php
             }
             ?>
         </select>                          
     </div>
 </div>

 <div class="form-group">
    {!! Form::label('Doc Type Column Name :', '', array('class'=> 'col-sm-4 control-label'))!!}
    <div class="col-sm-8">
        {!! Form:: 
        text('colname_edi', $docTypeCol->document_types_column_name, 
        array( 
        'class'=> 'form-control', 
        'id'=> 'colname_edi', 
        'title'=> 'Column Name', 
        'placeholder'=> 'Column Name',
        'required'               => '',
        'data-parsley-required-message' => 'Document type column name is required',
        'data-parsley-trigger'          => 'change focusout',
        'onchange'                      => 'duplication()'
        )
        ) 
        !!}      
        <div id="dp_edi">
                <span id="dp_wrn_edi" style="display:none;">
                    <i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>
                    <span class="">Please wait...</span>
                </span>
            </div>
    </div>
</div>
<div class="form-group">
    {!! Form::label('Doc Type Column Type :', '', array('class'=> 'col-sm-4 control-label'))!!}
    <div class="col-sm-8">
        <select name="coltype" id="coltype" class="form-control" data-parsley-required-message="Document type column type is required" required >
            <option value="">Select a type</option>
            <option value="interger" <?php if($docTypeCol->document_types_column_type == 'interger') echo 'selected'; ?>>Interger</option>
            <option value="string" <?php if($docTypeCol->document_types_column_type == 'string') echo 'selected'; ?>>String</option>
     </select>      

 </div>
</div>
<input type="hidden" id="edit_val" name="edit_val" value="{{$docTypeCol->document_types_column_id}}">
<input type="hidden" id="oldVal" name="oldVal" value="{{$docTypeCol->document_types_column_name}}">

<div class="form-group">
    <label class="col-sm-4 control-label"></label>
    <div class="col-sm-8">
        {!!Form::submit('Save', array('class' => 'btn btn-primary', 'id' => 'save')) !!} &nbsp;&nbsp;

        {!!Form::button('Cancel', array('class' => 'btn btn-primary btn-danger', 'id' => 'cnEdi', 'data-dismiss'=> 'modal', 'aria-hidden'=> 'true')) !!}
        <!-- </a> -->
    </div>
</div><!-- /.col -->
{!! Form::close() !!}
</div>

<script>
    $(function ($) {        
        $("#cnEdi").click(function() {
            window.location.reload();
        });
    });

</script>    