Changelog
=========

1.2.0-RC1
---------

* **2014-06-06**: Updated to PSR-4 autoloading

1.1.0
-----

Release 1.1.0

1.1.0-RC2
---------

* **2014-04-11**: drop Symfony 2.2 compatibility

1.1.0-RC1
---------

* **2014-03-24**: Blocks now support the ChildExtension to simplify attaching blocks
  in Sonata Admin. Activate the ChildExtension from the CoreBundle to profit from this.

* **2014-03-24**: [Dependencies]: Updated to the new SonataBlockBundle that
  requires the SonataCoreBundle.

* **2014-03-23**: Added a Repository Initializer for the block basepath

1.0.0-RC4
---------

* **2013-10-04**: [Sonata Admin]: Removed overwritten baseRouteName and
  baseRoutePattern in the block admins. The names now follow the convention,
  becoming cmf_block_actionblock instead of cmf_block_action and so on.

1.0.0-RC3
---------

* **2013-09-25**: Renamed `divisibleBy`, `divisibleClass` and `childClass` ContainerBlock
  options to `divisible_by`, `divisible_class` and `child_class`

1.0.0-RC2
---------

* **2013-08-31**: [Templating] The embed blocks filter defaults have changed
  to be safer in combination with wysiwyg editors.
  You can either adjust your content to match ``%embed-block|block-identifier|end%``
  or (not recommended) configure `twig.cmf_embed_blocks.prefix` and `.postfix`
  to match the previous values `<span>%embed-block:"` and `"%</span>`.
* **2013-08-31**: [Configuration] The persistence.phpcr.content_basepath was
  removed. It was used inconsistently with block_basepath. One is enough. If
  you configure content_basepath, remove it from the configuration, if you
  relied on CoreBundle to configure this for us, just make sure to update
  CoreBundle as well.

1.0.0-RC1
---------

* **2013-08-15**: [ImagineBlock] changed the template block_imagine.html.twig
  to pass the image id property to the imagine_filter instead of the image
  object.
* **2013-08-08**: [Model] Removed every Multilang models and implement TranslatableInterface instead to fit CMF standards.
* **2013-08-08**: [Admin] Added explicit base route name / patterns to fix broken schema. `cmf_bundle_action` becomes `cmf_block_action`.
* **2013-08-08**: [PublishWorkflow] AbstractBlock now implements the publish
  workflow and the PHPCRBlockLoader expects a security context to check if
  blocks are published. If a block is not published, the same behaviour as when
  the block is not found is used (depending on the configuration either
  returning an EmptyBlock or throwing an exception).

1.0.0-beta3
-----------

* **2013-08-01**: [DependencyInjection] moved phpcr specific configuration under ``persistence.phpcr`` and added ``enabled`` flag.
* **2013-08-01**: [Model] Updated content to body property for ``SimpleBlock``, ``MultilangSimpleBlock`` and ``StringBlock``.

  To migrate adapt the following script. Run it once for each document class,
  replacing DOCUMENT_CLASS with `SimpleBlock`, `MultilangSimpleBlock`,
  `StringBlock` respectively:

    $ php app/console doctrine:phpcr:nodes:update \
        --query="SELECT * FROM [nt:unstructured] WHERE [phpcr:class] = \"Symfony\\Cmf\\Bundle\\BlockBundle\\Doctrine\\Phpcr\\DOCUMENT_CLASS\"" \
        --apply-closure="\$node->setProperty('body', \$node->getPropertyValue('content'));"

    $ php app/console doctrine:phpcr:nodes:update \
        --query="SELECT * FROM [nt:unstructured] WHERE [phpcr:class] = \"Symfony\\Cmf\\Bundle\\BlockBundle\\Doctrine\\Phpcr\\DOCUMENT_CLASS\"" \
        --remove-prop=content

* **2013-08-01**: [Model] Adopted persistance standard model, see: http://symfony.com/doc/master/cmf/contributing/bundles.html#Persistence.

  The PHPCR-ODM will now not be automatically loaded but only when
  `persistence.phpcr.enabled` is set to true.

  To migrate adapt the following script. Run it once for each document class,
  replacing DOCUMENT_CLASS with `ActionBlock`, `ContainerBlock`,
  `ImagineBlock`, `MultilangImagineBlock`, `MultilangSimpleBlock`,
  `MultilangSlideshowBlock`, `ReferenceBlock`, `RssBlock`, `SimpleBlock`,
  `SlideshowBlock` and `StringBlock` respectively:

    $ php app/console doctrine:phpcr:nodes:update \
        --query="SELECT * FROM [nt:unstructured] WHERE [phpcr:class] = \"Symfony\\Cmf\\Bundle\\BlockBundle\\Document\\DOCUMENT_CLASS\"" \
        --set-prop=phpcr:class="Symfony\\Cmf\\Bundle\\BlockBundle\\Doctrine\\Phpcr\\DOCUMENT_CLASS"

1.0.0-beta2
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
