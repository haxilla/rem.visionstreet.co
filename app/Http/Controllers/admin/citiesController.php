<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Core\PostalCity;
use App\Support\AreaReview;
use App\Support\CityGuard;
use App\Support\NoStateFlyers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Admin > Areas: search, view, add, edit and delete the rows of the
 * postal_cities table (which region / sub-area / MLS each city belongs to).
 *
 * Every change is logged (who, what). All routes are admin-only.
 */
class citiesController extends Controller
{
    private const PER_PAGE = 50;

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /** The list, with search and filters. */
    public function index(Request $request)
    {
        if (!Schema::hasTable('remuserdb.postal_cities')) {
            return view('admin.cities.index', ['missing' => true]);
        }

        // "Needs review": the rows that have no region yet (see App\Support\AreaReview)
        $pendingCount = AreaReview::pendingCount();

        if ($request->query('view') === 'review') {
            $review = AreaReview::pending();

            return view('admin.cities.index', [
                'missing'      => false,
                'view'         => 'review',
                'review'       => $review,
                // what each city is probably a misspelling of (city => [city, how])
                'suggestions'  => $review->mapWithKeys(fn ($r) => [$r->id => CityGuard::suggest($r->city, $r->state)])->all(),
                'pendingCount' => $pendingCount,
                'total'        => PostalCity::count(),
            ]);
        }

        $search    = trim((string) $request->query('q', ''));
        $state     = strtoupper(trim((string) $request->query('state', '')));
        $region    = trim((string) $request->query('region', ''));
        $subregion = trim((string) $request->query('subregion', ''));
        $mls       = trim((string) $request->query('mls', ''));
        $list      = trim((string) $request->query('list', ''));

        // "local_list" is added to the table with SQL; until that has been run the page simply
        // doesn't show it.
        $hasLocal = $this->hasLocalList();

        $query = PostalCity::query();

        if ($search !== '') {
            $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search) . '%';

            $query->where(function ($q) use ($like, $hasLocal) {
                $q->where('city', 'like', $like)
                    ->orWhere('region', 'like', $like)
                    ->orWhere('subregion', 'like', $like)
                    ->orWhere('mls_system', 'like', $like);

                if ($hasLocal) {
                    $q->orWhere('local_list', 'like', $like);
                }
            });
        }

        if ($state !== '') {
            $query->where('state', $state);
        }

        // "-" means "none set" (a NULL value), which a normal filter value can't say
        $filterColumns = ['region' => $region, 'subregion' => $subregion, 'mls_system' => $mls];

        if ($hasLocal) {
            $filterColumns['local_list'] = $list;
        }

        foreach ($filterColumns as $column => $value) {
            if ($value === '-') {
                $query->whereNull($column);
            } elseif ($value !== '') {
                $query->where($column, $value);
            }
        }

        $cities = $query->orderBy('state')->orderBy('region')->orderBy('subregion')->orderBy('city')
            ->paginate(self::PER_PAGE)->withQueryString();

