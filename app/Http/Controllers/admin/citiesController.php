<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Core\PostalCity;
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

    /** Add a city. */
    public function store(Request $request)
    {
        $data = $this->validated($request);

        $city = PostalCity::create($data);

        Log::info('Admin added a city', ['admin_id' => Auth::guard('admin')->id(), 'city' => $data]);

        return redirect()->route('admin.cities', ['q' => $city->city])
            ->with('status', "Added {$city->city}, {$city->state}.");
    }

    public function edit($id)
    {
        return view('admin.cities.edit', [
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

        return redirect()->route('admin.cities', ['q' => $city->city])
            ->with('status', "Saved {$city->city}, {$city->state}.");
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
            : redirect()->back();

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
