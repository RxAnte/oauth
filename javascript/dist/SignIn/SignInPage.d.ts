import React, { ReactElement } from 'react';
import { LoadingType } from 'react-loading';
/**
 * @deprecated
 */
export default function SignInPage({ providerId, CustomLoadingPage, reactLoadingType, reactLoadingColor, }: {
    providerId: string;
    reactLoadingType?: LoadingType;
    reactLoadingColor?: string;
    CustomLoadingPage?: () => ReactElement;
}): React.JSX.Element;
