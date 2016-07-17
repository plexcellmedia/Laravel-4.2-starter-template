<?php

class MainController extends BaseController {

	public function index(){

		$vars = (object)null;
		$vars->title = 'Index';

		return View::make('general.index', $this->tplVars($vars));
	}

}
