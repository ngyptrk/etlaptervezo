<?php

namespace App\Http\Controllers;

use App\Models\Recipe as CurrentModel;
use App\Http\Requests\StoreRecipeRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateRecipeRequest as UpdateCurrentModelRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecipeController extends Controller
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
                $data = $request->validated();

                if ($request->hasFile('picture')) {
                    $file = $request->file('picture');
                    $targetDir = public_path('Pictures');
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }
                    $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeName = Str::slug($baseName);
                    if ($safeName === '') {
                        $safeName = 'recipe';
                    }
                    $fileName = $safeName . '-' . Str::random(8) . '.png';
                    $file->move($targetDir, $fileName);
                    $data['picture'] = 'Pictures/' . $fileName;
                }

                return CurrentModel::create($data);
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
            $data = $request->validated();

            if ($request->hasFile('picture')) {
                $file = $request->file('picture');
                $targetDir = public_path('Pictures');
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeName = Str::slug($baseName);
                if ($safeName === '') {
                    $safeName = 'recipe';
                }
                $fileName = $safeName . '-' . Str::random(8) . '.png';
                $file->move($targetDir, $fileName);
                $data['picture'] = 'Pictures/' . $fileName;
            } else {
                unset($data['picture']);
            }

            $row->update($data);
            return $row;
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            DB::transaction(function () use ($id) {
                $recipe = CurrentModel::findOrFail($id);
                $recipe->ingredients()->delete();
                $recipe->delete();
            });

            return ['id' => $id];
        });
    }
}
