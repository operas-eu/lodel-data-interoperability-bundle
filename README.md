# Lodel Data Interoperability Bundle

This bundle is developed as part of the **CRAFT-OA Project (https://www.craft-oa.eu/)** funded by the European Union (**HORIZON-INFRA-2022-EOSC-01** Grant Agreement: 101094397). It aims to support interoperability and transformation workflows in scholarly publishing.

It introduces a transformation step that converts **JATS-Publishing** files into **TEI-Commons** using **Métopes XSLT** stylesheets.
The transformation is executed prior to the import and validation process in **Lodel 2.0**.

⚠️ **This bundle requires Lodel 2.0 (which may not yet be available)**. For more information, visit: [Lodel 2.0 Announcement](https://leo.hypotheses.org/22760) ⚠️

📽️ **Video Demonstration**: A demonstration video of this bundle is available here: [Nakala Platform](https://api.nakala.fr/embed/10.34847/nkl.4a0ab841/340eddb033b6ab933348a3842ba5f34eb34b7930).

_Last updated: December 17, 2024_

## External resources

- [Saxon script](src/Resources/scripts/): The ```saxon-he-10.6.jar``` file is part of the Saxon-HE product, which is distributed under the Mozilla Public License version 2.0.
- [Métopes stylesheets](src/Resources/stylesheets/jatsToTei): The required stylesheets for this project are available at [Métopes Git Repository](https://git.unicaen.fr/metopes/xxe-addons/-/tree/master/metopes_tei/xxe/jats/jats_2_commons?ref_type=heads). Please download the stylesheets and place them in the ```src/Resources/stylesheets/jatsToTei``` directory.

## Requirements

Before installing this bundle, ensure your environment meets the following requirements:

- **PHP**: Version 8.1 or higher.
- **PHP Extensions**: ```dom```, ```mbstring```, ```xml```, ```xmlwriter```
- **Composer**: To manage dependencies.
- **Java**: OpenJDK 11 or higher, required for running XSLT transformations with Saxon.

## Installation

From the root directory of your Lodel application, declare the repository source:
```bash
$ composer config repositories.lodel/lodel-interoperability-bundle vcs git@gitlab.huma-num.fr:openedition/lodel/lodel-interoperability-bundle.git
```

Then add the dependency:
```bash
$ composer require lodel/lodel-interoperability-bundle:dev-main
```

The bundle should be automatically usable without requiring additional configuration.

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
