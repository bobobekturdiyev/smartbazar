<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Shop\ShopCreateRequest;
use App\Http\Requests\Api\V1\Shop\ShopUpdateRequest;
use App\Http\Resources\Api\V1\ShopResource;
use App\Models\Shop;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $merchantId = $this->getMerchantId();

        $shops = Shop::where('merchant_id', $merchantId)->get();

        $resources = ShopResource::collection($shops);

        return response()->json($resources->resolve());
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(ShopCreateRequest $request)
    {
        $merchantId = $this->getMerchantId();

        $data = array_merge($request->validated(), ['merchant_id' => $merchantId]);

        $model = Shop::create($data);

        $resource = new ShopResource($model);

        return response()->json($resource->resolve());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $merchantId = $this->getMerchantId();

        $shop = Shop::where('id', $id)->where('merchant_id', $merchantId)->first();

        if(!$shop){
            return response()->json(['message' => 'Shop is not found'], 404);
        }

        $resource = new ShopResource($shop);

        return response()->json($resource->resolve());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShopUpdateRequest $request, string $id)
    {
        $merchantId = $this->getMerchantId();

        $shop = Shop::where('id', $id)->where('merchant_id', $merchantId)->first();

        if(!$shop){
            return response()->json(['message' => 'Shop is not found'], 404);
        }

        $data = $request->all();
        unset($data['merchant_id']);

        $shop->update($data);

        $resource = new ShopResource($shop);

        return response()->json($resource->resolve());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $merchantId = $this->getMerchantId();

        $shop = Shop::where('id', $id)->where('merchant_id', $merchantId)->first();

        if(!$shop){
            return response()->json(['message' => 'Shop is not found'], 404);
        }

        $shop->delete();

        return response()->json(['message' => 'Shop is deleted'], 200);
    }

    /**
     * Remove all shops by merchant id
     */
    public function deleteAllShops(Request $request){

        $merchantId = $this->getMerchantId();

        Shop::where('merchant_id', $merchantId)->delete();

        return response()->json(['message' => 'All shops are deleted'], 200);
    }

    /**
     * Get all shops by merchant id
     */
    public function getAllShops(Request $request){

         $merchantId = $this->getMerchantId();

        $shops = Shop::where("merchant_id", $merchantId)->get();

        return response()->json( ShopResource::collection($shops)->resolve());
    }

    /**
     * Get all shops by merchant id
     */
    public function getNearestShop(Request $request){

        $merchantId = $this->getMerchantId();

        $rules = [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        $this->validatorResponse($validator);

        $shops = Shop::where("merchant_id", $merchantId)->get();

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        $shops->each(function ($shop) use ($latitude, $longitude) {
            $shop->distance = $this->calculateDistance($latitude, $longitude, $shop->latitude, $shop->longitude);
        });


        return response()->json(ShopResource::collection($shops->sortBy('distance'))->resolve());
    }

    private function validatorResponse(\Illuminate\Validation\Validator $validator){
        if($validator->fails()){
            $response = response()->json(['errors' => $validator->errors()], 422);
            throw new HttpResponseException($response);
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in kilometers
    }

    private function getMerchantId(){
        $merchant = auth()->user()->merchant;

        if(!$merchant){
            $response = response()->json(['message' => 'You have no any merchant associated yet'], 404);
            throw new HttpResponseException($response);
        }

        return $merchant->id;
    }
}
