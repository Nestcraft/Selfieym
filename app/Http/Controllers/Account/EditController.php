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

use App\Helpers\Localization\Country as CountryLocalization;
use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Http\Controllers\Auth\Traits\VerificationTrait;
use App\Http\Requests\Admin\Request;
use App\Http\Requests\UserRequest;
use App\Models\Gender;
use App\Models\Post;
use App\Models\SavedPost;
use App\Models\Scopes\VerifiedScope;
use App\Models\User;
use App\Models\UserType;
use Creativeorange\Gravatar\Facades\Gravatar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Torann\LaravelMetaTags\Facades\MetaTag;
use Redirect;
class EditController extends AccountBaseController
{
    use VerificationTrait;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $data = [];

        $data['countries'] = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());
        $data['genders']   = Gender::trans()->get();
        $data['userTypes'] = UserType::all();
        $data['userPhoto'] = (!empty(auth()->user()->email)) ? Gravatar::fallback(url('images/user.jpg'))->get(auth()->user()->email) : null;

        // Mini Stats
        $data['countPostsVisits'] = DB::table('posts')
        ->select('user_id', DB::raw('SUM(visits) as total_visits'))
        ->where('country_code', config('country.code'))
        ->where('user_id', auth()->user()->id)
        ->groupBy('user_id')
        ->first();
        $data['countPosts'] = Post::currentCountry()
        ->where('user_id', auth()->user()->id)
        ->count();
        $data['countFavoritePosts'] = SavedPost::whereHas('post', function ($query) {
            $query->currentCountry();
        })->where('user_id', auth()->user()->id)
        ->count();

        /// USER INFORMATION
        $user_details = DB::table('social')
        ->where('social.user_id', auth()->user()->id)
        ->select('social.profile_image')
        ->first();

        $user_info = DB::table('users')
        ->where('users.id', auth()->user()->id)
        ->select('users.user_type_id', 'users.profile_image_employer', 'users.id')
        ->first();

        // Wallet DATA//

        $data['user_wallet_balance'] = DB::table('wallet')
        ->select('wallet_amount')
        ->where('user_id', $user_info->id)->first();

        // Wallet DATA//

        $data['user_type_id'] = '';
        if (!empty($user_info)) {
            $data['user_type_id'] = $user_info->user_type_id;
        }

        $data['profile_image'] = "";

        if ($user_info->user_type_id == 2) {

            if (!empty($user_details->profile_image)) {
                $data['profile_image'] = $user_details->profile_image;
            }

        } else {

            if (!empty($user_info->profile_image_employer)) {
                $data['profile_image'] = $user_info->profile_image_employer;
            }

        }

        /// USER INFORMATION

        // Meta Tags
        MetaTag::set('title', t('My account'));
        MetaTag::set('description', t('My account on :app_name', ['app_name' => config('settings.app.app_name')]));

        return view('account.edit', $data);
    }

    /**
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateDetails(UserRequest $request)
    {

        $name = '';
        /******* UPLOAD PROFILE IMAGE CODE (WEBC) *****/
        if ($request->hasFile('profile_image')) {
            $image           = $request->file('profile_image');
            $name            = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/images/profile_images/');
            $image->move($destinationPath, $name);
        }

        $db_profile_image = DB::table('users')
        ->where('users.id', auth()->user()->id)
        ->select('users.profile_image_employer')
        ->first();

        if ($name == '') {
            $profile_image_user = $db_profile_image->profile_image_employer;
        } else {
            $profile_image_user = $name;
        }

        // Check if these fields has changed
        $emailChanged    = $request->filled('email') && $request->input('email') != auth()->user()->email;
        $phoneChanged    = $request->filled('phone') && $request->input('phone') != auth()->user()->phone;
        $usernameChanged = $request->filled('username') && $request->input('username') != auth()->user()->username;

        // Conditions to Verify User's Email or Phone
        $emailVerificationRequired = config('settings.mail.email_verification') == 1 && $emailChanged;
        $phoneVerificationRequired = config('settings.sms.phone_verification') == 1 && $phoneChanged;

        // Get User
        $user = User::withoutGlobalScopes([VerifiedScope::class])->find(auth()->user()->id);

        // Update User
        $input = $request->only($user->getFillable());
        foreach ($input as $key => $value) {
            if (in_array($key, ['email', 'phone', 'username']) && empty($value)) {
                continue;
            }
            $user->{$key} = $value;
        }

        $user->phone_hidden = $request->input('phone_hidden');

        $user->profile_image_employer = $profile_image_user;
      
        // Email verification key generation
        if ($emailVerificationRequired) {
            $user->email_token    = md5(microtime() . mt_rand());
            $user->verified_email = 0;
        }

        // Phone verification key generation
        if ($phoneVerificationRequired) {
            $user->phone_token    = mt_rand(100000, 999999);
            $user->verified_phone = 0;
        }

        // Don't logout the User (See User model)
        if ($emailVerificationRequired || $phoneVerificationRequired) {
            session(['emailOrPhoneChanged' => true]);
        }

        // Save
        $user->save();

        // Message Notification & Redirection
        flash(t("Your details account has updated successfully."))->success();
        $nextUrl = config('app.locale') . '/account';

        // Send Email Verification message
        if ($emailVerificationRequired) {
            $this->sendVerificationEmail($user);
            $this->showReSendVerificationEmailLink($user, 'user');
        }

        // Send Phone Verification message
        if ($phoneVerificationRequired) {
            // Save the Next URL before verification
            session(['itemNextUrl' => $nextUrl]);

            $this->sendVerificationSms($user);
            $this->showReSendVerificationSmsLink($user, 'user');

            // Go to Phone Number verification
            $nextUrl = config('app.locale') . '/verify/user/phone/';
        }

        // Redirection
        return redirect($nextUrl);
    }

    /**
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateSettings(UserRequest $request)
    {
        // Get User
        $user = User::find(auth()->user()->id);

        // Update
        $user->disable_comments = (int) $request->input('disable_comments');
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();

        flash(t("Your settings account has updated successfully."))->success();

        return redirect(config('app.locale') . '/account');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updatePreferences()
    {
        $data = [];

        return view('account.edit', $data);
    }

    public function wallet()
    {

        $data['user_id']             = Auth::id();
        $data['package_information'] = [];

        $user_wallet_amount = DB::table('wallet')->select('*')->where('wallet.user_id', $data['user_id'])->first();
        if(!empty($user_wallet_amount)){
            $data['wallet_amount'] = $user_wallet_amount->wallet_amount + $user_wallet_amount->blocked_amount;
        }else{
            $data['wallet_amount'] = '0';
        }

        // LATEST USER PACKAGE
        $user_info = DB::table('users')->select('*')->where('users.id', $data['user_id'])->first();


        $data['total_bids'] = DB::table('bids')->select('*')->where('bids.user_id', $data['user_id'])->first();


        
        $user_type_id = $user_info->user_type_id;

        if ($user_type_id == 2) {

            //Influencer packages info

            $package_id_query = DB::table('packagepayments')->select('packagepayments.package_id')->where('packagepayments.user_id', $data['user_id'])->where('packagepayments.payment_status', 'success')->orderBy('packagepayments.package_payment_id', 'DESC')->first();

            if (!empty($user_info->package_id)) {

                $package_id = $user_info->package_id;
                $package_info_query = DB::table('packages_influencer')->select('*')->where('packages_influencer.id',$user_info->package_id)->first();

                $data['package_information'] = $package_info_query;

            }

        } else {

            // Employer Packages Info

            $package_id_query = DB::table('packagepayments')->select('packagepayments.package_id')->where('packagepayments.user_id', $data['user_id'])->orderBy('packagepayments.package_payment_id', 'DESC')->first();

            if (!empty($user_info->package_id))
            {

                $package_id = $user_info->package_id;

                $package_info_query = DB::table('packages')->select('*')->where('packages.id', $package_id)->first();

                $data['package_information'] = $package_info_query;

            }

        }

        $data['user_transaction_history'] = DB::table('transactionhistory')->select()->where('user_id', $data['user_id'])->get();

        return view('account.wallet', $data);

    }

    public function transaction()
    {
        $data                             = [];
        $user_id                          = Auth::id();
        $data['user_transaction_history'] = DB::table('transactionhistory')->select()
        ->LeftJoin('users', 'users.id', '=', 'transactionhistory.influencer_id')
        ->where('user_id', $user_id)->get();

        return view('account.transaction', $data);
    }

    public function wallet_trans(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0|not_in:0',
        ]);

        if ($validator->fails()) {
            return redirect('account/wallet')
            ->withErrors($validator)
            ->withInput();
        }

        $amount = $request->input('amount');

        $base_url = url('/');
        // PAY U MONEY CREDENTIALS

       // $PAYU_MONEY_MERCHANT_KEY = "gtKFFx";
       //  $PAYU_MONEY_SALT         = "eCwWELxi";
        //live details

        $PAYU_MONEY_MERCHANT_KEY = PAY_U_MONEY_MERCHANT_KEY;
        $PAYU_MONEY_SALT         = PAY_U_MONEY_MERCHANT_SALT;

        $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);

        $surl = $base_url . '/paymentsuccesswallet';
        $furl = $base_url . '/paymentfailurewallet';

        // PAY U MONEY CREDENTIALS

        $logged_in_userid = Auth::id();

        $user_name      = '';
        $user_email     = '';
        $user_phone     = '';
        $product_amount = '';
        $productinfo    = '';

        if (Auth::id()) {
            $user_info = DB::table('users')
            ->select('users.*')
            ->where('users.id', $logged_in_userid)
            ->get();

            $user_name  = $user_info[0]->name;
            $user_email = $user_info[0]->email;
            $user_phone = $user_info[0]->phone;

        }

        $udf1 = $logged_in_userid;
        $udf2 = '';
        $udf3 = '';
        $udf4 = '';
        $udf5 = '';

        $productinfo = 'add_user_wallet';

        $hashstring = $PAYU_MONEY_MERCHANT_KEY . '|' . $txnid . '|' . $amount . '|' . $productinfo . '|' . $user_name . '|' . $user_email . '|' . $udf1 . '|' . $udf2 . '|' . $udf3 . '|' . $udf4 . '|' . $udf5 . '||||||' . $PAYU_MONEY_SALT;

        $hash = strtolower(hash('sha512', $hashstring));

        return view('home.inc.pay_u_money_standby', ['PAYU_MONYEY_MERCHANT_KEY' => $PAYU_MONEY_MERCHANT_KEY, 'txnid' => $txnid, 'product_amount' => $amount, 'productinfo' => $productinfo, 'user_name' => $user_name, 'user_email' => $user_email, 'udf1' => $udf1, 'udf2' => $udf2, 'udf3' => $udf3, 'udf4' => $udf4, 'udf5' => $udf5, 'PAYU_MONEY_SALT' => $PAYU_MONEY_SALT, 'hash' => $hash, 'user_phone' => $user_phone, 'surl' => $surl, 'furl' => $furl]);

    }

    // AWARDED PROJECTS
    public function awardedprojects(Request $request)
    {

        $influencer_id = Auth::id();

        DB::update('update jobprojectaward set is_read = 1 where influencer_id = ?', [$influencer_id]);

        // $data['awarded_projects'] = DB::table('projectaward')
        // ->select('posts.*', 'users.*', 'rating_review.*', 'projectaward.*')
        // ->Join('posts', 'posts.id', '=', 'projectaward.post_id')
        // ->Join('users', 'users.id', '=', 'projectaward.employer_id')
        // ->leftJoin('rating_review', 'rating_review.jobprojectaward_id_review', '=', 'projectaward.jobprojectaward_id')
        // ->leftJoin('rating_review', 'rating_review.from_user_id_review', '=', $influencer_id)
        // ->where('projectaward.influencer_id', $influencer_id)
        // //->orWhere('rating_review.from_user_id_review', $influencer_id)
        // ->get();

        $data['awarded_projects'] = DB::select('select `jobposts`.*, `jobusers`.*, `jobrating_review`.*, `jobprojectaward`.*
            from `jobprojectaward`
            inner join `jobposts` on `jobposts`.`id` = `jobprojectaward`.`post_id`
            inner join `jobusers` on `jobusers`.`id` = `jobprojectaward`.`employer_id`
            left join `jobrating_review` on `jobrating_review`.`jobprojectaward_id_review` = `jobprojectaward`.`jobprojectaward_id` && `jobrating_review`.`from_user_id_review` = ' . $influencer_id . '
            where `jobprojectaward`.`influencer_id` = ' . $influencer_id . ' and project_type="approve_bid"');

         /*echo "<pre>";
         print_r($data['awarded_projects']);

     die;
*/
     return view('account.awarded_projects', $data);

 }
 public function recievedprojects(Request $request)
 {

    $user_id = Auth::id();

    $loggedin_user_type = DB::table('users')->select('user_type_id')->where('users.id', $user_id)->first();

    DB::update('update jobprojectaward set is_read = 1 where influencer_id = ?', [$user_id]);

        // $data['awarded_projects'] = DB::table('projectaward')
        // ->select('posts.*', 'users.*', 'rating_review.*', 'projectaward.*')
        // ->Join('posts', 'posts.id', '=', 'projectaward.post_id')
        // ->Join('users', 'users.id', '=', 'projectaward.employer_id')
        // ->leftJoin('rating_review', 'rating_review.jobprojectaward_id_review', '=', 'projectaward.jobprojectaward_id')
        // ->leftJoin('rating_review', 'rating_review.from_user_id_review', '=', $influencer_id)
        // ->where('projectaward.influencer_id', $influencer_id)
        // //->orWhere('rating_review.from_user_id_review', $influencer_id)
        // ->get();


    if($loggedin_user_type->user_type_id == 1)
    {

        $data['recieved_projects'] = DB::select('select `jobmyrate`.*, `jobusers`.*,`jobprojectaward`.*
            from `jobprojectaward`
            inner join `jobmyrate` on `jobmyrate`.`id` = `jobprojectaward`.`package_id`
            inner join `jobusers` on `jobusers`.`id` = `jobprojectaward`.`employer_id`
            where `jobprojectaward`.`employer_id` = ' . $user_id . ' and project_type="purchased_package"');

    }else{

        $data['recieved_projects'] = DB::select('select `jobmyrate`.*, `jobusers`.*,`jobprojectaward`.*
            from `jobprojectaward`
            inner join `jobmyrate` on `jobmyrate`.`id` = `jobprojectaward`.`package_id`
            inner join `jobusers` on `jobusers`.`id` = `jobprojectaward`.`employer_id`
            where `jobprojectaward`.`influencer_id` = ' . $user_id . ' and project_type="purchased_package" and project_status!="pending"');

    }

        /* echo "<pre>";
          print_r($data['recieved_projects']);
          die('test');
        */

          $data['user_type_id'] =  $loggedin_user_type->user_type_id;

          return view('account.recieved_projects', $data);

      }

      public function complete_project(Request $request)
      {

        $post_id = decrypt($request->input('post_id'));
        $employer_id = decrypt($request->input('employer_id'));
        $influencer_id = Auth::id();

        $status_response = DB::update('update jobprojectaward set project_status = "completed" where influencer_id = ? and employer_id = ? and post_id = ? ', [$influencer_id,$employer_id,$post_id]);

        if($status_response)
        {
            echo json_encode(1);
        }else
        {
           echo json_encode(2);
       }
   }

   public function withdrawrequest(Request $request)
   {

    $user_id = Auth::id();

    $user_bank_details = DB::table('user_bank_details')->select('*')->where('user_bank_details.user_id', $user_id)->where('user_bank_details.payment_mode', 'bank')->first();

    $user_paypal_details = DB::table('user_bank_details')->select('*')->where('user_bank_details.user_id', $user_id)->where('user_bank_details.payment_mode', 'paypal')->first();

    $user_wallet_requests = DB::table('withdraw_request')->select('*')->where('withdraw_request.user_id', $user_id)->get();

    return view('account.withdraw_request', ['user_id' => $user_id, 'user_wallet_requests' => $user_wallet_requests, 'user_bank_details' => $user_bank_details, 'user_paypal_details' => $user_paypal_details]);

}

public function userwithdrawrequest(Request $request)
{

    $validator = Validator::make($request->all(), [
        'amount'         => 'required',
        'payment_method' => 'required',
    ]);

    if ($validator->fails()) {
        return redirect('account/withdrawrequest')
        ->withErrors($validator)
        ->withInput();
    }

    $user_id          = decrypt($request->input('enc_hash'));
    $requested_amount = $request->input('amount');
    $created_date     = date('Y-m-d H:i:s');

    $user_wallet_amount = DB::table('wallet')->select('*')->where('wallet.user_id', $user_id)->first();

    $total_wallet_amount = $user_wallet_amount->wallet_amount + $user_wallet_amount->blocked_amount;

    if ($total_wallet_amount < MINIMUM_WITHDRAW_AMOUNT) {
        return redirect('/account/withdrawrequest')->with('error_amount', 'Minimum Wallet Fund Required is '.MINIMUM_WITHDRAW_AMOUNT.'.  You can not withdraw money if your wallet fund is less than '.MINIMUM_WITHDRAW_AMOUNT);
    }
    $restWalletAmount = $total_wallet_amount-$requested_amount;
    if($restWalletAmount<MINIMUM_WITHDRAW_AMOUNT){
        return redirect('/account/withdrawrequest')->with('error_amount', 'Minimum Wallet Fund Required is '.MINIMUM_WITHDRAW_AMOUNT.'.  You can not withdraw money if your wallet fund is less than '.MINIMUM_WITHDRAW_AMOUNT);
    }
    

    $user_bank_details = DB::table('user_bank_details')->select('*')->where('user_bank_details.user_id', $user_id)->get();

    if (count($user_bank_details) == 0) {
        return redirect('/account/withdrawrequest')->with('error_amount', 'Please add your Paypal/Bank details');
    }

    /*if ($newAmount == '0' || $newAmount < '0') {
        return redirect('/account/withdrawrequest')->with('error_amount', 'Request amount must be greater than '.MINIMUM_WITHDRAW_AMOUNT);
    }*/

    

    if ($requested_amount > $total_wallet_amount) {

        return redirect('/account/withdrawrequest')->with('error_amount', 'Request amount is greater than Wallet Balance. Your wallet balance is Rs. ' . $total_wallet_amount);
    }

        // INSERT WITHDRAW REQUEST
    $payment_method = $request->input('payment_method');
    $withdraw_request_response = DB::table('withdraw_request')->insert(
        ['user_id' => $user_id, 'amount' => $requested_amount,'front_payment_method' => $payment_method, 'status' => 'pending', 'remarks' => '', 'created_date' => $created_date]
    );

    if ($withdraw_request_response) {

        return redirect('/account/withdrawrequest')->with('withdraw_success_request', 'Your request submitted successfully');

    } else {

        return redirect('/account/withdrawrequest')->with('error_amount', 'Something went wrong.Please try again later!');
    }

}

public function releasepayment_employer(Request $request)
{   
    $bid_amount=0;
    
    $milestone_id      =$request->input('milestone_id');
    
    if(!empty($milestone_id))
    {
        foreach ($milestone_id as $milestonekey) {
            $milestonesData =DB::table('milestones')
            ->where('milestones.jobmilestones_id',$milestonekey)
            ->select('*')
            ->first();

            @$bid_amount+=$milestonesData->jobmilestones_amount;
            $project_fee=$milestonesData->project_fee;
            /*$updatemilestone = DB::update('update milestones set milestone_status ="yes" where jobmilestones_id = ?',$milestonekey);*/

            $updatemilestone =   DB::table('milestones')
            ->where('jobmilestones_id', $milestonekey)
            ->limit(1) 
            ->update(array('milestone_status' => 'yes'));
        }


    }


    $employer_id     = decrypt($request->input('employer_id'));
    $influencer_id   = decrypt($request->input('influencer_id'));
    $conversation_id = decrypt($request->input('conversation_id'));
    $post_id         = decrypt($request->input('post_id'));
    $created_date    = date('Y-m-d H:i:s');


        // EMPLOYER INFO
    $employer_information = DB::table('users')
    ->select('users.*')
    ->where('users.id', $employer_id)
    ->first();

    $employer_name = $employer_information->name;
    $employer_main_pakcage_id = $employer_information->package_id;

        // EMPLOYER INFO

        // INFLUENCER INFO
    $influencer_information = DB::table('users')
    ->select('users.*')
    ->where('users.id', $influencer_id)
    ->first();

    $influencer_name = $influencer_information->name;
    if($influencer_information->package_id!=''){
        $user_package_id = $influencer_information->package_id;
    }else{
        $user_package_id ='1';
    }

        // INFLUENCER INFO
    /*$commision = $bid_amount * COMMISION_EMPLOYER/100;*/
    if($project_fee=='pending'){
     $commision= \App\Helpers\UrlGen::get_influencer_packageinfo($user_package_id);
    $employercommision= \App\Helpers\UrlGen::get_employer_packageinfo($employer_main_pakcage_id);
    }else{
       $commision='0';
       $employercommision='0'; 
    }

     

    $total_required_balance = $commision + $bid_amount;
    $total_required_balance_influencer =$bid_amount-$commision;
    //employer commission

  
    $block_amount_of_employer=$employercommision+$bid_amount;

    $employer_transaction = DB::update('update jobwallet set blocked_amount = blocked_amount - ' . $block_amount_of_employer . ' where user_id = ?', [$employer_id]);

        // EMPLOYER TRANSACTION HISTORY //
     $feespaid =   DB::table('milestones')
            ->where('jobmilestones_message_id',$conversation_id)
            ->update(array('project_fee' => 'paid'));

    $transaction_remarks = 'Amount deducted for the project completion to Influencer (' . $influencer_name . ') Rs' . $total_required_balance;

    $transaction_history_response = DB::table('transactionhistory')->insert(
        ['package_payment_id' => '', 'user_id' => $employer_id, 'amount' => $total_required_balance, 'transaction_type' => 'debit', 'remarks' => $transaction_remarks, 'package_id' => '', 'created_date' => $created_date]
    );

        // EMPLOYER TRANSACTION HISTORY //

    if ($employer_transaction && $transaction_history_response) {


        $influencer_wallet = DB::update('update jobwallet set wallet_amount = wallet_amount + ' . $total_required_balance_influencer . ' where user_id = ?', [$influencer_id]);
        $commission_super_admin = DB::update('update jobwallet set wallet_amount = wallet_amount + '.$commision.'  where user_id = ?', [1]);
        if($commision!='0'){
             $transaction_history_response = DB::table('transactionhistory')->insert(
        ['package_payment_id' => '', 'user_id' => '1', 'amount' => $commision, 'transaction_type' => 'credit', 'remarks' => $transaction_remarks, 'package_id' => '', 'created_date' => $created_date]);
        }

            // INFLUENCER TRANSACTION HISTORY //

        $transaction_remarks = 'Amount added for the project completion from Employer (' . $employer_name . ') Rs' . $total_required_balance_influencer;

        $transaction_history_response_influencer = DB::table('transactionhistory')->insert(
            ['package_payment_id' => '', 'user_id' => $influencer_id, 'amount' => $total_required_balance_influencer, 'transaction_type' => 'credit', 'remarks' => $transaction_remarks, 'package_id' => '', 'created_date' => $created_date]
        );
        

            // Notification//
        DB::table('notifications')->insert(
            ['notification_to_user_id' => $influencer_id, 'notification_text' => $transaction_remarks, 'notification_is_read' => '0', 'created_date' => $created_date]
        );
            //Notification//

            // INFLUENCER TRANSACTION HISTORY //


        if ($influencer_wallet && $transaction_history_response_influencer) {


            $milestonesCheckstatus = DB::table('milestones')->where('jobmilestones_message_id',$conversation_id)->where('milestone_status','no')->count();

            if($milestonesCheckstatus!='0'){
                $project_status='waiting';
            }else{
             $project_status='completed';

         }



         /* $update_project_status = DB::update('update jobprojectaward set project_status = ? where influencer_id = ? and employer_id = ? and post_id = ? and conversation_id = ?', [$project_status,$influencer_id, $employer_id, $post_id, $conversation_id]);*/
         $update_project_status =   DB::table('projectaward')
         ->where('influencer_id',$influencer_id)
         ->where('employer_id',$employer_id)
         ->where('post_id',$post_id)
         ->where('conversation_id',$conversation_id)
         ->limit(1) 
         ->update(array('project_status' =>$project_status));

         echo json_encode('success');
              /* if ($update_project_status) {
                echo json_encode('success');

            } else {
                echo json_encode('error');
            }*/

        } else {
            echo json_encode('error');
        }

    } else {
        echo json_encode('error');
    }
}
public function releasepayment_package_employer(Request $request)
{   

    $bid_amount=0;
    $rate_packages_flag=$request->input('rate_packages_flag');

    $influencer_package_id = decrypt($request->input('enc_rate_packages_id'));
    $package_type          = decrypt($request->input('enc_rate_packages_type'));

    if ($influencer_package_id) 
    {

        $package_info = DB::table('myrate')
        ->select('myrate.*')
        ->where('myrate.id', $influencer_package_id)
        ->first();
        if ($package_type == 'basic') {
            $bid_amount = $package_info->basic_package_price;
            $productinfo    = $package_info->basic_package_title;

        }
        if ($package_type == 'standard') {
            $bid_amount = $package_info->standard_package_price;
            $productinfo    = $package_info->standard_package_title;

        }
        if ($package_type == 'premium') {
            $bid_amount = $package_info->premium_package_price;
            $productinfo    = $package_info->premium_package_title;

        }

    }


    $employer_id     = decrypt($request->input('employer_id'));
    $influencer_id   = decrypt($request->input('influencer_id'));
    $conversation_id = decrypt($request->input('conversation_id'));
    $post_id         = decrypt($request->input('post_id'));
    $created_date    = date('Y-m-d H:i:s');


        // EMPLOYER INFO
    $employer_information = DB::table('users')
    ->select('users.*')
    ->where('users.id', $employer_id)
    ->first();

    $employer_name = $employer_information->name;
    $employer_main_pakcage_id = $employer_information->package_id;
    

        // EMPLOYER INFO

        // INFLUENCER INFO
    $influencer_information = DB::table('users')
    ->select('users.*')
    ->where('users.id', $influencer_id)
    ->first();

    $influencer_name = $influencer_information->name;
    if($influencer_information->package_id!=''){
    $user_package_id = $influencer_information->package_id;
    }else{
    $user_package_id ='1'; 
    }
    


        // INFLUENCER INFO
    /*$commision = $bid_amount * PACKAGE_COMMISION/100;*/
    $commision= \App\Helpers\UrlGen::get_influencer_packageinfo($user_package_id);
    $total_required_balance = $bid_amount-$commision;
    $total_required_balance_employer = $commision + $bid_amount;
    //employer commsionsssss
    $employercommision= \App\Helpers\UrlGen::get_employer_packageinfo($employer_main_pakcage_id);
    $block_amount_of_employer=$employercommision+$bid_amount;

    // negative wallet balance check

    $check_employer_wallet_balance = DB::table('wallet')
    ->select('wallet.*')
    ->where('wallet.user_id', $employer_id)
    ->first();
    

    $check_negative_value = $check_employer_wallet_balance->blocked_amount - $total_required_balance_employer;


    if($check_negative_value < 0)
    {

       echo json_encode('negative_balance_check');
       exit();

   }

   $employer_transaction = DB::update('update jobwallet set blocked_amount = blocked_amount - ' . $block_amount_of_employer . ' where user_id = ?', [$employer_id]);

        // EMPLOYER TRANSACTION HISTORY //

   $transaction_remarks = 'Amount deducted for the project completion to Influencer (' . $influencer_name . ') {!! \App\Helpers\Number::money($total_required_balance_employer) !!}';

   $transaction_history_response = DB::table('transactionhistory')->insert(
    ['package_payment_id' => '', 'user_id' => $employer_id, 'amount' => $total_required_balance_employer, 'transaction_type' => 'debit', 'remarks' => $transaction_remarks, 'package_id' => '', 'created_date' => $created_date]
);

        // EMPLOYER TRANSACTION HISTORY //

   if ($employer_transaction && $transaction_history_response) {


    $influencer_wallet = DB::update('update jobwallet set wallet_amount = wallet_amount + ' . $total_required_balance . ' where user_id = ?', [$influencer_id]);

    $commission_super_admin = DB::update('update jobwallet set wallet_amount = wallet_amount + '.$commision.'  where user_id = ?', [1]);
    if($commision!='0'){
        $transaction_remarkss = 'Commission added for the project '.$productinfo.'  {!! \App\Helpers\Number::money($commision) !!}';
             $transaction_history_response = DB::table('transactionhistory')->insert(
        ['package_payment_id' => '', 'user_id' => '1', 'amount' => $commision, 'transaction_type' => 'credit', 'remarks' => $transaction_remarkss, 'package_id' => '', 'created_date' => $created_date]);
        }


        // COMMISSION MESSAGE TO ADMIN 
    $transaction_remarks = 'Commission added for the project '.$productinfo.'  {!! \App\Helpers\Number::money($commision) !!}';

    $transaction_history_response_influencer = DB::table('transactionhistory')->insert(
        ['package_payment_id' => '', 'user_id' => $influencer_id, 'amount' => $total_required_balance, 'transaction_type' => 'credit', 'remarks' => $transaction_remarks, 'package_id' => '', 'created_date' => $created_date]
    );
        // COMMISSION MESSAGE TO ADMIN 

            // INFLUENCER TRANSACTION HISTORY //

    $transaction_remarks = 'Amount added for the project completion from Employer (' . $employer_name . ') Rs' . $total_required_balance;

    $transaction_history_response_influencer = DB::table('transactionhistory')->insert(
        ['package_payment_id' => '', 'user_id' => $influencer_id, 'amount' => $total_required_balance, 'transaction_type' => 'credit', 'remarks' => $transaction_remarks, 'package_id' => '', 'created_date' => $created_date]
    );

            // Notification//
    DB::table('notifications')->insert(
        ['notification_to_user_id' => $influencer_id, 'notification_text' => $transaction_remarks, 'notification_is_read' => '0', 'created_date' => $created_date]
    );
            //Notification//

            // INFLUENCER TRANSACTION HISTORY //


    if ($influencer_wallet && $transaction_history_response_influencer) {


        $project_status='completed';

        /* $update_project_status = DB::update('update jobprojectaward set project_status = ? where influencer_id = ? and employer_id = ? and post_id = ? and conversation_id = ?', [$project_status,$influencer_id, $employer_id, $post_id, $conversation_id]);*/
        $update_project_status =   DB::table('projectaward')
        ->where('influencer_id',$influencer_id)
        ->where('employer_id',$employer_id)
        ->where('post_id',$post_id)
        ->where('conversation_id',$conversation_id)
        ->limit(1) 
        ->update(array('project_status' =>$project_status));

        echo json_encode('success');
              /* if ($update_project_status) {
                echo json_encode('success');

            } else {
                echo json_encode('error');
            }*/

        } else {
            echo json_encode('error');
        }

    } else {
        echo json_encode('error');
    }
}

public function rating_review_influencer(Request $request)
{

    $rating             = $request->input('rating');
    $employer_id        = decrypt($request->input('employer_id'));
    $influencer_id      = decrypt($request->input('influencer_id'));
    $post_id            = decrypt($request->input('post_id'));
    $jobprojectaward_id = decrypt($request->input('jobprojectaward_id'));
    $review             = $request->input('review');
    $created_date       = date('Y-m-d H:i:s');
    $conversation_id    = decrypt($request->input('conversation_id'));

        // DB QUERY

        // Notification//

    $employer_info = DB::table('users')
    ->where('users.id', $employer_id)
    ->select('users.name')
    ->first();

    $notification_text = $employer_info->name . ' gave review to you.';

    DB::table('notifications')->insert(
        ['notification_to_user_id' => $employer_id, 'notification_text' => $notification_text, 'notification_is_read' => '0', 'created_date' => $created_date]
    );
        //Notification//

    $rating_review_response = DB::table('rating_review')->insert(
        ['to_user_id_review' => $employer_id, 'from_user_id_review' => $influencer_id, 'post_id_review' => $post_id, 'review' => $review, 'rating' => $rating, 'jobprojectaward_id_review' => $jobprojectaward_id, 'created_at' => $created_date, 'conversation_id' => $conversation_id, 'rating_review_employer' => '1']
    );

    if ($rating_review_response) {
        echo json_encode(1);
    } else {
        echo json_encode(2);
    }

}

public function rating_review(Request $request)
{

    $rating             = $request->input('rating');
    $employer_id        = decrypt($request->input('employer_id'));
    $influencer_id      = decrypt($request->input('influencer_id'));
    $post_id            = decrypt($request->input('post_id'));
    $jobprojectaward_id = decrypt($request->input('jobprojectaward_id'));
    $review             = $request->input('review');
    $created_date       = date('Y-m-d H:i:s');

        // DB QUERY

    $rating_review_response = DB::table('rating_review')->insert(
        ['to_user_id_review' => $employer_id, 'from_user_id_review' => $influencer_id, 'post_id_review' => $post_id, 'review' => $review, 'rating' => $rating, 'jobprojectaward_id_review' => $jobprojectaward_id, 'created_at' => $created_date, 'rating_review_influencer' => '1']
    );

        // Notification//

    $influencer_info = DB::table('users')
    ->where('users.id', $influencer_id)
    ->select('users.name')
    ->first();

    $notification_text = $influencer_info->name . ' gave review to you.';

    DB::table('notifications')->insert(
        ['notification_to_user_id' => $employer_id, 'notification_text' => $notification_text, 'notification_is_read' => '0', 'created_date' => $created_date]
    );
        //Notification//

    if ($rating_review_response) {
       return Redirect::back()->with('successmessage', 'Review successfully submitted.');
       /*return redirect('/account/awardedprojects')->with('successmessage', 'Review successfully submitted.');*/
   } else {
    /*return redirect('/account/awardedprojects')->with('errormessage', 'Something went wrong.Please try again later.');*/
    return Redirect::back()->with('errormessage', 'Something went wrong.Please try again later.');
}

}

public function save_bank_details(Request $request)
{

    $user_id = Auth::id();

    $first_name      = encrypt($request->input('first_name'));
    $last_name       = encrypt($request->input('last_name'));
    $bank_name       = encrypt($request->input('bank_name'));
    $branch_address  = encrypt($request->input('branch_address'));
    $ifsc_swift_code = encrypt($request->input('ifsc_swift_code'));
    $phone_number    = encrypt($request->input('phone_number'));
    $payment_method  = 'bank';
    $created_date    = date('Y-m-d H:i:s');

    $user_bank_details = DB::table('user_bank_details')->select('*')->where('user_bank_details.user_id', $user_id)->where('user_bank_details.payment_mode', 'bank')->first();

    if (!empty($user_bank_details)) {

        $response = DB::table('user_bank_details')->where('user_id', $user_id)->update(
            ['user_id' => $user_id, 'first_name' => $first_name, 'last_name' => $last_name, 'bank_name' => $bank_name, 'branch_address' => $branch_address, 'ifsc_swift_code' => $ifsc_swift_code, 'phone_number' => $phone_number, 'paypal_email' => '', 'payment_mode' => $payment_method, 'created_date' => $created_date]
        );

    } else {

        $response = DB::table('user_bank_details')->insert(
            ['user_id' => $user_id, 'first_name' => $first_name, 'last_name' => $last_name, 'bank_name' => $bank_name, 'branch_address' => $branch_address, 'ifsc_swift_code' => $ifsc_swift_code, 'phone_number' => $phone_number, 'paypal_email' => '', 'payment_mode' => $payment_method, 'created_date' => $created_date]
        );
    }

    if (!empty($response)) {
        echo json_encode(1);
    } else {
        echo json_encode(2);
    }

}

public function save_paypal_details(Request $request)
{

    $user_id      = Auth::id();
    $paypal_email = encrypt($request->input('paypal_email'));
    $created_date = date('Y-m-d H:i:s');

    $user_paypal_details = DB::table('user_bank_details')->select('*')->where('user_bank_details.user_id', $user_id)->where('user_bank_details.payment_mode', 'paypal')->first();

    if (!empty($user_paypal_details)) {

        $response = DB::table('user_bank_details')->update(
            ['user_id' => $user_id, 'first_name' => '', 'last_name' => '', 'bank_name' => '', 'branch_address' => '', 'ifsc_swift_code' => '', 'phone_number' => '', 'paypal_email' => $paypal_email, 'payment_mode' => 'paypal', 'created_date' => $created_date]
        )->where('user_id', $user_id);

    } else {

        $response = DB::table('user_bank_details')->insert(
            ['user_id' => $user_id, 'first_name' => '', 'last_name' => '', 'bank_name' => '', 'branch_address' => '', 'ifsc_swift_code' => '', 'phone_number' => '', 'paypal_email' => $paypal_email, 'payment_mode' => 'paypal', 'created_date' => $created_date]
        );

    }

    if (!empty($response)) {
        echo json_encode(1);
    } else {
        echo json_encode(2);
    }

}

public function notifications(Request $request)
{

    $user_id = Auth::id();

    DB::update('update jobnotifications set notification_is_read = 1 where notification_to_user_id = ?', [$user_id]);

    $notification_info = DB::table('notifications')
    ->where('notifications.notification_to_user_id', $user_id)
    ->select('*')
    ->get();

    return view('account.new_notifcation', ['notification_info' => $notification_info]);

}

public function check_employer_review(Request $request)
{

    $conversation_id = $request->input('conversation_id');
    $employer_id     = $request->input('employer_id');
    $influencer_id   = $request->input('influencer_id');
    $post_id         = $request->input('post_id');

    $check_record = DB::select('select * from jobrating_review where conversation_id = "' . $conversation_id . '" && from_user_id_review = "' . $employer_id . '" && to_user_id_review = "' . $influencer_id . '" && post_id_review = "' . $post_id . '" && rating_review_employer = "1"');

    $count_record = count($check_record);

    if ($count_record > 0) {
        echo json_encode(1);
    } else {
        echo json_encode(2);
    }

    die;

}

}
