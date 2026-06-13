<?php

$q = trim(request('q', ''));

 if (strlen($q) < 2) {
    return response()->json([]);
}

$agents = DB::table('propagents')
    ->select('id', 'agtFirst', 'agtLast', 'agtFullName', 'agtUname', 'agtEmail')
    ->where(function ($query) use ($q) {
        $query->where('id', $q)
            ->orWhere('agtFirst', 'like', "%{$q}%")
            ->orWhere('agtLast', 'like', "%{$q}%")
            ->orWhere('agtFullName', 'like', "%{$q}%")
            ->orWhere('agtUname', 'like', "%{$q}%")
            ->orWhere('agtEmail', 'like', "%{$q}%");
    })
    ->limit(10)
    ->get()
    ->map(function ($agent) {
        $name = trim(($agent->agtFirst ?? '') . ' ' . ($agent->agtLast ?? ''));

        if ($name === '') {
            $name = $agent->agtFullName
                ?: $agent->agtUname
                ?: $agent->agtEmail
                ?: 'No Name';
        }

        return [
            'id' => $agent->id,
            'name' => $name,
            'email' => $agent->agtEmail,
        ];
    });

header('Content-Type: application/json');
echo json_encode($agents);
exit;