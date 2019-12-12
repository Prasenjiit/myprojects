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


<section class="content-header">
    <div class="col-sm-8">
        <span style="float:left;">
            <strong>{{$language['faqs']}}</strong> &nbsp;
        </span>
        <span style="float:left;">
            <?php 
                $user_permission=Auth::user()->user_permission;
            ?>     
            @if(Auth::user()->user_role == 1)
                <a href="" data-toggle="modal" data-target="#dTAddModal">
                    <button class="btn btn-block btn-info btn-flat newbtn">{{$language['add new']}} <i class="fa fa-plus"></i></button>
                </a> 
            @endif
        </span>
    </div>
    <div class="col-sm-4">
        <!-- <ol class="breadcrumb">
          <li><a href="{{url('/home')}}"><i class="fa fa-dashboard"></i> {{$language['home']}}</a></li>
          <li class="active">{{$language['faqs']}}</li>
        </ol> -->
    </div>
</section>

<section class="content" id="shw">

    <!--Show message after insert and update-->
    @if (session('status'))
    <div class="alert alert-success" id="hide-div">
        <p style="text-align: center;"><strong>Success!</strong> {{ session('status') }}</p>
    </div>
    @endif

    <div class="row">
        <div class="col-xs-12">
            <div class="box">                
                <?php
                include (public_path()."/storage/includes/lang1.en.php" );
                ?>
                <div class="box-body">
                    <div class="row">
                    @if($faq)
                        <div class="panel-group col-sm-12" style="margin-bottom: 0px !important;">
                        @foreach($faq as $val)
                            <div class="panel panel-default" id="collapseDiv{{$val->faq_id}}">
                              <div class="panel-heading">
                                <h4 class="panel-title">
                                  <a data-toggle="collapse" href="#collapse{{$val->faq_id}}">{{$val->faq_title}}</a> 
                                    <!--Edit-->
                                    @if(Auth::user()->user_role == 1)
                                    <div style="float: right;">
                                        <a href="{{url('/faqEdit')}}?faqId={{$val->faq_id}}" title="Edit">
                                        <i class="fa fa-pencil" style="cursor:pointer;"></i>
                                        </a>
                                        <i class="fa fa-close faq-delete" title="{{$val->faq_title}}" faqId="{{$val->faq_id}}" title="Delete" style="color: red; cursor:pointer;"></i>
                                    </div>
                                    @endif
                                </h4>
                              </div>
                              <div id="collapse{{$val->faq_id}}" class="panel-collapse collapse">
                                <ul class="list-group">
                                  <li class="list-group-item">{{$val->faq_description}}</li>
                                </ul>
                              </div>
                            </div>
                        @endforeach
                        </div>
                    @else
                        <p id="no_msg" style="text-align: center;font-weight: 600;">There is no {{$language['faqs']}} found.</p>
                    @endif
                    </div>                    
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
  </div><!-- /.row -->
</section>
<!-- User add Form -->
<div class="modal fade" id="dTAddModal" data-backdrop="false" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dGAddModal" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                 {{$language['faqs']}}
                 <small>- {{$language['add new']}} {{$language['faq']}}</small>
             </h4>
         </div>
         <div class="modal-body">
            <!-- form start -->
            {!! Form::open(array('url'=> array('faqSave'), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'addForm', 'id'=> 'addForm','data-parsley-validate'=> '')) !!}
                        
            <div class="form-group">
                <label class="col-sm-2 control-label">{{$language['faq']}}: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <input class="form-control" id="name" placeholder="{{$language['faq']}}" required="" data-parsley-required-message="{{$language['faq']}} {{$language['is_required']}}" autofocus="autofocus" name="faq_title" value=""  title="{{$language['faq']}}">    
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('Description:', '', array('class'=> 'col-sm-2 control-label'))!!}
                <div class="col-sm-8">
                    {!! Form:: 
                    textarea(
                    'description', '', 
                    array( 
                    'class'                  => 'form-control', 
                    'id'                     => 'description',  
                    'placeholder'            => $language['description'],
                    'name'                   => 'faq_description',
                    'required'               => '',
                    'data-parsley-required-message'=>$language['description_is_required']
                    )
                    ) 
                    !!}
                </div>
            </div>
                
          <div>
            <div class="form-group">
                <label class="col-sm-4 control-label"></label>
                <div class="col-sm-8">
                    <input class="btn btn-primary" id="save" type="submit" value="{{$language['save']}}" onclick="myFunction()"> &nbsp;&nbsp;
                    {!!Form::button($language['close'], array('class' => 'btn btn-primary btn-danger', 'id' => 'cn', 'data-dismiss'=> 'modal', 'aria-hidden'=> 'true')) !!}
                </div>
            </div><!-- /.col -->
            {!! Form::close() !!}
        </div>
  </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
</div>

<script type="text/javascript">
$(document).ready(function(){
    // Delete faq
    $('.faq-delete').click(function(){
        var faqId = $(this).attr('faqId');    
        var title = $(this).attr('title');    
        // Delete
        swal({
              title: '{{$language['Swal_are_you_sure']}}',
              text: "{{$language['Swal_not_revert']}}",
              type: "{{$language['Swal_warning']}}",
              showCancelButton: true,
            }).then(function (result) {
                if(result){
                    // Success
                   $.ajax({
                        type:'GET',
                        url:base_url+'/deleteFaq',
                        data:'faqId='+faqId+'&title='+title,
                        success:function(result){
                            console.log('success');
                        }
                    });
                   // Remove the div
                   $('#collapseDiv'+faqId).remove();
                   // reload the same page
                }
                swal(
                '{{$language['Swal_deleted']}}'
                )
            });
    });
});  
</script>

@endsection

