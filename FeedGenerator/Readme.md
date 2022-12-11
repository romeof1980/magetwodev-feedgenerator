- an interface should be used for entity generation and implemented by the generator classes

## analysys
- check if magento already provides such functionality
- if not for the product csv export the default magento product export function could be re-used
see: Magento_CatalogImportExport and in particular: vendor/magento/module-catalog-import-export/Model/Export/Product/Type

## steps
- skeleton via pestle
- cmd using vendor/magento/module-catalog/Console/Command/ProductAttributesCleanUp.php as a reference
- this module's Registry namespace handles some performance improvements: need refinements but gives a clue of tthe main scope.
credits: https://github.com/run-as-root/Magento-2-Google-Shopping-Feed/blob/main/Registry/FeedRegistry.php
-

## important refinements (requirements)
- cache the product collection based on condition (leverage m2 caching system)

## possible refinements
- crons (and their own crontab)
- the cmd should output and accept a list of vendors and store to allow the user could choose which one to generate
- feed generation (cmd) could implement the process bar
- config checks if enabled for scope "website": this is probably the only case used here but could be improved (store).
- if we'd like to support backend building for feed, this is a good "use case" example: https://magefan.com/magento-2-google-shopping-feed-extension/configuration
- a priming to have a look at the possibility of an extension could be this: https://github.com/run-as-root/Magento-2-Google-Shopping-Feed

### working hours
20221206 free thinking analysis while driving (0,5hr)
20221207 15-17 (2hrs)
20221209 18:30-21:00 (2,5hrs)
20221210 18:30-21:30 (3 hrs)
20221211 9:45-11:45 (2hrs)
20221211 16:15-

## todos:
- IMPORTANT: implement cache for product collection
- put module in vendor, create repo, create appropriate tag, push to repo (bitbucket)
- implement brand logic (now returns "brand" if not present). see: MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\BrandProvider

## todos - IN PROGRESS
-

## stand-by (needed?)
- MageTwoDev/FeedGenerator/Model/GenerateProductFeedForStore.php : implement for csv and not for xml
- 

## todos done (to be tested):
- implement ProductToFeedAttributesRowMapper (see MageTwoDev\FeedGenerator\Model\GenerateProductFeedForStore)
- see how magento core use csv export (see: Magento_CatalogImportExport and in particular:
  vendor/magento/module-catalog-import-export/Model/Export/Product/Type)
- IMPORTANT: IMPLEMENT MageTwoDev\FeedGenerator\Converter\ArrayToCsvConverter
- 

## installation
- access the repo and install via composer
- app/code if you do not have access to the repo for compsoer installation

## usage
- instructions:
```
bin/magento magetwodev:feedgenerator:products:generategooglesh
```
- specify store to generate the feed (here: store code == "en"):
```
bin/magento magetwodev:feedgenerator:products:generategooglesh --store="en"
```
- where to find the file (here for the store feed generated for store with code == "en"):
```
ls www/pub/media/var/feed/en_feed_googleshopping.csv
```
