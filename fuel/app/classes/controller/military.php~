<?php
use \Model\Flights;

class Controller_Military extends Controller_Template
{

	public function action_index()
	{
        $military = Flights::getMilitary();
        return Response::forge(View::forge('front/military', array('data' => $military)));
		// $data["subnav"] = array('military'=> 'active' );
		// $this->template->title = 'Military &raquo; Military';
		// $this->template->content = View::forge('military/military', $data);
	}

}
