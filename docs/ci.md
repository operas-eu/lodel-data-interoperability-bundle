## Continuous Integration

This project uses [GitHub Actions](https://github.com/features/actions) for continuous integration.

Each commit and pull request triggers an automated workflow that checks code formatting, performs static analysis, and validates YAML configuration files

This helps ensure code quality and prevents regressions early in the development process.

See the [ci.yaml](https://github.com/operas-eu/lodel-data-interoperability-bundle/blob/main/.github/workflows/ci.yaml) file for details.

⚠️ To allow the CI to run successfully even though Lodel 2.0 is not yet publicly available, the workflow includes temporary blocks and placeholder code. These parts are clearly marked within the CI script and will need to be removed once Lodel 2.0 is released and installable via Composer.
