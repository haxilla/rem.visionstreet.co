<?php
use App\Models\Core\Propagent; // change to your actual model

$member = Propagent::findOrFail($id);

session([
    'impersonator_admin_id' => Auth::guard('admin')->id(),
    'impersonating_member_id' => $member->id,
]);

Auth::guard('member')->login($member);