<?php

namespace App\Http\Controllers\member;

use App\Http\Controllers\Controller;
use App\Models\Core\Propagent;
use App\Models\Core\Propdeliv;
use App\Models\Core\Propdelivnow;
use App\Models\Core\Propflyer;
use App\Models\Core\Propmapping;
use App\Models\Core\Propremark;
use App\Support\AgentCampaigns;
use App\Support\AgentImages;
use App\Support\AgentImageStore;
use App\Support\AgentPasswords;
use App\Support\AgentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class memberController extends Controller
{
    /** Max depth for /member/{segments?} */
    private const MAX_SEGMENTS = 5;

    public function __construct()
    {
        $this->middleware('auth:member');

        // Runs after auth: signs out a blocked agent on their next request.
        $this->middleware(\App\Http\Middleware\EnsureAgentNotBlocked::class);

        // Everything recorded in the member area uses the agent's own timezone.
        $this->middleware(\App\Http\Middleware\SetAgentTimezone::class);
    }

    public function segments(Request $request)
    {
        $segmentsPath = trim((string) $request->route('segments', ''), '/');    
        $parts        = ($segmentsPath === '') ? [] : explode('/', $segmentsPath);

        // Prepend 'admin' so dynamic_index resolves to admin.* views
        array_unshift($parts, 'member');

        require_once __DIR__ . '/../parts/dynamic_index.php';

        if($redirect) {
            return redirect($redirect);
        }

        // ---- partial vs full ----
        $isPartial = $request->header('X-Pageswap') === '1';
        if ($isPartial) {
            // return just the fragment for pageswap
            return response()
                ->view($viewName, compact('data'))
                ->header('Vary', 'X-Pageswap');}
                // cache safety

        // full chrome + the same fragment on refresh/direct visit
        return response()
            ->view($viewName, [         
                'data'        => $data,
                'contentView' => $viewName,
            ])->header('Vary', 'X-Pageswap');
        
    }
    
    /**
     * "Agent Info" (/member/agent-info): everything an agent may edit about themselves -
     * name, designations, contact details, brokerage / address, licence details - plus
     * adding, changing and removing their photo and logo. What can't be edited here: the
     * sign-in email (only support can change who can sign in), credits, start date.
     */
    public function agentInfo()
    {
        $agent = Propagent::with(['theAgtOffice', 'theAgentCleanup'])->findOrFail(auth('member')->id());

        return view('member.agent-info', [
            'agent'  => $agent,
            'office' => $agent->theAgtOffice,
            'photo'  => AgentImages::photo($agent),
            'logo'   => AgentImages::logo($agent),
            'states' => config('usstates'),
        ]);
    }

    public function agentInfoSave(Request $request)
    {
        $agent = Propagent::with('theAgtOffice')->findOrFail(auth('member')->id());

        $data = $request->validate(
            AgentProfile::contactRules() + AgentProfile::officeRules($agent->theAgtOffice) + AgentProfile::licenseRules($agent),
            [
                'agtEmail.email'   => 'Please enter a valid contact email address.',
                'officeState.max'  => 'Choose a state from the list.',
            ]
        );

        try {
            AgentProfile::saveContact($agent, $data);
            AgentProfile::saveOffice($agent, $data);
        } catch (\Throwable $e) {
            Log::error('Agent info save failed for agent ' . $agent->id . ': ' . $e->getMessage());

            return redirect('/member/agent-info')->withInput()
                ->withErrors(['profile' => 'Sorry, your changes could not be saved. Please check the fields and try again.']);
        }

        return redirect('/member/agent-info')->with('status', 'Your agent info was saved.');
    }

    /**
     * "Account Info" (/member/account): the agent's plan (type, start date, expiry, credits),
     * a little activity summary, the sign-in username with a way to email themselves a
     * password reset link, and their order history.
     */
    public function accountInfo()
    {
        $agent = Propagent::findOrFail(auth('member')->id());

        $campaigns = AgentCampaigns::forAgent($agent->id)->where('status', 'completed');

        return view('member.account', [
            'agent'    => $agent,
            'orders'   => DB::table('allorders')->where('propagent_id', $agent->id)->orderByDesc('payment_date')->get(),
            'flyers'   => Propflyer::where('propagent_id', $agent->id)->count(),
            'sent'     => $campaigns->count(),
            'emails'   => (int) $campaigns->sum(fn ($row) => (int) $row['emails']),
            'maskedEmail' => AgentPasswords::maskEmail((string) $agent->xxAgtUname),
        ]);
    }

    /** Email the agent a password reset link at their sign-in email (same link as "Forgot password"). */
    public function accountPasswordLink(Request $request)
    {
        $agent  = Propagent::findOrFail(auth('member')->id());
        $result = AgentPasswords::sendLink($agent, 'forgot', $request->ip());

        if ($result === 'sent') {
            return redirect('/member/account#signin')->with('status',
                'We emailed a password reset link to ' . AgentPasswords::maskEmail((string) $agent->xxAgtUname)
                . '. It works once and expires in ' . AgentPasswords::LINK_MINUTES . ' minutes.');
        }

        if ($result === 'limited') {
            return redirect('/member/account#signin')->with('status',
                'A link was just sent - please check your email. You can ask for another in a minute.');
        }

        return redirect('/member/account#signin')
            ->withErrors(['passwordLink' => 'We could not send the link. Please contact support.']);
    }

    /** Add or change the agent's own photo or logo ($kind is "photo" or "logo"). */
    public function agentImageUpload(Request $request, $kind)
    {
        $agent = Propagent::with('theAgtOffice')->findOrFail(auth('member')->id());
        $label = $kind === 'photo' ? 'photo' : 'logo';

        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ], [
            'image.required' => "Choose a {$label} to upload.",
            'image.image'    => "Your {$label} must be an image (JPG, PNG, GIF or WebP).",
            'image.mimes'    => "Your {$label} must be a JPG, PNG, GIF or WebP image.",
            'image.max'      => "Your {$label} can't be larger than 5 MB.",
            'image.uploaded' => "Your {$label} couldn't be uploaded - it may be larger than the server allows.",
        ]);

        $result = AgentImageStore::store($agent, $kind, $request->file('image'));

        return $result['ok']
            ? redirect('/member/agent-info#images')->with('status', $result['message'])
            : redirect('/member/agent-info#images')->withErrors(['image' => $result['message']]);
    }

    public function agentImageClear($kind)
    {
        $agent = Propagent::with('theAgtOffice')->findOrFail(auth('member')->id());

        return redirect('/member/agent-info#images')->with('status', AgentImageStore::clear($agent, $kind));
    }

    /**
     * Full campaign history for one of the agent's own flyers
     * (/member/campaigns/{flyerId}, linked from the dashboard's flyer cards).
     *
     * History lives in two tables: propdelivnow (requested / in progress, and
     * any completed rows still there) and propdelivs (the archive of finished
     * campaigns). Both are read and merged; a campaign present in both is
     * listed once (same area + same request time + same start time).
     */
    public function flyerCampaigns($flyerId)
    {
        // Only the flyer's own agent; a soft-deleted flyer is a 404 too.
        $flyer = Propflyer::with([
                'thePhotos' => fn ($query) => $query->where('def', 1),
                'theMeta',
                'theStats',
            ])
            ->where('id', $flyerId)
            ->where('propagent_id', auth('member')->id())
            ->firstOrFail();

        $areaLabels = [];
        foreach (include app_path('flyers/campaignAreas.php') as $area) {
            $areaLabels[$area['db']] = $area['label'];
        }

        // Real dates only: legacy rows can hold NULL or a zero date.
        $when = function ($value) {
            if (!$value) {
                return null;
            }

            try {
                $date = \Illuminate\Support\Carbon::parse($value);
            } catch (\Throwable $e) {
                return null;
            }

            return $date->year > 1970 ? $date : null;
        };

        $rows = Propdelivnow::where('propflyer_id', $flyer->id)->get()
            ->concat(Propdeliv::where('propflyer_id', $flyer->id)->get())
            ->map(function ($c) use ($when, $areaLabels) {
                $requested = $when($c->emRequest);
                $started   = $when($c->emStart);
                $completed = $when($c->emComplete);

                if ($completed) {
                    $status = 'completed';
                } elseif ($started) {
                    $status = 'delivering';
                } elseif ((int) ($c->authorized ?? 0) === 1) {
                    $status = 'approved';
                } else {
                    $status = 'pending';
                }

                // Which of the agent's two picks this was: campLabel 'area1' /
                // 'area2' (how the legacy data and the send form record them).
                // Anything else - e.g. an area an admin added - has no number,
                // and the agent-facing page says nothing about who added it.
                $slot = preg_match('/^area([12])$/i', (string) ($c->campLabel ?? ''), $m) ? (int) $m[1] : null;

                return [
                    'requested' => $requested,
                    'started'   => $started,
                    'completed' => $completed,
                    // legacy rows store the area code in mixed case (AzPhxSE)
                    'area'      => ($c->emArea_display ?? null) ?: ($areaLabels[strtolower($c->emArea ?? '')] ?? ($c->emArea ?? 'Unknown area')),
                    'subject'   => $c->emSubject ?? null,
                    'emails'    => $c->totalEmails ?? null,
                    'status'    => $status,
                    'slot'      => $slot,
                ];
            })
            // the same campaign held in both tables is one campaign
            ->unique(fn ($r) => $r['area'] . '|' . $r['requested']?->timestamp . '|' . $r['started']?->timestamp)
            ->sortByDesc(fn ($r) => ($r['requested'] ?? $r['started'] ?? $r['completed'])?->timestamp ?? 0)
            ->values();

        $completedRows = $rows->where('status', 'completed');

        $summary = [
            'sent'      => $completedRows->count(),
            'emails'    => (int) $completedRows->sum(fn ($r) => (int) $r['emails']),
            'lastSent'  => $completedRows->map(fn ($r) => $r['completed'])->filter()->sortDesc()->first(),
            // requested but not started yet
            'inQueue'    => $rows->whereIn('status', ['pending', 'approved'])->count(),
            // started but not finished
            'inProgress' => $rows->where('status', 'delivering')->count(),
        ];

        return view('member.flyer.campaignHistory', [
            'flyer'     => $flyer,
            'campaigns' => $rows,
            'summary'   => $summary,
        ]);
    }

    /**
     * Live preview for the Details step: renders the flyer from the
     * form's CURRENT, UNSAVED values so the agent sees changes as they
     * type. Nothing here is ever saved - values are laid over in-memory
     * models only. Mirrors the field mapping in
     * app/member/flyer/save_details.php (x* and xx* twins), so keep the
     * two in step when Details fields change.
     */
    public function flyerPreview(Request $request)
    {
        $flyerId = (int) $request->input('flyerId');

        // Accepts unsaved input, so only the flyer's own agent may use it.
        abort_unless(
            Propflyer::where('id', $flyerId)
                ->where('propagent_id', auth('member')->id())
                ->exists(),
            404
        );

        include app_path('queries/flyerdetails.php'); // builds $propInfo

        $text = function (string $key, int $max = 255) use ($request) {
            $value = trim((string) $request->input($key, ''));

            return $value === '' ? null : mb_substr($value, 0, $max);
        };

        $number = function (string $key) use ($request) {
            $value = trim((string) $request->input($key, ''));

            return is_numeric($value) ? $value : null;
        };

        $headline = $text('xHeadline');
        $propInfo->xHeadline  = $headline;
        $propInfo->xxHeadline = $headline;

        // "$450,000" and "450,000" are fine to type while previewing;
        // only digits count.
        $price = preg_replace('/\D/', '', (string) $request->input('xListPrice', ''));
        $propInfo->xListPrice = $price === '' ? null : (int) $price;

        $year = trim((string) $request->input('xYrBuilt', ''));
        $year = preg_match('/^\d{4}$/', $year) ? $year : null;
        $propInfo->xYrBuilt  = $year;
        $propInfo->xxYrBuilt = $year;

        foreach (['Beds', 'Baths', 'Sqft'] as $field) {
            $value = $number('x' . $field);
            $propInfo->{'x' . $field}  = $value;
            $propInfo->{'xx' . $field} = $value;
        }

        $propInfo->xVirtualTour = $text('xVirtualTour');
        $propInfo->xMlsLink     = $text('xMlsLink');

        // theMap / theRemarks may not exist yet for a flyer that has
        // never saved Details - use an unsaved stand-in.
        $map = $propInfo->theMap ?? new Propmapping();
        $map->xIntersection = $text('xIntersection');
        $propInfo->setRelation('theMap', $map);

        $remarks = $propInfo->theRemarks ?? new Propremark();
        for ($i = 1; $i <= 8; $i++) {
            $remarks->{'xb' . $i} = $text('xb' . $i, 42);
        }
        $remarks->xPubRemarks = $text('xPubRemarks', 65535);
        $propInfo->setRelation('theRemarks', $remarks);

        return response()
            ->view('member.flyer.previewPane', compact('propInfo'))
            ->header('Cache-Control', 'no-store');
    }

    public function flyerEdit($flyerId)
    {
        include app_path('queries/flyerdetails.php');
        return view('flyers.index',compact('propInfo'));
    }

    public function flyerText($flyerId)
    {
        include app_path('queries/flyerdetails.php');
        return view('flyers.textedit',compact('propInfo'));
    }

    public function flyerPhotos($flyerId)
    {
        include app_path('queries/flyerphotos.php');
        return view('flyers.photoedit',compact('propInfo'));
    }

}