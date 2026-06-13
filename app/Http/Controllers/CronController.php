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

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\Arr;
use App\Http\Requests\ContactRequest;
use App\Models\City;
use App\Models\Page;
use App\Mail\FormSent;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Torann\LaravelMetaTags\Facades\MetaTag;

class CronController extends FrontController
{
   
    public function ExpirePost ()
    {
        $getpost  = Post::where('archived', '=', '0')->get();
        foreach($getpost as $row)
        {
             $rowuser = User::find($row->user_id);
             
             
             $checkpayment_count = \DB::table('payments')
                ->select('payments.*', 'packages.duration')
                ->join('packages', 'packages.id','=','payments.package_id')
                ->where('payments.post_id', '=', $row->id)
                ->count();
                
            $checkpayment = \DB::table('payments')
                ->select('payments.*', 'packages.duration')
                ->join('packages', 'packages.id','=','payments.package_id')
                ->where('payments.post_id', '=', $row->id)
                ->first();
                
            //$toemail = $rowuser->email;
            $toemail = $rowuser->email;
            $data['toname']  = $rowuser->username;
            $data['title'] = $row->title;
        
            $from_email = 'admin@tmmat.com';
            $fromname = 'Tammat';
        
            if($checkpayment_count == 0)    
            {
                $rowduration = \DB::table('packages')
                ->select('packages.*')
                ->where('id', '=', 1)
                ->first();
                $duration = $rowduration->duration;
                
                $date = $row->updated_at;
                $sevendate = date('Y-m-d', strtotime($date. ' + '.$duration.' days'));
                if($sevendate < date('Y-m-d'))
                {
                     $update  = Post::find($row->id);
                     $update->archived = 1;
                     $update->save();
                     
                     $temp = \App::getLocale();
                    \App::setLocale($rowuser->language_code);
                   
                  
             
                    \Mail::send('emails.post.expire_post', $data, function($message) use ($toemail,$fromname,$from_email)
                    {
                        $message->to($toemail);
                        $message->subject(trans('mail.expire_post'));
                        $message->replyTo($from_email, $fromname);
                    });  
                    \App::setLocale($temp);



                   
                    
                }
            }
            else
            {
                $duration = $checkpayment->duration;
                $date = $row->updated_at;
                $sevendate = date('Y-m-d', strtotime($date. ' + '.$duration.' days'));
                if($sevendate < date('Y-m-d'))
                {
                    $update  = Post::find($row->id);
                    $update->archived = 1;
                    $update->save();
                    
                     $temp = \App::getLocale();
                    \App::setLocale($rowuser->language_code);
                    \Mail::send('emails.post.expire_post', $data, function($message) use ($toemail,$fromname,$from_email){
                        $message->to($toemail);
                        $message->subject(trans('mail.expire_post'));
                        $message->replyTo($from_email, $fromname);
                    });   
                     \App::setLocale($temp);
                    
                }   

            }
        }
        
    }
	

    
    
    
    
}
