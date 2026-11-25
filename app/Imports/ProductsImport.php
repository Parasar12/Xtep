<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductMaster;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;


class ProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $validator = Validator::make($rows->toArray(), [
            '*.name' => 'required|unique:products,name|max:255',
            '*.slug' => 'required|unique:products,slug|max:255',
            '*.category_id' => 'required|numeric',
            '*.description' => 'required|max:2000',
            '*.cost_price' => 'required',
            '*.selling_price' => 'required',
            '*.quantity' => 'required|numeric',
            '*.sku' => 'required|unique:products,sku|max:255',
            '*.fiscal_year_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            foreach ($rows as $key => $row) {

                $product = Product::create([
                    'brand_id' => $row['brand_id'],
                    'name' => $row['name'],
                    'slug' => $row['slug'],
                    'description' => $row['description'],
                    'category_id' => $row['category_id'],
                    'is_active' => 1,
                    'sku' => $row['sku'],
                    'image' => $row['image'],
                    'fiscal_year_id'=>$row['fiscal_year_id']

                ]);

                if ($product) {
                    ProductMaster::where('product_id', $product->id)->update([
                        'is_active' => 0
                    ]);
                    $profit = $row['selling_price'] - $row['cost_price'];

                    ProductMaster::create([
                        'product_id' => $product->id,
                        'is_active' => 1,
                        'price' => $row['selling_price'],
                        'cost_price' => $row['cost_price'],
                        'quantity' => $row['quantity'],
                        'profit_margin'=>$profit
                    ]);
                }
            }
            return response()->json(['status' => 1, 'msg' => 'Bulk Products Imported Successfully']);
        }
    }
}
