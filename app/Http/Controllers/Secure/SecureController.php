<?php

namespace App\Http\Controllers\Secure;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Book;
use App\Models\BookUser;
use App\Models\Diary;
use App\Models\Direction;
use App\Models\Exam;
use App\Models\Invoice;
use App\Models\Mark;
use App\Models\Notice;
use App\Models\Notification;
use App\Models\NotificationEvent;
use App\Models\Payment;
use App\Models\Salary;
use App\Models\School;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use App\Models\TeacherSubject;
use App\Models\User;
use Carbon\Carbon;
use Sentinel;
use Session;
use App\Http\Controllers\Traits\SchoolYearTrait;
use Calendar;
use DateTime;
use Laracasts\Flash\Flash;

class SecureController extends Controller
{
    use SchoolYearTrait;

    public function __construct()
    {
        if (Sentinel::check()) {
            $this->user = User::find(Sentinel::getUser()->id);
            view()->share('user', $this->user);
            $this->shareValues();
        } else {
            Sentinel::logout(null, true);

            return redirect('page')->send();
        }
    }

    public function shareValues()
    {
        if (isset($this->user->id)) {

            /*
             * if current user is super admin
             */
            if ($this->user->inRole('super_admin')
                || $this->user->inRole('human_resources')) {
                $current_school = Session::get('current_school');

                $result = $this->currentSchool($current_school);

                if (!isset($result['other_schools'])) {
                    Sentinel::logout(null, true);
                    Session::flush();
                    Flash::error(trans('secure.no_schools'));

                    return redirect()->guest('/');
                } else {
                    $this->setSessionSchool($result);
                }
            }

            /*
             * if current user is admin or human_resources or librarian
             */
            if ($this->user->inRole('admin')
                || $this->user->inRole('human_resources')
                || $this->user->inRole('librarian')
            ) {
                $current_school_year = Session::get('current_school_year');

                $result = $this->currentSchoolYear($current_school_year);

                if (!isset($result['other_school_years'])) {
                    Sentinel::logout(null, true);
                    Session::flush();
                    Flash::error(trans('secure.no_school_year'));

                    return redirect()->guest('/');
                } else {
                    $this->setSessionSchoolYears($result);
                }

            }
            /*
             * if current user is admin
             */
            if ($this->user->inRole('admin')
            ) {
                $current_school = Session::get('current_school');

                $result = $this->currentSchoolAdmin($current_school, $this->user->id);

                if (!isset($result['other_schools'])) {
                    Sentinel::logout(null, true);
                    Session::flush();
                    Flash::error(trans('secure.no_schools'));

                    return redirect()->guest('/');
                } else {
                    $this->setSessionSchool($result);
                }

            }
            /*
             * if current user is teacher
             */
            if ($this->user->inRole('teacher')) {

                $current_school = Session::get('current_school');

                $result = $this->currentSchoolTeacher($current_school, $this->user->id);

                if (!isset($result['other_schools'])) {
                    Sentinel::logout(null, true);
                    Session::flush();
                    Flash::error(trans('secure.no_schools'));

                    return redirect()->guest('/');
                } else {
                    $this->setSessionSchool($result);
                }

                $current_school_year = Session::get('current_school_year');
                $result = $this->currentSchoolYearTeacher($current_school_year, $this->user->id);
                if (!isset($result['other_school_years'])) {
                    Sentinel::logout(null, true);
                    Session::flush();
                    Flash::error(trans('secure.no_school_year'));

                    return redirect()->guest('/');
                } else {
                    $this->setSessionSchoolYears($result);
                }
                $student_group = Session::get('current_student_group');
                $current_school_year = Session::get('current_school_year');

                $result_groups = $this->currentTeacherStudentGroupSchool($student_group, $current_school_year,$current_school);
                if (empty($result_groups['student_groups'])) {
                    Sentinel::logout(null, true);
                    Session::flush();
                    Flash::error(trans('secure.no_school_year'));
                    return redirect()->guest('/');
                } else {
                    $this->setSessionTeacherStudentGroups($result_groups);
                }
            }
            /*
             * if current user is student
             */
            if ($this->user->inRole('student')) {
                $current_school = Session::get('current_school');

                $result = $this->currentSchoolStudent($current_school, $this->user->id);

                if (!isset($result['other_schools'])) {
                    Sentinel::logout(null, true);
                    Session::flush();
                    Flash::error(trans('secure.no_schools'));

                    return redirect()->guest('/');
                } else {
                    $this->setSessionSchool($result);
                }
                $current_school_year = Session::get('current_school_year');
                $result_school_year = $this->currentSchoolYearSchoolStudent($current_school_year, $this->user->id,Session::get('current_school'));

                if (!isset($result_school_year['other_school_years'])) {
                    Sentinel::logout(null, true);
                    Session::flush();
                    Flash::error(trans('secure.no_school_year'));
                    return redirect()->guest('/');
                } else {
                    $this->setSessionSchoolYears($result_school_year);
                }

                $student_section = Session::get('current_student_section');
                $current_school_year = Session::get('current_school_year');
                $result_section = $this->currentStudentSectionSchool($student_section, $current_school_year,Session::get('current_school'));
                if (!isset($result_section['student_section_id']) || $result_section['student_section_id']==0) {
                    Sentinel::logout(null, true);
                    Session::flush();
                    Flash::error(trans('secure.no_sections_added'));
                    return redirect()->guest('/');
                } else {
                    $this->setSessionStudentSection($result_section);
                }
            }
            /*
            * if current user is parent
            */
            if ($this->user->inRole('parent')) {

                $current_school = Session::get('current_school');

                $result = $this->currentSchoolParent($current_school, $this->user->id);

                if (!isset($result['other_schools'])) {
                    Sentinel::logout(null, true);
                    Session::flush();
                    Flash::error(trans('secure.no_schools'));

                    return redirect()->guest('/');
                } else {
                    $this->setSessionSchool($result);
                }

                $current_school_year = Session::get('current_school_year');
                $student_id = Session::get('current_student_id');
                $current_school = Session::get('current_school');

                $result = $this->currentSchoolYearParent($current_school_year, $student_id,$current_school);
                if (!isset($result['other_school_years'])) {
                    Sentinel::logout(null, true);
                    Session::flush();
                    Flash::error(trans('secure.no_school_year'));
                    return redirect()->guest('/');
                } else {
                    $this->setSessionSchoolYears($result);
                }

                $current_school_year = Session::get('current_school_year');
                $student_id = Session::get('current_student_id');
                $result = $this->currentParentStudents($student_id, $current_school_year,$current_school);

                if (!isset($result['student_ids'])) {
                    Sentinel::logout(null, true);
                    Session::flush();
                    Flash::error(trans('secure.no_students_added'));
                    return redirect()->guest('/');
                } else {
                    $this->setStudentParent($result);
                }

            }
        } else {
            return redirect('/signin');
        }
    }

