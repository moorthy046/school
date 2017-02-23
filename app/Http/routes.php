<?php

/****************   Model binding into route **************************/

Route::model('schoolyear', 'App\Models\SchoolYear');
Route::model('user', 'App\Models\User');
Route::model('teacher_user', 'App\Models\User');
Route::model('human_resource', 'App\Models\User');
Route::model('school_admin', 'App\Models\User');
Route::model('librarian_user', 'App\Models\User');
Route::model('student_user', 'App\Models\User');
Route::model('parent_user', 'App\Models\User');
Route::model('visitor', 'App\Models\User');
Route::model('section', 'App\Models\Section');
Route::model('studentgroup', 'App\Models\StudentGroup');
Route::model('student', 'App\Models\Student');
Route::model('subject', 'App\Models\Subject');
Route::model('transportation', 'App\Models\Transportation');
Route::model('invoice', 'App\Models\Invoice');
Route::model('payment', 'App\Models\Payment');
Route::model('message', 'App\Models\Message');
Route::model('notification', 'App\Models\Notification');
Route::model('notice', 'App\Models\Notice');
Route::model('exam', 'App\Models\Exam');
Route::model('feedback', 'App\Models\Feedback');
Route::model('semester', 'App\Models\Semester');
Route::model('dormitory', 'App\Models\Dormitory');
Route::model('dormitoryroom', 'App\Models\DormitoryRoom');
Route::model('dormitorybed', 'App\Models\DormitoryBed');
Route::model('marktype', 'App\Models\MarkType');
Route::model('markvalue', 'App\Models\MarkValue');
Route::model('noticetype', 'App\Models\NoticeType');
Route::model('setting', 'App\Models\Setting');
Route::model('direction', 'App\Models\Direction');
Route::model('book', 'App\Models\Book');
Route::model('parentstudent', 'App\Models\ParentStudent');
Route::model('bookuser', 'App\Models\BookUser');
Route::model('diary', 'App\Models\Diary');
Route::model('behavior', 'App\Models\Behavior');
Route::model('applyingleave', 'App\Models\ApplyingLeave');
Route::model('option', 'App\Models\Option');
Route::model('page', 'App\Models\Page');
Route::model('certificate', 'App\Models\Certificate');
Route::model('sms_message', 'App\Models\SmsMessage');
Route::model('salary', 'App\Models\Salary');
Route::model('scholarship', 'App\Models\Scholarship');

Route::pattern('slug', '[a-z0-9-]+');
Route::pattern('version', '[0-9.]+');

/******************   APP routes  ********************************/

//default route - homepage for all roles
Route::get('/', 'Secure\SecureController@showHome');
Route::post('events', 'Secure\SecureController@events');
Route::get('language/setlang/{slug}', 'Secure\LanguageController@setlang');

//route after user login into system
Route::get('signin', 'Secure\AuthController@getSignin');
Route::post('signin', 'Secure\AuthController@postSignin');
Route::post('signup', 'Secure\AuthController@postSignup');
Route::get('register', 'Secure\AuthController@getSignup');
Route::get('forgot', 'Secure\AuthController@getForgotPassword');
Route::post('password', 'Secure\AuthController@postForgotPassword');
Route::get('forgot-password/{passwordResetCode}', 'Secure\AuthController@getForgotPasswordConfirm');
Route::post('forgot-password/{passwordResetCode}', 'Secure\AuthController@postForgotPasswordConfirm');
Route::get('logout', 'Secure\AuthController@getLogout');
Route::get('activate/{activationCode}', 'Secure\AuthController@getActivate');
Route::get('page/{page?}', 'Frontend\PageController@show');

