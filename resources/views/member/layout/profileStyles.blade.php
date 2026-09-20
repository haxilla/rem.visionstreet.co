{{--
    Shared look for the agent's own profile-type pages (the Account Info page; the Agent Info
    page carries the same rules inline). Plain CSS so the pages show correctly whether or not
    the site stylesheet has been rebuilt since the last deploy.
--}}
<style>
    .ai-wrap    { max-width: 960px; margin: 0 auto; padding: 8px 16px 64px; }
    .ai-head h1 { margin: 0; font-size: 26px; line-height: 1.2; font-weight: 800; color: #0f172a; letter-spacing: -.01em; }
    .ai-head p  { margin: 6px 0 0; font-size: 14px; color: #64748b; }

    .ai-card    { margin-top: 18px; background: #fff; border-radius: 20px; border: 1px solid rgba(15,23,42,.06);
                  box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 6px 20px rgba(15,23,42,.04); overflow: hidden; }
    .ai-card-b  { padding: 20px 22px 22px; }

    /* a headline band for each topic */
    .ai-sec-h   { display: flex; align-items: center; gap: 14px; padding: 16px 22px; background: #f4f7fd; border-bottom: 1px solid #e3e9f5; }
    .ai-sec-h h2 { margin: 0; font-size: 18px; line-height: 1.2; font-weight: 800; color: #0f172a; }
    .ai-sec-h p  { margin: 3px 0 0; font-size: 13px; color: #64748b; }
    .ai-icon    { flex: 0 0 auto; width: 40px; height: 40px; border-radius: 12px; background: #123f91; color: #fff;
                  display: flex; align-items: center; justify-content: center; }
    .ai-icon svg { width: 20px; height: 20px; }

    .ai-alert   { margin-top: 16px; padding: 11px 16px; border-radius: 14px; font-size: 14px; font-weight: 600; border: 1px solid; }
    .ai-alert.ok  { background: #ecfdf5; border-color: #a7f3d0; color: #047857; }
    .ai-alert.bad { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }

    .ai-btn     { display: inline-block; border-radius: 10px; padding: 8px 14px; font-size: 13px; font-weight: 700; cursor: pointer;
                  border: 1px solid #d5dbe6; background: #fff; color: #334155; text-decoration: none; }
    .ai-btn:hover { background: #f3f5fa; }
    .ai-btn.primary { background: #123f91; border-color: #123f91; color: #fff; }
    .ai-btn.primary:hover { background: #0f3274; }
</style>
