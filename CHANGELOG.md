Changelog
=========

* **2013-07-16**: [Model] Adopted persistance standard model, see: http://symfony.com/doc/master/cmf/contributing/bundles.html#Persistence.

  To migrate adapt the following script. Run it once for each document class,
  replacing <documentClass> with `ActionBlock`, `ContainerBlock`,
  `ImagineBlock`, `MultilangImagineBlock`, `MultilangSimpleBlock`,
  `MultilangSlideshowBlock`, `ReferenceBlock`, `RssBlock`, `SimpleBlock`,
  `SlideshowBlock` and `StringBlock` respectively:

    $ php app/console doctrine:phpcr:nodes:update \
        --query="SELECT * FROM [nt:unstructured] WHERE [phpcr:class] = \"Symfony\\Cmf\\Bundle\\BlockBundle\\Document\\<documentClass>\"" \
        --set-prop=phpcr:class="Symfony\\Cmf\\Bundle\\BlockBundle\\Doctrine\\Phpcr\\<documentClass>"

1.0.0-beta3
-----------

* **2013-06-21**: [ActionBlock] ActionBlock got the method resolveRequestParams.
  If you want the locale to be propagated, update your blocks to have `_locale`
  in the $requestParams. This is done automatically for new ActionBlock
  instances. You can also configure custom things to pass on to the sub request.
* **2013-05-30**: [DependencyInjection] Renamed config item `document_manager_name` to `manager_name`
* **2013-05-23**: Dropped Symfony 2.1 support
* **2013-05-23**: [Block] Implemented SonataBlockBundle BlockContext

1.0.0-alpha3
------------

[earlier commits before creation of Changelog]
