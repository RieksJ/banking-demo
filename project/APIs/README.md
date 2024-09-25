# README for APIs

The purpose of any API file is to provide the simplest possible, yet most reusable API that allows for 
- obtaining (parts of) data-objects and/or credentials, thereby creating an object in the &-database, and
- providing (parts of) data-objects from the &-database, possibly enveloping them as some kind of credential.

The structure of an API file is generally as follows:
1. It has `CLASSIFY` statements that allow an API to be 'called' from another API.
   It has `REPRESENT` statements to ensure that incompatible changes is representations do not go undetected.
2. It must `INCLUDE` the API files that it uses/calls. Using/calling such APIs should not require additional code.
3. It must defines all relations that are being used in the API, possibly duplicating other code. 
4. It may have an `IDENT` statement.
5. It may have ExecEngine rules to ensure that duplicates of already existing objects are detected and merged.
   For example, if data for a NatuurlijkPersoon is provided, a new NatuurlijkPersoon-object is created
   which may result in two such objects referring to the same (real-world) NatuurlijkPersoon)
6. It defines an `API` (as follows: API "<Concept>Data" CRud BOX", followed by the API contents)
7. It defines a `VIEW` that can be used in an `INTERFACE` to render a button that the user can push 
   in order to upload the corresponding data object credential.

## NOTES

- API files should NOT contain any rules or other stuff that has to do with validation.
- Specialization concepts may be included in an API for a more general concept. Examples:
  - [Organization.api](./Organization.api) and [Onderneming.api](./Onderneming.api), and
  - [DutchAddr.api](./DutchAddr.api) and [PhysicalAddr.api](./PhysicalAddr.api)