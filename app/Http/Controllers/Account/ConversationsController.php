<?php
/**
 * JobClass - Job Board Web Application
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
use Illuminate\Http\Request;
use App\Helpers\UrlGen;
use App\Http\Requests\ReplyMessageRequest;
use App\Models\User;
use App\Models\Message;
use App\Notifications\ReplySent;
use Torann\LaravelMetaTags\Facades\MetaTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConversationsController extends AccountBaseController
{
	private $perPage = 10;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->perPage = (is_numeric(config('settings.listing.items_per_page'))) ? config('settings.listing.items_per_page') : $this->perPage;
	}
	
	/**
	 * Conversations List
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		$data = [];

		$user_id = Auth::id();

		$user_info = DB::table('users')->select()->where('users.id',$user_id)->first();

		$data['user_role'] = $user_info->user_type_id;
		// Set the Page Path
		view()->share('pagePath', 'conversations');
		
		// Get the Conversations
		$data['conversations'] = $this->conversations->paginate($this->perPage);
	/*	$conversations=DB::table('messages')
->select('messages.id','messages.to_user_id','messages.created_at','messages.subject','messages.message','messages.bid_amount','messages.rate_packages_flag','messages.rate_packages_id','messages.rate_packages_type','social.*','projectaward.jobprojectaward_id','projectaward.employer_id','projectaward.influencer_id','projectaward.project_status','projectaward.post_id as post_id_award','projectaward.conversation_id as conversation_id_award')
->leftJoin('social', 'social.user_id', '=', 'messages.from_user_id')
->leftJoin('projectaward','projectaward.conversation_id', '=', 'messages.id')
->where('parent_id', 0)
->orderByDesc('messages.id')
->get(); 


		
			$data['conversations'] =$conversations;*/
