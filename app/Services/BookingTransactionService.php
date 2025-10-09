<?php

namespace App\Services;

use App\Repositories\BookingTransactionRepository;
use App\Repositories\DoctorRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class BookingTransactionService
{
    private BookingTransactionRepository $bookingTransactionRepository;
    private DoctorRepository $doctorRepository;

    public function __construct(
        BookingTransactionRepository $bookingTransactionRepository,
        DoctorRepository $doctorRepository
    ) {
        $this->bookingTransactionRepository = $bookingTransactionRepository;
        $this->doctorRepository = $doctorRepository;
    }

    //manager services

    public function getAll()
    {
        return $this->bookingTransactionRepository->getAll();
    }

    public function getByIdForManager(int $id)
    {
        return $this->bookingTransactionRepository->getByIdForManager($id);
    }

    public function updateStatus(int $id, string $status)
    {
        if (!in_array($status, ['Approved', 'Rejected'])) {
            throw ValidationException::withMessages(['status' => ['Invalid status value.']]);
        }

        return $this->bookingTransactionRepository->updateStatus($id, $status);
    }


    //customer services

    public function getAllForUser(int $userId)
    {
        return $this->bookingTransactionRepository->getAllForUser($userId);
    }

    public function getById(int $id, int $userId)
    {
        return $this->bookingTransactionRepository->getById($id, $userId);
    }

    public function create(array $data)
    {
        $data['user_id'] = auth()->id();

        if ($this->bookingTransactionRepository->isTimeSlotTakenForDoctor($data['doctor_id'], $data['started_at'], $data['time_at'])) {
            throw ValidationException::withMessages([
                'time_at' => ['Waktu yang dipilih dokter ini sudah terisi.']
            ]);
        }

        // $doctor = $this->doctorRepository->getById($data['doctor_id'], ['*']);
        $doctor = $this->doctorRepository->getById($data['doctor_id'], ['*']);

        if (!$doctor) {
            throw ValidationException::withMessages([
                'doctor_id' => ['Doctor not found.']
            ]);
        }

        $price = $doctor->specialist->price;
        $tax = (int) round($price * 0.11);
        $grand = $price + $tax;

        $data['sub_total'] = $price;
        $data['tax_total'] = $tax;
        $data['grand_total'] = $grand;
        $data['status'] = 'Waiting';

        if (isset($data['proof']) && $data['proof'] instanceof UploadedFile) {
            $data['proof'] = $this->uploadProof($data['proof']);
        }
    }

    private function uploadProof(UploadedFile $file)
    {
        return $file->store('proof', 'public');
    }
}
