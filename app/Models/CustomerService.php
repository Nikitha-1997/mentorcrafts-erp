<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerService extends Model
{
     use HasFactory;
     protected $fillable = ['customer_id', 'service_id', 'service_code'];
     public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    // Generate service code (e.g., BU-001)
    public static function generateServiceCode($serviceName)
{
    // Step 1: Generate abbreviation from the first letter of each word
    $words = preg_split('/\s+/', trim($serviceName));
    $prefix = strtoupper(implode('', array_map(fn($word) => $word[0], $words)));

   // $prefix = strtoupper(substr($serviceName, 0, 2));
    $monthYear = now()->format('my'); // e.g., 1025

    // Find latest code for this service type (prefix)
    $latestCode = self::where('service_code', 'LIKE', "{$prefix}-%")
        ->orderBy('id', 'desc')
        ->value('service_code');

    if ($latestCode) {
        // Extract numeric part
        preg_match('/(\d+)$/', $latestCode, $matches);
        $count = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
    } else {
        $count = 1;
    }

    return sprintf('%s-%s-%03d', $prefix, $monthYear, $count);
}

    /*public static function generateServiceCode($serviceName)
{
    // Step 1: Generate abbreviation from the first letter of each word
    $words = preg_split('/\s+/', trim($serviceName));
    $prefix = strtoupper(implode('', array_map(fn($word) => $word[0], $words)));

    // Step 2: Get current month and year (MMYY)
    $monthYear = date('my'); // e.g., 1025 for October 2025

    // Step 3: Find the last service code for this prefix (continuous count)
    $latestCode = self::where('service_code', 'LIKE', "{$prefix}-%")
                    ->orderBy('service_code', 'desc')
                    ->value('service_code');

    // Step 4: Determine next sequence number
    $nextNumber = 1;
    if ($latestCode) {
        $parts = explode('-', $latestCode);
        $nextNumber = isset($parts[2]) ? intval($parts[2]) + 1 : 1;
    }

    // Step 5: Return formatted code, e.g., SI-1025-001
    return sprintf('%s-%s-%03d', $prefix, $monthYear, $nextNumber);
}*/


}
