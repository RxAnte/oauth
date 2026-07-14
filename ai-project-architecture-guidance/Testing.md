# Testing Rules

Tests should conform to the following guidelines and principles.

## Co-location

Test files should be co-located with the code they test, not off in some `test` directory. This improves discoverability and reduces cognitive load by keeping related code together.

Follow these naming conventions: PHPUnit tests should be suffixed with `Test.php`, as in `NameOfTestedClassTest.php`, and Pest tests should be suffixed with `.test.php` as in `NameOfTestedClass.test.php`. TypeScript/Vitest tests should use `.test.ts`, or `.test.tsx` for files containing JSX.

PHP test files are PHP files — they must also declare `strict_types=1`.

## Arrange, Act, Assert

Every test has exactly three phases, kept visually distinct.

1. Arrange. The "Arrange" phase sets up the state the test requires.
2. Act. The "Act" phase triggers the single behavior under test.
3. Assert. The "Assert" phase verifies one observable outcome.

Examples:

```php
// PHPUnit
public function test_calculates_total_with_tax(): void
{
    // Arrange
    $cart = new Cart();
    $cart->add(new LineItem(price: 10000, quantity: 2));

    // Act
    $total = $cart->total(taxRate: 0.10);

    // Assert
    $this->assertSame(22000, $total);
}
```

```php
// Pest
it('calculates total with tax', function (): void {
    // Arrange
    $cart = new Cart();
    $cart->add(new LineItem(price: 10000, quantity: 2));

    // Act
    $total = $cart->total(taxRate: 0.10);

    // Assert
    expect($total)->toBe(22000);
});
```

Do not assert mid-setup, and do not run multiple Act steps in a single test. If you find yourself doing either, the test is covering too much.

"One observable outcome" means one logical behavior, not necessarily one assertion. A single outcome may require a few related assertions to fully verify. If you find yourself asserting two distinct behaviors, write two tests.

In PHPUnit, `setUp()` may be used to extract Arrange logic that is truly shared across every test in a class. Prefer keeping Arrange visible in the test body when setup is simple or differs between tests. If `setUp()` includes state that only some tests need, the tests likely belong in separate classes.

## Test Isolation

Each test must pass or fail independently. Tests must not share mutable state, and no test should depend on another having run first.

In PHPUnit use `setUp()` and `tearDown()` to reset state between tests. In Pest use `beforeEach()` and `afterEach()`. In Vitest, state is reset between tests by default — do not disable this.

If a test only passes in a specific order, that is a bug in the test suite.

## Test observable behavior, not implementation details

Test what the code does, not how it does it.

Under most circumstances, tests have two legitimate users: the end user and the developer consuming the public API. Do not create a third user — the test — that reaches into private state or verifies that a specific internal method was called.

The exception is when the system under test is a class or method whose sole purpose is to delegate to a specific collaborator or third-party code/package. In those cases, that should be the only responsibility of the system under test.

## Mock at boundaries, not within them

Within the domain of the system under test, use real collaborators. Let the objects talk to each other for real. Mocking objects often means each test passes against a version of its neighbor that may no longer reflect reality.

If a class is too hard to test without mocking its internals, that's a design signal — the dependency probably needs to be extracted and injected so it can be replaced at the boundary.

Mock only at the edges of the system: the database, external HTTP APIs, the filesystem, the clock, etc.

## Don't mock what the local system does not own

When possible, don't mock what the local system does not own, particularly third-party objects that are not interfaces. Local code that needs third-party integration should be implemented through locally owned wrappers and adapters. Those wrappers or adapters may need third-party mocks to be tested, but it should be kept at those boundaries.

## When a high-level test catches a bug, push the failure down

Write a unit test at the lowest level so the next regression fails fast and cheap. Keep the high-level test — it stays as a safety net. The unit test ensures the next failure is caught at the source rather than through the full stack.

## Don't extend a production class in a test just to override one of its methods

If a test needs to fake a class, that class needs an interface — extract one in production code and have the test implement it directly. The telltale sign of this anti-pattern is a `parent::__construct(...)` call in a test file with throwaway values passed to satisfy the production constructor. Anonymous classes that implement a collaborator's interface are a fine lightweight test double. Anonymous classes that extend a concrete production class to override one of its methods are not.

## React / TypeScript (Vitest)

The same AAA structure applies. Use `describe` blocks to group related tests.

When testing React components, use React Testing Library. Query by what the user sees — roles, labels, visible text — not by component internals, CSS class names, or test IDs. This is the TypeScript expression of "test observable behavior, not implementation details."

```tsx
it('shows an error when the email field is left blank', async () => {
    // Arrange
    render(<LoginForm />);

    // Act
    await userEvent.click(screen.getByRole('button', { name: /sign in/i }));

    // Assert
    expect(screen.getByRole('alert')).toHaveTextContent('Email is required');
});
```

Prefer `getByRole`, `getByLabelText`, and `getByText` over `getByTestId`. A test ID is an implementation detail — if the markup changes, the test breaks even if the behavior has not.

Do not use `any` in test files. The type system is as load-bearing in tests as in production code.
