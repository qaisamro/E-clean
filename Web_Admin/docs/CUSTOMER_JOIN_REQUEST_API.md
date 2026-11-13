# Customer Join Requests + OTP — API Contracts

Base URL: `https://<admin-domain>/api`
Response envelope (all endpoints):

```json
{ "message": "string", "data": { } }   // "data" omitted when empty
```

All requests/responses are UTF-8 JSON over **HTTPS** only.

> **Middleware responses** (`ensure.customer.approved`) use a **flat** body —
> `{"message": "...", "next_step": "verify_otp"}` — with no `data` wrapper.

---

## 0. Registration states (driven by Super Admin settings)

Two **independent** settings control the flow. Neither duplicates the other:

| Setting | Where | Meaning |
| --- | --- | --- |
| `otp.enabled` + `otp.enabled_registration` | Integration Center → OTP & Authentication | phone verification required at registration |
| `customer_registration.enabled` + `customer_registration.approval_required` | Integration Center → طلبات انضمام العملاء | admin approval required |

| State | `otp_required` | `approval_required` | Behaviour |
| --- | --- | --- | --- |
| 1 | false | false | account created, **access token issued immediately**, no OTP screen |
| 2 | true | false | account created, **no token** until OTP verified, then token issued |
| 3 | false | true | account created, **no OTP**, join request `pending`, tracking token returned, no access token |
| 4 | true | true | account created, **OTP first**, join request `pending`, tracking token, no access token until OTP **and** approval |

`next_step` is always one of: `verify_otp`, `await_approval`, `ready`, `rejected`.

---

## 1. Register

`POST /api/register`

Request:

| Field | Type | Notes |
| --- | --- | --- |
| `first_name` | string | required |
| `last_name` | string | required |
| `email` | string | required, unique |
| `mobile` | string | required, unique |
| `password` | string | required, confirmed |
| `password_confirmation` | string | required |
| `device_key` | string | optional |

**State 1 (OTP off, approval off)** — `200 OK`

```json
{
  "message": "Registration successfully complete",
  "data": {
    "user": { "...": "UserResource" },
    "otp_required": false,
    "approval_required": false,
    "approval_status": "approved",
    "next_step": "ready",
    "access": { "auth_type": "Bearer", "token": "...", "expires_at": "..." }
  }
}
```

**State 2 (OTP on, approval off)** — `200 OK`

```json
{
  "message": "Registration successfully complete",
  "data": {
    "user": { "...": "UserResource" },
    "otp_required": true,
    "approval_required": false,
    "approval_status": "approved",
    "next_step": "verify_otp"
  }
}
```

**State 3 (OTP off, approval on)** — `200 OK`

```json
{
  "message": "تم إنشاء حسابك بنجاح، وطلبك قيد المراجعة...",
  "data": {
    "user": { "...": "UserResource" },
    "otp_required": false,
    "approval_required": true,
    "approval_status": "pending",
    "next_step": "await_approval",
    "tracking_token": "a1b2c3...64-hex-chars"
  }
}
```

**State 4 (OTP on, approval on)** — `200 OK`

```json
{
  "message": "تم إنشاء حسابك بنجاح، وطلبك قيد المراجعة...",
  "data": {
    "user": { "...": "UserResource" },
    "otp_required": true,
    "approval_required": true,
    "approval_status": "pending",
    "next_step": "verify_otp",
    "tracking_token": "a1b2c3...64-hex-chars"
  }
}
```

Server rules:

- `access` is returned **only** when `next_step === "ready"`.
- `otp` is included only for whitelisted test contacts in local/staging/testing.
- `mobile_verified_at` is never set here — only after a successful OTP (§2),
  and only when OTP is enabled.
- The account is flagged with the OTP policy it was created under
  (`users.phone_verification_required`). Accounts created earlier keep `false`,
  so enabling OTP later never locks out existing customers.
- Rate limit: 10 requests/minute. Too many OTP requests → `429`.

