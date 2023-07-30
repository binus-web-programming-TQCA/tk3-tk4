<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pembeli;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class PembeliController extends Controller
{
    public function list()
    {
        $data = Pembeli::all();
        return view('pembeli.list', ['data' => $data]);
    }

    public function add() {
        return view('pembeli.add');
    }

    public function view($id)
    {
        $data = Pembeli::find($id);
        return view('pembeli.view', ['data' => $data]);
    }

    public function store(Request $request) {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'user' => ['required', 'string', 'max:255', 'unique:'.Pembeli::class],
            'tempat_lahir' => ['required', 'string'],
            'tanggal_lahir' => ['required', 'string'],
            'jenis_kelamin' => ['required', 'string'],
            'alamat' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults(),],
            'ktp' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = new User();
        $user->name = $request->input('nama');
        $user->username = $request->input('user');
        $user->password = Hash::make($request->input('password'));
        $user->jenis_kelamin = $request->input('jenis_kelamin');
        $user->role = "pembeli";
        $user->save();

        if ($request->hasFile('ktp')) {
            $ktp = $request->file('ktp')->store('public/ktp');
        }

        $data = new Pembeli();
        $data->id = $user->id;
        $data->nama = $request->input('nama');
        $data->tempat_lahir = $request->input('tempat_lahir');
        $data->tanggal_lahir = $request->input('tanggal_lahir');
        $data->jenis_kelamin = $request->input('jenis_kelamin');
        $data->alamat = $request->input('alamat');
        $data->user = $request->input('user');
        $data->foto_ktp = $ktp;
        $data->password = Hash::make($request->input('password'));
        $data->save();

        return redirect()->route('pembeli.list');
    }

    public function delete($id) {
        $data = Pembeli::findOrFail($id);
        $data->delete();

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('pembeli.list');
    }

    public function edit($id) {
        $data = Pembeli::findOrFail($id);
        return view('pembeli.edit', ['data' => $data]);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'user' => ['required', 'string', 'max:255', 'unique:'.Pembeli::class],
            'tempat_lahir' => ['required', 'string'],
            'tanggal_lahir' => ['required', 'string'],
            'jenis_kelamin' => ['required', 'string'],
            'alamat' => ['required', 'string'],
            'ktp' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->input('password') != "") {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
        }

        if ($request->hasFile('ktp')) {
            $ktp = $request->file('ktp')->store('public/ktp');
        }

        $data = Pembeli::findOrFail($id);
        $data->nama = $request->input('nama');
        $data->tempat_lahir = $request->input('tempat_lahir');
        $data->tanggal_lahir = $request->input('tanggal_lahir');
        $data->jenis_kelamin = $request->input('jenis_kelamin');
        $data->alamat = $request->input('alamat');
        $data->user = $request->input('user');
        $data->foto_ktp = $ktp;

        if ($request->input('password') != "") {
            $data->password = Hash::make($request->input('password'));
        }

        $data->save();

        $user = User::findOrFail($id);
        $user->name = $data->nama;
        $user->username = $data->user;
        $user->password = $data->password;
        $user->jenis_kelamin = $data->jenis_kelamin;
        $user->role = "pembeli";
        $user->save();

        return redirect()->route('pembeli.list');
    }
}
