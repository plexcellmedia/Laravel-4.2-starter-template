<?php

class AuthController extends BaseController {

	/**
	 * Auth mailer sender and name
	 */
	private $authEmailAddress = 'noreply@laravel.com';
	private $authEmailName    = 'Laravel';

	public function showLogin(){
		$vars = (object) null;
		$vars->title = 'Login';

		$ip                = Request::getClientIp();
		$fails             = DB::table('users_block')->where('ip', '=', $ip)->where('tries', '>=', '5');
		$showCaptcha       = $fails->count() > 0 ? true : false;
		$vars->showCaptcha = $showCaptcha;

		return View::make('auth.login', $this->tplVars($vars));
 	}

	public function doLogin(){
		if(Input::get()){
			
			$ip                = Request::getClientIp();
			$fails             = DB::table('users_block')->where('ip', '=', $ip)->where('tries', '>=', '5');
			$showCaptcha       = $fails->count() > 0 ? true : false;

			$input = Input::all();

			$rules = array(
				'email'    => 'required|max:64|email',
				'password' => 'required|max:64'
			);

			$messages = array();

			if($showCaptcha){
				$rules['g-recaptcha-response']             = 'required|captcha';
				$messages['g-recaptcha-response.required'] = 'Make sure you are not a robot!';
				$messages['g-recaptcha-response.captcha']  = 'Invalid captcha';
			}

			$validator = Validator::make($input, $rules, $messages);
			if($validator->fails()){
				return Redirect::to(URL::to('/login'))->withErrors($validator->messages())->withInput();
			}

			$email    = Input::get('email');
			$password = Input::get('password');

			$parameters = array(
				'email'    => $email,
				'password' => $password
			);

			if(Auth::user()->attempt($parameters)){
				DB::table('users_block')->where('ip', '=', $ip)->delete();
				return Redirect::to(URL::to('/users'))->with('message', 'Logged in successfully');
			}else{
				$results = DB::table('users_block')->where('ip', '=', $ip);
				if($results->count() <= 0){
					DB::table('users_block')->insert(
						array(
							'ip'    => $ip,
							'tries' => 1
						)
					);
				}else{
					DB::table('users_block')->where('ip', '=', $ip)->increment('tries');
				}
				return Redirect::to(URL::to('/login'))->with('error', 'Invalid email or password');
			}
		}

		return Redirect::to(URL::to('/login'));
	}

	public function showRegister(){
		$vars = (object) null;
		$vars->title = 'Register';
		return View::make('auth.register', $this->tplVars($vars));
	}

	public function doRegister(){
		if(Input::get()){

			$input = Input::all();
			$rules = array(
				'email'            => 'required|max:64|email|unique:users,email',
				'password'         => 'required|max:32',
				'password_confirm' => 'same:password',
				'g-recaptcha-response' => 'required|captcha'
			);

			$messages = array(
				'g-recaptcha-response.required' => 'Make sure you are not a robot!',
				'g-recaptcha-response.captcha'  => 'Invalid captcha'
			);

			$validator = Validator::make($input, $rules, $messages);
			if($validator->fails()){
				return Redirect::to(URL::to('/register'))->withErrors($validator->errors())->withInput();
			}else{

				$email      = Input::get('email');
				$password   = Input::get('password');
				$password   = Hash::make($password);
				$key        = str_random(64);

				DB::table('users_verify')
				->insert(
					array(
						'email'      => $email,
						'password'   => $password,
						'key'        => $key,
						'ip'         => Request::getClientIp()
					)
				);

				$activate_url                 = URL::to('/register/verify?email=' . $email . '&key=' . $key);
				$parameters                   = (object) null;
				$parameters->email            = $email;
				$parameters->authEmailAddress = $this->authEmailAddress;
				$parameters->authEmailName    = $this->authEmailName;

				Mail::later(5, 'emails.welcome', array('activate_url' => $activate_url), function($message) use($parameters)
				{
					$message->from($parameters->authEmailAddress, $parameters->authEmailName);
					$message->to($parameters->email, $parameters->email)->subject('Activate your account');
				});
				return Redirect::to(URL::to('/register/confirm'));
			}
		}
		return Redirect::to(URL::to('/register'));
	}

	public function showRegisterConfirm(){
		$vars = (object) null;
		$vars->title = 'Activate account';
		return View::make('auth.registerConfirm', $this->tplVars($vars));
	}

