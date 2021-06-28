<?php

namespace Component\Movie;

use App;

/**
* Movie 관련 
*
*/
class Movie 
{
	public function getMenus()
	{
		$board = App::load(\Component\Board\Board::class);
		$conf = $board->getBoard("movie");
		debug($conf);
	}
}