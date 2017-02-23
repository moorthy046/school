<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests;
use App\Models\Book;
use App\Models\BookUser;
use App\Repositories\BookRepository;
use Session;
use Sentinel;
use DB;
use Datatables;

class BookUserController extends SecureController
{
    /**
     * @var BookRepository
     */
    private $bookRepository;

    /**
     * BookUserController constructor.
     * @param BookRepository $bookRepository
     */
    public function __construct(BookRepository $bookRepository)
    {
        parent::__construct();

        $this->bookRepository = $bookRepository;

        view()->share('type', 'bookuser');
    }

    public function index()
    {
        $title = trans('bookuser.bookuser');
        return view('bookuser.index', compact('title'));
    }

    public function data()
    {
        $books = $this->bookRepository->getAll()
            ->with('subject')
            ->get()
            ->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'subject' => isset($book->subject)?$book->subject->title:"",
                ];
            });
        return Datatables::of($books)
            ->add_column('actions', '<a href="{{ url(\'/bookuser/\' . $id . \'/reserve\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-check-circle"></i> {{ trans("bookuser.reserve") }}</a>')
            ->remove_column('id')
            ->make();
    }

    public function reserve(Book $book)
    {
        $bookUser = new BookUser();
        $bookUser->book_id = $book->id;
        $bookUser->user_id = ($this->user->inRole('student') || $this->user->inRole('teacher'))?
                                                                        $this->user->id:
                                                                        Session::get('current_student_user_id');
        $bookUser->reserved = date('Y-m-d');
        $bookUser->save();

        return redirect()->back();

    }

}
