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

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Auth\Traits\VerificationTrait;
use App\Http\Requests\Admin\Request;
use App\Http\Requests\Admin\UserRequest as StoreRequest;
use App\Http\Requests\Admin\UserRequest as UpdateRequest;
use App\Models\Gender;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Scopes\VerifiedScope;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Larapen\Admin\app\Http\Controllers\PanelController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;


class UserController extends PanelController
{
	use VerificationTrait;
	
	public function setup()
	{
		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->xPanel->setModel('App\Models\User');
		
		// If the logged admin user has permissions to manage users and is has not 'super-admin' role,
		// don't allow him to manage 'super-admin' role's users.
		if (!auth()->user()->can(Permission::getSuperAdminPermissions())) {
			// Get 'super-admin' role's users IDs
			$usersIds = [];
			try {
				$users = User::withoutGlobalScopes([VerifiedScope::class])->role('super-admin')->get(['id', 'created_at']);
				if ($users->count() > 0) {
					$usersIds = $users->keyBy('id')->keys()->toArray();
				}
			} catch (\Exception $e) {}
			
			// Exclude 'super-admin' role's users from list
			if (!empty($usersIds)) {
				$this->xPanel->addClause('whereNotIn', 'id', $usersIds);
			}
		}
		
		$this->xPanel->setRoute(admin_uri('users'));
		$this->xPanel->setEntityNameStrings(trans('admin::messages.user'), trans('admin::messages.users'));
		if (!request()->input('order')) {
			$this->xPanel->orderBy('created_at', 'DESC');
		}
		
		$this->xPanel->addButtonFromModelFunction('top', 'bulk_delete_btn', 'bulkDeleteBtn', 'end');
		$this->xPanel->addButtonFromModelFunction('line', 'impersonate', 'impersonateBtn', 'beginning');
		$this->xPanel->removeButton('delete');
		$this->xPanel->addButtonFromModelFunction('line', 'delete', 'deleteBtn', 'end');
		
		// Filters
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'id',
			'type'  => 'text',
			'label' => 'ID',
		],
		false,
		function ($value) {
			$this->xPanel->addClause('where', 'id', '=', $value);
		});
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'from_to',
			'type'  => 'date_range',
			'label' => trans('admin::messages.Date range'),
		],
		false,
		function ($value) {
			$dates = json_decode($value);
			$this->xPanel->addClause('where', 'created_at', '>=', $dates->from);
			$this->xPanel->addClause('where', 'created_at', '<=', $dates->to);
		});
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'name',
			'type'  => 'text',
			'label' => trans('admin::messages.Name'),
		],
		false,
		function ($value) {
			$this->xPanel->addClause('where', 'name', 'LIKE', "%$value%");
		});
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'country',
			'type'  => 'select2',
			'label' => trans('admin::messages.Country'),
		],
		getCountries(),
		function ($value) {
			$this->xPanel->addClause('where', 'country_code', '=', $value);
		});
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'status',
			'type'  => 'dropdown',
			'label' => trans('admin::messages.Status'),
		], [
			1 => trans('admin::messages.Unactivated'),
			2 => trans('admin::messages.Activated'),
		], function ($value) {
			if ($value == 1) {
				$this->xPanel->addClause('where', 'verified_email', '=', 0);
				$this->xPanel->addClause('orWhere', 'verified_phone', '=', 0);
			}
			if ($value == 2) {
				$this->xPanel->addClause('where', 'verified_email', '=', 1);
				$this->xPanel->addClause('where', 'verified_phone', '=', 1);
			}
		});
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'type',
			'type'  => 'dropdown',
			'label' => trans('admin::messages.Permissions/Roles'),
		], [
			1 => trans('admin::messages.Has Admins Permissions'),
			2 => trans('admin::messages.Has Super-Admins Permissions'),
			3 => trans('admin::messages.Has Super-Admins Role'),
		], function ($value) {
			if ($value == 1) {
				$this->xPanel->addClause('permission', Permission::getStaffPermissions());
			}
			if ($value == 2) {
				$this->xPanel->addClause('permission', Permission::getSuperAdminPermissions());
			}
			if ($value == 3) {
				$this->xPanel->addClause('role', Role::getSuperAdminRole());
			}
		});
		
		/*
		|--------------------------------------------------------------------------
		| COLUMNS AND FIELDS
		|--------------------------------------------------------------------------
		*/
		if (request()->segment(2) != 'account') {
			// COLUMNS
			$this->xPanel->addColumn([
				'name'  => 'id',
				'label' => '',
				'type'  => 'checkbox',
				'orderable' => false,
			]);
			$this->xPanel->addColumn([
				'name'  => 'created_at',
				'label' => trans("admin::messages.Date"),
				'type'  => 'datetime',
			]);
			$this->xPanel->addColumn([
				'name'  => 'name',
				'label' => trans("admin::messages.Name"),
			]);
			$this->xPanel->addColumn([
				'name'  => 'email',
				'label' => trans("admin::messages.Email"),
			]);
			$this->xPanel->addColumn([
				'name'      => 'user_type_id',
				'label'     => trans("admin::messages.Type"),
				'model'     => 'App\Models\UserType',
				'entity'    => 'userType',
				'attribute' => 'name',
				'type'      => 'select',
			]);
			$this->xPanel->addColumn([
				'label'         => trans("admin::messages.Country"),
				'name'          => 'country_code',
				'type'          => 'model_function',
				'function_name' => 'getCountryHtml',
			]);
			$this->xPanel->addColumn([
				'name'          => 'verified_email',
				'label'         => trans("admin::messages.Verified Email"),
				'type'          => 'model_function',
				'function_name' => 'getVerifiedEmailHtml',
			]);
			$this->xPanel->addColumn([
				'name'          => 'verified_phone',
				'label'         => trans("admin::messages.Verified Phone"),
				'type'          => 'model_function',
				'function_name' => 'getVerifiedPhoneHtml',
			]);


			$this->xPanel->addColumn([
				'name'          => 'is_featured',
				'label'         => trans("admin::messages.Featured"),
				'type'          => 'model_function',
				'function_name' => 'getIsFeaturedHtml',
			]);
			
			// FIELDS
			$emailField = [
				'name'       => 'email',
				'label'      => trans("admin::messages.Email"),
				'type'       => 'email',
				'attributes' => [
					'placeholder' => trans("admin::messages.Email"),
				],
			];
			$this->xPanel->addField($emailField + [
				'wrapperAttributes' => [
					'class' => 'form-group col-md-6',
				]
			], 'create');
			$this->xPanel->addField($emailField, 'update');
			
			$passwordField = [
				'name'       => 'password',
				'label'      => trans("admin::messages.Password"),
				'type'       => 'password',
				'attributes' => [
					'placeholder' => trans("admin::messages.Password"),
				],
				'wrapperAttributes' => [
					'class' => 'form-group col-md-6',
				],
			];
			$this->xPanel->addField($passwordField, 'create');
			
			$this->xPanel->addField([
				'label'             => trans("admin::messages.Gender"),
				'name'              => 'gender_id',
				'type'              => 'select2_from_array',
				'options'           => $this->gender(),
				'allows_null'       => false,
				'wrapperAttributes' => [
					'class' => 'form-group col-md-6',
				],
			]);
			$this->xPanel->addField([
				'name'              => 'name',
				'label'             => trans("admin::messages.Name"),
				'type'              => 'text',
				'attributes'        => [
					'placeholder' => trans("admin::messages.Name"),
				],
				'wrapperAttributes' => [
					'class' => 'form-group col-md-6',
				],
			]);
			$this->xPanel->addField([
				'name'              => 'phone',
				'label'             => trans("admin::messages.Phone"),
				'type'              => 'text',
				'attributes'        => [
					'placeholder' => trans("admin::messages.Phone"),
				],
				'wrapperAttributes' => [
					'class' => 'form-group col-md-6',
				],
			]);
			$this->xPanel->addField([
				'name'              => 'phone_hidden',
				'label'             => trans("admin::messages.Phone hidden"),
				'type'              => 'checkbox',
				'wrapperAttributes' => [
					'class' => 'form-group col-md-6',
					'style' => 'margin-top: 20px;',
				],
			]);
			$this->xPanel->addField([
				'label'             => trans("admin::messages.Country"),
				'name'              => 'country_code',
				'model'             => 'App\Models\Country',
				'entity'            => 'country',
				'attribute'         => 'asciiname',
				'type'              => 'select2',
				'wrapperAttributes' => [
					'class' => 'form-group col-md-6',
				],
			]);
			$this->xPanel->addField([
				'name'              => 'user_type_id',
				'label'             => trans("admin::messages.Type"),
				'type'              => 'select2_from_array',
				'options'           => $this->userType(),
				'allows_null'       => true,
				'wrapperAttributes' => [
					'class' => 'form-group col-md-6',
				],
			]);
			$this->xPanel->addField([
				'name'              => 'verified_email',
				'label'             => trans("admin::messages.Verified Email"),
				'type'              => 'checkbox',
				'wrapperAttributes' => [
					'class' => 'form-group col-md-6',
					'style' => 'margin-top: 20px;',
				],
			]);
			$this->xPanel->addField([
				'name'              => 'verified_phone',
				'label'             => trans("admin::messages.Verified Phone"),
				'type'              => 'checkbox',
				'wrapperAttributes' => [
					'class' => 'form-group col-md-6',
					'style' => 'margin-top: 20px;',
				],
			]);

			// IS FEATURED CODE WEBC 20 AUG
			$this->xPanel->addField([
				'name'              => 'is_featured',
				'label'             => trans("admin::messages.Featured"),
				'type'              => 'checkbox',
				'wrapperAttributes' => [
					'class' => 'form-group col-md-6',
					'style' => 'margin-top: 20px;',
				],
			]);
			// IS FEATURED CODE WEBC 20 AUG
			$this->xPanel->addField([
				'name'              => 'blocked',
				'label'             => trans("admin::messages.Blocked"),
				'type'              => 'checkbox',
				'wrapperAttributes' => [
					'class' => 'form-group col-md-6',
					'style' => 'margin-top: 20px;',
				],
			]);
			$entity = $this->xPanel->getModel()->find(request()->segment(3));

			if (!empty($entity)) {
				$ipLink = config('larapen.core.ipLinkBase') . $entity->ip_addr;
				$this->xPanel->addField([
					'name'  => 'ip_addr',
					'type'  => 'custom_html',
					'value' => '<h5><strong>IP:</strong> <a href="' . $ipLink . '" target="_blank">' . $entity->ip_addr . '</a></h5>',
				], 'update');
				if (!empty($entity->email)) {
					$btnUrl = admin_url('blacklists/add') . '?email=' . $entity->email;
					
					$cMsg = trans('admin::messages.confirm_this_action');
					$cLink = "window.location.replace('" . $btnUrl . "'); window.location.href = '" . $btnUrl . "';";
					$cHref = "javascript: if (confirm('" . addcslashes($cMsg, "'") . "')) { " . $cLink . " } else { void('') }; void('')";
					
					$btnText = trans("admin::messages.ban_the_user");
					$btnHint = trans("admin::messages.ban_the_user_email", ['email' => $entity->email]);
					$tooltip = ' data-toggle="tooltip" title="' . $btnHint . '"';
					
					$btnLink = '<a href="' . $cHref . '" class="btn btn-danger"' . $tooltip . '>' . $btnText . '</a>';
					$this->xPanel->addField([
						'name'              => 'ban_button',
						'type'              => 'custom_html',
						'value'             => $btnLink,
						'wrapperAttributes' => [
							'style' => 'text-align:center;',
						],
					], 'update');
				}
			}
			// Only 'super-admin' can assign 'roles' or 'permissions' to users
			// Also logged admin user cannot manage his own 'role' or 'permissions'
			if (
				auth()->user()->can(Permission::getSuperAdminPermissions())
				&& auth()->user()->id != request()->segment(3)
			) {
				$this->xPanel->addField([
					'name'  => 'separator',
					'type'  => 'custom_html',
					'value' => '<hr>'
				]);
				$this->xPanel->addField([
					// two interconnected entities
					'label'             => trans('admin::messages.user_role_permission'),
					'field_unique_name' => 'user_role_permission',
					'type'              => 'checklist_dependency',
					'name'              => 'roles_and_permissions', // the methods that defines the relationship in your Model
					'subfields'         => [
						'primary'   => [
							'label'            => trans('admin::messages.roles'),
							'name'             => 'roles', // the method that defines the relationship in your Model
							'entity'           => 'roles', // the method that defines the relationship in your Model
							'entity_secondary' => 'permissions', // the method that defines the relationship in your Model
							'attribute'        => 'name', // foreign key attribute that is shown to user
							'model'            => config('permission.models.role'), // foreign key model
							'pivot'            => true, // on create&update, do you need to add/delete pivot table entries?]
							'number_columns'   => 3, //can be 1,2,3,4,6
						],
						'secondary' => [
							'label'          => mb_ucfirst(trans('admin::messages.permission_singular')),
							'name'           => 'permissions', // the method that defines the relationship in your Model
							'entity'         => 'permissions', // the method that defines the relationship in your Model
							'entity_primary' => 'roles', // the method that defines the relationship in your Model
							'attribute'      => 'name', // foreign key attribute that is shown to user
							'model'          => config('permission.models.permission'), // foreign key model
							'pivot'          => true, // on create&update, do you need to add/delete pivot table entries?]
							'number_columns' => 3, //can be 1,2,3,4,6
						],
					],
				]);
			}
		}
	}


	public function user_wallet()
	{

		$list_users = DB::table('users')->select('*')
		->LeftJoin('wallet', 'wallet.user_id', '=', 'users.id')
		->get();

/*$total_employer_balance =DB::table('users')
->select('jobwallet.wallet_amount')
->join('jobwallet','jobwallet.user_id','=','users.id')
->where(['users.user_type_id' => '1', 'users.id' => '1'])
->get();
echo '<pre>';
print_r($total_employer_balance);
die;*/
		$total_employer_balance = DB::select('select sum(wallet_amount) as employer_wallet_balance from jobwallet join jobusers on jobusers.id = jobwallet.user_id where jobusers.user_type_id = 1 and jobusers.id != 1');

		/*$total_employer_balance =DB::table('users')
->select('users.id','users.name','profiles.photo')
->join('profiles','profiles.id','=','users.id')
->where(['something' => 'something', 'otherThing' => 'otherThing'])
->get();*/
	$total_employers =DB::table('users')->where('user_type_id',1)->where('id','!=',1)->count();
		
		$total_influencer_balance = DB::select('select sum(wallet_amount) as influencer_wallet_balance from jobwallet join jobusers on jobusers.id = jobwallet.user_id where jobusers.user_type_id = 2 and jobusers.id != 1');
		$total_influencer =DB::table('users')->where('user_type_id',2)->where('id','!=',1)->count();
         
$total_commisstion = DB::select('select sum(wallet_amount) as commisstion from jobwallet join jobusers on jobusers.id = jobwallet.user_id where jobusers.id = 1');
		$total_influencer =DB::table('users')->where('user_type_id',2)->where('id','!=',1)->count();
         


		if(isset($total_employer_balance[0]->employer_wallet_balance))
		{

			$emp_wallet_balance = $total_employer_balance[0]->employer_wallet_balance;

		}else{

			$emp_wallet_balance = '0';

		}
		
		
		if(isset($total_influencer_balance[0]->influencer_wallet_balance))
		{

			$influencer_wallet_balance = $total_influencer_balance[0]->influencer_wallet_balance;

		}else{

			$influencer_wallet_balance = '0';

		}
		if(isset($total_commisstion[0]->commisstion))
		{

			$commisstion = $total_commisstion[0]->commisstion;

		}else{

			$commisstion = '0';

		}
		
		return view('vendor.admin.user_wallet')->with('list_users',$list_users)->with('emp_wallet_balance',$emp_wallet_balance)->with('total_employers',$total_employers)->with('influencer_wallet_balance',$influencer_wallet_balance)->with('total_influencer',$total_influencer)->with('commisstion',$commisstion);
	}

	public function user_withdraw_request()
	{

		$withdraw_request = DB::table('withdraw_request')
		->select('withdraw_request.*','users.name','users.email','users.user_type_id','wallet.wallet_amount','user_bank_details.first_name','user_bank_details.last_name','user_bank_details.bank_name','user_bank_details.branch_address','user_bank_details.ifsc_swift_code','user_bank_details.phone_number','user_bank_details.paypal_email')
		->join('users', 'users.id', '=', 'withdraw_request.user_id')
		->join('wallet', 'users.id', '=', 'wallet.user_id')
		->join('user_bank_details', 'users.id', '=', 'user_bank_details.user_id')
		->where('withdraw_request.status','pending')->get();
		//echo "<pre>";print_r($withdraw_request);die;
		return view('vendor.admin.user_withdraw_request')->with('withdraw_request',$withdraw_request);
	}
	public function completed_withdraw_request()
	{


		$withdraw_request = DB::table('withdraw_request')
		->select('withdraw_request.*','users.name','users.email','users.user_type_id','wallet.wallet_amount','user_bank_details.first_name','user_bank_details.last_name','user_bank_details.bank_name','user_bank_details.branch_address','user_bank_details.ifsc_swift_code','user_bank_details.phone_number','user_bank_details.paypal_email')
		->join('users', 'users.id', '=', 'withdraw_request.user_id')
		->join('wallet', 'users.id', '=', 'wallet.user_id')
		->join('user_bank_details', 'users.id', '=', 'user_bank_details.user_id')
		->where('withdraw_request.status','!=','pending')
		->get();
	
		return view('vendor.admin.completed_withdraw_request')->with('withdraw_request',$withdraw_request);
	}

	public function update_user_wallet_transaction_request(Request $request)
	{

		$request_id = $request->input('request_id');
		$trasaction_status = $request->input('trasaction_status');
		$payment_method = $request->input('payment_method');
		$remarks = $request->input('remarks');
		$user_id = $request->input('enc_user_id');
		$amount = $request->input('amount');
		
		$response = DB::update('update jobwithdraw_request set status = "'.$trasaction_status.'",payment_method = "'.$payment_method.'",remarks = "'.$remarks.'" where withdraw_request_id = ?', [$request_id]);
		
		if($response)
		{
           if($trasaction_status=='approve'){
           	$wallet_response = DB::update('update jobwallet set wallet_amount = wallet_amount - '.$amount.'  where user_id = ?', [$user_id]);
           }

			echo json_encode(1);
		}else{
			echo json_encode(2);
		}

	}

	public function wallet($user_id)
	{

		$user_check = DB::table('users')->select()->where('id',$user_id)->get();

		if(count($user_check) == 0)
		{
			return Redirect::to(url('/admin'));
		} 

		return view('vendor.admin.credit_debit_wallet')->with('user_id',$user_id);

	}

	public function wallet_transaction(Request $request)
	{

		$validate = $this->validate($request, [
			'transaction_type' => 'required',
			'amount' => 'required',
			'remarks' => 'required',
			'transaction_type' => 'required',
		]);


		$user_id = decrypt($request->input('enc_hash'));
		$amount = $request->input('amount');
		$remarks = $request->input('remarks');
		$transaction_type = $request->input('transaction_type');
		$created_date = date('Y-m-d H:i:s');


        // USER WALLET BALANCE

		$user_wallet_balance = DB::table('wallet')
		->where('wallet.user_id', $user_id)
		->select('wallet.*')
		->first();

		$success_check = 0;

		if($transaction_type == 'debit')
		{
			if(isset($user_wallet_balance) && $user_wallet_balance->wallet_amount < $amount){

				return redirect()->back()->with('error_message', 'User doesnot have Rs. '.$amount.' Balance');

				// return Redirect::back()->withErrors(['errors', 'User doesnot have Rs. '.$amount.' Balance']);

			}else{

				DB::update('update jobwallet set wallet_amount = wallet_amount - '.$amount.'  where user_id = ?', [$user_id]);
				$success_check = 1;
			}

		}
		
		// USER WALLET OPERATIONS
		
		if($transaction_type == 'credit')
		{   
			$user_wallet_check = DB::table('wallet')
		->where('wallet.user_id', $user_id)
		->select('wallet.*')
		->count();
		if($user_wallet_check>0){
			DB::update('update jobwallet set wallet_amount = wallet_amount + '.$amount.'  where user_id = ?', [$user_id]);
			$success_check = 1;
			}else{
				$add_balance = DB::table('wallet')->insert(
				['user_id' =>  $user_id,'wallet_amount' => $amount] 
			);
         $success_check = 1;
			}
		}

		// USER WALLET OPERATIONS
		if($success_check == 1){

			$transaction_history_response = DB::table('transactionhistory')->insert(
				['package_payment_id' => '','user_id' =>  $user_id,'amount' => $amount , 'transaction_type' => $transaction_type,'remarks' => $remarks ,'package_id' => '', 'created_date' => $created_date] 
			);

		}
		
		return redirect()->back()->with('message', 'Successfully Done.');

	}

	public function usertransactions($user_id)
	{

		$user_type = DB::table('users')->select('users.user_type_id')->where('id',$user_id)->get();

		if(count($user_type) == 0)
		{
			return Redirect::to(url('/admin'));
		}

		$user_transaction_history = DB::table('transactionhistory')->select()->where('user_id',$user_id)->get();

		$user_wallet_balance = DB::table('wallet')->select('wallet.wallet_amount')->where('user_id',$user_id)->get();

		$user_bids = DB::table('bids')->select('bids.*')->where('user_id',$user_id)->get();

		return view('vendor.admin.usertransactions')->with('user_transaction_history',$user_transaction_history)->with('user_wallet_balance',$user_wallet_balance)->with('user_type',$user_type)->with('user_bids',$user_bids);
		
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function account()
	{

		// FIELDS
		$this->xPanel->addField([
			'label'             => trans("admin::messages.Gender"),
			'name'              => 'gender_id',
			'type'              => 'select2_from_array',
			'options'           => $this->gender(),
			'allows_null'       => false,
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'name',
			'label'             => trans("admin::messages.Name"),
			'type'              => 'text',
			'placeholder'       => trans("admin::messages.Name"),
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'email',
			'label'             => trans("admin::messages.Email"),
			'type'              => 'email',
			'placeholder'       => trans("admin::messages.Email"),
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'password',
			'label'             => trans("admin::messages.Password"),
			'type'              => 'password',
			'placeholder'       => trans("admin::messages.Password"),
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'phone',
			'label'             => trans("admin::messages.Phone"),
			'type'              => 'text',
			'placeholder'       => trans("admin::messages.Phone"),
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'phone_hidden',
			'label'             => "Phone hidden",
			'type'              => 'checkbox',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
				'style' => 'margin-top: 20px;',
			],
		]);
		$this->xPanel->addField([
			'label'             => trans("admin::messages.Country"),
			'name'              => 'country_code',
			'model'             => 'App\Models\Country',
			'entity'            => 'country',
			'attribute'         => 'asciiname',
			'type'              => 'select2',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'user_type_id',
			'label'             => trans("admin::messages.Type"),
			'type'              => 'select2_from_array',
			'options'           => $this->userType(),
			'allows_null'       => true,
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		
		// Get logged user
		if (auth()->check()) {
			return $this->edit(auth()->user()->id);
		} else {
			abort(403, 'Not allowed.');
		}
	}
	
	public function store(StoreRequest $request)
	{
		$this->handleInput($request);
		
		return parent::storeCrud();
	}
	
	public function update(UpdateRequest $request)
	{
		$this->handleInput($request);
		
		// Prevent user's role removal
		if (
			auth()->user()->id == request()->segment(3)
			|| Str::contains(URL::previous(), admin_uri('account'))
		) {
			$this->xPanel->disableSyncPivot();
		}
		
		return parent::updateCrud();
	}
	
	// PRIVATE METHODS
	
	/**
	 * @return array
	 */
	private function gender()
	{
		$entries = Gender::trans()->get();
		
		return $this->getTranslatedArray($entries);
	}
	
	/**
	 * @return array
	 */
	private function userType()
	{
		$entries = UserType::active()->get();
		
		$tab = [];
		if ($entries->count() > 0) {
			foreach ($entries as $entry) {
				$tab[$entry->id] = $entry->name;
			}
		}
		
		return $tab;
	}
	
	/**
	 * Handle Input values
	 *
	 * @param \App\Http\Requests\Admin\Request $request
	 */
	private function handleInput(Request $request)
	{
		$this->handlePasswordInput($request);
		
		if ($this->isAdminUser($request)) {
			request()->merge(['is_admin' => 1]);
		} else {
			request()->merge(['is_admin' => 0]);
		}
	}
	
	/**
	 * Handle password input fields
	 *
	 * @param Request $request
	 */
	private function handlePasswordInput(Request $request)
	{
		// Remove fields not present on the user
		$request->request->remove('password_confirmation');
		
		/*
		// Encrypt password if specified
		if ($request->filled('password')) {
			$request->request->set('password', Hash::make($request->input('password')));
		} else {
			$request->request->remove('password');
		}
		*/
		
		// Encrypt password if specified (OK)
		if (request()->filled('password')) {
			request()->merge(['password' => Hash::make(request()->input('password'))]);
		} else {
			request()->replace(request()->except(['password']));
		}
	}
	
	/**
	 * Check if the set permissions are corresponding to the Staff permissions
	 *
	 * @param \App\Http\Requests\Admin\Request $request
	 * @return bool
	 */
	private function isAdminUser(Request $request)
	{
		$isAdmin = false;
		if (request()->filled('roles')) {
			$rolesIds = request()->input('roles');
			foreach ($rolesIds as $rolesId) {
				$role = Role::find($rolesId);
				if (!empty($role)) {
					$permissions = $role->permissions;
					if ($permissions->count() > 0) {
						foreach ($permissions as $permission) {
							if (in_array($permission->name, Permission::getStaffPermissions())) {
								$isAdmin = true;
							}
						}
					}
				}
			}
		}
		
		if (request()->filled('permissions')) {
			$permissionIds = request()->input('permissions');
			foreach ($permissionIds as $permissionId) {
				$permission = Permission::find($permissionId);
				if (in_array($permission->name, Permission::getStaffPermissions())) {
					$isAdmin = true;
				}
			}
		}
		
		return $isAdmin;
	}
		public function commission()
	{
		
		$commission = DB::table('transactionhistory')->select()->where('user_id',1)->get();

		
		return view('vendor.admin.commission')->with('commission',$commission);

	}
}
