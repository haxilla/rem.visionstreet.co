<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

// One emailed "set your password" link. Only a SHA-256 hash of the link's token
// is stored (never the token itself), so a copy of this table can't be used to
// take over an account. Each link works once and expires (see AgentPasswords).
//
// created_at / expires_at / used_at are always UTC - they are technical times
// for comparing against "now", not an agent's activity (that is passwordResetAt
// on the agent, recorded in the agent's own timezone).
class AgentPasswordReset extends Model
{
    protected $table   = 'remuserdb.agent_password_resets';
    public $timestamps = false;
    protected $guarded = ['id'];
}
