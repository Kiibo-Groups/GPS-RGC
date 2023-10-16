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
		 
	}
}
