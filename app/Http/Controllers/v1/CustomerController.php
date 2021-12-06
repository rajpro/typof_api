<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
	public function __construct()
	{
		$this->middleware('store');
	}

	public function index(Request $request)
	{
		$data['status'] = true;
		$customer = $request->store->customers()->get()->append('ordercount');
		if(!empty($customer)){
			$data['data'] = $customer;
		}else{
			$data['status'] = false;
			$data['error'] = "Data Not Found";
		}
		return response()->json($data);
	}
}