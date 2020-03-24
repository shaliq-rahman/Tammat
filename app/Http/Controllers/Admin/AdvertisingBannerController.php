<?php
/**
 * LaraClassified - Geo Classified Ads CMS
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Http\Controllers\Admin;

// use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Auth\Traits\VerificationTrait;
use Illuminate\Support\Facades\Hash;
use Larapen\Admin\app\Http\Controllers\PanelController;
use App\Models\Gender;
use App\Models\User;
use App\Models\Newsletter;
use App\Models\Country;
use App\Models\Category;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\Admin\UserRequest as StoreRequest;
use App\Http\Requests\Admin\UserRequest as UpdateRequest;
use App\Http\Requests;
use Illuminate\Http\Request;


class AdvertisingBannerController extends PanelController
{
    use VerificationTrait;



    public function advertisings_banner()
    {
        $advertisings_banner = \DB::table('banner')->get();
        return view('banner/banner', compact('advertisings_banner'));
    }
    
    public function category_banner()
    {
        $advertisings_banner = \DB::table('category_banner')
                ->join('categories', 'categories.id', '=', 'category_banner.category_id')
                ->select('category_banner.*','categories.name')
                ->get();
                
        return view('banner/category_banner', compact('advertisings_banner'));
    }
    
    public function side_bar_post_banner()
    {
        $advertisings_banner = \DB::table('category_sidebar_banner')
                ->join('categories', 'categories.id', '=', 'category_sidebar_banner.category_id')
                ->select('category_sidebar_banner.*','categories.name')
                ->get();
                
        return view('banner/side_bar_post_banner', compact('advertisings_banner'));
    }
    
    
    public function add_banner()
    {
        $data['countrydata'] = Country::get();
        return view('banner/add_banner',$data);
    }
    
    public function add_category_banner()
    {
        $data['countrydata'] = Country::get();
        $data['categorydata'] = \DB::table('categories')->where('translation_lang', '=', 'en')->where('parent_id', '=', '0')->where('active','=','1')->get();
        return view('banner/add_category_banner',$data);
    }

    public function add_sidebar_banner()
    {
        $data['countrydata'] = Country::get();
        $data['categorydata'] = \DB::table('categories')->where('translation_lang', '=', 'en')->where('parent_id', '=', '0')->where('active','=','1')->get();
        return view('banner/add_sidebar_banner',$data);
    }
    
    public function edit_banner($id)
    {
        $data['banner'] = \DB::table('banner')->where('id', '=', $id)->first();
        $data['countrydata'] = Country::get();
        return view('banner/edit_banner',$data);
    }
    
    public function edit_category_banner($id)
    {
        $data['countrydata'] = Country::get();
        $data['categorydata'] = \DB::table('categories')->where('translation_lang', '=', 'en')->where('parent_id', '=', '0')->where('active','=','1')->get();
        $data['category_banner'] = \DB::table('category_banner')->where('id', '=', $id)->first();
        return view('banner/edit_category_banner',$data);
    }
    
    public function edit_sidebar_category_banner($id)
    {
        $data['countrydata'] = Country::get();
        $data['categorydata'] = \DB::table('categories')->where('translation_lang', '=', 'en')->where('parent_id', '=', '0')->where('active','=','1')->get();
        $data['category_sidebar_banner'] = \DB::table('category_sidebar_banner')->where('id', '=', $id)->first();
        return view('banner/edit_sidebar_category_banner',$data);
    }
    
    
    
    public function update_category_banner(Request $request)
    {
            $dir = 'banner/';
        
            if(!empty($request->file('tracking_code_large')))
            {
                $extension = $request->file('tracking_code_large');
                $extension = $request->file('tracking_code_large')->getClientOriginalExtension(); // getting excel extension
                $tracking_code_large = uniqid().'_'.time().'_'.date('Ymd').'.'.$extension;
                $request->file('tracking_code_large')->move($dir, $tracking_code_large);    
            }
            else
            {
                $tracking_code_large =      $request->tracking_code_large_old;
            }
            
            
            
            if(!empty($request->file('tracking_code_medium')))
            {
                $extension = $request->file('tracking_code_medium');
                $extension = $request->file('tracking_code_medium')->getClientOriginalExtension(); // getting excel extension
                $tracking_code_medium = uniqid().'_'.time().time().date('Ymd').'.'.$extension;
                $request->file('tracking_code_medium')->move($dir, $tracking_code_medium);    
            }
            else
            {
                $tracking_code_medium =      $request->tracking_code_medium_old;
            }
            
            
            if(!empty($request->file('tracking_code_small')))
            {
                $extension = $request->file('tracking_code_small');
                $extension = $request->file('tracking_code_small')->getClientOriginalExtension(); // getting excel extension
                $tracking_code_small = uniqid().'_'.time().date('Ymd').'.'.$extension;
                $request->file('tracking_code_small')->move($dir, $tracking_code_small);    
            }
            else
            {
                $tracking_code_small =      $request->tracking_code_small_old;
            }
            
        
            $checkrecord = \DB::table('category_banner')->where('banner_type', '=', $request->banner_type)->where('country_code', '=', $request->country_code)->where('category_id', '=', $request->category_id)->first();
            if(!empty($checkrecord))
            {
                $query_update =  \DB::table('category_banner')
                  ->where('id', $checkrecord->id)
                  ->update([
                        'banner_type' => $request->banner_type, 
                        'country_code' => $request->country_code, 
                        'category_id' => $request->category_id, 
                        'tracking_code_large' => $tracking_code_large, 
                        'tracking_code_medium' => $tracking_code_medium, 
                        'tracking_code_small' => $tracking_code_small, 
                        'updated_date' => date('Y-m-d H:i:s'),
                      ]);
            }
            else
            {
                $query_update =  \DB::table('category_banner')
                  ->where('id', $request->id)
                  ->update([
                        'banner_type' => $request->banner_type, 
                        'country_code' => $request->country_code, 
                        'category_id' => $request->category_id, 
                        'tracking_code_large' => $tracking_code_large, 
                        'tracking_code_medium' => $tracking_code_medium, 
                        'tracking_code_small' => $tracking_code_small, 
                        'updated_date' => date('Y-m-d H:i:s'),
                      ]);
            }
              
        return redirect('/admin/category_banner')->with('success','Category Banner Updated Successfully!');
        
        
        
    }
    
    
    
    public function post_category_banner(Request $request)
    {
        
         $dir = 'banner/';
        $tracking_code_large = '';
        if(!empty($request->file('tracking_code_large')))
        {
            $extension = $request->file('tracking_code_large');
            $extension = $request->file('tracking_code_large')->getClientOriginalExtension(); // getting excel extension
            $tracking_code_large = uniqid().'_'.time().'_'.date('Ymd').'.'.$extension;
            $request->file('tracking_code_large')->move($dir, $tracking_code_large);    
        }
        $tracking_code_medium = '';
        if(!empty($request->file('tracking_code_medium')))
        {
            $extension = $request->file('tracking_code_medium');
            $extension = $request->file('tracking_code_medium')->getClientOriginalExtension(); // getting excel extension
            $tracking_code_medium = uniqid().'_'.time().time().date('Ymd').'.'.$extension;
            $request->file('tracking_code_medium')->move($dir, $tracking_code_medium);    
        }
        $tracking_code_small = '';
        if(!empty($request->file('tracking_code_small')))
        {
            $extension = $request->file('tracking_code_small');
            $extension = $request->file('tracking_code_small')->getClientOriginalExtension(); // getting excel extension
            $tracking_code_small = uniqid().'_'.time().date('Ymd').'.'.$extension;
            $request->file('tracking_code_small')->move($dir, $tracking_code_small);    
        }
        
        
          $checkrecord = \DB::table('category_banner')->where('banner_type', '=', $request->banner_type)->where('country_code', '=', $request->country_code)->where('category_id', '=', $request->category_id)->first();
          if(!empty($checkrecord))
          {
              $query_update =  \DB::table('category_banner')
                  ->where('id', $checkrecord->id)
                  ->update([
                        'banner_type' => $request->banner_type, 
                        'country_code' => $request->country_code, 
                        'category_id' => $request->category_id, 
                        'tracking_code_large' => $tracking_code_large, 
                        'tracking_code_medium' => $tracking_code_medium, 
                        'tracking_code_small' => $tracking_code_small, 
                        'updated_date' => date('Y-m-d H:i:s'),
                      ]);
          }
          else
          {
                $responce = \DB::table('category_banner')->insert([
                        'banner_type'  => $request->banner_type, 
                        'country_code' => $request->country_code,
                        'category_id'  => $request->category_id, 
                        'tracking_code_large' => $tracking_code_large, 
                        'tracking_code_medium' => $tracking_code_medium, 
                        'tracking_code_small' => $tracking_code_small, 
                        'created_date' => date('Y-m-d H:i:s'),
                        'updated_date' => date('Y-m-d H:i:s'),
                    ]
                );
          }
          
          return redirect('/admin/category_banner')->with('success','Category Banner Added Successfully!');
          
    }
    
    
    public function update_sidebar_category_banner(Request $request)
    {
        
        
        $dir = 'banner/';
        
        if(!empty($request->file('tracking_code_large')))
        {
            $extension = $request->file('tracking_code_large');
            $extension = $request->file('tracking_code_large')->getClientOriginalExtension(); // getting excel extension
            $tracking_code_large = uniqid().'_'.time().'_'.date('Ymd').'.'.$extension;
            $request->file('tracking_code_large')->move($dir, $tracking_code_large);    
        }
        else
        {
            $tracking_code_large =      $request->tracking_code_large_old;
        }
        
        
        $checkrecord = \DB::table('category_sidebar_banner')->where('country_code', '=', $request->country_code)->where('category_id', '=', $request->category_id)->first();
          if(!empty($checkrecord))
          {
              $query_update =  \DB::table('category_sidebar_banner')
                  ->where('id', $checkrecord->id)
                  ->update([
                        'country_code' => $request->country_code, 
                        'category_id' => $request->category_id, 
                        'tracking_code_large' => $tracking_code_large, 
                        'updated_date' => date('Y-m-d H:i:s'),
                      ]);
          }
          else
          {
               $query_update =  \DB::table('category_sidebar_banner')
                  ->where('id', $request->id)
                  ->update([
                        'country_code' => $request->country_code, 
                        'category_id' => $request->category_id, 
                        'tracking_code_large' => $tracking_code_large, 
                         'updated_date' => date('Y-m-d H:i:s'),
                     ]);
          }
          
          return redirect('/admin/side_bar_post_banner')->with('success','Category Sidebar Banner Added Successfully!');
    }
    
    
    public function post_sidebar_category_banner(Request $request)
    {
        $dir = 'banner/';
        $tracking_code_large = '';
        if(!empty($request->file('tracking_code_large')))
        {
            $extension = $request->file('tracking_code_large');
            $extension = $request->file('tracking_code_large')->getClientOriginalExtension(); // getting excel extension
            $tracking_code_large = uniqid().'_'.time().'_'.date('Ymd').'.'.$extension;
            $request->file('tracking_code_large')->move($dir, $tracking_code_large);    
        }
        
        
        $checkrecord = \DB::table('category_sidebar_banner')->where('country_code', '=', $request->country_code)->where('category_id', '=', $request->category_id)->first();
          if(!empty($checkrecord))
          {
              $query_update =  \DB::table('category_sidebar_banner')
                  ->where('id', $checkrecord->id)
                  ->update([
                        'country_code' => $request->country_code, 
                        'category_id' => $request->category_id, 
                        'tracking_code_large' => $tracking_code_large, 
                        'updated_date' => date('Y-m-d H:i:s'),
                      ]);
          }
          else
          {
                $responce = \DB::table('category_sidebar_banner')->insert([
                        'country_code' => $request->country_code,
                        'category_id'  => $request->category_id, 
                        'tracking_code_large' => $tracking_code_large, 
                        'created_date' => date('Y-m-d H:i:s'),
                        'updated_date' => date('Y-m-d H:i:s'),
                    ]
                );
          }
          
          return redirect('/admin/side_bar_post_banner')->with('success','Category Sidebar Banner Added Successfully!');
    }
    
    
    
    public function post_banner(Request $request)
    {
        $dir = 'banner/';
        $tracking_code_large = '';
        if(!empty($request->file('tracking_code_large')))
        {
            $extension = $request->file('tracking_code_large');
            $extension = $request->file('tracking_code_large')->getClientOriginalExtension(); // getting excel extension
            $tracking_code_large = uniqid().'_'.time().'_'.date('Ymd').'.'.$extension;
            $request->file('tracking_code_large')->move($dir, $tracking_code_large);  
            
            
            $responce = \DB::table('banner')->insert(
                [
                    'banner_type' => $request->banner_type, 
                    'country_code' => $request->country_code, 
                    'tracking_code_large' => $tracking_code_large, 
                    'created_date' => date('Y-m-d H:i:s'),
                    'updated_date' => date('Y-m-d H:i:s'),
                ]
            );
            
            
        }
        
        // $tracking_code_medium = '';
        // if(!empty($request->file('tracking_code_medium')))
        // {
        //     $extension = $request->file('tracking_code_medium');
        //     $extension = $request->file('tracking_code_medium')->getClientOriginalExtension(); // getting excel extension
        //     $tracking_code_medium = uniqid().'_'.time().time().date('Ymd').'.'.$extension;
        //     $request->file('tracking_code_medium')->move($dir, $tracking_code_medium);    
        // }
        // $tracking_code_small = '';
        // if(!empty($request->file('tracking_code_small')))
        // {
        //     $extension = $request->file('tracking_code_small');
        //     $extension = $request->file('tracking_code_small')->getClientOriginalExtension(); // getting excel extension
        //     $tracking_code_small = uniqid().'_'.time().date('Ymd').'.'.$extension;
        //     $request->file('tracking_code_small')->move($dir, $tracking_code_small);    
        // }
        
        
        
    //   $checkrecord = \DB::table('banner')->where('banner_type', '=', $request->banner_type)->where('country_code', '=', $request->country_code)->first();
    //   if(!empty($checkrecord))
    //   {
    //       $query_update =  \DB::table('banner')
    //           ->where('id', $checkrecord->id)
    //           ->update([
    //                 'banner_type' => $request->banner_type, 
    //                 'country_code' => $request->country_code, 
    //                 'tracking_code_large' => $tracking_code_large, 
    //                 'tracking_code_medium' => $tracking_code_medium, 
    //                 'tracking_code_small' => $tracking_code_small, 
    //                 'updated_date' => date('Y-m-d H:i:s'),
    //               ]);
    //   }
    //   else
    //   {
        
    //   }
    
    
      return redirect('/admin/banner')->with('success','Banner Added Successfully!');
      
    }
    
    

      
    public function update_banner(Request $request)
    {
       $dir = 'banner/';
        
        if(!empty($request->file('tracking_code_large')))
        {
            $extension = $request->file('tracking_code_large');
            $extension = $request->file('tracking_code_large')->getClientOriginalExtension(); // getting excel extension
            $tracking_code_large = uniqid().'_'.time().'_'.date('Ymd').'.'.$extension;
            $request->file('tracking_code_large')->move($dir, $tracking_code_large);    
        }
        else
        {
            $tracking_code_large =      $request->tracking_code_large_old;
        }
        
        
        
        // if(!empty($request->file('tracking_code_medium')))
        // {
        //     $extension = $request->file('tracking_code_medium');
        //     $extension = $request->file('tracking_code_medium')->getClientOriginalExtension(); // getting excel extension
        //     $tracking_code_medium = uniqid().'_'.time().time().date('Ymd').'.'.$extension;
        //     $request->file('tracking_code_medium')->move($dir, $tracking_code_medium);    
        // }
        // else
        // {
        //     $tracking_code_medium =      $request->tracking_code_medium_old;
        // }
        
        
        // if(!empty($request->file('tracking_code_small')))
        // {
        //     $extension = $request->file('tracking_code_small');
        //     $extension = $request->file('tracking_code_small')->getClientOriginalExtension(); // getting excel extension
        //     $tracking_code_small = uniqid().'_'.time().date('Ymd').'.'.$extension;
        //     $request->file('tracking_code_small')->move($dir, $tracking_code_small);    
        // }
        // else
        // {
        //     $tracking_code_small =      $request->tracking_code_small_old;
        // }
         
        
        
            // $checkrecord = \DB::table('banner')->where('banner_type', '=', $request->banner_type)->where('country_code', '=', $request->country_code)->first();
            // if(!empty($checkrecord))
            // {
            //   $query_update =  \DB::table('banner')
            //       ->where('id', $checkrecord->id)
            //       ->update([
            //             'banner_type' => $request->banner_type, 
            //             'country_code' => $request->country_code, 
            //             'tracking_code_large' => $tracking_code_large, 
            //             'tracking_code_medium' => $tracking_code_medium, 
            //             'tracking_code_small' => $tracking_code_small, 
            //             'updated_date' => date('Y-m-d H:i:s'),
            //           ]);
            // }
            // else
            // {
            
              $query_update =  \DB::table('banner')
                   ->where('id', $request->id)
                   ->update([
                        'banner_type' => $request->banner_type, 
                        'country_code' => $request->country_code, 
                        'tracking_code_large' => $tracking_code_large, 
                        'updated_date' => date('Y-m-d H:i:s'),
                   ]);
                   
            // }
              
        return redirect('/admin/banner')->with('success','Banner Updated Successfully!');
    }
    
    
    public function delete_banner($id)
    {
        \DB::table('banner')->where('id', '=', $id)->delete();
        return redirect('/admin/banner')->with('success','Banner Deleted Successfully!');
    }
    
    public function delete_category_banner($id)
    {
        \DB::table('category_banner')->where('id', '=', $id)->delete();
        return redirect('/admin/category_banner')->with('success','Category Banner Deleted Successfully!');
    }
    
    
    public function delete_sidebar_category_banner($id)
    {
        \DB::table('category_sidebar_banner')->where('id', '=', $id)->delete();
        return redirect('/admin/side_bar_post_banner')->with('success','Category Sidebar Banner Deleted Successfully!');
    }
    
    


    
}
