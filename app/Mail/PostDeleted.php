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
use App\Models\User;

class PostDeleted extends Mailable
{
    use Queueable, SerializesModels;

    public $post;
    public $flag;
    /**
     * Create a new message instance.
     *
     * @param $post
     */
    public function __construct($post,$flag = 1)
    {
        $this->post = $post;
        $this->flag = $flag;
        $fromname = 'Tammat';
        $from_email = 'admin@tmmat.com'; 
		$this->from($from_email, $fromname);
        $this->to($post->email, $post->contact_name);
        $this->subject(trans('mail.post_deleted_title', ['title' => $post->title]));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $flag = $this->flag;
        $post = $this->post;
        $user = User::where('email',$post->email)->first();
        return $this->view('emails.post.deleted',compact('flag','user','post'));
    }
}