    public function setYear($id)
    {
        Session::put('current_school_year', $id);

        return redirect('/');
    }

    public function setSchool($id)
    {
        Session::put('current_school', $id);

        return redirect('/');
    }


    public function setGroup($id)
    {
        Session::put('current_student_group', $id);

        return redirect('/');
    }

    public function setStudent($id)
    {
        Session::put('current_student_id', $id);

        return redirect('/');
    }

    public function showHome()
    {
        if (Sentinel::check()) {
            if ($this->user->inRole('super_admin')) {
                list($schools, $teachers, $parents, $directions) = $this->super_admin_dashboard();
                return view('dashboard.super_admin', compact('schools','teachers','parents','directions'));
            }
            elseif ($this->user->inRole('admin')) {
                list($sections, $teachers, $parents, $directions, $per_month, $per_school_year) = $this->admin_dashboard();
                return view('dashboard.admin', compact('sections','teachers','parents','directions','per_month',
                    'per_school_year'));
            }
            elseif ($this->user->inRole('human_resources')) {
                list($schools, $teachers, $parents, $directions, $per_month) = $this->human_resources_dashboard();
                return view('dashboard.human_resources', compact('schools','teachers','parents','directions','per_month'));
            }
            elseif ($this->user->inRole('teacher')) {
                list($teachergroups, $subjects, $diaries, $exams) = $this->teacher_dashboard();
                return view('dashboard.teacher', compact('teachergroups','subjects','diaries','exams'));
            }
            elseif ($this->user->inRole('librarian')) {
                list($books_total, $issued_books, $reserved_books) = $this->librarian_dashboard();
                $books = array(array('title'=>trans('dashboard.total_books'),'items'=>$books_total, 'color'=>"#fd9883"),
                            array('title'=>trans('dashboard.issued_books'),'items'=>$issued_books, 'color'=>"#c2185b"),
                            array('title'=>trans('dashboard.reserved_books'),'items'=>$reserved_books, 'color'=>"#00796b"));

                return view('dashboard.librarian', compact('books'));
            }
            elseif ($this->user->inRole('student')) {
                list($borrowed_books, $dairies, $attendances, $marks) = $this->student_dashboard();
                return view('dashboard.student', compact('borrowed_books','dairies','attendances','marks'));
            }
            elseif ($this->user->inRole('parent')) {
                list($borrowed_books, $dairies, $attendances, $marks) = $this->parent_dashboard();
                return view('dashboard.parent', compact('borrowed_books','dairies','attendances','marks'));
            }
            return view('dashboard.index');
        } else {
            return redirect('signin');
        }
    }

