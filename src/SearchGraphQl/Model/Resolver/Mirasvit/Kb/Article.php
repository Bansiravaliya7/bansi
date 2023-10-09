<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-ultimate
 * @version   2.2.19
 * @copyright Copyright (C) 2023 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\SearchGraphQl\Model\Resolver\Mirasvit\Kb;

use Magento\CmsGraphQl\Model\Resolver\DataProvider\Page as PageDataProvider;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Article implements ResolverInterface
{
    private $pageDataProvider;

    private $size = 0;

    public function __construct(
        PageDataProvider $pageDataProvider
    ) {
        $this->pageDataProvider = $pageDataProvider;
    }

    public function resolve(
        Field       $field,
                    $context,
        ResolveInfo $info,
        array       $value = null,
        array       $args = null
    ) {
        $result = $value[$field->getName()] ?? null;
        if (!$result) {
            return null;
        }

        $collection = $result['instance']->getSearchCollection();
        $collection->setPageSize($args['pageSize'])->setCurPage($args['currentPage']);

        $items = [];
        foreach ($collection as $item) {
            $items[] = ['name' => $item->getName(), 'url' => $item->getUrl()];
        }

        $totalCount = $collection->getSize();

        return [
            ...$result,
            'items'       => $items,
            'total_count' => $totalCount,
            'page_info'   => [
                'total_pages'  => ceil($totalCount / $args['pageSize']),
                'page_size'    => $args['pageSize'],
                'current_page' => $args['currentPage'],
            ],
        ];
    }
}