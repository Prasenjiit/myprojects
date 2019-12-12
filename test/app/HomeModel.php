<?php

namespace App;
use Auth;
use Illuminate\Database\Eloquent\Model;

class HomeModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = '';

    /**
    *Primary key
    */
    protected $primaryKey = '';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    /* Define Default widget Postions */    
    public static function dashboard_widget_default_postion()
     {
            $center_div1 = array('recentdoc','notaccesseddoc');
            $center_div2 = array('docdepartment','doctype','docextension');
            $center_div3 = array('docusers','docmedia','diskspace');
            $widget = array('center_div1' => $center_div1,'center_div2' => $center_div2,'center_div3' => $center_div3);
            return $widget;
     }
     /* Convert a multi-dimensional array into a single-dimensional array 
     recursive function*/  
    public static function array_flatten($array = null) 
    {
        $result = array();

        if (!is_array($array)) { $array = func_get_args(); } 

            foreach ($array as $key => $value) 
            { 
                if (is_array($value)) 
                {
                    $result = array_merge($result, array_flatten($value));
                } else {
                    $result = array_merge($result, array($key => $value));
                }
            }

            return $result;
    }
    
    /* checing if  new widgets are added after sorting*/  

    public static function prepare_widget_postion() 
    {
       $user_widget = (isset(Auth::user()->dashboard_widgets) && Auth::user()->dashboard_widgets)?unserialize(Auth::user()->dashboard_widgets):array();
       $defult_widget         =  HomeModel::dashboard_widget_default_postion();
       if(count($user_widget) == 0)
       {
            $widget_postion = $defult_widget;
       }
       else
       {
            $defult_widget_flatten =  HomeModel::array_flatten($defult_widget);
            $user_widget_flatten   =  HomeModel::array_flatten($user_widget);
            $new_widget=array_diff($defult_widget_flatten,$user_widget_flatten);
            if(count($new_widget) == 0)
            {
                $widget_postion = $user_widget;
            }
            else
            {
                foreach ($defult_widget as $key => $value) 
                { 
                    if (is_array($value)) 
                    {
                        foreach ($value as $key2 => $value2) 
                        { 
                            if (in_array($value2, $new_widget))
                            {
                                    $user_widget[$key][]=$value2;
                            }

                        }
                    }
                }
                $widget_postion = $user_widget;

            }
       }

       
       return $widget_postion;
    }

   
}
