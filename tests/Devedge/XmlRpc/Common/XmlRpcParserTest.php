<?php
namespace Devedge\XmlRpc\Common;

use Devedge\XmlRpc\Server;
use PHPUnit_Framework_TestCase;

class XmlRpcParserTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function testParseElementString()
    {
        $element = simplexml_load_string("<?xml version=\"1.0\"?>\n<string>1</string>\n");
        $this->assertEquals("1", XmlRpcParser::parseElement($element));
        $this->assertTrue(is_string(XmlRpcParser::parseElement($element)));
        $this->assertFalse(is_int(XmlRpcParser::parseElement($element)));
    }

    public function testParseElementBase64()
    {
        // we handle a base64 just like a regular string for now
        $element = simplexml_load_string("<?xml version=\"1.0\"?>\n<base64>1</base64>\n");
        $this->assertEquals("1", XmlRpcParser::parseElement($element));
        $this->assertTrue(is_string(XmlRpcParser::parseElement($element)));
        $this->assertFalse(is_int(XmlRpcParser::parseElement($element)));
    }

    public function testParseElementNil()
    {
        $element = simplexml_load_string("<?xml version=\"1.0\"?>\n<nil />\n");
        $this->assertTrue(is_null(XmlRpcParser::parseElement($element)));
    }

    public function testParseElementDouble()
    {
        $element = simplexml_load_string("<?xml version=\"1.0\"?>\n<double>1.01</double>\n");
        // float/real/double = same thing in php, so checking one is enough
        $this->assertTrue(is_double(XmlRpcParser::parseElement($element)));
        $this->assertEquals(1.01, XmlRpcParser::parseElement($element));
    }

    public function testParseElementBoolean()
    {
        $element = simplexml_load_string("<?xml version=\"1.0\"?>\n<boolean>1</boolean>\n");
        $this->assertTrue(is_bool(XmlRpcParser::parseElement($element)));
        $this->assertTrue(XmlRpcParser::parseElement($element));
    }

    public function testParseElementDateTime()
    {
        $element = simplexml_load_string("<?xml version=\"1.0\"?>\n<dateTime.iso8601>1980-07-15T00:00:00+0000</dateTime.iso8601>\n");
        $this->assertInstanceOf('\DateTime', XmlRpcParser::parseElement($element));
        $this->assertEquals(332467200, XmlRpcParser::parseElement($element)->getTimestamp());
    }

    public function testParseElementStruct()
    {
        $element = simplexml_load_string("<?xml version=\"1.0\"?>\n<struct><member><name>foo</name><value><string>bar</string></value></member><member><name>1</name><value><int>2</int></value></member></struct>\n");
        $this->assertTrue(is_array(XmlRpcParser::parseElement($element)));
        $this->assertEquals(["foo" => "bar", 1 => 2], XmlRpcParser::parseElement($element));
    }

    public function testParseElementArray()
    {
        $element = simplexml_load_string("<?xml version=\"1.0\"?>\n<array><data><value><string>foo</string></value><value><int>1</int></value></data></array>\n");
        $this->assertTrue(is_array(XmlRpcParser::parseElement($element)));
        $this->assertEquals(["foo", 1], XmlRpcParser::parseElement($element));
    }

    public function testParseElementInt()
    {
        $element = simplexml_load_string("<?xml version=\"1.0\"?>\n<int>1</int>\n");
        $this->assertEquals(1, XmlRpcParser::parseElement($element));
        $this->assertTrue(is_int(XmlRpcParser::parseElement($element)));
        $this->assertFalse(is_string(XmlRpcParser::parseElement($element)));


        $element = simplexml_load_string("<?xml version=\"1.0\"?>\n<i4>1</i4>\n");
        $this->assertEquals(1, XmlRpcParser::parseElement($element));
        $this->assertTrue(is_int(XmlRpcParser::parseElement($element)));
        $this->assertFalse(is_string(XmlRpcParser::parseElement($element)));
    }

    public function testParseParams()
    {
        $element = simplexml_load_string("<?xml version=\"1.0\"?>\n<params><param><value><string>foo</string></value></param><param><value><int>1</int></value></param></params>\n");
        $this->assertTrue(is_array(XmlRpcParser::parseParams($element)));
        $this->assertEquals(["foo", 1], XmlRpcParser::parseParams($element));
    }
}
