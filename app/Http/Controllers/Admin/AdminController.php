<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;


use App\Models\User;

use Auth;
use DB;
use Validator;
use Redirect;
class AdminController extends Controller
{
	public $folder = "admin.";

    /*
	|------------------------------------------------------------------
	|Index page for login
	|------------------------------------------------------------------
	*/
	public function index()
	{
		return View($this->folder.'dashboard.home');
	}

	/*
	|------------------------------------------------------------------
	|Homepage, Dashboard
	|------------------------------------------------------------------
	*/
	public function home()
	{
		return View($this->folder.'dashboard.home');
	}

	public function conexiones()
	{
		return View($this->folder.'dashboard.home');
	}


	/*
	|------------------------------------------------------------------
	|Logout
	|------------------------------------------------------------------
	*/
	public function logout()
	{
		auth()->guard()->logout();

		return Redirect::to('/login')->with('message', 'Ha cerrado sesión con éxito !');
	}
}
