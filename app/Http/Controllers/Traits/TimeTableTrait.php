<?php

namespace App\Http\Controllers\Traits;

use App\Models\TeacherSubject;
use App\Models\Timetable;
use DB;

trait TimeTableTrait
{
    public function studentsTimetable($student_user_id, $school_year_id)
    {
        $timetable = array();
        for ($i = 1; $i < 8; $i++) {
            for ($j = 1; $j < 7; $j++) {
                $timetable[$i][$j] = Timetable::join('teacher_subjects', 'timetables.teacher_subject_id', '=', 'teacher_subjects.id')
                    ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
                    ->join('student_groups', 'teacher_subjects.student_group_id', '=', 'student_groups.id')
                    ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
                    ->join('student_student_group', 'student_student_group.student_group_id', '=', 'student_groups.id')
                    ->join('students', 'students.id', '=', 'student_student_group.student_id')
                    ->join('users', 'teacher_subjects.teacher_id', '=', 'users.id')
                    ->where('students.user_id', $student_user_id)
                    ->whereNull('directions.deleted_at')
                    ->whereNull('student_groups.deleted_at')
                    ->whereNull('subjects.deleted_at')
                    ->where('students.school_year_id', $school_year_id)
                    ->where('week_day', $j)->where('hour', $i)
                    ->select('timetables.id', 'subjects.title as subject',
                        DB::raw('CONCAT(users.first_name, " ", users.last_name) as teacher'))->get();
            }
        }
        return $timetable;
    }

    public function studentsTimetableAPI($student_user_id, $school_year_id)
    {
        $timetable = array();
        for ($i = 1; $i < 8; $i++) {
            for ($j = 1; $j < 7; $j++) {
                $timetable[$i][$j] = Timetable::join('teacher_subjects', 'timetables.teacher_subject_id', '=', 'teacher_subjects.id')
                    ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
                    ->join('student_groups', 'teacher_subjects.student_group_id', '=', 'student_groups.id')
                    ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
                    ->join('student_student_group', 'student_student_group.student_group_id', '=', 'student_groups.id')
                    ->join('students', 'students.id', '=', 'student_student_group.student_id')
                    ->join('users', 'teacher_subjects.teacher_id', '=', 'users.id')
                    ->where('students.user_id', $student_user_id)
                    ->whereNull('directions.deleted_at')
                    ->whereNull('student_groups.deleted_at')
                    ->whereNull('subjects.deleted_at')
                    ->where('students.school_year_id', $school_year_id)
                    ->where('week_day', $j)->where('hour', $i)
                    ->select('timetables.id', 'subjects.title as subject',
                        DB::raw('CONCAT(users.first_name, " ", users.last_name) as teacher'))->get();
            }
        }
        return array("timetable" => $timetable);
    }

    public function studentsTimetableDay($student_user_id, $school_year_id, $day_id)
    {
        $timetable = array();
        for ($j = 1; $j < 8; $j++) {
            $timetable[$j] = Timetable::join('teacher_subjects', 'timetables.teacher_subject_id', '=', 'teacher_subjects.id')
                ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
                ->join('student_groups', 'teacher_subjects.student_group_id', '=', 'student_groups.id')
                ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
                ->join('student_student_group', 'student_student_group.student_group_id', '=', 'student_groups.id')
                ->join('students', 'students.id', '=', 'student_student_group.student_id')
                ->join('users', 'teacher_subjects.teacher_id', '=', 'users.id')
                ->where('students.user_id', $student_user_id)
                ->whereNull('directions.deleted_at')
                ->whereNull('student_groups.deleted_at')
                ->whereNull('subjects.deleted_at')
                ->where('students.school_year_id', $school_year_id)
                ->where('week_day', $day_id)->where('hour', $j)
                ->select('timetables.id', 'subjects.title as subject',
                    DB::raw('CONCAT(users.first_name, " ", users.last_name) as teacher'))->get();
        }
        return $timetable;
    }

