"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.NextMiddlewareHeadersFactory = NextMiddlewareHeadersFactory;
function NextMiddlewareHeadersFactory(req) {
    let { headers } = req;
    if (req.url.includes('_next')) {
        return headers;
    }
    const { nextUrl } = req;
    headers = new Headers(headers);
    headers.set('middleware-pathname', nextUrl.pathname);
    headers.set('middleware-search-params', nextUrl.searchParams.toString());
    return headers;
}
