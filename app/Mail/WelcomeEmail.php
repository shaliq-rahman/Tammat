<?php
/**
 * LaraClassified - Geo Classified Ads Software
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
use Illuminate\Mail\Mailables\Envelope;

class WelcomeEmail extends Mailable
{
	use Queueable, SerializesModels;
	
	public $entity;
	public $entityRef;
	
	/**
	 * WelcomeEmail constructor.
	 *
	 * @param $entity
	 * @param $entityRef
	 */
	public function __construct($to_email, $to_name)
	{

		$fromname = 'Tammat';
        $from_email = 'admin@tmmat.com'; 
		$this->from($from_email, $fromname);		 
		$this->entityRef = '1';	
		$this->to_email = $to_email;	
		$this->to_name = $to_name;		
		$this->to($to_email, $to_name);
		$this->subject(trans('mail.user_activated_content_1', ['appName' => 'Tammat', 'userName' => $to_name]));
	 
	}
	
 

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->view('emails.welcome');
	}
}
