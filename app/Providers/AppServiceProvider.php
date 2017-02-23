<?php

namespace App\Providers;

use App\Models\Certificate;
use App\Models\LoginHistory;
use App\Models\Page;
use App\Models\Salary;
use App\Models\Scholarship;
use App\Models\School;
use App\Models\SmsMessage;
use App\Models\StaffAttendance;
use App\Models\StudentFinalMark;
use App\Repositories\CertificateRepository;
use App\Repositories\CertificateRepositoryEloquent;
use App\Repositories\LoginHistoryRepository;
use App\Repositories\LoginHistoryRepositoryEloquent;
use App\Repositories\PageRepository;
use App\Repositories\PageRepositoryEloquent;
use App\Repositories\SalaryRepository;
use App\Repositories\SalaryRepositoryEloquent;
use App\Repositories\ScholarshipRepository;
use App\Repositories\ScholarshipRepositoryEloquent;
use App\Repositories\SchoolRepository;
use App\Repositories\SchoolRepositoryEloquent;
use App\Repositories\SmsMessageRepository;
use App\Repositories\SmsMessageRepositoryEloquent;
use App\Repositories\StaffAttendanceRepository;
use App\Repositories\StaffAttendanceRepositoryEloquent;
use App\Repositories\StudentFinalMarkRepository;
use App\Repositories\StudentFinalMarkRepositoryEloquent;
use Illuminate\Support\ServiceProvider;
use App\Models\ApplyingLeave;
use App\Models\Attendance;
use App\Models\Behavior;
use App\Models\Book;
use App\Models\BookUser;
use App\Models\Diary;
use App\Models\Direction;
use App\Models\Dormitory;
use App\Models\DormitoryBed;
use App\Models\DormitoryRoom;
use App\Models\Exam;
use App\Models\Feedback;
use App\Models\Invoice;
use App\Models\Mark;
use App\Models\MarkType;
use App\Models\MarkValue;
use App\Models\Message;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\Notification;
use App\Models\Option;
use App\Models\ParentStudent;
use App\Models\Payment;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\Timetable;
use App\Models\Transportation;
use App\Models\User;
use App\Repositories\ApplyingLeaveRepository;
use App\Repositories\ApplyingLeaveRepositoryEloquent;
use App\Repositories\AttendanceRepository;
use App\Repositories\AttendanceRepositoryEloquent;
use App\Repositories\BehaviorRepository;
use App\Repositories\BehaviorRepositoryEloquent;
use App\Repositories\BookRepository;
use App\Repositories\BookRepositoryEloquent;
use App\Repositories\BookUserRepository;
use App\Repositories\BookUserRepositoryEloquent;
use App\Repositories\DiaryRepository;
use App\Repositories\DiaryRepositoryEloquent;
use App\Repositories\DirectionRepository;
use App\Repositories\DirectionRepositoryEloquent;
use App\Repositories\DormitoryBedRepository;
use App\Repositories\DormitoryBedRepositoryEloquent;
use App\Repositories\DormitoryRepository;
use App\Repositories\DormitoryRepositoryEloquent;
use App\Repositories\DormitoryRoomRepository;
use App\Repositories\DormitoryRoomRepositoryEloquent;
use App\Repositories\ExamRepository;
use App\Repositories\ExamRepositoryEloquent;
use App\Repositories\FeedbackRepository;
use App\Repositories\FeedbackRepositoryEloquent;
use App\Repositories\InstallRepository;
use App\Repositories\InstallRepositoryEloquent;
use App\Repositories\InvoiceRepository;
use App\Repositories\InvoiceRepositoryEloquent;
use App\Repositories\MarkRepository;
use App\Repositories\MarkRepositoryEloquent;
use App\Repositories\MarkTypeRepository;
use App\Repositories\MarkTypeRepositoryEloquent;
use App\Repositories\MarkValueRepository;
use App\Repositories\MarkValueRepositoryEloquent;
use App\Repositories\MessageRepository;
use App\Repositories\MessageRepositoryEloquent;
use App\Repositories\NoticeRepository;
use App\Repositories\NoticeRepositoryEloquent;
use App\Repositories\NoticeTypeRepository;
use App\Repositories\NoticeTypeRepositoryEloquent;
use App\Repositories\NotificationRepository;
use App\Repositories\NotificationRepositoryEloquent;
use App\Repositories\OptionRepository;
use App\Repositories\OptionRepositoryEloquent;
use App\Repositories\ParentStudentRepository;
use App\Repositories\ParentStudentRepositoryEloquent;
use App\Repositories\PaymentRepository;
use App\Repositories\PaymentRepositoryEloquent;
use App\Repositories\SchoolYearRepository;
use App\Repositories\SchoolYearRepositoryEloquent;
use App\Repositories\SectionRepository;
use App\Repositories\SectionRepositoryEloquent;
use App\Repositories\SemesterRepository;
use App\Repositories\SemesterRepositoryEloquent;
use App\Repositories\StudentGroupRepository;
use App\Repositories\StudentGroupRepositoryEloquent;
use App\Repositories\StudentRepository;
use App\Repositories\StudentRepositoryEloquent;
use App\Repositories\SubjectRepository;
use App\Repositories\SubjectRepositoryEloquent;
use App\Repositories\TeacherSubjectRepository;
use App\Repositories\TeacherSubjectRepositoryEloquent;
use App\Repositories\TimetableRepository;
use App\Repositories\TimetableRepositoryEloquent;
use App\Repositories\TransportationRepository;
use App\Repositories\TransportationRepositoryEloquent;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryEloquent;
use Cartalyst\Sentinel\Sentinel;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setDbConfigurations();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->register(DropboxServiceProvider::class);

        $this->app->bind(ApplyingLeaveRepository::class, function ($app) {
            return new ApplyingLeaveRepositoryEloquent(new ApplyingLeave());
        });
        $this->app->bind(AttendanceRepository::class, function ($app) {
            return new AttendanceRepositoryEloquent(new Attendance());
        });
        $this->app->bind(BehaviorRepository::class, function ($app) {
            return new BehaviorRepositoryEloquent(new Behavior());
        });
        $this->app->bind(BookRepository::class, function ($app) {
            return new BookRepositoryEloquent(new Book());
        });
        $this->app->bind(BookUserRepository::class, function ($app) {
            return new BookUserRepositoryEloquent(new BookUser());
        });
        $this->app->bind(CertificateRepository::class, function ($app) {
            return new CertificateRepositoryEloquent(new Certificate());
        });
        $this->app->bind(DiaryRepository::class, function ($app) {
            return new DiaryRepositoryEloquent(new Diary());
        });
        $this->app->bind(DirectionRepository::class, function ($app) {
            return new DirectionRepositoryEloquent(new Direction());
        });
        $this->app->bind(DormitoryRepository::class, function ($app) {
            return new DormitoryRepositoryEloquent(new Dormitory());
        });
        $this->app->bind(DormitoryBedRepository::class, function ($app) {
            return new DormitoryBedRepositoryEloquent(new DormitoryBed());
        });
        $this->app->bind(DormitoryRoomRepository::class, function ($app) {
            return new DormitoryRoomRepositoryEloquent(new DormitoryRoom());
        });
        $this->app->bind(ExamRepository::class, function ($app) {
            return new ExamRepositoryEloquent(new Exam());
        });
        $this->app->bind(FeedbackRepository::class, function ($app) {
            return new FeedbackRepositoryEloquent(new Feedback());
        });
        $this->app->bind(InvoiceRepository::class, function ($app) {
            return new InvoiceRepositoryEloquent(new Invoice());
        });
        $this->app->bind(LoginHistoryRepository::class, function ($app) {
            return new LoginHistoryRepositoryEloquent(new LoginHistory());
        });
        $this->app->bind(MarkRepository::class, function ($app) {
            return new MarkRepositoryEloquent(new Mark());
        });
        $this->app->bind(MarkTypeRepository::class, function ($app) {
            return new MarkTypeRepositoryEloquent(new MarkType());
        });
        $this->app->bind(MarkValueRepository::class, function ($app) {
            return new MarkValueRepositoryEloquent(new MarkValue());
        });
        $this->app->bind(MessageRepository::class, function ($app) {
            return new MessageRepositoryEloquent(new Message());
        });
        $this->app->bind(NoticeRepository::class, function ($app) {
            return new NoticeRepositoryEloquent(new Notice());
        });
        $this->app->bind(NoticeTypeRepository::class, function ($app) {
            return new NoticeTypeRepositoryEloquent(new NoticeType());
        });
        $this->app->bind(NotificationRepository::class, function ($app) {
            return new NotificationRepositoryEloquent(new Notification());
        });
        $this->app->bind(OptionRepository::class, function ($app) {
            return new OptionRepositoryEloquent(new Option());
        });
        $this->app->bind(SectionRepository::class, function ($app) {
            return new SectionRepositoryEloquent(new Section());
        });
        $this->app->bind(PageRepository::class, function ($app) {
            return new PageRepositoryEloquent(new Page());
        });
        $this->app->bind(ParentStudentRepository::class, function ($app) {
            return new ParentStudentRepositoryEloquent(new ParentStudent());
        });
        $this->app->bind(PaymentRepository::class, function ($app) {
            return new PaymentRepositoryEloquent(new Payment());
        });
        $this->app->bind(SalaryRepository::class, function ($app) {
            return new SalaryRepositoryEloquent(new Salary());
        });
        $this->app->bind(ScholarshipRepository::class, function ($app) {
            return new ScholarshipRepositoryEloquent(new Scholarship());
        });
        $this->app->bind(SchoolRepository::class, function ($app) {
            return new SchoolRepositoryEloquent(new School());
        });
        $this->app->bind(SchoolYearRepository::class, function ($app) {
            return new SchoolYearRepositoryEloquent(new SchoolYear());
        });
        $this->app->bind(SemesterRepository::class, function ($app) {
            return new SemesterRepositoryEloquent(new Semester());
        });
        $this->app->bind(SmsMessageRepository::class, function ($app) {
            return new SmsMessageRepositoryEloquent(new SmsMessage());
        });
        $this->app->bind(StaffAttendanceRepository::class, function ($app) {
            return new StaffAttendanceRepositoryEloquent(new StaffAttendance());
        });
        $this->app->bind(StudentFinalMarkRepository::class, function ($app) {
            return new StudentFinalMarkRepositoryEloquent(new StudentFinalMark());
        });
        $this->app->bind(StudentGroupRepository::class, function ($app) {
            return new StudentGroupRepositoryEloquent(new StudentGroup());
        });
        $this->app->bind(StudentRepository::class, function ($app) {
            return new StudentRepositoryEloquent(new Student());
        });
        $this->app->bind(SubjectRepository::class, function ($app) {
            return new SubjectRepositoryEloquent(new Subject());
        });
        $this->app->bind(TeacherSubjectRepository::class, function ($app) {
            return new TeacherSubjectRepositoryEloquent(new TeacherSubject());
        });
        $this->app->bind(TimetableRepository::class, function ($app) {
            return new TimetableRepositoryEloquent(new Timetable());
        });
        $this->app->bind(TransportationRepository::class, function ($app) {
            return new TransportationRepositoryEloquent(new Transportation());
        });

        $this->app->bind(InstallRepository::class, function ($app) {
            return new InstallRepositoryEloquent();
        });

        $this->app->bind(UserRepository::class, function ($app) {
            $sentinel = new Sentinel(
                $app['sentinel.persistence'],
                $app['sentinel.users'],
                $app['sentinel.roles'],
                $app['sentinel.activations'],
                $app['events']
            );
            return new UserRepositoryEloquent(new User(), $sentinel);
        });
    }

    private function setDbConfigurations()
    {

        try {
            //Pusher
            Config::set('broadcasting.connections.pusher.key', Settings::get('pusher_key'));
            Config::set('broadcasting.connections.pusher.secret', Settings::get('pusher_secret'));
            Config::set('broadcasting.connections.pusher.app_id', Settings::get('pusher_app_id'));

            //Backup Manager
            Config::set('laravel-backup.destination.filesystem', Settings::get('backup_type'));

            //DISK Amazon S3
            Config::set('filesystems.disks.s3.key', Settings::get('disk_aws_key'));
            Config::set('filesystems.disks.s3.secret', Settings::get('disk_aws_secret'));
            Config::set('filesystems.disks.s3.region', Settings::get('disk_aws_region'));
            Config::set('filesystems.disks.s3.bucket', Settings::get('disk_aws_bucket'));

            //DISK Dropbox
            Config::set('filesystems.disks.dropbox.secret', Settings::get('disk_dbox_secret'));
            Config::set('filesystems.disks.dropbox.token', Settings::get('disk_dbox_token'));

            //Stripe
            Config::set('services.stripe.secret', Settings::get('stripe_secret'));
            Config::set('services.stripe.key', Settings::get('stripe_publishable'));

            //Mailserver
            Config::set('mail.driver', ((Settings::get('email_driver')==null)?Settings::get('email_driver'):'mail'));
            Config::set('mail.host', Settings::get('email_host'));
            Config::set('mail.port', Settings::get('email_port'));
            Config::set('mail.username', Settings::get('email_username'));
            Config::set('mail.password', Settings::get('email_password'));

            //Twilio
            Config::set('twilio.twilio.connections.twilio.sid', Settings::get('twilio_sid'));
            Config::set('twilio.twilio.connections.twilio.token', Settings::get('twilio_token'));
            Config::set('twilio.twilio.connections.twilio.from', Settings::get('twilio_from'));

        } catch (\Exception $e) {

        }
    }
}