    public function studentsTimetableDayAPI($student_user_id, $school_year_id, $day_id)
    {
        $timetable = array();
        for ($j = 1; $j < 8; $j++) {
            $timetable[$j] = Timetable::join('teacher_subjects', 'timetables.teacher_subject_id', '=', 'teacher_subjects.id')
                ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
                ->join('student_groups', 'teacher_subjects.student_group_id', '=', 'student_groups.id')
                ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
                ->join('student_student_group', 'student_student_group.student_group_id', '=', 'student_groups.id')
                ->join('students', 'students.id', '=', 'student_student_group.student_id')
                ->join('users', 'teacher_subjects.teacher_id', '=', 'users.id')
                ->where('students.user_id', $student_user_id)
                ->whereNull('directions.deleted_at')
                ->whereNull('student_groups.deleted_at')
                ->whereNull('subjects.deleted_at')
                ->where('students.school_year_id', $school_year_id)
                ->where('week_day', $day_id)->where('hour', $j)
                ->select('timetables.id', 'subjects.title as subject',
                    DB::raw('CONCAT(users.first_name, " ", users.last_name) as teacher'))->get();
        }
        return array('timetable' => $timetable);
    }

    public function studentsTimetableSubjectsDayAPI($student_user_id, $school_year_id)
    {
        $subject_list = Timetable::join('teacher_subjects', 'timetables.teacher_subject_id', '=', 'teacher_subjects.id')
            ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
            ->join('student_groups', 'teacher_subjects.student_group_id', '=', 'student_groups.id')
            ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
            ->join('student_student_group', 'student_student_group.student_group_id', '=', 'student_groups.id')
            ->join('students', 'students.id', '=', 'student_student_group.student_id')
            ->join('users', 'teacher_subjects.teacher_id', '=', 'users.id')
            ->where('students.user_id', $student_user_id)
            ->whereNull('directions.deleted_at')
            ->whereNull('student_groups.deleted_at')
            ->whereNull('subjects.deleted_at')
            ->where('students.school_year_id', $school_year_id)
            ->select('subjects.title as subject',
                DB::raw('CONCAT(users.first_name, " ", users.last_name) as teacher'))
            ->distinct()->get();

        return array('subject_list' => $subject_list);
    }

    public function teacherTimetable($teacher_id, $school_year_id,$current_school)
    {
        $subject_list = TeacherSubject::join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
            ->join('student_groups', 'teacher_subjects.student_group_id', '=', 'student_groups.id')
            ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
            ->whereNull('directions.deleted_at')
            ->whereNull('student_groups.deleted_at')
            ->whereNull('subjects.deleted_at')
            ->where('teacher_subjects.school_year_id', $school_year_id)
            ->where('teacher_subjects.teacher_id', $teacher_id)
            ->where('teacher_subjects.school_id', '=', $current_school)
            ->select('teacher_subjects.id', 'subjects.title as subject', 'student_groups.title as group')->get();

        $timetable = array();
        for ($i = 1; $i < 8; $i++) {
            for ($j = 1; $j < 7; $j++) {
                $timetable[$i][$j] = Timetable::join('teacher_subjects', 'timetables.teacher_subject_id', '=', 'teacher_subjects.id')
                    ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
                    ->join('student_groups', 'teacher_subjects.student_group_id', '=', 'student_groups.id')
                    ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
                    ->whereNull('directions.deleted_at')
                    ->whereNull('student_groups.deleted_at')
                    ->whereNull('subjects.deleted_at')
                    ->where('teacher_subjects.teacher_id', $teacher_id)
                    ->where('teacher_subjects.school_year_id', $school_year_id)
                    ->where('teacher_subjects.school_id', '=', $current_school)
                    ->where('week_day', $j)->where('hour', $i)
                    ->select('timetables.id', 'subjects.title as subject', 'student_groups.title as group')->get();
            }
        }
        return array($timetable, $subject_list);
    }

