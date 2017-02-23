<?php

namespace App\Repositories;

use App\Models\Diary;
use Sentinel;

class DiaryRepositoryEloquent implements DiaryRepository
{
    /**
     * @var Diary
     */
    private $model;
    private $user;


    /**
     * DiaryRepositoryEloquent constructor.
     * @param Diary $model
     */
    public function __construct(Diary $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }

    public function getAllForSchoolYear($school_year_id)
    {
        return $this->model->where('school_year_id', $school_year_id);
    }

    public function getAllForSchoolYearAndStudentUserId($school_year_id, $student_user_id)
    {
        return $this->model->join('subjects', 'subjects.id', '=', 'diaries.subject_id')
            ->join('teacher_subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
            ->join('student_student_group', 'student_student_group.student_group_id', '=', 'teacher_subjects.student_group_id')
            ->join('students', 'students.id', '=', 'student_student_group.student_id')
           ->where('diaries.school_year_id', $school_year_id)
            ->where('students.user_id', $student_user_id);
    }

    public function getAllForSchoolYearAndSchool($school_year_id, $school_id)
    {
        return $this->model->where('school_year_id', $school_year_id)->where('school_id', $school_id);
    }
}