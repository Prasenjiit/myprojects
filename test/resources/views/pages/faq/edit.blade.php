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
<div class="modal-header">
        <strong>{{$language['faqs']}}</strong>
        <small>- {{$language['edit']}} {{$language['faq']}}: {{ $faq[0]->faq_title }}</small>    
</div>
<div class="modal-body"> 
    <!-- form start -->
            {!! Form::open(array('url'=> array('faqSave'), 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'addForm', 'id'=> 'addForm','data-parsley-validate'=> '')) !!}
            <!--Hidden fields--> 
            <input type="hidden" value="{{ $faq[0]->faq_id }}" name="faq_id"><!--To update faq-->  
            <div class="form-group">
                <label class="col-sm-2 control-label">{{$language['faq']}}: <span class="compulsary">*</span></label>
                <div class="col-sm-8">
                    <input class="form-control" id="name" placeholder="{{$language['faq']}}" required="" data-parsley-required-message="{{$language['faq']}} {{$language['is_required']}}" autofocus="autofocus" name="faq_title" value="{{ $faq[0]->faq_title }}"  title="{{$language['faq']}}">    
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('Description:', '', array('class'=> 'col-sm-2 control-label'))!!}
                <div class="col-sm-8">
                    {!! Form:: 
                    textarea(
                    'description', $faq[0]->faq_description, 
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
                <label class="col-sm-2 control-label"></label>
                <div class="col-sm-8" style="text-align:right;">
                    <input class="btn btn-primary" id="save" type="submit" value="{{$language['save']}}" onclick="myFunction()"> &nbsp;&nbsp;
                    <a href="{{url('/faqs')}}">{!!Form::button($language['cancel'], array('class' => 'btn btn-primary btn-danger', 'id' => 'cn', 'data-dismiss'=> 'modal', 'aria-hidden'=> 'true')) !!}</a>
                </div>
            </div><!-- /.col -->
            {!! Form::close() !!}
        </div>
</div><!-- /.modal-dialog -->
<style type="text/css">
    [draggable] {
      -khtml-user-drag: element;
      -webkit-user-drag: element;
    }
    div.grippy {
      content: '....';
      width: 10px;
      
      display: inline-block;
      line-height: 5px;
      padding: 3px 4px;
      cursor: move;
      vertical-align: middle;
      margin-top: -.7em;
      margin-right: .3em;
      font-size: 14px;
      font-family: sans-serif;
      letter-spacing: 2px;
      color: black;
      text-shadow: 1px 0 1px black;
    }
    div.grippy::after {
      content: '.. .. .. ..';
    }
    .slide-placeholder {
        background: #ecf0f5;
        position: relative;
    }
    .slide-placeholder:after {
        content: " ";
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 0;
        background-color: #FFF;
    }
</style>
@endsection