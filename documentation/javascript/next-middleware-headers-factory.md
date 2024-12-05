# Middleware

Headers set from Next [Middleware](https://nextjs.org/docs/app/building-your-application/routing/middleware) must be in place for [`makeWithSignInRedirect` on the `Request`](request-factory.md) to function correctly.

It is used as follows in `middleware.ts` (or `middleware.js`):

```typescript
import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';
import { NextMiddlewareHeadersFactory } from 'rxante-oauth/dist/NextMiddlewareHeadersFactory';

export async function middleware (req: NextRequest) {
    return NextResponse.next({
        request: { headers: NextMiddlewareHeadersFactory(req) },
    });
}
```
