<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

// One "other known email" of an agent: an address that account is known by besides the login and
// contact emails on the agent record - kept when duplicate accounts are merged (the deleted account's
// emails are saved here on the account that stays), or added by hand on the agent's page.
// See App\Support\AgentKnownEmails. Created with the SQL in that class (SETUP_SQL); nothing here is
// used as a login.
class AgentKnownEmail extends Model
{
    protected $table   = 'remuserdb.agent_known_emails';
    public $timestamps = false;
    protected $guarded = ['id'];
}
