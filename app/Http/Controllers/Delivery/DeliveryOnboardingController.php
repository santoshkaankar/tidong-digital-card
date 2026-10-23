<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Services\Delivery\DeliveryIdentity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class DeliveryOnboardingController extends Controller
{
    private function pending(Request $request): object
    {
        $userId = (int)$request->user()->id;
        abort_if(app(DeliveryIdentity::class)->isIneligibleRider($userId),403);
        $allowed = DB::table('user_account_types as uat')
            ->join('account_types as at','at.id','=','uat.account_type_id')
            ->where('uat.user_id',$userId)->where('uat.status','active')
            ->where('at.code','delivery')->where('at.is_active',true)->exists();
        abort_unless($allowed,403);
        $partner = DB::table('delivery_partners')->where('user_id',$userId)->first();
        abort_unless($partner && $partner->status === 'pending' && ! $partner->approved_at,403);
        return $partner;
    }

    public function edit(Request $request): View
    {
        return view('delivery.profile-pending', ['partner' => $this->pending($request)]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'vehicle_type'=>['required',Rule::in(['bike','scooter','bicycle','car','van','on_foot'])],
            'phone'=>['required','regex:/^\+?[0-9]{8,15}$/'],
        ]);
        DB::transaction(function () use ($request,$data): void {
            $record = $this->pending($request);
            $locked = DB::table('delivery_partners')->where('id',$record->id)->lockForUpdate()->first();
            abort_unless($locked && $locked->status === 'pending' && ! $locked->approved_at,403);
            DB::table('delivery_partners')->where('id',$record->id)->update($data + ['updated_at'=>now()]);
        });
        return redirect()->route('registration.status')->with('success','Delivery profile saved. Platform approval is still required.');
    }
}
