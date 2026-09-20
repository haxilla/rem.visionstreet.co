<?php

namespace App\Support;

use App\Models\Core\Propagent;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * An agent's web address slug (propagents.agent_slug): the last part of their own page,
 *
 *     https://<the site>/debralee
 *
 * It is the agent's first and last name joined, lowercase, letters and digits only
 * ("Mary-Ann O'Brien" -> maryannobrien). A name that would run past LIMIT characters becomes
 * the first name plus the last initial ("Christopher Vanderbilt-Montgomery" -> christopherv),
 * and if even that is too long the first name is cut to make room for the initial. Two agents
 * with the same name get a number: debralee, debralee2, debralee3.
 *
 * A slug is made ONCE (Propagent's saved hook calls ensure(), as does the backfill command) and
 * never changed by this class - the address goes into emails and has to keep working.
 *
 * The page is served by guestController::segment() only where the site would otherwise show a
 * 404, so a slug can never shadow a real page. Even so, a slug is never allowed to equal
 * anything that IS a page, route or folder - see reserved().
 */
class AgentSlug
{
    /** Longest slug built from a name (a number added for a duplicate can run a few over). */
    public const LIMIT = 20;

    /** Shortest slug worth having ("jo" is not an address). */
    public const MIN = 3;

    /** Names that are never given out, on top of everything found automatically (see reserved()). */
    private const FIXED_RESERVED = [
        'admin', 'member', 'login', 'logout', 'register', 'signup', 'signin', 'homedetails', 'up',
        'api', 'agent', 'agents', 'about', 'contact', 'help', 'support', 'pricing', 'blog', 'terms',
        'privacy', 'search', 'home', 'index', 'flyer', 'flyers', 'listing', 'listings', 'property',
        'properties', 'storage', 'build', 'images', 'my', 'favicon', 'robots', 'sitemap', 'public',
        'assets', 'static', 'vendor', 'hqphotos', 'agentphotos', 'officelogos', 'hqoffice',
        'realtyemails', 'realtyemail', 'visionstreet', 'null', 'undefined',
    ];

    /**
     * Give the agent a slug if they have none and have a name to make one from. Returns the slug
     * the agent has afterwards, or null when they have none. Never throws: a slug problem must not
     * stop an agent's record from saving.
     */
    public static function ensure($agentId): ?string
    {
        try {
            if (!self::columnExists()) {
                return null;
            }

            $agent = Propagent::find($agentId, ['id', 'agent_slug', 'agtFirst', 'agtLast', 'agtFullName']);

            if (!$agent) {
                return null;
            }

            if (filled($agent->agent_slug)) {
                return $agent->agent_slug;
            }

            $slug = self::build($agent->getAttributes());

            if ($slug === null) {
                return null;
            }

            $slug = self::unique($slug, $agent->getKey());

            // straight to the table: no updated_at change, no model events
            Propagent::whereKey($agent->getKey())->toBase()->update(['agent_slug' => $slug]);

            return $slug;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    /**
     * The slug (and contact email) the flyer email's contact banner needs for one agent, read on
     * its own so the flyer queries don't have to carry the new column. Always returns both keys;
     * a value is null when there is none, or when the column hasn't been added yet.
     *
     * @return array{slug:?string, email:?string}
     */
    public static function contactFor($agentId): array
    {
        $result = ['slug' => null, 'email' => null];

        try {
            $row = Propagent::whereKey($agentId)->first(['agtEmail', 'agent_slug']);

            $result['slug']  = filled($row?->agent_slug) ? $row->agent_slug : null;
            $result['email'] = filled($row?->agtEmail) ? trim($row->agtEmail) : null;
        } catch (\Throwable $e) {
            // the column may not exist yet - still get the email
            try {
                $row = Propagent::whereKey($agentId)->first(['agtEmail']);

                $result['email'] = filled($row?->agtEmail) ? trim($row->agtEmail) : null;
            } catch (\Throwable $e2) {
                // nothing to add
            }
        }

        return $result;
    }

    /** The slug these name parts give (an array with agtFirst / agtLast / agtFullName), or null. Not checked for duplicates. */
    public static function build(array $parts): ?string
    {
        [$first, $last] = AgentNames::forForm((object) $parts);

        $first = self::letters($first);
        $last  = self::letters($last);

        if ($first === '' && $last === '') {
            return null;
        }

        $slug = $first . $last;

        if (strlen($slug) > self::LIMIT) {
            // first name + last initial; a first name too long for that is cut to leave room for it
            $initial = substr($last, 0, 1);
            $slug    = strlen($first . $initial) <= self::LIMIT
                ? $first . $initial
                : substr($first, 0, self::LIMIT - strlen($initial)) . $initial;
        }

        return strlen($slug) >= self::MIN ? $slug : null;
    }

    /**
     * The slug, or slug2, slug3 ... - the first that is not reserved and not used by ANOTHER agent.
     * $alsoTaken lets a dry run count the slugs it has handed out but not saved.
     */
    public static function unique(string $slug, $exceptAgentId = null, array $alsoTaken = []): string
    {
        $candidate = $slug;

        for ($n = 2; $n < 1000; $n++) {
            $taken = self::isReserved($candidate)
                || in_array($candidate, $alsoTaken, true)
                || Propagent::where('agent_slug', $candidate)
                    ->when($exceptAgentId, fn ($query) => $query->whereKeyNot($exceptAgentId))
                    ->exists();

            if (!$taken) {
                return $candidate;
            }

            $candidate = $slug . $n;
        }

        return $slug . ($exceptAgentId ?: uniqid());
    }

    public static function isReserved(string $slug): bool
    {
        return in_array(strtolower($slug), self::reserved(), true);
    }

    /**
     * Everything a slug must never equal: the fixed list, the first part of every route the app
     * has, every public page (resources/views/public/*.blade.php) and everything in public/. So a
     * page or folder added later is picked up without touching this list.
     */
    public static function reserved(): array
    {
        static $reserved = null;

        if ($reserved !== null) {
            return $reserved;
        }

        $names = self::FIXED_RESERVED;

        foreach (Route::getRoutes()->getRoutes() as $route) {
            $first = explode('/', trim($route->uri(), '/'))[0] ?? '';

            if ($first !== '' && !str_starts_with($first, '{')) {
                $names[] = $first;
            }
        }

        foreach (glob(resource_path('views/public/*.blade.php')) ?: [] as $file) {
            $names[] = basename($file, '.blade.php');
        }

        foreach (scandir(public_path()) ?: [] as $entry) {
            if ($entry !== '.' && $entry !== '..') {
                $names[] = pathinfo($entry, PATHINFO_FILENAME);
            }
        }

        // the same letters-and-digits form a slug has, so "search_homes" blocks "searchhomes"
        return $reserved = array_values(array_unique(array_filter(array_map(
            fn ($name) => self::letters($name),
            $names
        ))));
    }

    /**
     * The agents a BACKFILL looks at: no slug yet AND they have sent something - an email request
     * (propdelivnow or the propdelivs archive) or a flyer with a real last-sent date. Every other
     * agent is left out of the run entirely. New agents are unaffected: the saved hook gives any
     * agent a slug once they have a name.
     */
    public static function backfillCandidates()
    {
        $since = '1971-01-01';

        return Propagent::query()
            ->where(function ($query) {
                $query->whereNull('agent_slug')->orWhere('agent_slug', '');
            })
            ->where(function ($query) use ($since) {
                $query->whereIn('id', \App\Models\Core\Propdelivnow::query()
                        ->select('propagent_id')
                        ->whereNotNull('emRequest')
                        ->where('emRequest', '>=', $since))
                    ->orWhereIn('id', \App\Models\Core\Propdeliv::query()
                        ->select('propagent_id')
                        ->whereNotNull('emRequest')
                        ->where('emRequest', '>=', $since))
                    ->orWhereIn('id', \App\Models\Core\Propflyerstat::query()
                        ->select('propagent_id')
                        ->whereNotNull('xLastDeliveryDate')
                        ->where('xLastDeliveryDate', '>=', $since));
            });
    }

    /** Whether propagents has the agent_slug column (the SQL in the command's help adds it). */
    public static function columnExists(): bool
    {
        static $exists = null;

        if ($exists === null) {
            $model  = new Propagent();
            $exists = Schema::connection($model->getConnectionName())->hasColumn($model->getTable(), 'agent_slug');
        }

        return $exists;
    }

    /** Plain ASCII, lowercase, letters and digits only. */
    private static function letters($text): string
    {
        return strtolower(preg_replace('/[^A-Za-z0-9]/', '', Str::ascii((string) $text)));
    }
}
