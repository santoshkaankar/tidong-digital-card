<?php
namespace App\Services\Food;
use Illuminate\Validation\ValidationException;
final class ProceedTiffinPricing {
    public static function rupeesToPaise(string $amount): int {
        if (!preg_match('/^\d{1,7}(?:\.\d{1,2})?$/D',$amount)) {
            throw ValidationException::withMessages(['price'=>'Invalid rupee amount.']);
        }
        [$whole,$fraction]=array_pad(explode('.',$amount,2),2,'');
        return (int)$whole*100+(int)str_pad($fraction,2,'0');
    }
    public static function total(array $rows): int {
        $total=0;
        foreach($rows as $r){
            $unit=(int)$r['unit_mrp_paise']; $qty=(int)$r['quantity'];
            if($unit<1 || $qty<1 || $qty>100){
                throw ValidationException::withMessages(['components'=>'Invalid item MRP or quantity.']);
            }
            $total+=$unit*$qty;
            if($total>999999999)throw ValidationException::withMessages(['components'=>'Amount exceeds limit.']);
        }
        return $total;
    }
    public static function sale(string $amount, int $mrp): int {
        $price=trim($amount)==='' ? $mrp : self::rupeesToPaise($amount);
        if($price<1||$price>$mrp)throw ValidationException::withMessages(['sale_price'=>'Sale price must be between zero and MRP.']);
        return $price;
    }
    public static function scheduleKey(string $scope,string $date,string $slot): string {
        if(!in_array($slot,array_keys(config('proceed_tiffin.slots',[])),true)
          ||!in_array($scope,['day','everyday'],true))throw ValidationException::withMessages(['meal_type'=>'Invalid schedule.']);
        return $scope==='everyday' ? 'everyday:'.$slot : 'day:'.$date.':'.$slot;
    }
}