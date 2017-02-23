<?php

use Illuminate\Database\Seeder;
use App\Models\Option;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        //truncate existing data
        DB::table('options')->truncate();

        //payment_methods options
        Option::create([
            'category' => 'payment_methods',
            'school_id' => 0,
            'title' => 'Cash',
            'value' => 'Cash'
        ]);
        Option::create([
            'category' => 'payment_methods',
            'title' => 'Check',
            'school_id' => 0,
            'value' => 'Check'
        ]);
        Option::create([
            'category' => 'payment_methods',
            'title' => 'Bank Account',
            'school_id' => 0,
            'value' => 'Bank Account'
        ]);
        Option::create([
            'category' => 'payment_methods',
            'title' => 'Credit Card',
            'school_id' => 0,
            'value' => 'Credit Card'
        ]);

        //status_payment options
        Option::create([
            'category' => 'status_payment',
            'title' => 'Payed',
            'school_id' => 0,
            'value' => 'Payed'
        ]);
        Option::create([
            'category' => 'status_payment',
            'title' => 'Suspended',
            'school_id' => 0,
            'value' => 'Suspended'
        ]);
        Option::create([
            'category' => 'status_payment',
            'title' => 'Canceled',
            'school_id' => 0,
            'value' => 'Canceled'
        ]);
        Option::create([
            'category' => 'status_payment',
            'title' => 'Pending',
            'school_id' => 0,
            'value' => 'Pending'
        ]);
        Option::create([
            'category' => 'status_payment',
            'title' => 'Success With Warning',
            'school_id' => 0,
            'value' => 'Success With Warning'
        ]);

        //currency statuses
        Option::create([
            'category' => 'currency',
            'school_id' => 0,
            'title' => 'USD',
            'value' => 'USD'
        ]);
        Option::create([
            'category' => 'currency',
            'school_id' => 0,
            'title' => 'EUR',
            'value' => 'EUR'
        ]);

        //Cloud Servers
        Option::create([
            'category' => 'backup_type',
            'school_id' => 0,
            'title' => 'local',
            'value' => 'Local'
        ]);

        Option::create([
            'category' => 'backup_type',
            'school_id' => 0,
            'title' => 'dropbox',
            'value' => 'Dropbox'
        ]);

        Option::create([
            'category' => 'backup_type',
            'school_id' => 0,
            'title' => 's3',
            'value' => 'Amazon S3'
        ]);

        //justified options
        Option::create([
            'category' => 'justified',
            'school_id' => 0,
            'title' => 'Unknown',
            'value' => 'Unknown'
        ]);
        Option::create([
            'category' => 'justified',
            'school_id' => 0,
            'title' => 'Yes',
            'value' => 'Yes'
        ]);
        Option::create([
            'category' => 'justified',
            'school_id' => 0,
            'title' => 'No',
            'value' => 'No'
        ]);

        //report_type options
        Option::create([
            'category' => 'report_type',
            'school_id' => 0,
            'title' => 'List of students attendances',
            'value' => 'list_attendances'
        ]);
        Option::create([
            'category' => 'report_type',
            'school_id' => 0,
            'title' => 'List of students marks',
            'value' => 'list_marks'
        ]);
        Option::create([
            'category' => 'report_type',
            'school_id' => 0,
            'title' => 'List of marks for selected exam',
            'value' => 'list_exam_marks'
        ]);
        Option::create([
            'category' => 'report_type',
            'school_id' => 0,
            'title' => 'List of students behaviors',
            'value' => 'list_behaviors'
        ]);

        //attendance_student_type options
        Option::create([
            'category' => 'attendance_type',
            'school_id' => 0,
            'title' => 'Present',
            'value' => 'Present'
        ]);
        Option::create([
            'category' => 'attendance_type',
            'school_id' => 0,
            'title' => 'Absent',
            'value' => 'Absent'
        ]);
        Option::create([
            'category' => 'attendance_type',
            'school_id' => 0,
            'title' => 'Late',
            'value' => 'Late'
        ]);
        Option::create([
            'category' => 'attendance_type',
            'school_id' => 0,
            'title' => 'Late with excuse',
            'value' => 'Late with excuse'
        ]);

        //feedback_type options
        Option::create([
            'category' => 'feedback_type',
            'school_id' => 0,
            'title' => 'Praise',
            'value' => 'Praise'
        ]);
        Option::create([
            'category' => 'feedback_type',
            'school_id' => 0,
            'title' => 'Contact',
            'value' => 'Contact'
        ]);
        Option::create([
            'category' => 'feedback_type',
            'school_id' => 0,
            'title' => 'Error',
            'value' => 'Error'
        ]);
        Option::create([
            'category' => 'feedback_type',
            'school_id' => 0,
            'title' => 'Proposal',
            'value' => 'Proposal'
        ]);
        Option::create([
            'category' => 'feedback_type',
            'school_id' => 0,
            'title' => 'Request',
            'value' => 'Request'
        ]);

        Eloquent::reguard();
    }
}