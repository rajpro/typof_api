<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Product;

class ProductController extends Controller
{
	public function __construct()
	{
		$this->middleware('store');
	}

	public function index(Request $request, $pid = '')
	{
		$data['status'] = true;
		if(!empty($pid)){
			$products = $request->store->products()->where('product_id', $pid)->orderBy('product_id', 'desc')->whereNull('variants')->get()->append(['mediacollection']);
		}else{
			$products = $request->store->products()->orderBy('product_id', 'desc')->whereNull('variants')->get()->append(['mediacollection']);
		}
		if(!empty($products)){
			$data['data'] = $products;
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
			"price" => 'required|regex:/^\d+(\.\d{1,2})?$/',
			"mrp" => 'regex:/^\d+(\.\d{1,2})?$/',
			"cost" => 'regex:/^\d+(\.\d{1,2})?$/',
			"gstper" => 'numeric',
			"shipping_cost" => 'regex:/^\d+(\.\d{1,2})?$/',
			"available" => 'required|numeric',
			"sku" => 'required|max:15',
			"published_status" => 'required|in:P,D',
			"is_saleable" => 'required|in:yes,no'
		]);

		if($validator->fails()){
			$response['status'] = false;
			$response['error'] = $validator->errors()->first();
			// return response()->json($validator->errors());
			return response()->json($response);
		}
		$data['store_id'] = $request->store->store_id;
        $data['slug'] = $this->__slug($request->store, $data['product_name']);
        $data['custom_fields'] = [
        	"gst" => $data['gstper']
        ];
        
        $saveProduct=new Product($data);
		if($saveProduct->save()){
			if($data['gstper'] !=''){
			    $saveProduct->setting()->create(['type'=>'gst', 'data'=>['percent'=>$data['gstper']]]);
			}else{
			    $saveProduct->setting()->create(['type'=>'gst', 'data'=>['percent'=>0]]);
			}
			if(!empty($data['image'])){
				$saveProduct->addMedia($data['image'])->toMediaCollection('products');
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
			"price" => 'required|regex:/^\d+(\.\d{1,2})?$/',
			"mrp" => 'regex:/^\d+(\.\d{1,2})?$/',
			"cost" => 'regex:/^\d+(\.\d{1,2})?$/',
			"gstper" => 'numeric',
			"shipping_cost" => 'regex:/^\d+(\.\d{1,2})?$/',
			"available" => 'required|numeric',
			"sku" => 'required|max:15',
			"published_status" => 'required|in:P,D',
			"is_saleable" => 'required|in:yes,no'
		]);

		if($validator->fails()){
			$response['status'] = false;
			$response['error'] = $validator->errors()->first();
			// return response()->json($validator->errors());
			return response()->json($response);
		}

        $updateProduct = Product::where('store_id', $request->store->store_id)->find($id);

		if($updateProduct->update($data)){
			if(!empty($data['gstper'])){
				$setting = $updateProduct->setting('type', 'gst')->first();
				$d = $setting->data;
				$d['percent'] = $data['gstper'];
			    $setting->update(['data'=>$d]);
			}
		}else{
			$response['status'] = false;
			$response['error'] = "Product Not Update";
		}

		return response()->json($response);
	}

	public function delete($id)
	{
		$data['status'] = true;
		$product = Product::where('product_id', $id)->first();
		// $media = $product->getMedia('products')->first();
  //       if(!empty($media)){
  //           $media->delete();
  //       }
        $product->delete();
		return response()->json($response);
	}

	private function __slug($store, $product_name)
    {
        $slug = preg_replace("/[`!@#$%^&*()_+\=\[\]{};':\"\\|,.<>\/?~\s]/", "-", $product_name);
        $slug = preg_replace("/([-]+)/", "-", $slug);
        $slug = preg_replace("/^([-]+)/", "", $slug);
        $slug = preg_replace("/([-]+)$/", "", $slug);
        $slug = strtolower($slug);
        $pslug= Product::where('store_id', $store->store_id)->where('slug',$slug)->count();
        if($pslug>0){
            return $slug.'-'.(string)($pslug+1);
        }else{
            return $slug;
        }
    }
}