<?php

namespace App\Http\Controllers;

use App\Models\AutomationRule;
use App\Models\ContactTag;
use App\Models\EmailAccount;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AutomationRuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $rules = AutomationRule::where('user_id', Auth::id())
            ->with(['triggerTag', 'emailTemplate', 'emailAccount'])
            ->orderByDesc('created_at')
            ->get();

        return view('automation-rules.index', compact('rules'));
    }

    public function create()
    {
        return view('automation-rules.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateRule($request);
        $data['user_id'] = Auth::id();

        AutomationRule::create($data);

        return redirect()->route('automation-rules.index')->with('success', 'Automation rule created.');
    }

    public function edit(AutomationRule $automation_rule)
    {
        $this->authorizeOwner($automation_rule);

        return view('automation-rules.edit', array_merge(
            $this->formData(),
            ['rule' => $automation_rule]
        ));
    }

    public function update(Request $request, AutomationRule $automation_rule)
    {
        $this->authorizeOwner($automation_rule);

        $data = $this->validateRule($request);
        $automation_rule->update($data);

        return redirect()->route('automation-rules.index')->with('success', 'Automation rule updated.');
    }

    public function destroy(AutomationRule $automation_rule)
    {
        $this->authorizeOwner($automation_rule);
        $automation_rule->delete();

        return back()->with('success', 'Automation rule deleted.');
    }

    public function toggleActive(AutomationRule $automation_rule)
    {
        $this->authorizeOwner($automation_rule);
        $automation_rule->update(['is_active' => !$automation_rule->is_active]);

        return back()->with('success', 'Automation rule ' . ($automation_rule->is_active ? 'activated' : 'deactivated') . '.');
    }

    private function authorizeOwner(AutomationRule $rule): void
    {
        abort_unless($rule->user_id === Auth::id(), 403);
    }

    private function formData(): array
    {
        return [
            'tags' => ContactTag::where('user_id', Auth::id())->get(),
            'templates' => EmailTemplate::where('is_active', true)->get(),
            'emailAccounts' => EmailAccount::where('is_active', true)->get(),
        ];
    }

    private function validateRule(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'trigger_type' => 'required|in:contact_created,contact_tagged',
            'trigger_tag_id' => 'required_if:trigger_type,contact_tagged|nullable|exists:contact_tags,id',
            'email_template_id' => 'required|exists:email_templates,id',
            'email_account_id' => 'required|exists:email_accounts,id',
            'is_active' => 'nullable|boolean',
        ]);

        $validator->validate();

        return [
            'name' => $request->name,
            'trigger_type' => $request->trigger_type,
            'trigger_tag_id' => $request->trigger_type === 'contact_tagged' ? $request->trigger_tag_id : null,
            'action_type' => 'send_email',
            'email_template_id' => $request->email_template_id,
            'email_account_id' => $request->email_account_id,
            'is_active' => $request->has('is_active'),
        ];
    }
}
