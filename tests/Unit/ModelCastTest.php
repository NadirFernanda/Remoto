<?php

namespace Tests\Unit;

use App\Models\FreelancerProfile;
use App\Models\KycSubmission;
use Tests\TestCase;

class ModelCastTest extends TestCase
{
    public function test_kyc_submission_reviewed_at_is_cast_to_datetime(): void
    {
        $casts = (new KycSubmission())->getCasts();

        $this->assertArrayHasKey('reviewed_at', $casts);
        $this->assertEquals('datetime', $casts['reviewed_at']);
    }

    public function test_kyc_submission_fillable_includes_required_fields(): void
    {
        $fillable = (new KycSubmission())->getFillable();

        foreach (['user_id', 'status', 'document_type', 'admin_notes'] as $field) {
            $this->assertContains($field, $fillable, "Campo '$field' deveria estar em fillable");
        }
    }

    public function test_freelancer_profile_skills_cast_to_array(): void
    {
        $casts = (new FreelancerProfile())->getCasts();

        $this->assertArrayHasKey('skills', $casts);
        $this->assertEquals('array', $casts['skills']);
    }

    public function test_freelancer_profile_metrics_cast_to_array(): void
    {
        $casts = (new FreelancerProfile())->getCasts();

        $this->assertArrayHasKey('metrics', $casts);
        $this->assertEquals('array', $casts['metrics']);
    }

    public function test_freelancer_profile_fillable_includes_kyc_status(): void
    {
        $fillable = (new FreelancerProfile())->getFillable();

        $this->assertContains('kyc_status', $fillable);
    }
}
