<?php

namespace App\Services;

use App\Models\AutomationRule;
use App\Models\EmailContact;

class AutomationRuleService
{
    public function __construct(private TrackedMailerService $mailer)
    {
    }


    public function fireContactCreated(EmailContact $contact): void
    {
        $rules = AutomationRule::active()
            ->where('user_id', $contact->user_id)
            ->where('trigger_type', 'contact_created')
            ->get();

        foreach ($rules as $rule) {
            $this->runRule($rule, $contact);
        }
    }

    public function fireTagAdded(EmailContact $contact, int $tagId): void
    {
        $rules = AutomationRule::active()
            ->where('user_id', $contact->user_id)
            ->where('trigger_type', 'contact_tagged')
            ->where('trigger_tag_id', $tagId)
            ->get();

        foreach ($rules as $rule) {
            $this->runRule($rule, $contact);
        }
    }

    protected function runRule(AutomationRule $rule, EmailContact $contact): void
    {
        if ($rule->action_type !== 'send_email') {
            return;
        }

        if (!$rule->emailTemplate || !$rule->emailAccount) {
            return;
        }

        $this->mailer->send(
            $rule->user_id,
            $rule->emailAccount,
            $contact->email,
            $rule->emailTemplate->subject,
            $rule->emailTemplate->body
        );

        $rule->increment('runs_count');
        $rule->update(['last_run_at' => now()]);
    }
}
