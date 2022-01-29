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

	public function find(Request $request)
	{
		$data['status'] = true;
		$store_category = $request->store->store_category()->where('category_name', $request->category_name)->orderBy('id', 'desc')->get()->append('mediacollection');
		
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
        // if(!empty($data['sub_category'])){
        // 	$data['sub_category'] = implode(",", $data['sub_category']);
        // }else{
        // 	unset($data['sub_category']);
        // }
        $data['sub_category'] = $data['sub_category'] ?? null;
        

        $saveProduct = new StoreCategory($data);
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

        $updateProduct = StoreCategory::where('store_id', $request->store->store_id)->find($id);
        if(!empty($data['sub_category'])){
			$data['sub_category'] = implode(",", $data['sub_category']);
		}else{
			unset($data['sub_category']);
		}
		
		if(!$updateProduct->update($data)){
			$response['status'] = false;
			$response['error'] = "Subcategory Not Update";
		}

		return response()->json($response);
	}

	public function delete($id)
	{
		$data['status'] = true;
		$product = StoreCategory::where('id', $id)->first();
		// $media = $product->getMedia('category')->first();
  //       if(!empty($media)){
  //           $media->delete();
  //       }
        $product->delete();
		return response()->json($response);
	}

	private function __slug($store, $category_name)
    {
        $slug = preg_replace("/[`!@#$%^&*()_+\=\[\]{};':\"\\|,.<>\/?~\s]/", "-", $category_name);
        $slug = preg_replace("/([-]+)/", "-", $slug);
        $slug = preg_replace("/^([-]+)/", "", $slug);
        $slug = preg_replace("/([-]+)$/", "", $slug);
        $slug = strtolower($slug);
        $pslug= StoreCategory::where('category_name',$category_name)->where('store_id', $store->store_id)->count();
        if($pslug>0){
            return $slug.'-'.(string)($pslug+1);
        }else{
            return $slug;
        }
    }
}