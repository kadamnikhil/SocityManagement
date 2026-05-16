<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocietyFlat;
use App\Models\SocietyFlatDocument;
use App\Models\SocietyWing;
use App\Models\SocietyWingFloor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SocietySetupController extends Controller
{
    /**
     * Step 2 — wings and floor counts (scoped to the logged-in user).
     */
    public function wings(): View
    {
        $userId = auth()->id();
        $wings = SocietyWing::where('user_id', $userId)->orderBy('sort_order')->get();

        $existing = [
            'total' => $wings->count(),
            'floors' => $wings->pluck('floors_count')->values()->all(),
        ];

        return view('Admin.SocietySetup.wings', compact('existing', 'wings'));
    }

    /**
     * Save owner contact, per-type vehicle counts, and documents for one flat (same user only).
     */
    public function updateFlatDetail(Request $request, SocietyFlat $flat): RedirectResponse
    {
        abort_unless((int) $flat->user_id === (int) auth()->id(), 403);

        $validated = $request->validate([
            'owner_name' => ['nullable', 'string', 'max:255'],
            'owner_mobile' => ['nullable', 'string', 'max:32', 'regex:/^[\d\s+().-]*$/'],
            'owner_email' => ['nullable', 'string', 'lowercase', 'email', 'max:255'],
            'vehicles_2w' => ['nullable', 'integer', 'min:0', 'max:99'],
            'vehicles_3w' => ['nullable', 'integer', 'min:0', 'max:99'],
            'vehicles_4w' => ['nullable', 'integer', 'min:0', 'max:99'],
            'existing_documents' => ['nullable', 'array'],
            'existing_documents.*.name' => ['nullable', 'string', 'max:120'],
            'delete_documents' => ['nullable', 'array'],
            'delete_documents.*' => ['integer'],
            'new_documents' => ['nullable', 'array'],
            'new_documents.*.name' => ['nullable', 'string', 'max:120'],
            'new_documents.*.file' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'],
        ]);

        $v2 = (int) ($validated['vehicles_2w'] ?? 0);
        $v3 = (int) ($validated['vehicles_3w'] ?? 0);
        $v4 = (int) ($validated['vehicles_4w'] ?? 0);
        $userId = (int) auth()->id();

        DB::transaction(function () use ($request, $flat, $validated, $v2, $v3, $v4, $userId) {
            $flat->update([
                'owner_name' => ! empty($validated['owner_name']) ? trim($validated['owner_name']) : null,
                'owner_mobile' => ! empty($validated['owner_mobile']) ? trim($validated['owner_mobile']) : null,
                'owner_email' => ! empty($validated['owner_email']) ? trim($validated['owner_email']) : null,
                'vehicles_2w' => $v2,
                'vehicles_3w' => $v3,
                'vehicles_4w' => $v4,
                'vehicles_count' => $v2 + $v3 + $v4,
            ]);

            if (! empty($validated['existing_documents']) && is_array($validated['existing_documents'])) {
                foreach ($validated['existing_documents'] as $docId => $row) {
                    $doc = SocietyFlatDocument::query()
                        ->where('user_id', $userId)
                        ->where('society_flat_id', $flat->id)
                        ->where('id', (int) $docId)
                        ->first();
                    if ($doc) {
                        $newName = trim((string) ($row['name'] ?? ''));
                        $doc->update(['name' => $newName !== '' ? mb_substr($newName, 0, 120) : $doc->file_original_name]);
                    }
                }
            }

            if (! empty($validated['delete_documents'])) {
                $toDelete = SocietyFlatDocument::query()
                    ->where('user_id', $userId)
                    ->where('society_flat_id', $flat->id)
                    ->whereIn('id', $validated['delete_documents'])
                    ->get();
                foreach ($toDelete as $doc) {
                    if ($doc->file_path) {
                        Storage::disk('public')->delete($doc->file_path);
                    }
                    $doc->delete();
                }
            }

            $newDocsMeta = $request->input('new_documents', []);
            if (is_array($newDocsMeta)) {
                $sort = (int) SocietyFlatDocument::where('society_flat_id', $flat->id)->max('sort_order');
                foreach ($newDocsMeta as $key => $row) {
                    $file = $request->file('new_documents.'.$key.'.file');
                    if (! $file || ! $file->isValid()) {
                        continue;
                    }
                    $name = trim((string) ($row['name'] ?? ''));
                    if ($name === '') {
                        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    }
                    $dir = 'flat-documents/'.$userId.'/'.$flat->id;
                    $path = $file->store($dir, 'public');
                    SocietyFlatDocument::create([
                        'user_id' => $userId,
                        'society_flat_id' => $flat->id,
                        'name' => mb_substr($name, 0, 120),
                        'file_path' => $path,
                        'file_original_name' => $file->getClientOriginalName(),
                        'file_size' => (int) $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'sort_order' => ++$sort,
                    ]);
                }
            }
        });

        return back()->with('success', 'Flat details saved.');
    }

    /**
     * Persist wings and floor rows for the current user (replaces previous setup).
     */
    public function storeWings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'total_wings' => ['required', 'integer', 'min:1', 'max:26'],
            'floors' => ['required', 'array'],
            'floors.*' => ['required', 'integer', 'min:1', 'max:60'],
        ]);

        $total = (int) $validated['total_wings'];
        $floors = array_values($validated['floors']);

        if (count($floors) !== $total) {
            return back()
                ->withErrors(['floors' => 'Provide exactly one floor count for each wing.'])
                ->withInput();
        }

        $userId = (int) auth()->id();

        DB::transaction(function () use ($userId, $total, $floors) {
            SocietyFlat::where('user_id', $userId)->delete();
            SocietyWingFloor::where('user_id', $userId)->delete();
            SocietyWing::where('user_id', $userId)->delete();

            for ($i = 0; $i < $total; $i++) {
                $code = chr(65 + $i);
                $wing = SocietyWing::create([
                    'user_id' => $userId,
                    'code' => $code,
                    'label' => 'Wing '.$code,
                    'floors_count' => (int) $floors[$i],
                    'sort_order' => $i,
                ]);

                $floorCount = (int) $floors[$i];
                for ($f = 0; $f <= $floorCount; $f++) {
                    SocietyWingFloor::create([
                        'user_id' => $userId,
                        'society_wing_id' => $wing->id,
                        'floor_number' => $f,
                        'flats_count' => $f === 0 ? 0 : null,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.society-setup.flats')
            ->with('success', 'Building layout saved. Now create flats for each floor.');
    }

    /**
     * Step 3 — flats per floor (per wing).
     */
    public function flats(): View|RedirectResponse
    {
        $userId = auth()->id();

        $wings = SocietyWing::query()
            ->where('user_id', $userId)
            ->with(['floors' => fn ($q) => $q->orderBy('floor_number')])
            ->orderBy('sort_order')
            ->get();

        if ($wings->isEmpty()) {
            return redirect()
                ->route('admin.society-setup.wings')
                ->with('warning', 'Complete wings setup first.');
        }

        foreach ($wings as $wing) {
            if ($wing->floors->isEmpty()) {
                return redirect()
                    ->route('admin.society-setup.wings')
                    ->with('warning', 'Wing floors are missing. Please save wings again.');
            }
        }

        $wings->load([
            'floors' => fn ($q) => $q->orderBy('floor_number'),
            'flats' => fn ($q) => $q->orderBy('floor_number')->orderBy('flat_index'),
            'flats.documents',
        ]);

        $hasFlats = SocietyFlat::where('user_id', $userId)->exists();

        return view('Admin.SocietySetup.flats', compact('wings', 'hasFlats'));
    }

    /**
     * Save flat counts and generate flat unit rows for the current user.
     */
    public function generateFlats(Request $request): RedirectResponse
    {
        $userId = (int) auth()->id();

        $wings = SocietyWing::query()
            ->where('user_id', $userId)
            ->with(['floors' => fn ($q) => $q->orderBy('floor_number')])
            ->orderBy('sort_order')
            ->get();

        if ($wings->isEmpty()) {
            return redirect()
                ->route('admin.society-setup.wings')
                ->with('warning', 'Complete wings setup first.');
        }

        $rules = [];
        foreach ($wings as $wing) {
            foreach ($wing->floors as $floor) {
                $rules['flats.'.$wing->id.'.'.$floor->floor_number] = [
                    'required',
                    'integer',
                    'min:'.((int) $floor->floor_number === 0 ? '0' : '1'),
                    'max:120',
                ];
            }
        }

        $validated = $request->validate($rules);
        $flatInput = $validated['flats'];

        DB::transaction(function () use ($userId, $wings, $flatInput) {
            SocietyFlat::where('user_id', $userId)->delete();

            $sort = 0;
            foreach ($wings as $wing) {
                foreach ($wing->floors as $floor) {
                    $count = (int) ($flatInput[$wing->id][$floor->floor_number] ?? 0);

                    $floor->flats_count = $count;
                    $floor->save();

                    if ($count < 1) {
                        continue;
                    }

                    for ($n = 1; $n <= $count; $n++) {
                        $floorCode = (int) $floor->floor_number === 0 ? 'G' : 'F'.$floor->floor_number;
                        $unitCode = $wing->code.'-'.$floorCode.'-'.str_pad((string) $n, 2, '0', STR_PAD_LEFT);

                        SocietyFlat::create([
                            'user_id' => $userId,
                            'society_wing_id' => $wing->id,
                            'floor_number' => $floor->floor_number,
                            'flat_index' => $n,
                            'unit_code' => $unitCode,
                            'sort_order' => $sort++,
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('admin.society-setup.flats')
            ->with('success', 'Flats created. Scroll down to step 2 and tap each home to add owner and vehicle details.');
    }
}
