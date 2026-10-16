<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class AccountController extends Controller
{
    public function index(): View
    {
        $title = 'Akun';
        return view('admin.account.index', compact('title'));
    }

    public function get_account_index(Request $request): JsonResponse
    {
        abort_unless($request->ajax(), 404);

        $counter = 1;

        return DataTables::of(Account::query())
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counter) {
                return $counter++;
            })
            ->addColumn('action', function (Account $account) {
                $deleteId = $account->id;

                return '<a href="#" onclick="getData('.$deleteId.')" title="Edit" data-toggle="modal" data-target="#exampleModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        </a>
                        <a href="#" onclick="deleteData('.$deleteId.')" class="warning confirm" data-toggle="tooltip" data-placement="top" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function validate(Request $request, string $action): JsonResponse
    {
        abort_unless($request->ajax(), 404);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], [], [
            'name' => 'Nama Akun',
        ]);

        return response()->json(['success' => true]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->ajax(), 404);

        Account::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], [], [
            'name' => 'Nama Akun',
        ]));

        activity()->log('Create Account Data');

        return response()->json(['success' => true, 'message' => 'Tambah Data Berhasil']);
    }

    public function edit(Request $request, Account $account): JsonResponse
    {
        abort_unless($request->ajax(), 404);

        return response()->json(['success' => true, 'data' => $account]);
    }

    public function update(Request $request, Account $account): JsonResponse
    {
        abort_unless($request->ajax(), 404);

        $account->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], [], [
            'name' => 'Nama Akun',
        ]));

        activity()->log('Edit Account Data With ID = '.$account->id);

        return response()->json(['success' => true, 'message' => 'Ubah Data Berhasil']);
    }

    public function delete(Request $request, Account $account): JsonResponse
    {
        abort_unless($request->ajax(), 404);

        $account->delete();

        activity()->log('Delete Account Data With ID = '.$account->id);

        return response()->json(['success' => true, 'message' => 'Hapus Data Berhasil']);
    }
}
