<?php

namespace App\Repositories;

use App\Models\Hospital;

class HostpitalRepository
{
    public function getAll(array $fields)
    {
        return Hospital::select($fields)->latest()->paginate(10);
    }

    public function getById(int $id, array $fields)
    {
        return Hospital::select($fields)->with(['doctors.specialist', 'specialists'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Hospital::create($data);
    }

    public function update(int $id, array $data)
    {
        $hostpital = Hospital::findOrFail($id);
        $hostpital->update($data);
        return $hostpital;
    }

    public function delete(int $id)
    {
        $hospital = Hospital::findOrFail($id);
        $hospital->delete();
    }
}
