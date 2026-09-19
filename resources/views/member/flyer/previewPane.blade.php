{{--
    The flyer itself, drawn from $propInfo with the SAME templates the rest of
    the app uses (flyers.s1pc ... flyers.s5pt), so the preview can never drift
    from the real flyer. Used both for the Details step's first paint and for
    the live refresh endpoint (memberController::flyerPreview), which passes a
    $propInfo carrying the agent's UNSAVED form values.
--}}
@php
    include(app_path() . '/flyers/variables.php');

    $previewTemplateView = 'flyers.s' . strtolower($propInfo->theStyle?->template ?: '1pc');
@endphp

@if(View::exists($previewTemplateView))
    @include($previewTemplateView)
@endif
