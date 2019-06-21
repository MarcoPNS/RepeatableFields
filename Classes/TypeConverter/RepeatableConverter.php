<?php
namespace Mireo\RepeatableFields\TypeConverter;

use Mireo\RepeatableFields\Model\Repeatable;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Property\PropertyMapper;
use Neos\Flow\Property\PropertyMappingConfigurationInterface;
use Neos\Flow\Property\TypeConverter\AbstractTypeConverter;
use Neos\ContentRepository\Exception\NodeException;

/**
 * An Object Converter for Nodes which can be used for routing (but also for other
 * purposes) as a plugin for the Property Mapper.
 *
 * @Flow\Scope("singleton")
 */
class RepeatableConverter extends AbstractTypeConverter
{
    /**
     * @Flow\Inject
     * @var PropertyMapper
     */
    protected $propertyMapper;

    /**
     * @var array
     */
    protected $sourceTypes = ['repeatable'];

    /**
     * @var string
     */
    protected $targetType = Repeatable::class;

    /**
     * @var integer
     */
    protected $priority = 1;

    /**
     * @param string|array $source Either a string or array containing the absolute context node path which identifies the node. For example "/sites/mysitecom/homepage/about@user-admin"
     * @param string $targetType not used
     * @param array $subProperties not used
     * @param PropertyMappingConfigurationInterface $configuration
     * @return mixed An object or \Neos\Error\Messages\Error if the input format is not supported or could not be converted for other reasons
     * @throws
     */
    public function convertFrom($source, $targetType, array $subProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {

        $fieldsDeclaration = $configuration->getConfigurationValue('Mireo\RepeatableFields\TypeConverter\RepeatableConverter', 'fieldsDeclaration');

        $repeatable = new Repeatable($source, $fieldsDeclaration);
        $repeatable->injectPropertyMapper($this->propertyMapper);
        $repeatable->initialize();

        return $repeatable;
    }

}
