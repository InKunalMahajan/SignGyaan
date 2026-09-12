# SignGyaan Accessibility Standard — Phase 13

SignGyaan is designed for Deaf and hard-of-hearing learners and follows a keyboard-first, visual-first accessibility baseline. Phase 13 applies shared accessibility behavior across learner, teacher, parent, admin, authentication, and public screens without changing business rules.

## 13A — Structure and keyboard access

- Every page with a `<main>` landmark receives a stable main-content target.
- A keyboard-visible **Skip to main content** link is added automatically.
- Navigation regions receive accessible labels when a view does not already provide one.
- Current navigation links receive `aria-current="page"` where the destination matches the current path.
- Focus indicators remain visible and high contrast.
- Core controls use a minimum 44px target height where practical.
- Escape closes the accessibility settings panel and returns focus to its trigger.

## 13B — Visual accessibility

- White, gray, and black remain the product design palette.
- Higher-contrast mode strengthens borders and muted text without adding color dependency.
- Larger-text mode increases the root text size and line spacing while retaining responsive reflow.
- System `prefers-reduced-motion`, `prefers-contrast`, and Windows forced-colors preferences are supported.
- A manual Reduce motion preference is also available.
- Accessibility preferences are stored only in the current browser using `localStorage`; they are not learner records and are not sent to the server.

## 13C — Deaf / hard-of-hearing media support

All learning videos must use visible player controls. If a video has a caption file, render it with `data-caption-src`, optionally `data-caption-lang` and `data-caption-label`. The shared runtime creates a `<track kind="captions">` element.

Example:

```html
<video
    src="/media/lesson.mp4"
    data-caption-src="/media/lesson.en.vtt"
    data-caption-lang="en"
    data-caption-label="English captions"
    data-transcript-id="lesson-transcript"
></video>
<div id="lesson-transcript">...</div>
```

Important rules:

- Do not treat plain English text as an Indian Sign Language translation.
- When an ISL video or teacher-led ISL explanation exists, identify it accurately as ISL support.
- Important audio information must also be available visually through captions, text, diagrams, demonstrations, or equivalent content.
- Captions and transcripts must preserve important educational meaning rather than only literal sound labels.

## 13D — Forms and feedback

- Required native form controls receive `aria-required="true"`.
- Existing visible validation errors are linked to the nearest field with `aria-describedby` when they can be detected safely.
- Fields with an associated validation error receive `aria-invalid="true"`.
- Alert and status regions receive suitable live-region behavior.
- Placeholder text is used as a fallback accessible name only where an existing explicit label is absent; explicit `<label>` elements remain the preferred implementation.

## 13E — Data and content presentation

- Tables without a caption receive a screen-reader-only fallback caption.
- Column headings in `<thead>` receive `scope="col"` if it is missing.
- Videos are constrained to responsive width and always expose controls.
- Status information should use text, labels, symbols, or structure as well as visual styling; meaning must not depend on color alone.

## 13F — Quality gate

`AccessibilityPhase13Test` verifies that the shared runtime and styles stay connected to the application build and retain critical accessibility behaviors. Phase 13 does not claim automatic WCAG conformance for every future page: new content and components must continue to use semantic headings, explicit labels, meaningful link text, captions, and keyboard-operable controls.

## Authoring checklist

Before publishing a lesson or interface change, verify:

1. Keyboard users can reach and operate every interactive control.
2. Focus is clearly visible.
3. Headings are meaningful and ordered logically.
4. Form fields have visible labels and understandable error messages.
5. Images carrying information have useful alternative text; decorative images use empty alt text or are hidden from assistive technology.
6. Video/audio learning content has equivalent visual information, normally captions and/or transcript.
7. ISL support is labeled accurately and never represented by ordinary written English alone.
8. Meaning does not rely only on color, sound, position, hover, or animation.
9. Content remains usable at browser zoom and on narrow screens.
10. Automated tests pass, followed by a manual keyboard and screen-reader spot check.
