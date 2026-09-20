@include('member.layout.head', ['pageTitle' => 'Agent Info | Realty Emails'])

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

{{--
    The agent's own profile: everything they may edit (name, designations, contact details,
    brokerage and address, licence details) and their photo and logo. Saved by
    memberController::agentInfoSave / agentImageUpload / agentImageClear.
    Not on this page: the sign-in email and password (Account Info), credits and start date.

    Layout is plain CSS (.ai-*) so it shows correctly whether or not the site stylesheet
    has been rebuilt since the last deploy.
--}}
<style>
    .ai-wrap    { max-width: 960px; margin: 0 auto; padding: 8px 16px 64px; }
    .ai-head h1 { margin: 0; font-size: 26px; line-height: 1.2; font-weight: 800; color: #0f172a; letter-spacing: -.01em; }
    .ai-head p  { margin: 6px 0 0; font-size: 14px; color: #64748b; }

    .ai-card    { margin-top: 18px; background: #fff; border-radius: 20px; border: 1px solid rgba(15,23,42,.06);
                  box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 6px 20px rgba(15,23,42,.04); overflow: hidden; }
    .ai-card-h  { padding: 16px 22px 12px; border-bottom: 1px solid #eef1f6; }
    .ai-card-h h2 { margin: 0; font-size: 16px; font-weight: 800; color: #123f91; }
    .ai-card-h p  { margin: 3px 0 0; font-size: 13px; color: #64748b; }
    .ai-card-b  { padding: 20px 22px 22px; }

    .ai-alert   { margin-top: 16px; padding: 11px 16px; border-radius: 14px; font-size: 14px; font-weight: 600; border: 1px solid; }
    .ai-alert.ok  { background: #ecfdf5; border-color: #a7f3d0; color: #047857; }
    .ai-alert.bad { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }

    /* photo + logo */
    .ai-images  { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
    .ai-tile    { border: 1px solid #e6eaf2; border-radius: 16px; padding: 16px; display: flex; gap: 16px; align-items: center; }
    .ai-thumb   { flex: 0 0 auto; display: flex; align-items: center; justify-content: center; background: #f3f5fa;
                  color: #94a3b8; font-size: 12px; font-weight: 700; text-align: center; overflow: hidden; }
    .ai-thumb.photo { width: 96px; height: 96px; border-radius: 999px; }
    .ai-thumb.logo  { width: 150px; height: 84px; border-radius: 12px; }
    .ai-thumb img   { width: 100%; height: 100%; }
    .ai-thumb.photo img { object-fit: cover; object-position: top; }
    .ai-thumb.logo img  { object-fit: contain; }
    .ai-tile h3 { margin: 0; font-size: 14px; font-weight: 800; color: #0f172a; }
    .ai-tile small { display: block; margin-top: 2px; font-size: 12px; color: #64748b; line-height: 1.4; }
    .ai-tile-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
    .ai-btn     { display: inline-block; border-radius: 10px; padding: 8px 14px; font-size: 13px; font-weight: 700; cursor: pointer;
                  border: 1px solid #d5dbe6; background: #fff; color: #334155; }
    .ai-btn:hover { background: #f3f5fa; }
    .ai-btn.primary { background: #123f91; border-color: #123f91; color: #fff; }
    .ai-btn.primary:hover { background: #0f3274; }
    .ai-btn.danger  { color: #b91c1c; border-color: #fecaca; }
    .ai-btn.danger:hover { background: #fef2f2; }
    .ai-btn[disabled] { opacity: .45; cursor: not-allowed; }
    .ai-file    { position: absolute; width: 1px; height: 1px; opacity: 0; overflow: hidden; }

    /* form */
    .ai-section { margin: 0 0 22px; }
    .ai-section:last-of-type { margin-bottom: 4px; }
    .ai-section h3 { margin: 0 0 10px; font-size: 13px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: #64748b; }
    .ai-grid    { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px 18px; }
    .ai-field   { min-width: 0; }
    .ai-field.full { grid-column: 1 / -1; }
    .ai-field label { display: flex; align-items: center; gap: 8px; margin-bottom: 5px; font-size: 13px; font-weight: 700; color: #334155; }
    .ai-field input, .ai-field select { width: 100%; height: 42px; border: 1px solid #d5dbe6; border-radius: 12px; background: #fff;
                       padding: 0 12px; font-size: 15px; color: #0f172a; outline: none; }
    .ai-field input:focus, .ai-field select:focus { border-color: #123f91; box-shadow: 0 0 0 3px rgba(18,63,145,.12); }
    /* a headline band for each topic */
    .ai-sec-h   { display: flex; align-items: center; gap: 14px; padding: 16px 22px; background: #f4f7fd; border-bottom: 1px solid #e3e9f5; }
    .ai-sec-h h2 { margin: 0; font-size: 18px; line-height: 1.2; font-weight: 800; color: #0f172a; }
    .ai-sec-h p  { margin: 3px 0 0; font-size: 13px; color: #64748b; }
    .ai-icon    { flex: 0 0 auto; width: 40px; height: 40px; border-radius: 12px; background: #123f91; color: #fff;
                  display: flex; align-items: center; justify-content: center; }
    .ai-icon svg { width: 20px; height: 20px; }
    .ai-savebar { position: sticky; bottom: 12px; z-index: 5; margin-top: 18px; display: flex; align-items: center; justify-content: space-between;
                  gap: 14px; padding: 12px 16px 12px 20px; border-radius: 16px; background: #fff; border: 1px solid rgba(15,23,42,.08);
                  box-shadow: 0 8px 28px rgba(15,23,42,.12); font-size: 13.5px; color: #475569; }

    @media (max-width: 720px) {
        .ai-images { grid-template-columns: 1fr; }
        .ai-grid   { grid-template-columns: 1fr; }
        .ai-tile   { flex-direction: column; align-items: flex-start; }
    }
</style>

@php
    [$firstName, $lastName] = \App\Support\AgentNames::forForm($agent);

    $currentState = old('officeState', $office->officeState ?? '');

    $officeInput = fn (string $column) => old($column, $office->{$column} ?? '');
@endphp

<main class="min-h-screen bg-[#f0f2f7] pt-24">
<div class="ai-wrap">

    <div class="ai-head">
        <h1>Agent Info</h1>
        <p>This is the information that appears on your flyers. Your changes show on your flyers right away.</p>
    </div>

    @if(session('status'))
        <div class="ai-alert ok">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="ai-alert bad">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- ========== PHOTO + LOGO ========== --}}
    <section id="images" class="ai-card">
        <header class="ai-sec-h">
            <span class="ai-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m21 16-5-5-8 8"/></svg>
            </span>
            <div>
                <h2>Photo &amp; logo</h2>
                <p>JPG, PNG, GIF or WebP, up to 5 MB. We resize them for email automatically.</p>
            </div>
        </header>

        <div class="ai-card-b">
            <div class="ai-images">

                @foreach([
                    'photo' => ['title' => 'Your photo', 'data' => $photo, 'can' => true],
                    'logo'  => ['title' => 'Office logo', 'data' => $logo, 'can' => (bool) $office],
                ] as $kind => $spec)
                    @php $img = $spec['data']; @endphp

                    <div class="ai-tile">
                        <div class="ai-thumb {{ $kind }}">
                            @if($img['url'])
                                <img src="{{ $img['url'] }}" alt="{{ $spec['title'] }}">
                            @elseif($img['file'])
                                File missing
                            @else
                                No {{ $kind }}
                            @endif
                        </div>

                        <div style="min-width:0">
                            <h3>{{ $spec['title'] }}</h3>

                            @if(!$spec['can'])
                                <small>Add your brokerage or office address below and save it first &mdash; then you can add a logo.</small>
                            @endif

                            <div class="ai-tile-actions">
                                <form method="POST" action="{{ route('member.agentInfo.image', $kind) }}" enctype="multipart/form-data">
                                    @csrf
                                    <label class="ai-btn primary" @if(!$spec['can']) style="opacity:.45;cursor:not-allowed" @endif>
                                        {{ $img['file'] ? 'Change ' . $kind : 'Add ' . $kind }}
                                        <input type="file" name="image" class="ai-file"
                                               accept="image/jpeg,image/png,image/gif,image/webp"
                                               @if(!$spec['can']) disabled @endif
                                               onchange="if (this.files.length) this.form.submit()">
                                    </label>
                                </form>

                                @if($img['file'])
                                    <form method="POST" action="{{ route('member.agentInfo.imageClear', $kind) }}"
                                          onsubmit="return confirm('Remove your {{ $kind }}?')">
                                        @csrf
                                        <button type="submit" class="ai-btn danger">Remove</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    {{-- ========== DETAILS: one form, one card per topic, one Save ========== --}}
    <form method="POST" action="{{ route('member.agentInfo.save') }}" novalidate>
        @csrf

        {{-- ---- 1. ABOUT YOU ---- --}}
        <section class="ai-card">
            <header class="ai-sec-h">
                <span class="ai-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                </span>
                <div>
                    <h2>About you</h2>
                    <p>Your name and the titles shown after it.</p>
                </div>
            </header>

            <div class="ai-card-b">
                <div class="ai-grid">
                    <div class="ai-field">
                        <label for="agtFirst">First name</label>
                        <input type="text" id="agtFirst" name="agtFirst" maxlength="48" autocomplete="given-name"
                               value="{{ old('agtFirst', $firstName) }}">
                    </div>
                    <div class="ai-field">
                        <label for="agtLast">Last name</label>
                        <input type="text" id="agtLast" name="agtLast" maxlength="48" autocomplete="family-name"
                               value="{{ old('agtLast', $lastName) }}">
                    </div>
                    <div class="ai-field full">
                        <label for="agtDesigs">Designations</label>
                        <input type="text" id="agtDesigs" name="agtDesigs" maxlength="100"
                               placeholder="e.g. REALTOR&reg;, ABR, CRS"
                               value="{{ old('agtDesigs', $agent->agtDesigs) }}">
                    </div>
                </div>
            </div>
        </section>

        {{-- ---- 2. CONTACT ---- --}}
        <section class="ai-card">
            <header class="ai-sec-h">
                <span class="ai-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/></svg>
                </span>
                <div>
                    <h2>Contact</h2>
                    <p>How buyers and other agents reach you.</p>
                </div>
            </header>

            <div class="ai-card-b">
                <div class="ai-grid">
                    <div class="ai-field">
                        <label for="agtMainPhone">Phone</label>
                        <input type="tel" id="agtMainPhone" name="agtMainPhone" maxlength="30" autocomplete="tel"
                               value="{{ old('agtMainPhone', $agent->agtMainPhone) }}">
                    </div>
                    <div class="ai-field">
                        <label for="agtEmail">Contact email</label>
                        <input type="email" id="agtEmail" name="agtEmail" maxlength="100" autocomplete="email"
                               value="{{ old('agtEmail', $agent->agtEmail) }}">
                    </div>
                </div>
            </div>
        </section>

        {{-- ---- 3. BROKERAGE & ADDRESS (county belongs here) ---- --}}
        <section class="ai-card">
            <header class="ai-sec-h">
                <span class="ai-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9v.01M9 12v.01M9 15v.01M9 18v.01"/></svg>
                </span>
                <div>
                    <h2>Brokerage &amp; address</h2>
                    <p>Your office, as it appears on your flyers.</p>
                </div>
            </header>

            <div class="ai-card-b">
                <div class="ai-grid">
                    <div class="ai-field full">
                        <label for="officeName">Brokerage</label>
                        <input type="text" id="officeName" name="officeName" maxlength="150" autocomplete="organization"
                               value="{{ $officeInput('officeName') }}">
                    </div>
                    <div class="ai-field full">
                        <label for="officeAddress1">Street address</label>
                        <input type="text" id="officeAddress1" name="officeAddress1" maxlength="150" autocomplete="address-line1"
                               value="{{ $officeInput('officeAddress1') }}">
                    </div>
                    <div class="ai-field">
                        <label for="officeCity">City</label>
                        <input type="text" id="officeCity" name="officeCity" maxlength="100" autocomplete="address-level2"
                               value="{{ $officeInput('officeCity') }}">
                    </div>
                    <div class="ai-field">
                        <label for="officeState">State</label>
                        <select id="officeState" name="officeState" autocomplete="address-level1">
                            <option value="">&mdash;</option>
                            @foreach($states as $abbr => $stateName)
                                <option value="{{ $abbr }}" @selected($currentState === $abbr)>{{ $stateName }}</option>
                            @endforeach
                            {{-- a value saved by the old system that isn't a state code stays selectable, so it isn't lost --}}
                            @if($currentState !== '' && !array_key_exists($currentState, $states))
                                <option value="{{ $currentState }}" selected>{{ $currentState }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="ai-field">
                        <label for="officeZip">ZIP</label>
                        <input type="text" id="officeZip" name="officeZip" maxlength="10" autocomplete="postal-code"
                               value="{{ $officeInput('officeZip') }}">
                    </div>
                    <div class="ai-field">
                        <label for="agtCounty">County</label>
                        <input type="text" id="agtCounty" name="agtCounty" maxlength="100"
                               value="{{ old('agtCounty', $agent->agtCounty) }}">
                    </div>
                </div>
            </div>
        </section>

        {{-- ---- 4. LICENSE ---- --}}
        <section class="ai-card">
            <header class="ai-sec-h">
                <span class="ai-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M6 16c.6-1.4 1.7-2 3-2s2.4.6 3 2"/><path d="M15 10h3M15 13h3"/></svg>
                </span>
                <div>
                    <h2>License</h2>
                    <p>Your MLS details.</p>
                </div>
            </header>

            <div class="ai-card-b">
                <div class="ai-grid">
                    <div class="ai-field">
                        <label for="agtMlsID">MLS ID</label>
                        <input type="text" id="agtMlsID" name="agtMlsID" maxlength="100"
                               value="{{ old('agtMlsID', $agent->agtMlsID) }}">
                    </div>
                    <div class="ai-field">
                        <label for="agtBoard">MLS</label>
                        {{-- a choice from a short list (AgentProfile::MLS_OPTIONS), not free text --}}
                        @php $mlsNow = \App\Support\AgentProfile::canonicalMls(old('agtBoard', $agent->agtBoard)); @endphp
                        <select id="agtBoard" name="agtBoard">
                            <option value="">Select your MLS</option>
                            @foreach(\App\Support\AgentProfile::mlsChoices() as $mlsValue => $mlsText)
                                <option value="{{ $mlsValue }}" @selected($mlsNow === $mlsValue)>{{ $mlsText }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </section>

        {{-- Save stays in view while you scroll the sections --}}
        <div class="ai-savebar">
            <span>Changes show on your flyers right away.</span>
            <button type="submit" class="ai-btn primary" style="padding:10px 24px;font-size:14px">Save changes</button>
        </div>
    </form>

</div>
</main>

@include('public.layout.footer')

</body>
</html>
