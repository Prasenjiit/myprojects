<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApplicationLogController;
use App\Http\Requests;
use Auth;
use View;
use URL;
use File;
use Validator;
use Input;
use Session;
use DB;
use App\Mylibs\Common;
// calling model
use App\TagWordsModel as TagWordsModel;
use App\TagWordsCategoryModel as TagWordsCategoryModel;
use App\StacksModel as StacksModel;
use App\DepartmentsModel as DepartmentsModel;
use App\DocumentTypesModel as DocumentTypesModel;

use Lang;

class TagWordsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        Session::put('menuid', '8');
        $this->middleware(['auth', 'user.status']);

        // Set common variable
        $this->actionName1 = 'Tagwords Category';
        $this->actionName2 = 'Tagwords';
        $this->docObj     = new Common(); // class defined in app/mylibs

    }
    
    public function index()
    {
        // checking wether user logged in or not
        if (Auth::user()) {
            // select all cat for select box
            Session::put('menuid', '8');
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
                
                $data['stckApp'] = $this->docObj->common_stack();
                $data['deptApp'] = $this->docObj->common_dept();
                $data['doctypeApp'] = $this->docObj->common_type();
                $data['records'] = $this->docObj->common_records();

            $data['category'] = DB::table('tbl_tagwords_category')->select('tagwords_category_id','tagwords_category_name')->get();
            return View::make('pages/tagwords/index')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    // save category
    public function saveCategory(){
        $cat_name = Input::get('catName');
        $catNameAlreadyExists  = TagWordsCategoryModel::where('tagwords_category_name',Input::get('catName'))->exists();

        if($catNameAlreadyExists):
            print_r('false');
            exit;//ajax response
        else:
            // save data
            $records= new TagWordsCategoryModel;
            $records->tagwords_category_name= $cat_name;
            $records->tagwords_category_created_by= Auth::user()->username;
            $records->tagwords_category_modified_by= Auth::user()->username;
            $records->save();
            // Save in audits
            $user = Auth::user()->username;

            // Get save action message
            $actionMsg = Lang::get('language.save_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName1,$cat_name,$user,$actionMsg);
            
            $result = (new AuditsController)->log(Auth::user()->username, 'Tagwords Category', 'Add',$actionDes);
            // get last inserted id
            echo $records->tagwords_category_id;
            exit;//ajax response 
        endif;
        
    }

    // get all category : ajax response
    public function getAllCatgory(){
        $category = DB::table('tbl_tagwords_category')->select('tagwords_category_id','tagwords_category_name')->get();
       
        echo '<option value="">Please Choose</option>';
        foreach($category as $cat):
            echo '<option value="'.$cat->tagwords_category_id.'">'.$cat->tagwords_category_name.'</option>';
            endforeach;
    }

    // update category using ajax
    public function updateCategory(){
    
        $catId   = Input::get('catId');
        $catName = Input::get('catName');
        // checking name already exists or not
        $hasValue = DB::select("SELECT `tagwords_category_id`,`tagwords_category_name` FROM `tbl_tagwords_category` WHERE(`tagwords_category_name`='$catName' AND `tagwords_category_id`!='$catId')");

        if($hasValue):
            // show error msg
            print_r('false');exit;
        else:
            // update
            $data['tagwords_category_name'] = $catName;
            $data['updated_at']              = date('Y-m-d H:i:s');
            TagWordsCategoryModel::where('tagwords_category_id',$catId)->update($data);
            // Save in audits
            $user = Auth::user()->username;

            // Get update action message
            $actionMsg = Lang::get('language.update_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName1,$catName,$user,$actionMsg);

            $result = (new AuditsController)->log(Auth::user()->username, 'Tagwords Category', 'Update',$actionDes);
            print_r('success');exit;//ajax response
        endif;
    }

    // delete category
    public function deleteCat(){
        $tagIds = DB::table('tbl_tagwords')->select('tagwords_id','tagwords_title')->where('tagwords_category_id',Input::get('catId'))->get();
        //delete tag
        if($tagIds):
            foreach($tagIds as $tag){
                TagWordsModel::find($tag->tagwords_id)->delete();
            }
        endif;
        // delete cat
        TagWordsCategoryModel::where('tagwords_category_id',Input::get('catId'))->delete();
         foreach($tagIds as $tag){
            // Save in audits
            $user = Auth::user()->username;

            // Get delete action message
            $actionMsg = Lang::get('language.delete_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName1,$tag->tagwords_title,$user,$actionMsg);

            $result = (new AuditsController)->log(Auth::user()->username, 'Tagwords Category', 'Delete',$actionDes);
                
            }
        
        echo '<pre>';print_r('true');exit;
    }

    // add tag by ajax
    public function addTag(){
        // save data
        if(Input::get('tagName')){
            $records= new TagWordsModel;
            $records->tagwords_title= Input::get('tagName');
            $records->tagwords_category_id= Input::get('catId');
            $records->tagwords_created_by= Auth::user()->username;
            $records->tagwords_modified_by= Auth::user()->username;
            $records->save();
            // Save in audits
            $name = Input::get('tagName');
            $user = Auth::user()->username;

            // Get save action message
            $actionMsg = Lang::get('language.save_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName2,$name,$user,$actionMsg);

            $result = (new AuditsController)->log(Auth::user()->username, 'Tagwords', 'Add',$actionDes);
            // get last inserted id
            echo $records->tagwords_id;exit;//ajax response 
        }else{
            echo "null";exit;//ajax response
        }

    } 

    public function updateTag(){
        $tagId = Input::get('tagId');
        $name  = Input::get('name');

        if($name):
        // update
            $data['tagwords_title'] = $name;
            $data['updated_at']      = date('Y-m-d H:i:s');
            TagWordsModel::where('tagwords_id',$tagId)->update($data);
            // Save in audits
            $user = Auth::user()->username;
            
            // Get update action message
            $actionMsg = Lang::get('language.update_action_msg');
            $actionDes = $this->docObj->stringReplace($this->actionName2,$name,$user,$actionMsg);

            $result = (new AuditsController)->log(Auth::user()->username, 'Tagwords', 'Update',$actionDes);
            print_r('true');exit;//ajax response
        else:
            print_r('false');exit;//ajax response
        endif;
    }

    // delete tag
    public function deleteTag(){
        $tagid  = Input::get('tagId'); 
        $tagNam = Input::get('tagNam');
        // chacking this cat has any tag exists or not 
        $catCount = TagWordsModel::where('tagwords_category_id',Input::get('catId'))->count();// if true result willbe 1
        // chacking tag esists or not 
        $isTagExists = DB::table('tbl_documents')->whereRaw('FIND_IN_SET('.$tagid.',document_tagwords)')->get();
        if(!$isTagExists){
            $array['catCount']    = $catCount;
            $array['isTagExists'] = 'false';
            echo json_encode($array);exit;// ajax response
        }else{
            TagWordsModel::find(Input::get('tagId'))->delete();

            // Get delete action message for save in audits
            $actionMsg = Lang::get('language.delete_action_msg'); 
            $actionDes = $this->docObj->stringReplace($this->actionName2,$tagNam,Auth::user()->username,$actionMsg);

            $result = (new AuditsController)->log(Auth::user()->username, 'Tagwords Category', 'Delete',$actionDes);

            $array['catCount']     = $catCount;
            $array['isTagExists'] = 'true';
            echo json_encode($array);exit;// ajax response
        }
    }

    // get tags details by category id
    public function getTags(){
        // get permissionS
        $addTagButton = '';
        $addIcon      = '<i class="fa fa-plus"></i>';

        $editTag      = '';
        $editIcon     = '<i class="fa fa-plus"></i>';

        $deleteTag    = ''; 
        $deleteIcon   = '<i class="fa fa-close" id="tag-fa-close"></i>';

        if(Auth::user()->user_role == Session::get('user_role_regular_user') || Auth::user()->user_role == Session::get('user_role_private_user')):
            // add permission
            if(stristr(Auth::user()->user_permission,'add') == ''){
                $addTagButton = 'disabled';
                $addIcon      = ''; 
            }
            // edit permission
            if(stristr(Auth::user()->user_permission,'edit') == ''){
                $editTag = 'disabled'; 
                $editIcon      = '';
            }
            // delete permission
            if(stristr(Auth::user()->user_permission,'delete') == ''){
                $deleteTag = 'disabled'; 
                $deleteIcon = '';
            }
        endif;
        
        $catId = Input::get('catId');
        $tagDatas = DB::table('tbl_tagwords')->where('tagwords_category_id',Input::get('catId'))->select('tagwords_id','tagwords_title')->get();
        if($tagDatas):
            foreach($tagDatas as $tag):
                echo '<div class="box-body" id="delete_formgroup_'.$tag->tagwords_id.'"><label for="Tag :" class="col-sm-2 control-label">Tag : <span style="color:red">*</span></label><div class="col-sm-4"><input class="form-control global_s_msg" id="tags_word_title_'.$tag->tagwords_id.'" required="" placeholder="Tag Name" name="tagwords_title" type="text" value='.$tag->tagwords_title.'> <p id="tag-val-msg_'.$tag->tagwords_id.'" style="color:red"></p></div><div class="col-sm-2" id="twm-add-tag_'.$tag->tagwords_id.'" style="display: none;"><a href="" data-toggle="modal" id="add-tag_'.$tag->tagwords_id.'"catID='.$catId.'><button class="btn btn-block btn-info btn-flat" '.$addTagButton.'>Add Tag  '.$addIcon.'</button></a></div><div class="col-sm-2" id="twm-update-tag_'.$tag->tagwords_id.'" style="display: block;"><a href="" class="update_tag" data-toggle="modal" id="update-tag_'.$tag->tagwords_id.'" tagid='.$tag->tagwords_id.'><button class="btn btn-block btn-info btn-flat" '.$editTag.'>Update  '.$editIcon.'</button></a></div><div class="col-sm-2" id="twm-delete-tag_'.$tag->tagwords_id.'" style="display: block;"><a href="" class="delete_tag" data-toggle="modal" id="delete-tag_'.$tag->tagwords_id.'" tagid='.$tag->tagwords_id.'><button class="btn btn-block btn-info btn-flat" '.$deleteTag.'>Delete  '.$deleteIcon.'</button></a></div></div>';
            endforeach;
                echo '<div class="box-body" id="delete_formgroup"><label for="Tag :" class="col-sm-2 control-label">Tag : <span style="color:red">*</span></label><div class="col-sm-4"><input class="form-control global_s_msg" id="tagwords_title" required="" placeholder="Tag Name" name="tagwords_title" type="text" value=""> <p id="tag-val-msg" style="color:red"></p></div><div class="col-sm-2" id="twm-add-tag"><a href="" data-toggle="modal" id="add-tag" catID='.$catId.'><button class="btn btn-block btn-info btn-flat" '.$addTagButton.'>Add Tag  '.$addIcon.'</button></a></div><div class="col-sm-2" id="twm-update-tag" style="display:none"><a href="" class="update_tag" data-toggle="modal" id="update-tag" tagid=""><button class="btn btn-block btn-info btn-flat" '.$editTag.'>Update  '.$editIcon.'</button></a></div><div class="col-sm-2" id="twm-delete-tag" style="display:none"><a href="" class="delete_tag" data-toggle="modal" id="delete-tag" tagid=""><button class="btn btn-block btn-info btn-flat" '.$deleteTag.'>Delete  '.$deleteIcon.'</button></a></div></div>';
            else:
                return 0;
            endif;
        // ajax response       
    }

}/*<--END-->*/
