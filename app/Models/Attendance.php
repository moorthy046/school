<?php

namespace App\Models;

use Carbon\Carbon;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $guarded = array('id');

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function school_year()
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id');
    }

    public function date_format()
    {
        return Settings::get('date_format');
    }

    public function setDateAttribute($date)
    {
        $this->attributes['date'] = Carbon::createFromFormat($this->date_format(),$date)->format('Y-m-d');
    }
    public function getDateAttribute($date)
    {
        if ($date == "0000-00-00" || $date == "") {
            return "";
        } else {
            return date($this->date_format(), strtotime($date));
        }
    }
}
