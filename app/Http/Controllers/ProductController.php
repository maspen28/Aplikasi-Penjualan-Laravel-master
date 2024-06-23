<?php

namespace App\Http\Controllers;

use App\Jobs\ProductJob;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductController extends Controller {
  public function index() {
    $product = Product::with(['category'])->orderBy('created_at', 'DESC');
    if (request()->q != '') {
      $product = $product->where('name', 'LIKE', '%' . request()->q . '%');
    }
    $product = $product->paginate(10);
    return view('produk.produk', compact('product'));
  }

  public function create() {
    $category = Category::orderBy('name', 'DESC')->get();

    return view('produk.create', compact('category'));
  }

  public function store(Request $request) {
    $this->validate($request, [
      'name' => 'required|string|max:100',
      'description' => 'required',
      'category_id' => 'required|exists:categories,id',
      'price' => 'required|integer',
      'weight' => 'required|integer',
      'stock' => 'required|integer',
      'image' => 'required|image|mimes:png,jpeg,jpg',
    ]);

    if ($request->hasFile('image')) {
      $file = $request->file('image');
      $filename = time() . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
      $filePath = $file->storeAs('public/products', $filename);
      $imageUrl = asset('storage/products/' . $filename);

      $product = Product::create([
        'name' => $request->name,
        'slug' => Str::slug($request->name),
        'category_id' => $request->category_id,
        'description' => $request->description,
        'image' => $imageUrl,
        'price' => $request->price,
        'weight' => $request->weight,
        'stock' => $request->stock,
        'status' => $request->status,
      ]);

      return redirect(route('product.index'))->with(['success' => 'Produk Baru Ditambahkan']);
    }
  }

  public function destroy($id) {
    $product = Product::find($id); //QUERY UNTUK MENGAMBIL DATA PRODUK BERDASARKAN ID
    //HAPUS FILE IMAGE DARI STORAGE PATH DIIKUTI DENGNA NAMA IMAGE YANG DIAMBIL DARI DATABASE
    File::delete(storage_path('app/public/products/' . $product->image));
    //KEMUDIAN HAPUS DATA PRODUK DARI DATABASE
    $product->delete();
    //DAN REDIRECT KE HALAMAN LIST PRODUK
    return redirect(route('product.index'))->with(['success' => 'Produk Sudah Dihapus']);
  }

  public function massUploadForm() {
    $category = Category::orderBy('name', 'DESC')->get();
    return view('produk.bulk', compact('category'));
  }

  public function massUpload(Request $request) {
    //VALIDASI DATA YANG DIKIRIM
    $this->validate($request, [
      'category_id' => 'required|exists:categories,id',
      'file' => 'required|mimes:xlsx', //PASTIKAN FORMAT FILE YANG DITERIMA ADALAH XLSX
    ]);

    //JIKA FILE-NYA ADA
    if ($request->hasFile('file')) {
      $file = $request->file('file');
      $filename = time() . '-product.' . $file->getClientOriginalExtension();
      $file->storeAs('public/uploads', $filename); //MAKA SIMPAN FILE TERSEBUT DI STORAGE/APP/PUBLIC/UPLOADS

      //BUAT JADWAL UNTUK PROSES FILE TERSEBUT DENGAN MENGGUNAKAN JOB
      //ADAPUN PADA DISPATCH KITA MENGIRIMKAN DUA PARAMETER SEBAGAI INFORMASI
      //YAKNI KATEGORI ID DAN NAMA FILENYA YANG SUDAH DISIMPAN
      ProductJob::dispatch($request->category_id, $filename);
      return redirect()->back()->with(['success' => 'Upload Produk Dijadwalkan']);
    }
  }

  public function edit($id) {
    $product = Product::find($id); //AMBIL DATA PRODUK TERKAIT BERDASARKAN ID
    $category = Category::orderBy('name', 'DESC')->get(); //AMBIL SEMUA DATA KATEGORI
    return view('produk.edit', compact('product', 'category')); //LOAD VIEW DAN PASSING DATANYA KE VIEW
  }

  public function update(Request $request, $id) {
    $this->validate($request, [
      'name' => 'required|string|max:100',
      'description' => 'required',
      'category_id' => 'required|exists:categories,id',
      'price' => 'required|integer',
      'weight' => 'required|integer',
      'image' => 'nullable|image|mimes:png,jpeg,jpg',
    ]);

    $product = Product::find($id);
    $filename = $product->image;

    if ($request->hasFile('image')) {
      $file = $request->file('image');
      $filename = time() . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
      $filePath = $file->storeAs('public/products', $filename);
      $imageUrl = asset('storage/products/' . $filename);
      File::delete(storage_path('app/public/products/' . $product->image));
    } else {
      $imageUrl = $product->image;
    }

    $product->update([
      'name' => $request->name,
      'description' => $request->description,
      'category_id' => $request->category_id,
      'price' => $request->price,
      'weight' => $request->weight,
      'image' => $imageUrl,
    ]);

    return redirect(route('product.index'))->with(['success' => 'Data Produk Diperbaharui']);
  }

}
