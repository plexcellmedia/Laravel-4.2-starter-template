<?php

class BaseController extends Controller {

	protected function tplVars($vars){

		$defaultVariables = array(
			'title' => ''
		);

		foreach($vars as $key => $value){
			$defaultVariables[$key] = $value;
		}

		return $defaultVariables;
	}

}
