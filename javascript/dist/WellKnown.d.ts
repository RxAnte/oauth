interface WellKnown {
    authorizationEndpoint: string;
    tokenEndpoint: string;
    userinfoEndpoint: string;
}
export declare function GetWellKnown(wellKnownUrl: string): Promise<WellKnown>;
export {};
