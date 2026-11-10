import eslintrc from 'eslint-config-rxante-coding-standards';

export default [
    ...eslintrc,
    {
        ignores: [
            'dist/**',
            'coverage/**',
            // Outside tsconfig's src/ include, so type-aware rules can't
            // parse it, and adding it to tsconfig would change dist's layout
            'setup.ts',
        ],
    },
    {
        rules: {
            // This is a library, not a Next app, so there is no pages directory
            '@next/next/no-html-link-for-pages': 'off',
        },
    },
    {
        files: ['**/*.test.ts', '**/*.test.tsx'],
        rules: {
            // vi.spyOn needs a module namespace, and index.test.ts inspects
            // the package's namespace to check its public exports
            'import-x/no-namespace': 'off',
            'import-x/namespace': 'off',
        },
    },
];
