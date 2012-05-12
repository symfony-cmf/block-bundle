# Symfony Cmf Block Bundle
## What is this?
This Bundle is part of the [Symfony CMF](http://cmf.symfony.com/). It assists you in managing fragments of contents, so called blocks. What the SymfonyCmfBlockBundle does is similar to what Twig does, but for blocks that are persisted in a DB. Thus the blocks can be made editable for an editor. Also the SymfonyCmfBlockBundle provides the logic to determine which block should be rendered on which pages.

## What is this not?
The SymfonyCmfBlockBundle does not provide an editing functionality for blocks itself. However, you can find examples on how making blocks editable in the [Symfony CMF Sandbox](https://github.com/symfony-cmf/cmf-sandbox).

## Usage
### Installation / Dependencies

Dependencies of the SymfonyCmfBlockBundle are managed by [Composer](https://github.com/composer/composer), so if you use Composer, you just have to add a requirement for ```symfony-cmf/block-bundle``` to your composer.json and run the composer installer. Otherwise check ```composer.json``` and add the required dependencies to your ```deps``` file.

After this, instantiate the bundle in your ```AppKernel.php```

    new Symfony\Cmf\Bundle\BlockBundle\SymfonyCmfBlockBundle()

Since the SymfonyCmfBlockBundle extends the SonataBlockBundle (see [Technical details](#technical-details) for further information), also add this to your ```AppKernel.php```

    new Sonata\BlockBundle\SonataBlockBundle()

### Render your first block
### Block types
### Create your own block

## Examples
You can find example usages of this bundle in the [Symfony CMF Sandbox](https://github.com/symfony-cmf/cmf-sandbox). Have a look at the BlockBundle in the Sandbox. It also shows you how to make blocks editable using the [LiipVieBundle](https://github.com/liip/LiipVieBundle).

## Technical details
### Sonata Block Bundle
### Tests
