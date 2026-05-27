<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'slug',
    'description',
    'parent_id',
    'is_active',

])]
class Category extends Model
{
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function product()
    {
        return $this->hasMany(Product::class);
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function parentRecursive()
    {
        return $this->parent()->with('parentRecursive');
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value
                ? url('storage/'.$value)
                : null,
        );
    }

    public function toggleTreeStatus(): void
    {
        $newStatus = ! $this->is_active;

        $ids = [];
        $this->collectDescendantIds($ids);

        $ids[] = $this->id;

        static::whereIn('id', $ids)->update([
            'is_active' => $newStatus,
        ]);
    }

    private function collectDescendantIds(array &$ids): void
    {
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $child->load('children');
            $child->collectDescendantIds($ids);
        }
    }

    public function getFullNameAttribute()
    {
        $names = [];
        $category = $this;

        while ($category) {
            array_unshift($names, $category->slug);
            $category = $category->parentRecursive;
        }

        $names = implode('-', $names);
        $lower_name = strtolower($names);
        $lower_name = preg_replace('/[^A-Za-z0-9-]/', '', $lower_name);
        return $lower_name;
    }
}
