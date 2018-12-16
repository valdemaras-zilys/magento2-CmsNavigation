<?php

namespace Raguvis\CmsNavigation\Plugin\Block;

use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;

class Navigation
{
    /**
     * @var NodeFactory
     */
    protected $nodeFactory;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory
     */
    protected $pageCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $objectmanager
     */
    protected $storeManager;

    /**
     * @var \Magento\Cms\Helper\Page $cmsPageHelper
     */
    protected $cmsPageHelper;

    /**
     * @var string $currentPageIdentifier
     */
    protected $currentPageIdentifier;

    public function __construct(
        NodeFactory $nodeFactory,
        PageCollectionFactory $pageCollectionFactory,
        \Magento\Cms\Helper\Page $pageHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Page $page
    )
    {
        $this->nodeFactory = $nodeFactory;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->cmsPageHelper = $pageHelper;
        $this->currentPageIdentifier = $page->getIdentifier();
        $this->storeManager = $storeManager;
    }

    /**
     * Get collection of CMS pages with flag "Show in navigation"
     * and add to navigation tree
     *
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     */
    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    )
    {
        $pageCollection = $this->getPageCollection();
        foreach ($pageCollection as $page) {
            $pageArray = $this->getNodeAsArray($page);
            $this->addNode($subject, $pageArray);
        }
    }

    /**
     * Prepare and return array of pages to be included in top navigation
     *
     * @return \Magento\Cms\Model\ResourceModel\Page\Collection
     */
    protected function getPageCollection()
    {
        $collection = $this->pageCollectionFactory->create();
        $collection->addFieldToFilter('show_in_navigation', ['eq' => 1]);
        $collection->addFieldToFilter('is_active', ['eq' => 1]);
        $collection->addStoreFilter($this->storeManager->getStore()->getId());
        return $collection;
    }

    /**
     * Prepare node array from Page object
     *
     * @param \Magento\Cms\Model\Page $page
     * @return array
     */
    protected function getNodeAsArray($page)
    {
        return [
            'name' => __($page->getTitle()),
            'id' => $page->getId(),
            'url' => $this->cmsPageHelper->getPageUrl($page->getId()),
            'has_active' => false,
            'is_active' => ($page->getIdentifier() == $this->currentPageIdentifier)
        ];
    }

    /**
     * Add navigation node witch page details
     *
     *
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param array $pageArray
     */
    protected function addNode($subject, $pageArray)
    {
        $node = $this->nodeFactory->create(
            [
                'data' => $pageArray,
                'idField' => 'id',
                'tree' => $subject->getMenu()->getTree()
            ]
        );
        $subject->getMenu()->addChild($node);
    }
}
