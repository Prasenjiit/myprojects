<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use View;
use Validator;
use Input;
use Session;
use App\DocumentTypeColumnModel as DocumentTypeColumnModel;
use App\DocumentTypesModel as DocumentTypesModel;

class DocumentTypeColumnController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'user.status']);
    }
    
    public function index()
    {
        if (Auth::user()) {
            $data['items'] = DocumentTypeColumnModel::all();
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
            return View::make('pages/document_types_column/index')->with($data);

        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function documentTypeColumnList()
    {
        if (Auth::user()) {
           $data['dtcollist']= DocumentTypeColumnModel::all();
            return View::make('pages/document_types_column/list')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function save(Request $request, $id)
    {
        if (Auth::user()) {
            if ($id) {
                $name= Input::get('colname_edi');
                $documentTypeColumn   =  DocumentTypeColumnModel:: find($id);
                $documentTypeColumn->document_type_id= Input::get('doctypeid');
                $documentTypeColumn->document_type_column_name= $name;
                $documentTypeColumn->document_type_column_type= Input::get('coltype');
                $documentTypeColumn->document_type_column_modified_by= Auth::user()->username;

                if ($documentTypeColumn->save()) {
                    $result = (new AuditController)->log(Auth::user()->username, 'Document column name', 'Edit', 'Document column name:'.$name);
                    if($result > 0) {                    
                        Session::flash('flash_message_edit', "Document column name '". $name ."' edited successfully.");
                        Session::flash('alert-class', 'alert alert-success alert-sty');
                        return redirect('documentTypeColumn');
                    } else {
                        Session::flash('flash_message_edit', "Some issues in log file,contact admin.");
                        Session::flash('alert-class', 'alert alert-danger alert-sty');
                        return redirect('documentTypeColumn');
                    }
                    
                } else {
                    Session::flash('flash_message_edit', 'You cannot edit document type data.');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('documentTypeColumn');
                }
                
            } else {

                    $validators= Validator:: make(
                    $request-> all(),
                    [
                        'colname'=> 'required',
                        'coltype'=> 'required'
                    ]
                );
                if ($validators->passes()) {

                    $name= Input::get('colname');
                    //Duplicate entry checking
                    $duplicateEntry= DocumentTypeColumnModel::where('document_types_column_name', '=', $name)->get();
                                
                    if(count($duplicateEntry) > 0)
                    {
                        echo '<div class="alert alert-danger alert-sty">'. $name.' is already in our database. </div>';
                        exit();
                    } else {
                        $documentTypeColumn= new DocumentTypeColumnModel;
                        $documentTypeColumn->document_types_id= Input::get('doctypeid');
                        $documentTypeColumn->document_types_column_name= $name;
                        $documentTypeColumn->document_types_column_type= Input::get('coltype');
                        $documentTypeColumn->document_types_column_created_by= Auth::user()->username;
                        if ($documentTypeColumn->save()) {     
                            $result = (new AuditController)->log(Auth::user()->username, 'Document Type Column', 'Add', 'Document Type Column:'.$name);
                            if($result > 0) {
                                echo "<div class='alert alert-success alert-sty'>Document type column '". Input::get('colname') ."' added successfully.</div>";
                                exit();
                            } else {
                                echo "Some issues in log file,contact admin";
                                exit;
                            }
                            
                        } else {
                            echo '<div class="alert alert-danger alert-sty">Sorry you cant add document type column . </div>';
                            exit();
                        }
                    }
                    
                } else {
                    echo '<div class="alert alert-danger alert-sty">Please fill the document group correctely.</div>';
                    exit();
                }
            }
            
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    public function edit(Request $request, $id)
    {
        if (Auth::user()) {
            $data['docType'] = DocumentTypesModel::orderBy('document_type_order', 'ASC')->get();
            $data['docTypeCol']= DocumentTypeColumnModel:: find($id);
            return View::make('pages/document_types_column/edit')->with($data);
        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }

    public function delete()
    {
        if (Auth::user()) {
            $id= Input::get('id');
            $documentTypeClmn= DocumentTypeColumnModel:: find($id);
            if ($documentTypeClmn->delete())
            {                
                $result = (new AuditController)->log(Auth::user()->username, 'Document Type Column', 'Delete', 'Document Type Column:'.$documentTypeClmn->document_type_column_name);
                if($result > 0) {
                    echo json_encode("Document type column '". $documentTypeClmn->document_type_column_name ."' deleted successfully.");
                    exit();
                } else {
                    echo json_encode("Some issues in log file,contact admin");
                    exit;
                }
                
            }

        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
    
    public function duplication()
    {
        if (Auth::user()) {
            $name= Input::get('name');
            $editId= Input::get('editId');
            $oldVal= Input::get('oldVal');
            if($editId > 0)
            {
                $duplicateEntry= DocumentTypeColumnModel::where('document_types_column_name', '=', $name )
                                                    ->where('document_types_column_name', '!=', $oldVal)->get();
            }
            else{
                $duplicateEntry= DocumentTypeColumnModel::where('document_types_column_name', '=', $name )->get();
            }            

            if(count($duplicateEntry) > 0 )
            {                
                echo json_encode('<div class="parsley-errors-list filled" id="dp-inner">'. $name.' is already in our database. </div>');
                exit();
            } else {
                echo json_encode('1');
                exit;
            }           

        } else {
            return redirect('')->withErrors("Please login")->withInput();
        }
    }
}
