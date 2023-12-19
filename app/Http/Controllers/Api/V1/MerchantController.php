<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Merchant\StoreMerchantRequest;
use App\Http\Requests\Api\V1\Merchant\UpdateMerchantRequest;
use App\Models\Merchant;

class MerchantController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMerchantRequest $request)
    {
        $model = Merchant::create($request->validated());

        return response()->json($model, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        $merchant = Merchant::find($id);

        if(!$merchant){
            return response()->json(['message' => 'Merchant is not found'], 404);
        }

        return response()->json($merchant, 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMerchantRequest $request, $id)
    {
        $merchant = Merchant::find($id);

        if(!$merchant){
            return response()->json(['message' => 'Merchant is not found'], 404);
        }
        $merchant->update($request->all());

        return response()->json($merchant, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $merchant = Merchant::find($id);

        if(!$merchant){
            return response()->json(['message' => 'Merchant is not found'], 404);
        }
        $merchant->delete();

        return response()->json(['message' => 'Merchant is deleted'], 200);
    }
}
