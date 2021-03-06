<?php
/**
 * Wibble
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/wibble/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/wibble/blob/master/LICENSE New BSD License
 */

/**
 * @namespace
 */
namespace WibbleTest;
use Wibble;

class FragmentTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->fragment = '<div>a</div><div>b</div>';
        $this->xml = '<root><div>a</div><div>b</div></root>';
    }
    
    /**
     * Helpers
     */
    protected function getInnerHTML(\DOMNode $node)
    {
        $dom = new \DOMDocument;
        $dom->preserveWhitespace = false;
        $dom->formatOutput = false;
        $children = $node->childNodes;
        foreach ($children as $child) {
            $dom->appendChild($dom->importNode($child, true));
        }
        return trim($dom->saveHTML());
    }
    
    protected function getInnerHTMLFrom(\DOMNode $node, $path) {
        if ($node instanceof \DOMDocument) {
            $rootDoc = $node;
        } else {
            $rootDoc = $node->ownerDocument;
        }
        $xpath = new \DOMXPath($rootDoc);
        $result = $xpath->query($path);
        if ($result->length > 0) {
            return $this->getInnerHTML($result->item(0));
        }
    }
    
    /**
     * Tests
     */
    public function testBasicFragmentWithTidy()
    {
        if (!class_exists('\tidy', false)) $this->markTestSkipped('Tidy unavailable');
        $doc = new Wibble\HTML\Fragment($this->fragment);
        $this->assertEquals($this->fragment, str_replace("\n",'',$doc->toString()));
    }
    
    public function testBasicFragmentWithoutTidy()
    {
        $doc = new Wibble\HTML\Fragment($this->fragment, array('disable_tidy'=>true));
        $this->assertEquals($this->fragment, str_replace("\n",'',$doc->toString()));
    }
    
    public function testBasicHTMLOutputWithTidy()
    {
        if (!class_exists('\tidy', false)) $this->markTestSkipped('Tidy unavailable');
        $options = array(
            'doctype' => Wibble\HTML\Document::HTML4_TRANSITIONAL
        );
        $doc = new Wibble\HTML\Fragment(
            '<br>foo',
            $options
        );
        $this->assertRegExp('/<br>foo/', str_replace("\n",'',$doc->toString()));
    }
    
    public function testBasicXHTMLOutputWithTidy()
    {
        if (!class_exists('\tidy', false)) $this->markTestSkipped('Tidy unavailable');
        $options = array(
            'doctype' => Wibble\HTML\Document::XHTML1_STRICT
        );
        $doc = new Wibble\HTML\Fragment(
            '<br>foo',
            $options
        );
        $this->assertRegExp('/<br \/>foo/', str_replace("\n",'',$doc->toString()));
    }
    
    public function testBasicHTMLOutputWithoutTidy()
    {
        $options = array(
            'doctype' => Wibble\HTML\Document::HTML4_TRANSITIONAL,
            'disable_tidy' => true
        );
        $doc = new Wibble\HTML\Fragment(
            '<br>foo',
            $options
        );
        $this->assertRegExp('/<br>foo/', str_replace("\n",'',$doc->toString()));
    }
    
    public function testBasicXHTMLOutputWithoutTidy()
    {
        $options = array(
            'doctype' => Wibble\HTML\Document::XHTML1_STRICT,
            'disable_tidy' => true
        );
        $doc = new Wibble\HTML\Fragment(
            '<br>foo',
            $options
        );
        $this->assertRegExp('/<br>foo/', str_replace("\n",'',$doc->toString()));
    }
    
    public function testDocumentOutputThrowsExceptionIfTidyNotAvailableAndNotDisabledExplicitly()
    {
        if (class_exists('\tidy', false)) $this->markTestSkipped('Tidy installed');
        $doc = new Wibble\HTML\Fragment($this->fragment);
        $this->setExpectedException('Wibble\Exception');
        $doc->toString();
    }
    
    public function testDocumentOutputDoesNotThrowExceptionIfTidyUnavailableButDisabledExplicitly()
    {
        if (class_exists('\tidy', false)) $this->markTestSkipped('Tidy installed');
        $doc = new Wibble\HTML\Fragment($this->fragment, array('disable_tidy'=>true));
        $doc->toString();
    }

}
