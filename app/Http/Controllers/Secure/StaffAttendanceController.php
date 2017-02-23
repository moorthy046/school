<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests\Secure\AddStaffAttendanceRequest;
use App\Http\Requests\Secure\DeleteRequest;
use App\Http\Requests\Secure\AttendanceGetRequest;
use App\Models\StaffAttendance;
use App\Repositories\OptionRepository;
use App\Repositories\StaffAttendanceRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Efriandika\LaravelSettings\Facades\Settings;
use Session;
use Datatables;
use Illuminate\Support\Collection;

class StaffAttendanceController extends SecureController
{

    /**
     * @var OptionRepository
     */
    private $optionRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var StaffAttendanceRepository
     */
    private $staffAttendanceRepository;

    /**
     * StaffAttendanceController constructor.
     * @param OptionRepository $optionRepository
     * @param UserRepository $userRepository
     * @param StaffAttendanceRepository $staffAttendanceRepository
     */
    public function __construct(OptionRepository $optionRepository,
                                UserRepository $userRepository,
                                StaffAttendanceRepository $staffAttendanceRepository)
    {
        parent::__construct();

        view()->share('type', 'staff_attendance');
        $this->optionRepository = $optionRepository;
        $this->userRepository = $userRepository;
        $this->staffAttendanceRepository = $staffAttendanceRepository;
    }

    public function index()
    {
        $title = trans('staff_attendance.attendances');
        $users = $this->list_of_users();
        $options = $this->optionRepository->getAllForSchool(Session::get('current_school'))
            ->where('category', 'attendance_type')->get()
            ->map(function ($option) {
                return [
                    "title" => $option->title,
                    "value" => $option->id,
                ];
            })->lists('title', 'value')->toArray();

        return view('staff_attendance.index', compact('title', 'users', 'options'));
    }

    public function addAttendance(AddStaffAttendanceRequest $request)
    {
        if (isset($request['users'])) {
            foreach ($request['users'] as $user_id) {
                $attendance = new StaffAttendance($request->except('users'));
                $attendance->user_id = $user_id;
                $attendance->school_year_id = Session::get('current_school_year');
                $attendance->school_id = Session::get('current_school');
                $attendance->save();
            }
        }
    }

    public function attendanceForDate(AttendanceGetRequest $request)
    {
        $attendances = $this->staffAttendanceRepository->getAllForSchoolYear(Session::get('current_school_year'))
            ->with('user', 'option')
            ->get()
            ->filter(function ($attendance) use ($request) {
                return (Carbon::createFromFormat(Settings::get('date_format'), $attendance->date) ==
                    Carbon::createFromFormat(Settings::get('date_format'), $request->date));
            })
            ->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'name' => $attendance->user->full_name,
                    'option' => $attendance->option->title,
                ];
            })->toArray();
        return json_encode($attendances);
    }

    public function deleteattendance(DeleteRequest $request)
    {
        $attendance = StaffAttendance::find($request['id']);
        $attendance->delete();
    }

    public function list_of_users()
    {
        $teachers = $this->userRepository->getUsersForRole('teacher')
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name,
                ];
            })
            ->lists('name', 'id')
            ->toArray();
        $human_resources = $this->userRepository->getUsersForRole('human_resources')
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name,
                ];
            })
            ->lists('name', 'id')
            ->toArray();
        $admins = $this->userRepository->getUsersForRole('admin')
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name,
                ];
            })
            ->lists('name', 'id')
            ->toArray();
        return $teachers + $human_resources + $admins;
    }

}
