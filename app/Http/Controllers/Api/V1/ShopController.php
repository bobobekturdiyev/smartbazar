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
        $shops = Shop::all();

        $resources = ShopResource::collection($shops);

        return response()->json($resources->resolve());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShopCreateRequest $request)
    {
        $model = Shop::create($request->validated());

        $resource = new ShopResource($model);

        return response()->json($resource->resolve());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shop = Shop::find($id);

        if(!$shop){
            return response()->json(['message' => 'Shop is not found'], 404);
        }

        $resource = new ShopResource($shop);

        return response()->json($resource->resolve());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShopUpdateRequest $request, string $id)
    {
        $shop = Shop::find($id);

        if(!$shop){
            return response()->json(['message' => 'Shop is not found'], 404);
        }

        $shop->update($request->all());

        $resource = new ShopResource($shop);

        return response()->json($resource->resolve());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shop = Shop::find($id);

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

        $rules = [
            'merchant_id' => [
                Rule::exists('merchants', 'id')
            ],
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $response = response()->json(['errors' => $validator->errors()], 422);
            throw new HttpResponseException($response);
        }

        Shop::where("merchant_id", $request->merchant_id)->delete();

        return response()->json(['message' => 'All shops are deleted'], 200);
    }

    /**
     * Get all shops by merchant id
     */
    public function getAllShops(Request $request){

        $rules = [
            'merchant_id' => [
                Rule::exists('merchants', 'id')
            ],
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $response = response()->json(['errors' => $validator->errors()], 422);
            throw new HttpResponseException($response);
        }

        $shops = Shop::where("merchant_id", $request->merchant_id)->get();

        return response()->json( ShopResource::collection($shops)->resolve());
    }

    /**
     * Get all shops by merchant id
     */
    public function getNearestShop(Request $request){

        $rules = [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'merchant_id' => [
                Rule::exists('merchants', 'id')
            ],
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $response = response()->json(['errors' => $validator->errors()], 422);
            throw new HttpResponseException($response);
        }

        $shops = Shop::where("merchant_id", $request->merchant_id)->get();

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        $shops->each(function ($shop) use ($latitude, $longitude) {
            $shop->distance = $this->calculateDistance($latitude, $longitude, $shop->latitude, $shop->longitude);
        });


        return response()->json(ShopResource::collection($shops->sortBy('distance'))->resolve());
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
}
