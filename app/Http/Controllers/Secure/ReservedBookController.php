<?php

namespace App\Http\Controllers\Secure;

use App\Models\Book;
use App\Models\BookUser;
use App\Repositories\BookUserRepository;
use DB;
use Datatables;

class ReservedBookController extends SecureController
{
    /**
     * @var BookUserRepository
     */
    private $bookUserRepository;

    /**
     * ReservedBookController constructor.
     * @param BookUserRepository $bookUserRepository
     */
    public function __construct(BookUserRepository $bookUserRepository)
    {
        parent::__construct();

        $this->bookUserRepository = $bookUserRepository;

        view()->share('type', 'reservedbook');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('reservedbook.reservedbooks');
        return view('reservedbook.index', compact('title'));
    }

    public function show(BookUser $bookuser)
    {
        $title = trans('reservedbook.issue');
        $action = 'issue';
        $available = isset($bookuser->book->id)?$bookuser->book->availableCopies():0;
        return view('reservedbook.issue', compact('bookuser', 'title', 'action', 'available'));
    }


    public function issue(BookUser $bookuser)
    {
        $bookuser->get = date('Y-m-d');
        $bookuser->save();
        return redirect("/reservedbook");
    }


    public function delete(BookUser $bookuser)
    {
        $title = trans('reservedbook.delete');
        $available = isset($bookuser->book->id)?$bookuser->book->availableCopies():0;
        return view('reservedbook.delete', compact('bookuser', 'title', 'available'));
    }


    public function destroy($bookuserid)
    {
        $bookuser = BookUser::find($bookuserid);
        $bookuser->delete();
        return redirect('/reservedbook');
    }

    public function data()
    {
        $reservedBooks = $this->bookUserRepository->getAll()
            ->with('book','user')
            ->get()
            ->filter(function($reservedBook){
                return ((!is_null($reservedBook->reserved)) &&
                isset($reservedBook->book) &&
                is_null($reservedBook->get) &&
                is_null($reservedBook->back));
            })
            ->map(function($reservedBook){
                return [
                    'id' => $reservedBook->book_id,
                    'name' => isset($reservedBook->user)?$reservedBook->user->full_name:"",
                    'book' => $reservedBook->book->title.' '.$reservedBook->book->author.'('.$reservedBook->book->internal.')',
                ];
            });

        return Datatables::of($reservedBooks)
            ->add_column('actions', '<a href="{{ url(\'/reservedbook/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("reservedbook.issue") }}</a>
                                     <a href="{{ url(\'/reservedbook/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("reservedbook.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }
}
