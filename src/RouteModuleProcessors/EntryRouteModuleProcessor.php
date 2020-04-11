<?php
namespace PoP\RESTAPI\RouteModuleProcessors;

use PoP\ModuleRouting\AbstractEntryRouteModuleProcessor;
use PoP\ComponentModel\State\ApplicationState;
use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\API\Facades\FieldQueryConvertorFacade;
use PoP\Routing\RouteNatures;
use PoP\RESTAPI\DataStructureFormatters\RESTDataStructureFormatter;
use PoP\Engine\ModuleProcessors\RootRelationalFieldDataloadModuleProcessor;

class EntryRouteModuleProcessor extends AbstractEntryRouteModuleProcessor
{
    private static $restFieldsQuery;
    private static $restFields;
    public static function getRESTFields(): array
    {
        if (is_null(self::$restFields)) {
            self::$restFields = self::getRESTFieldsQuery();
            if (is_string(self::$restFields)) {
                self::$restFields = FieldQueryConvertorFacade::getInstance()->convertAPIQuery(self::$restFields);
            }
        }
        return self::$restFields;
    }
    public static function getRESTFieldsQuery(): string
    {
        if (is_null(self::$restFieldsQuery)) {
            self::$restFieldsQuery = (string) HooksAPIFacade::getInstance()->applyFilters(
                'Root:RESTFields',
                'fullSchema'
            );
        }
        return self::$restFieldsQuery;
    }
    public function getModulesVarsPropertiesByNature()
    {
        $ret = array();

        $vars = ApplicationState::getVars();
        $ret[RouteNatures::HOME][] = [
            'module' => [RootRelationalFieldDataloadModuleProcessor::class, RootRelationalFieldDataloadModuleProcessor::MODULE_DATALOAD_RELATIONALFIELDS_ROOT, ['fields' => isset($vars['query']) ? $vars['query'] : self::getRESTFields()]],
            'conditions' => [
                'scheme' => POP_SCHEME_API,
                'datastructure' => RESTDataStructureFormatter::getName(),
            ],
        ];

        return $ret;
    }
}
