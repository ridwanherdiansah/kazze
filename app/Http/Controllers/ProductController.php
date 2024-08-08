<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExport;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
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

            $data = Product::select(
                'product.id',
                'products.name',
                'products.weight',
                'products.size',
                'products.type',
                DB::raw("
                    (SELECT COUNT(transaksis.product_id) 
                     FROM transaksis 
                     WHERE transaksis.product_id = products.id
                    ) AS jumlah_transaksi"
                ),
                'products.created_at',
            )
            ->whereDate('created_at', '>=', $request->tanggal_awal)
            ->whereDate('created_at', '<=', $request->tanggal_akhir)
            ->orderBy('id', 'desc')
            ->get();
            
            return Excel::download(new ProductExport($data), 'Product.xlsx');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function filter(Request $request)
    {
        try {
            $title = "Product";
            $type_menu = 'Product';
            
            $request->validate([
                'tanggal_awal' => 'required',
                'tanggal_akhir' => 'required',
            ],[
                'tanggal_awal.required' => 'tanggal awal harus diisi',
                'tanggal_akhir.required' => 'tanggal akhir harus diisi',
            ]);

            $data = Product::select(
                'products.name',
                'products.weight',
                'products.size',
                'products.type',
                DB::raw("
                    (SELECT COUNT(transaksis.product_id) 
                     FROM transaksis 
                     WHERE transaksis.product_id = products.id
                    ) AS jumlah_transaksi"
                ),
                'products.created_at',
            )
            ->whereDate('created_at', '>=', $request->tanggal_awal)
            ->whereDate('created_at', '<=', $request->tanggal_akhir)
            ->orderBy('id', 'desc')
            ->paginate(10);
            
            return view('Pages.Admin.Product.index', compact('data', 'type_menu', 'title'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function index()
    {
        try {
            $title = "Product";
            $type_menu = 'Product';

            $data = Product::select(
                'products.name',
                'products.weight',
                'products.size',
                'products.type',
                DB::raw("
                    (SELECT COUNT(transaksis.product_id) 
                     FROM transaksis 
                     WHERE transaksis.product_id = products.id
                    ) AS jumlah_transaksi"
                ),
                'products.created_at',
            )
            ->get();

            return view('Pages.Admin.Product.index', compact('title', 'type_menu', 'data'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
        
    }

    public function store(Request $request)
    {
        try {
             // Validasi data yang diterima dari formulir
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'weight' => 'required|numeric|min:0',
                'size' => 'required|string|max:255',
                'type' => 'required|string|max:255',
            ]);

            // Menyimpan data produk baru
            Product::create($validated);

            // Mengarahkan kembali dengan pesan sukses
            return redirect()->route('product.index')->with('success', 'Create success.');
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
        
            $title = "Product";
            $type_menu = 'Product';
            $data = $data = Product::select(
                'products.name',
                'products.weight',
                'products.size',
                'products.type',
                DB::raw("
                    (SELECT COUNT(transaksis.product_id) 
                     FROM transaksis 
                     WHERE transaksis.product_id = products.id
                    ) AS jumlah_transaksi"
                ),
                'products.created_at',
            )
            ->orderBy('id', 'desc')
            ->where('name', 'like', '%' . $search . '%')
            ->paginate(10);
            return view('Pages.Admin.Product.index', compact('data', 'type_menu', 'title'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validasi request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'weight' => 'required|numeric|min:0',
                'size' => 'required|string|max:255',
                'type' => 'required|string|max:255',
            ]);

            // Temukan data berdasarkan ID
            $data = Product::find($id);

            if (!$data) {
                return back()->with('error', 'Product tidak ditemukan.');
            }

            // Update data
            $data->update([
                'name' => $request->input('name'),
                'weight' => $request->input('weight'),
                'size' => $request->input('size'),
                'type' => $request->input('type'),
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
            $data = Product::find($id);

            if (!$data) {
                return back()->with('error', 'Product tidak ditemukan.');
            }

            // Soft delete data
            $data->delete();

            return back()->with('success', 'Product berhasil dihapus.');
        } catch (\Exception $e) {
            // Tangani kesalahan dengan mengembalikan pesan error
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

    }
}
