<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'sku',
        'type', // goods or service
        'name',
        'image',
        'category_id',
        'uom_id',
        'currency_id',
        'standard_price',
        'stock',
        'min_stock',
        'supplier_id',
        'photo_path',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Generate SKU with format KAT-KODE-YYMMDD-URUT
     */
    public static function generateSku(?int $categoryId = null, ?string $type = 'barang'): string
    {
        $kat = 'GEN';
        if ($categoryId) {
            $category = Category::find($categoryId);
            if ($category) {
                $words = preg_split('/[\s&]+/', trim($category->name));
                if (count($words) >= 3) {
                    $kat = strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1).substr($words[2], 0, 1));
                } elseif (count($words) === 2) {
                    $kat = strtoupper(substr($words[0], 0, 2).substr($words[1], 0, 1));
                } else {
                    $kat = strtoupper(substr($category->name, 0, 3));
                }
            }
        }

        $kode = ($type === 'jasa') ? 'JSA' : 'BRG';
        $yymmdd = now()->format('ymd');

        $prefix = "{$kat}-{$kode}-{$yymmdd}";
        $latest = self::where('sku', 'like', "{$prefix}-%")->latest('id')->first();

        $seq = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest->sku, $matches)) {
            $seq = intval($matches[1]) + 1;
        }

        $urut = str_pad($seq, 3, '0', STR_PAD_LEFT);

        return "{$prefix}-{$urut}";
    }

    /**
     * Get auto-formatted SKU adhering to KAT-KODE-YYMMDD-URUT
     */
    public function getFormattedSkuAttribute(): string
    {
        if (preg_match('/^[A-Z0-9]{2,5}-[A-Z0-9]{2,4}-\d{6}-\d+$/', $this->sku)) {
            return $this->sku;
        }

        $kat = 'GEN';
        if ($this->category) {
            $words = preg_split('/[\s&]+/', trim($this->category->name));
            if (count($words) >= 3) {
                $kat = strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1).substr($words[2], 0, 1));
            } elseif (count($words) === 2) {
                $kat = strtoupper(substr($words[0], 0, 2).substr($words[1], 0, 1));
            } else {
                $kat = strtoupper(substr($this->category->name, 0, 3));
            }
        } elseif (preg_match('/SKU-([A-Z]+)-/i', $this->sku, $m)) {
            $kat = strtoupper($m[1]);
        }

        $kode = ($this->type === 'jasa') ? 'JSA' : 'BRG';
        $yymmdd = $this->created_at ? $this->created_at->format('ymd') : now()->format('ymd');

        $urutNum = $this->id;
        if (preg_match('/(\d+)$/', $this->sku, $m)) {
            $urutNum = intval($m[1]);
        }
        $urut = str_pad($urutNum, 3, '0', STR_PAD_LEFT);

        return "{$kat}-{$kode}-{$yymmdd}-{$urut}";
    }
}
