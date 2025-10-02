<?php

namespace App\Http\Controllers;

use App\Http\Requests\HospitalRequest;
use App\Http\Resources\HospitalResource;
use App\Services\HospitalService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    private $hospitalService;

    public function __construct(HospitalService $hospitalService)

    {
        $this->hospitalService = $hospitalService;
    }

    public function index()
    {
        $fields = ['id', 'name', 'photo', 'city', 'phone'];
        $hospitals = $this->hospitalService->getAll($fields);
        return response()->json(HospitalResource::collection($hospitals));
    }

    public function show(int $id)
    {
        try {
            $fields = ['*'];
            $hospital = $this->hospitalService->getById($id, $fields);
            return response()->json(new HospitalResource($hospital));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Hospital not found',

            ], 404);
        }
    }
    public function store(HospitalRequest $request)
    {
        $hospital = $this->hospitalService->create($request->validated());
        return response()->json(new HospitalResource($hospital), 201);
    }

    public function update(HospitalRequest $request, int $id)
    {
        try {
            $hospital = $this->hospitalService->update($id, $request->validated());
            return response()->json(new HospitalResource($hospital));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Hospital not found',
            ], 404);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->hospitalService->delete($id);
            return response()->json([
                'message' => 'Hospital deleted succesfuly',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Hospital not found',
            ], 404);
        }
    }
}
