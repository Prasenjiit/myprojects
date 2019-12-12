<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApplicationLogController;
use App\Http\Requests;
use Auth;
use View;
use Validator;
use Input;
use Session;

use DB;
use App\ActivityModel as ActivityModel;
use App\StacksModel as StacksModel;
use App\DepartmentsModel as DepartmentsModel;
use App\DocumentTypesModel as DocumentTypesModel;

use App\Mylibs\Common;
use Lang;
use Intervention\Image\ImageManagerStatic as Image;
use Imagick;


class TestController extends Controller
{
    public function test(){

        $PdfFile       = public_path('images/test/big.tif');// File from server
        $fileToBeSaved = public_path('images/test/new.pdf');// File to be saved
        $path = public_path('images/new/');
        
        //header('Content-type: image/jpeg');
        $image = new Imagick();

        $image->setResourceLimit(imagick::RESOURCETYPE_MEMORY, 256);
        $image->setResourceLimit(imagick::RESOURCETYPE_MAP, 256);
        $image->setResourceLimit(imagick::RESOURCETYPE_AREA, 1512);
        $image->setResourceLimit(imagick::RESOURCETYPE_FILE, 768);
        $image->setResourceLimit(imagick::RESOURCETYPE_DISK, -1);

        $image->newPseudoImage(200, 200, 'xc:white');
        $image->setType(\Imagick::IMGTYPE_TRUECOLOR);
        $imagick->setImageDepth(16);

        $image->readImage($PdfFile);
        
        $image->setImageFormat("pdf");
        
        // $image->setImageCompression(imagick::COMPRESSION_JPEG); 
        // $image->setImageCompressionQuality(100);

        // Test
        //$image->setImageColorSpace(Imagick::COLORSPACE_GRAY);  
        // Test

        $image->writeImages($fileToBeSaved, true);
        //exec("$path $fileToBeSaved $PdfFile");    
        $image->clear();
        $image->destroy();
    
       
         print_r("Success");exit;

        return View::make('pages/test');
    }

                  // Save or Update
    public function testSave(){ 

        //header('Content-type: image/jpeg');
        // $image = new Imagick($PdfFile);
        // $image->setImageFormat("pdf");
        // $image->writeImages($fileToBeSaved, true);
        // //exec("$path $fileToBeSaved $PdfFile");    
        

        //Upload image
        if(Input::hasfile('file')){
            $file = $this->uploadImage();
            echo $file;exit;
        }
    }

    // Upload image laravel (common function)
    public function uploadImage(){
        $image = Input::file('file');
        $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
        $destinationPath    = public_path('images/new'); 
        $image->move($destinationPath, $input['imagename']);
        return $input['imagename'];
    }



    public function test1(){

                /*<--Rezise image-->*/
                $filename = 'cc.jpg';
                $filename = public_path('images/test/'.$filename);// Image from server public/images/test/name.jpg

                // Check file already has
                $tes = "dfsd.jpg";
                if(file_exists($tes) == NULL){
                    // Create image
                    // Get new sizes
                    list($width, $height) = getimagesize($filename);
                    // If width more tha 850 then resize it
                    if(($width > 850) || ($height > 1000) ):

                        $info = getimagesize($filename);
                        $mime = $info['mime'];

                         switch ($mime) {
                                case 'image/jpeg':
                                        $image_create_func = 'imagecreatefromjpeg';
                                        $image_save_func = 'imagejpeg';
                                        $new_image_ext = 'jpg';
                                        break;

                                case 'image/png':
                                        $image_create_func = 'imagecreatefrompng';
                                        $image_save_func = 'imagepng';
                                        $new_image_ext = 'png';
                                        break;

                                case 'image/gif':
                                        $image_create_func = 'imagecreatefromgif';
                                        $image_save_func = 'imagegif';
                                        $new_image_ext = 'gif';
                                        break;

                                default: 
                                        $image_create_func = NULL;
                        }


                       if($image_create_func):
                            $newwidth = 850;
                            $newheight = 850;
                            // Load
                            $thumb = imagecreatetruecolor($newwidth, $newheight);
                            $source = $image_create_func($filename);
                            // Resize
                            imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                            // Output
                            $saveto     =   $_SERVER['DOCUMENT_ROOT']."/images/test/bb.jpg"; // Same path
                            imagejpeg($thumb,$saveto);
                        endif;

                    endif;
                }
                /*<--//Rezise image-->*/

                return View::make('pages/test');    
        }


