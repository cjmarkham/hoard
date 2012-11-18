<?php

class HomeController extends PageController
{
	
	public function before ()
	{
		
		if (Auth::$id)
		{
			header('Location: /apps/');
			exit;
		}
		else
		{
			header('Location: /login/');
			exit;
		}

		$this->set('title', 'Hoard - Event Tracking System');
		
	}
	
}