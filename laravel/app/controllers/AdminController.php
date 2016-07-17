<?php

class AdminController extends BaseController {

	public function index(){
		if(Auth::admin()->check()){
			return Redirect::to(URL::to('/admin/dashboard'));
		}else{
			return Redirect::to(URL::to('/admin/login'));
		}
	}

	public function showLogin(){
		$vars = (object) null;
		$vars->title = 'Admin Login';

		$ip                = Request::getClientIp();
		$fails             = DB::table('users_block')->where('ip', '=', $ip)->where('tries', '>=', '5');
		$showCaptcha       = $fails->count() > 0 ? true : false;
		$vars->showCaptcha = $showCaptcha;

		return View::make('admin.login', $this->tplVars($vars));
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
				return Redirect::to(URL::to('/admin/login'))->withErrors($validator->messages())->withInput();
			}

			$email    = Input::get('email');
			$password = Input::get('password');

			$parameters = array(
				'email'    => $email,
				'password' => $password
			);

			if(Auth::admin()->attempt($parameters)){
				DB::table('users_block')->where('ip', '=', $ip)->delete();
				return Redirect::to(URL::to('/admin/dashboard'))->with('message', 'Logged in successfully');
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
				return Redirect::to(URL::to('/admin/login'))->with('error', 'Invalid email or password');
			}
		}

		return Redirect::to(URL::to('/admin/login'));
	}

	public function showDashboard(){
		$vars = (object) null;
		$vars->title = "Dashboard";

		return View::make('admin.dashboard', $this->tplVars($vars));
	}

	public function doLogout(){
		$vars = (object) null;
		$vars->title = "Logout";

		Auth::admin()->logout();

		return View::make('admin.logout', $this->tplVars($vars));
	}

}
