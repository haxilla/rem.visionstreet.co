<?php

namespace App\Support;

/**
 * Agent-typed flyer text (remarks) for showing on a flyer or in an email.
 *
 * Agents paste remarks in from listing sites, and those often carry HTML the agent never meant to show:
 * "<p>", "<br>", "&lt;b&gt;". The flyers always print remarks as plain, escaped text (never as raw HTML - that
 * would let anything an agent pastes into an email that goes out under our name), so the tags were printed
 * literally. This turns line-break tags into a space and drops every other tag, leaving plain text that is
 * then escaped as usual.
 */
class FlyerText
{
    public static function plain(?string $text): string
    {
        $text = (string) $text;

        // paragraph / line-break tags become a space: the remarks box flows as one block
        $text = preg_replace('~<\s*/?\s*(?:br|p|div|li|ul|ol|tr|h[1-6])\b[^>]*>~i', ' ', $text);

        // any other tag (<b>, <a href=...>, <span>) goes, its words stay
        $text = strip_tags($text);

        return trim(preg_replace('/[ \t]{2,}/', ' ', $text));
    }
}
