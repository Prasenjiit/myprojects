<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')


{!! Html::script('js/parsley.min.js') !!}    
{!! Html::script('js/jquery.form.js') !!}   
{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
{!! Html::style('plugins/datatables/dataTables.bootstrap.css') !!}

<!--{!! Html::script('js/build.min.js') !!} 
{!! Html::style('css/build.min.css') !!} -->
{!! Html::style('css/fastselect.min.css') !!} 
{!! Html::script('js/fastselect.standalone.js') !!}  
{!! Html::script('plugins/daterangepicker/daterangepicker.js') !!}

<!-- Content Wrapper. Contains page content -->
<style type="text/css">
    .fstElement{
        width: 100%;
    }

    @media(max-width:991px){
    .modal-body {
        position: relative;
        top: 31px;
        }
    }

</style>

    <section class="content-header">
        <div class="col-sm-8">        
            <strong>{{trans('audits.audits')}}</strong>
        </div>        
        <div class="col-sm-4">
            <!-- <ol class="breadcrumb">
                <li><a href="<?php echo  url('/home');?>"><i class="fa fa-dashboard"></i> {{trans('language.home')}}</a></li>
                <li class="active">{{trans('audits.audits')}}</li>
            </ol> -->
        </div>
        {{trans('language.input_search')}} 
    </section>

    @if(Session::has('data'))
    <section class="content content-sty" id="spl-wrn">        
        <div class="alert alert-sty {{ Session::get('alert-class', 'alert-success') }} ">{{ Session::get('data') }}</div>        
    </section>
    @endif

    @if(Session::get('enbval5')==Session::get('tval'))
            <div class="modal fade" id="dTAddModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dGAddModal" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h4 class="modal-title">
                           {{Lang::get('language.audit_record')}}
                           <small>- {{Lang::get('language.purge_audit_records')}}</small>
                       </h4>
                    </div>

                        <div class="modal-body">
                            <!-- form start -->
                            {!!Form::open(array('class'=>'form-horizontal'))!!}
                             
                                <!--<div class="form-group">
                                    <label for="clear data from" class="col-sm-4 control-label">{{$language['label_from']}}:</label>
                                    <div class="col-sm-6">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="{{@$old_date_in_audit}}" class="form-control active" id="cleardate_from" name="cleardate_from" placeholder="YYYY-MM-DD" title="Clear Data- From" data-toggle="tooltip" data-original-title="" readonly>
                                        
                                    </div>
                                    </div>
                                </div>
                                <div style="margin-top: 5px;margin-left: 300px;margin-bottom: 5px;"><span>{{$language['old_date_from_audits']}}</span></div>-->
                                <div class="form-group">
                                    <label for="clear data to" class="col-sm-4 control-label">{{trans('audits.label_Purge_audit_records_untill')}}:</label>
                                    <div class="col-sm-6">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="{{@$no_of_days}}" class="form-control active" id="cleardate_to" name="cleardate_to" placeholder="YYYY-MM-DD" title="Clear Data- To" readonly>
                                    </div>
                                    </div>
                                </div>
                                <div style="margin-top: 5px;margin-left: 300px;margin-bottom: 5px;" class="warning"><span>{{$purge_audits_info_msg}}</span></div>
                            </div>
                            <div class="modal-footer" style="text-align: center !important;">
                            
                            {!! Form::submit(Lang::get('language.purge_data'),array('class'=>"btn btn-primary",'id' => 'delete-audits-own')) !!}
                       {!! Form::close() !!}
                        <button class="btn btn-danger" id='cancel' data-dismiss="modal">{{trans('language.cancel')}}</button>
                    </div><!-- modal-footer -->
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

            <!--checking view permission-->
            @if(Auth::user()->user_role == '1')   
            <div class="modal-body"> 
                <!-- form start -->
                {!! Form::open(array('url'=> array('auditsAdvSearch'), 'files'=>true, 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'documentSrch', 'id'=> 'documentSrch','data-parsley-validate'=> '','onsubmit' => 'return validateForm()')) !!}            
                <div class="form-group">
                    <div class="col-sm-6"> 
                        {!! Form::label(trans('language.sign in name').':','', array('class'=> 'control-label'))!!} 

                        <select name="username[]" id="username" class="multipleSelect form-control" multiple>
                            <?php foreach ($username as $key => $val) { ?>
                                <option value="<?php echo $val->audit_user_name; ?>" <?php if(session::get('username')):if(in_array($val->audit_user_name,explode(',',session::get('username')))): echo 'selected';endif;endif;?> ><?php echo $val->audit_user_name;?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-6"> 
                        {!! Form::label(trans('language.item').':', '', array('class'=> 'control-label'))!!}
                        <select name="category[]" id="category" class="multipleSelect form-control" multiple>
                            <?php foreach ($category as $key => $val) { ?>
                                <option value="<?php echo $val->audit_owner; ?>" <?php if(Session::get('items')):if(in_array($val->audit_owner,explode(',',Session::get('items')))):echo 'selected';endif;endif;?> ><?php echo $val->audit_owner;?></option>
                            <?php } ?>
                        </select>
                    </div>       
                </div>
                
                <div class="form-group">
                    <div class="col-sm-6">
                        {!! Form::label(trans('language.document no').':','', array('class'=> 'control-label'))!!}
                        <input type="text" class="form-control" name="docno" id="docno" value="{{Session::get('docno')}}" />
                    </div>
                    <div class="col-sm-6">
                        {!! Form::label(trans('language.document name').':', '', array('class'=> 'control-label'))!!}
                        <input type="text" class="form-control" name="docname" id="docname" value="{{Session::get('docname')}}" />
                    </div>       
                </div>
                
                <div class="form-group">
                    <div class="col-sm-6">  
                        {!! Form::label(trans('stack.stacks').':','', array('class'=> 'control-label'))!!}
                        <select name="stacks[]" id="stacks" class="multipleSelect form-control" multiple>
                            <?php foreach ($stacks as $key => $val) { ?>
                                <option value="<?php echo $val->stack_id; ?>" @if(Session::get('stacks')) @if(in_array($val->stack_id,explode(',',Session::get('stacks')))) selected @endif @endif ><?php echo @$val->stack_name;?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-6"> 
                        <label class="control-label">@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.department')}} @endif:</label>
                        <select name="dept[]" id="dept" class="multipleSelect form-control" multiple>
                            <?php foreach ($depts as $key => $val) { ?>
                                <option value="<?php echo $val->department_id; ?>"  @if(Session::get('dept')) @if(in_array($val->department_id,explode(',',Session::get('dept')))) selected @endif @endif ><?php echo @$val->department_name;?></option>
                            <?php } ?>
                        </select>
                    </div>        
                </div>
                
                <div class="form-group">
                	<div class="col-sm-6"> 
                        {!! Form::label(trans('language.document type').':', '', array('class'=> 'control-label'))!!}
                        
                        <select name="dctype[]" id="dctype" class="multipleSelect form-control" multiple>

                            <?php 
                            foreach ($doctypes as $key => $val) { ?>
                                <option value="<?php echo $val->document_type_id; ?>" @if(Session::get('document_type_ids')) @if(in_array($val->document_type_id,explode(',',Session::get('document_type_ids')))) selected @endif @endif ><?php echo @$val->document_type_name;?></option>
                            <?php } ?>
                        </select>
                    </div>    
                    
                    <div class="col-sm-6"> <?php echo Session::get('actions'); ?>
                        {!! Form::label(trans('language.action').':','', array('class'=> 'control-label'))!!}
                        <select name="actions[]" id="actions" class="multipleSelect form-control" multiple>
                            <?php foreach ($action as $key => $val) { ?>
                                <option value="<?php echo $val->audit_action_type; ?>" @if(Session::get('actions')) @if(in_array($val->audit_action_type,explode(',',Session::get('actions')))) selected @endif @endif ><?php echo $val->audit_action_type;?></option>
                            <?php } ?>
                        </select>
                    </div>
                   
                </div>

               
               
                <div class="form-group">
                    <div class="col-sm-6">
                        {!! Form::label(trans('language.date_from').':', '', array('class'=> 'control-label'))!!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" value="{{Session::get('date_from')}}" class="form-control active" id="createddate_from" name="createddate_from" placeholder="YYYY-MM-DD" title="Created Date- From" data-toggle="tooltip" data-original-title="">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        {!! Form::label(trans('language.date_to').':', '', array('class'=> 'control-label'))!!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" value="{{Session::get('date_to')}}" class="form-control active" id="createddate_to" name="createddate_to" placeholder="YYYY-MM-DD" title="Created Date - To" data-toggle="tooltip" data-original-title="">
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-top:30px;margin-bottom:185px;">
                    <div class="col-sm-12" style="text-align:right;">                
                            {!!Form::submit(trans('language.search'), array('class' => 'btn btn-primary', 'id'=> 'saveEdi')) !!}&nbsp;&nbsp;
                            <a href="{{URL::route('auditsList')}}" class = "btn btn-primary">{{trans('language.show all')}}</a>&nbsp;&nbsp;
                            <!--Delete from audita-->
                            <a href="" data-toggle="modal" @if(@$is_old_date_valid=='No') id="no-records" @else data-target="#dTAddModal" @endif>{{Lang::get('language.purge_audit_records')}}</a>                
                    </div>
                </div><!-- /.col -->

                {!! Form::close() !!}        
            </div>
            @endif
        @elseif(Session::get('enbval5')==Session::get('fval'))
            <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{trans('language.purchase_now')}}</div></section>
        @else
            <section class="content" style="min-height: 797px;"><div class="alert alert-danger alert-sty">{{trans('language.module_expired')}}</div></section>
        @endif


<script>
$('#spl-wrn').delay(5000).slideUp('slow');

    $(document).ready(function() {

        var d           = new Date();
        var currentYear = d.getFullYear();
        var newDate     = currentYear+10;
        var date        = '12/31/'+newDate;
       
        $('.multipleSelect').fastselect();

        $('#createddate_from').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
            maxDate: moment(date),
            showDropdowns: true
        });

        $('#createddate_to').daterangepicker({
            singleDatePicker: true,
            "drops": "up",
            maxDate: moment(date),
            showDropdowns: true
        });

        //<!--Datepicker in purge audits-->
        var no_of_days = "{{@$no_of_days}}";
        var old_date_in_audit      = "{{@$old_date_in_audit}}";
        $('#cleardate_to').daterangepicker({
            singleDatePicker: true,
            "drops": "down",
            minDate: old_date_in_audit,
            maxDate: no_of_days,
            showDropdowns: true

        });

        // No records in audits then show msg
        $('body').on('click','#no-records',function(){
            swal("{{trans('language.no_records_to_delete_audit')}}");
        });
              
    });

    function validateForm() {
        var texta = document.forms["documentSrch"]["username"].value;
        var textb = document.forms["documentSrch"]["category"].value;
        var textc = document.forms["documentSrch"]["actions"].value;
        var textd = document.forms["documentSrch"]["createddate_from"].value;
        var texte = document.forms["documentSrch"]["createddate_to"].value;
		
		var textf = document.forms["documentSrch"]["docno"].value;
		var textg = document.forms["documentSrch"]["docname"].value;		
		var texth = document.forms["documentSrch"]["stacks"].value;
		var texti = document.forms["documentSrch"]["dept"].value;
		var textj = document.forms["documentSrch"]["dctype"].value;
		
        if((texta=="") && (textb=="") && (textc=="") && (textd=="") && (texte=="") && (textf=="") && (textg=="") && (texth=="") && (texti=="") && (textj=="")){
            swal("{{trans('language.no entry msg')}}");
            return false;
        }
    }

    // Approve and delete audits record
    $('body').on('click','#delete-audits-own',function(e){ 
        e.preventDefault();
        $('#cancel').trigger('click');
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var date_from_clear = "{{@$old_date_in_audit}}";

        var date_to_clear = $('#cleardate_to').val();
        var ownUser       = 'Yes';
        ///
        swal({
              title: "{{trans('language.Swal_are_you_sure')}}",
              text: "{{trans('language.Swal_not_revert')}}",
              type: "{{trans('language.Swal_warning')}}",
              showCancelButton: true
            }).then(function (result) {
                if(result){
                    // Success
                    var count_super_admin = '<?php echo @$count_superadmin;?>';
                    if(count_super_admin > 1)//more than one super admin
                    {
                        swal({
                              title: "{{trans('language.for_security_reson_msg')}}"
                            }).then((result) => {
                                 $.ajax({
                                    type:'POST',
                                    url:'{{URL('AuditDelete_Notifications')}}',
                                    data:{_token:CSRF_TOKEN,date_from_clear:date_from_clear,date_to_clear:date_to_clear},
                                    success:function(data)
                                    {
                                        if(data == 'false'){
                                            // Show msg
                                            swal("{{trans('language.already_proceed_this_action')}}");
                                        }else if(data == 'Success'){
                                            // Success
                                            window.location.reload();
                                        }else{
                                            // failed
                                            swal("{{trans('language.msg_not_send')}}");
                                        }                                
                                    }

                                });
                            });    
                    }else if(count_super_admin == 1)//one superadmin
                    {

                    swal({
                          title: "{{trans('language.plse_entr_ur_pswd')}}", 
                          input: 'password',
                          showCancelButton: true
                        }).then((result) => {
                            if(result){
                                // Function defined in app.blade.php
                                deleteAuditsRecords(CSRF_TOKEN,null,date_from_clear,date_to_clear,result,ownUser,null);
                             
                            }else{
                              swal("{{trans('language.password_is_required')}}");
                            }

                        });
                    }//End
                    
                }
            });
    });
    

</script>
<style type="text/css">
    .form-group {
        margin-bottom: 0px;
    }
    .fstControls{
        width: 100% !important;
    }
</style>
@endsection

