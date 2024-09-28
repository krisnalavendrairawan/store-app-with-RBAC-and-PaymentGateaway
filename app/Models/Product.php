<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product';
    protected $guarded = ['id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_by'];
    protected $fillable = [];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            $product->created_by = Auth::id();
        });

        static::updating(function (Product $product) {
            $product->updated_by = Auth::id();
        });

        static::deleting(function (Product $product) {
            self::whereId($product->id)->update(['deleted_by' => Auth::id()]);
        });
    }

    protected function encryptedId(): Attribute
    {
        return Attribute::make(
            get: fn() => Crypt::encrypt($this->id)
        );
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->whereId(Crypt::decrypt($value))->firstOrFail();
    }

    //relasi ke category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    //relasi ke transaction
    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }
}
