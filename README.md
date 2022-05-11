Contao mobile.de Ads Bundle
=======================

[![](https://img.shields.io/packagist/v/numero2/contao-mobilede-ads.svg?style=flat-square)](https://packagist.org/packages/numero2/contao-mobilede-ads) [![](https://img.shields.io/badge/License-LGPL%20v3-blue.svg?style=flat-square)](http://www.gnu.org/licenses/lgpl-3.0)

About
--

Imports ads from mobile.de.
Currently this does not provide any backend or frontend modules to interact with.

System requirements
--

* [Contao 4](https://github.com/contao/contao)

Installation
--

* Install via Contao Manager or Composer (`composer require numero2/contao-mobilede-ads`)
* Run a database update via the Contao-Installtool or using the [contao:migrate](https://docs.contao.org/dev/reference/commands/) command.

Configuration
--

Please add your credentials in the global `parameters.yml`:

```yml
parameters:
    ...
    mobile.ads_username: 'username'
    mobile.ads_password: 'password'
```

Usage
--
You can either start the import via `System › Maintenance › Import ads from mobile.de` or the command for the Contao console `contao:mobileads:import`.

At the start of the import all Ads in the Contao database will be set to `published = ''` and all active ads found on mobile.de will then be published again.

Helper
--
Functions added to helper converting the data:
- `numero2\MobileDeAdsBundle\Util\DataUtil::wikitext2Html($text);` will convert the wikitext stored in `description_enriched` to html markup
- `numero2\MobileDeAdsBundle\Util\DataUtil::convertKwToPS($kw);` will convert the kw to ps
