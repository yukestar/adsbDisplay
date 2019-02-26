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
class Controller_Misc extends Controller
{
	/**
	 * The basic welcome message
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
        $flights = Flights::getPlanesByLatLong('RJTT');
        print '<pre>';
        print_r($flights); 
        print '</pre>';
        exit;
       
		return Response::forge(View::forge('front/index', array('flights' => $flights, 'dest' => $dest, 'destlist' => $allDest)));
	}

    public function action_military() {
        // return Response::forge(Presenter::forge('military/military'));
        return Response::forge(View::forge('front/military'));
    }
}
