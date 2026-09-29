<?php

namespace App\Repositories;

use App\Models\Absence;

class AbsenceRepository{
    private function save (Absence $absence, array $inputs): Absence{
        $absence->fill($inputs);
        $absence->save();

        return $absence;
    }
    public function store (array $inputs): Absence{
        return $this-> save(new Absence(), $inputs);
    }
    public function update (Absence $absence ,array $inputs): Absence{
        return $this->save($absence, $inputs);
    }
    public function delete(Absence $absence): void{
        $absence->delete();
    }
}
