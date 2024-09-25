# README file for Addresses module

## Introduction

The purpose of the addresses module is

- to specify a reusable set of relations for handling (physical) addresses
- to define specializations, e.g. for Dutch addresses, that allow for automated filling in of some of these relations.

The idea is that every physical address is just a set of address-lines
(addrLine1, addrLine2, addrLine3, addrLine4, addrLine5 - which obviously is easily extendable)
that can be concatenated into a single address-label, that can e.g. be printed.
The creation of the address-label is done automatically (in PhysicalAddr.adl)

Then, specializations can be made for specific kinds of physical addresses,
For example, the DutchAddr extension is one that maps nicely on the official Dutch BAG addresses.
Another example is the SchemaOrg extension, which maps nicely on the physical addresses of @schema.org.
Other extensions can be added as well.

A specialization SHOULD automatically populate the addrLine-relations,
which implies that a corresponding address label will automatically be created.

## Installation/configuration

### Physical Addresses

Just include the file PhysicalAddr.adl in your adl file.

- ensure that the `PostcodeAPI.php` file becomes part of the ExecEngine PHP-functions
  (this file defines the functions `concatext` and `concatlines` that help create address labels)
  This file can be found in the `customizations\bootstrap\files\PostcodeAPI.php`.
  It must be copied to the same location in the `customizations` directory for your project.

### Dutch Addressess

In order to make Dutch addresses work, you additionally need to do the following:

- register at [https://www.postcodeapi.nu/](https://www.postcodeapi.nu/) and obtain an X-Api-key
- Configure this X-Api-Key in the `customizations\config\project.yaml` file, as follows:

```markdown
## Key/value pair list of config settings
settings:
  postcodeAPI.X-Api-Key: x-api-key-data
```

- ensure that the `project.yaml` file becomes part of your projects configuration, by copying it to the same location in the `customizations` directory for your project.
- in order to test whether or not this works, you can create a prototype from `DutchAddrAutofillTest.adl`, and exercise the interface.

More information on the API can be found at [https://www.postcodeapi.nu/](https://www.postcodeapi.nu/). There are some alternatieve API's:

- [https://bwnr.nl/postcode.php](https://bwnr.nl/postcode.php) (250/week zonder registratie, 5000/week met registratie, daarboven betalen)
- [https://services.postcode.nl/adresdata/adres-validatie/applicaties](https://services.postcode.nl/adresdata/adres-validatie/applicaties) (abonnement nodig)
- PDOK, zie bijv. [https://pdokforum.geonovum.nl/t/documentatie-en-voorbeelden-locatieserver/262](https://pdokforum.geonovum.nl/t/documentatie-en-voorbeelden-locatieserver/262)
      en [https://github.com/PDOK/locatieserver/wiki/Zoekvoorbeelden-Locatieserver](https://github.com/PDOK/locatieserver/wiki/Zoekvoorbeelden-Locatieserver)

### SchemaOrg Addresses

To be done

That's it.