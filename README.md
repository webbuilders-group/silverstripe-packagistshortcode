Packagist Short Code
=================
Add a short code for adding a Packagist installs button with a count to a HTMLText field.

## Maintainer Contact
* Ed Chipman ([UndefinedOffset](https://github.com/UndefinedOffset))

## Requirements
* SilverStripe CMS ^4|^5|^6


## Installation
__Composer (recommended):__
```
composer require webbuilders-group/silverstripe-packagistshortcode
```


If you prefer you may also install manually:
* Download the module from here https://github.com/webbuilders-group/silverstripe-packagistshortcode/archive/master.zip
* Extract the downloaded archive into your site root so that the destination folder is called githubshortcode, opening the extracted folder should contain _config.php in the root along with other files/folders
* Run dev/build?flush=all to regenerate the manifest


## Usage
Usage is pretty straight forward to add a packagist downloads button you simply add the following:
```
[packagist package="package owner/package name"]
```

Optionally you may add mode="monthly" or button="daily" (defaults to total) to show the download count for the given period.
```
[packagist package="package owner/package name" mode="monthly"]

```

In 3.1 the short codes above will work as included however the updated syntax for the short code would be (of course layout and button are not required):
```
[packagist,package="package owner/package name",mode="monthly"]
```


#### Configuration Options
There are a few configuration options available to you:

```yml
SilverStripe\Core\Injector\Injector:
    Psr\SimpleCache\CacheInterface.PackagistShortCode:
        factory: SilverStripe\Core\Cache\CacheFactory
        constructor:
            namespace: "NewRelic"
            defaultLifetime: 86400 #Cache time in seconds
WebbuildersGroup\PackagistShortCode\PackagistShortCode:
    UseShortHandNumbers: true #Use short hand numbers i.e 5.6K or not
```
