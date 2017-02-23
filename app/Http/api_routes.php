<?php
/******************   API routes  ******************************/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers'], function ($api) {

    $api->post('login', 'Api\AuthController@login');

    $api->group(array('middleware' => 'jwt.auth'), function ($api) {

        //routes for all users
        $api->get('refresh', 'Api\AuthController@refreshToken');
        $api->get('behaviors', 'Api\GeneralController@behaviors');
        $api->get('books', 'Api\GeneralController@books');
        $api->get('directions', 'Api\GeneralController@directions');
        $api->get('dormitories', 'Api\GeneralController@dormitories');
        $api->get('dormitory_beds', 'Api\GeneralController@dormitoryBeds');
        $api->get('dormitory_rooms', 'Api\GeneralController@dormitoryRooms');
        $api->get('mark_types', 'Api\GeneralController@markTypes');
        $api->get('mark_values', 'Api\GeneralController@markValues');
        $api->get('notice_types', 'Api\GeneralController@noticeTypes');
        $api->get('notifications', 'Api\GeneralController@notifications');
        $api->get('sections', 'Api\GeneralController@sections');
        $api->get('semesters', 'Api\GeneralController@semesters');
        $api->get('subjects', 'Api\GeneralController@subjects');
        $api->get('transportations', 'Api\GeneralController@transportations');
        $api->get('transportation_directions', 'Api\GeneralController@transportationDirections');
        $api->get('messages', 'Api\GeneralController@messages');
        $api->post('send_message', 'Api\GeneralController@sendMssage');
        $api->post('reserve_book', 'Api\GeneralController@reserveBook');
        $api->get('payments', 'Api\GeneralController@payments');
        $api->get('student', 'Api\GeneralController@student');
        $api->get('book_search', 'Api\GeneralController@bookSearch');
        $api->get('user_search', 'Api\GeneralController@userSearch');
        $api->get('reserved_user_books', 'Api\GeneralController@reservedUserBooks');
        $api->get('borrowed_user_books', 'Api\GeneralController@borrowedUserBooks');
        $api->get('subject_books', 'Api\GeneralController@subjectBooks');
        $api->get('feedback', 'Api\GeneralController@feedback');
        $api->post('post_feedback', 'Api\GeneralController@postFeedback');
        $api->get('transportations_directions', 'Api\GeneralController@transportationsDirections');

        //routes for student
        $api->group(array('prefix' => 'student', 'middleware' => 'api.student'), function ($api) {
            $api->get('school_year_student', 'Api\StudentController@selectSchoolYearStudent');
            $api->get('school_years', 'Api\StudentController@schoolYears');
            $api->get('timetable', 'Api\StudentController@timetable');
            $api->get('timetable_day', 'Api\StudentController@timetableDay');
            $api->get('subject_list', 'Api\StudentController@subjectList');
            $api->get('borrowed_books', 'Api\StudentController@borrowedBooks');
            $api->get('marks', 'Api\StudentController@marks');
            $api->get('attendances', 'Api\StudentController@attendances');
            $api->get('notices', 'Api\StudentController@notices');
            $api->get('exams', 'Api\StudentController@exams');
            $api->get('diary', 'Api\StudentController@diary');
            $api->get('diary_date', 'Api\StudentController@diaryForDate');
            $api->get('exam_subject', 'Api\StudentController@examForSubject');
            $api->get('exam_group', 'Api\StudentController@examForGroup');
            $api->get('student_subject', 'Api\StudentController@studentSubject');
            $api->get('exam_marks', 'Api\StudentController@examMarks');
            $api->get('exam_marks_details', 'Api\StudentController@examMarksDetails');
            $api->get('schools', 'Api\StudentController@schools');
        });

        //routes for parent
        $api->group(array('prefix' => 'parent', 'middleware' => 'api.parent'), function ($api) {
            $api->get('school_year_child', 'Api\ParentController@selectSchoolYearChild');
            $api->get('school_years', 'Api\ParentController@schoolYears');
            $api->get('timetable', 'Api\ParentController@timetable');
            $api->get('timetable_day', 'Api\ParentController@timetableDay');
            $api->get('subject_list', 'Api\ParentController@subjectList');
            $api->get('borrowed_books', 'Api\ParentController@borrowedBooks');
            $api->get('marks', 'Api\ParentController@marks');
            $api->get('attendances', 'Api\ParentController@attendances');
            $api->get('invoices', 'Api\ParentController@invoices');
            $api->get('children', 'Api\ParentController@children');
            $api->get('notices', 'Api\ParentController@notices');
            $api->get('exams', 'Api\ParentController@exams');
            $api->get('diary', 'Api\ParentController@diary');
            $api->get('diary_date', 'Api\ParentController@diaryForDate');
            $api->get('exam_subject', 'Api\ParentController@examForSubject');
            $api->get('exam_group', 'Api\ParentController@examForGroup');
            $api->get('student_for_user', 'Api\ParentController@studentForUser');
            $api->get('student_subject', 'Api\ParentController@studentSubject');
            $api->get('exam_marks', 'Api\ParentController@examMarks');
            $api->get('exam_marks_details', 'Api\ParentController@examMarksDetails');
            $api->get('applying_leave', 'Api\ParentController@applyingLeave');
            $api->post('post_applying_leave', 'Api\ParentController@postApplyingLeave');
            $api->post('delete_applying_leave', 'Api\ParentController@deleteApplyingLeave');
            $api->get('fee_details', 'Api\ParentController@feeDetails');
            $api->get('schools', 'Api\ParentController@schools');

        });

        //routes for teacher
        $api->group(array('prefix' => 'teacher', 'middleware' => 'api.teacher'), function ($api) {
            $api->get('school_year_group', 'Api\TeacherController@selectSchoolYearGroup');
            $api->get('school_years', 'Api\TeacherController@schoolYears');
            $api->get('timetable', 'Api\TeacherController@timetable');
            $api->get('timetable_group', 'Api\TeacherController@timetableGroup');
            $api->get('timetable_day', 'Api\TeacherController@timetableDay');
            $api->get('timetable_group_day', 'Api\TeacherController@timetableGroupDay');
            $api->get('subject_list', 'Api\TeacherController@subjectList');
            $api->get('subject_list_group', 'Api\TeacherController@subjectListGroup');
            $api->get('groups', 'Api\TeacherController@groups');
            $api->get('notices', 'Api\TeacherController@notices');
            $api->post('post_notice', 'Api\TeacherController@postNotice');
            $api->post('edit_notice', 'Api\TeacherController@editNotice');
            $api->post('delete_notice', 'Api\TeacherController@deleteNotice');
            $api->get('exams', 'Api\TeacherController@exams');
            $api->post('post_exam', 'Api\TeacherController@postExam');
            $api->post('edit_exam', 'Api\TeacherController@editExam');
            $api->post('delete_exam', 'Api\TeacherController@deleteExam');
            $api->get('attendances', 'Api\TeacherController@attendances');
            $api->get('attendances_date', 'Api\TeacherController@attendancesDate');
            $api->get('attendance_hour_list', 'Api\TeacherController@attendanceHourList');
            $api->post('post_attendance', 'Api\TeacherController@postAttendance');
            $api->post('edit_attendance', 'Api\TeacherController@editAttendance');
            $api->post('justify_attendance', 'Api\TeacherController@justifyAttendance');
            $api->post('delete_attendance', 'Api\TeacherController@deleteAttendance');
            $api->get('marks', 'Api\TeacherController@marks');
            $api->get('marks_date', 'Api\TeacherController@marksDate');
            $api->post('post_mark', 'Api\TeacherController@postMark');
            $api->post('edit_mark', 'Api\TeacherController@editMark');
            $api->post('delete_mark', 'Api\TeacherController@deleteMark');
            $api->get('students', 'Api\TeacherController@students');
            $api->get('borrowed_books', 'Api\TeacherController@borrowedBooks');
            $api->get('diary', 'Api\TeacherController@diary');
            $api->get('diary_date', 'Api\TeacherController@diaryForDate');
            $api->post('post_diary', 'Api\TeacherController@postDairy');
            $api->post('edit_diary', 'Api\TeacherController@editDairy');
            $api->post('delete_diary', 'Api\TeacherController@deleteDairy');
            $api->get('exam_marks', 'Api\TeacherController@examMarks');
            $api->get('exam_marks_details', 'Api\TeacherController@examMarksDetails');
            $api->get('applying_leave', 'Api\TeacherController@applyingLeave');
            $api->get('subjects','Api\TeacherController@subjects');
            $api->get('subject_exams','Api\TeacherController@subjectExams');
            $api->get('hours','Api\TeacherController@hours');
            $api->get('schools', 'Api\TeacherController@schools');
        });

        //routes for librarian
        $api->group(array('prefix' => 'librarian', 'middleware' => 'api.librarian'), function ($api) {
            $api->get('borrowed_books', 'Api\LibrarianController@borrowedBooks');
            $api->post('edit_book', 'Api\LibrarianController@editBook');
            $api->post('add_book', 'Api\LibrarianController@addBook');
            $api->post('delete_book', 'Api\LibrarianController@deleteBook');
            $api->get('reserved_books', 'Api\LibrarianController@reservedBooks');
            $api->post('delete_reserved_book', 'Api\LibrarianController@deleteReserveBook');
            $api->post('issue_reserved_book', 'Api\LibrarianController@issueReservedBook');
            $api->post('issue_book', 'Api\LibrarianController@issueBook');
            $api->post('return_book', 'Api\LibrarianController@returnBook');
            $api->get('subject_list', 'Api\LibrarianController@subjectList');
            $api->get('user_list', 'Api\LibrarianController@userList');
        });

    });
});