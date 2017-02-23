<?php

namespace App\Http\Controllers\Traits;

use App\Models\Attendance;
use App\Models\Timetable;
use DB;

trait AttendanceTrait
{
    public function listAttendanceGroup($student_group_id,$start_date,$end_date)
    {
        return Attendance::join('students', 'students.id', '=', 'attendances.student_id')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->join('subjects', 'subjects.id', '=', 'attendances.subject_id')
            ->join('student_student_group', 'student_student_group.student_id', '=', 'students.id')
            ->whereBetween('attendances.date',array($start_date,$end_date))
            ->where('student_student_group.student_group_id', $student_group_id)
            ->orderBy('attendances.hour')
            ->orderBy('students.order')
            ->select('attendances.id',DB::raw('CONCAT(users.first_name, " ", users.last_name) as student_name'),'attendances.student_id',
                'attendances.hour', 'attendances.date', 'attendances.justified')
            ->get()->toArray();
    }
    public function listAttendanceGroupDate($student_group_id,$date)
    {
        return Attendance::join('students', 'students.id', '=', 'attendances.student_id')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->join('subjects', 'subjects.id', '=', 'attendances.subject_id')
            ->join('student_student_group', 'student_student_group.student_id', '=', 'students.id')
            ->where('attendances.date',$date)
            ->where('student_student_group.student_group_id', $student_group_id)
            ->orderBy('attendances.hour')
            ->orderBy('students.order')
            ->select('attendances.id',DB::raw('CONCAT(users.first_name, " ", users.last_name) as student_name'),'attendances.student_id',
                'attendances.hour', 'attendances.date', 'attendances.justified')
            ->get()->toArray();
    }
    public function listAttendanceStudent($student_id,$start_date,$end_date)
    {
        return Attendance::join('students', 'students.id', '=', 'attendances.student_id')
            ->join('subjects', 'subjects.id', '=', 'attendances.subject_id')
            ->join('student_student_group', 'student_student_group.student_id', '=', 'students.id')
            ->whereBetween('attendances.date',array($start_date,$end_date))
            ->where('students.id', $student_id)
            ->orderBy('attendances.hour')
            ->orderBy('students.order')
            ->select('attendances.id','subjects.title as subject','attendances.hour', 'attendances.date',
                'attendances.justified')
            ->get()->toArray();
    }
    public function listAttendanceStudentAPI($student_id,$start_date,$end_date)
    {
        $i=1;
        $current = strtotime($start_date);
        $last = strtotime($end_date);
        $attendance = array();

        while( $current <= $last ) {
            $attendance[$i]['date'] = date('Y-m-d', $current);
            $timetables = Timetable::join("teacher_subjects", "teacher_subjects.id", '=', "timetables.teacher_subject_id")
                ->join('student_student_group','student_student_group.student_group_id', '=', 'teacher_subjects.student_group_id')
                ->join('subjects','subjects.id', '=', 'teacher_subjects.subject_id')
                ->where("student_student_group.student_id", $student_id)
                ->where('timetables.week_day', date('w', $current))
                ->select('timetables.hour',
                    DB::raw(date('Y-m-d', $current).' as date'))->get()->toArray();
            $attendances = array();
            if(!empty($timetables)) {
                foreach ($timetables as $timetable) {
                    $justified = Attendance::where('attendances.hour', $timetable['hour'])
                        ->where('attendances.date', $timetable['date'])
                        ->select('attendances.justified')
                        ->first();
                    $attendances[$timetable['hour']] = isset($justified) ? $justified->justified : "yes";
                }
            }
            else{
                $attendances = array(""=>"");
            }
            $attendance[$i]['attendances'] = $attendances;
            $current = strtotime("+1 day", $current);
            $i++;
        }
        return $attendance;
    }

}