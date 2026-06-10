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

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PostNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $post;
    public $user;

    /**
     * PostNotification constructor.
     * @param $post
     * @param $adminUser
     */
    public function __construct($post, $adminUser)
    {
        $this->post = $post;
        $this->user = $adminUser;
        $fromname = 'Tammat'; 
        $from_email = 'admin@tmmat.com'; 
		$this->from($from_email, $fromname);
        //$this->to($adminUser->email, $adminUser->name);
        $this->to('admin@tmmat.com', $adminUser->name);
        if($this->user->is_admin == 1){
            $this->subject(trans('mail.post_notification_title'));
        }else{
            $this->subject(trans('mail.user_post_notification_title'));
        }
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $post = $this->post;
        $user = $this->user;
        if($this->user->is_admin == 1){
            return $this->view('emails.post.notification');
        }else{
            return $this->view('emails.post.user_notification',compact('post','user'));
        }
    }
}