    public function events()
    {
        if ($this->user->inRole('student')) {
            $data = Notice::where("user_id", $this->user->id)->select(array("id", "title", "date", "description"))->get();
        } else {
            if ($this->user->inRole('parent')) {
                $user = User::find(Session::get('current_student_user_id'));
                if (isset($user)) {
                    $data = Notice::where("user_id", $user->id)->select(array("id", "title", "date", "description"))->get();
                }
            } else {
                $data = Notification::where("user_id", $this->user->id)->select(
                    array("id", "title", "date", "content as description")
                )->get();
            }
        }
        $events = [];
        if (isset($data)) {
            foreach ($data as $d) {
                $event = [];
                $date = date_format(new DateTime($d->date), 'Y-m-d');
                $event['title'] = $d->title;
                $event['id'] = $d->id;
                $event['start'] = $date;
                $event['end'] = $date;
                $event['allDay'] = true;
                $event['description'] = $d->description;
                array_push($events, $event);
            }
        }

        return json_encode($events);
    }

    /*
 * Matches each symbol of PHP date format standard
 * with jQuery equivalent codeword
 * @author Stojan Kukrika
 */
    function dateformat_PHP_to_jQueryUI($php_format)
    {
        $SYMBOLS_MATCHING = array(
            // Day
            'd' => 'DD',
            'D' => 'ddd',
            'j' => 'D',
            'l' => 'dddd',
            'N' => 'do',
            'S' => 'do',
            'w' => 'd',
            'z' => 'DDD',
            // Week
            'W' => 'w',
            // Month
            'F' => 'MMMM',
            'm' => 'MM',
            'M' => 'MMM',
            'n' => 'M',
            't' => '',
            // Year
            'L' => '',
            'o' => '',
            'Y' => 'GGGG',
            'y' => 'GG',
            // Time
            'a' => 'a',
            'A' => 'A',
            'B' => '',
            'g' => 'h',
            'G' => 'H',
            'h' => 'hh',
            'H' => 'HH',
            'i' => 'mm',
            's' => 'ss',
            'u' => ''
        );


        $jqueryui_format = "";
        $escaping = false;
        for ($i = 0; $i < strlen($php_format); $i++) {
            $char = $php_format[$i];
            if ($char === '\\') // PHP date format escaping character
            {
                $i++;
                if ($escaping) $jqueryui_format .= $php_format[$i];
                else $jqueryui_format .= '\'' . $php_format[$i];
                $escaping = true;
            } else {
                if ($escaping) {
                    $jqueryui_format .= "'";
                    $escaping = false;
                }
                if (isset($SYMBOLS_MATCHING[$char]))
                    $jqueryui_format .= $SYMBOLS_MATCHING[$char];
                else
                    $jqueryui_format .= $char;
            }
        }
        return $jqueryui_format;
    }

