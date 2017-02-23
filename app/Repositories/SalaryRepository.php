<?php

namespace App\Repositories;


interface SalaryRepository
{
    public function getAll();

    public function getAllForSchoolYearSchool($school_year_id,$school_id);
}