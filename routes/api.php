<?php

use App\Http\Controllers\BookingTransactionController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\HospitalSpecialistController;
use App\Http\Controllers\MyOrderController;
use App\Http\Controllers\SpecialistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('specialists', SpecialistController::class);
Route::apiResource('doctors', DoctorController::class);
Route::apiResource('hospitals', HospitalController::class);

Route::post('hospitals/{hospital}/specialists', [HospitalSpecialistController::class, 'attach']);
Route::delete('hospitals/{hospital}/specialists/{specialist}', [HospitalSpecialistController::class, 'detach']);

Route::apiResource('transactions', BookingTransactionController::class);
Route::patch('/transactions/{id}/status', [BookingTransactionController::class, 'UpdateStatus']);

Route::get('/doctors-filter', [DoctorController::class, 'filterBySpecialistAndHospital']);
Route::get('/doctors/{doctorId}/available-slots', [DoctorController::class, 'availableSlots']);


Route::get('my-orders', [MyOrderController::class, 'index']);
Route::post('my-orders', [MyOrderController::class, 'store']);
Route::get('my-orders/{id}', [MyOrderController::class, 'show']);
