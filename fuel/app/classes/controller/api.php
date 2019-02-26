<?php
use \Model\Flights;

class Controller_Api extends Controller_Rest
{

	public function action_index()
	{
        $dest = Input::get('dest', 'RJTT');
        $allDest = Flights::allDest();
        if (in_array($dest, $allDest) == false) { // エラー処理
            $dest = 'RJTT';
            // return Response::forge(View::forge('front/index'));
        }
        $flights = Flights::getFlightsFromAdsb('RJAA');
        return $this->response($flights);
        // print_r(json_encode($flights));
	}

}
