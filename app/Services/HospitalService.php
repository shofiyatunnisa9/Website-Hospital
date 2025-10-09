<?php

namespace App\Services;

use App\Repositories\HospitalRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class HospitalService
{
    private $hospitalRepository;

    public function __construct(HospitalRepository $hospitalRepository)
    {
        $this->hospitalRepository = $hospitalRepository;
    }


    public function getAll(array $fields)
    {
        return $this->hospitalRepository->getAll($fields);
    }

    public function getById(int $id, array $fields)
    {
        return $this->hospitalRepository->getById($id, $fields);
    }

    public function create(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->hospitalRepository->create($data);
    }


    public function update(int $id, array $data)
    {
        $fields = ['*']; //select * (all)
        $specialist = $this->hospitalRepository->getById($id, $fields);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if (!empty($specialist->photo)) {
                $this->deletePhoto($specialist->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->hospitalRepository->update($id, $data);
    }


    public function delete(int $id)
    {
        $fields = ['*'];
        $specialist = $this->hospitalRepository->getById($id, $fields);

        if ($specialist->photo) {
            $this->deletePhoto($specialist->photo);
        }
        $this->hospitalRepository->delete($id);
    }

    private function uploadPhoto(UploadedFile $photo)
    {
        return $photo->store('hospitals', 'public');
    }

    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'hospitals/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    public function attacchSpecialist(int $hospitalId, int $specialistId)
    {
        $hospital = $this->hospitalRepository->getById($hospitalId, ['id']);
        $hospital->specialists()->syncWithoutDetaching($specialistId);
    }

    public function detachSpecialist(int $hospitalId, int $specialistId)
    {
        $hospital = $this->hospitalRepository->getById($hospitalId, ['id']);
        $hospital->specialists()->detach($specialistId);
    }
}
