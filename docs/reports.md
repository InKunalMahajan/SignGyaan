# SignGyaan Reports — Phase 15

Phase 15 adds role-aware progress and performance reports without changing existing learning, assessment, mastery, or authorization rules.

## Report access

- **Learner:** only the signed-in learner's own mastery and completed assessment data.
- **Teacher:** only learners in the teacher's own classes, courses assigned to those classes, and assessments created by that teacher.
- **Parent:** only learners connected through an **approved** parent-learner link. Pending and declined links are not reportable.
- **Admin:** platform-wide reporting across users, classes, and mastery records.
- Guests cannot open or export reports.

## Report content

The report UI shows summary metrics and a detailed table containing learner, course, lesson completion, assessment percentage, mastery percentage, and mastery level where available.

Mastery labels continue to use the existing SignGyaan thresholds:

- 0–39: Needs Support
- 40–59: Developing
- 60–79: Good
- 80–100: Mastered

Phase 15 reads the existing stored mastery values; it does not create a second mastery formula.

## CSV export

`GET /reports/export` returns a UTF-8 CSV download. Exported rows follow exactly the same role-aware scope as the on-screen report. No user can export data they are not authorized to see.

## Accessibility

Reports use semantic headings, summary cards, a real table with a caption and column scopes, keyboard-operable export action, responsive horizontal table scrolling, and the shared Phase 13 accessibility runtime.

## Privacy and safety rules

1. Do not expose cross-teacher class or learner data to teachers.
2. Do not expose another learner's report to a learner.
3. Parent reports require an approved link.
4. CSV export must reuse the same authorization scope as the report screen.
5. Reports must not reveal passwords, authentication secrets, AI keys, or hidden system metadata.
6. Reports are read-only. They do not change scores, mastery, assessment attempts, or progress.

## Quality gate

Run:

```powershell
php artisan test --filter=ReportsPhase15Test
php artisan test --filter=SearchPhase14Test
php artisan test --filter=AccessibilityPhase13Test
php artisan test --filter=MasteryProgressSystemTest
```

Phase 15 adds no database migration.
