<?php

namespace App\Services\Delivery;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Services\Platform\ModuleRegistry;
use App\Services\Platform\FeatureRegistry;
use Illuminate\Validation\ValidationException;

class DeliveryService
{
    private ?string $oneTimePin = null;

    public function takeHandoffPin(): ?string
    {
        $pin = $this->oneTimePin;
        $this->oneTimePin = null;
        return $pin;
    }

    private function requireEnabled(): void
    {
        abort_unless(app(ModuleRegistry::class)->isEnabled('delivery'), 503, 'Delivery module is disabled.');
    }

    private function requireFeature(string $code): void
    {
        abort_unless(app(FeatureRegistry::class)->isEnabled($code), 503, 'This delivery feature is disabled.');
    }

    private function access(): VendorDeliveryAccess
    {
        return app(VendorDeliveryAccess::class);
    }

    /** Vendor-owned business roster; partners never become automatically available to other firms. */
    public function inviteToBusiness(int $businessId, int $userId, string $vehicleType, ?string $phone, int $actorId): int
    {
        $this->requireEnabled();
        $this->requireFeature('delivery.partner_enrollment');
        $this->access()->assert($actorId, $businessId, null, 'delivery.partners.manage');
        return DB::transaction(function () use ($businessId, $userId, $vehicleType, $phone, $actorId): int {
            $partner = DB::table('delivery_partners')->where('user_id', $userId)->lockForUpdate()->first();
            if ($partner) {
                if (app(DeliveryIdentity::class)->isIneligibleRider($userId)) {
                    throw ValidationException::withMessages(['user_id' => 'This account is no longer eligible for delivery work.']);
                }
                $partnerId = (int) $partner->id;
            } else {
                $partnerId = $this->onboard($userId, $vehicleType, $phone, $actorId);
            }
            if (DB::table('delivery_business_partners')->where('business_id', $businessId)
                ->where('partner_id', $partnerId)->exists()) {
                throw ValidationException::withMessages(['email' => 'Partner is already registered with this business.']);
            }
            DB::table('delivery_business_partners')->insert([
                'business_id' => $businessId, 'partner_id' => $partnerId,
                'status' => 'pending', 'invited_by' => $actorId,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $this->audit($actorId, 'delivery.business.partner.invited', 'business', $businessId,
                ['partner_id' => $partnerId]);
            return $partnerId;
        });
    }

    private function onboard(int $userId, string $vehicleType, ?string $phone, int $actorId): int
    {
        $this->requireEnabled();
        return DB::transaction(function () use ($userId, $vehicleType, $phone, $actorId): int {
            $user = DB::table('users')->where('id', $userId)->lockForUpdate()->first();
            if (! $user) {
                throw ValidationException::withMessages(['user_id' => 'Account not found.']);
            }
            if (DB::table('delivery_partners')->where('user_id', $userId)->exists()) {
                throw ValidationException::withMessages(['user_id' => 'A partner record already exists.']);
            }
            if (app(DeliveryIdentity::class)->isIneligibleRider($userId)) {
                throw ValidationException::withMessages(['user_id' => 'Vendor and platform staff accounts cannot become delivery partners.']);
            }
            $id = DB::table('delivery_partners')->insertGetId([
                'user_id' => $userId, 'vehicle_type' => $vehicleType, 'phone' => $phone,
                'status' => 'pending', 'created_by' => $actorId,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $this->audit($actorId, 'delivery.partner.onboarded', 'delivery_partner', $id,
                ['user_id' => $userId, 'status' => 'pending']);
            return $id;
        });
    }

    public function verifyForBusiness(int $businessId, int $partnerId, int $actorId): void
    {
        $this->requireEnabled();
        $this->requireFeature('delivery.partner_approval');
        $this->access()->assert($actorId, $businessId, null, 'delivery.partners.manage');
        DB::transaction(function () use ($businessId, $partnerId, $actorId): void {
            $link = DB::table('delivery_business_partners')->where('business_id', $businessId)
                ->where('partner_id', $partnerId)->lockForUpdate()->first();
            abort_unless($link, 404);
            DB::table('delivery_business_partners')->where('id', $link->id)->update([
                'verified_by' => $actorId, 'verified_at' => now(), 'updated_at' => now(),
            ]);
            $this->audit($actorId, 'delivery.business.partner.screened', 'business', $businessId,
                ['partner_id' => $partnerId]);
        });
    }

    public function setRosterStatus(int $businessId, int $partnerId, string $status, int $actorId): void
    {
        $this->requireEnabled();
        $this->requireFeature('delivery.partner_approval');
        $this->access()->assert($actorId, $businessId, null, 'delivery.partners.manage');
        if (! in_array($status, ['active', 'suspended'], true)) {
            throw ValidationException::withMessages(['status' => 'Invalid partner status.']);
        }
        DB::transaction(function () use ($businessId, $partnerId, $status, $actorId): void {
            $link = DB::table('delivery_business_partners')->where('business_id', $businessId)
                ->where('partner_id', $partnerId)->lockForUpdate()->first();
            abort_unless($link, 404);
            if ($status === 'active' && ! $link->verified_at) {
                throw ValidationException::withMessages(['partner' => 'Verify this business partner before approval.']);
            }
            if ($status === 'suspended' && DB::table('delivery_jobs')
                ->where('partner_id', $partnerId)->where('business_id', $businessId)
                ->where(fn ($q) => $q->whereIn('status', ['assigned', 'accepted', 'picked_up'])
                    ->orWhere(fn ($failed) => $failed->where('status','failed')->whereNull('return_confirmed_at')))
                ->exists()) {
                throw ValidationException::withMessages(['partner' => 'Resolve outstanding business deliveries before suspension.']);
            }
            if ($status === 'active') {
                $partner = DB::table('delivery_partners')->where('id', $partnerId)->lockForUpdate()->first();
                abort_unless($partner, 404);
                if (app(DeliveryIdentity::class)->isIneligibleRider((int)$partner->user_id)) {
                    throw ValidationException::withMessages(['partner' => 'This account can no longer be approved as a delivery partner.']);
                }
                // Admin or individually authorized Admin Employee MUST approve first.
                // A vendor may approve their own roster but cannot approve platform identity.
                if (! app(DeliveryIdentity::class)->isPlatformApproved((int)$partner->user_id)) {
                    throw ValidationException::withMessages(['partner' => 'Platform approval is required before vendor activation.']);
                }
                $this->grantPartnerIdentity((int) $partner->user_id, $actorId);
                // Approval by a second business must not force an intentionally offline rider online.
                $availability = $partner->status === 'active' ? (bool)$partner->is_available : false; // Rider must explicitly go online.
                DB::table('delivery_partners')->where('id', $partnerId)->update([
                    'status' => 'active', 'is_available' => $availability,
                    'verification_status' => 'verified', 'verified_by' => $actorId,
                    'verified_at' => now(), 'updated_at' => now(),
                ]);
            }
            DB::table('delivery_business_partners')->where('id', $link->id)->update([
                'status' => $status,
                'approved_by' => $status === 'active' ? $actorId : $link->approved_by,
                'approved_at' => $status === 'active' ? ($link->approved_at ?? now()) : $link->approved_at,
                'updated_at' => now(),
            ]);
            if ($status === 'suspended' && ! DB::table('delivery_business_partners')
                ->where('partner_id', $partnerId)->where('status', 'active')->exists()) {
                DB::table('delivery_partners')->where('id', $partnerId)->update([
                    'status' => 'suspended', 'is_available' => false, 'updated_at' => now(),
                ]);
            }
            $this->audit($actorId, 'delivery.business.partner.status', 'business', $businessId,
                ['partner_id' => $partnerId, 'old_status' => $link->status, 'new_status' => $status]);
        });
    }

    public function setAvailability(int $userId, bool $available): void
    {
        $this->requireEnabled();
        $this->requireFeature('delivery.partner_progress');
        DB::transaction(function () use ($userId, $available): void {
            $partner = DB::table('delivery_partners')->where('user_id', $userId)->lockForUpdate()->first();
            abort_unless($partner && app(DeliveryIdentity::class)->hasActiveRiderCredentials($userId), 403);
            if (!$available && DB::table('delivery_jobs')->where('partner_id', $partner->id)
                ->where(fn ($q) => $q->whereIn('status', ['assigned','accepted','picked_up'])
                    ->orWhere(fn ($failed) => $failed->where('status', 'failed')->whereNull('return_confirmed_at')))->exists()) {
                throw ValidationException::withMessages(['availability' => 'Complete or reassign current deliveries before going offline.']);
            }
            DB::table('delivery_partners')->where('id', $partner->id)->update([
                'is_available' => $available, 'updated_at' => now(),
            ]);
            $this->audit($userId, 'delivery.partner.availability', 'delivery_partner', (int)$partner->id,
                ['is_available'=>$available]);
        });
    }

    private function grantPartnerIdentity(int $userId, int $actorId): void
    {
        $type = DB::table('account_types')->where('code', 'delivery')->where('is_active', true)->first();
        $role = DB::table('roles')->where('code', 'delivery_partner')->first();
        if (! $type || ! $role) {
            throw ValidationException::withMessages(['partner' => 'Run the existing DigitalFoundationSeeder to provision the delivery role and account type.']);
        }
        $currentAccount = DB::table('user_account_types')->where('user_id',$userId)
            ->where('account_type_id',$type->id)->lockForUpdate()->first();
        if ($currentAccount) {
            DB::table('user_account_types')->where('id',$currentAccount->id)->update([
                'status'=>'active', 'activated_at'=>now(), 'updated_at'=>now(),
            ]);
        } else {
            DB::table('user_account_types')->insert([
                'user_id'=>$userId, 'account_type_id'=>$type->id, 'status'=>'active',
                'activated_at'=>now(), 'created_at'=>now(), 'updated_at'=>now(),
            ]);
        }
        $existing = DB::table('user_role_assignments')->where('user_id', $userId)
            ->where('role_id', $role->id)->where('scope_type', 'platform')->whereNull('scope_id')
            ->lockForUpdate()->first();
        if ($existing) {
            DB::table('user_role_assignments')->where('id', $existing->id)->update([
                'is_active' => true, 'starts_at' => now(), 'expires_at' => null, 'updated_at' => now(),
            ]);
        } else {
            DB::table('user_role_assignments')->insert([
                'user_id' => $userId, 'role_id' => $role->id,
                'scope_type' => 'platform', 'scope_id' => null, 'assigned_by' => $actorId,
                'is_active' => true, 'starts_at' => now(), 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function createJob(array $data, int $actorId): int
    {
        $this->requireEnabled();
        $this->requireFeature('delivery.vendor_dispatch');
        $this->access()->assert($actorId, (int)$data['business_id'], (int)$data['branch_id'], 'delivery.jobs.manage');
        // Generated PIN only held in request memory and flashed once to dispatcher.
        $this->oneTimePin = null;
        return DB::transaction(function () use ($data, $actorId): int {
            $branch = DB::table('branches as br')->join('businesses as bu','bu.id','=','br.business_id')
                ->join('vendors as v','v.id','=','bu.vendor_id')
                ->where('br.id', (int) $data['branch_id'])
                ->where('br.business_id', (int) $data['business_id'])
                ->whereNull('br.deleted_at')->whereNull('bu.deleted_at')->whereNull('v.deleted_at')
                ->where('br.status','active')->where('bu.status','approved')->where('v.status','approved')
                ->first(['br.id']);
            if (! $branch) {
                throw ValidationException::withMessages(['branch_id'=>'Select an active branch belonging to an approved business and vendor.']);
            }
            $partner = DB::table('delivery_partners')->where('id', (int) $data['partner_id'])
                ->where('status', 'active')->where('is_available', true)->lockForUpdate()->first();
            if (! $partner || ! app(DeliveryIdentity::class)->hasActiveRiderCredentials((int)$partner->user_id)
                || ! DB::table('delivery_business_partners')
                ->where('business_id', (int)$data['business_id'])
                ->where('partner_id', (int)$data['partner_id'])->where('status', 'active')->exists()) {
                throw ValidationException::withMessages(['partner_id'=>'Select a partner approved by this business.']);
            }
            $posOrderId = $data['pos_order_id'] ?? null;
            if ($posOrderId) {
                $order = DB::table('pos_orders')->where('id', $posOrderId)
                    ->where('branch_id', $data['branch_id'])->where('business_id',$data['business_id'])
                    ->whereNull('dining_table_id')->lockForUpdate()->first();
                if (! $order || in_array($order->status, ['cancelled', 'voided'], true)) {
                    throw ValidationException::withMessages(['pos_order_id' => 'Order must be active, in the same branch, and not a dine-in table order.']);
                }
                if (DB::table('delivery_jobs')->where('pos_order_id', $posOrderId)
                    ->whereNotIn('status', ['cancelled'])->exists()) {
                    throw ValidationException::withMessages(['pos_order_id' => 'This order already has a delivery job.']);
                }
            }
            $pin = (string) random_int(100000, 999999);
            $job = DB::table('delivery_jobs')->insertGetId([
                'reference' => 'DLV-'.Str::upper((string) Str::ulid()),
                'business_id' => $data['business_id'], 'branch_id' => $data['branch_id'],
                'pos_order_id' => $posOrderId, 'partner_id' => $partner->id,
                'status' => 'assigned', 'recipient_name' => $data['recipient_name'],
                'recipient_phone' => $data['recipient_phone'],
                'dropoff_address' => $data['dropoff_address'],
                'delivery_note' => $data['delivery_note'] ?? null,
                'handoff_pin_hash' => Hash::make($pin),
                'delivery_fee_paise' => (int)($data['delivery_fee_paise'] ?? 0),
                'partner_earning_paise' => (int)($data['partner_earning_paise'] ?? 0),
                'created_by' => $actorId, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $this->event($job, $actorId, 'created', null, 'assigned', ['partner_id' => $partner->id]);
            $this->audit($actorId, 'delivery.job.created', 'delivery_job', $job,
                ['branch_id' => $data['branch_id'], 'partner_id'=>$partner->id, 'pos_order_id'=>$posOrderId]);
            $this->oneTimePin = $pin;
            return $job;
        });
    }

    public function progress(int $jobId, int $actorId, string $target, bool $asPartner, array $proof = []): void
    {
        $this->requireEnabled();
        $this->requireFeature($asPartner ? 'delivery.partner_progress' : 'delivery.vendor_dispatch');
        if (! $asPartner) $this->access()->assertJob($actorId, $jobId);
        $result = DB::transaction(function () use ($jobId, $actorId, $target, $asPartner, $proof): string {
            $job = DB::table('delivery_jobs')->where('id', $jobId)->lockForUpdate()->first();
            if (! $job) abort(404);
            if ($asPartner) {
                $partner = DB::table('delivery_partners')->where('id', $job->partner_id)
                    ->where('user_id', $actorId)->where('status', 'active')->first();
                abort_unless($partner, 403);
                abort_unless(app(DeliveryIdentity::class)->hasActiveRiderCredentials($actorId), 403);
                abort_unless(DB::table('delivery_business_partners')
                    ->where('business_id', (int)$job->business_id)
                    ->where('partner_id', (int)$job->partner_id)
                    ->where('status', 'active')->exists(), 403);
            }
            $allowed = $asPartner ? [
                'assigned' => ['accepted', 'declined'],
                'accepted' => ['picked_up'],
                'picked_up' => ['delivered', 'failed'],
            ] : [
                'assigned' => ['cancelled'], 'accepted' => ['cancelled'],
                'declined' => ['cancelled'], 'failed' => ['cancelled'],
            ];
            if (! in_array($target, $allowed[$job->status] ?? [], true)) {
                throw ValidationException::withMessages(['status' => 'Invalid delivery status transition.']);
            }
            if (!$asPartner && $target === 'cancelled' && $job->status === 'failed' && !$job->return_confirmed_at) {
                throw ValidationException::withMessages(['job' => 'Confirm physical return before cancelling a failed collected parcel.']);
            }
            $dates = [];
            if ($target === 'delivered') {
                $pin = (string)($proof['handoff_pin'] ?? '');
                // Lockout is persistent PER JOB, not only per HTTP user/IP. Do not throw
                // within the transaction after a failed PIN or the counter would roll back.
                if ($job->handoff_locked_until && now()->lt($job->handoff_locked_until)) {
                    return 'locked';
                }
                if (!$job->handoff_pin_hash || !Hash::check($pin, $job->handoff_pin_hash)) {
                    $previous = $job->handoff_locked_until && now()->gte($job->handoff_locked_until)
                        ? 0 : (int) $job->handoff_failed_attempts;
                    $attempts = $previous + 1;
                    $lock = $attempts >= 5 ? now()->addMinutes(15) : null;
                    DB::table('delivery_jobs')->where('id', $jobId)->update([
                        'handoff_failed_attempts' => $attempts,
                        'handoff_last_failed_at' => now(),
                        'handoff_locked_until' => $lock,
                        'updated_at' => now(),
                    ]);
                    $this->event($jobId, $actorId, 'handoff_pin_rejected', $job->status, $job->status,
                        ['attempt_count' => $attempts, 'locked' => $lock !== null]);
                    $this->audit($actorId, 'delivery.handoff.pin_rejected', 'delivery_job', $jobId,
                        ['attempt_count' => $attempts, 'locked' => $lock !== null]);
                    return $lock ? 'locked' : 'invalid_pin';
                }
                $dates['handoff_failed_attempts'] = 0;
                $dates['handoff_locked_until'] = null;
                $name = trim((string)($proof['recipient_name'] ?? ''));
                if ($name === '') {
                    throw ValidationException::withMessages(['recipient_name' => 'Recipient name is required.']);
                }
                $dates['handoff_verified_at'] = now();
                $dates['proof_recipient_name'] = $name;
            }
            if ($target === 'failed') {
                $reason = trim((string)($proof['failure_reason'] ?? ''));
                if ($reason === '') throw ValidationException::withMessages(['failure_reason' => 'Reason is required.']);
                $dates['failure_reason'] = $reason;
            }
            if ($target === 'accepted') $dates['accepted_at'] = now();
            if ($target === 'picked_up') $dates['picked_up_at'] = now();
            if ($target === 'delivered') $dates['delivered_at'] = now();
            DB::table('delivery_jobs')->where('id', $jobId)->update(array_merge([
                'status'=>$target, 'updated_at'=>now(),
            ],$dates));
            $this->event($jobId, $actorId, 'status', $job->status, $target);
            $this->audit($actorId, 'delivery.job.status', 'delivery_job', $jobId,
                ['old_status'=>$job->status,'new_status'=>$target]);
            return 'ok';
        });
        if ($result !== 'ok') {
            // Deliberately outside the DB transaction: rejected attempts must remain recorded.
            throw ValidationException::withMessages(['handoff_pin' => $result === 'locked'
                ? 'Too many attempts. Delivery PIN temporarily locked. Contact your dispatcher.'
                : 'Incorrect confirmation PIN.']);
        }
    }

    /** Rotate compromised/lost PIN; only authorized firm dispatchers can reissue it. */
    public function reissuePin(int $jobId, int $actorId): void
    {
        $this->requireEnabled();
        $this->requireFeature('delivery.vendor_dispatch');
        $this->access()->assertJob($actorId, $jobId);
        $this->oneTimePin = null;
        DB::transaction(function () use ($jobId, $actorId): void {
            $job = DB::table('delivery_jobs')->where('id', $jobId)->lockForUpdate()->first();
            abort_unless($job, 404);
            if (!in_array($job->status, ['assigned', 'accepted', 'picked_up'], true)) {
                throw ValidationException::withMessages(['job' => 'Only active deliveries can receive a replacement PIN.']);
            }
            // Never reissue the SAME code by chance; verify against the previous hash.
            do { $pin = (string) random_int(100000, 999999); }
            while ($job->handoff_pin_hash && Hash::check($pin, $job->handoff_pin_hash));
            DB::table('delivery_jobs')->where('id', $jobId)->update([
                'handoff_pin_hash' => Hash::make($pin), 'handoff_failed_attempts' => 0,
                'handoff_locked_until' => null, 'handoff_pin_rotated_at' => now(),
                'updated_at' => now(),
            ]);
            $this->event($jobId, $actorId, 'handoff_pin_rotated', $job->status, $job->status);
            $this->audit($actorId, 'delivery.handoff.pin_rotated', 'delivery_job', $jobId,
                ['business_id' => $job->business_id, 'branch_id' => $job->branch_id]);
            $this->oneTimePin = $pin;
        });
    }

    public function confirmReturned(int $jobId, int $actorId): void
    {
        $this->requireEnabled();
        $this->requireFeature('delivery.vendor_dispatch');
        $this->access()->assertJob($actorId, $jobId);
        DB::transaction(function () use ($jobId, $actorId): void {
            $job = DB::table('delivery_jobs')->where('id', $jobId)->lockForUpdate()->first();
            if (!$job) abort(404);
            if ($job->status !== 'failed') {
                throw ValidationException::withMessages(['job' => 'Only failed, collected deliveries need physical return confirmation.']);
            }
            if ($job->return_confirmed_at) return;
            DB::table('delivery_jobs')->where('id', $jobId)->update([
                'return_confirmed_at' => now(), 'return_confirmed_by'=>$actorId, 'updated_at'=>now(),
            ]);
            $this->event($jobId, $actorId, 'returned_to_branch', 'failed', 'failed');
            $this->audit($actorId, 'delivery.job.return_confirmed', 'delivery_job', $jobId, ['confirmed'=>true]);
        });
    }

    public function reassign(int $jobId, int $partnerId, int $actorId): void
    {
        $this->requireEnabled();
        $this->requireFeature('delivery.vendor_dispatch');
        $this->access()->assertJob($actorId, $jobId);
        $this->oneTimePin = null;
        DB::transaction(function () use ($jobId,$partnerId,$actorId): void {
            $job = DB::table('delivery_jobs')->where('id', $jobId)->lockForUpdate()->first();
            if (! $job) abort(404);
            if (! in_array($job->status, ['assigned','declined','failed'],true)) {
                throw ValidationException::withMessages(['partner_id'=>'Only uncollected, declined or failed deliveries can be reassigned.']);
            }
            if ($job->status === 'failed' && !$job->return_confirmed_at) {
                throw ValidationException::withMessages(['job' => 'Confirm parcel return before reassigning a failed pickup.']);
            }
            $partner = DB::table('delivery_partners')->where('id', $partnerId)
                ->where('status', 'active')->where('is_available', true)->first();
            if (! $partner || ! app(DeliveryIdentity::class)->hasActiveRiderCredentials((int)$partner->user_id)
                || (int)$job->partner_id === $partnerId
                || ! DB::table('delivery_business_partners')->where('business_id', $job->business_id)
                    ->where('partner_id', $partnerId)->where('status', 'active')->exists()) {
                throw ValidationException::withMessages(['partner_id'=>'Choose another partner approved for this business.']);
            }
            // A reassigned parcel gets a NEW recipient PIN; never reuse a prior handoff proof.
            do { $pin = (string) random_int(100000, 999999); }
            while ($job->handoff_pin_hash && Hash::check($pin, $job->handoff_pin_hash));
            DB::table('delivery_jobs')->where('id',$jobId)->update([
                'partner_id'=>$partnerId,'status'=>'assigned','accepted_at'=>null,
                'picked_up_at'=>null, 'failure_reason'=>null, 'return_confirmed_at'=>null,
                'return_confirmed_by'=>null, 'handoff_pin_hash'=>Hash::make($pin),
                'handoff_failed_attempts'=>0, 'handoff_locked_until'=>null,
                'handoff_pin_rotated_at'=>now(), 'updated_at'=>now(),
            ]);
            $this->oneTimePin = $pin;
            $this->event($jobId, $actorId, 'reassigned', $job->status, 'assigned',
                ['old_partner_id'=>$job->partner_id,'new_partner_id'=>$partnerId]);
            $this->audit($actorId, 'delivery.job.reassigned', 'delivery_job', $jobId,
                ['old_partner_id'=>$job->partner_id,'new_partner_id'=>$partnerId]);
        });
    }

    private function event(int $jobId,int $actorId,string $action,?string $old,string $new,array $details = []): void
    {
        DB::table('delivery_job_events')->insert([
            'delivery_job_id'=>$jobId,'actor_user_id'=>$actorId,'action'=>$action,
            'old_status'=>$old,'new_status'=>$new,
            'details'=>$details ? json_encode($details) : null, 'created_at'=>now(),
        ]);
    }

    private function audit(int $actorId,string $action,string $type,int $id,array $details):void
    {
        DB::table('audit_logs')->insert([
            'actor_user_id'=>$actorId,'action'=>$action,'entity_type'=>$type,'entity_id'=>$id,
            'new_values'=>json_encode($details),'created_at'=>now(),
        ]);
    }
}
