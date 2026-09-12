<?php

Auth::guard('member')->logout();

session()->forget(['impersonator_admin_id', 'impersonating_member_id']);
