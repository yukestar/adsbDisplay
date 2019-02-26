<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.8
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2016 Fuel Development Team
 * @link       http://fuelphp.com
 */

use \Model\Flights;

/**
 * The Welcome Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Welcome extends Controller
{
	/**
	 * The basic welcome message
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
        $allDest = Flights::allDest();
        
        $dest = Input::post('dest', 'RJTT');
        if (in_array($dest, $allDest) == false) { // エラー処理
            $dest = 'RJTT';
        }

        // $flights = Flights::getFlights($dest);
        $flights = Flights::getFlightsFromAdsb($dest);
        
		return Response::forge(View::forge('front/index', array('flights' => $flights['result'], 'dest' => $dest, 'destlist' => $allDest, 'misc' => $flights['misc'])));
	}

	/**
	 * The 404 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_404()
	{
		return Response::forge(Presenter::forge('welcome/404'), 404);
	}
	public function action_about()
	{
        print 'aaaa';
        return Response::forge(Presenter::forge('welcome/about'));
    }

    public function action_military() {
        // return Response::forge(Presenter::forge('military/military'));
        return Response::forge(View::forge('front/military'));
    }
}
