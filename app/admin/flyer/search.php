<?php

$q = trim(request('q', ''));

if (strlen($q) < 2) {
    return response()->json([]);
}

$flyers = DB::table('propflyers')
    ->select('id', 'xFullStreet', 'xMlsNum')
    ->whereNull('deleted_at')
    ->where(function ($query) use ($q) {
        $query->where('id', $q)
            ->orWhere('xFullStreet', 'like', "%{$q}%")
            ->orWhere('xMlsNum', 'like', "%{$q}%");
    })
    ->limit(10)
    ->get()
    ->map(function ($flyer) {
        return [
            'id' => $flyer->id,
            'address' => $flyer->xFullStreet ?: 'No Address',
            'mls' => $flyer->xMlsNum,
        ];
    });

header('Content-Type: application/json');
echo json_encode($flyers);
exit;