    public function generateStudentNo($student_id,$school_id)
    {
        $school = School::find($school_id);
        return $school->student_card_prefix.$student_id;
    }

    //Dashboard methods for each role
    private function human_resources_dashboard()
    {
        list($schools, $teachers, $parents, $directions) = $this->super_admin_dashboard();

        $per_month = array();
        for ($i = 11; $i >= 0; $i--) {
            $per_month[] =
                array(
                    'month' => Carbon::now()->subMonth($i)->format('M'),
                    'year' => Carbon::now()->subMonth($i)->format('Y'),
                    'salary_by_month' => Salary::where(
                        'date',
                        'LIKE',
                        Carbon::now()->subMonth($i)->format('Y-m') . '%'
                    )->sum('salary'),
                    'sum_of_payments' => Payment::where(
                        'created_at',
                        'LIKE',
                        Carbon::now()->subMonth($i)->format('Y-m') . '%'
                    )->sum('amount'),
                    'sum_of_invoices' => Invoice::where(
                        'created_at',
                        'LIKE',
                        Carbon::now()->subMonth($i)->format('Y-m') . '%'
                    )->sum('amount')
                );
        }
        return array($schools, $teachers, $parents, $directions, $per_month);
    }

    private function admin_dashboard()
    {
        $sections = Section::where('school_year_id', Session::get('current_school_year'))
            ->where('school_id', Session::get('current_school'))->get();

        $teachers = User::join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->where('roles.slug', 'teacher')->count();

        $parents = User::join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->where('roles.slug', 'parent')->count();

        $directions = Direction::count();

        $per_month = array();
        for ($i = 11; $i >= 0; $i--) {
            $per_month[] =
                array(
                    'month' => Carbon::now()->subMonth($i)->format('M'),
                    'year' => Carbon::now()->subMonth($i)->format('Y'),
                    'salary_by_month' => Salary::where(
                        'date',
                        'LIKE',
                        Carbon::now()->subMonth($i)->format('Y-m') . '%'
                    )->sum('salary'),
                    'sum_of_payments' => Payment::where(
                        'created_at',
                        'LIKE',
                        Carbon::now()->subMonth($i)->format('Y-m') . '%'
                    )->sum('amount'),
                    'sum_of_invoices' => Invoice::where(
                        'created_at',
                        'LIKE',
                        Carbon::now()->subMonth($i)->format('Y-m') . '%'
                    )->sum('amount')
                );
        }

        $per_school_year = array();
        $school_years = SchoolYear::all();
        foreach ($school_years as $school_year) {
            $per_school_year[] =
                array(
                    'school_year' => $school_year->title,
                    'number_of_students' => Student::where('school_year_id', $school_year->id)
                        ->where('school_id', Session::get('current_school'))->count(),
                    'number_of_sections' => Section::where('school_year_id', $school_year->id)
                        ->where('school_id', Session::get('current_school'))->count()
                );
        }
        return array($sections, $teachers, $parents, $directions, $per_month, $per_school_year);
    }

    private function super_admin_dashboard()
    {
        $schools = School::all();

        $teachers = User::join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->where('roles.slug', 'teacher')->count();

        $parents = User::join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->where('roles.slug', 'parent')->count();

        $directions = Direction::count();
        return array($schools, $teachers, $parents, $directions);
    }

    /**
     * @return array
     */
    private function teacher_dashboard()
    {
        $teachergroups = TeacherSubject::join('student_groups', 'student_groups.id', '=', 'teacher_subjects.student_group_id')
            ->where('school_year_id', Session::get('current_school_year'))
            ->where('school_id', Session::get('current_school'))
            ->where('teacher_id', $this->user->id)
            ->distinct('student_group_id');
        
        $subjects = TeacherSubject::where('school_year_id', Session::get('current_school_year'))
            ->where('school_id', Session::get('current_school'))
            ->where('teacher_id', $this->user->id)
            ->distinct('subject_id')->count();
        $diaries = Diary::where('school_year_id', Session::get('current_school_year'))
            ->where('school_id', Session::get('current_school'))
            ->where('user_id', $this->user->id)->count();
        $exams = Exam::join('student_groups', 'student_groups.id', '=', 'exams.student_group_id')
            ->join('sections', 'sections.id', '=', 'student_groups.section_id')
            ->where('school_id', Session::get('current_school'))
            ->where('user_id', $this->user->id)->count();
        return array($teachergroups, $subjects, $diaries, $exams);
    }

