# Installation Guide

## Requirements

Before installing this bundle, ensure your environment meets the following requirements:

- **PHP**: Version 8.1 or higher.
- **PHP Extensions**: ```dom```, ```mbstring```, ```xml```, ```xmlwriter```
- **Composer**: To manage dependencies.
- **Java**: OpenJDK 11 or higher, required for running XSLT transformations with Saxon.

## Installation

From the root directory of your Lodel application, declare the repository source:
```bash
$ composer config repositories.lodel/lodel-data-interoperability-bundle vcs https://github.com/operas-eu/lodel-data-interoperability-bundle.git
```

Then add the specific version tag vX.X.X:
```bash
$ composer require lodel/lodel-data-interoperability-bundle:vX.X.X
```

> Make sure:
> - The tag vX.X.X exists in the GitHub repo.
> - The composer.json in that version contains the correct name:
> ```json
> "name": "lodel/lodel-data-interoperability-bundle"
> ```

Normally, the bundle will be automatically enabled and ready to use without requiring any additional configuration. However, if manual enabling is necessary, make sure the bundle is registered in your application as follows:

```php
// config/bundles.php

return [
    // ...
    Lodel\DataInteroperabilityBundle\DataInteroperabilityBundle::class => ['all' => true],
];
```

## Register routes

In order to enable the routes provided by this bundle, you need to explicitly import them in your Lodel application's routing configuration. This is required because the bundle does not use automatic route import.

Create the file ```config/routes/lodel_data_interoperability.yaml``` in your application with the following content:

```yaml
_lodel_data_interoperability:
    resource: '@DataInteroperabilityBundle/Resources/config/routing.yaml'
```

## Defining transformations

### _Configuration_

Transformations must be configured in [```src/Resources/config/packages/lodel_data_interoperability.yaml```](https://github.com/operas-eu/lodel-data-interoperability-bundle/blob/main/src/Resources/config/packages/lodel_data_interoperability.yaml). Each transformation entry requires:

- A unique identifier (the key, e.g., ```jatsToTei```) used internally.
- A human-readable label (```label```) that describes the transformation.
- An operation type (```operation```) indicating whether the transformation is for **import** or **export**.
- A list of XSLT ```files``` executed in order by Saxon. Each step processes the output of the previous one.

This structure allows chaining multiple transformation steps while keeping configuration centralized and explicit.

Example:
```yaml
lodel_data_interoperability:
    (...)
    transformation:
        jatsToTei:
            label: JATS to TEI
            operation: import
            files:
                - jats_to_tei-1.xsl
                - jats_to_tei-2.xsl
        teiToJats:
            label: TEI to JATS
            operation: export
            files:
                - tei_to_jats.xsl
```

This example defines two transformations:
- **jatsToTei**, which converts JATS XML into TEI XML using two sequential XSLT files for the import process,
- **teiToJats**, which converts TEI XML back into JATS XML using a single XSLT for the export process.

### _Stylesheets_

All XSLT files required for the transformation processes — including both the executable stylesheets (listed in the configuration) and any supporting/imported stylesheets — must be placed in the [```src/Resources/stylesheets/```](https://github.com/operas-eu/lodel-data-interoperability-bundle/blob/main/src/Resources/stylesheets/) directory. This ensures they are correctly located and accessible during Saxon processing.

## JATS Export – Optional Twig Integration

If you want to add JATS export buttons directly in your content view templates, you can include the following line:

```php
{% include '@DataInteroperabilityBundle/jats_export_buttons.html.twig' ignore missing %}
```

for example in:

```
vendor/lodel/lodel/src/Lodel/Bundle/CoreBundle/Resources/views/site/templates/views/_body.html.twig
```

Place it wherever you want the export buttons to appear (e.g. at the end of the content body).

This will display two buttons:
- "View JATS" – to preview the JATS XML
- "Download JATS" – to download the JATS file

Optional: This is not required for JATS export to work — use it only if you want to offer users quick access to the export directly in the interface.

After uninstalling the bundle, this line becomes inactive but harmless.
You may leave it as is, or remove it manually if you want to keep your templates tidy.

## External resources

### _Saxon script_

The [```saxon-he-10.6.jar```](https://github.com/operas-eu/lodel-data-interoperability-bundle/blob/main/src/Resources/scripts/) file is part of the Saxon-HE product, which is distributed under the Mozilla Public License version 2.0.
