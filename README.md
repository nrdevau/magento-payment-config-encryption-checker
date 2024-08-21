# magento-payment-config-encryption-checker
Crudely check that Magento 2 payment keys are encrypting/decrypting ok.

Note: this is intentionally breaking best practice, using the Object Manager directly so it is not left in the wild (hopefully)

Disclaimer: This is intended to be used on prod, once, by someone who knows what they're doing. Please don't install on prod and then leave it!

## Installation
Copy `nrdev-encryption-check.php` to `<mageroot>/dev/tools/nrdev-encryption-check.php`
Then run the script to see which config values need addressing
Then delete `<mageroot>/dev/tools/nrdev-encryption-check.php`

### Motivation
This was written to help diagnose issues after the APSB24-40 hotfix with the resultant encryption key rotation.

Please hit me up if you need assistance (nate@nrdev.au)


TODO: maybe make a command in N98 - depending on whether this is useful for anyone other than me