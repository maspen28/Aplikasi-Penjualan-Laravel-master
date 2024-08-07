<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\StockHistory;
use App\Models\Category;
use App\Models\Discount;
use Illuminate\Support\Facades\File;
use App\Jobs\ProductJob;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::with(['category'])->orderBy('created_at', 'DESC');
        $stockHistories = StockHistory::with('product')->orderBy('updated_at', 'DESC')->paginate(10);
        if (request()->q != '') {
            $product = $product->where('name', 'LIKE', '%' . request()->q . '%');
        }
        $product = $product->paginate(10);

        // Ambil kategori dari database
        $category = Category::orderBy('name', 'DESC')->get();
        $discount = Discount::orderBy('discount_name', 'DESC')->get();
        

        return view('produk.produk', compact('product', 'category', 'stockHistories', 'discount'));
    }


    public function create()
    {
        $category = Category::orderBy('name', 'DESC')->get();

        return view('produk.create', compact('category'));
    }

    public function store(Request $request)
    {
        //VALIDASI REQUESTNYA
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id', //CATEGORY_ID KITA CEK HARUS ADA DI TABLE CATEGORIES DENGAN FIELD ID
            'price' => 'required|integer',
            'weight' => 'required|integer',
            // 'discount_id' => 'nullable|exists:discounts,id',
            // 'stock' => 'required|integer',
            'image' => 'required|image|mimes:png,jpeg,jpg' //GAMBAR DIVALIDASI HARUS BERTIPE PNG,JPG DAN JPEG
        ]);

        //JIKA FILENYA ADA
        if ($request->hasFile('image')) {
            //MAKA KITA SIMPAN SEMENTARA FILE TERSEBUT KEDALAM VARIABLE FILE
            $file = $request->file('image');
            //KEMUDIAN NAMA FILENYA KITA BUAT CUSTOMER DENGAN PERPADUAN TIME DAN SLUG DARI NAMA PRODUK. ADAPUN EXTENSIONNYA KITA GUNAKAN BAWAAN FILE TERSEBUT
            $filename = time() . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            //SIMPAN FILENYA KEDALAM FOLDER PUBLIC/PRODUCTS, DAN PARAMETER KEDUA ADALAH NAMA CUSTOM UNTUK FILE TERSEBUT
            $file->storeAs('public/products', $filename);

            //SETELAH FILE TERSEBUT DISIMPAN, KITA SIMPAN INFORMASI PRODUKNYA KEDALAM DATABASE
            $product = Product::create([
                'name' => $request->name,
                'slug' => $request->name,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'image' => $filename, //PASTIKAN MENGGUNAKAN VARIABLE FILENAM YANG HANYA BERISI NAMA FILE SAJA (STRING)
                'price' => $request->price,
                'weight' => $request->weight,
                'discount_id' => $request->discount_id,
                'stock' => 0,
                'status' => $request->status
            ]);
            //JIKA SUDAH MAKA REDIRECT KE LIST PRODUK
            return redirect(route('product.index'))->with(['success' => 'Produk Baru Ditambahkan']);
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id); //QUERY UNTUK MENGAMBIL DATA PRODUK BERDASARKAN ID
        //HAPUS FILE IMAGE DARI STORAGE PATH DIIKUTI DENGNA NAMA IMAGE YANG DIAMBIL DARI DATABASE
        File::delete(storage_path('app/public/products/' . $product->image));
        //KEMUDIAN HAPUS DATA PRODUK DARI DATABASE
        $product->delete();
        //DAN REDIRECT KE HALAMAN LIST PRODUK
        return redirect(route('product.index'))->with(['success' => 'Produk Sudah Dihapus']);
    }

    // public function massUploadForm()
    // {
    //     $category = Category::orderBy('name', 'DESC')->get();
    //     return view('produk.bulk', compact('category'));
    // }

    // public function massUpload(Request $request)
    // {
    // //VALIDASI DATA YANG DIKIRIM
    //     $this->validate($request, [
    //         'category_id' => 'required|exists:categories,id',
    //         'file' => 'required|mimes:xlsx' //PASTIKAN FORMAT FILE YANG DITERIMA ADALAH XLSX
    //     ]);

    //     //JIKA FILE-NYA ADA
    //     if ($request->hasFile('file')) {
    //         $file = $request->file('file');
    //         $filename = time() . '-product.' . $file->getClientOriginalExtension();
    //         $file->storeAs('public/uploads', $filename); //MAKA SIMPAN FILE TERSEBUT DI STORAGE/APP/PUBLIC/UPLOADS

    //         //BUAT JADWAL UNTUK PROSES FILE TERSEBUT DENGAN MENGGUNAKAN JOB
    //         //ADAPUN PADA DISPATCH KITA MENGIRIMKAN DUA PARAMETER SEBAGAI INFORMASI
    //         //YAKNI KATEGORI ID DAN NAMA FILENYA YANG SUDAH DISIMPAN
    //         ProductJob::dispatch($request->category_id, $filename);
    //         return redirect()->back()->with(['success' => 'Upload Produk Dijadwalkan']);
    //     }
    // }

    public function edit($id)
    {
        $product = Product::find($id); //AMBIL DATA PRODUK TERKAIT BERDASARKAN ID
        $category = Category::orderBy('name', 'DESC')->get(); //AMBIL SEMUA DATA KATEGORI
        return view('produk.edit', compact('product', 'category')); //LOAD VIEW DAN PASSING DATANYA KE VIEW
    }

    public function update(Request $request, $id)
    {
    //VALIDASI DATA YANG DIKIRIM
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|integer',
            'weight' => 'required|integer',
            // 'discount_id' => 'required|exists:discounts,id',
            'image' => 'nullable|image|mimes:png,jpeg,jpg' //IMAGE BISA NULLABLE
        ]);

        $product = Product::find($id); //AMBIL DATA PRODUK YANG AKAN DIEDIT BERDASARKAN ID
        $filename = $product->image; //SIMPAN SEMENTARA NAMA FILE IMAGE SAAT INI

        //JIKA ADA FILE GAMBAR YANG DIKIRIM
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            //MAKA UPLOAD FILE TERSEBUT
            $file->storeAs('public/products', $filename);
            //DAN HAPUS FILE GAMBAR YANG LAMA
            File::delete(storage_path('app/public/products/' . $product->image));
        }

    //KEMUDIAN UPDATE PRODUK TERSEBUT
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'weight' => $request->weight,
            'discount_id' => $request->discount_id,
            'status' => $request->status,
            'image' => $filename
        ]);
        return redirect(route('product.index'))->with(['success' => 'Data Produk Diperbaharui']);
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'added_stock' => 'required|integer|min:1',
            'harga_beli' => 'required|numeric|min:0',
            'total_beli' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'profit' => 'required|numeric|min:0'
        ]);

        $product = Product::findOrFail($request->input('product_id'));
        $product->stock += $request->input('added_stock');
        $product->price = $request->input('price'); // Update selling price in products table
        $product->save();

        // Save stock history
        StockHistory::create([
            'product_id' => $product->id,
            'added_stock' => $request->input('added_stock'),
            'harga_beli' => $request->input('harga_beli'),
            'total_beli' => $request->input('total_beli'),
            'profit' => $request->input('profit')
        ]);

        return redirect()->route('product.index')->with('success', 'Stok produk berhasil ditambahkan');
    }


}
