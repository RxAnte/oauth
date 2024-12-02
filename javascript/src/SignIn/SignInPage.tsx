'use client';

import React, { ReactElement, useEffect } from 'react';
import { signIn } from 'next-auth/react';
import ReactLoading, { LoadingType } from 'react-loading';

export default function SignInPage (
    {
        providerId,
        CustomLoadingPage,
        reactLoadingType = 'spin',
        reactLoadingColor = '#000',
    }: {
        providerId: string;
        reactLoadingType?: LoadingType;
        reactLoadingColor?: string;
        CustomLoadingPage?: () => ReactElement;
    },
) {
    useEffect(() => {
        const url = new URL(window.location.href);

        signIn(providerId, {
            callbackUrl: url.searchParams.get('authReturn')?.toString(),
        });
    });

    if (CustomLoadingPage) {
        return <CustomLoadingPage />;
    }

    return (
        <div style={{
            position: 'fixed',
            top: 0,
            left: 0,
            bottom: 0,
            right: 0,
            width: '100%',
            height: '100vh',
            zIndex: '999',
            overflow: 'hidden',
            backgroundColor: 'white',
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center',
            justifyContent: 'center',
        }}
        >
            <ReactLoading
                type={reactLoadingType}
                color={reactLoadingColor}
            />
        </div>
    );
}
