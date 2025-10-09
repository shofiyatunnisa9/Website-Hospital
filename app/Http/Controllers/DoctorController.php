<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorRequest;
use App\Http\Requests\SpecialistHospitalDoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Services\DoctorService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    private $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    public function index()
    {
        $fields = ['id', 'name', 'photo', 'gender', 'yoe', 'specialist_id', 'hospital_id'];
        $doctors = $this->doctorService->getAll($fields);
        return response()->json(DoctorResource::collection($doctors));
    }


    public function show(int $id)
    {
        try {
            $fields = ['*'];
            $doctor = $this->doctorService->getById($id, $fields);
            return response()->json(new DoctorResource($doctor));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Doctor not found',

            ], 404);
        }
    }

    public function store(DoctorRequest $request)
    {
        $doctor = $this->doctorService->create($request->validated());
        return response()->json(new DoctorResource($doctor), 201);
    }

    public function update(DoctorRequest $request, int $id)
    {
        try {
            $doctor = $this->doctorService->update($id, $request->validated());
            return response()->json(new DoctorResource($doctor));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Doctor not found',
            ], 404);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->doctorService->delete($id);
            return response()->json([
                'message' => 'Doctor deleted succesfuly',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Doctor not found',
            ], 404);
        }
    }

    public function filterBySpecialistAndHospital(SpecialistHospitalDoctorRequest $request) //bisa dipisah
    {

        $validated = $request->validated();

        $doctors = $this->doctorService->filterBySpecialistAndHospital(
            $validated['hospital_id'],
            $validated['specialist_id']
        );
        return DoctorResource::collection($doctors);
    }

    public function availableSlots(int $doctorId)
    {
        try {
            $availability = $this->doctorService->getAvailableSlots($doctorId);
            return response()->json(new DoctorResource($availability));
        } catch (ModelNotFoundException $e) {

            return response()->json(['message' => 'Doctor not found'], 404);
        }
    }
}
