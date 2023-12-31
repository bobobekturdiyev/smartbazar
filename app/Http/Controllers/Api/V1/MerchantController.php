<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Merchant\StoreMerchantRequest;
use App\Http\Requests\Api\V1\Merchant\UpdateMerchantRequest;
use App\Http\Resources\Api\V1\MerchantResource;
use App\Models\Merchant;
use Illuminate\Http\Request;

class MerchantController extends Controller
{


    public function index(Request $request)
    {
        $query = Merchant::query();

        $query->where('user_id', auth()->id());

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('order_by_id')) {
            $query->orderBy('id', $request->input('order_by_id'));
        }

        $merchants = $query->get();


        $resources = MerchantResource::collection($merchants);

        return response()->json($resources->resolve());

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMerchantRequest $request)
    {
        $data = array_merge($request->validated(), ['user_id' => auth()->id()]);

        $merchant = Merchant::where("user_id", auth()->id())->first();

        if($merchant){
            return response()->json(['message' => 'You have already merchant associated'], 403);
        }

        $model = Merchant::create($data);

        $resource = new MerchantResource($model);

        return response()->json($resource->resolve());
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        $merchant = Merchant::where('id', $id)->where("user_id", auth()->id())->first();

        if(!$merchant){
            return response()->json(['message' => 'Merchant is not found'], 404);
        }

        $resource = new MerchantResource($merchant);

        return response()->json($resource->resolve());

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMerchantRequest $request, $id)
    {
        $merchant = Merchant::where('id', $id)->where("user_id", auth()->id())->first();

        if(!$merchant){
            return response()->json(['message' => 'Merchant is not found'], 404);
        }
        $merchant->update($request->all());


        $resource = new MerchantResource($merchant);

        return response()->json($resource->resolve());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $merchant = Merchant::where('id', $id)->where("user_id", auth()->id())->first();

        if(!$merchant){
            return response()->json(['message' => 'Merchant is not found'], 404);
        }
        $merchant->delete();

        return response()->json(['message' => 'Merchant is deleted'], 200);
    }
}
