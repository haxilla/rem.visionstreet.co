@php
    $email = $data['email'] ?? '';
    $foundByDb = $data['foundByDb'] ?? [];
    $hasProblem = $data['hasProblem'] ?? true;
    $azMatch = $data['azMatch'] ?? null;
    $arizonaMatch = $data['arizonaMatch'] ?? null;
    $row = $data['row'] ?? null;
@endphp

<div class="max-w-5xl mx-auto p-6">

    <h2 class="text-2xl font-bold mb-4">Email Lookup Result</h2>

    <p class="mb-4">
        <strong>Searched Email:</strong> {{ $email }}
    </p>

    @if($hasProblem)

        <div class="p-4 mb-6 bg-yellow-100 border border-yellow-400 rounded">
            <strong>Needs Review:</strong>
            Each remote database should contain exactly one matching record.
        </div>

        @foreach($foundByDb as $dbLabel => $matches)
            <div class="mb-5 p-4 border rounded bg-white">
                <h3 class="font-bold text-lg mb-3">
                    {{ $dbLabel }} — {{ count($matches) }} match(es)
                </h3>

                @if(count($matches) === 0)
                    <div class="p-3 bg-red-100 border border-red-400 rounded">
                        No matching record found.
                    </div>
                @endif

                @foreach($matches as $match)
                    @php $r = $match['row']; @endphp

                    <div class="p-3 mb-3 border bg-gray-50 rounded">
                        <div><strong>Table:</strong> {{ $match['table'] }}</div>
                        <div><strong>EID:</strong> {{ $r->eid ?? '' }}</div>
                        <div><strong>Email:</strong> {{ $r->email ?? '' }}</div>
                        <div><strong>Name:</strong> {{ $r->FullName ?? '' }}</div>
                        <div><strong>Office:</strong> {{ $r->Officename ?? '' }}</div>
                        <div><strong>License #:</strong> {{ $r->agentLicenseNum ?? '' }}</div>
                        <div><strong>License Checked:</strong> {{ $r->checkLicDate ?? '' }}</div>
                    </div>
                @endforeach
            </div>
        @endforeach

    @else

        <div class="p-4 mb-6 bg-green-100 border border-green-400 rounded">
            <strong>Mirror Match Found:</strong> One record exists in each remote database.<br>
            <strong>AZEmails:</strong> {{ $azMatch['table'] }} / EID {{ $azMatch['row']->eid ?? '' }}<br>
            <strong>ArizonaEmails:</strong> {{ $arizonaMatch['table'] }} / EID {{ $arizonaMatch['row']->eid ?? '' }}
        </div>

        <form method="POST" action="/admin/bounces/update-recipient" class="space-y-4">
            @csrf

            <input type="hidden" name="az_table" value="{{ $azMatch['table'] }}">
            <input type="hidden" name="az_eid" value="{{ $azMatch['row']->eid ?? '' }}">

            <input type="hidden" name="arizona_table" value="{{ $arizonaMatch['table'] }}">
            <input type="hidden" name="arizona_eid" value="{{ $arizonaMatch['row']->eid ?? '' }}">

            <div>
                <label class="font-bold">Email</label>
                <input type="email" name="email" value="{{ $row->email ?? '' }}" class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="font-bold">Office Name</label>
                <input type="text" name="Officename" value="{{ $row->Officename ?? '' }}" class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="font-bold">Office Address 1</label>
                <input type="text" name="officeaddress1" value="{{ $row->officeaddress1 ?? '' }}" class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="font-bold">Office Address 2</label>
                <input type="text" name="officeaddress2" value="{{ $row->officeaddress2 ?? '' }}" class="w-full border p-2 rounded">
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="font-bold">Office City</label>
                    <input type="text" name="officecity" value="{{ $row->officecity ?? '' }}" class="w-full border p-2 rounded">
                </div>

                <div>
                    <label class="font-bold">Office State</label>
                    <input type="text" name="officestate" value="{{ $row->officestate ?? '' }}" class="w-full border p-2 rounded">
                </div>

                <div>
                    <label class="font-bold">Office Zip</label>
                    <input type="text" name="officezip" value="{{ $row->officezip ?? '' }}" class="w-full border p-2 rounded">
                </div>
            </div>

            <div>
                <label class="font-bold">Office Phone</label>
                <input type="text" name="officephone" value="{{ $row->officephone ?? '' }}" class="w-full border p-2 rounded">
            </div>

            <div class="p-4 bg-gray-100 border rounded leading-7">
                <strong>Full Name:</strong> {{ $row->FullName ?? '' }}<br>
                <strong>License #:</strong> {{ $row->agentLicenseNum ?? '' }}<br>
                <strong>License Checked:</strong> {{ $row->checkLicDate ?? '' }}<br>
                <strong>Agent Status:</strong> {{ $row->agentLicStatus ?? '' }}<br>
                <strong>Send OK:</strong> {{ $row->sendOK ?? '' }}<br>
                <strong>Bounce Count:</strong> {{ $row->bounceCount ?? '' }}<br>
                <strong>Suspend Count:</strong> {{ $row->suspendCount ?? '' }}
            </div>

            <button type="submit" class="px-5 py-3 bg-blue-700 text-white font-bold rounded">
                Save Changes to Both Databases
            </button>
        </form>

    @endif

</div>