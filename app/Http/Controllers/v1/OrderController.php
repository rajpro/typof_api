<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
	public function __construct()
	{
		$this->middleware('store');
	}

	public function index(Request $request)
	{
		$data['status'] = true;
		$orders = $request->store->orders()->where('status', '!=', 'pending')->get();
		if(!empty($orders)){
			$data['data'] = $orders;
		}else{
			$data['status'] = false;
			$data['error'] = "Data Not Found";
		}
		return response()->json($data);
	}
}