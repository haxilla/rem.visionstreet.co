{{--
    Shared look for admin pages built from now on (the same calm style as the Agents page): a slim
    title line with search, one white card holding chips + a table, slim alerts, and simple forms.
    Plain CSS on purpose (.ui-*), so a page looks right whether or not the site stylesheet has been
    rebuilt since the last deploy.
--}}
<style>
    .ui-wrap    { max-width: 1400px; margin: 0 auto; }

    .ui-top     { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
    .ui-title   { display: flex; align-items: baseline; gap: 10px; min-width: 0; }
    .ui-title h1 { margin: 0; font-size: 22px; line-height: 1.2; font-weight: 650; letter-spacing: -.01em; color: #0f172a; }
    .ui-title span { font-size: 13px; color: #64748b; white-space: nowrap; }
    .ui-tools   { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; flex: 1 1 320px; justify-content: flex-end; }

    .ui-search  { position: relative; flex: 1 1 260px; max-width: 420px; }
    .ui-search input { width: 100%; height: 40px; border: 1px solid #d5dbe6; border-radius: 12px; background: #fff; padding: 0 14px 0 38px;
                       font-size: 14px; color: #0f172a; outline: none; box-shadow: 0 1px 2px rgba(15,23,42,.04); }
    .ui-search input:focus { border-color: #214e9b; box-shadow: 0 0 0 3px rgba(33,78,155,.12); }
    .ui-search svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #94a3b8; pointer-events: none; }

    .ui-btn     { display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 40px; padding: 0 16px; border-radius: 12px;
                  border: 1px solid #d5dbe6; background: #fff; color: #334155; font-size: 14px; font-weight: 650; cursor: pointer; text-decoration: none; }
    .ui-btn:hover { background: #f3f5fa; }
    .ui-btn.primary { background: #214e9b; border-color: #214e9b; color: #fff; }
    .ui-btn.primary:hover { background: #1b3f80; }
    .ui-btn.danger  { color: #b91c1c; border-color: #fecaca; }
    .ui-btn.danger:hover { background: #fef2f2; }
    .ui-btn.sm  { height: 32px; padding: 0 12px; font-size: 13px; border-radius: 10px; }

    .ui-card    { background: #fff; border-radius: 18px; box-shadow: 0 8px 28px rgba(15,23,42,.06); overflow: hidden; margin-bottom: 16px; }
    .ui-card-h  { padding: 14px 20px; border-bottom: 1px solid #e8edf5; }
    .ui-card-h h2 { margin: 0; font-size: 15px; font-weight: 700; color: #0f172a; }
    .ui-card-h p  { margin: 2px 0 0; font-size: 12.5px; color: #64748b; }
    .ui-card-b  { padding: 16px 20px 20px; }

    .ui-chips   { display: flex; gap: 2px; padding: 0 10px; border-bottom: 1px solid #e8edf5; overflow-x: auto; white-space: nowrap; scrollbar-width: none; }
    .ui-chips::-webkit-scrollbar { display: none; }
    .ui-chip    { display: inline-flex; align-items: center; gap: 8px; padding: 13px 12px; margin-bottom: -1px; font-size: 13px; font-weight: 600;
                  color: #64748b; text-decoration: none; border-bottom: 2px solid transparent; }
    .ui-chip:hover { color: #0f172a; }
    .ui-chip.is-on { color: #214e9b; border-bottom-color: #214e9b; }
    .ui-count   { padding: 3px 7px; border-radius: 999px; background: #eef2f8; color: #475569; font-size: 11px; font-weight: 700; line-height: 1; }
    .ui-chip.is-on .ui-count { background: #214e9b; color: #fff; }

    .ui-filters { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; padding: 12px 16px; border-bottom: 1px solid #eef1f6; background: #fafbfd; }
    .ui-select  { height: 34px; border: 1px solid #d5dbe6; border-radius: 10px; background: #fff; padding: 0 10px; font-size: 13px; color: #334155; outline: none; }
    .ui-select:focus { border-color: #214e9b; }

    .ui-alert   { margin-bottom: 12px; padding: 10px 14px; border-radius: 12px; font-size: 13px; font-weight: 600; border: 1px solid; }
    .ui-alert.ok  { background: #ecfdf5; border-color: #a7f3d0; color: #047857; }
    .ui-alert.bad { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }
    .ui-alert.warn { background: #fffbeb; border-color: #fde68a; color: #92400e; }

    .ui-scroll  { overflow-x: auto; }
    .ui-table   { width: 100%; border-collapse: collapse; font-size: 14px; }
    .ui-table th { padding: 10px 16px; text-align: left; font-size: 11px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase;
                   color: #64748b; background: #f8fafd; border-bottom: 1px solid #e6eaf2; white-space: nowrap; }
    .ui-table td { padding: 9px 16px; border-bottom: 1px solid #eef1f6; color: #334155; vertical-align: middle; }
    .ui-table tr:hover td { background: #fafbfd; }
    .ui-table tr:last-child td { border-bottom: 0; }
    .ui-table .actions { text-align: right; white-space: nowrap; }
    .ui-table .actions form { display: inline; }
    .ui-empty   { padding: 36px 16px; text-align: center; font-size: 14px; color: #64748b; }

    .ui-pill    { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; background: #eef1f6; color: #475569; }
    .ui-pill.phoenix  { background: #e0ecff; color: #1d4ed8; }
    .ui-pill.northern { background: #dcfce7; color: #166534; }
    .ui-pill.southern { background: #ffedd5; color: #9a3412; }
    .ui-pill.western  { background: #f3e8ff; color: #6b21a8; }
    .ui-muted   { color: #94a3b8; }

    .ui-grid    { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 14px 16px; }
    .ui-field label { display: block; margin-bottom: 5px; font-size: 12.5px; font-weight: 700; color: #334155; }
    .ui-field input, .ui-field select { width: 100%; height: 40px; border: 1px solid #d5dbe6; border-radius: 10px; background: #fff; padding: 0 12px;
                       font-size: 14px; color: #0f172a; outline: none; }
    .ui-field input:focus, .ui-field select:focus { border-color: #214e9b; box-shadow: 0 0 0 3px rgba(33,78,155,.12); }
    .ui-help    { margin-top: 4px; font-size: 12px; color: #64748b; line-height: 1.4; }
    .ui-form-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 18px; }

    details.ui-add > summary { list-style: none; cursor: pointer; }
    details.ui-add > summary::-webkit-details-marker { display: none; }

    @media (max-width: 640px) { .ui-search { max-width: none; flex-basis: 100%; } }
</style>
