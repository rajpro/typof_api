<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\StoreCategory;

class StoreCategoryController extends Controller
{
	public function __construct()
	{
		$this->middleware('store');
	}

	public function index(Request $request)
	{
		$data['status'] = true;
		$store_category = $request->store->store_category()->get()->append('mediacollection');
		if(!empty($store_category)){
			$data['data'] = $store_category;
		}else{
			$data['status'] = false;
			$data['error'] = "Data Not Found";
		}
		return response()->json($data);
	}

	public function create(Request $request)
	{
		$data = $request->all();
		$response['status'] = true;
		$validator = Validator::make($request->all(), [
			"product_name" => 'required',
			"category" => 'required',
			"price" => 'required|numeric',
			"mrp" => 'numeric',
			"cost" => 'numeric',
			"gstper" => 'numeric',
			"shipping_cost" => 'numeric',
			"available" => 'required|numeric',
			"sku" => 'required|max:15',
			"published_status" => 'required|in:P,D',
			"is_saleable" => 'required|in:yes,no'
		]);

		if($validator->fails()){
			$response['status'] = false;
			$response['error'] = $validator->errors()->first();
			return response()->json($response);
		}
		$data['store_id'] = $request->store->store_id;
        $data['slug'] = $this->__slug($request->store, $data['product_name']);
        $cat = explode("~", $data['category']);
        $data['category'] = $cat[0];
        $data['sub_category'] = $cat[1];
        $saveProduct=new StoreCategory($data);
		if($saveProduct->save()){
			if($data['gstper'] !=''){
			    $saveProduct->setting()->create(['type'=>'gst', 'data'=>['percent'=>$data['gstper']]]);
			}else{
			    $saveProduct->setting()->create(['type'=>'gst', 'data'=>['percent'=>0]]);
			}
		}else{
			$response['status'] = false;
			$response['error'] = "Failed";
		}

		return response()->json($response);
	}

	public function update(Request $request, $id)
	{
		$data = $request->all();
		$response['status'] = true;
		$validator = Validator::make($request->all(), [
			"product_name" => 'required',
			"category" => 'required',
			"price" => 'required|numeric',
			"mrp" => 'numeric',
			"cost" => 'numeric',
			"gstper" => 'numeric',
			"shipping_cost" => 'numeric',
			"available" => 'required|numeric',
			"sku" => 'required|max:15',
			"published_status" => 'required|in:P,D',
			"is_saleable" => 'required|in:yes,no'
		]);

		if($validator->fails()){
			$response['status'] = false;
			$response['error'] = $validator->errors()->first();
			return response()->json($response);
		}

        $data['slug'] = $this->__slug($request->store, $data['product_name']);
        $cat = explode("~", $data['category']);
        $data['category'] = $cat[0];
        $data['sub_category'] = $cat[1];
        $updateProduct = StoreCategory::where('store_id', $request->store->store_id)->find($id);
		if($updateProduct->update($data)){
			
		}else{
			$response['status'] = false;
			$response['error'] = "Product Not Update";
		}

		return response()->json($response);
	}

	public function delete($id)
	{
		$data['status'] = true;
		$product = StoreCategory::where('id',$id)->first();
		$media = $product->getMedia('category')->first();
        if(!empty($media)){
            $media->delete();
        }
        $product->delete();
		return response()->json($response);
	}

	private function __slug($store, $product_name)
    {
        $slug=strtolower(str_replace(' ', '-', $product_name));
        $slug=strtolower(str_replace('/', '-', $slug));
        $pslug= Product::where('product_name',$product_name)->where('store_id', $store->store_id)->count();
        if($pslug>0){
            return $slug.'-'.(string)($pslug+1);
        }else{
            return $slug;
        }
    }
}