        return view('admin.cities.index', [
            'missing'   => false,
            'view'      => 'list',
            'pendingCount' => $pendingCount,
            'cities'    => $cities,
            'search'    => $search,
            'filters'   => ['state' => $state, 'region' => $region, 'subregion' => $subregion, 'mls' => $mls, 'list' => $list],
            'hasLocal'  => $hasLocal,
            'total'     => PostalCity::count(),
            'regions'   => PostalCity::selectRaw('region, COUNT(*) as n')->groupBy('region')->orderBy('region')->get(),
            'lists'     => $this->suggestions(),
            'states'    => config('usstates'),
        ]);
    }

    /**
     * Areas > No state: the flyers whose state is blank or "N0" (the old system's "couldn't read it"). Everything
     * needed to decide, flyer by flyer, whether to fix or delete them - address, city, the raw state text, agent,
     * whether it was ever sent, its views and a guess at the state - with a search, filters and a CSV download.
     * Read-only: nothing here changes a flyer.
     */
    public function noState(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $sent   = in_array($request->query('sent'), ['sent', 'never'], true) ? $request->query('sent') : '';
        $city   = in_array($request->query('city'), ['has', 'none'], true) ? $request->query('city') : '';

        $query = NoStateFlyers::query();

        if ($search !== '') {
            $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search) . '%';

            $query->where(function ($q) use ($like, $search) {
                $q->where('propflyers.xFullStreet', 'like', $like)
                    ->orWhere('propflyers.xCity', 'like', $like)
                    ->orWhere('propflyers.xZip', 'like', $like)
                    ->orWhere('propflyers.xState', 'like', $like)
                    ->orWhere('a.agtFullName', 'like', $like);

                if (ctype_digit($search)) {
                    $q->orWhere('propflyers.id', (int) $search)->orWhere('propflyers.propagent_id', (int) $search);
                }
            });
        }

        if ($sent === 'sent') {
            $query->whereNotNull('s.xLastDeliveryDate');
        } elseif ($sent === 'never') {
            $query->whereNull('s.xLastDeliveryDate');
        }

        if ($city === 'has') {
            $query->whereRaw("TRIM(COALESCE(propflyers.xCity, '')) <> ''");
        } elseif ($city === 'none') {
            $query->whereRaw("TRIM(COALESCE(propflyers.xCity, '')) = ''");
        }

        $cityStates = NoStateFlyers::cityStates();

        // the whole (filtered) list as a spreadsheet, for working through it outside the site
        if ($request->query('export') === 'csv') {
            return response()->streamDownload(function () use ($query, $cityStates) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['flyer_id', 'agent_id', 'agent', 'address', 'city', 'zip', 'state_column', 'xState_as_typed', 'created', 'last_sent', 'views', 'probable_state', 'probable_from']);

                foreach ($query->orderBy('propflyers.id')->cursor() as $f) {
                    $g = NoStateFlyers::guess($f, $cityStates);

                    fputcsv($out, [
                        $f->id, $f->propagent_id, $f->agent_name, $f->xFullStreet, $f->xCity, $f->xZip, $f->state, $f->xState,
                        $f->created_at ?: $f->creationDate, $f->xLastDeliveryDate, (int) $f->xWebViews, $g[0] ?? '', $g[1] ?? '',
                    ]);
                }

                fclose($out);
            }, 'flyers-with-no-state-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
        }

        $flyers = $query->orderByDesc('propflyers.id')->paginate(self::PER_PAGE)->withQueryString();

        return view('admin.cities.nostate', [
            'flyers'     => $flyers,
            'summary'    => NoStateFlyers::summary(),
            'cityStates' => $cityStates,
            'search'     => $search,
            'filters'    => ['sent' => $sent, 'city' => $city],
            'total'      => PostalCity::count(),
        ]);
    }

    /**
     * Compare every flyer's city + state with the table and add the missing ones as bare (region-less)
     * rows - so they show up under "Needs review". (New flyers do this by themselves; this catches the
     * flyers that already existed.)
     */
    public function sync()
    {
        $report = AreaReview::syncFromFlyers();

        Log::info('Admin checked flyers for new cities', [
            'admin_id' => Auth::guard('admin')->id(),
            'flyers'   => $report['flyers'],
            'pairs'    => $report['pairs'],
            'known'    => $report['known'],
            'added'    => $report['added'],
            'unusable' => count($report['unusable']),
        ]);

        // shown on the Needs review page, so a "found nothing" can be checked against what was looked at
        return redirect()->route('admin.cities', ['view' => 'review'])->with('sync_report', $report);
    }

    /**
     * The mass fix, from the Needs review list: for each ticked city, change the flyers that have it to the
     * city it is a misspelling of (worked out again here, not taken from the form), then drop the bare row.
     */
    public function fixFlyers(Request $request)
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (! $ids) {
            return redirect()->route('admin.cities', ['view' => 'review'])->withErrors(['fix' => 'Tick the cities to fix first.']);
        }

        $flyers = 0;
        $fixed  = [];
        $skipped = 0;

        foreach (PostalCity::whereIn('id', $ids)->whereNull('region')->get() as $row) {
            $suggestion = CityGuard::suggest($row->city, $row->state);

            if (! $suggestion) {
                $skipped++;
                continue;
            }

            $flyers += $this->applyFix($row, $suggestion['city']);
            $fixed[] = $row->city . ' → ' . $suggestion['city'];
        }

        Log::info('Admin mass-fixed flyer cities', ['admin_id' => Auth::guard('admin')->id(), 'flyers' => $flyers, 'fixed' => $fixed]);

        $message = number_format($flyers) . ' ' . ($flyers === 1 ? 'flyer' : 'flyers') . ' corrected (' . count($fixed) . ' ' . (count($fixed) === 1 ? 'city' : 'cities') . ').';

        if ($skipped) {
            $message .= ' ' . $skipped . ' had no match and were left alone.';
        }

        return redirect()->route('admin.cities', ['view' => 'review'])->with('status', $message);
    }

    /** Fix the flyers of one city by hand: choose which known city they should have. */
    public function fix($id)
    {
        $row = PostalCity::findOrFail($id);

        $flyerIds = CityGuard::flyerIds($row->city, $row->state);

        // every flyer that uses it (newest first), so each can be opened and corrected
        $sample = \App\Models\Core\Propflyer::withTrashed()
            ->leftJoin('remuserdb.propagents as a', 'a.id', '=', 'propflyers.propagent_id')
            ->leftJoin('propflyerstats as s', 's.propflyer_id', '=', 'propflyers.id')
            ->whereIn('propflyers.id', $flyerIds ?: [0])
            ->orderByDesc('propflyers.id')
            ->select('propflyers.id', 'propflyers.xFullStreet', 'propflyers.xCity', 'propflyers.xState', 'propflyers.state', 'propflyers.xZip',
                'propflyers.propagent_id', 'propflyers.deleted_at', 'propflyers.created_at', 'propflyers.creationDate', 's.xLastDeliveryDate', 'a.agtFullName as agent_name')
            ->paginate(self::PER_PAGE)->withQueryString();

        return view('admin.cities.fixflyers', [
            'row'        => $row,
            'flyerCount' => count($flyerIds),
            'sample'     => $sample,
            'choices'    => CityGuard::knownCities($row->state),
            'suggestion' => CityGuard::suggest($row->city, $row->state),
            'total'      => PostalCity::count(),
        ]);
    }

    /**
     * Delete every region-less entry that no flyer uses any more (what is left after flyers were corrected by
     * hand). An entry a flyer still uses is kept.
     */
    public function destroyUnused()
    {
        $removed = 0;

        foreach (AreaReview::pending()->where('flyers', 0) as $row) {
            // the list matched on exact text; check again the way the mass fix matches before deleting
            if (CityGuard::flyerIds($row->city, $row->state) === []) {
                $removed += PostalCity::where('id', $row->id)->whereNull('region')->delete();
            }
        }

        Log::info('Admin removed unused cities from the review list', ['admin_id' => Auth::guard('admin')->id(), 'removed' => $removed]);

        return redirect()->route('admin.cities', ['view' => 'review'])
            ->with('status', $removed === 0 ? 'Nothing to remove - every city on this list is still used by a flyer.' : 'Removed ' . $removed . ' ' . ($removed === 1 ? 'city' : 'cities') . ' that no flyer uses.');
    }

    /** Change the flyers of one city to the chosen known city. */
    public function applyFixOne(Request $request, $id)
    {
        $row = PostalCity::findOrFail($id);

        $choices = CityGuard::knownCities($row->state);
        $to      = $choices[mb_strtolower(trim((string) $request->input('to')))] ?? null;

        if (! $to) {
            return back()->withErrors(['to' => 'Choose the city these flyers should have.']);
        }

        $flyers = $this->applyFix($row, $to);

        Log::info('Admin fixed the city on flyers', ['admin_id' => Auth::guard('admin')->id(), 'from' => $row->city, 'to' => $to, 'state' => $row->state, 'flyers' => $flyers]);

        return redirect()->route('admin.cities', ['view' => 'review'])
            ->with('status', number_format($flyers) . ' ' . ($flyers === 1 ? 'flyer' : 'flyers') . ' changed from "' . $row->city . '" to "' . $to . '".');
    }

    /** Change the flyers to the city $to, and remove the bare row they came from. Returns how many flyers changed. */
    private function applyFix(PostalCity $row, string $to): int
    {
        $count = CityGuard::relabelFlyers($row->city, $row->state, $to);

        // only a region-less row goes: one an admin has set up stays, whatever flyers said
        if ($row->region === null && mb_strtolower(trim($row->city)) !== mb_strtolower(trim($to))) {
            $row->delete();
        }

        return $count;
    }

    /** Add a city. */
    public function store(Request $request)
    {
        $data = $this->validated($request);

        $city = PostalCity::create($data);

        Log::info('Admin added a city', ['admin_id' => Auth::guard('admin')->id(), 'city' => $data]);

        return redirect()->route('admin.cities', ['q' => $city->city])
            ->with('status', "Added {$city->city}, {$city->state}.");
    }

    public function edit(Request $request, $id)
    {
        return view('admin.cities.edit', [
            'from'   => $request->query('from') === 'review' ? 'review' : null,
            'city'   => PostalCity::findOrFail($id),
            'lists'  => $this->suggestions(),
            'states' => config('usstates'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $city   = PostalCity::findOrFail($id);
        $before = $city->only(['city', 'state', 'region', 'subregion', 'mls_system']);

        $city->update($this->validated($request, $city->id));

        Log::info('Admin edited a city', [
            'admin_id' => Auth::guard('admin')->id(),
            'id'       => $city->id,
            'before'   => $before,
            'after'    => $city->only(['city', 'state', 'region', 'subregion', 'mls_system']),
        ]);

        // saved from the "Needs review" list: go back to it, so the next one is one click away
        $to = $request->input('from') === 'review'
            ? redirect()->route('admin.cities', ['view' => 'review'])
            : redirect()->route('admin.cities', ['q' => $city->city]);

        return $to->with('status', "Saved {$city->city}, {$city->state}.");
    }

    public function destroy($id)
    {
        $city = PostalCity::findOrFail($id);

        Log::info('Admin deleted a city', [
            'admin_id' => Auth::guard('admin')->id(),
            'city'     => $city->only(['id', 'city', 'state', 'region', 'subregion', 'mls_system']),
        ]);

        $city->delete();

        // from the list: back to the same search / page; from the city's own edit page: that page is gone
        $back = str_contains(url()->previous(), '/edit')
            ? redirect()->route('admin.cities')
            : (str_contains(url()->previous(), '/fix-flyers')
                ? redirect()->route('admin.cities', ['view' => 'review'])
                : redirect()->back());

        return $back->with('status', "Deleted {$city->city}, {$city->state}.");
    }

    /**
     * The validated, tidied fields. City + state must be unique together (case-insensitive, checked
     * here through the model - the same connection every other form saves with).
     *
     * Region, sub-area and MLS are picked from the values already in the table. "Add a new..." posts
     * the value "__new__" plus what was typed in <field>_new: that is tidied and used. A NEW region or
     * sub-area is turned into a lower-case key ("West Valley" -> west_valley) so the same territory
     * can't end up spelled three ways. Blank optional fields are stored as NULL.
     */
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        // "Add a new ..." picked: the typed value takes the place of the "__new__" marker
        $typed = [];

        $hasLocal = $this->hasLocalList();

        foreach (['region', 'subregion', 'mls_system', 'local_list'] as $field) {
            if ($request->input($field) === '__new__') {
                $new = trim(preg_replace('/\s+/', ' ', (string) $request->input($field . '_new')));

                // region / sub-area / list name are keys: lower case, words joined with underscores
                if ($field !== 'mls_system') {
                    $new = trim(preg_replace('/[\s\-]+/', '_', strtolower($new)), '_');
                }

                $typed[$field] = $new;
            }
        }

        $request->merge($typed);

        $state = strtoupper(trim((string) $request->input('state')));

        $rules = [
            'city'       => ['required', 'string', 'max:100'],
            'state'      => ['required', 'string', Rule::in(array_keys(config('usstates')))],
            'region'     => ['required', 'string', 'max:50', 'regex:/^[a-z0-9_]+$/'],
            'subregion'  => ['nullable', 'string', 'max:50', 'regex:/^[a-z0-9_]+$/'],
            'mls_system' => ['nullable', 'string', 'max:100'],
        ];

        if ($hasLocal) {
            // a mailing list's name, as the mailer knows it (azphxwv, aznaz ...)
            $rules['local_list'] = ['nullable', 'string', 'max:50', 'regex:/^[a-z0-9_]+$/'];
        }

        $validator = Validator::make($request->all(), $rules, [
            'local_list.regex' => 'A list name can use lower-case letters, numbers and underscores only (for example: azphxwv).',
            'region.required' => 'Choose a region (or add a new one).',
            'region.regex'    => 'A new region can use letters, numbers and underscores only (for example: central).',
            'subregion.regex' => 'A new sub-area can use letters, numbers and underscores only (for example: east_valley).',
        ]);

        $validator->after(function ($v) use ($request, $state, $ignoreId) {
            if ($v->errors()->has('city') || $v->errors()->has('state')) {
                return;
            }

            $city = trim(preg_replace('/\s+/', ' ', (string) $request->input('city')));

            $exists = PostalCity::where('state', $state)
                ->where('city', $city)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists();

            if (\App\Support\PostalCityRegistrar::isCountryName($city, $state)) {
                $v->errors()->add('city', 'Mexico is a country, not a city. Use its real city name with the state MX.');
            }

            if ($exists) {
                $v->errors()->add('city', 'That city is already in the list for this state.');
            }
        });

        $data = $validator->validate();

        $data['city']  = trim(preg_replace('/\s+/', ' ', $data['city']));
        $data['state'] = $state;

        foreach (['subregion', 'mls_system', 'local_list'] as $optional) {
            if (array_key_exists($optional, $data)) {
                $data[$optional] = ($data[$optional] ?? '') === '' ? null : trim($data[$optional]);
            }
        }

        return $data;
    }

    /** Has the local_list column been added to the table yet (it is added with SQL)? */
    private function hasLocalList(): bool
    {
        return Schema::hasColumn('remuserdb.postal_cities', 'local_list');
    }

    /** Values already in use, offered as suggestions in the add / edit boxes so spellings stay consistent. */
    private function suggestions(): array
    {
        $distinct = fn (string $column) => PostalCity::whereNotNull($column)->where($column, '<>', '')
            ->distinct()->orderBy($column)->pluck($column)->all();

        // The mailing lists the site can send to (value = the list's name as the mailer knows it, shown with
        // its plain name), plus any list name already used in the table that isn't one of those.
        $lists = [];

        foreach (include app_path('flyers/campaignAreas.php') as $area) {
            $lists[$area['db']] = $area['db'] . ' - ' . $area['label'];
        }

        if ($this->hasLocalList()) {
            foreach ($distinct('local_list') as $used) {
                $lists[$used] = $lists[$used] ?? $used;
            }
        }

        ksort($lists);

        return [
            'regions'    => $distinct('region'),
            'subregions' => $distinct('subregion'),
            'mls'        => $distinct('mls_system'),
            'lists'      => $lists,
            'hasLocal'   => $this->hasLocalList(),
        ];
    }
}
