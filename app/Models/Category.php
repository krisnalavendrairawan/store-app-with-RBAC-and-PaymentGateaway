<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'category';
    protected $guarded = ['id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_by'];
    protected $fillable = [];

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            $category->created_by = Auth::id();
        });

        static::updating(function (Category $category) {
            $category->updated_by = Auth::id();
        });

        static::deleting(function (Category $category) {
            self::whereId($category->id)->update(['deleted_by' => Auth::id()]);
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

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
