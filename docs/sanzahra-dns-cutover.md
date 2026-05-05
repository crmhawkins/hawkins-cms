# Sanzahra DNS Cutover

## Pre-cutover checklist
- [ ] hawkins-cms deployed to Coolify .81
- [ ] Sanzahra tenant seeded: `php artisan sanzahra:migrate`
- [ ] Domain sanzahra.com (or subdomain sanzahra.hawkins.es) added in Coolify
- [ ] SSL certificate issued
- [ ] HTTP 200 verified on staging URL

## DNS changes
- A record: sanzahra.com → 217.160.39.81
- CNAME: www.sanzahra.com → sanzahra.com

## Post-cutover verification
- [ ] https://sanzahra.com → 200 OK
- [ ] All 13 pages accessible
- [ ] Images loading correctly
- [ ] Contact form submits without error
- [ ] Admin login at https://sanzahra.com/admin works

## Rollback
If issues: revert A record to previous IP. Coolify old deploy UUID: mo8s888wc8ggkg8g4s0c04ok (static HTML).
