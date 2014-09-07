<?php
namespace Devedge\XmlRpc\Common;

class XmlRpcParser
{

    /**
     * @param \SimpleXMLElement $element
     * @return array|bool|\DateTime|float|string
     */
    public static function parseElement(\SimpleXMLElement $element)
    {
        switch ($element->getName()) {
            case "i4":
            case "int":
                return (int)((string)$element);
                break;
            case "dateTime.iso8601":
                return new \DateTime((string)$element);
                break;
            case "double":
                return (double)((string)$element);
                break;
            case "boolean":
                return (boolean)((string)$element);
                break;
            case "struct":
                return static::parseStruct($element);
                break;
            case "array":
                return static::parseArray($element);
                break;
            case "nil":
                // nil is an extension, not standard
                return null;
                break;
            case "base64":
            case "string":
            default:
                return (string)$element;
        }
    }

    /**
     * @param \SimpleXmlElement $element
     * @return array
     */
    public static function parseStruct(\SimpleXmlElement $element)
    {
        $struct = [];
        /** @var \SimpleXmlElement $member */
        foreach ($element->children() as $member) {
            // the special string-has-possibly-no-element-around-it-case
            if ($member->value->count() == 0) {
                $struct[(string)$member->name] = (string)$member->value;
                continue;
            }

            // traversable abuse once again (see array), as we don't have another method of getting
            // the only child directly if we don't know the name, and while it supports traversable, the array
            // access to it does something entirely different (attributes)
            // so this should actually be one iteration if the xml string is valid.
            foreach($member->value->children() as $child) {
                $struct[(string)$member->name] = self::parseElement($child);
            }

        }

        return $struct;
    }

    /**
     * @param \SimpleXmlElement $element
     * @return array
     */
    public static function parseArray(\SimpleXmlElement $element)
    {
        /** @var \SimpleXmlElement $data */
        $data = $element->data;

        $list = [];

        /** @var \SimpleXmlElement $value */
        foreach ($data->children() as $value) {

            // if there is no child, we treat this as a string (xml-rpc strings don't need to be marked)
            if ($value->count() == 0) {
                $list[] = (string)$value;
                continue;
            }
            // value should only have one child, but as children() returns an traversable element..
            foreach ($value->children() as $child) {
                // so this should actually only be called once, if the xml is valid
                $list[] = static::parseElement($child);
            }
        }

        return $list;
    }

    /**
     * @param \SimpleXMLElement $params
     * @return array
     */
    public static function parseParams(\SimpleXMLElement $params)
    {
        $return = [];
        foreach($params->children() as $param)
        {

            if ($param->value->count() == 0) {
                $return[] = (string) $param->value;
            }

            foreach($param->value->children() as $child)
            {
                $return[] = XmlRpcParser::parseElement($child);
            }
        }
        return $return;
    }
}
