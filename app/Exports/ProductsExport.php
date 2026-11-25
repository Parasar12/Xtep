<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\ProductMaster;

// use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductsExport implements FromView
{
    public function view(): View
    {
        return view('exports.products', [
            'products' => Product::all()
        ]);
    }
}
