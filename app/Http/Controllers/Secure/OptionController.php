<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests\Secure\OptionRequest;
use App\Http\Requests;
use App\Models\Option;
use App\Models\School;
use App\Repositories\OptionRepository;
use Sentinel;
use Datatables;

class OptionController extends SecureController
{
    private $categories;
    /**
     * @var OptionRepository
     */
    private $optionRepository;

    /**
     * OptionController constructor.
     * @param OptionRepository $optionRepository
     */
    public function __construct(OptionRepository $optionRepository)
    {
        parent::__construct();

        $this->categories = array(
            'feedback_type' => 'Feedback type',
            'report_type' => 'Report type',
            'justified' => 'Justified',
            'backup_type' => 'Backup type',
            'currency' => 'Currency',
            'status_payment' => "Status payment",
            'payment_methods' => 'Payment methods',);


        view()->share('type', 'option');
        $this->optionRepository = $optionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('option.options');

        $this->generateParams();

        return View('option.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = trans('option.new');

        $this->generateParams();

        return view('option.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(OptionRequest $request)
    {
        Option::create($request->all());
        return redirect("/option");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Option $option
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function edit(Option $option)
    {
        $title = trans('option.edit');

        $this->generateParams();

        return view('option.edit', compact('title', 'option'));
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(OptionRequest $request, Option $option)
    {
        $option->update($request->all());

        return redirect("/option");
    }

    public function show(Option $option)
    {
        $action = "show";
        $title = trans('option.show');
        return view('option.show', compact('title', 'option', 'action'));
    }

    public function delete(Option $option)
    {
        $title = trans('option.delete');
        return view('option.delete', compact('title', 'option'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Option $option
     * @return \Illuminate\Http\Response
     */
    public function destroy(Option $option)
    {
        $option->delete();
        return redirect('/option');
    }

    /**
     * Get ajax datatables data
     *
     */
    public function data($category='__')
    {
        $options = $this->optionRepository->getAll();

        if ($category != "__") {
            $options = $options->where('category', $category);
        }
        $options = $options->get()
            ->map(function($option){
                return [
                    "id" => $option->id,
                    "category" => $option->category,
                    "title" => $option->title,
                    "value" => $option->value,
                ];
            });

        return Datatables::of($options)
            ->add_column('actions', '<a href="{{ url(\'/option/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning"></i>  </a>
                                     <a href="{{ url(\'/option/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}">
                                            <i class="fa fa-fw fa-eye text-primary"></i></a>
                                     <a href="{{ url(\'/option/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-times text-danger"></i> </a>')
            ->remove_column('id')
            ->make();
    }


    private function generateParams()
    {
        $schools = ['0'=>trans('option.all_schools')] + School::lists('title', 'id')->toArray();

        view()->share('schools', $schools);
        view()->share('categories', $this->categories);
    }
}