    public function teacherTimetableAPI($teacher_id, $school_year_id)
    {
        $subject_group = TeacherSubject::join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
            ->join('student_groups', 'teacher_subjects.student_group_id', '=', 'student_groups.id')
            ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
            ->join('schools', 'schools.id', '=', 'teacher_subjects.school_id')
            ->whereNull('directions.deleted_at')
            ->whereNull('student_groups.deleted_at')
            ->whereNull('subjects.deleted_at')
            ->where('teacher_subjects.school_year_id', $school_year_id)
            ->where('teacher_subjects.teacher_id', $teacher_id)
            ->select('teacher_subjects.id', 'subjects.title as subject', 'student_groups.title as group',
                'teacher_subjects.school_id','schools.title')->get()->toArray();

        $timetable = array();
        for ($i = 1; $i < 8; $i++) {
            for ($j = 1; $j < 7; $j++) {
                $timetable[$i][$j] = Timetable::join('teacher_subjects', 'timetables.teacher_subject_id', '=', 'teacher_subjects.id')
                    ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
                    ->join('student_groups', 'teacher_subjects.student_group_id', '=', 'student_groups.id')
                    ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
                    ->join('schools', 'schools.id', '=', 'teacher_subjects.school_id')
                    ->whereNull('directions.deleted_at')
                    ->whereNull('student_groups.deleted_at')
                    ->whereNull('subjects.deleted_at')
                    ->where('teacher_subjects.teacher_id', $teacher_id)
                    ->where('teacher_subjects.school_year_id', $school_year_id)
                    ->where('week_day', $j)->where('hour', $i)
                    ->select('timetables.id', 'subjects.title as subject', 'student_groups.title as group',
                        'teacher_subjects.school_id','schools.title')->get()->toArray();
            }
        }
        return array('timetable' => $timetable, 'subject_group' => $subject_group);
    }

    public function teacherTimetableDayAPI($teacher_id, $school_year_id, $day_id)
    {
        $timetable = array();
        for ($j = 1; $j < 8; $j++) {
            $timetable[$j] = Timetable::join('teacher_subjects', 'timetables.teacher_subject_id', '=', 'teacher_subjects.id')
                ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
                ->join('student_groups', 'teacher_subjects.student_group_id', '=', 'student_groups.id')
                ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
                ->join('schools', 'schools.id', '=', 'teacher_subjects.school_id')
                ->whereNull('directions.deleted_at')
                ->whereNull('student_groups.deleted_at')
                ->whereNull('subjects.deleted_at')
                ->where('teacher_subjects.teacher_id', $teacher_id)
                ->where('teacher_subjects.school_year_id', $school_year_id)
                ->where('week_day', $day_id)->where('hour', $j)
                ->select('timetables.id', 'subjects.title as subject', 'student_groups.title as group',
                    'teacher_subjects.school_id','schools.title')->get()->toArray();
        }
        return array('timetable' => $timetable);
    }
    public function teacherSubjectListAPI($teacher_id,$school_year_id)
    {
        $subject_list = TeacherSubject::join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
            ->join('student_groups', 'teacher_subjects.student_group_id', '=', 'student_groups.id')
            ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
            ->join('schools', 'schools.id', '=', 'teacher_subjects.school_id')
            ->whereNull('directions.deleted_at')
            ->whereNull('student_groups.deleted_at')
            ->whereNull('subjects.deleted_at')
            ->where('teacher_subjects.school_year_id', $school_year_id)
            ->where('teacher_subjects.teacher_id', $teacher_id)
            ->select('teacher_subjects.id', 'subjects.title as subject', 'student_groups.title as group',
                'teacher_subjects.school_id','schools.title')->get()->toArray();
        return array('subject_list' => $subject_list);
    }

