<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\{User, Getgsminfo};

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function getGSMInfo(Request $request)
	{
		$data = $request->getContent();
		$info = new Getgsminfo;

		$info->log = $data;
		$info->save();

		return [
			'status' => 200,
			'data' => $data
		];
	}
}
