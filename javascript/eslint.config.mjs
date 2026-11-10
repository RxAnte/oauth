import eslintrc from 'eslint-config-rxante-coding-standards';

export default [
    ...eslintrc,
    {
        ignores: ['dist/**', 'coverage/**'],
    },
];