Route::group(array('middleware' => ['sentinel', 'xss_protection']), function () {

    Route::get('profile', 'Secure\AuthController@getProfile');
    Route::get('account', 'Secure\AuthController@getAccount');
    Route::post('account', 'Secure\AuthController@postAccount');
    Route::post('webcam', 'Secure\AuthController@postWebcam');
    Route::get('my_certificate', 'Secure\AuthController@getCertificate');

    Route::get('account', 'Secure\AuthController@getAccount');
    Route::post('account', 'Secure\AuthController@postAccount');
    Route::get('setyear/{id}', 'Secure\SecureController@setYear');
    Route::get('setschool/{id}', 'Secure\SecureController@setSchool');
    Route::get('setgroup/{id}', 'Secure\SecureController@setGroup');
    Route::get('setstudent/{id}', 'Secure\SecureController@setStudent');

    Route::get('mailbox', 'Secure\MailboxController@index');
    Route::get('mailbox/all', 'Secure\MailboxController@getData');
    Route::get('mailbox/{id}/get', 'Secure\MailboxController@getMail');
    Route::post('mailbox/{id}/reply', 'Secure\MailboxController@postReply');
    Route::get('mailbox/data', 'Secure\MailboxController@getAllData');
    Route::get('mailbox/received', 'Secure\MailboxController@getReceived');
    Route::post('mailbox/send', 'Secure\MailboxController@sendEmail');
    Route::get('mailbox/sent', 'Secure\MailboxController@getSent');
    Route::post('mailbox/mark-as-read', 'Secure\MailboxController@postMarkAsRead');
    Route::post('mailbox/delete', 'Secure\MailboxController@postDelete');

    Route::get('notification/all', 'Secure\NotificationController@getAllData');
    Route::get('notification/data', 'Secure\NotificationController@data');
    Route::get('notification/{notification}/show', 'Secure\NotificationController@show');
    Route::get('notification/{notification}/edit', 'Secure\NotificationController@edit');
    Route::get('notification/{notification}/delete', 'Secure\NotificationController@delete');
    Route::resource('notification', 'Secure\NotificationController');

    Route::group(['prefix' => 'feedback'], function () {
        Route::get('data', 'Secure\FeedbackController@data');
        Route::get('{feedback}/delete', 'Secure\FeedbackController@delete');
        Route::get('{feedback}/show', 'Secure\FeedbackController@show');
    }
    );
    Route::resource('feedback', 'Secure\FeedbackController');

    Route::get('report/{user}/forstudent', 'Secure\ReportController@student');
    Route::post('report', 'Secure\ReportController@create');

    Route::get('diary/data', 'Secure\DairyController@data');
    Route::get('diary/{diary}/show', 'Secure\DairyController@show');
    Route::resource('diary', 'Secure\DairyController');

    Route::group(['prefix' => 'schools'], function () {
        Route::get('data', 'Secure\SchoolController@data');
        Route::get('{school}/edit', 'Secure\SchoolController@edit');
        Route::put('{school}', 'Secure\SchoolController@update');
        Route::get('{school}/delete', 'Secure\SchoolController@delete');
        Route::delete('{school}', 'Secure\SchoolController@destroy');
        Route::get('{school}/show', 'Secure\SchoolController@show');
    }
    );
    Route::resource('schools', 'Secure\SchoolController');

    Route::get('visitor_card/{user}', 'Secure\VisitorStudentCardController@visitor');
    Route::get('student_card/{user}', 'Secure\VisitorStudentCardController@student');

    //route for admin users
    Route::group(array('has_any_role' => 'admin,super_admin'), function () {

        Route::group(['prefix' => 'login_history'], function () {
            Route::get('data', 'Secure\LoginHistoryController@data');
        }
        );
        Route::resource('login_history', 'Secure\LoginHistoryController');

        Route::group(['prefix' => 'schoolyear'], function () {
            Route::get('data', 'Secure\SchoolYearController@data');
            Route::get('{schoolyear}/delete', 'Secure\SchoolYearController@delete');
            Route::get('{schoolyear}/show', 'Secure\SchoolYearController@show');
            Route::get('{schoolyear}/get_sections', 'Secure\SchoolYearController@getSections');
            Route::get('{schoolyear}/copy_data', 'Secure\SchoolYearController@copyData');
            Route::post('{schoolyear}/post_data', 'Secure\SchoolYearController@postData');
        }
        );
        Route::resource('schoolyear', 'Secure\SchoolYearController');

        Route::group(['prefix' => 'subject'], function () {
            Route::get('data', 'Secure\SubjectController@data');
            Route::get('{subject}/delete', 'Secure\SubjectController@delete');
            Route::get('{subject}/show', 'Secure\SubjectController@show');
            Route::get('{subject}/edit', 'Secure\SubjectController@edit');
        }
        );
        Route::resource('subject', 'Secure\SubjectController');

        Route::group(['prefix' => 'pages'], function () {
            Route::get('data', 'Secure\PageController@data');
            Route::get('{page}/delete', 'Secure\PageController@delete');
            Route::get('{page}/show', 'Secure\PageController@show');
            Route::get('{page}/edit', 'Secure\PageController@edit');
            Route::put('{page}', 'Secure\PageController@update');
        });
        Route::resource('pages', 'Secure\PageController');

        Route::group(['prefix' => 'sms_message'], function () {
            Route::get('data', 'Secure\SmsMessageController@data');
            Route::get('{sms_message}/delete', 'Secure\SmsMessageController@delete');
            Route::get('{sms_message}/show', 'Secure\SmsMessageController@show');
            Route::get('{sms_message}/edit', 'Secure\SmsMessageController@edit');
        }
        );
        Route::resource('sms_message', 'Secure\SmsMessageController');

        Route::group(['prefix' => 'certificate'], function () {
            Route::get('data', 'Secure\CertificateController@data');
            Route::get('{certificate}/delete', 'Secure\CertificateController@delete');
            Route::get('{certificate}/show', 'Secure\CertificateController@show');
            Route::get('{certificate}/edit', 'Secure\CertificateController@edit');
            Route::get('{certificate}/user', 'Secure\CertificateController@user');
            Route::put('{certificate}/addusers', 'Secure\CertificateController@addusers');
        });
        Route::resource('certificate', 'Secure\CertificateController');

        Route::post('student_final_mark/add-final-mark', 'Secure\StudentFinalMarkController@addFinalMark');
        Route::get('student_final_mark/{section}/get-groups', 'Secure\StudentFinalMarkController@getGroups');
        Route::get('student_final_mark/{studentgroup}/get-subjects', 'Secure\StudentFinalMarkController@getSubjects');
        Route::get('student_final_mark/{studentgroup}/{subject}/get-students', 'Secure\StudentFinalMarkController@getStudents');
        Route::resource('student_final_mark', 'Secure\StudentFinalMarkController');

        Route::get('semester/data', 'Secure\SemesterController@data');
        Route::get('semester/{semester}/show', 'Secure\SemesterController@show');
        Route::get('semester/{semester}/edit', 'Secure\SemesterController@edit');
        Route::get('semester/{semester}/delete', 'Secure\SemesterController@delete');
        Route::resource('semester', 'Secure\SemesterController');

        Route::get('behavior/data', 'Secure\BehaviorController@data');
        Route::get('behavior/{behavior}/show', 'Secure\BehaviorController@show');
        Route::get('behavior/{behavior}/edit', 'Secure\BehaviorController@edit');
        Route::get('behavior/{behavior}/delete', 'Secure\BehaviorController@delete');
        Route::resource('behavior', 'Secure\BehaviorController');

        Route::get('dormitory/data', 'Secure\DormitoryController@data');
        Route::get('dormitory/{dormitory}/show', 'Secure\DormitoryController@show');
        Route::get('dormitory/{dormitory}/edit', 'Secure\DormitoryController@edit');
        Route::get('dormitory/{dormitory}/delete', 'Secure\DormitoryController@delete');
        Route::resource('dormitory', 'Secure\DormitoryController');

        Route::get('dormitoryroom/data', 'Secure\DormitoryRoomController@data');
        Route::get('dormitoryroom/{dormitoryroom}/show', 'Secure\DormitoryRoomController@show');
        Route::get('dormitoryroom/{dormitoryroom}/edit', 'Secure\DormitoryRoomController@edit');
        Route::get('dormitoryroom/{dormitoryroom}/delete', 'Secure\DormitoryRoomController@delete');
        Route::resource('dormitoryroom', 'Secure\DormitoryRoomController');

        Route::get('dormitorybed/data', 'Secure\DormitoryBedController@data');
        Route::get('dormitorybed/{dormitorybed}/show', 'Secure\DormitoryBedController@show');
        Route::get('dormitorybed/{dormitorybed}/edit', 'Secure\DormitoryBedController@edit');
        Route::get('dormitorybed/{dormitorybed}/delete', 'Secure\DormitoryBedController@delete');
        Route::resource('dormitorybed', 'Secure\DormitoryBedController');

        Route::get('markvalue/data', 'Secure\MarkValueController@data');
        Route::get('markvalue/{markvalue}/show', 'Secure\MarkValueController@show');
        Route::get('markvalue/{markvalue}/edit', 'Secure\MarkValueController@edit');
        Route::get('markvalue/{markvalue}/delete', 'Secure\MarkValueController@delete');
        Route::resource('markvalue', 'Secure\MarkValueController');

        Route::get('marktype/data', 'Secure\MarkTypeController@data');
        Route::get('marktype/{marktype}/show', 'Secure\MarkTypeController@show');
        Route::get('marktype/{marktype}/edit', 'Secure\MarkTypeController@edit');
        Route::get('marktype/{marktype}/delete', 'Secure\MarkTypeController@delete');
        Route::resource('marktype', 'Secure\MarkTypeController');

        Route::get('noticetype/data', 'Secure\NoticeTypeController@data');
        Route::get('noticetype/{noticetype}/show', 'Secure\NoticeTypeController@show');
        Route::get('noticetype/{noticetype}/edit', 'Secure\NoticeTypeController@edit');
        Route::get('noticetype/{noticetype}/delete', 'Secure\NoticeTypeController@delete');
        Route::resource('noticetype', 'Secure\NoticeTypeController');

        Route::get('setting', 'Secure\SettingController@index');
        Route::post('setting', 'Secure\SettingController@store');

        Route::group(['prefix' => 'option'], function () {
            Route::get('data/{slug2}', 'Secure\OptionController@data');
            Route::get('data', 'Secure\OptionController@data');
            Route::get('{option}/show', 'Secure\OptionController@show');
            Route::get('{option}/delete', 'Secure\OptionController@delete');
        }
        );
        Route::resource('option', 'Secure\OptionController');

        Route::get('direction/data', 'Secure\DirectionController@data');
        Route::get('direction/{direction}/show', 'Secure\DirectionController@show');
        Route::get('direction/{direction}/edit', 'Secure\DirectionController@edit');
        Route::get('direction/{direction}/delete', 'Secure\DirectionController@delete');
        Route::resource('direction', 'Secure\DirectionController');

        Route::get('section/data', 'Secure\SectionController@data');
        Route::get('section/{section}/show', 'Secure\SectionController@show');
        Route::get('section/{section}/edit', 'Secure\SectionController@edit');
        Route::get('section/{section}/delete', 'Secure\SectionController@delete');
        Route::get('section/{section}/students', 'Secure\SectionController@students');
        Route::get('section/{section}/studentsdata', 'Secure\SectionController@students_data');
        Route::get('section/{section}/groups', 'Secure\SectionController@groups');
        Route::get('section/{section}/groupsdata', 'Secure\SectionController@groups_data');
        Route::resource('section', 'Secure\SectionController');

        Route::group(['prefix' => 'school_admin'], function () {
            Route::get('data', 'Secure\SchoolAdminController@data');
            Route::get('{school_admin}/edit', 'Secure\SchoolAdminController@edit');
            Route::get('{school_admin}/delete', 'Secure\SchoolAdminController@delete');
            Route::get('{school_admin}/show', 'Secure\SchoolAdminController@show');
        }
        );
        Route::resource('school_admin', 'Secure\SchoolAdminController');

        Route::group(['prefix' => 'visitor'], function () {
            Route::get('data', 'Secure\VisitorController@data');
            Route::get('{visitor}/show', 'Secure\VisitorController@show');
        }
        );
        Route::resource('visitor', 'Secure\VisitorController');

        Route::get('studentgroup/{section}/create', 'Secure\StudentGroupController@create');
        Route::get('studentgroup/duration', 'Secure\StudentGroupController@getDuration');
        Route::get('studentgroup/{section}/{studentgroup}/show', 'Secure\StudentGroupController@show');
        Route::get('studentgroup/{section}/{studentgroup}/edit', 'Secure\StudentGroupController@edit');
        Route::get('studentgroup/{section}/{studentgroup}/delete', 'Secure\StudentGroupController@delete');
        Route::get('studentgroup/{section}/{studentgroup}/students', 'Secure\StudentGroupController@students');
        Route::put('studentgroup/{section}/{studentgroup}/addstudents', 'Secure\StudentGroupController@addstudents');
        Route::get('studentgroup/{section}/{studentgroup}/subjects', 'Secure\StudentGroupController@subjects');
        Route::put('studentgroup/{subject}/{studentgroup}/addeditsubject', 'Secure\StudentGroupController@addeditsubject');
        Route::get('studentgroup/{section}/{studentgroup}/timetable', 'Secure\StudentGroupController@timetable');
        Route::post('studentgroup/{section}/{studentgroup}/addtimetable', 'Secure\StudentGroupController@addtimetable');
        Route::delete('studentgroup/{section}/{studentgroup}/deletetimetable', 'Secure\StudentGroupController@deletetimetable');
        Route::resource('studentgroup', 'Secure\StudentGroupController');

        Route::get('teacher/data', 'Secure\TeacherController@data');
        Route::get('teacher/{teacher_user}/show', 'Secure\TeacherController@show');
        Route::get('teacher/{teacher_user}/edit', 'Secure\TeacherController@edit');
        Route::get('teacher/{teacher_user}/delete', 'Secure\TeacherController@delete');
        Route::resource('teacher', 'Secure\TeacherController');

        Route::get('librarian/data', 'Secure\LibrarianController@data');
        Route::get('librarian/{librarian_user}/show', 'Secure\LibrarianController@show');
        Route::get('librarian/{librarian_user}/edit', 'Secure\LibrarianController@edit');
        Route::get('librarian/{librarian_user}/delete', 'Secure\LibrarianController@delete');
        Route::resource('librarian', 'Secure\LibrarianController');

        Route::get('transportation/data', 'Secure\TransportationController@data');
        Route::get('transportation/{transportation}/show', 'Secure\TransportationController@show');
        Route::get('transportation/{transportation}/edit', 'Secure\TransportationController@edit');
        Route::get('transportation/{transportation}/delete', 'Secure\TransportationController@delete');
        Route::resource('transportation', 'Secure\TransportationController');

        Route::get('invoice/data', 'Secure\InvoiceController@data');
        Route::get('invoice/{invoice}/show', 'Secure\InvoiceController@show');
        Route::get('invoice/{invoice}/edit', 'Secure\InvoiceController@edit');
        Route::get('invoice/{invoice}/delete', 'Secure\InvoiceController@delete');
        Route::resource('invoice', 'Secure\InvoiceController');

        Route::get('payment/data', 'Secure\PaymentController@data');
        Route::get('payment/{payment}/show', 'Secure\PaymentController@show');
        Route::get('payment/{payment}/edit', 'Secure\PaymentController@edit');
        Route::get('payment/{payment}/delete', 'Secure\PaymentController@delete');
        Route::resource('payment', 'Secure\PaymentController');

        Route::group(['prefix' => 'salary'], function () {
            Route::get('data', 'Secure\SalaryController@data');
            Route::get('{salary}/delete', 'Secure\SalaryController@delete');
            Route::get('{salary}/show', 'Secure\SalaryController@show');
            Route::get('{salary}/edit', 'Secure\SalaryController@edit');
        });
        Route::resource('salary', 'Secure\SalaryController');

        Route::group(['prefix' => 'scholarship'], function () {
            Route::get('data', 'Secure\ScholarshipController@data');
            Route::get('{scholarship}/delete', 'Secure\ScholarshipController@delete');
            Route::get('{scholarship}/show', 'Secure\ScholarshipController@show');
            Route::get('{scholarship}/edit', 'Secure\ScholarshipController@edit');
        });
        Route::resource('scholarship', 'Secure\ScholarshipController');


    });
    Route::group(array('has_any_role' => 'human_resource,admin,super_admin'), function () {
        Route::group(['prefix' => 'student'], function () {
            Route::get('data', 'Secure\StudentController@data');
            Route::get('{student}/delete', 'Secure\StudentController@delete');
            Route::get('{student}/show', 'Secure\StudentController@show');
        });
        Route::resource('student', 'Secure\StudentController');

        Route::group(['prefix' => 'parent'], function () {
            Route::get('data', 'Secure\ParentController@data');
            Route::get('{parentstudent}/edit', 'Secure\ParentController@edit');
            Route::get('{parentstudent}/delete', 'Secure\ParentController@delete');
            Route::get('{parentstudent}/show', 'Secure\ParentController@show');});
        Route::resource('parent', 'Secure\ParentController');

        Route::group(['prefix' => 'human_resource'], function () {
            Route::get('data', 'Secure\HumanResourceController@data');
            Route::get('{human_resource}/edit', 'Secure\HumanResourceController@edit');
            Route::get('{human_resource}/delete', 'Secure\HumanResourceController@delete');
            Route::get('{human_resource}/show', 'Secure\HumanResourceController@show');});
        Route::resource('human_resource', 'Secure\HumanResourceController');

        Route::group(['prefix' => 'staff_attendance'], function () {
            Route::post('attendance', 'Secure\StaffAttendanceController@attendanceForDate');
            Route::post('delete', 'Secure\StaffAttendanceController@deleteattendance');
            Route::post('add', 'Secure\StaffAttendanceController@addAttendance');
        });
        Route::resource('staff_attendance', 'Secure\StaffAttendanceController');

    });

    Route::group(array('has_any_role' => 'teacher,student,parent'), function () {

        Route::get('bookuser/index', 'Secure\BookUserController@index');
        Route::get('bookuser/data', 'Secure\BookUserController@data');
        Route::get('bookuser/{book}/reserve', 'Secure\BookUserController@reserve');

        Route::get('borrowedbook/index', 'Secure\BorrowedBookController@index');
        Route::get('borrowedbook/data', 'Secure\BorrowedBookController@data');

        Route::get('report/{user}/subjectbook', 'Secure\ReportController@subjectbook');
        Route::get('report/{user}/getSubjectBook', 'Secure\ReportController@getSubjectBook');

    });

    //route for teacher and admin users
    Route::group(array('has_any_role' => 'teacher,admin'), function () {
        Route::get('notice/data', 'Secure\NoticeController@data');
        Route::get('notice/{notice}/show', 'Secure\NoticeController@show');
        Route::get('notice/{notice}/edit', 'Secure\NoticeController@edit');
        Route::get('notice/{notice}/delete', 'Secure\NoticeController@delete');
        Route::resource('notice', 'Secure\NoticeController');
    });

    //route for teacher and parent users
    Route::group(array('has_any_role' => 'teacher,parent'), function () {
        Route::get('applyingleave/data', 'Secure\ApplyingLeaveController@data');
        Route::get('applyingleave/{applyingleave}/edit', 'Secure\ApplyingLeaveController@edit');
        Route::get('applyingleave/{applyingleave}/delete', 'Secure\ApplyingLeaveController@delete');
        Route::get('applyingleave/{applyingleave}/show', 'Secure\ApplyingLeaveController@show');
        Route::resource('applyingleave', 'Secure\ApplyingLeaveController');
    }
    );

    //route for teacher users
    Route::group(array('middleware' => 'teacher'), function () {
        Route::get('teachergroup/data', 'Secure\TeacherGroupController@data');
        Route::get('teachergroup/timetable', 'Secure\TeacherGroupController@timetable');
        Route::get('teachergroup/{studentgroup}/show', 'Secure\TeacherGroupController@show');
        Route::get('teachergroup/{studentgroup}/students', 'Secure\TeacherGroupController@students');
        Route::put('teachergroup/{studentgroup}/addstudents', 'Secure\TeacherGroupController@addstudents');
        Route::get('teachergroup/{studentgroup}/subjects', 'Secure\TeacherGroupController@subjects');
        Route::put('teachergroup/{studentgroup}/addeditsubject', 'Secure\TeacherGroupController@addeditsubject');
        Route::get('teachergroup/{studentgroup}/grouptimetable', 'Secure\TeacherGroupController@grouptimetable');
        Route::post('teachergroup/addtimetable', 'Secure\TeacherGroupController@addtimetable');
        Route::delete('teachergroup/deletetimetable', 'Secure\TeacherGroupController@deletetimetable');
        Route::resource('teachergroup', 'Secure\TeacherGroupController');

        Route::get('diary/{diary}/edit', 'Secure\DairyController@edit');
        Route::get('diary/{diary}/delete', 'Secure\DairyController@delete');

        Route::get('exam/data', 'Secure\ExamController@data');
        Route::get('exam/{exam}/show', 'Secure\ExamController@show');
        Route::get('exam/{exam}/edit', 'Secure\ExamController@edit');
        Route::get('exam/{exam}/delete', 'Secure\ExamController@delete');
        Route::resource('exam', 'Secure\ExamController');

        Route::get('teacherstudent/data', 'Secure\TeacherStudentController@data');
        Route::get('teacherstudent/{student}/behavior', 'Secure\TeacherStudentController@behavior');
        Route::post('teacherstudent/{student}/changebehavior', 'Secure\TeacherStudentController@change_behavior');
        Route::get('teacherstudent/{student}/show', 'Secure\TeacherStudentController@show');
        Route::resource('teacherstudent', 'Secure\TeacherStudentController');

        Route::post('mark/exams', 'Secure\MarkController@examsForSubject');
        Route::post('mark/marks', 'Secure\MarkController@marksForSubjectAndDate');
        Route::post('mark/delete', 'Secure\MarkController@deletemark');
        Route::post('mark/add', 'Secure\MarkController@addmark');
        Route::resource('mark', 'Secure\MarkController');

        Route::post('attendance/attendance', 'Secure\AttendanceController@attendanceForDate');
        Route::post('attendance/hoursfordate', 'Secure\AttendanceController@hoursForDate');
        Route::post('attendance/delete', 'Secure\AttendanceController@deleteattendance');
        Route::post('attendance/add', 'Secure\AttendanceController@addAttendance');
        Route::post('attendance/justified', 'Secure\AttendanceController@justified');
        Route::resource('attendance', 'Secure\AttendanceController');

        Route::get('report/index', 'Secure\ReportController@index');

        Route::get('transportteacher/data', 'Secure\TransportationController@data');
        Route::get('transportteacher', 'Secure\TransportationController@index');
        Route::get('transportteacher/{transportation}/show', 'Secure\TransportationController@show');

    });

    //route for student and parent users
    Route::group(array('has_any_role' => 'student,parent'), function () {

        Route::get('studentsection/timetable', 'Secure\StudentSectionController@timetable');
        Route::get('studentsection/payment', 'Secure\StudentSectionController@payment');
        Route::get('studentsection/data', 'Secure\StudentSectionController@data');

        Route::get('report/{user}/marks', 'Secure\ReportController@marks');
        Route::get('report/{user}/attendances', 'Secure\ReportController@attendances');
        Route::get('report/{user}/notice', 'Secure\ReportController@notice');
        Route::post('report/{user}/studentsubjects', 'Secure\ReportController@getStudentSubjects');
        Route::post('report/{user}/semesters', 'Secure\ReportController@semesters');
        Route::get('report/{user}/marksforsubject', 'Secure\ReportController@marksForSubject');
        Route::get('report/{user}/attendancesforsubject', 'Secure\ReportController@attendancesForSubject');
        Route::get('report/{user}/noticeforsubject', 'Secure\ReportController@noticesForSubject');
        Route::get('report/{user}/exams', 'Secure\ReportController@exams');
        Route::get('report/{user}/examforsubject', 'Secure\ReportController@examForSubject');

    }
    );

    //route for student user
    Route::group(array('middleware' => 'student'), function () {
        Route::get('transportstudent/data', 'Secure\TransportationController@data');
        Route::get('transportstudent', 'Secure\TransportationController@index');
        Route::get('transportstudent/{transportation}/show', 'Secure\TransportationController@show');
    }
    );

    //route for parent users
    Route::group(array('middleware' => 'parent'), function () {
        Route::get('parentsection', 'Secure\ParentSectionController@index');
        Route::get('parentsection/data', 'Secure\ParentSectionController@data');

        Route::get('payment/{invoice}/pay', 'Secure\PaymentController@pay');
        Route::post('payment/{invoice}/paypal', 'Secure\PaymentController@paypalPayment');
        Route::post('payment/{invoice}/stripe', 'Secure\PaymentController@stripe');
        Route::get('payment/{invoice}/paypal_success', 'Secure\PaymentController@paypalSuccess');
        Route::get('payment/{invoice}/paypal_cancel', function () {
            return Redirect::to('/');
        });

        Route::get('studentsection/invoice', 'Secure\StudentSectionController@invoice');

        Route::get('transportparent/data', 'Secure\TransportationController@data');
        Route::get('transportparent', 'Secure\TransportationController@index');
        Route::get('transportparent/{transportation}/show', 'Secure\TransportationController@show');
    }
    );

    //route for librarians
    Route::group(array('middleware' => 'librarian'), function () {
        Route::get('book/data', 'Secure\BookController@data');
        Route::get('book/{book}/show', 'Secure\BookController@show');
        Route::get('book/{book}/edit', 'Secure\BookController@edit');
        Route::get('book/{book}/delete', 'Secure\BookController@delete');
        Route::resource('book', 'Secure\BookController');

        Route::get('reservedbook/data', 'Secure\ReservedBookController@data');
        Route::get('reservedbook/{bookuser}/show', 'Secure\ReservedBookController@show');
        Route::get('reservedbook/{bookuser}/delete', 'Secure\ReservedBookController@delete');
        Route::get('reservedbook/{bookuser}/issue', 'Secure\ReservedBookController@issue');
        Route::resource('reservedbook', 'Secure\ReservedBookController');

        Route::get('booklibrarian/issuebook/{user}/{book}', 'Secure\BookLibrarianController@issueBook');
        Route::post('booklibrarian/getusers', 'Secure\BookLibrarianController@getUsers');
        Route::get('booklibrarian/issuereturn/{user}', 'Secure\BookLibrarianController@issueReturnBook');
        Route::get('booklibrarian/return/{bookuser}', 'Secure\BookLibrarianController@returnBook');
        Route::post('booklibrarian/getbooks', 'Secure\BookLibrarianController@getBooks');
        Route::get('booklibrarian/book/{book}', 'Secure\BookLibrarianController@getBook');
        Route::get('booklibrarian', 'Secure\BookLibrarianController@index');

    }
    );
}
);

/**
 * Installation
 */
Route::group(['prefix' => 'install'], function () {
    Route::get('', 'InstallController@index');
    Route::get('requirements', 'InstallController@requirements');
    Route::get('permissions', 'InstallController@permissions');
    Route::get('database', 'InstallController@database');
    Route::get('start-installation', 'InstallController@installation');
    Route::post('start-installation', 'InstallController@installation');
    Route::get('install', 'InstallController@install');
    Route::post('install', 'InstallController@install');
    Route::get('settings', 'InstallController@settings');
    Route::post('settings', 'InstallController@settingsSave');
    Route::get('email_settings', 'InstallController@settingsEmail');
    Route::post('email_settings', 'InstallController@settingsEmailSave');
    Route::get('complete', 'InstallController@complete');
    Route::get('error', 'InstallController@error');
});

/**
 * Update
 */
Route::group(['prefix' => 'update/{version}'], function () {
    Route::get('', 'UpdateController@index');
    Route::post('start-update', 'UpdateController@update');
    Route::get('complete', 'UpdateController@complete');
    Route::get('error', 'UpdateController@error');
});