# SignInPage

Sign-in is done via a page and must be set up via a page route.

Create the page at `app/api/auth/sign-in/page.tsx`, return the `SignInPage` component with appropriate params.

```tsx
import React from 'react';
import { Metadata } from 'next';
import { SignInPage } from 'rxante-oauth';

export const metadata: Metadata = {
    title: 'Sign In',
};

export default function Page () {
    return (
        <SignInPage
            // Whatever the ID of the provider you set up is
            providerId="auth0"

            // Optional: use your own loading page. If this is used,
            // reactLoadingType, and reactLoadingColor will be ignored
            CustomLoadingPage={MyCustomComponent}

            // Optional Set the type of the react-loading loader
            reactLoadingType="bubbles"

            // Optional Set the color of the react-loading loader
            reactLoadingColor="#4CA8CB"
        />
    );
}

```
