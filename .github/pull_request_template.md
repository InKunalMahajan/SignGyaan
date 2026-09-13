## Summary

Describe the change and the user-facing or domain behavior it affects.

## Verification

- [ ] Focused feature/regression tests pass
- [ ] Full `php artisan test` suite passes
- [ ] `npm run build` passes
- [ ] `composer audit` reviewed
- [ ] `npm audit --audit-level=high` reviewed
- [ ] Role and ownership boundaries checked where applicable
- [ ] Accessibility impact checked where applicable
- [ ] External AI/provider calls are faked in tests
- [ ] No real secrets, credentials, or personal data added
- [ ] Database changes are additive and safe; never rely on `migrate:fresh`

## Database changes

State `None` or list migrations and rollback considerations.

## Notes

Include any manual verification steps or known limitations.
