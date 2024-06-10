<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function purchase(Request $request) {

        try{
            DB::beginTransaction();

              //リクエストから商品IDを取得
              $productId = $request->input('product_id');
              // dd($request);
              $product = Product::find($productId); //リクエストから商品IDを取得

              if(!$product) {
                DB::rollBack();
                return response()->json(['error' => '商品が存在しません']);
              }

              if($product->stock <= 0) {
                DB::rollBack();
                return response()->json(['error' => '在庫が不足しています']);
              }
              //Productsテーブルの在庫数を減らす
              $product->stock -= 1;
              $product->save();

              $sale = new Sale();
              $sale->product_id = $product->id;
              $sale->save();

            DB::commit();
            return ['success' => true];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => '購入処理に失敗しました'];
        }
        /*
        if(isset($result->success)) {
            return response()->json($result, 200);
        } else {
            return response()->json($result, 400);
        }
        */
    }
}
