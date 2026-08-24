<?php

namespace App\Http\Controllers;

use App\Models\ContactTag;
use App\Models\LeadForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LeadFormController extends Controller
{
    private const AVAILABLE_FIELDS = ['first_name', 'last_name', 'phone', 'company'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $forms = LeadForm::where('user_id', Auth::id())
            ->withCount('submissions')
            ->with('tag')
            ->orderByDesc('created_at')
            ->get();

        return view('lead-forms.index', compact('forms'));
    }

    public function create()
    {
        return view('lead-forms.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateForm($request);
        $data['user_id'] = Auth::id();
        $data['slug'] = $this->uniqueSlug($request->name);

        LeadForm::create($data);

        return redirect()->route('lead-forms.index')->with('success', 'Form created.');
    }

    public function edit(LeadForm $lead_form)
    {
        $this->authorizeOwner($lead_form);

        return view('lead-forms.edit', array_merge(
            $this->formData(),
            ['form' => $lead_form]
        ));
    }

    public function update(Request $request, LeadForm $lead_form)
    {
        $this->authorizeOwner($lead_form);
        $data = $this->validateForm($request);
        $lead_form->update($data);

        return redirect()->route('lead-forms.index')->with('success', 'Form updated.');
    }

    public function destroy(LeadForm $lead_form)
    {
        $this->authorizeOwner($lead_form);
        $lead_form->delete();

        return back()->with('success', 'Form deleted.');
    }

    public function toggleActive(LeadForm $lead_form)
    {
        $this->authorizeOwner($lead_form);
        $lead_form->update(['is_active' => !$lead_form->is_active]);

        return back()->with('success', 'Form ' . ($lead_form->is_active ? 'activated' : 'paused') . '.');
    }

    private function authorizeOwner(LeadForm $form): void
    {
        abort_unless($form->user_id === Auth::id(), 403);
    }

    private function formData(): array
    {
        return [
            'tags' => ContactTag::where('user_id', Auth::id())->get(),
            'availableFields' => self::AVAILABLE_FIELDS,
        ];
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'form';
        $slug = $base . '-' . Str::lower(Str::random(5));

        while (LeadForm::where('slug', $slug)->exists()) {
            $slug = $base . '-' . Str::lower(Str::random(5));
        }

        return $slug;
    }

    private function validateForm(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'headline' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'fields' => 'nullable|array',
            'fields.*' => 'in:' . implode(',', self::AVAILABLE_FIELDS),
            'tag_id' => 'nullable|exists:contact_tags,id',
            'success_message' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $validator->validate();

        return [
            'name' => $request->name,
            'headline' => $request->headline ?: $request->name,
            'description' => $request->description,
            'fields_config' => $request->input('fields', []),
            'tag_id' => $request->tag_id ?: null,
            'success_message' => $request->success_message ?: 'Thanks for signing up!',
            'is_active' => $request->has('is_active'),
        ];
    }
}