## 2. Verify mobile (OTP)

`POST /api/contact/verify` — public, rate limit 5/minute.

| Field | Type | Notes |
| --- | --- | --- |
| `contact` | string | email or mobile |
| `otp` | string | numeric code |

`200 OK`:

```json
{
  "message": "Mobile verification complete",
  "data": {
    "user": { "...": "UserResource", "phone_verified": true },
    "otp_verified": true,
    "approval_required": false,
    "approval_status": "approved",
    "next_step": "ready",
    "access": { "auth_type": "Bearer", "token": "...", "expires_at": "..." }
  }
}
```

- `next_step` is `await_approval` when a join request is pending, `rejected` when
  it was rejected — in both cases **no** access token is returned.
- `422` when OTP is disabled, `400` for an invalid code, `429` when limited.

## 3. Login

`POST /api/login` — rate limit 5/minute.

| Contact state | Response |
| --- | --- |
| Phone verification outstanding (account registered under an OTP policy) | `403` — `{"message": "...", "data": {"otp_required": true, "phone_verified": false, "approval_required": false, "approval_status": "approved", "next_step": "verify_otp"}}` |
| Join request pending | `403` — `{"message": "...", "data": {"approval_status": "pending", "approval_required": true, "next_step": "await_approval", "message": "..."}}` |
| Join request rejected | `403` — `{"message": "...", "data": {"approval_status": "rejected", "approval_required": true, "next_step": "rejected", "message": "..."}}` |
| Valid credentials, nothing outstanding | `200` — `{user, access, approval_status: "approved", approval_required: false, next_step: "ready"}` |
| Invalid credentials | `400` — `{"message": "Credential is invalid!"}` |

## 4. Check join-request status (public, token-based)

`POST /api/customer/join-request-status` — replaces the removed
`GET /api/customer/join-request-status/{contact}`.

| Field | Type | Notes |
| --- | --- | --- |
| `tracking_token` | string | required, 16–128 chars |

Rate limit: 10/minute.

`200 OK` — `{"message": "حالة الطلب.", "data": {"approval_status": "pending|approved|rejected", "message": "..."}}`

`404` — unknown, expired or revoked token (all indistinguishable):
`{"message": "رمز المتابعة غير صالح أو منتهي الصلاحية."}`

Never returns user data, contact details or rejection reasons.

## 5. Recover a lost tracking token (2 steps)

Recovery OTP uses the **`generic`** purpose, which follows only the global OTP
switch. It therefore still works when **registration** OTP is switched off, and
it is a separate mechanism from the registration flow.

### Step 1 — request an OTP

`POST /api/customer/join-request-recovery` — rate limit 3/minute.

| Field | Type | Notes |
| --- | --- | --- |
| `contact` | string | email or mobile |

`200 OK` (identical whether or not the account exists):

```json
{ "message": "إذا كان هناك طلب انضمام لهذا الاتصال، فقد تم إرسال رمز التأكيد." }
```

`422` when OTP is disabled globally:
`{"message": "خدمة إرسال رموز التحقق معطلة حاليًا، يرجى التواصل مع الدعم."}`

### Step 2 — verify OTP, receive a new token

`POST /api/customer/join-request-recovery/verify` — rate limit 5/minute.

| Field | Type | Notes |
| --- | --- | --- |
| `contact` | string | email or mobile |
| `otp` | string | 4–12 chars |

`200 OK`: `{"message": "تم إنشاء رمز متابعة جديد.", "data": {"tracking_token": "...", "approval_status": "pending"}}`
`422`: invalid/expired code or no join request — `{"message": "رمز غير صالح أو منتهي الصلاحية."}`

Issuing a new token invalidates the previous one.

## 6. Authenticated status check

`GET /api/customer/join-request-status` — requires `Authorization: Bearer <token>`.

Returns `approval_status`, `approval_required`, `next_step` and a generic
message only; no PII and no rejection reason. Customers without a join-request
record get `approval_status: "approved"` and `next_step: "ready"`.

