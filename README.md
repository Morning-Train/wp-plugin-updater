# Morningtrain\WP\PluginUpdater

Make it possible to update a plugin through a private plugin repo.

## Table of Contents

- [Introduction](#introduction)
- [Getting Started](#getting-started)
    - [Installation](#installation)
- [Usage](#usage)
    - [Initialize](#initialize)
- [Contributing](#contributing)
- [Contributors](#contributors)
- [License](#license)

## Introduction

Make it possible to update a plugin through a private plugin repo.

## Getting Started

To get started install the package as described below in [Installation](#installation).

To use the tool have a look at [Usage](#usage)

### Installation

Install with composer

```bash
composer require morningtrain/wp-plugin-updater
```

## Usage

## Getting started

### Initialize

Construct at new `Morningtrain\WP\PluginUpdater\PluginUpdater` with url to the private plugin repo json info and plugin information (slug, base name and plugin version).

```php
// plugin.php
require __DIR__ . "/vendor/autoload.php";

new \Morningtrain\WP\PluginUpdater\PluginUpdater(
    '[PLUGIN_INFO_JSON_URL]',
    '[PLUGIN_SLUG]',
    '[PLUGIN_BASE_NAME]',
    '[PLUGIN_VERSION]'
)
```

## Contributing

Thank you for your interest in contributing to the project.

### Bug Report

If you found a bug, we encourage you to make a pull request.

To add a bug report, create a new issue. Please remember to add a telling title, detailed description and how to reproduce the problem.

### Support Questions

We do not provide support for this package.

### Pull Requests

1. Fork the Project
2. Create your Feature Branch (git checkout -b feature/AmazingFeature)
3. Commit your Changes (git commit -m 'Add some AmazingFeature')
4. Push to the Branch (git push origin feature/AmazingFeature)
5. Open a Pull Request

## Contributors

- [Martin Schadegg Brønniche](https://github.com/mschadegg)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.


---

<div align="center">
Developed by <br>
</div>
<br>
<div align="center">
<a href="https://morningtrain.dk" target="_blank">
<img src="https://morningtrain.dk/wp-content/themes/mtt-wordpress-theme/assets/img/logo-only-text.svg" width="200" alt="Morningtrain logo">
</a>
</div>
