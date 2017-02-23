<?php namespace App\Http\Controllers\Secure;

use App\Models\BookUser;
use App\Http\Requests;
use App\Models\User;

use App\Repositories\BookUserRepository;
use Session;
use Sentinel;
use DB;
use Datatables;


class BorrowedBookController extends SecureController
{
    /**
     * @var BookUserRepository
     */
    private $bookUserRepository;

    /**
     * BorrowedBookController constructor.
     * @param BookUserRepository $bookUserRepository
     */
    public function __construct(BookUserRepository $bookUserRepository)
    {
        parent::__construct();

        $this->bookUserRepository = $bookUserRepository;

        view()->share('type', 'borrowedbook');
    }
    public function index()
    {
        $title = trans('borrowedbook.borrowedbooks');
        return view('borrowedbook.index', compact('title'));
    }

    public function data()
    {
        if ($this->user->inRole('student') || $this->user->inRole('teacher')) {
            $user_id = $this->user->id;
        } else {
            $user_id = Session::get('current_student_user_id');
        }
        $bookUsers = $this->bookUserRepository->getAll()
            ->with('book')
            ->get()
            ->filter(function($item) use ($user_id){
                return ((!is_null($item->get)) &&
                    is_null($item->back) && $item->user_id==$user_id && isset($item->book));
            })
            ->map(function($bookUser){
                return [
                    "title" => isset($bookUser->book)?$bookUser->book->title:"",
                    "author" => isset($bookUser->book)?$bookUser->book->author:"",
                    "get" => $bookUser->get,
                ];
            });
        return Datatables::of($bookUsers)->make();


    }
}
