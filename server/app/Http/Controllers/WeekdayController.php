<?php

namespace App\Http\Controllers;

use App\Models\Weekday as CurrentModel;
use App\Http\Requests\StoreWeekdayRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateWeekdayRequest as UpdateCurrentModelRequest;

class WeekdayController extends Controller
{
    public function index()
    {
        return $this->apiResponse(function () {
            return CurrentModel::all();
        });
    }

    public function store(StoreCurrentModelRequest $request)
    {
        return $this->apiResponse(function () use ($request) {
            return CurrentModel::create($request->validated());
        });
    }

    public function show(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            return CurrentModel::findOrFail($id);
        });
    }

    public function update(UpdateCurrentModelRequest $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            $row = CurrentModel::findOrFail($id);
            $row->update($request->validated());
            return $row;
        });
    }

    public function destroy(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            CurrentModel::findOrFail($id)->delete();
            return ['id' => $id];
        });
    }
}
