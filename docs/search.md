# SignGyaan Global Search — Phase 14

Phase 14 adds one authenticated search experience across learner, teacher, parent, and admin workspaces.

## Search rules

Search is permission-aware. It must never turn inaccessible records into discoverable records.

- Learners can search only their enrolled classes and the courses, active chapters, and published lessons available through those classes.
- Teachers can search only their own classes, courses they created or teach, their chapters and lessons, and assessments they created.
- Parents can search only learners linked to them through an approved parent-learner relationship.
- Admins can search platform users, classes, courses, chapters, and lessons.
- Guests cannot use search.

The first release deliberately uses normal database queries instead of an external search engine. This keeps authorization close to the source data and is appropriate for the current SignGyaan scale. Search results are capped per category.

## Accessibility

- Search uses a native GET form with `role="search"` and a visible search results page.
- Desktop workspaces receive a header search field.
- Small screens receive a visible Search link.
- `Ctrl+K` / `Cmd+K` focuses the header search field when available.
- Results use headings, lists, text labels, and keyboard-visible focus styles.
- Search does not rely on color alone.

## Privacy and security

Search must not expose hidden learner records, unapproved parent links, another teacher's private classes, draft learner lessons, passwords, API keys, or unrestricted database fields.

Whenever a new searchable entity is added, its authorization scope must be implemented before its text matching logic.

## Quality gate

Run:

```powershell
npm run build
php artisan optimize:clear
php artisan test --filter=SearchPhase14Test
php artisan test --filter=AccessibilityPhase13Test
php artisan test --filter=TeacherAiAssistantPhase11Test
php artisan test --filter=TeacherRagPhase12Test
```

No database migration is required for Phase 14.
