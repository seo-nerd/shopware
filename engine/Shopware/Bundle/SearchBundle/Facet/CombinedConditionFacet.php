<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Bundle\SearchBundle\Facet;

use Shopware\Search\ConditionInterface;
use Shopware\Search\FacetInterface;
use Shopware\Components\ReflectionHelper;

class CombinedConditionFacet implements FacetInterface
{
    /**
     * @var \Shopware\Search\ConditionInterface[]
     */
    private $conditions;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $requestParameter;

    /**
     * @param string|array $conditions
     * @param string       $label
     * @param string       $requestParameter
     */
    public function __construct($conditions, $label, $requestParameter)
    {
        if (is_array($conditions)) {
            $this->conditions = $conditions;
        } else {
            $this->conditions = $this->unserialize(json_decode($conditions, true));
        }
        $this->label = $label;
        $this->requestParameter = $requestParameter;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'combined_facet_' . md5(json_encode($this->conditions));
    }

    /**
     * @return \Shopware\Search\ConditionInterface[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getRequestParameter()
    {
        return $this->requestParameter;
    }

    /**
     * @param array $serialized
     *
     * @return \Shopware\Search\ConditionInterface[]
     */
    private function unserialize($serialized)
    {
        $reflector = new ReflectionHelper();
        if (empty($serialized)) {
            return [];
        }
        $sortings = [];
        foreach ($serialized as $className => $arguments) {
            $className = explode('|', $className);
            $className = $className[0];
            $sortings[] = $reflector->createInstanceFromNamedArguments($className, $arguments);
        }

        return $sortings;
    }
}
