<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests;
use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use App\Repositories\StudentRepository;
use Datatables;
use Session;
use DB;
use App\Http\Requests\Secure\InvoiceRequest;
use Illuminate\Support\Facades\App;

class InvoiceController extends SecureController
{
    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;
    /**
     * @var StudentRepository
     */
    private $studentRepository;

    /**
     * InvoiceController constructor.
     * @param InvoiceRepository $invoiceRepository
     * @param StudentRepository $studentRepository
     */
    public function __construct(InvoiceRepository $invoiceRepository,
                                StudentRepository $studentRepository)
    {
        parent::__construct();

        $this->invoiceRepository = $invoiceRepository;
        $this->studentRepository = $studentRepository;

        view()->share('type', 'invoice');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('invoice.invoice');
        return view('invoice.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('invoice.new');
        $this->generateParams();

        return view('invoice.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(InvoiceRequest $request)
    {
        foreach($request['user_id'] as $user_id) {
            $invoice = new Invoice($request->except('user_id'));
            $invoice->user_id = $user_id;
            $invoice->save();
        }
        return redirect('/invoice');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(Invoice $invoice)
    {
        $data = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <html>
                <body>
                <h1>' . trans('invoice.details') . '</h1>
                        '.trans('invoice.title').': '.$invoice->title.'<br>
                        '.trans('invoice.description').': '.$invoice->description.'<br>
                        '.trans('invoice.amount').': '.$invoice->amount.'<br>
                        '.trans('invoice.student').': '.$invoice->user->first_name .' '. $invoice->user->last_name.'<br>
            </body></html>';
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($data);
        return $pdf->stream();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit(Invoice $invoice)
    {
        $title = trans('invoice.edit');
        $this->generateParams();

        return view('invoice.edit', compact('title', 'invoice'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        $invoice->update($request->all());
        return redirect('/invoice');
    }

    /**
     *
     *
     * @param $website
     * @return Response
     */
    public function delete(Invoice $invoice)
    {
        $title = trans('invoice.delete');
        return view('/invoice/delete', compact('invoice', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect('/invoice');
    }

    public function data()
    {
        $invoices = $this->invoiceRepository->getAll()
            ->with('user')
            ->get()
            ->map(function ($invoice) {
                return [
                    "id" => $invoice->id,
                    "title" => $invoice->title,
                    "name" => isset($invoice->user) ? $invoice->user->full_name : "",
                    "amount" => $invoice->amount,
                ];
            });
        return Datatables::of($invoices)
            ->add_column('actions', '<a href="{{ url(\'/invoice/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a target="_blank" href="{{ url(\'/invoice/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/invoice/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }

    /**
     * @return mixed
     */
    private function generateParams()
    {
        $students = $this->studentRepository->getAllForSchoolYear(Session::get('current_school_year'))
            ->with('user')
            ->get()
            ->map(function ($item) {
                return [
                    "id" => $item->user_id,
                    "name" => isset($item->user) ? $item->user->full_name : "",
                ];
            })->lists("name", 'id')->toArray();
        view()->share('students',  $students);
    }

}
