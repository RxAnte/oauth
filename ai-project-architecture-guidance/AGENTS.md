## Architecture Rules

- Organize code by user flow and use case rather than by technical concern.
- Prefer co-location. UI, behavior, state, and feature-local helpers should live near the feature they serve.
- Optimize for locality of behavior. A developer should be able to understand or change a feature by reading a small, nearby cluster of files.
- Prefer file moves first and abstraction second.
- Prefer one small named object per workflow over one large manager.
  - Prefer role-based names such as `Workflow`, `Store`, `Factory`, `Resolver`, `Builder`, and `Repository`.
- Prefer file names that describe a role, feature slice, or user-visible responsibility rather than "an extension of X".
- Avoid vague suffixes such as `Actions`, `Content`, `Bindings`, `Operations`, or similar buckets when a more role-based name is available.
- Avoid vague buckets and vague names such as `Services`, `Managers`, `Helpers`, and `Utilities`.
- Keep extracted collaborators feature-local by default.
- When unsure between centralization and co-location, choose co-location.
- Do not normalize code into cross-cutting layers unless explicitly asked.
- When touching a large file, prefer extracting feature-local collaborators over extending the file further.
- Optimize for boundary clarity and maintainability over deduplication. Do not introduce shared abstractions for incidental duplication.

## PHP

- Declare `strict_types=1` at the top of every PHP file.
- Use PHP enums for any known set of values. Do not use magic strings or loose string constants when the valid options are known at compile time.
- Do not pass raw arrays as data transport between layers. Wrap them in purpose-built typed collection classes that validate and type-hint item types in the constructor.
- Prefer constructor injection. Do not use the service locator pattern — dependencies must be explicit, visible at the declaration site, and statically discoverable.
- Write code that is statically verifiable. Prefer patterns that phpstan can analyze without execution.

## React

- Write TypeScript. Do not use plain JavaScript. Rely on the type system to prevent silent coercion errors.
- Co-locate event handlers and component behavior with the component. Do not extract behavior into separate files in the name of "separation of concerns."

## Git

Do not stage, commit, push, or run any destructive git commands without explicit instruction. Reading git state (status, log, diff) is fine.

The user may intentionally stage acceptable work before asking for further changes. Do not treat staged changes as a problem, and do not unstage files unless explicitly asked. When reporting repo state, distinguish staged and unstaged changes only if it matters for the task.

## Build Quality

The project must pass linting (phpcs, phpstan, eslint, tsc) with zero warnings.

## Review Standard

Before closing work, self-check against:

- `Architecture.md`
- `ReviewChecklist.md`
