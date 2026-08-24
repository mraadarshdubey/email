<?php

namespace App\Http\Controllers;

use App\Models\AutomationSequence;
use App\Models\AutomationSequenceStep;
use App\Models\ContactTag;
use App\Models\EmailAccount;
use App\Models\EmailTemplate;
use App\Services\SequenceRunnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AutomationSequenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $sequences = AutomationSequence::where('user_id', Auth::id())
            ->withCount([
                'steps',
                'enrollments as active_enrollments_count' => fn ($q) => $q->where('status', 'active'),
                'enrollments as completed_enrollments_count' => fn ($q) => $q->where('status', 'completed'),
            ])
            ->with('triggerTag')
            ->orderByDesc('created_at')
            ->get();

        return view('automation-sequences.index', compact('sequences'));
    }

    public function create()
    {
        return view('automation-sequences.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateSequence($request);

        DB::transaction(function () use ($data, $request) {
            $sequence = AutomationSequence::create([
                'user_id' => Auth::id(),
                'name' => $data['name'],
                'trigger_tag_id' => $data['trigger_tag_id'],
                'email_account_id' => $data['email_account_id'],
                'is_active' => $data['is_active'],
            ]);

            $this->saveSteps($sequence, $request->input('steps', []));
        });

        return redirect()->route('automation-sequences.index')->with('success', 'Sequence created.');
    }

    public function show(AutomationSequence $automation_sequence)
    {
        $this->authorizeOwner($automation_sequence);

        $enrollments = $automation_sequence->enrollments()
            ->with('contact')
            ->orderByDesc('enrolled_at')
            ->get();

        $automation_sequence->load('steps.emailTemplate', 'triggerTag');

        return view('automation-sequences.show', [
            'sequence' => $automation_sequence,
            'enrollments' => $enrollments,
        ]);
    }

    public function edit(AutomationSequence $automation_sequence)
    {
        $this->authorizeOwner($automation_sequence);
        $automation_sequence->load('steps');

        return view('automation-sequences.edit', array_merge(
            $this->formData(),
            ['sequence' => $automation_sequence]
        ));
    }

    public function update(Request $request, AutomationSequence $automation_sequence)
    {
        $this->authorizeOwner($automation_sequence);
        $data = $this->validateSequence($request);

        DB::transaction(function () use ($data, $request, $automation_sequence) {
            $automation_sequence->update([
                'name' => $data['name'],
                'trigger_tag_id' => $data['trigger_tag_id'],
                'email_account_id' => $data['email_account_id'],
                'is_active' => $data['is_active'],
            ]);

            $automation_sequence->steps()->delete();
            $this->saveSteps($automation_sequence, $request->input('steps', []));
        });

        return redirect()->route('automation-sequences.index')->with('success', 'Sequence updated.');
    }

    public function destroy(AutomationSequence $automation_sequence)
    {
        $this->authorizeOwner($automation_sequence);
        $automation_sequence->delete();

        return back()->with('success', 'Sequence deleted.');
    }

    public function toggleActive(AutomationSequence $automation_sequence)
    {
        $this->authorizeOwner($automation_sequence);
        $automation_sequence->update(['is_active' => !$automation_sequence->is_active]);

        return back()->with('success', 'Sequence ' . ($automation_sequence->is_active ? 'activated' : 'paused') . '.');
    }

    public function runNow(AutomationSequence $automation_sequence, SequenceRunnerService $runner)
    {
        $this->authorizeOwner($automation_sequence);
        $count = $runner->processDue($automation_sequence->id);

        return back()->with('success', $count > 0 ? "Sent {$count} due step(s)." : 'Nothing due to send right now.');
    }

    private function authorizeOwner(AutomationSequence $sequence): void
    {
        abort_unless($sequence->user_id === Auth::id(), 403);
    }

    private function formData(): array
    {
        return [
            'tags' => ContactTag::where('user_id', Auth::id())->get(),
            'templates' => EmailTemplate::where('is_active', true)->get(),
            'emailAccounts' => EmailAccount::where('is_active', true)->get(),
        ];
    }

    private function saveSteps(AutomationSequence $sequence, array $steps): void
    {
        foreach (array_values($steps) as $index => $step) {
            if (empty($step['email_template_id'])) {
                continue;
            }

            AutomationSequenceStep::create([
                'sequence_id' => $sequence->id,
                'position' => $index,
                'delay_minutes' => (int) ($step['delay_minutes'] ?? 0),
                'email_template_id' => $step['email_template_id'],
            ]);
        }
    }

    private function validateSequence(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'trigger_tag_id' => 'required|exists:contact_tags,id',
            'email_account_id' => 'required|exists:email_accounts,id',
            'is_active' => 'nullable|boolean',
            'steps' => 'required|array|min:1',
            'steps.*.email_template_id' => 'required|exists:email_templates,id',
            'steps.*.delay_minutes' => 'nullable|integer|min:0',
        ]);

        $validator->validate();

        return [
            'name' => $request->name,
            'trigger_tag_id' => $request->trigger_tag_id,
            'email_account_id' => $request->email_account_id,
            'is_active' => $request->has('is_active'),
        ];
    }
}
