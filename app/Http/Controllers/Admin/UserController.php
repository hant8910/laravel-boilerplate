<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Repositories\UserRepositoryInterface;
use App\Http\Requests\PaginationRequest;
use App\Http\Requests\Admin\UserRequest;

use Illuminate\Support\MessageBag;

class UserController extends Controller
{
    /** @var \App\Repositories\UserRepositoryInterface */
    protected $userRepository;

    /** @var \Illuminate\Support\MessageBag */
    protected $messageBag;

    public function __construct(
        UserRepositoryInterface $userRepositoryInterface,
        MessageBag $messageBag
    )
    {
        $this->userRepository = $userRepositoryInterface;
        $this->messageBag = $messageBag;
    }

    public function index(PaginationRequest $request)
    {
        $offset = $request->offset();
        $limit = $request->limit();

        $users = $this->userRepository->get('id', 'desc', $offset, $limit);
        $count = $this->userRepository->count();

        return view('pages.admin.users.index', [
            'users'   => $users,
            'offset'  => $offset,
            'limit'   => $limit,
            'count'   => $count,
            'baseUrl' => \URL::action('Admin\UserController@index'),
            'menu'    => 'user',
        ]);
    }

    public function show($id)
    {
        $user = $this->userRepository->find($id);
        if (empty( $user )) {
            abort(404);
        }

        return view('pages.admin.users.edit', [
            'user' => $user,
            'menu' => 'user',
        ]);
    }

    public function create()
    {

    }

    public function store(UserRequest $request)
    {
        $model = $this->userRepository->create($request->all());

        return redirect()->action('Admin\UserController@show', [$model->id])->with('message-success',
            \Lang::get('admin.messages.general.create_success'));
    }

    public function update($id, UserRequest $request)
    {
        $user = $this->userRepository->find($id);
        if (empty( $user )) {
            abort(404);
        }

        $this->userRepository->update($user, $request->all());

        return redirect()->action('Admin\UserController@show', [$id])->with('message-success',
            \Lang::get('admin.messages.general.update_success'));
    }

    public function destroy($id)
    {
        $user = $this->userRepository->find($id);
        if (empty( $user )) {
            abort(404);
        }
        $this->userRepository->delete($user);

        return redirect()->action('Admin\UserController@index')->with('message-success',
            \Lang::get('admin.messages.general.delete_success'));
    }

}