    /**
     * @return array
     */
    private function librarian_dashboard()
    {
        $books_total = Book::sum('quantity');
        $issued_books = BookUser::whereNull('back')->whereNotNull('get')->count();
        $reserved_books = BookUser::whereNull('get')->whereNull('back')->whereNotNull('reserved')->count();
        return array($books_total, $issued_books, $reserved_books);
    }

    /**
     * @return array
     */
    private function student_dashboard()
    {
        $borrowed_books = BookUser::whereNull('back')->whereNotNull('get')->where('user_id', $this->user->id)->count();
        $dairies = Diary::join('subjects', 'subjects.id', '=', 'diaries.subject_id')
            ->join('teacher_subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
            ->join('student_student_group', 'student_student_group.student_group_id', '=', 'teacher_subjects.student_group_id')
            ->join('students', 'students.id', '=', 'student_student_group.student_id')
            ->where('diaries.school_year_id', Session::get('current_school_year'))
            ->where('students.user_id', $this->user->id)->count();
        $attendances = Attendance::join('students', 'students.id', '=', 'attendances.student_id')
            ->where('students.user_id', $this->user->id)
            ->where('students.school_year_id', Session::get('current_school_year'))
            ->orderBy('attendances.created_at', 'desc')
            ->select('attendances.date', 'attendances.hour')
            ->get();
        $marks = Mark::join('students', 'students.id', '=', 'marks.student_id')
            ->join('mark_types', 'mark_types.id', '=', 'marks.mark_type_id')
            ->join('mark_values', 'mark_values.id', '=', 'marks.mark_value_id')
            ->where('students.user_id', $this->user->id)
            ->where('students.school_year_id', Session::get('current_school_year'))
            ->orderBy('marks.created_at', 'desc')
            ->select('marks.date', 'mark_values.title as mark_value','mark_types.title as mark_type')
            ->get();
        return array($borrowed_books, $dairies, $attendances, $marks);
    }

    /**
     * @return array
     */
    private function parent_dashboard()
    {
        $borrowed_books = BookUser::whereNull('back')->whereNotNull('get')->where('user_id', $this->user->id)->count();
        $dairies = Diary::join('subjects', 'subjects.id', '=', 'diaries.subject_id')
            ->join('teacher_subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
            ->join('student_student_group', 'student_student_group.student_group_id', '=', 'teacher_subjects.student_group_id')
            ->join('students', 'students.id', '=', 'student_student_group.student_id')
            ->where('diaries.school_year_id', Session::get('current_school_year'))
            ->where('students.user_id', Session::get('current_student_user_id'))->count();
        $attendances = Attendance::join('students', 'students.id', '=', 'attendances.student_id')
            ->where('students.user_id', Session::get('current_student_user_id'))
            ->where('students.school_year_id', Session::get('current_school_year'))
            ->orderBy('attendances.created_at', 'desc')
            ->select('attendances.date', 'attendances.hour')
            ->get();
        $marks = Mark::join('students', 'students.id', '=', 'marks.student_id')
            ->join('mark_types', 'mark_types.id', '=', 'marks.mark_type_id')
            ->join('mark_values', 'mark_values.id', '=', 'marks.mark_value_id')
            ->where('students.user_id', Session::get('current_student_user_id'))
            ->where('students.school_year_id', Session::get('current_school_year'))
            ->orderBy('marks.created_at', 'desc')
            ->select('marks.date', 'mark_values.title as mark_value','mark_types.title as mark_type')
            ->get();
        return array($borrowed_books, $dairies, $attendances, $marks);
    }

}