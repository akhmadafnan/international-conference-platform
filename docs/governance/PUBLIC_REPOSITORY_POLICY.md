# Public Repository Policy

This repository may remain public provided it contains only sanitized engineering assets.

## Allowed
- source code;
- architecture/docs;
- PRD;
- public workflow;
- synthetic tests/data;
- example config without credentials.

## Never commit
- `.env` or real secrets;
- API keys/tokens;
- OAuth client secrets;
- database passwords/credentialed URLs;
- payment gateway secrets;
- WhatsApp API credentials;
- SMTP passwords;
- private keys;
- real participant PII;
- payment proofs;
- private reviewer comments tied to real people;
- production backups;
- confidential finance reconciliation data.

Public visibility does not require an open-source license. License choice is a separate Product Owner decision.
