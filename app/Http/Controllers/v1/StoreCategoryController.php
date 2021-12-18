<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\StoreCategory;

class StoreCategoryController extends Controller
{
	public function __construct()
	{
		$this->middleware('store');
	}

	public function index(Request $request, $id='')
	{
		$data['status'] = true;
		if(!empty($id)){
			$store_category = $request->store->store_category()->where('id', $id)->orderBy('id', 'desc')->get()->append('mediacollection');
		}else{
			$store_category = $request->store->store_category()->orderBy('id', 'desc')->get()->append('mediacollection');
		}
		
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
			"category_name" => 'required',
		]);

		if($validator->fails()){
			$response['status'] = false;
			$response['error'] = $validator->errors()->first();
			return response()->json($response);
		}
		$data['store_id'] = $request->store->store_id;
        $data['slug'] = $this->__slug($request->store, $data['category_name']);
        unset($data['sub_category']);
        // $data['sub_category'] = (!empty($data['sub_category']))?implode(",", $data['sub_category']):'';
        $saveProduct=new StoreCategory($data);
		if(!$saveProduct->save()){
			$response['status'] = false;
			$response['error'] = "Failed";
		}

		if(!empty($data['image'])){
			$saveProduct->addMedia($data['image'])->toMediaCollection('category');
		}

		return response()->json($response);
	}

	public function update(Request $request, $id)
	{
		$data = $request->all();
		$response['status'] = true;
		$validator = Validator::make($request->all(), [
			"category_name" => 'required',
		]);

		if($validator->fails()){
			$response['status'] = false;
			$response['error'] = $validator->errors()->first();
			return response()->json($response);
		}

        $data['slug'] = $this->__slug($request->store, $data['product_name']);
        $updateProduct = StoreCategory::where('store_id', $request->store->store_id)->find($id);
		if(!$updateProduct->update($data)){
			$response['status'] = false;
			$response['error'] = "Subcategory Not Update";
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

	private function __slug($store, $category_name)
    {
        $slug=strtolower(str_replace(' ', '-', $category_name));
        $slug=strtolower(str_replace('/', '-', $slug));
        $pslug= StoreCategory::where('category_name',$category_name)->where('store_id', $store->store_id)->count();
        if($pslug>0){
            return $slug.'-'.(string)($pslug+1);
        }else{
            return $slug;
        }
    }
}