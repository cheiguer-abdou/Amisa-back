<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'image', 'description', 'initPrice', 'price', 'quantity',];

    public function getSalesPerMonthAttribute()
    {
        $salesPerMonth = $this->orders()->selectRaw('MONTH(created_at) as month, SUM(quantity) as total_quantity')
            ->groupBy('month')
            ->pluck('total_quantity', 'month')
            ->toArray();

        $formattedSales = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::createFromFormat('m', $i)->format('F');
            $formattedSales[$monthName] = $salesPerMonth[$i] ?? 0;
        }

        return $formattedSales;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
