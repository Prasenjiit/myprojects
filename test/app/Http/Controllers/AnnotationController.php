<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Annotation;
use App\Comment;
use Input;
use Auth;
use DB;
use View;

class AnnotationController extends Controller
{
    public function index($id)
    {
        if (Auth::user()) 
        {   
            $data['docid'] = Input::get('dcno');
            $file = Input::get('file');
            $pdf = ['id' => $id, 'url'=>$file ];
            $data['pdf'] = ['id' => $id, 'url'=>$file ];
            if(Input::get('dcno')){
                $data['bookmarklist'] = DB::table('tbl_document_bookmarks')->select('document_id','document_bookmark_id','document_bookmark')->where('document_id',Input::get('dcno'))->get();
            }
            //print($data['bookmarklist']);
            return View::make('annotation.index')->with($data);
            //return view('annotation.index', compact('pdf','data'));
        } 
        else 
        {
            return redirect('')->withErrors("Please login")->withInput();
        }
        /*// Todo- get pdf detail by id
        $file = Input::get('file');
        //$pdf = ['id' => $id, 'url'=>url('script/test.pdf') ];
        $pdf = ['id' => $id, 'url'=>$file ];
        return view('annotation.index', compact('pdf'));*/
    }

    public function search($id, Request $request)
    {
        $q = $request->get('q');

        $result = Annotation::select('annotations.id', 'annotations.page', 'comments.text')
        ->join('comments', 'comments.annotation_id', '=', 'annotations.id')
        ->whereRaw('comments.text LIKE "%'.trim($q).'%"')
        ->get();

        $ids = [];
        $data =[];
        foreach ($result as $row) {
            if (!in_array($row->id, $ids)) {
                $ids[]=$row->id;
                $data[]=$row;
            }
        }

        return response()->json(['total' => count($data), 'rows' => $data]);
    }

    /**
      * @param Request $request
      * @return string
      */
    public function listall(Request $request)
    {
        $page =  $request->get('page');
        $pdf_id =  $request->get('pdf_id');

        $data = Annotation::select('id', 'page', 'pdf_id', 'properties', 'ranges', 'shapes', 'quote')->with('comments')->where('page', $page)->where('pdf_id', $pdf_id)->get();
        $annotations=[];
        foreach ($data as $annotation) {
            foreach ($annotation->comments as &$comment) {
                $comment->created_date = $comment->created_at->timestamp . '000';
                $comment->created_by = ['id'=>$comment->user->id, 'name'=>$comment->user->username];
                unset($comment->created_at);
                unset($comment->updated_at);
                unset($comment->annotation_id);
                unset($comment->user);
                unset($comment->user_id);
            }

            if (empty($annotation->ranges)) {
                unset($annotation->quote);
                unset($annotation->ranges);
            }

            $annotations[] = $annotation;
        }
        return response()->json(['total' => count($annotations), 'rows' => $annotations]);
    }
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $input = [
            'created_by'   => \Auth::id(),
            'ranges'  => isset($data['ranges']) ? $data['ranges'] : null,
            'shapes'  => isset($data['shapes']) ? $data['shapes'] : null,
            'quote'  => isset($data['quote']) ? $data['quote'] : '',
            'pdf_id' => $data['pdf_id'],
            'page' => $data['page'],
            'properties' =>$data['properties']
        ];
        try {
            $annotation = Annotation::create($input);
            $comments = [];
            foreach ($data['comments'] as $d) {
                $comment= Comment::create([
                    'annotation_id' =>$annotation->id,
                    'user_id' => \Auth::id(),
                    'text' => $d['text']
                ]);
                $comment->created_date = $comment->created_at->timestamp . '000';
                $comment->created_by = ['id'=>$comment->user->id, 'name'=>$comment->user->username];
                unset($comment->created_at);
                unset($comment->updated_at);
                unset($comment->annotation_id);
                unset($comment->user);
                unset($comment->user_id);
                $comments[]=$comment;
            }
            return response()->json(['id' => $annotation->id, 'comments'=>$comments]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update($id, Request $request)
    {
        $annotation = Annotation::find($id);
        if ($annotation) {
            $data = json_decode($request->getContent(), true);
            if (isset($data['ranges'])) {
                $annotation->ranges = $data['ranges'];
                $annotation->quote = $data['quote'];
            }

            if (isset($data['shapes'])) {
                $annotation->shapes = $data['shapes'];
            }

            $annotation->page = $data['page'];
            $annotation->pdf_id = $data['pdf_id'];
            $annotation->properties = $data['properties'];

            $annotation->save();
            $comments = [];

            foreach ($annotation->comments as $comment) {
                $found =false;
                foreach ($data['comments'] as $d) {
                    if (isset($d['id']) && $d['id'] == $comment->id) {
                        $found = true;
                    }
                }

                if (!$found) {
                    Comment::destroy($comment->id);
                }
            }

            foreach ($data['comments'] as $d) {
                if (isset($d['id'])) {
                    $comment= Comment::where('id', $d['id'])->update([ 'text' => $d['text']]);
                    $comment = Comment::find($d['id']);
                } else {
                    $comment= Comment::create([
                        'annotation_id' =>$annotation->id,
                        'user_id' => \Auth::id(),
                        'text' => $d['text']
                        ]);
                }
                $comment->created_date = $comment->created_at->timestamp . '000';
                $comment->created_by = ['id'=>$comment->user->id, 'name'=>$comment->user->username];
                unset($comment->created_at);
                unset($comment->updated_at);
                unset($comment->annotation_id);
                unset($comment->user);
                unset($comment->user_id);
                $comments[]=$comment;
            }
            try {
                return response()->json(['comments'=>$comments]);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
            }
        }
        return response()->json(['status' => 'error', 'message' => 'Could not find the annotation.'], 400);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete($id)
    {
        try {
            if (Annotation::destroy($id)) {
                Comment::where('annotation_id', $id)->delete();
                return response()->json(['status'=>'OK']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
        return response()->json(['status' => 'error', 'message' => 'Could not find the annotation.'], 400);
    }

    public function deleteAll($id)
    {
        Annotation::where('pdf_id', $id)->delete();
        return response()->json(['status' => 'OK']);
    }
}