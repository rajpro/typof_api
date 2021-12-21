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

	public function index(Request $request, $order_id="")
	{
		$data['status'] = true;
		if(!empty($order_id)){
			$orders = $request->store->orders()->where('order_id', $order_id)->with('customer')->where('status', '!=', 'pending')->orderBy('order_id', 'desc')->get();
		}else{
			$orders = $request->store->orders()->with('customer')->where('status', '!=', 'pending')->orderBy('order_id', 'desc')->get();
		}
		
		if(!empty($orders)){
			$data['data'] = $orders;
		}else{
			$data['status'] = false;
			$data['error'] = "Data Not Found";
		}
		return response()->json($data);
	}
}