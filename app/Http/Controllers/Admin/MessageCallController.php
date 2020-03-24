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

use App\Models\Package;
use App\Models\PaymentMethod;
use Larapen\Admin\app\Http\Controllers\PanelController;
use Larapen\Admin\app\Http\Requests\Request as StoreRequest;
use Larapen\Admin\app\Http\Requests\Request as UpdateRequest;
use App\Models\Country;
use App\Http\Requests;
use Illuminate\Http\Request;

class MessageCallController extends PanelController
{
	public function messagecall()
	{
	    $data['messagecall'] = \DB::table('delivery')
                ->join('messages', 'messages.id', '=', 'delivery.message_id')
                ->join('posts', 'posts.id', '=', 'messages.post_id')
                ->join('users as fromuser', 'fromuser.id', '=', 'messages.from_user_id')
                ->join('users as touser', 'touser.id', '=', 'messages.to_user_id')
                ->select('posts.title','delivery.*','fromuser.username as fromusername', 'touser.username as tousername','messages.post_id')
                ->get();
        return view('messagecall', $data);
	}
    
    public function messagecallDel($id)
	{
        \DB::table('delivery')->where('id', '=', $id)->delete();
        
        return back()->with('success','Record Successfully Deleted!');
	}
	
	public function deliveryemail()
	{
	    $data['deliveryemail'] = \DB::table('delivery_email')
                ->select('*')
                ->get();
        return view('delivery.deliveremail', $data);
	}
	
	
	public function add_delivery_email()
	{
	    
       $data['countrydata'] = Country::get();
        return view('delivery/add_delivery_email',$data);
	}
	
	public function post_delivery_email(Request $request)
	{
	    
	    $deliveryemail = \DB::table('delivery_email')
                ->where('country_code','=',$request->country_code)
                ->select('*')
                ->count();
                
        if($deliveryemail  == '0')
        {
            $responce = \DB::table('delivery_email')->insert(
                [
                    'country_code' => $request->country_code, 
                    'email' => $request->email, 
                    'created_date' => date('Y-m-d H:i:s'),
                ]
            );
        }
        else
        {
            $query_update =  \DB::table('delivery_email')
                  ->where('country_code', $request->country_code)
                  ->update([
                        'email' => $request->email, 
                  ]);
        }
	    
        return redirect('/admin/deliveryemail')->with('success','Delivery email successfully add!');
       
	}
	
	public function post_delivery_email_edit(Request $request)
	{
        $query_update =  \DB::table('delivery_email')
                  ->where('id', $request->id)
                  ->update([
                        'email' => $request->email, 
                  ]);
                      
        return redirect('/admin/deliveryemail')->with('success','Delivery email updated successfully!');
           
	}
	
	
	
	public function delete_delivery_email($id)
	{
         \DB::table('delivery_email')->where('id', '=', $id)->delete();
        return redirect()->back()->with('success','Delivery Email Deleted Successfully!');
	}
	
    public function edit_delivery_email($id)
    {
        $data['delivery'] = \DB::table('delivery_email')->where('id', '=', $id)->first();
        $data['countrydata'] = Country::get();
        return view('delivery/edit_delivery_email',$data);
    }
	
	
	
	
	
	
	
	
}
