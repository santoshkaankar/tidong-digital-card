<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\PartnerApprovalCenterController;
use App\Http\Controllers\Partner\PartnerApplicationController;
use Tests\TestCase;

final class SerialPartnerApprovalWorkflowTest extends TestCase
{
    public function test_new_approval_module_defaults_to_off(): void
    {
        $this->assertFalse((bool) config('tidong_modules.partner_approvals.default_enabled'));
        $this->assertTrue((bool) config('tidong_modules.partner_approvals.installed'));
    }

    public function test_guest_cannot_access_private_approval_pages(): void
    {
        $this->get(route('partner.application.index'))->assertRedirect(route('login'));
        $this->post(route('partner.application.store'), [])->assertRedirect(route('login'));
        $this->get(route('admin.approval-center.index'))->assertRedirect(route('login'));
        $this->post(route('admin.approval-center.decide', ['type'=>'professional','user'=>999999]), [])
            ->assertRedirect(route('login'));
    }

    public function test_review_and_submission_are_separate_actions(): void
    {
        $this->assertTrue(method_exists(PartnerApplicationController::class,'store'));
        $this->assertTrue(method_exists(PartnerApprovalCenterController::class,'decide'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.approval-center.index'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.approval-center.queue'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.approval-center.decide'));
    }

    public function test_vendor_approval_page_displays_email_step(): void
    {
        $source = file_get_contents(resource_path('views/vendor-management/show.blade.php'));
        $this->assertStringContainsString('Email Verification Required', $source);
        $this->assertStringContainsString('Pending — email link required', $source);
    }
}
