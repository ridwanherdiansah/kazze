<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function export(Request $request)
    {
        try {
            $request->validate([
                'tanggal_awal' => 'required',
                'tanggal_akhir' => 'required',
            ],[
                'tanggal_awal.required' => 'tanggal awal harus diisi',
                'tanggal_akhir.required' => 'tanggal akhir harus diisi',
            ]);

            $data = Transaksi::select(
                'transaksis.id',
                'products.name AS name_product',
                'transaksis.total_amount',
                DB::raw("
                    CASE 
                        WHEN transaksis.status = 1 THEN 'Sudah Bayar' 
                        WHEN transaksis.status = 0 THEN 'Belum Bayar' 
                        ELSE 'Unknown'
                    END AS status_transaksi"
                ),
                'transaksis.created_at'
            )
            ->leftJoin('products', 'transaksis.product_id', '=', 'products.id')
            ->whereDate('transaksis.created_at', '>=', $request->tanggal_awal)
            ->whereDate('transaksis.created_at', '<=', $request->tanggal_akhir)
            ->orderBy('id', 'desc')
            ->get();
            
            return Excel::download(new TransaksiExport($data), 'Transaksi.xlsx');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function filter(Request $request)
    {
        try {
            $title = "Transaksi";
            $type_menu = 'Transaksi';
            
            $request->validate([
                'tanggal_awal' => 'required',
                'tanggal_akhir' => 'required',
            ],[
                'tanggal_awal.required' => 'tanggal awal harus diisi',
                'tanggal_akhir.required' => 'tanggal akhir harus diisi',
            ]);

            $data = Transaksi::select(
                'transaksis.*',
                'products.name AS name_product'
            )
            ->leftJoin('products', 'transaksis.product_id', '=', 'products.id')
            ->whereDate('transaksis.created_at', '>=', $request->tanggal_awal)
            ->whereDate('transaksis.created_at', '<=', $request->tanggal_akhir)
            ->orderBy('id', 'desc')
            ->paginate(10);
            
            return view('Pages.Admin.Transaksi.index', compact('data', 'type_menu', 'title'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function index()
    {
        try {
            $title = "Transaksi";
            $type_menu = 'Transaksi';

            $data = Transaksi::select(
                'transaksis.*',
                'products.name AS name_product'
            )
            ->leftJoin('products', 'transaksis.product_id', '=', 'products.id')
            ->get();
            return view('Pages.Admin.Transaksi.index', compact('title', 'type_menu', 'data'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
        
    }

    public function store(Request $request)
    {
        try {
             // Validasi data yang diterima dari formulir
            $validated = $request->validate([
                'product_id' => 'required|string|max:3',
                'total_amount' => 'required|string|max:255',
                'status' => 'required|numeric|min:0',
            ]);

            // Menyimpan data Transaksi baru
            Transaksi::create($validated);

            // Mengarahkan kembali dengan pesan sukses
            return redirect()->route('transaksi.index')->with('success', 'Create success.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        try {
            $request->validate([
                'search' => 'required|max:255',
            ],[
                'search.required' => 'search harus diisi',
                'search.max' => 'search maksimal 255 karakter',
            ]);

            $search = $request->search;
        
            $title = "Transaksi";
            $type_menu = 'Transaksi';
            $data = Transaksi::orderBy('id', 'desc')->where('name', 'like', '%' . $search . '%')->paginate(10);
            return view('Pages.Admin.Transaksi.index', compact('data', 'type_menu', 'title'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validasi request
            $validated = $request->validate([
                'total_amount' => 'required|string|max:255',
                'status' => 'required|numeric|min:0',
            ]);

            // Temukan data berdasarkan ID
            $data = Transaksi::find($id);

            if (!$data) {
                return back()->with('error', 'Transaksi tidak ditemukan.');
            }

            // Update data
            $data->update([
                'total_amount' => $request->input('total_amount'),
                'status' => $request->input('status'),
            ]);

            // Kondisi Data Berhasil atau Gagal
            return back()->with('success', 'Berhasil edit Menu');
        } catch (\Exception $e) {
            // Tangani kesalahan dengan mengembalikan pesan error
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            // Temukan data berdasarkan ID
            $data = Transaksi::find($id);

            if (!$data) {
                return back()->with('error', 'Transaksi tidak ditemukan.');
            }

            // Soft delete data
            $data->delete();

            return back()->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            // Tangani kesalahan dengan mengembalikan pesan error
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

    }
}
