<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Repositories\SQL\UserRepository;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public $repository;

    /**
     * UserController constructor.
     *
     * @param UserRepository $repository
     *
     * **/
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->repository->create($request->except('attachment'));
            DB::commit();
            return response()->json(['success' => true, 'message' => 'user added successfully and its attachment is uploaded'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'reason' => $e], 422);

        }
    }

}
