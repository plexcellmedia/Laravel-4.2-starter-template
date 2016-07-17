<?php

class MembersController extends BaseController {

	public function members(){

		$vars = (object) null;
		$vars->title = 'User panel';

		return View::make('members.main', $this->tplVars($vars));
	}

	public function logout(){
		$vars = (object) null;
		$vars->title = 'Logout';

		Auth::user()->logout();
		
		return View::make('members.logout', $this->tplVars($vars));
	}

	public function showChangePassword(){

		$vars = (object) null;
		$vars->title = 'Change password';

		return View::make('members.password', $this->tplVars($vars));
	}

	public function doChangePassword(){
		if(Input::get()){
			$input = Input::all();
			$rules = array(
				'new_password'         => 'required|max:64',
				'new_password_confirm' => 'same:new_password',
				'password'             => 'required|max:64'
			);
			$validator = Validator::make($input, $rules);
			if($validator->fails()){
				return Redirect::to(URL::to('/users/password'))->withErrors($validator->errors())->withInput();
			}

			$current_password = Input::get('password');
			$user = DB::table('users')->where('id', '=', Auth::user()->get()->id)->first();
			if (Hash::check($current_password, $user->password)){
				$new_password = Hash::make(Input::get('new_password'));
				DB::table('users')
				->where('id', '=', Auth::user()->get()->id)
				->update(
					array(
						'password' => $new_password
					)
				);

				return Redirect::to(URL::to('/users/password'))->with('message', 'Password changed!');
			}else{
				return Redirect::to(URL::to('/users/password'))->with('error', 'Invalid current password');
			}

		}
		return Redirect::to(URL::to('/users/password'));
	}
}