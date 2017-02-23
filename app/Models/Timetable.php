<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;

class Timetable extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $guarded  = array('id');

    public function teacher_subject()
    {
        return $this->belongsTo(TeacherSubject::class, 'teacher_subject_id');
    }

}
