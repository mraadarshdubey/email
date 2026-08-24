<?php

namespace App\Services;

use App\Models\AutomationSequence;
use App\Models\AutomationSequenceEnrollment;
use App\Models\EmailContact;

class SequenceEnrollmentService
{
    public function enrollForTag(EmailContact $contact, int $tagId): void
    {
        $sequences = AutomationSequence::active()
            ->where('user_id', $contact->user_id)
            ->where('trigger_tag_id', $tagId)
            ->with('steps')
            ->get();

        foreach ($sequences as $sequence) {
            $this->enroll($sequence, $contact);
        }
    }

    public function enroll(AutomationSequence $sequence, EmailContact $contact): ?AutomationSequenceEnrollment
    {
        $alreadyEnrolled = AutomationSequenceEnrollment::where('sequence_id', $sequence->id)
            ->where('email_contact_id', $contact->id)
            ->exists();

        if ($alreadyEnrolled) {
            return null;
        }

        $firstStep = $sequence->steps->first();

        if (!$firstStep) {
            return null;
        }

        return AutomationSequenceEnrollment::create([
            'sequence_id' => $sequence->id,
            'email_contact_id' => $contact->id,
            'current_step' => 0,
            'status' => 'active',
            'next_run_at' => now()->addMinutes($firstStep->delay_minutes),
            'enrolled_at' => now(),
        ]);
    }
}
