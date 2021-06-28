<?php

namespace Controller\Front\Board;

use App;

class IndexController extends \Controller\Front\Controller
{
	public function __construct() 
	{
		$this->addCss(["board"]);
	}
	
	public function index()
	{
		App::render("Board/main");
	}
}