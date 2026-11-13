<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Support\SupplierStatement;
use Illuminate\Contracts\View\View;

class SupplierStatementController extends Controller
{
    public function print(Supplier $supplier): View
    {
        return view('suppliers.statement-print', [
            'supplier' => $supplier,
            'statement' => SupplierStatement::build($supplier),
        ]);
    }
}