	public function doRegisterVerify(){
		$vars = (object) null;
		$vars->title = 'Activate account';

		$error         = true;
		$error_message = 'Invalid activation link';
		$success       = false;
		if(Input::get()){
			$input = Input::all();
			$rules = array(
				'email'	=> 'required',
				'key'	=> 'required'
			);
			$validator = Validator::make($input, $rules);
			if(!$validator->fails()){

				$email = Input::get('email');
				$key   = Input::get('key');

				$results = DB::table('users_verify')->where('email', '=', $email)->where('key', '=', $key);
				if($results->count() > 0){
					$user = $results->first();
					$results = DB::table('users')->where('email', '=', $email);
					if($results->count() > 0){
						$error_message = 'This email address is already registered to somebody else';
					}else{
						DB::transaction(function() use ($key, $user)
						{
							DB::table('users')->insertGetId(
								array(
									'password'   => $user->password,
									'email'      => $user->email,
									'created_at' => $user->created_at,
									'ip'         => $user->ip
								)
							);
							DB::table('users_verify')->where('key', '=', $key)->delete();
						});
						$error   = false;
						$success = true;
					}
				}
			}
		}

		$vars->error         = $error;
		$vars->error_message = $error_message;
		$vars->success       = $success;

		return View::make('auth.activate', $this->tplVars($vars));
	}

	public function showForgot(){
		$vars = (object) null;
		$vars->title = 'Recover account';
		return View::make('auth.forgot', $this->tplVars($vars));
	}

	public function doForgot(){
		if(Input::get()){
			$input = Input::all();
			$rules = array(
				'email'                => 'required|max:64|email',
				'g-recaptcha-response' => 'required|captcha'
			);
			$messages = array(
				'g-recaptcha-response.required' => 'Make sure you are not a robot!',
				'g-recaptcha-response.captcha'  => 'Invalid captcha'
			);
			$validator = Validator::make($input, $rules, $messages);
			if($validator->fails()){
				return Redirect::to(URL::to('/forgot'))->withErrors($validator->errors())->withInput();
			}else{
				$email   = Input::get('email');
				$results = DB::table('users')->where('email', '=', $email);
				if($results->count() <= 0){
					return Redirect::to(URL::to('/forgot'))->with('error', 'Sähköpostiosoitteella ei löytynyt yhtäkään tunnusta.');
				}else{
					$user = $results->first();
					$key = str_random(64);
					DB::table('users_forgot')->insert(
						array(
							'user_id'    => $user->id,
							'key'        => $key,
							'created_at' => Carbon\Carbon::now()->addDay()
						)
					);
					$reset_url = URL::to('/forgot/reset?email=' . $user->email . '&key=' . $key);

					$parameters = (object) null;
					$parameters->user = $user;
					$parameters->authEmailAddress = $this->authEmailAddress;
					$parameters->authEmailName    = $this->authEmailName;

					Mail::later(5, 'emails.reset', array('reset_url' => $reset_url), function($message) use($parameters)
					{
						$message->from($parameters->authEmailAddress, $parameters->authEmailName);
						$message->to($parameters->user->email, $parameters->user->email)->subject('Recover account');
					});
					return Redirect::to(URL::to('/forgot/confirm'));
				}
			}
		}
		return Redirect::to(URL::to('/forgot'));
	}

	public function showForgotConfirm(){
		$vars = (object) null;
		$vars->title = 'Recover account';
		return View::make('auth.forgotConfirm', $this->tplVars($vars));
	}

	public function doForgotReset(){
		$vars = (object) null;
		$vars->title = 'Recover account';

		$error         = true;
		$error_message = 'Invalid account recovery link';
		$success       = false;

		if(Input::get()){
			$input = Input::all();
			$rules = array(
				'email'	=> 'required',
				'key'	=> 'required'
			);

			$validator = Validator::make($input, $rules);
			if(!$validator->fails()){

				$email = Input::get('email');
				$key   = Input::get('key');

				$results = DB::table('users_forgot as uf')
						   ->select(
						       array(
						          'uf.created_at as forgot_date'
						       )
						   )
						   ->join('users as u', 'u.id', '=', 'uf.user_id')
						   ->where('u.email', '=', $email)
						   ->where('uf.key', '=', $key);

				if($results->count() > 0){
					$forgot = $results->first();
					if(Carbon\Carbon::now() > $forgot->forgot_date){
						$error_message = 'Account recovery link is expired';
						DB::table('users_forgot')->where('key', '=', $key)->delete();
					}else{

						DB::transaction(function() use ($email, $key)
						{
							$new_password     = str_random(8);
							$new_passwordHash = Hash::make($new_password);
							DB::table('users')
							->where('email', '=', $email)
							->update(
								array(
									'password' => $new_passwordHash
								)
							);

							$parameters                   = (object) null;
							$parameters->email            = $email;
							$parameters->authEmailAddress = $this->authEmailAddress;
							$parameters->authEmailName    = $this->authEmailName;

							Mail::later(5, 'emails.newpassword', array('password' => $new_password), function($message) use($parameters)
							{
								$message->from($parameters->authEmailAddress, $parameters->authEmailName);
								$message->to($parameters->email, $parameters->email)->subject('New password');
							});

							DB::table('users_forgot')->where('key', '=', $key)->delete();
						});

						$error   = false;
						$success = true;
					}
				}
			}
		}

		$vars->error         = $error;
		$vars->error_message = $error_message;
		$vars->success       = $success;

		return View::make('auth.reset', $this->tplVars($vars));
	}

}