<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'lot_number'])]
class Medication extends Model
{
    /** @use HasFactory<\Database\Factories\MedicationFactory> */
    use HasFactory;

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeLot(Builder $query, string $lotNumber): Builder
    {
        return $query->where('lot_number', $lotNumber);
    }
}
