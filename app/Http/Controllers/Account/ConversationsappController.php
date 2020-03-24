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

namespace App\Http\Controllers\Account;

use App\Http\Requests\ReplyMessageRequest;
use App\Models\User;
use App\Models\Message;
use App\Notifications\ReplySent;
use Torann\LaravelMetaTags\Facades\MetaTag;
use Illuminate\Http\Request;
use DB;

class ConversationsappController extends AccountappBaseController
{
	private $perPage = 10;
	
	public function __construct()
	{
		parent::__construct();
		
		
	}
	
	/**
	 * Conversations List
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index(Request $request)
	{
		$conversations1 = Message::with('latestReply')
			->with('reply')
// 			->whereHas('post', function($query) {
// 				$query->currentCountry();
// 			})
			->byUserId($request->userid)
			->where('parent_id', 0)
			->orderByDesc('id');
		$count = $conversations1->count();
		$conversations = $conversations1->get();
		
		//return $conversations;
		
		
		if(!empty($conversations))
		{
		    
		foreach ($conversations as $key => $conversation) {
		$getpostpicture = \DB::table('pictures')->where('post_id', '=', $conversation->post_id)->where('position', '=', 1)->where('active', '=', 1)->first();
		if(!empty($getpostpicture)) {
		    $conversation->filenamee = url('storage/'.$getpostpicture->filename);
		}
		 
		 $date = date('d F Y H:i',strtotime($conversation->created_at));
		 $conversation->created_at_app = $date;
		}
		}
		
			
		return response()->json(['results'=>$conversations,'num'=>$count]);
	}
	
	/**
	 * Conversation Messages List
	 *
	 * @param $conversationId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function messages($conversationId, Request $request)
	{
		$data = [];
		
		// Get the Conversation
		$conversation = Message::where('id', $conversationId)
			->byUserId($request->userid)
			->firstOrFail();
		$data['conversation'] = $conversation;
		
		// Get the Conversation's Messages
		$data['messages'] = Message::where('parent_id', $conversation->id)
			->byUserId($request->userid)
			->orderByDesc('id')->get();
		$data['countMessages'] = $data['messages']->count();
		
		
		// Mark the Conversation as Read
		if ($conversation->is_read != 1) {
			if ($data['countMessages'] > 0) {
				// Check if the latest Message is from the current logged user
				if ($data['messages']->has(0)) {
					$latestMessage = $data['messages']->get(0);
					//print_r($latestMessage);
					if ($latestMessage->from_user_id != $request->userid) {
						$conversation->is_read = 1;
						$conversation->save();
					}
				}
			} else {
				if ($conversation->from_user_id != $request->userid) {
					$conversation->is_read = 1;
					$conversation->save();
				}
			}
		}
		
		
		return response()->json(['results'=>$data]);
	}
	
	/**
	 * @param $conversationId
	 * @param ReplyMessageRequest $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function reply($conversationId, ReplyMessageRequest $request)
	{
		// Get Conversation
		$conversationId = $request->id;
		$msgdata = DB::table('messages')
			->select('*')
			->where('id', $conversationId)
			->first();
		if(!empty($msgdata)){
		$conversation = Message::findOrFail($conversationId);
		$user = User::findOrFail($request->userid);
		//print_r($user);
		// Get Recipient Data
		if ($conversation->from_user_id != $request->userid) {
			$toUserId = $conversation->from_user_id;
			$toName = $conversation->from_name;
			$toEmail = $conversation->from_email;
			$toPhone = $conversation->from_phone;
		} else {
			$toUserId = $conversation->to_user_id;
			$toName = $conversation->to_name;
			$toEmail = $conversation->to_email;
			$toPhone = $conversation->to_phone;
		}
		
		// Don't reply to deleted (or non exiting) users
		if (config('settings.single.guests_can_post_ads') != 1 && config('settings.single.guests_can_contact_seller') != 1) {
			if (User::where('id', $toUserId)->count() <= 0) {
				
				return response()->json(['results'=>'This user no longer exists. Maybe the user account has been disabled or deleted.']);
				//return back();
			}
		}
		
		// New Message
		$message = new Message();
		$input = $request->only($message->getFillable());
		//print_r($input);
		foreach ($input as $key => $value) {
			$message->{$key} = $value;
		}
		
		$message->post_id = $conversation->post->id;
		$message->parent_id = $conversation->id;
		$message->from_user_id = $request->userid;
		$message->from_name = $user->username;
		$message->from_email = $user->email;
		$message->from_phone = $user->phone;
		$message->to_user_id = $toUserId;
		$message->to_name = $toName;
		$message->to_email = $toEmail;
		$message->to_phone = $toPhone;
		$message->subject = 'RE: ' . $conversation->subject;
		
		$attr = ['slug' => slugify($conversation->post->title), 'id' => $conversation->post->id];
		$message->message = $request->input('message')
			. '<br><br>'
			. t('Related to the ad')
			. ': <a href="' . lurl($conversation->post->uri, $attr) . '">' . t('Click here to see') . '</a>';
		
		// Save
		$message->save();
		
		// Save and Send user's resume
		if ($request->hasFile('filename')) {
			$message->filename = $request->file('filename');
			$message->save();
		}
		
		// Mark the Conversation as Unread
		if ($conversation->is_read != 0) {
			$conversation->is_read = 0;
			$conversation->save();
		}
		
		// Send Reply Email
		try {
			$conversation->notify(new ReplySent($message));
			return response()->json(['results'=>'Your reply has been sent. Thank you!']);
		} catch (\Exception $e) {
			return response()->json(['results'=>$user->username]);
		}
	    }
	    else{
	      return response()->json(['results'=>'Invalid Id']);  
	    }
	}
	
	/**
	 * Delete Conversation OR Message
	 *
	 * @param null $conversationId
	 * @param null $messageId
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function destroy($conversationId = null, $messageId = null, Request $request)
	{
	
		// Get the type of Entity ID
		$id = $conversationId;
		if (!empty($messageId)) {
			$id = $messageId;
		}
		
		// Get Entries ID
		$ids = [];
		if (request()->filled('entries')) {
			$ids = request()->input('entries');
		} else {
			if (!is_numeric($id) && $id <= 0) {
				$ids = [];
			} else {
				$ids[] = $id;
			}
		}
		
		// Delete
		$nb = 0;
		foreach ($ids as $item) {
			
			$message = Message::where('id', $item)
				->byUserId($request->userid)
				->first();
			
			if (!empty($message)) {
				if (empty($message->deleted_by)) {
					// Delete the Entry for current user
					$message->deleted_by = $request->userid;
					$message->save();
					$nb = 1;
				} else {
					// If the 2nd user delete the Entry,
					// Delete the Entry (definitely)
					if ($message->deleted_by != $request->userid) {
						$nb = $message->delete();
					}
				}
			}
		}
		
		// Confirmation
		if ($nb == 0) {
			return response()->json(['results'=>"No deletion is done. Please try again."]);
		} else {
			$count = count($ids);
			return response()->json(['results'=>"entity has been deleted successfully."]);
		} 
	
	}
}