    public function teacherGroupTimetable($teachergroup_id, $teacher_id)
    {

        $subject_list = TeacherSubject::join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
            ->join('student_groups', 'student_groups.id', '=', 'teacher_subjects.student_group_id')
            ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
            ->whereNull('directions.deleted_at')
            ->whereNull('student_groups.deleted_at')
            ->whereNull('subjects.deleted_at')
            ->where('teacher_subjects.student_group_id', $teachergroup_id)
            ->where('teacher_subjects.teacher_id', $teacher_id)
            ->select('teacher_subjects.id', 'subjects.title as subject', "student_groups.title as group")->get();
        $timetable = array();
        for ($i = 1; $i < 8; $i++) {
            for ($j = 1; $j < 7; $j++) {
                $timetable[$i][$j] = Timetable::join('teacher_subjects', 'timetables.teacher_subject_id', '=', 'teacher_subjects.id')
                    ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
                    ->join('student_groups', 'student_groups.id', '=', 'teacher_subjects.student_group_id')
                    ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
                    ->whereNull('directions.deleted_at')
                    ->whereNull('student_groups.deleted_at')
                    ->whereNull('subjects.deleted_at')
                    ->where('teacher_subjects.student_group_id', $teachergroup_id)
                    ->where('teacher_subjects.teacher_id', $teacher_id)
                    ->where('week_day', $j)->where('hour', $i)
                    ->select('timetables.id', 'subjects.title as subject', "student_groups.title as group")->get();
            }
        }
        return array($timetable, $subject_list);
    }


    public function teacherGroupTimetableAPI($teachergroup_id, $teacher_id)
    {
        $subject_group = TeacherSubject::join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
            ->join('student_groups', 'student_groups.id', '=', 'teacher_subjects.student_group_id')
            ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
            ->whereNull('directions.deleted_at')
            ->whereNull('student_groups.deleted_at')
            ->whereNull('subjects.deleted_at')
            ->where('teacher_subjects.student_group_id', $teachergroup_id)
            ->where('teacher_subjects.teacher_id', $teacher_id)
            ->select('teacher_subjects.id', 'subjects.title as subject', "student_groups.title as group")->get()->toArray();
        $timetable = array();
        for ($i = 1; $i < 8; $i++) {
            for ($j = 1; $j < 7; $j++) {
                $timetable[$i][$j] = Timetable::join('teacher_subjects', 'timetables.teacher_subject_id', '=', 'teacher_subjects.id')
                    ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
                    ->join('student_groups', 'student_groups.id', '=', 'teacher_subjects.student_group_id')
                    ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
                    ->whereNull('directions.deleted_at')
                    ->whereNull('student_groups.deleted_at')
                    ->whereNull('subjects.deleted_at')
                    ->where('teacher_subjects.student_group_id', $teachergroup_id)
                    ->where('teacher_subjects.teacher_id', $teacher_id)
                    ->where('week_day', $j)->where('hour', $i)
                    ->select('timetables.id', 'subjects.title as subject', "student_groups.title as group")->get()->toArray();
            }
        }
        return array('timetable' => $timetable, 'subject_group' => $subject_group);
    }
    public function teacherSubjectListGroupAPI($teacher_id,$teachergroup_id)
    {
        $subject_list = TeacherSubject::join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
            ->join('student_groups', 'student_groups.id', '=', 'teacher_subjects.student_group_id')
            ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
            ->whereNull('directions.deleted_at')
            ->whereNull('student_groups.deleted_at')
            ->whereNull('subjects.deleted_at')
            ->where('teacher_subjects.student_group_id', $teachergroup_id)
            ->where('teacher_subjects.teacher_id', $teacher_id)
            ->select('teacher_subjects.id', 'subjects.title as subject', "student_groups.title as group")->get()->toArray();
        return array('subject_list' => $subject_list);
    }

    public function teacherGroupTimetableDayAPI($teachergroup_id, $teacher_id, $day_id)
    {
        $timetable = array();
        for ($j = 1; $j < 8; $j++) {
            $timetable[$j] = Timetable::join('teacher_subjects', 'timetables.teacher_subject_id', '=', 'teacher_subjects.id')
                ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
                ->join('student_groups', 'student_groups.id', '=', 'teacher_subjects.student_group_id')
                ->join('directions', 'directions.id', '=', 'student_groups.direction_id')
                ->whereNull('directions.deleted_at')
                ->whereNull('student_groups.deleted_at')
                ->whereNull('subjects.deleted_at')
                ->where('teacher_subjects.student_group_id', $teachergroup_id)
                ->where('teacher_subjects.teacher_id', $teacher_id)
                ->where('week_day', $day_id)->where('hour', $j)
                ->select('timetables.id', 'subjects.title as subject', "student_groups.title as group")->get()->toArray();
        }
        return array('timetable' => $timetable);
    }
}