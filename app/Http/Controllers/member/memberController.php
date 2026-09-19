<?php

namespace App\Http\Controllers\member;

use App\Http\Controllers\Controller;
use App\Models\Core\Propflyer;
use App\Models\Core\Propmapping;
use App\Models\Core\Propremark;
use Illuminate\Http\Request;

class memberController extends Controller
{
    /** Max depth for /member/{segments?} */
    private const MAX_SEGMENTS = 5;

    public function __construct()
    {
        $this->middleware('auth:member');
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