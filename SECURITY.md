# Security Policy

## Supported versions

QuickPoll is a small project; security fixes are applied to the latest `main` branch.

## Reporting a vulnerability

Please report security issues **privately** so they can be fixed before public disclosure.

- Use GitHub's **[Report a vulnerability](https://docs.github.com/en/code-security/security-advisories/guidance-on-reporting-and-writing-information-about-vulnerabilities/privately-reporting-a-security-vulnerability)**
  feature (Security tab → "Report a vulnerability"), **or**
- Open a minimal issue asking for a private contact channel — without disclosing details.

Please include:

- A description of the vulnerability and its impact
- Steps to reproduce or a proof of concept
- Affected files or endpoints, if known

You can expect an acknowledgement within a few days. Thank you for helping keep QuickPoll
and its users safe.

## Known limitations

- **Duplicate-vote prevention is session-based.** It deters casual double-voting but does
  not stop a determined user who clears cookies. Do not rely on QuickPoll for high-stakes or
  binding votes without adding stronger controls.
- There is a **single shared admin password** and no built-in brute-force rate limiting.
  Use a strong password and consider fronting the admin panel with additional protection
  (IP allow-listing, a reverse-proxy auth layer) for sensitive deployments.
