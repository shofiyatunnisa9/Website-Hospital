<?php

namespace App\Services;

use App\Repositories\DoctorRepository;
use App\Repositories\HospitalSpecialistRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DoctorService
{
    private DoctorRepository $doctorRepository;
    private HospitalSpecialistRepository $hospitalSpecialistRepository;

    public function __construct(
        DoctorRepository $doctorRepository,
        HospitalSpecialistRepository $hospitalSpecialistRepository
    ) {
        $this->doctorRepository = $doctorRepository;
        $this->hospitalSpecialistRepository = $hospitalSpecialistRepository;
    }

    public function getAll(array $fields)
    {
        return $this->doctorRepository->getAll($fields);
    }

    public function getById(int $id, array $fields)
    {
        return $this->doctorRepository->getById($id, $fields);
    }

    public function create(array $data)
    {
        if (!$this->hospitalSpecialistRepository->existsForHospitalAndSpecialist($data['hospital_id'], $data['specialist_id'])) {
            throw ValidationException::withMessages(['specialist_id' => ['Selected specialist is not available in the selected hospital.']]);
        }

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->doctorRepository->create($data);
    }
    public function update(int $id, array $data)
    {
        if (!$this->hospitalSpecialistRepository->existsForHospitalAndSpecialist($data['hospital_id'], $data['specialist_id'])) {
            throw ValidationException::withMessages(['specialist_id' => ['Selected specialist is not available in the selected hospital.']]);
        }
        $doctor = $this->doctorRepository->getById($id, ['*']);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if (!empty($doctor->photo)) {
                $this->deletePhoto($doctor->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->doctorRepository->update($id, $data);
    }

    public function delete(int $id)
    {

        $$doctor = $this->doctorRepository->getById($id, ['*']);

        if ($$doctor->photo) {
            $this->deletePhoto($doctor->photo);
        }
        $this->doctorRepository->delete($id);
    }



    public function uploadPhoto(UploadedFile $photo)
    {
        return $photo->store('doctors', 'public');
    }

    public function deletePhoto(string $photoPath)
    {
        $relativePath = 'doctors/' . basename($photoPath);

        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}