    public function test_backup(){

               //  $filename   = 'hm3.jpg';
               //  //$filename  = time() . '.' . $filename->getClientOriginalExtension();
               //  $actualFile = public_path('images/test/'.$filename);// Image from server public/images/test/name.jpg
               //  $saveto     =   $_SERVER['DOCUMENT_ROOT']."/images/test/test1.jpg"; // Same path
               //  Image::make($actualFile)->resize(800,800)->save($saveto);
               // // $user->image = $filename;
               //  //$user->save();

                // File and new size
                $saveto     =   $_SERVER['DOCUMENT_ROOT']."/images/test/bb.jpg"; // Same path
                $filename = 'hm1.jpg';
                $filename = public_path('images/test/'.$filename);// Image from server public/images/test/name.jpg
                $percent  = 0.5;

                // Content type
                header('Content-Type: image/jpeg');

                // Get new sizes
                list($width, $height) = getimagesize($filename);
                // $newwidth = $width * $percent;
                // $newheight = $height * $percent;
                $newwidth = 800;
                $newheight = 800;

                // Load
                $thumb = imagecreatetruecolor($newwidth, $newheight);
                $source = imagecreatefromjpeg($filename);

                // Resize
                imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

                // Output
                $img = imagejpeg($thumb,$saveto);
                print_r($img);exit;
                return View::make('pages/test');    
        }

    public function getTestData(){

        $noOfRecords = DB::table('tbl_audits')->count();
        // storing  request (ie, get/post) global array to a variable  
        $requestData= $_REQUEST;
        $query = DB::table('tbl_audits');
                 $query->select('audit_id','audit_owner','audit_action_desc','audit_action_type');
                 // Search
                 if( !empty($requestData['search']['value']) ) {
                    $query->where("audit_action_type","LIKE",''.$requestData['search']['value']."%");
                    $query->orWhere("audit_owner","LIKE",''.$requestData['search']['value']."%");
                    $query->orWhere("audit_action_desc","LIKE",''.$requestData['search']['value']."%");
                    $query->orWhere("audit_id","LIKE",''.$requestData['search']['value']."%");
                    $noOfRecords = $query->count();
                 }

                $query->offset($requestData['start'])->limit($requestData['length']);   
         
        $data['data'] = $query->get();
        // For ajax result
        $data['draw'] = intval( $requestData['draw'] );
        $data['recordsTotal'] = $noOfRecords;
        $data['recordsFiltered'] = $noOfRecords;

       foreach( $data['data'] as $val):
        $x = (array) $val;
        $y[] = array_values($x);  
        endforeach;
        $data['data'] = $y;
        echo json_encode($data);  // send data as json format

    }


     public function tesdfsdft(){
        //
        $filename   = 'test.jpg';
        $actualFile = public_path('images/test/'.$filename);// Image from server public/images/test/name.jpg
        //$degrees  = 90+90+90+90+90+90;
        $degrees  = 90;
        $saveto     =   $_SERVER['DOCUMENT_ROOT']."/images/test/test1.jpg"; // Same path
        $x = $this->RotateImg($actualFile,$degrees,$saveto);
        echo "<pre>";print_r($x);exit;
        return view::make('pages/test');
    }

    function RotateImg($filename = '',$angle = 0,$savename = false)
    {
       $original   =   imagecreatefromjpeg($filename);
       $rotated    =   imagerotate($original, $angle, 0);
       
       if($savename == false) {
                header('Content-Type: image/jpeg');
                imagejpeg($rotated);

            }
        else {
           imagejpeg($rotated,$savename);
        }

        imagedestroy($rotated);
        echo "Success";
    }


}
