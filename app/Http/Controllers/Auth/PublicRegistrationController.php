<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Identity\AccountType;
use App\Models\User;
use App\Models\Vendor\Vendor;
use App\Services\Auth\RegistrationAffiliateIntent;
use App\Notifications\RegistrationVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class PublicRegistrationController extends Controller
{
    private const TYPES = [
        'vendor' => 'vendor_owner',
        'delivery' => 'delivery_partner',
        'professional' => 'professional',
        'service_provider' => 'service_provider',
        'customer' => 'customer',
    ];

    public function show(Request $request): View
    {
        $this->ensureRegistrationEnabled();
        $referral = strtoupper(trim((string) $request->query('ref', $request->session()->get('affiliate_referral_code', ''))));
        $leg = strtoupper(trim((string) $request->query('leg', $request->session()->get('affiliate_referral_leg', 'A'))));
        if ($referral !== '' && ! preg_match('/^[A-F0-9]{16}$/', $referral)) {
            abort(404);
        }
        if (! in_array($leg, ['A', 'B'], true)) {
            abort(404);
        }
        // Persist a valid referral-link association through POST; never trust its hidden input alone.
        if ($referral !== '') {
            abort_unless(DB::table('affiliate_members')->where('referral_code', $referral)
                ->where('status', 'active')->exists(), 404);
            $request->session()->put('affiliate_referral_code', $referral);
            $request->session()->put('affiliate_referral_leg', $leg);
        }
        return view('auth.register-v2', [
            'referralCode' => $referral ?: $this->rootReferralCode(),
            'referralLinked' => $referral !== '',
            'selectedLeg' => $leg,
            'registrationTypes' => array_keys(array_filter(config('tidong_registration.types', []))),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureRegistrationEnabled();
        abort_unless(config('tidong_registration.terms_url'), 503, 'Terms & Conditions URL must be configured.');
        $request->merge([
            'email' => Str::lower(trim((string) $request->input('email'))),
            'username' => Str::lower(trim((string) $request->input('username'))),
            'mobile' => preg_replace('/[\s\-()]/', '', (string) $request->input('mobile')),
        ]);
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'mobile' => ['required', 'regex:/^\+?[0-9]{8,15}$/', 'unique:users,mobile'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'username' => ['required', 'string', 'min:4', 'max:30', 'regex:/^[a-z][a-z0-9._-]+$/', 'unique:users,username'],
            'account_type' => ['required', Rule::in(array_keys(self::TYPES))],
            'vendor_name' => ['required_if:account_type,vendor', 'nullable', 'string', 'min:2', 'max:255'],
            'referral_code' => ['nullable', 'string', 'size:16', 'regex:/^[a-fA-F0-9]{16}$/'],
            'leg' => ['nullable', Rule::in(['A','B'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
        ]);
        $type = $data['account_type'];
        if (! config("tidong_registration.types.{$type}", false)) {
            throw ValidationException::withMessages(['account_type' => 'Registration for this account type is unavailable.']);
        }
        // No public signup can set an arbitrary role, vendor ownership or admin permission.
        if ($type === 'customer') {
            $this->ensureCustomerRegistrationEnabled();
            abort_unless(DB::getSchemaBuilder()->hasTable('affiliate_settings')
                && DB::getSchemaBuilder()->hasTable('registration_affiliate_intents'), 503);
            $linkedCode = $request->session()->get('affiliate_referral_code');
            $code = $linkedCode ?: null;
            if ($linkedCode && strtoupper((string) ($data['referral_code'] ?? '')) !== $linkedCode) {
                throw ValidationException::withMessages(['referral_code' => 'The referral link cannot be changed.']);
            }
            if (! $linkedCode) {
                $providedCode = strtoupper((string) ($data['referral_code'] ?? ''));
                $rootCode = $this->rootReferralCode();
                if ($providedCode && $providedCode !== $rootCode) {
                    throw ValidationException::withMessages(['referral_code' => 'Choose a verified referral link.']);
                }
                // For direct Left placement, preserve the existing left-chain algorithm.
                $code = ($data['leg'] ?? 'A') === 'B' ? $rootCode : null;
                if (($data['leg'] ?? 'A') === 'B' && ! $rootCode) {
                    throw ValidationException::withMessages(['leg' => 'The network root is not ready yet.']);
                }
            }
            if ($code && ! DB::table('affiliate_members')->where('referral_code', $code)
                ->where('status','active')->exists()) {
                throw ValidationException::withMessages(['referral_code' => 'Invalid referral.']);
            }
            $data['affiliate_code'] = $code;
            $data['affiliate_leg'] = $data['leg'] ?? $request->session()->get('affiliate_referral_leg', 'A');
        }

        $user = DB::transaction(function () use ($data, $type): User {
            $account = AccountType::query()->where('code', $type)->where('is_active', true)->first();
            $role = DB::table('roles')->where('code', self::TYPES[$type])->first();
            if (! $account || ! $role) {
                throw ValidationException::withMessages(['account_type' => 'This account type is not configured.']);
            }
            $user = User::create([
                'name' => trim($data['name']), 'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            $user->mobile = $data['mobile'];
            $user->username = $data['username'];
            $user->save();
            $user->userAccountTypes()->create([
                'account_type_id' => $account->id, 'status' => 'active', 'activated_at' => now(),
            ]);
            $scopeType = 'platform';
            $scopeId = null;
            if ($type === 'vendor') {
                // Vendor identity is the registered person's name; NO OUTLET is created.
                $vendor = Vendor::create([
                    'user_id' => $user->id, 'vendor_code' => 'VND-'.Str::upper((string) Str::ulid()),
                    'display_name' => trim($data['vendor_name']), 'status' => 'pending',
                ]);
                $scopeType = 'vendor';
                $scopeId = $vendor->id;
            } elseif ($type === 'delivery') {
                // The delivery-partner profile needs vehicle details/approval later.
                DB::table('delivery_partners')->insert([
                    'user_id' => $user->id, 'phone' => $user->mobile,
                    'vehicle_type' => 'pending', 'status' => 'pending',
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            } elseif ($type === 'customer') {
                DB::table('registration_affiliate_intents')->insert([
                    'user_id' => $user->id, 'referral_code' => $data['affiliate_code'],
                    'placement_leg' => $data['affiliate_leg'], 'status' => 'pending_verification',
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            $user->roleAssignments()->create([
                'role_id' => $role->id, 'scope_type' => $scopeType,
                'scope_id' => $scopeId, 'starts_at' => now(), 'is_active' => true,
            ]);
            return $user;
        }, 3);
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget(['affiliate_referral_code','affiliate_referral_leg']);
        try {
            $user->notify(new RegistrationVerifyEmail());
        } catch (\Throwable $error) {
            Log::warning('Registration verification email was not sent.', ['user_id' => $user->id]);
        }
        return redirect()->route('registration.status')
            ->with('success', 'Your account was created. Please verify your email and finish onboarding.');
    }

    public function status(Request $request): View
    {
        $user = $request->user();
        $type = $user->userAccountTypes()->where('status','active')
            ->whereHas('accountType', fn ($q) => $q->whereIn('code', array_keys(self::TYPES)))
            ->with('accountType')->latest('id')->first()?->accountType?->code;
        abort_unless($type, 403);
        $intent = $type === 'customer' && DB::getSchemaBuilder()->hasTable('registration_affiliate_intents')
            ? DB::table('registration_affiliate_intents')->where('user_id', $user->id)->first()
            : null;
        $partnerApplication = in_array($type, ['professional', 'service_provider'], true)
            && DB::getSchemaBuilder()->hasTable('partner_approval_applications')
            ? DB::table('partner_approval_applications')->where('user_id', $user->id)
                ->where('account_type', $type)->first() : null;
        return view('auth.registration-status', compact('user','type','intent','partnerApplication'));
    }

    public function sendVerification(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->email_verified_at) {
            try {
                $user->notify(new RegistrationVerifyEmail());
            } catch (\Throwable $error) {
                Log::warning('Verification email could not be resent.', ['user_id' => $user->id]);
                return back()->withErrors(['email' => 'Verification email service is unavailable.']);
            }
        }
        return back()->with('success','If needed, a verification email has been sent.');
    }

    public function verifyEmail(Request $request, int $id, string $hash, RegistrationAffiliateIntent $affiliate): RedirectResponse
    {
        $user = User::query()->findOrFail($id);
        abort_unless(hash_equals(sha1((string) $user->email), $hash), 403);
        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }
        $affiliate->complete((int) $user->id);
        return redirect()->route(Auth::check() && Auth::id() === $user->id ? 'registration.status' : 'login')
            ->with('success','Your email is verified.');
    }

    public function requestPasswordForm(): View
    {
        return view('auth.forgot-password-v2');
    }

    public function sendPasswordReset(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required','email']]);
        Password::sendResetLink(['email' => Str::lower(trim($request->email))]);
        return back()->with('success','If the email is registered, a reset link will be sent.');
    }

    public function resetPasswordForm(Request $request, string $token): View
    {
        return view('auth.reset-password-v2', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required','string'], 'email' => ['required','email'],
            'password' => ['required','min:8','confirmed'],
        ]);
        $result = Password::reset($data, function (User $user, string $password): void {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
        });
        return $result === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Your password has been updated.')
            : back()->withErrors(['email' => __($result)]);
    }

    private function ensureRegistrationEnabled(): void
    {
        abort_unless(app(\App\Services\Platform\ModuleRegistry::class)->isEnabled('public_registration'), 404);
    }

    private function ensureCustomerRegistrationEnabled(): void
    {
        abort_unless(config('tidong_customer.public_registration_enabled', false)
            && config('tidong_customer.enabled', true)
            && app(\App\Services\Platform\ModuleRegistry::class)->isEnabled('customer'), 404);
    }

    private function rootReferralCode(): ?string
    {
        if (! DB::getSchemaBuilder()->hasTable('affiliate_settings')) {
            return null;
        }
        $id = DB::table('affiliate_settings')->where('id',1)->value('root_member_id');
        return $id ? DB::table('affiliate_members')->where('id',$id)->value('referral_code') : null;
    }
}
