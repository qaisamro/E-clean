# Replit Prompt — E-Clean Mobile App (React Native / Expo)

> ⚠️ **The React Native / Expo app ALREADY EXISTS on Replit.**
> Do **NOT** create a new project, do **NOT** re-scaffold, do **NOT** rebuild or
> redesign existing screens, and do **NOT** change the existing navigation
> structure, theme, colors, fonts or layout. Only modify the **registration /
> verification / login flow** as described below, and keep everything else
> working exactly as it is.

Copy everything below the line into Replit AI (Ghostwriter) as the prompt.

---

The E-Clean customer app (React Native + Expo, TypeScript) is already built and
working. I need to change **only the authentication/registration flow** so it
follows the requirements returned by my Laravel backend instead of assuming a
fixed behaviour.

Backend base URL: `EXPO_PUBLIC_API_URL` (already configured). All responses use
the envelope `{ "message": string, "data": {...} }`.

## What the backend now returns

`POST /register` returns in `data`:

- `otp_required` (boolean) — does the user must verify a phone code?
- `approval_required` (boolean) — must an admin approve the account?
- `approval_status` (`"pending"` | `"approved"`)
- `next_step` — one of `"verify_otp"`, `"await_approval"`, `"ready"`, `"rejected"`
- `access` — **present only when `next_step === "ready"`**
- `tracking_token` — present only when `approval_required` is true
- `otp` — only in test environments for whitelisted contacts

`POST /login` returns `200` with `data.access` when the account may log in, or
`403` with `data.next_step` (`verify_otp` / `await_approval` / `rejected`) and
`data.otp_required` / `data.approval_required` when it may not.

`POST /contact/verify` returns `data.otp_verified: true`, `data.next_step`, and
`data.access` **only** when nothing else is outstanding.

## Required changes (modify the existing flow only)

1. **Registration**: after `POST /register`, do not assume anything. Branch on
   `next_step`:
   - `ready` → store `data.access` and continue into the app exactly as today.
   - `verify_otp` → do **not** store any token; navigate to the **existing**
     OTP screen. Fetch the code delivery through the existing resend endpoint.
     After a successful `POST /contact/verify`, store `data.access` **only if**
     the returned `next_step` is `ready`; if it is `await_approval`, go to the
     approval-waiting screen.
   - `await_approval` → skip the OTP screen entirely and go directly to the
     approval-waiting screen, storing `tracking_token`.

2. **OTP screen**: make its **display conditional** on `otp_required`. When
   `otp_required` is false the screen must not be shown at all (no code is sent
   by the server in that case). When true, keep the existing 6-digit input and
   resend behaviour unchanged.

3. **Approval-waiting screen**: reuse the existing pending/approval screen if
   one exists; otherwise add one minimal screen in the same visual style.
   It must:
   - Show the generic Arabic `message` from the API. Never show an internal
     rejection reason — the API does not send one.
   - Refresh status with `POST /customer/join-request-status` and
     `{ tracking_token }` on focus and every 60 s.
   - On `approval_status: "approved"`: clear the pending state, navigate to the
     Login screen (the user logs in to obtain a token).
   - On `approval_status: "rejected"`: show the generic message and keep the
     recovery option.
   - On HTTP `404` (invalid/expired/revoked token): clear the stored token and
     offer recovery.
   - "Lost your tracking token?" → `POST /customer/join-request-recovery` with
     `{ contact }` (always shows the same generic message), then
     `POST /customer/join-request-recovery/verify` with `{ contact, otp }`,
     then store the returned new `tracking_token`.

4. **Login**: on `403`, read `data.next_step`:
   - `verify_otp` → send the user to the OTP screen (and expose the resend
     action). After OTP succeeds they can log in again.
   - `await_approval` / `rejected` → approval-waiting screen.
   On `200`, store `data.access`.

5. **Authenticated API calls**: when any API call returns `403` with a flat body
   containing `next_step` (no `data` wrapper — the approval middleware answers
   that way), clear the stored token and route the user to the matching screen
   instead of showing a generic error.

6. **Persistence**: keep `accessToken` and `trackingToken` in `expo-secure-store`
   as today. Never log the tracking token and never put it in a URL.

7. **Preserve everything else**: screens, navigation, theme, language/RTL,
   existing API services, error handling, loading states, and all other user
   flows must stay as they are.

## Acceptance criteria

- Registration works in all four combinations of OTP on/off and approval on/off,
  driven purely by the server response.
- With OTP disabled the OTP screen is never shown.
- With approval required the app lands on the waiting screen and shows no access
  token before approval.
- A token is only stored when `next_step === "ready"`.
- Existing customers who registered earlier continue to work unchanged.
