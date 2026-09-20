{{--
    The flyer itself, drawn from $propInfo with the SAME templates the rest of
    the app uses (flyers.s1pc ... flyers.s5pt), so the preview can never drift
    from the real flyer. Used both for the Details step's first paint and for
    the live refresh endpoint (memberController::flyerPreview), which passes a
    $propInfo carrying the agent's UNSAVED form values.
--}}
@php
    // A flyer that has never had Details saved has NO remarks / map record yet (they
    // are created on the first save), and the flyer templates read them unconditionally
    // (countBullets.php: "array offset on null"). Use empty, unsaved stand-ins so a brand
    // new flyer draws as a flyer with no highlights yet. Nothing here is ever saved.
    if (!$propInfo->theRemarks) {
        $propInfo->setRelation('theRemarks', new \App\Models\Core\Propremark());
    }

    if (!$propInfo->theMap) {
        $propInfo->setRelation('theMap', new \App\Models\Core\Propmapping());
    }

    include(app_path() . '/flyers/variables.php');

    $previewTemplateView = 'flyers.s' . strtolower($propInfo->theStyle?->template ?: '1pc');
@endphp

@if(View::exists($previewTemplateView))
    @include($previewTemplateView)
@endif
