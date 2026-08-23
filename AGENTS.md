# AGENTS.md - RealtyEmails Project Rules

These rules govern all Codex work in this repository and apply to the entire repository unless a more specific
AGENTS.md establishes additional restrictions for a subdirectory.

## Core Working Rules

- For every substantive change, investigate the relevant existing implementation before proposing or making changes.
- Present an implementation plan and wait for the user's explicit approval before modifying any files.
- Before implementation, identify:
  - The files expected to be modified.
  - The intended change in each file.
  - Any meaningful risks, dependencies, or effects on existing behavior.
- Once the user explicitly approves an implementation plan, that approval authorizes the file modifications
  specifically described in the approved plan. Separate approval is not required before editing each already-approved
  file.
- Prefer the smallest change that safely accomplishes the requested goal.
- Do not make unrelated cleanup, refactoring, modernization, reformatting, renaming, dependency changes, or
  architectural changes.
- Preserve existing conventions in the area being changed unless the user explicitly approves a broader change.
- If a request could reasonably affect existing RealtyEmails behavior outside the requested feature, stop and explain
  the possible effects before proceeding.
- When legacy behavior or intent is uncertain, ask the user rather than guessing.

## Legacy Architecture

RealtyEmails is a hybrid Laravel and legacy/procedural PHP application. Controllers may intentionally load procedural
processors, queries, and maintenance code with `include`, `require`, or `require_once`.

- Treat legacy and procedural code as potentially intentional and depended upon.
- Do not replace, remove, relocate, or substantially restructure legacy code merely because a more modern Laravel
  approach exists.
- Do not convert procedural code to controllers, services, jobs, repositories, or other abstractions unless explicitly
  requested and approved.
- Preserve the established relationship between routes, dynamic processors under `app/`, and Blade views.
- Treat files and directories named `0ld` as retained project material. Do not modify or delete them unless explicitly
  requested.
- Do not assume stock Laravel conventions accurately describe the production database or runtime behavior.

## Approval Boundary

Read-only inspection and analysis are permitted when needed to understand a request. Avoid commands that may generate
caches, compiled assets, logs, lockfile changes, or other filesystem changes during analysis.

An explicitly approved implementation plan authorizes only the file modifications specifically identified and
described in that plan. During implementation, stop and request additional approval before:

- Modifying, creating, moving, renaming, or deleting any file not identified in the approved plan.
- Materially changing the approved implementation approach.
- Expanding the scope beyond the approved plan.
- Proceeding after encountering an unexpected high-risk area.
- Deleting files or removing large blocks of code unless explicitly approved.
- Performing any database, dependency, Git, deployment, production, destructive, or otherwise separately restricted
  operation.
- Making any other broader change than the approved implementation plan.

If investigation or implementation reveals that the approved plan is incomplete, unsafe, or materially inaccurate,
stop and request new approval before continuing.

## Database and Production Data

Database behavior is high risk because this application uses legacy tables, schema-qualified table mappings, and local
and remote database connections.

Never change any of the following without explicit approval:

- Database schemas or tables.
- Migrations, factories, or seeders.
- Database connection definitions or credentials.
- Model connection settings.
- Model table names, schema-qualified mappings, or primary-key mappings.
- Production data.
- Queries or scripts whose purpose is to migrate, rebuild, normalize, convert, or delete data.

Never run any of the following without explicit approval:

- Migrations or migration rollbacks.
- Seeders or factories that write data.
- Destructive database commands.
- Data-conversion, user-rebuild, password-hashing, flyer-code, flyer-slug, or similar maintenance scripts.
- Commands or application paths that may write to a production or remote database.

Before any approved database operation, state the target connection, database/schema, affected tables, expected
writes, rollback or recovery approach, and whether production data is involved.

## Secrets and Credentials

- Never expose, copy, print, log, commit, or modify credentials, secrets, API keys, passwords, tokens, mailbox
  credentials, or production `.env` values.
- Do not display the contents of `.env`, `.env.production`, credential stores, or similarly sensitive files.
- Do not place secrets directly in code, configuration committed to Git, commands, test fixtures, documentation, or
  chat responses.
- Refer to sensitive configuration by environment-variable name only.
- If a secret is encountered unexpectedly, do not repeat it. Stop and notify the user without disclosing its value.

## High-Risk Application Areas

The following areas require explicit discussion and approval before implementation:

- Authentication and session behavior.
- Authorization, roles, middleware, and access controls.
- Administrator impersonation or agent login.
- Dynamic and catch-all routing.
- Email sending and bounce-mailbox processing.
- Remote database connections or queries.
- Photo synchronization and remote uploads.
- File uploads, file paths, and storage behavior.
- Image validation, resizing, conversion, or deletion.
- Any behavior that writes to external services or production infrastructure.

For work in these areas, identify the security, data-integrity, compatibility, and operational risks in the
implementation plan.

If an unexpected high-risk area is encountered during implementation and was not discussed in the approved plan, stop
and request additional approval.

## Dependencies and Build Artifacts

- Never install, remove, or update Composer or npm dependencies without explicit approval.
- Never modify `composer.lock` or `package-lock.json` without explicit approval.
- Do not run dependency installation or update commands merely to inspect the project.
- Do not commit generated frontend assets, caches, logs, or other build artifacts unless the user specifically
  requests and approves that result.
- If a requested implementation appears to require a dependency change, stop and present that as a separate approval
  item.

## Git and Deployment

Never perform any of the following without explicit approval:

- Create commits or tags.
- Push or force-push.
- Pull changes that modify the working tree.
- Merge, rebase, reset, revert, cherry-pick, or amend.
- Create, delete, rename, or switch branches when doing so could affect work in progress.
- Modify Git remotes or repository configuration.
- Deploy the application.
- Connect to or modify a production server.
- Change production configuration, services, files, databases, queues, scheduled tasks, or built assets.

A general development request or approved implementation plan does not authorize Git, deployment, or production
operations. Production access requires the user to request a specific production operation explicitly.

Do not discard or overwrite existing user changes. Treat an already-dirty working tree as user-owned work and avoid
unrelated files.

## Testing and Verification

Use verification proportional to the approved change and its risks.

- Do not run tests, builds, or commands that may modify files or external state until implementation has been
  approved.
- Prefer focused tests and checks relevant to the changed behavior.
- Do not run tests against production or remote databases.
- Do not claim behavior was tested if only static inspection was performed.
- If the existing test environment cannot represent the legacy schema, remote services, mailbox, uploads, or image-
  processing environment, state that limitation clearly.
- Do not fix unrelated failures discovered during testing without a separate plan and approval.

## Implementation Handoff

After implementation, report:

- Exactly which files changed.
- What changed in each file.
- Any behavioral effects or compatibility considerations.
- Which tests and checks were run and their results.
- Anything that could not be tested and why.
- Any remaining risks or recommended manual verification.
- Whether the working tree contained unrelated pre-existing changes.

Show or recommend that the user review `git diff` before committing or deploying. Do not commit or deploy the work
unless separately and explicitly authorized.

