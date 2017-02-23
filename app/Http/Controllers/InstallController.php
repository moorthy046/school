<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstallSettingsEmailRequest;
use App\Http\Requests\InstallSettingsRequest;
use App\Models\Option;
use App\Models\School;
use App\Models\SchoolAdmin;
use App\Repositories\InstallRepository;
use Dotenv\Dotenv;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Log;
use Swift_SmtpTransport;
use Swift_TransportException;

class InstallController extends Controller
{
	/**
	 * @var InstallRepository
	 */
	private $installRepository;

	/**
	 * InstallController constructor.
	 * @param InstallRepository $installRepository
	 */
	public function __construct(InstallRepository $installRepository)
	{

		$this->installRepository = $installRepository;
	}

	public function index()
	{
		$steps = [
			'welcome' => 'active'];
		return view('install.start', compact('steps'));
	}

	public function requirements()
	{
		$requirements = $this->installRepository->getRequirements();
		$allLoaded = $this->installRepository->allRequirementsLoaded();

		$steps = [
			'welcome' => 'success_step',
			'requirements' => 'active',
		];
		return view('install.requirements', compact('requirements', 'allLoaded','steps'));
	}

	public function permissions()
	{
		if (!$this->installRepository->allRequirementsLoaded()) {
			return redirect('install/requirements');
		}

		$folders = $this->installRepository->getPermissions();
		$allGranted = $this->installRepository->allPermissionsGranted();

		$steps = [
			'welcome' => 'success_step',
			'requirements' => 'success_step',
			'permissions' => 'active',
		];
		return view('install.permissions', compact('folders', 'allGranted','steps'));
	}

	public function database()
	{
		if (!$this->installRepository->allRequirementsLoaded()) {
			return redirect('install/requirements');
		}

		if (!$this->installRepository->allPermissionsGranted()) {
			return redirect('install/permissions');
		}

		$steps = [
			'welcome' => 'success_step',
			'requirements' => 'success_step',
			'permissions' => 'success_step',
			'database' => 'active',
		];

		return view('install.database', compact('steps'));
	}

	public function installation(Request $request)
	{
		if (!$this->installRepository->allRequirementsLoaded()) {
			return redirect('install/requirements');
		}

		if (!$this->installRepository->allPermissionsGranted()) {
			return redirect('install/permissions');
		}

		$dbCredentials = $request->only('host', 'username', 'password', 'database');

       // copy(base_path('.env.example'), base_path('.env'));
        
		if (!$this->installRepository->dbCredentialsAreValid($dbCredentials)) {
			return redirect('install/database')
				->withInput(array_except($dbCredentials, 'password'))
				->withErrors(trans("install.connection_established"));
		}
		$steps = [
			'welcome' => 'success_step',
			'requirements' => 'success_step',
			'permissions' => 'success_step',
			'database' => 'success_step',
			'installation' => 'active'
		];
		return view('install.installation', compact('steps'));
	}

	public function install()
	{
		try {
            Artisan::call('cache:clear');            
			Artisan::call('key:generate');
            Artisan::call('jwt:secret');
			Artisan::call('migrate', ['--force' => true]);
			Artisan::call('db:seed', ['--force' => true]);

			return redirect('install/settings');

		} catch (\Exception $e) {
			@unlink(base_path('.env'));
			Log::error($e->getMessage());
			Log::error($e->getTraceAsString());

			return redirect('install/error');
		}
	}

	public function disable()
	{
		$foldersDisable = $this->installRepository->getDisablePermissions();
		$allDisableGranted = $this->installRepository->allDisablePermissionsGranted();
		$steps = [
			'welcome' => 'success_step',
			'requirements' => 'success_step',
			'permissions' => 'success_step',
			'database' => 'success_step',
			'installation' => 'success_step',
			'settings' => 'active',
		];
		return view('install.disable', compact('foldersDisable','allDisableGranted','steps'));
	}
	public function settings()
	{
		$steps = [
			'welcome' => 'success_step',
			'requirements' => 'success_step',
			'permissions' => 'success_step',
			'database' => 'success_step',
			'installation' => 'success_step',
			'settings' => 'active',
			];
		Settings::forget('install.db_credentials');

		$currency = Option::where('category', 'currency')->lists('title', 'value')->toArray();

		return view('install.settings', compact('currency','steps'));
	}

	public function settingsSave(InstallSettingsRequest $request)
	{
		Settings::set('currency', $request->currency);

		Settings::set('multi_school', $request->multi_school);

		Settings::set('date_format', "Y-d-m");

		Settings::set('time_format', "g:i a");

		Settings::set('jquery_date', "GGGG-DD-MM");

		Settings::set('jquery_date_time', "GGGG-DD-MM h:mm a");

		$super_admin = Sentinel::registerAndActivate(array(
			'email' => $request->email,
			'password' => $request->password,
			'first_name' => $request->first_name,
			'last_name' => $request->last_name,
		));

		$role = Sentinel::findRoleBySlug('super_admin');
		$role->users()->attach($super_admin);

		if($request->multi_school=='no')
		{
			$school = new School($request->only('title','phone','address'));
			$school->email = $request->school_email;
			$school->save();

			$school_admin = new SchoolAdmin();
			$school_admin->user_id = $super_admin->id;
			$school_admin->school_id = $school->id;
			$school_admin->save();
			
			$role = Sentinel::findRoleBySlug('admin');
			$role->users()->attach($super_admin);

			$options = Option::where('school_id', 0)->get();

			foreach($options as $item)
			{
				$option = new Option();
				$option->category = $item->category;
				$option->school_id = $school->id;
				$option->title = $item->title;
				$option->value = $item->value;
				$option->save();
			}

		}

		return redirect('install/email_settings');
	}

	public function settingsEmail()
	{
		$steps = [
			'welcome' => 'success_step',
			'requirements' => 'success_step',
			'permissions' => 'success_step',
			'database' => 'success_step',
			'installation' => 'success_step',
			'settings' => 'success_step',
			'mail_settings' => 'active'];
		return view('install.mail_settings', compact('steps'));
	}

	public function settingsEmailSave(InstallSettingsEmailRequest $request)
	{
		try {
			if ($request->email_driver == 'smtp') {
				$transport = Swift_SmtpTransport::newInstance($request->email_host, $request->email_port, 'ssl');
				$transport->setUsername($request->email_username);
				$transport->setPassword($request->email_password);
				$mailer = \Swift_Mailer::newInstance($transport);
				$mailer->getTransport()->start();
			}
			foreach ($request->except('_token') as $key => $value) {
				Settings::set($key, $value);
			}
			file_put_contents(storage_path('installed'), 'Welcome to SMS');

			return redirect('install/complete');

		} catch (Swift_TransportException $e) {
			return redirect()->back()->withErrors($e->getMessage());
		} catch (\Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	public function complete()
	{
		$steps = [
		'welcome' => 'success_step',
		'requirements' => 'success_step',
		'permissions' => 'success_step',
		'database' => 'success_step',
		'installation' => 'success_step',
		'settings' => 'success_step',
		'mail_settings' => 'success_step',
		'complete' => 'active'];

		return view('install.complete', compact('steps'));
	}

	public function error()
	{
		return view('install.error');
	}
}
