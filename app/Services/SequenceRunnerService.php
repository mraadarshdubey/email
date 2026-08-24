<?php

namespace App\Services;

use App\Models\AutomationSequenceEnrollment;

class SequenceRunnerService
{
    public function __construct(private TrackedMailerService $mailer)
    {
    }

    /**
     * Process every enrollment across all users that is due to run.
     * Safe to call frequently — it only acts on rows whose next_run_at has passed.
     */
    public function processDue(?int $sequenceId = null): int
    {
        $query = AutomationSequenceEnrollment::where('status', 'active')
            ->where('next_run_at', '<=', now())
            ->with(['sequence.steps', 'sequence.emailAccount', 'contact']);

        if ($sequenceId) {
            $query->where('sequence_id', $sequenceId);
        }

        $processed = 0;

        foreach ($query->get() as $enrollment) {
            $this->runStep($enrollment);
            $processed++;
        }

        return $processed;
    }

    protected function runStep(AutomationSequenceEnrollment $enrollment): void
    {
        $sequence = $enrollment->sequence;
        $contact = $enrollment->contact;

        if (!$sequence || !$contact || !$sequence->emailAccount) {
            $enrollment->update(['status' => 'cancelled']);
            return;
        }

        $steps = $sequence->steps;
        $step = $steps->get($enrollment->current_step);

        if (!$step) {
            $enrollment->update(['status' => 'completed', 'completed_at' => now()]);
            return;
        }

        $this->mailer->send(
            $sequence->user_id,
            $sequence->emailAccount,
            $contact->email,
            $step->emailTemplate->subject ?? $sequence->name,
            $step->emailTemplate->body ?? ''
        );

        $nextStep = $steps->get($enrollment->current_step + 1);

        if ($nextStep) {
            $enrollment->update([
                'current_step' => $enrollment->current_step + 1,
                'next_run_at' => now()->addMinutes($nextStep->delay_minutes),
            ]);
        } else {
            $enrollment->update([
                'current_step' => $enrollment->current_step + 1,
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }
}
