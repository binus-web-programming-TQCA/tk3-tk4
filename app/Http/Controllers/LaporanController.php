<?php

namespace App\Http\Controllers;
use App\Models\Transaksi;
use App\Models\DataBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class LaporanController extends Controller
{
    public function laporan(){

        $subquery = DB::table('transaksis')
        ->select('id_barang', DB::raw('count(id) as jumlah_transaksi'))
        ->where('status', '=', 1)
        ->groupBy('id_barang');

        $barang = DB::table('data_barang')
            ->joinSub($subquery, 'transaksis', function ($join) {
                $join->on('data_barang.id', '=', 'transaksis.id_barang');
            })
            ->select('data_barang.*', 'transaksis.jumlah_transaksi')
            ->get();
                // dd($barang);
            
            return view('laporan.index', compact('barang'));
        }
}

