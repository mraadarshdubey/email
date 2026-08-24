<?php

namespace App\Http\Controllers;

use App\Models\EmailContact;
use App\Models\LeadForm;
use App\Models\LeadFormSubmission;
use App\Services\AutomationRuleService;
use App\Services\SequenceEnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeadFormPublicController extends Controller
{
    public function show(string $slug)
    {
        $form = LeadForm::active()->where('slug', $slug)->firstOrFail();

        return view('lead-forms.public', ['form' => $form, 'submitted' => false]);
    }

    public function submit(Request $request, string $slug, AutomationRuleService $rules, SequenceEnrollmentService $sequences)
    {
        $form = LeadForm::active()->where('slug', $slug)->firstOrFail();

        // Honeypot: a hidden field real visitors never fill in.
        if ($request->filled('website')) {
            return view('lead-forms.public', ['form' => $form, 'submitted' => true]);
        }

        $rulesSet = ['email' => 'required|email|max:255'];
        foreach (['first_name', 'last_name', 'phone', 'company'] as $field) {
            if ($form->hasField($field)) {
                $rulesSet[$field] = 'nullable|string|max:255';
            }
        }

        $validator = Validator::make($request->all(), $rulesSet);

        if ($validator->fails()) {
            return view('lead-forms.public', [
                'form' => $form,
                'submitted' => false,
                'errors' => $validator->errors(),
                'old' => $request->all(),
            ]);
        }

        $contact = EmailContact::where('user_id', $form->user_id)
            ->where('email', $request->email)
            ->first();

        $isNewContact = !$contact;

        if (!$contact) {
            $contact = EmailContact::create([
                'user_id' => $form->user_id,
                'email' => $request->email,
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'phone' => $request->input('phone'),
                'company' => $request->input('company'),
            ]);
        } else {
            $contact->update(array_filter([
                'first_name' => $contact->first_name ?: $request->input('first_name'),
                'last_name' => $contact->last_name ?: $request->input('last_name'),
                'phone' => $contact->phone ?: $request->input('phone'),
                'company' => $contact->company ?: $request->input('company'),
            ]));
        }

        if ($isNewContact) {
            $rules->fireContactCreated($contact);
        }

        if ($form->tag_id) {
            $result = $contact->tags()->syncWithoutDetaching([$form->tag_id]);
            if (!empty($result['attached'])) {
                $rules->fireTagAdded($contact, $form->tag_id);
                $sequences->enrollForTag($contact, $form->tag_id);
            }
        }

        LeadFormSubmission::create([
            'lead_form_id' => $form->id,
            'email_contact_id' => $contact->id,
            'ip_address' => $request->ip(),
            'submitted_at' => now(),
        ]);

        $form->increment('submissions_count');

        return view('lead-forms.public', ['form' => $form, 'submitted' => true]);
    }
}