Note: a pending or rejected account is blocked by the middleware (`403`) before
it reaches this endpoint, so in practice it only answers for accounts that may
use the app.

## 7. Protected APIs and blocked accounts

`ensure.customer.approved` protects the authenticated customer group.

| Condition | Response |
| --- | --- |
| Phone verification outstanding | `403` flat — `{"message": "...", "otp_required": true, "next_step": "verify_otp"}` |
| Join request pending | `403` flat — `{"message": "...", "approval_status": "pending", "next_step": "await_approval"}` |
| Join request rejected | `403` flat — `{"message": "...", "approval_status": "rejected", "next_step": "rejected"}` (no internal reason) |
| Approval enabled but the store cannot be read | `503` flat — `{"message": "...", "next_step": "retry_later"}` |

In every blocked case the bearer token is **revoked**, so an existing token
stops working immediately — a valid token never bypasses the gate.

**Failure policy (fail-closed):** while the approval feature is **enabled**, an
unreadable `customer_join_requests` store means approval status cannot be
verified, so protected access is refused (`503`) instead of assuming the account
is approved. When the feature is **off**, the store is not needed and the
previous behaviour is preserved, so existing customers keep working before the
migration runs.

## 7b. Registration when the store is unavailable

`POST /api/register` with approval enabled and the table missing returns
`503` — `{"message": "تعذر إنشاء طلب الانضمام حاليًا...", "data": {"next_step": "retry_later"}}`.
The whole registration runs inside a transaction, so **no partial or unapproved
account is ever created**. Normal registration (approval off) is unaffected.

## 8. Admin panel (web, root/admin)

| Route | Purpose |
| --- | --- |
| `GET /customer-join-requests` | list, filters, pending badge |
| `GET /customer-join-requests/{id}` | detail |
| `POST /customer-join-requests/{id}/approve` | approve → activates the account (never sets `mobile_verified_at`) |
| `POST /customer-join-requests/{id}/reject` | reject + revoke sessions |
| `POST /customer-join-requests/{id}/review` | approve/reject with note |
| `POST /customer-join-requests/{id}/review-again` | rejected → back to pending |
| `POST /customer-join-requests/{id}/revoke-tracking-token` | invalidate a tracking token |

## 9. Settings (Integration Center — runtime, no code change)

| Key | Default | Notes |
| --- | --- | --- |
| `otp.enabled` | `1` | global OTP switch (existing) |
| `otp.enabled_registration` | `1` | OTP required at registration (existing) |
| `customer_registration.enabled` | `0` | enables the approval check |
| `customer_registration.approval_required` | `0` | requires approval when enabled |
| `customer_registration.pending_message` | Arabic | shown to pending users |
| `customer_registration.approved_message` | Arabic | shown to approved users |
| `customer_registration.rejected_message` | Arabic | shown to rejected users (generic — never the internal reason) |

Both flags must be `1` for approval to apply. Turning them off later does **not**
auto-approve requests that are already pending — a Super Admin must still act.

## 10. Database change

`2026_11_09_000001_add_phone_verification_required_to_users_table`

- Adds `users.phone_verification_required` (boolean, default `0`).
- Additive: existing rows get `0` (not gated), so no existing customer is
  affected by enabling OTP afterwards.

## 11. Deployment notes

- Two new migrations: `2026_11_08_000001_create_customer_join_requests_table`
  and `2026_11_09_000001_add_phone_verification_required_to_users_table`.
  Both are additive — no existing data is modified or deleted.
- Deploy with approval disabled (default) to keep current behaviour exactly.
- If a migration has not run yet, the read paths fail open (customers keep
  working) instead of erroring; the gate cannot be enforced until the table
  exists.
- **Rollback warning:** `migrate:rollback` drops `customer_join_requests` and
  removes the users column. Never use it after real requests exist — restore
  from the database backup instead.
