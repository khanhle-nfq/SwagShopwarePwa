<?php declare(strict_types=1);

namespace SwagVueStorefront\VueStorefront\PageLoader;

use Shopware\Storefront\Page\Product\ProductPageLoader as StorefrontProductPageLoader;
use SwagVueStorefront\VueStorefront\PageLoader\Context\PageLoaderContext;
use SwagVueStorefront\VueStorefront\PageResult\Product\ProductPageResult;
use SwagVueStorefront\VueStorefront\PageResult\Product\ProductPageResultHydrator;

/**
 * This class is a wrapper/proxy for the Shopware\Storefront\Page\Product\ProductPageLoader which is a part of the Shopware storefront bundle.
 * We don't want dependencies from this layer of the application, that's why there is this facade
 * Once composite page loading will be included in the Shopware core, this layer of abstraction becomes obsolete.
 * Otherwise it can serve as a structural reference for the implementation of the sales channel api.
 *
 * @package SwagVueStorefront\VueStorefront\PageLoader
 */
class ProductPageLoader implements PageLoaderInterface
{
    private const RESOURCE_TYPE = 'frontend.detail.page';

    /**
     * @var StorefrontProductPageLoader
     */
    private $productPageLoader;

    /**
     * @var ProductPageResultHydrator
     */
    private $resultHydrator;

    public function getResourceType(): string
    {
        return self::RESOURCE_TYPE;
    }

    public function __construct(StorefrontProductPageLoader $productPageLoader, ProductPageResultHydrator $resultHydrator)
    {
        $this->productPageLoader = $productPageLoader;
        $this->resultHydrator = $resultHydrator;
    }

    public function load(PageLoaderContext $pageLoaderContext): ProductPageResult
    {
        $pageLoaderContext->getRequest()->attributes->set('productId', $pageLoaderContext->getResourceIdentifier());

        $productPage = $this->productPageLoader->load($pageLoaderContext->getRequest(), $pageLoaderContext->getContext());

        return $this->resultHydrator->hydrate($pageLoaderContext, $productPage);
    }
}
