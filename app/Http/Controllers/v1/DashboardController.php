<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use App\Models\Order;

class DashboardController extends Controller
{
	public function __construct()
	{
		$this->middleware('store');
	}
	
	public function index(Request $request)
	{
		$data = ['status' => true];
		$orders = $request->store->orders()->where('status', '!=', 'pending')->with('customer')->with('product_order.orderCom')->orderBy('order_id', 'desc')->limit(10)->get();
		$data['data'] = [
			"website" => $request->store->website,
			"statistics" => [
				"today_sales" => $this->__todaySales($request->store->store_id),
				"seven_day_sales" => $this->__sevenDaySales($request->store->store_id),
				"thirty_day_sales" => $this->__thirtyDaySales($request->store->store_id),
				"all_sales" => $this->__allSales($request->store->store_id),
			],
			"orders" => $orders,
		];
		return response()->json($data);
	}

	public function get_store(Request $request)
	{
		$data = ['status' => true];
		$data['data'] = $request->store;
		return response()->json($data);
	}

	private function __todaySales($store_id)
	{
		return number_format(Order::where('status', '!=', 'pending')
			->where('store_id', $store_id)
			->whereDate('created_at', date('Y-m-d'))
			->orderBy('store_id','desc')
			->sum('total_price'), 2);
	}

	private function __sevenDaySales($store_id)
	{
		$date = Carbon::now();
		return number_format(Order::where('status', '!=', 'pending')
			->where('store_id', $store_id)
			->whereBetween(DB::raw('DATE(created_at)'), [$date->toDateString(), $date->subDays(7)->toDateString()])
			->orderBy('store_id','desc')
			->sum('total_price'), 2);
	}

	private function __thirtyDaySales($store_id)
	{
		$date = Carbon::now();
		return number_format(Order::where('status', '!=', 'pending')
			->where('store_id', $store_id)
			->whereBetween(DB::raw('DATE(created_at)'), [$date->toDateString(), $date->subDays(30)->toDateString()])
			->orderBy('store_id','desc')
			->sum('total_price'), 2);
	}

	private function __allSales($store_id)
	{
		return number_format(Order::where('status', '!=', 'pending')
			->where('store_id', $store_id)
			->orderBy('store_id','desc')
			->sum('total_price'),2);
	}
}