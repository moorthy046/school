<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Version;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class UpdateController extends Controller
{
    public function index($version)
    {
        $steps = [
            'welcome' => 'active'];
        return view('update.start', compact('steps', 'version'));
    }

    public function update($version)
    {
        try {
            Artisan::call('migrate', ['--force' => true]);

            $this->updateToVersion($version);

            return redirect('update/' . $version . '/complete');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());

            return redirect('update/' . $version . '/error');
        }
    }

    public function complete($version)
    {
        $steps = [
            'welcome' => 'success_step',
            'complete' => 'active'];

        return view('update.complete', compact('steps'));
    }

    public function error($version)
    {
        return view('update.error');
    }

    private function updateToVersion($version)
    {
        switch ($version) {
            case '3.4':
                $this->update34();
                break;
        }

        $version_last = Version::first();
        if (isset($version_last)) {
            $version_last->version = $version;
        } else {
            $version_last = new Version(['version' => $version]);
        }
        $version_last->save();
    }

//update methods
    private function update34()
    {
        //add attendance_type options
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
    }
}
