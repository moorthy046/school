<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherSubject extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $guarded = array('id');

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function student_group()
    {
        return $this->belongsTo(StudentGroup::class, 'student_group_id');
    }

    public function school_year()
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id');
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class,'teacher_subject_id','id');
    }

}
