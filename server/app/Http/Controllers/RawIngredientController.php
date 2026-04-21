<?php

namespace App\Http\Controllers;

use App\Models\RawIngredient as CurrentModel;
use App\Http\Requests\StoreRawIngredientRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateRawIngredientRequest as UpdateCurrentModelRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class RawIngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->apiResponse(
            function () {
                return CurrentModel::all();
            }
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCurrentModelRequest $request)
    {
        return $this->apiResponse(
            function () use ($request) {
                return CurrentModel::create($request->validated());
            }
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            return CurrentModel::findOrFail($id);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCurrentModelRequest $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            $row = CurrentModel::findOrFail($id);
            $row->update($request->validated());
            return $row;
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            $row = CurrentModel::findOrFail($id);

            if ($row->ingredients()->exists()) {
                return [
                    'restricted' => true,
                    'message' => 'Ezt az elemet nem tudod törölni.'
                ];
            }

            $row->delete();
            return ['id' => $id];
        });
    }
}
