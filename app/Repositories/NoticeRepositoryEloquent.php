<?php

namespace App\Repositories;

use App\Models\Notice;
use Sentinel;

class NoticeRepositoryEloquent implements NoticeRepository
{
    /**
     * @var Notice
     */
    private $model;
    private $user;


    /**
     * NoticeRepositoryEloquent constructor.
     * @param Notice $model
     */
    public function __construct(Notice $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }

    public function getAllForSchoolYearAndSchool($school_year_id,$school_id)
    {
        return $this->model->with('subject', 'student_group', 'school_year')
            ->where('notices.school_year_id', $school_year_id)
            ->where('notices.school_id', $school_id);
    }

    public function getAllForSchoolYearAndGroup($school_year_id, $student_group_id)
    {
        return $this->model->with('subject', 'student_group', 'school_year')
            ->where('notices.student_group_id', $student_group_id)
            ->where('notices.school_year_id', $school_year_id)
            ->where('notices.user_id', $this->user->id);
    }
}