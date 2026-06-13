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
use App\Models\Post;
use App\Models\User;

class PostArchived extends Mailable
{
    use Queueable, SerializesModels;
    
    public $post;

    /**
     * Create a new message instance.
     *
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
		$fromname = 'Tammat';
        $from_email = 'admin@tmmat.com'; 
		$this->from($from_email, $fromname);
        $this->to($post->email, $post->contact_name);
        $this->subject(trans('mail.post_archived_title', ['title' => $post->title]));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $post = $this->post;
        $user = User::where('email',$post->email)->first();
        dd($post);
        return $this->view('emails.post.archived',compact('post','user'));
    }
}
