<?php

namespace App\Repositories;

use App\Models\StudentGroup;
use Sentinel;

class StudentGroupRepositoryEloquent implements StudentGroupRepository
{
    /**
     * @var StudentGroup
     */
    private $model;
    private $user;


    /**
     * StudentRepositoryEloquent constructor.
     * @param StudentGroup $model
     */
    public function __construct(StudentGroup $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }

    public function getAllForSchoolYearSchool($school_year_id,$school_id)
    {
        return $this->model->whereHas('section', function ($q) use ($school_year_id,$school_id) {
            $q->where('sections.school_year_id', $school_year_id)
                ->where('sections.school_id', $school_id);
        });
    }

    public function getAllForSection($section_id)
    {
        return $this->model->where('section_id', $section_id);
    }
}