<?php
include (public_path()."/storage/includes/lang1.en.php" );
?>
@extends('layouts.app')
@section('main_content')

{!! Html::style('css/font-awesome.min.css') !!} 
{!! Html::style('css/ionicons.min.css') !!} 
<style type="text/css">
@media(max-width: 991px){
    .content-header {
        padding: 22px 15px 0 !important;
        height: 48px;
    }
    .content-header>h1 {
        margin-top: -19px;
    }
}

@media (max-width: 767px){
    .content-header {
        padding: 15px 0px 0 !important;
        height: 20px;
    }
}

/*<--mobile view table-->*/
@media(max-width:500px){
    .box{
        overflow-x: auto;
    }
}

</style>

<!-- Content Wrapper. Contains page content -->
    <section class="content-header">
        <div class="col-sm-12">
            <strong>
            {{$language['tag words']}}
            </strong>
        </div>
       
    </section>
    @if(Session::has('flash_message_edit'))
    <section class="content content-sty" id="spl-wrn">        
        <div class="alert alert-sty {{ Session::get('alert-class', 'alert-info') }} ">{{ Session::get('flash_message_edit') }}</div>        
    </section>
    @endif

    <!--Success,error and warning messages-->
    <section class="content content-sty" id="ajax-msg" style="display:none"></section><!-- Ajax response messages-->

    <!--checking user permission-->
    <?php $user_permission=Auth::user()->user_permission;?>
    <input type="hidden" id="add-premission" value="<?php echo stristr($user_permission,'add');?>" >
    <input type="hidden" id="edit-premission" value="<?php echo stristr($user_permission,'edit');?>" >
    <input type="hidden" id="delete-premission" value="<?php echo stristr($user_permission,'delete');?>" >
    <!--checking edit permission-->
    @if(stristr($user_permission,"view"))
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-4">
                    <h5>{{$language['select category']}}</h5>
                    <!-- Profile Image -->
                    <div class="box box-primary">
                        <div class="box-body box-profile">
                            <div class="form-group">
                                <label for="Category" class="col-sm-4 control-label">{{$language['category']}}: <span style="color:red">*</span></label>
                                <div class="col-sm-8">
                                    <select id="twm-update-select-box" class="form-control">
                                        <option value="">{{$language['choose category']}}</option>
                                            @foreach($category as $cat)
                                                <option value="<?php echo $cat->tagwords_category_id;?>"><?php echo $cat->tagwords_category_name;?></option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>          
                        </div><!-- /.box-body -->
                    </div><!-- /.box --> 
                </div><!-- /.tab-content -->

                <div class="col-md-8">       
                    <h5>{{$language['add category']}}: <span id="filid"></span></h5>
                    <div class="box box-primary" id="refresh-from">
                        <div class="box-body box-profile"><div id="simple"></div>
                            {!! Form::open(array('url'=> array('tagsCatAdd'), 'files'=>true, 'method'=> 'post', 'class'=> 'form-horizontal', 'name'=> 'tagscatAddForm', 'id'=> 'tagscatAddForm','data-parsley-validate'=> '')) !!} 
                            <div class="box-body">
                                <label for="Name:" class="col-sm-2 control-label">{{$language['name']}}: <span style="color:red">*</span></label>
                                <div class="col-sm-4">
                                    {!! Form::text('tagwords_category_name','',array('class'=> 'form-control global_s_msg','title'=>"'".$language['name']."'".$language['length_others'],'data-parsley-maxlength' => $language['max_length'],'id'=>'tagwords_category_name','required'=>'','placeholder'=>'Category Name')) !!}  
                                    <p id="show-val-msg" style="color:red"></p><!--ajax validatin message--> 
                                </div>
                                <div class="col-sm-2 " id="twm-add-update-cat">
                                    <a href="" data-toggle="modal" id="add-cat"><button class="btn btn-block btn-info btn-flat" @if(stristr($user_permission,"add") == '') <?php echo "disabled";?> @endif >Add @if(stristr($user_permission,"add")) <?php echo '<i class="fa fa-plus"></i>';?> @endif</button></a>
                                </div>
                                <div class="col-sm-2" id="twm-delete-cat"></div>
                            </div>
                            {!! Form::close() !!}
                        </div><!-- /.box-body -->
                        <div class='box-body' id="twm-add-tags"></div><!--add more tag ajax response-->
                    </div><!-- /.box -->
                </div><!-- /.tab-content -->
            </div><!-- /.nav-tabs-custom -->
        </section><!-- /.content -->
    @else
        <section class="content content-sty">        
            <div class="alert alert-danger alert-sty"><?php echo $language['no_permission'];?></div>
        </section>
    @endif
<script type="text/javascript">
// select all desired input fields and attach tooltips to them
      $("#tagscatAddForm :input").tooltip({ 
      // place tooltip on the right edge
      position: "center", 
      // a little tweaking of the position
      offset: [-2, 10], 
      // use the built-in fadeIn/fadeOut effect
      effect: "fade", 
      // custom opacity setting
      opacity: 0.7 
      }); 
</script>
@endsection
