<?php

namespace App\Http\Controllers\Secure;

use App\Models\BookUser;
use App\Models\User;
use App\Models\Book;
use App\Repositories\BookRepository;
use App\Repositories\BookUserRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use DB;
use Illuminate\Routing\Controller;

class BookLibrarianController extends SecureController
{
    /**
     * @var UserRepository
     */
    private $userbRepository;
    /**
     * @var BookRepository
     */
    private $bookRepository;
    /**
     * @var BookUserRepository
     */
    private $bookUserRepository;

    /**
     * BookLibrarianController constructor.
     * @param UserRepository $userbRepository
     * @param BookRepository $bookRepository
     * @param BookUserRepository $bookUserRepository
     */
    public function __construct(UserRepository $userbRepository,
                                BookRepository $bookRepository,
                                BookUserRepository $bookUserRepository)
    {
        parent::__construct();

        $this->userbRepository = $userbRepository;
        $this->bookRepository = $bookRepository;
        $this->bookUserRepository = $bookUserRepository;

        view()->share('type', 'booklibrarian');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('booklibrarian.find');
        return view('booklibrarian.index', compact('title'));
    }

    public function getUsers(Request $request)
    {
        $userb = trim($request['userb']);
        $words = explode(" ", $userb);
        $userbs = array();
        foreach ($words as $word) {
            $result = $this->userbRepository->getAll()
                ->where('first_name', 'LIKE', '%' . $word . '%')
                ->orWhere('last_name', 'LIKE', '%' . $word . '%')
                ->get()
                ->map(function($userb)
                {
                    return [
                        "value" => $userb->id,
                        "label" => $userb->full_name,
                    ];
                })
                ->toArray();
            $userbs = array_merge($userbs, $result);
        }
        return response()->json($userbs);
    }

    public function issueReturnBook(User $userb)
    {
        $title = trans('booklibrarian.find');
        if ($userb == null) {
            return response()->json(trans('booklibrarian.no_userb'));
        } else {
            $bookuserb = $this->bookUserRepository->getAll()
                ->with('book')
                ->get()
                ->filter(function($item) use ($userb){
                    return ((!is_null($item->get)) &&
                    is_null($item->back) && $item->user_id==$userb->id && isset($item->book));
                })
                ->map(function($item){
                    return [
                        "id" => $item->id,
                        "title" => $item->book->title,
                        "author" => $item->book->author,
                    ];
                });
            return view('booklibrarian.issuereturn', compact('title', 'userb', 'bookuserb'));
        }
    }

    public function returnBook(BookUser $bookUser)
    {
        $bookUser->back = date('Y-m-d');
        $bookUser->save();
        $userb = $this->userbRepository->getAll()->where('id', $bookUser->userb_id)->first();

        return Controller::callAction('issueReturnBook',array($userb));
    }

    public function getBooks(Request $request)
    {
        $book = trim($request['book']);
        $words = explode(" ", $book);
        $books = array();
        foreach ($words as $word) {
            $result = $this->bookRepository->getAll()
                ->where('title', 'LIKE', '%' . $word . '%')
                ->orWhere('publisher', 'LIKE', '%' . $word . '%')
                ->orWhere('author', 'LIKE', '%' . $word . '%')
                ->orWhere('internal', 'LIKE', '%' . $word . '%')
                ->get()
                ->map(function($book)
                {
                    return [
                        "value" => $book->id,
                        "label" => $book->title.' - '. $book->author.'('.$book->internal.')',
                    ];
                })->toArray();
            $books = array_merge($books, $result);
        }
        return response()->json($books);
    }

    public function getBook(Book $book, User $userb)
    {
        return view("booklibrarian.book", compact('book', 'userb'));
    }

    public function issueBook(User $userb, Book $book)
    {
        $bookuserb = new BookUser();
        $bookuserb->userb_id = $userb->id;
        $bookuserb->book_id = $book->id;
        $bookuserb->get = date('Y-m-d');
        $bookuserb->save();

        return Controller::callAction('issueReturnBook',array($userb));
    }
}