/*	echo "<pre>";
		print_r($conversations);
	 die("welll");*/

		$data['award_projects'] = DB::table('projectaward')
		->select('projectaward.*')
		->where('projectaward.employer_id',$user_id)->get();

		// Meta Tags
		MetaTag::set('title', t('Conversations Received'));
		MetaTag::set('description', t('Conversations Received on :app_name', ['app_name' => config('settings.app.app_name')]));
		
		return view('account.conversations', $data);
	}
	
	/**
	 * Conversation Messages List
	 *
	 * @param $conversationId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function messages($conversationId)
	{
		$data = [];


		
		// Set the Page Path
		view()->share('pagePath', 'conversations');
		
		// Get the Conversation
		$conversation = Message::where('id', $conversationId)
		->byUserId(auth()->user()->id)
		->firstOrFail();
		view()->share('conversation', $conversation);
		
		// Get the Conversation's Messages
		$data['messages']      = Message::where('parent_id', $conversation->id)
		->byUserId(auth()->user()->id)
		->orderByDesc('id');
		$data['countMessages'] = $data['messages']->count();
		$data['messages']      = $data['messages']->paginate($this->perPage);
		
		// Mark the Conversation as Read
		if ($conversation->is_read != 1) {
			if ($data['countMessages'] > 0) {
				// Check if the latest Message is from the current logged user
				if ($data['messages']->has(0)) {
					$latestMessage = $data['messages']->get(0);
					if ($latestMessage->from_user_id != auth()->user()->id) {
						$conversation->is_read = 1;
						$conversation->save();
					}
				}
			} else {
				if ($conversation->from_user_id != auth()->user()->id) {
					$conversation->is_read = 1;
					$conversation->save();
				}
			}
		}
		
		// Meta Tags
		MetaTag::set('title', t('Messages Received'));
		MetaTag::set('description', t('Messages Received on :app_name', ['app_name' => config('settings.app.app_name')]));
		
		return view('account.messages', $data);
	}
	
	/**
	 * @param $conversationId
	 * @param ReplyMessageRequest $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function reply($conversationId, ReplyMessageRequest $request)
	{

		// SPAM MESSAGE CHECKS //

		if(strstr($request->input('message'),'gmail.com') || strstr($request->input('message'),'yahoo.com') || strstr($request->input('message'),'yahoo.in'))
		{

			flash(t("You cannot share personal information!"))->error();
			return back();

		}

		// SPAM MESSAGE CHECKS //

		// Get Conversation
		$conversation = Message::findOrFail($conversationId);
		
		// Get Recipient Data
		if ($conversation->from_user_id != auth()->user()->id) {
			$toUserId = $conversation->from_user_id;
			$toName   = $conversation->from_name;
			$toEmail  = $conversation->from_email;
			$toPhone  = $conversation->from_phone;
		} else {
			$toUserId = $conversation->to_user_id;
			$toName   = $conversation->to_name;
			$toEmail  = $conversation->to_email;
			$toPhone  = $conversation->to_phone;
		}
		
		// Don't reply to deleted (or non exiting) users
		if (config('settings.single.guests_can_post_ads') != 1 && config('settings.single.guests_can_contact_ads_authors') != 1) {
			if (User::where('id', $toUserId)->count() <= 0) {
				flash(t("This user no longer exists.") . ' ' . t("Maybe the user's account has been disabled or deleted."))->error();
				return back();
			}
		}
		
		// New Message
		$message = new Message();
		$input   = $request->only($message->getFillable());
		foreach ($input as $key => $value) {
			$message->{$key} = $value;
		}
		
		$message->post_id      = $conversation->post->id;
		$message->parent_id    = $conversation->id;
		$message->from_user_id = auth()->user()->id;
		$message->from_name    = auth()->user()->name;
		$message->from_email   = auth()->user()->email;
		$message->from_phone   = auth()->user()->phone;
		$message->to_user_id   = $toUserId;
		$message->to_name      = $toName;
		$message->to_email     = $toEmail;
		$message->to_phone     = $toPhone;
		$message->subject      = 'RE: ' . $conversation->subject;
		
		$message->message = $request->input('message')
		. '<br><br>'
		. t('Related to the ad')
		. ': <a href="' . UrlGen::post($conversation->post) . '">' . t('Click here to see') . '</a>';
		
		// Save
		$message->save();
		
		// Save and Send user's resume
		if ($request->hasFile('filename')) {
			
			if ($request->hasFile('filename')) {
				$image = $request->file('filename');
				$doc_name = rand().'-upoad-doc-chat'.'.'.$image->getClientOriginalExtension();
				$destinationPath = public_path('/chat_documents/');
				$image->move($destinationPath, $doc_name);
				$message->filename = $doc_name;
			}
			$message->save();
		}
		
		// Mark the Conversation as Unread
		if ($conversation->is_read != 0) {
			$conversation->is_read = 0;
			$conversation->save();
		}
		
		// Send Reply Email

		// UPDATE STATUS OF THE AWARDED PROJECT IF POST PARAMETERS ARE THERE//

		if($request->input('employer_id') && $request->input('influencer_id') && $request->input('post_id'))
		{

			$post_id = decrypt($request->input('post_id'));
			$employer_id = decrypt($request->input('employer_id'));
			$influencer_id = decrypt($request->input('influencer_id'));

			DB::update('update jobprojectaward set project_status = "waiting" where influencer_id = ? and employer_id = ? and post_id = ? ', [$influencer_id,$employer_id,$post_id]);


		}
		// UPDATE STATUS OF THE AWARDED PROJECT IF POST PARAMETERS ARE THERE//  

		try {
			$message->notify(new ReplySent($message));
			flash(t("Your reply has been sent. Thank you!"))->success();
		} catch (\Exception $e) {
			flash($e->getMessage())->error();
		}
		
		return back();
	}
	
	/**
	 * Delete Conversation
	 *
	 * @param null $conversationId
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function destroy($conversationId = null)
	{
		// Get Entries ID
		$ids = [];
		if (request()->filled('entries')) {
			$ids = request()->input('entries');
		} else {
			if (!is_numeric($conversationId) && $conversationId <= 0) {
				$ids = [];
			} else {
				$ids[] = $conversationId;
			}
		}
		
		// Delete
		$nb = 0;
		foreach ($ids as $item) {
			// Get the conversation
			$message = Message::where('id', $item)
			->byUserId(auth()->user()->id)
			->first();
			
			if (!empty($message)) {
				if (empty($message->deleted_by)) {
					// Delete the Entry for current user
					$message->deleted_by = auth()->user()->id;
					$message->save();
					$nb = 1;
				} else {
					// If the 2nd user delete the Entry,
					// Delete the Entry (definitely)
					if ($message->deleted_by != auth()->user()->id) {
						$nb = $message->delete();
					}
				}
			}
		}
		
		// Confirmation
		if ($nb == 0) {
			flash(t("No deletion is done. Please try again."))->error();
		} else {
			$count = count($ids);
			if ($count > 1) {
				flash(t("x :entities has been deleted successfully.", ['entities' => t('messages'), 'count' => $count]))->success();
			} else {
				flash(t("1 :entity has been deleted successfully.", ['entity' => t('message')]))->success();
			}
		}
		
		return back();
	}
	
	/**
	 * Delete Message
	 *
	 * @param $conversationId
	 * @param null $messageId
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function destroyMessages($conversationId, $messageId = null)
	{
		// Get Entries ID
		$ids = [];
		if (request()->filled('entries')) {
			$ids = request()->input('entries');
		} else {
			if (!is_numeric($messageId) && $messageId <= 0) {
				$ids = [];
			} else {
				$ids[] = $messageId;
			}
		}
		
		// Delete
		$nb = 0;
		foreach ($ids as $item) {
			// Don't delete the main conversation
			if ($item == $conversationId) {
				continue;
			}
			
			// Get the message
			$message = Message::where('parent_id', $conversationId)->where('id', $item)
			->byUserId(auth()->user()->id)
			->first();
			
			if (!empty($message)) {
				if (empty($message->deleted_by)) {
					// Delete the Entry for current user
					$message->deleted_by = auth()->user()->id;
					$message->save();
					$nb = 1;
				} else {
					// If the 2nd user delete the Entry,
					// Delete the Entry (definitely)
					if ($message->deleted_by != auth()->user()->id) {
						$nb = $message->delete();
					}
				}
			}
		}
		
		// Confirmation
		if ($nb == 0) {
			flash(t("No deletion is done. Please try again."))->error();
		} else {
			$count = count($ids);
			if ($count > 1) {
				flash(t("x :entities has been deleted successfully.", ['entities' => t('messages'), 'count' => $count]))->success();
			} else {
				flash(t("1 :entity has been deleted successfully.", ['entity' => t('message')]))->success();
			}
		}
		
		return back();
	}

	// AWARD PROJECT 

	public function award_project(Request $request)
	{

		$influencer_id = decrypt($request->input('influencer_id'));
		$employer_id = Auth::id();
		$employer_name = decrypt($request->input('employer_name'));
		$conversation_id = decrypt($request->input('conversation_id'));
		// influencer details//
		$influencer_info = DB::table('users')
		->select('users.*')
		->where('users.id',$influencer_id)
		->first();
		// influencer details//
		$bid_amount = decrypt($request->input('bid_amount'));

		$post_id = decrypt($request->input('post_id'));

		$created_date = date('Y-m-d H:i:s');

        // post details 
		$post_details = DB::table('posts')
		->where('posts.id',$post_id)
		->select('posts.*')
		->first();
		// post details 
		
		$employer_id = Auth::id();
		$employer_info = DB::table('users')
		->select('users.*')
		->where('users.id',$employer_id)
		->first();
		if($employer_info->package_id!=''){
		$user_package_id = $employer_info->package_id;	
		}else{
			$user_package_id ='1';	
		}
		
     
    

		// check employer wallet 
		$employer_wallet = DB::table('wallet')
		->where('wallet.user_id',$employer_id)
		->select('wallet.wallet_amount')
		->first();
		// check employer wallet 
$commision= \App\Helpers\UrlGen::get_employer_packageinfo($user_package_id);
		/*$commision = $bid_amount * COMMISION_EMPLOYER/100;*/

		$total_required_balance = $commision + $bid_amount;

		if($employer_wallet->wallet_amount < $total_required_balance)
		{
			echo json_encode('low_wallet_balance');
			return false;

		}else{
			 // Notification//
		$notification_text = $post_details->title. ' Project awarded to you .&nbsp;<a href="/account/awardedprojects">Project Awards</a>';
		DB::table('notifications')->insert(
			['notification_to_user_id' => $influencer_id,'notification_text' => $notification_text,'notification_is_read' => '0', 'created_date' => $created_date] 
		);
    //Notification//

            // Deduct total amount with commision from the employer account

			$employer_wallet = DB::update('update jobwallet set wallet_amount = wallet_amount - '.$total_required_balance.'  where user_id = ?', [$employer_id]);

			$employer_wallet_block_amount = DB::update('update jobwallet set blocked_amount = blocked_amount + '.$total_required_balance.'  where user_id = ?', [$employer_id]);

			if($employer_wallet && $employer_wallet_block_amount)
			{

		       // Transaction details for the employer deduction
				$transaction_remarks = 'Amount '.$total_required_balance.' blocked for the Project Name -  '.$post_details->title.'. (Influencer Details - '.$influencer_info->name.' )';

				$transaction_history_response = DB::table('transactionhistory')->insert(['package_payment_id' => '','user_id' =>  $employer_id,'amount' => $total_required_balance , 'transaction_type' => 'debit','remarks' => $transaction_remarks ,'package_id' => '', 'created_date' => $created_date] 
			);




           // Credit commision amount to the SUPER ADMIN WALLET

				$commission_super_admin = DB::update('update jobwallet set wallet_amount = wallet_amount + '.$commision.'  where user_id = ?', [1]);

				if($commission_super_admin){

		          // Transaction details for the SUPER ADMIN commision

					$transaction_remarks = 'Commision Amount '.$commision.' credited. Project name - '.$post_details->title. ' (Post ID - '.$post_id.')';

					$transaction_history_response = DB::table('transactionhistory')->insert(['package_payment_id' => '','user_id' =>  1,'amount' => $total_required_balance , 'transaction_type' => 'credit','remarks' => $transaction_remarks ,'package_id' => '', 'created_date' => $created_date]);

					// Project Award details for the inluencer
					//jobprojectaward

					$jobprojectaward_res = DB::table('projectaward')->insert(['employer_id' => $employer_id,'influencer_id' =>  $influencer_id,'bid_amount' => $bid_amount , 'post_id' => $post_id,'is_read' => 0,'project_status' => 'pending','created_date' => $created_date,'conversation_id' =>$conversation_id] 
				);

					// Project Award details for the inluencer

					echo json_encode('success_message');

				}else{

					echo json_encode('error_message');
				}

			}else{

				echo json_encode('error_message');
			}

		}

	}

	public function milestoneget(Request $request){
	$conversation_id =decrypt($request->input('conversation_id'));
	$employer_id =$request->input('employer_id');	
	$influencer_id =$request->input('influencer_id');	
	$post_id =$request->input('post_id');		
	$conversation_id1 =$request->input('conversation_id');
	$html="";
	
	if(!empty($conversation_id)){
		$html.='<input type="hidden" name="employer_id" value="'.$employer_id.'" id="employer_id">
	<input type="hidden" name="influencer_id" value="'.$influencer_id.'" id="influencer_id">
	<input type="hidden" name="post_id" value="'.$post_id.'" id="post_id">
	<input type="hidden" name="conversation_id" value="'.$conversation_id1.'" id="conversation_id">
	';
	$milestones =DB::table('milestones')
		->where('milestones.jobmilestones_message_id',$conversation_id)
		->where('milestones.milestone_status','no')
		->select('*')
		->get();
      if(count($milestones)==1){
      	$checked='checked';
      }else{
		$checked='';
      }
		foreach($milestones as $singlemilestone){

			$html.='<div class="form-group required">
						<label for="milestone" class="control-label">
						<input type="checkbox" name="milestone_id[]" value="'.$singlemilestone->jobmilestones_id.'" '.$checked.'>
						</label>
                       <div class="row miletsone_number" id="1">
							<div class="col-sm-5">
								<input type="text" class="form-control" name="milestone_title[]" value="'.$singlemilestone->jobmilestones_title.'" placeholder="Title">
							</div>
							<div class="col-sm-5 input-group">
								
								<input type="number" class="form-control milestone_amount" name="milestone_amount[]" value="'.$singlemilestone->jobmilestones_amount.'">
							</div>
						</div>
							
							
						</div>
					</div>';
		}
		print_r($html);
	}
	else{
		echo '<p>No Data</p>';
	}
	exit();
	}
}
