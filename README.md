# Lodel Data Interoperability Bundle

This bundle is developed as part of the **CRAFT-OA Project (https://www.craft-oa.eu/)** funded by the European Union (**HORIZON-INFRA-2022-EOSC-01** Grant Agreement: 101094397). It aims to support interoperability and transformation workflows in scholarly publishing.

It introduces a transformation step that converts **JATS-Publishing** files into **TEI-Commons** using **Métopes XSLT** stylesheets.
The transformation is executed prior to the import and validation process in **Lodel 2.0**.

⚠️ **This bundle requires Lodel 2.0 (which may not yet be available)**. For more information, visit: [Lodel 2.0 Announcement](https://leo.hypotheses.org/22760) ⚠️

📽️ **Video Demonstration**: A demonstration video of this bundle is available here: [Nakala Platform](https://api.nakala.fr/data/10.34847/nkl.616471b2/d8ce9ca6f4e585bf251e4103163ebe5f3a9d4166).

_Last updated: June 17, 2025_

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

Then add the dependency:
```bash
$ composer require lodel/lodel-data-interoperability-bundle
```

Normally, the bundle will be automatically enabled and ready to use without requiring any additional configuration. However, if manual enabling is necessary, make sure the bundle is registered in your application as follows:

```bash
// config/bundles.php

return [
    // ...
    Lodel\DataInteroperabilityBundle\DataInteroperabilityBundle::class => ['all' => true],
];
```

## Defining transformations

### _Configuration_

Transformations must be configured in [```src/Resources/config/packages/lodel_data_interoperability.yaml```](src/Resources/config/packages/lodel_data_interoperability.yaml). Each transformation entry requires:

- A unique identifier (the key, e.g., ```jatsToTei```) used internally.
- A human-readable label (```label```) that describes the transformation.
- A list of XSLT ```files``` executed in order by Saxon. Each step processes the output of the previous one.

This structure allows chaining multiple transformation steps while keeping configuration centralized and explicit.

Example:
```bash
lodel_data_interoperability:
    (...)
    transformation:
        jatsToTei:
            label: JATS to TEI
            files:
                - jats_to_tei-1.xsl
                - jats_to_tei-2.xsl
```

This example defines a transformation named ```jatsToTei```, which converts JATS XML into TEI XML using two sequential XSLT files.

### _Stylesheets_

All XSLT files required for the transformation processes — including both the executable stylesheets (listed in the configuration) and any supporting/imported stylesheets — must be placed in the [```src/Resources/stylesheets/```](src/Resources/stylesheets/) directory. This ensures they are correctly located and accessible during Saxon processing.

## External resources

### _Saxon script_

The [```saxon-he-10.6.jar```](src/Resources/scripts/) file is part of the Saxon-HE product, which is distributed under the Mozilla Public License version 2.0.

## Defining transformations

### _Configuration_

Transformations must be configured in [```src/Resources/config/packages/lodel_data_interoperability.yaml```](src/Resources/config/packages/lodel_data_interoperability.yaml). Each transformation entry requires:

- A unique identifier (the key, e.g., ```jatsToTei```) used internally.
- A human-readable label (```label```) that describes the transformation.
- A list of XSLT ```files``` executed in order by Saxon. Each step processes the output of the previous one.

This structure allows chaining multiple transformation steps while keeping configuration centralized and explicit.

Example:
```bash
lodel_data_interoperability:
    (...)
    transformation:
        jatsToTei:
            label: JATS to TEI
            files:
                - jats_to_tei-1.xsl
                - jats_to_tei-2.xsl
```

This example defines a transformation named ```jatsToTei```, which converts JATS XML into TEI XML using two sequential XSLT files.

### _Stylesheets_

All XSLT files required for the transformation processes — including both the executable stylesheets (listed in the configuration) and any supporting/imported stylesheets — must be placed in the [```src/Resources/stylesheets/```](src/Resources/stylesheets/) directory. This ensures they are correctly located and accessible during Saxon processing.

## External resources

### _Saxon script_

The [```saxon-he-10.6.jar```](src/Resources/scripts/) file is part of the Saxon-HE product, which is distributed under the Mozilla Public License version 2.0.

## Documentation

This bundle uses Doxygen to generate code documentation.
Doxygen reads PHPDoc comments to produce browsable HTML documentation.
Ensure Doxygen is installed on your system. You can install it via:

```bash
$ apt-get install doxygen
```

Run the following command to generate the documentation:

```bash
$ doxygen Doxyfile
```

The generated HTML documentation will be available in ```docs/html/```.
Open ```index.html``` in a browser to view it.
If the code changes, rerun the above steps to update the documentation.

## Makefile Commands

This project includes a ```Makefile``` to simplify common development tasks.
Below are the available commands and their usage:

- ```make quality```: Runs PHP-CS-Fixer and PHPStan to ensure code quality and perform static analysis.  
- ```make security```: Uses Symfony's security check to check for known vulnerabilities in dependencies.
- ```make tests```: Executes all automated tests with PHPUnit to ensure that the application behaves as expected.

## Troubleshooting
### Common Issues

1. Java Not Installed: Ensure Java is installed and accessible in your environment. Verify by running:

```bash
$ java --version
```

2. Missing PHP Extensions: Install required PHP extensions:

```bash
$ apt-get install -y php-xml php-mbstring
```

## Contributors

This project has been developed at **OpenEdition Center**, a french CNRS Support and Research Unit (UAR 2504) associated with Aix-Marseille University, the EHESS and Avignon University.

For detailed copyright and license information, please refer to the [LICENSE](LICENSE) file that was distributed with this source code.

- **Edith Cannet** - IR Métopes - XSL transformations
- **João Martins**  - OpenEdition, Protisvalor - Lead Developer (05/2024-07/2025)
- **Dominique Roux** - IR Métopes - XSL transformations
- **Jean-Christophe Souplet** - OpenEdition - CRAFT-OA T4.2 Co-Task Leader and Lodel Data Interoperability Bundle Project Manager
- **Nicolas Vernot Cortes** - OpenEdition - Lodel 2.0 Lead Developer & Bundle Integration & Architecture
- **Dulip Withanage** - TIB - CRAFT-OA T4.2 Co-Task Leader
