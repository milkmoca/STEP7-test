<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //商品一覧画面
    //CRUD→Read(読み取り)
    //メゾット→index=データ一覧表示
    public function index(Request $request)
    {
        $products = Product::all();
        $companies = Company::get();
        //Productモデルに基づいて操作要求(クリエ)を初期化
        //この行の後にクエリを逐次構築
        $query = Product::query();

        if($search = $request->search){
            $query->where('product_name','LIKE',"%{$search}%");
        }

        if($search = $request->search){
            $query->where('company_id',  'LIKE', "%{$search}%");
        }
    
        
        $products = $query->paginate(10);
        $products = Product::all();
        $companies = Company::get();
    
        // 商品一覧ビューを表示し、取得した商品情報をビューに渡す
        return view('products.index', ['products' => $products], compact('companies', 'products'));
       
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    //Create(作成)
    //create=新規作成用フォーム表示
    public function create()
    {
        //商品新規登録画面
        //会社情報必要
        $companies = Company::all();
                
        return view('products.create', compact('companies'));
        
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    //Create(作成)
     //store=データ新規保存
     public function store(Request $request) // フォームから送られたデータを　$requestに代入して引数として渡している
     {
         
         $request->validate([
             'product_name' => 'required', //requiredは必須という意味です
             'company_id' => 'required',
             'price' => 'required',
             'stock' => 'required',
             'comment' => 'nullable', //'nullable'はそのフィールドが未入力でもOKという意味です
             'img_path' => 'nullable|image|max:2048',
         ]);
        
         $product = new Product([
             'product_name' => $request->get('product_name'),
             'company_id' => $request->get('company_id'),
             'price' => $request->get('price'),
             'stock' => $request->get('stock'),
             'comment' => $request->get('comment'),
         ]);
         
 
 
 
         // リクエストに画像が含まれている場合、その画像を保存します。
         if($request->hasFile('img_path')){ 
             $filename = $request->img_path->getClientOriginalName();
             $filePath = $request->img_path->storeAs('products', $filename, 'public');
             $product->img_path = '/storage/' . $filePath;
         }
         
         // 作成したデータベースに新しいレコードとして保存
         $product->save();
 
         // 全ての処理が終わったら、商品一覧画面に戻ります。
         return redirect('products');
     }
 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     //Read(読み取り)
     //show=データ個別表示
    public function show($id)
    {
        //商品情報詳細画面
        //指定されたIDでデータベースから検索する
        $product = Product::find($id);
        $companies = Company::all();

        return view('products.show',compact('companies','product') );

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //Update(更新)
    //edit=データ編集用フォーム表示
    public function edit($id)
    {
        $product = Product::find($id);
        $companies = Company::all();
        //→会社情報が必要

        return view('products.edit', compact('product', 'companies'));

    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //Update(更新)
    //update=データ更新
    public function update(Request $request, Product $product)
    {
        //更新ボタン
        DB::beginTransaction();

        try {
            $request->validate([
                'product_name' => 'required',
                'price' => 'required',
                'stock' => 'required',
            ]);
    
            //商品情報を更新
            //モデルの値を書き換える
            $product->product_name = $request->product_name;
            $product->price = $request->price;
            $product->stock = $request->stock;
    
            $product->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back();
        }

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
        //→ビューにメッセージを送る(代入：success)
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //Delete(削除)
    //destroy=データ削除
        /**
     * 削除処理
     */
    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            $product_id = $product->id;
            $product->delete();

            return to_route('/products');

        }catch (\Exception $e){
            report($e);
            session()->flash('flash_message', '更新が失敗しました');
        }
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully');
    }
}
