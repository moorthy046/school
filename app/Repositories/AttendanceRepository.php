<?php

namespace App\Repositories;


interface AttendanceRepository
{
    public function getAll();

    public function getAllForStudentsAndSchoolYear($students,$school_year_id);
}